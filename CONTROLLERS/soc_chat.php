<?php
// PROTOCOL
// всякий раз как кто-то заходит на сайт система регистрирует его в таблице `online`
// далее каждую минуту CRON проверяет не отвалился ли кто, а отваливаются те, у кого
// не открыт чат (чат перодически шлёт AJAX что б обновить сессию раз в минуту, удаляет если нет активност
// более 2-ух минут (это уже CRON))
// или кто долго не юзает по сайту (тем самым вызывая INIT который в route.php у нас)
// ONLINE::INIT - добавляет или обновляет человека, но только в таблицу БД без id_connect
// сам id_connect обновляется только после того как активируется чат, который и
// передаёт TOKEN (уже по sockets) по которому в soc_chat в case=begin token связывается с id_connect
//
// TOKEN - штука основанная на $_SESSION поэтому robot.js через setInterval раз в 5 минут обновляет
// его по AJAX методом UPDATE_TOKEN_CHAT который по сути тоже вызывает ONLINE::INIT и обновляет и сессию
// и запись в таблице `online`
//
// При выходе из чата connection_id в таблице `online` меняется на -1 и из массива в soc_chat запись
// тоже исчезает
//
//
//
//

use Workerman\Worker;
use Workerman\Timer;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../CONTROLLERS/config.php';
require_once __DIR__.'/../libs/class_DB.php';
require_once __DIR__.'/../libs/class_SUBD.php';
require_once __DIR__.'/../libs/class_VALUES.php';
require_once __DIR__.'/../libs/class_Access.php';
require_once __DIR__.'/../libs/class_MAIL.php';
require_once __DIR__.'/../libs/class_TELE.php';
require_once __DIR__.'/../libs/class_ONLINE.php';
require_once __DIR__.'/../libs/class_ROOM.php';
require_once __DIR__.'/../libs/class_IM.php';
require_once __DIR__.'/../libs/class_RBS.php';
require_once __DIR__.'/../libs/class_AUDIO.php';
require_once __DIR__.'/../libs/class_PHONEBOOK.php';
require_once __DIR__.'/../APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
require_once __DIR__.'/../APPLICATIONS/SHOPS/libs/class_SHOP.php';
require_once __DIR__.'/../libs/class_SEARCH.php';
require_once __DIR__.'/../libs/class_Message.php';
require_once __DIR__.'/../libs/class_PROFIL.php';
require_once __DIR__.'/../CONTROLLERS/soc_autoload.php';

$context = [
    'ssl' => [
        'local_cert' => '/var/www/httpd-cert/www-root/rumbra.ru_le3.crt',
        'local_pk' => '/var/www/httpd-cert/www-root/rumbra.ru_le3.key',
        'verify_peer' => false,
    ],
];

global $wsWorker;
$wsWorker = new Worker('websocket://80.87.203.249:2348', $context);
$wsWorker->transport = 'ssl';
$wsWorker->count = 1;

$wsWorker->onConnect = function($connection) use ($wsWorker) {
    echo "Connection OPEN_SOC_CHAT \n";
};

function error(string $txt, int $id_connect) {
    global $wsWorker;
    $data['type'] = 'error';
    $data['result'] = $txt;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    $wsWorker->connections[$id_connect]->send($data);
}

/**
 * @param string|array $txt_or_data
 * @param int $id_connect
 * @param string $type = text|action|error
 * @param string $com - тут может быть или не быть всё что угодно, это обрабатывается на клиенте
 * @param string $owner_token - подпись отправителя
 * @param string $additional_params - дополнительные параметры
 * @return void
 */
function ans(string|array $txt_or_data, int $id_connect, string $type='text', string $com='', string $owner_token='---', string|array|int $additional_params='') {
    global $wsWorker;
    $data['type'] = $type;
    $data['result'] = $txt_or_data;
    $data['com'] = $com;
    $data['writed'] = md5('erw23sdg'.$owner_token);
    $data['additional'] = $additional_params;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    $wsWorker->connections[$id_connect]->send($data);
}

$wsWorker->onMessage = function($connection, $data) use ($wsWorker) {
    $data = json_decode($data, JSON_UNESCAPED_UNICODE);
    $im = IM::INIT($data['token']);
    if(isset($data['type'])) {
        switch ($data['type']) {
            case 'begin':
                if(!isset_fields($data, ['type_room', 'type', 'room', 'token'])) {
                    error('Не переданны нужные поля', $connection->id);
                    return;
                }
                $data['type'] = 'text';
                if($row = ONLINE::get_user_for_token($data['token'])) {
                    $onliner = ONLINE::set_connection_id($data['token'], $connection->id, (int)$row['user_auth']);
                    switch($data['type_room']) {
                        case 'one2one':
                            $data['params']['IM'] = $im;
                            if($room_real = ROOM::INIT($data['room'], $data['token'], $data['type_room'], $onliner['registred'], $data['params'] ?? [])) {
                                if ($room_real !== true) {
                                    ans($room_real, $connection->id, 'action', 'connected', $data['token']);
                                } else {
                                    ans('Чат one2one - не может быть инициирован...', $connection->id, 'action', 'unconnected');
                                }
                            } else {
                                error('Что-то пошло не так с подключением one2one...', $connection->id);
                            }
                            break;
                        case 'shop':
                        case 'product':
                            if ($room_real = ROOM::INIT($data['room'], $data['token'], $data['type_room'], $onliner['registred'], $data['params'] ?? [])) {
                                if ($room_real !== true) {
                                    ans($room_real, $connection->id, 'action', 'connected', $data['token']);
                                } else {
                                    ans('Чат открыт на собственной площадке', $connection->id, 'action', 'unconnected');
                                }
                            } else {
                                error('Ошибка определения родключения к чату продавца...', $connection->id);
                            }
                            break;
                        case 'personal':
                            ans('PERSONAL', $connection->id, 'action', 'connected', $data['token']);
                            break;
                        default:
                            error('Ошибка определения точки входа в чат', $connection->id);
                            break;
                    }
                } else {
                    error('Ошибка подтверждения токенов доступа', $connection->id);
                }
                break;
            case 'comand':  // ЭТОТ КОНТРОЛ ЗАПУСКАЕТСЯ ИЗ JS командой set_comand
                if(!ONLINE::access($data['token'])) {
                    error('Ошибка подтверждения токенов доступа', $connection->id);
                } else {
                    switch($data['text']) {
                        case 'delete_message':
                            if(ROOM::delete_message((int)$data['datas'], $data['token'])) {
//                                ans('Сообщение удалено', $connection->id, 'info');
                                send_command_in_room($data, 'delete_message', $data['datas']);
                            } else {
                                ans('Не удалось удалить', $connection->id, 'info');
                            }
                            break;
                        case 'close_session':
                            ONLINE::close_session($connection->id);
                            break;
                        case 'set_emojy':
                            if(ROOM::change_message($data['datas']['id'], $data['token'], $data['datas']['emojy'], 'emojy')) {
                                send_command_in_room($data, 'set_emojy', $data['datas']);
                            } else {
                                error('Не удалось изменить сообщение', $connection->id);
                            }
                            break;
                        case 'change_message':   // id, value
                            if(ROOM::change_message($data['datas']['id'], $data['token'], $data['datas']['value'])) {
                                send_command_in_room($data, 'change_message', $data['datas']);
                            } else {
                                error('Не удалось изменить сообщение', $connection->id);
                            }
                            break;
                        default:

                            break;
                    }
                }
                break;
            case 'com':
                if(!ONLINE::access($data['token'])) {
                    error('Ошибка подтверждения токенов доступа', $connection->id);
                } else {
                    switch($data['text']) {
                        case 'get_room':
                            ROOM::update_hu_in_room_for_token($data['token'], $im);
                            if($llist = ROOM::get_room_chat($data['token'], $data['target']['room'], 1, 30)) {
                                ans($llist, $connection->id, 'list', '', $data['token'], 'islist');
                            }
                            break;
                        case 'update_last_use':
                            if(ROOM::isset_in_room_sql($data['target'], $im->token ?? '-')) {
                                ROOM::update_time_last_use($data['target']);
                            }
                            break;
                        case 'get_livers_parameters':
                            ans(ROOM::get_livers_parameters($data['target']), $connection->id, 'action', 'set_livers_parameters');
                            break;
                        case 'get_dump':
                            say(ONLINE::$all);
                            say(ONLINE::$users);
                            say(ONLINE::$tokens);
                            say(ONLINE::$users_ids);
                            ans('ok', $connection->id, 'text');
                            break;
                        case 'delete_contact':
                            PHONEBOOK::delete_contact($im->id_user, $data['target']);
                            break;
                        case 'change_image_for_phone_book':
                            PHONEBOOK::change_image_for_phone_book((int)$data['target']['id'], $data['target']['img']);
                            break;
                        case 'change_title_for_phone_book':
                            PHONEBOOK::change_title_for_phone_book((int)$data['target']['id'], $data['target']['title']);
                            break;
                        case 'save_contact':
                            if(isset($data['target'])) {
                                $target = $data['target'];
                                if($im->id_user !== -1) {
                                    PHONEBOOK::add_contact($im->id_user, $target['room'], $target['liver_id']);
                                }
                            }
                            break;
                        case 'update_contact':
                            $liver = explode('-', $data['target'])[0];
                            $room = explode('-', $data['target'])[1];
                            if($row = ROOM::get_contact_from_id_liver($room, $liver)) {
                                ans($row, $connection->id, 'action', 'update_contact');
                            } else {
                                ans(['id'=>-1, 'avatar'=>'20240113-181427_id-2-236369.png'], $connection->id, 'action', 'update_contact');
                            }
                            break;
                        default:
                            error('Неизвестная команда ['.$data['text'].']',  $connection->id);
                            break;
                    }
                }
                break;
            case 'info':
                if(!ONLINE::access($data['token'])) {
                    error('Ошибка подтверждения токенов доступа', $connection->id);
                } else {
                    switch($data['text']) {
                        case 'message_reading':
                            q("UPDATE main_chat SET showed=1 WHERE id=".(int)$data['target']);
                            break;
                        default:
                            error('Неопознаный тип информации', $connection->id);
                            break;
                    }
                }
                break;
            case 'mess':
                if(!ONLINE::access($data['token'])) {
                    error('Ошибка подтверждения токенов доступа...', $connection->id);
                } else {
                    send_message_in_room($data, $data['target']);
                }
                break;
            case 'answer':
                if(!ONLINE::access($data['token'])) {
                    error('Ошибка подтверждения токенов доступа...', $connection->id);
                } else {
                    send_message_in_room($data, $data['target']['room'], 2, 'answer');
                }
                break;
            case 'file':
                if(!ONLINE::access($data['token'])) {
                    error('Ошибка подтверждения токенов доступа...', $connection->id);
                } else {
                    send_message_in_room($data, $data['target']['target_room'], 2, $data['target']['type_file']);
                }
                break;
        }
//        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
//        $wsWorker->connections[$connection->id]->send($data);

//        foreach($wsWorker->connections as $clientConnection) {
//            $clientConnection->send($data);
//        }
    }
};

$wsWorker->onClose = function($connection) use ($wsWorker) {
    ONLINE::offline_id_client($connection->id);
    echo "Connection CLOSE_SOC_CHAT \n";
};

$wsWorker->onWorkerStart = function($wsWorker) {
    Timer::add(60, function () use ($wsWorker) {
        try {
            setParam('SOC_CHAT_UPDATED', date('d.m.Y H:i:s'));
        } catch (Exception $e) {
            say('ERROR TIMER FINDER...');
            say($e);
            setParam('date_last_search', 'ERROR');
        }
    });
};

function send_message_for_all($data) {
    global $wsWorker;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    foreach(ONLINE::$all as $k=>$v) {
        if(isset($wsWorker->connections[$k])) {
            $wsWorker->connections[$k]->send($data);
        }
    }
}

/**
 * Метод отправляет одну и ту же команду всем участником комнаты, при чём с базами ничего не делает
 */
function send_command_in_room(array $data, string $command, array|string|int $params): bool
{
    $arr = ROOM::get_all_id_contacts_from_room($data['target']);
    if(count($arr) > 0) {
        foreach($arr as $v) {
            ans($command, (int)$v, 'action', $command, '---', $params);
        }
    }
    return true;
}

function send_message_in_room(array $data, string $room, int $max_count_livers=2, string $type_mess='text') {
    $access_types = ['text', 'answer', 'img', 'image', 'audio', 'video', 'voice', 'file'];
    $label = '-';
    if(!in_array($type_mess, $access_types)) {
        $type_mess = 'text';
    }
    switch($type_mess) {
        case 'image':
            $type_mess = 'img';
            $data['text'] = $data['target']['insert_last_id']."|".$type_mess."|".$data['target']['sys_name']."|".$data['target']['user_name'];
            break;
        case 'voice':
            $label = $data['target']['long'] ?? '';
            break;
        case 'answer':
            $post = SQL_ONE_ROW(q("
            SELECT main_chat.type_mess, 
                   main_chat.mess 
            FROM main_chat 
            WHERE 
                  id=".(int)$data['target']['id_mess']." LIMIT 1")
            );
            $block = '';
            switch($post['type_mess']) {
                case 'text':
                    $block = '<i class="old-mess">'.$post['mess'].'</i>';
                    $data['text'] = '<div data-id-target="'.$data['target']['id_mess'].'" class="answer">'.$block.$data['text'].'</div>';
                    break;
                case 'img':
                    $img = explode('|', $post['mess'])[2];
                    $block = '<img width="40" height="40" src="/IMG/img100x100/'.$img.'"><br><div class="ans-line"></div>';
                    $data['text'] = '<div data-id-target="'.$data['target']['id_mess'].'" class="answer">'.$block.$data['text'].'</div>';
                    break;
            }
            break;
    }

    $rows = SQL_ROWS_FIELD(q("
    SELECT room.id AS id_room, hu_in_room.* FROM room
    LEFT JOIN hu_in_room ON 
    hu_in_room.id_room = room.id 
    WHERE room.room = '".$room."'
    LIMIT ".$max_count_livers."
    "), 'id_user');

    $id_connections = [];
    (int) $id_user_sender = ONLINE::$users[$data['token']]['ID'] ?? -1;
    if($id_user_sender !== -1 &&
        isset($rows[$id_user_sender]) &&
        $data['token'] !== $rows[$id_user_sender]['token']) {
        $data['token'] = $rows[$id_user_sender]['token'];
    }

    $id_live_self = -1;
    foreach($rows as $v) {

        if($v['token'] === $data['token']) {
            $id_live_self = (int)$v['id'];
        }
        if($id_connects = ONLINE::token_TO_connection_id($v['token'])) {
            foreach($id_connects as $vv) {
                $id_connections[$vv] = [
                    'id_connect' => $vv,
                    'id_lives' => $v['id'],
                ];
            }
        } else {
            // В СЛУЧАЕ ЕСЛИ ПОЛЬЗОВАТЕЛЬ НЕ ДОСТУПЕН ПРОИСХОДИТ ОПОВЕЩЕНИЕ В TELEGRAMM ИЛИ ПО EMAIL
            if($v['id_user'] !== -1) {
//                $user = new PROFIL($v['id_user']);
//                $tele = $user->get_field('tele', '-');
//                $cd = md5(date('Y.m.d H:i:s').'fweknkw'.rand(10000, 99999));
//                Access::set_system_message($cd, $v['id_user'], 'разрешение', '', 180);
//                $code = 'https://rumbra.ru/chat?token_auth='.$cd;
//                if($tele !== '-') {
//                    $login = $user->get_field('login');
//                    Access::set_system_message($login, $login, 'сообщение telegram', $code, 1);
//                } else {
//                    $email = $user->get('email', '-');
//                    if($email !== '-') {
//                        Access::set_system_message($email, $email, 'отправка email', $code, 1);
//                    }
//                }
            }
        }
        if($us_params = ONLINE::token_TO_user_params($v['token'])) {
            if((int)$us_params['ID'] !== -1) {
                foreach(ONLINE::$users_ids[$us_params['ID']] as $vv) {
                    if($vv !== $v['token']) {
                        if($id_connects = ONLINE::token_TO_connection_id($vv)) {
                            foreach($id_connects as $vvv) {
                                $id_connections[$vvv] = [
                                    'id_connect' => $vvv,
                                    'id_lives' => $v['id'],
                                ];
                            }
                        }
                    }
                }
            }
        }
    }

    if(count($rows) > 0) {
        $first_row = reset($rows);
        start_transaction();
        q("
        INSERT INTO main_chat SET
        `id_room`=".(int)$first_row['id_room'].",
        `id_hu_in_room`=".$id_live_self.",
        `datatime`='".date('Y-m-d H:i:s')."',
        `type_mess`='".$type_mess."',
        `mess`='".db_secur($data['text'])."',
        `label`='".db_secur($label)."'
        ");

        $row = SQL_ONE_ROW(q("
        SELECT
        main_chat.id, 
        main_chat.id_room,
        '".db_secur($room)."' AS room,
        main_chat.id_hu_in_room AS id_liver, 
        main_chat.showed, 
        main_chat.reaction, 
        main_chat.datatime, 
        main_chat.label, 
        main_chat.type_mess AS type, 
        ".$id_live_self." AS SELF,
        main_chat.mess FROM main_chat ORDER BY id DESC LIMIT 1"));

        $dt = date('Y-m-d H:i:s');

        q("
        UPDATE room JOIN phone_book ON phone_book.room = room.room
        SET room.last_use='".$dt."', phone_book.datatime='".$dt."' WHERE room.room='".$room."'
        ");
        end_transaction();

        foreach($id_connections as $v) {
            $row['SELF'] = $v['id_lives'];
            ans([$row], $v['id_connect'], 'list', '', $data['token']);
        }

    } else {
        t('Какая-то лажа со строками для сообщения');
    }
}
function send_message_for_token_array(array $tokens, array $data=['type'=>'mess', 'result'=>'']) {
    global $wsWorker;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    foreach($tokens as $v) {
        if(isset(ONLINE::$tokens[$v])) {
            if (isset($wsWorker->connections[ONLINE::$tokens[$v]])) {
                $wsWorker->connections[ONLINE::$tokens[$v]]->send($data);
            }
        }
    }
}
function send_message_for_id_connections_array(array $id_connections, array $data=['type'=>'mess', 'result'=>'']) {
    global $wsWorker;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    foreach($id_connections as $v) {
        if(isset(ONLINE::$all[$v])) {
            if (isset($wsWorker->connections[ONLINE::$all[$v]])) {
                $wsWorker->connections[ONLINE::$all[$v]]->send($data);
            }
        }
    }
}
function isset_fields($arr, $access_arr): bool
{
    foreach($access_arr as $v) {
        if(!isset($arr[$v])) {
            return false;
        }
    }
    return true;
}
function logout($token, $mess='') {
    foreach(ONLINE::token_TO_connection_id($token) as $id_connect) {
        ans($mess, $id_connect, 'action', 'logout');
    }
}
//$wsWorker->onWorkerStart = function($wsWorker) use ($red) {
//    Timer::add(10, function() use ($wsWorker, $red) {
//
//    });
//};

//$wsWorker->onMessage = function($connection, $data) use ($wsWorker, $red) {
//    global $clients;
//    $sha = $clients[$connection->id]['data']['SHA'];
//    $id = (int)$red->obj_get('SHA', $sha);
//    TELE::send_at_user_name('robin', $id);
//    foreach($wsWorker->connections as $clientConnection) {
//        $clientConnection->send($data);
//    }
//};

Worker::runAll();