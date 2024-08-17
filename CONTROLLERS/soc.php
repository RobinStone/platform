<?php
use Workerman\Worker;
use Workerman\Timer;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../CONTROLLERS/config.php';
require_once __DIR__.'/../libs/class_DB.php';
require_once __DIR__.'/../libs/class_ActionsList.php';
require_once __DIR__.'/../libs/class_SUBD.php';
require_once __DIR__.'/../libs/class_VALUES.php';
require_once __DIR__.'/../libs/class_REDISE.php';
require_once __DIR__.'/../libs/class_Access.php';
require_once __DIR__.'/../libs/class_TELE.php';
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
global $clients;
global $rooms;
$clients = [];
$rooms = [];

$wsWorker = new Worker('websocket://80.87.203.249:2346', $context);
$wsWorker->transport = 'ssl';
$wsWorker->count = 1;

$red = new REDISE();
$red->set('connections', '0');
$red->del('ID-CONNECT');
$red->del('STACK');

$wsWorker->onConnect = function($connection) use ($red, $wsWorker) {
    $connection->onMessage = function($connection, $data) use ($red, $wsWorker) {
        $data = json_decode($data, JSON_UNESCAPED_UNICODE);

        if(count($data) === 4 && isset($data['type'], $data['SHA'], $data['room'], $data['body'])) {
            if($red->get('BB') === '1') {
                say('BB = 1 (online - BB-scan)');
                say($data);
            }
//            if($red->obj_isset('SHA', $data['SHA'] ?? '1223')) {
                global $clients;
                global $rooms;
                ///////////////////////////////////////////////////////////////////////////////////////
                //////////////////////////////////////////// HANDPASS /////////////////////////////////
                ///////////////////////////////////////////////////////////////////////////////////////
                if ($data['type'] === 'new') {
                    $room = $data['room'];
                    $clients[$connection->id] = [
                 //         'user_id'=>..., - устанавливается ниже
                            'room'=>$room,
                            'connection_id'=>$connection->id,
                            'type'=>$data['type'],
                            'SHA'=>$data['SHA'],
                            'body'=>$data['body'],    // body <- ['text'=>'random text...']
                    ];
                    $scan_id_user = $red->obj_get('SHA', $data['SHA']);
                    if($scan_id_user[0] === '+') {
                        $clients[$connection->id]['user_id'] = -1;
                        $red->add_in_group('CONNECTED', '+'.$data['SHA']);
                    } else {
                        $clients[$connection->id]['user_id'] = (int)$scan_id_user;
                        $red->add_in_group('CONNECTED', (int)$scan_id_user);
                    }
                    $clients[$connection->id]['scan_id_user'] = $scan_id_user;
                    $red->obj_set('ID-CONNECT', $scan_id_user, $connection->id);
                    if(!isset($rooms[$room])) {
                        $rooms[$room] = [];
                    }
                    if(!isset($rooms[$room][$connection->id])) {
                        $rooms[$room][] = $connection->id;
                    }
                    $room_mess_all = $red->chanal_last_mess($room, 40, 0);
                    if(count($room_mess_all) > 0) {
                        $wsWorker->connections[$connection->id]->send(json_encode(get_chat($room_mess_all), JSON_UNESCAPED_UNICODE));
                    }
                    ///////////////////////////////////////////////////////////////////////////////////////
                    ///////////////////////////////////////////////////////////////////////////////////////
                    ///////////////////////////////////////////////////////////////////////////////////////
                } else {
                    if($red->get('BB') === '1') {
                        say('BB (command block)');
                    }
                    $ID = $clients[$connection->id]['user_id'];
                    $ROOM = $clients[$connection->id]['room'];
                    if($red->get('BB') === '1') {
                        say('ID = '.$ID."\r\nROOM = ".$ROOM);
                    }
                    ///////////////////////////////////////////////////////////////////////////////////////////////////////
                    ///////////////////////////////////         COMMANDS BLOCK      ///////////////////////////////////////
                    ///////////////////////////////////////////////////////////////////////////////////////////////////////
                    $txt = $data['body']['text'];
                    if($txt !== '' && $txt[0] === '>') {
                        $arr = explode(' ', $txt);
                        $ans = ['<div class="line"></div>'];
                        switch($arr[0]) {
                            case '>rooms':
                                $ans = $rooms;
                                break;
                            case '>add_in_stack':
                                if($red->isset_in_group('ADMIN', $ID)) {
                                    $red->add_in_group('STACK', $arr[1]); // id~~self_del_count~~com~~target~~data
                                } else {
                                    $ans = 'error|Выполнение данной команды доступно только админам';
                                }
                                break;
                            case '>com':
                                if($arr[1] === 'cli') {
                                    $ans = $clients;
                                } else {
                                    if (file_exists(__DIR__ . '/../CONTROLLERS/SCR/' . $arr[1] . '.php')) {
                                        ob_start();
                                        include __DIR__ . '/../CONTROLLERS/SCR/' . $arr[1] . '.php';
                                        $ans = ob_get_clean();
                                    }
                                }
                                break;
                            case '>change':
                                if(change_room($connection->id, $arr[1], $red)) {
                                    $ans = 'set_new_room|'.$arr[1];
                                } else {
                                    $ans = 'error|Такой комнаты не существует или доступ к ней - закрыт...';
                                }
                                break;
                            case '>get_chat':
                                $room_mess_all = $red->chanal_last_mess($arr[1], 40, 0);
                                if(count($room_mess_all) > 0) {
                                    $wsWorker->connections[$connection->id]->send(json_encode(get_chat($room_mess_all), JSON_UNESCAPED_UNICODE));
                                }
                                break;
                            case '>my_room':
                                if($red->isset_in_group('ADMIN', $ID)) {
                                    $ans = $ROOM;
                                } else {
                                    $ans = 'error|Выполнение данной команды доступно только админам';
                                }
                                break;
                        }
                        $ans = [
                            'type'=>'answer',
                            'text'=>$ans,
                        ];
                        $wsWorker->connections[$connection->id]->send(json_encode($ans, JSON_UNESCAPED_UNICODE));
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////
                    } else {
                        $red->mess_send($ROOM, $ID, $txt, mess_type::text);
                        if((int)$ID !== -1) {
                            $P = new PROFIL($ID);
                        }

                        if($red->get('BB') === '1') {
                            say('PROFIL SENDER');
                        }

                        $owner_id_user = -2;
                        $buff = $red->get('b$-'.$ROOM);
                        if($buff !== null) {
                            $owner_id_user = (int)$buff;
                        } else {
                            if($red->get('BB') === '1') {
                                say('Scan ROOM = '.$ROOM);
                                $row = SUBD::getLineDB('rooms', 'room_id', $ROOM, true);
                            } else {
                                $row = SUBD::getLineDB('rooms', 'room_id', $ROOM);
                            }
                            if($red->get('BB') === '1') {
                                say($row);
                            }
                            if(is_array($row)) {
                                if($row['type'] === 'product') {
                                    $owner_id_user = (int)$row['owner'];
                                } else {
                                    $owner_id_user = -2;   // установка если текст не относится к чату продавец-покупатель
                                }
                                $red->set('b$-'.$ROOM, $owner_id_user);
                            } else {
                                TELE::send_at_user_name('robin', 'Не найден ID - SHOP');
                            }
                        }
                        $red->set_time_live('b$-'.$ROOM, TYPE_INTERVAL::minutes, 10);


                        if($owner_id_user !== -2) {  // текст относится к чату продавец-покупатель
                            if($red->get('BB') === '1') {
                                say($data);
                                say($rooms[$ROOM]);
                            }
                            if ($ID == $owner_id_user) { // Пишет продавец покупателю на уже готовую ROOM  !!!!!!!!!!!!!!!!!!!!!!!!!
                                q("
                                UPDATE `rooms` SET 
                                `created` = '".date('Y-m-d H:i:s')."',
                                `showed_us` = 0
                                WHERE `room_id`='".$ROOM."' ");
                                $row = SQL_ROWS(q("SELECT * FROM `rooms` WHERE `room_id`='".$ROOM."' "));
                                if(isset($row[0])) {
                                    $client_id_user = (int)$row[0]['client_id'];
                                    $buff = $red->get('PROFIL_'.$client_id_user);
                                    if($buff === null) {
                                        $P_BUY = new PROFIL($client_id_user);
                                        if(count($P_BUY->errors) === 0) {
                                            $client = [
                                                'login'=>$P_BUY->get_field('login'),
                                                'name'=>$P_BUY->get('name'),
                                                'email'=>$P_BUY->get_field('email'),
                                                'tele'=>$P_BUY->get_field('tele'),
                                                'phone'=>$P_BUY->get_field('phone'),
                                                'avatar'=>$P_BUY->get('avatar'),
                                            ];
                                            $red->set('PROFIL_'.$client_id_user, serialize($client));
                                            $red->set_time_live('PROFIL_'.$client_id_user, TYPE_INTERVAL::minutes, 2);
                                        }
                                    } else {
                                        $client = unserialize($buff);
                                    }
//                                    $params_ord = explode('~~', $row[0]['params']);
                                    if (!$red->isset_in_group('CONNECTED', $client_id_user)) { // отсутствует на сайте в любом из чатов на текущий момент
                                        if(isset($client) && $client['tele'] !== '-') {
                                            $txt_mess = "Сообщние в чат: \r\n*" . $data['body']['text'] . "*\r\nhttps://kokonk.com/messenger?room=" . $ROOM;
                                            TELE::send_at_user_name($client['login'], $txt_mess);
                                        }
                                    } else {  // где то на сайте в одном из чатов
//                                        $client_id = $red->obj_get('ID-CONNECT', $client_id_user);  //  $client_id - это id WsWorker
                                        $dt = [
                                                'type' => 'alert',
                                                'text' => "Сообщение в чат - \r\n" . $data['body']['text'],
                                                'room_id' => $ROOM,
                                        ];
                                        $itter = get_all_connections_of_user($client_id_user);
                                        foreach($itter as $num) {
                                            $wsWorker->connections[$num]->send(json_encode($dt, JSON_UNESCAPED_UNICODE));
                                        }
                                    }
                                }
                            } else {   //  ПИШЕТ ПОКУПАТЕЛЬ ПРОДАВЦУ

                                if (!$red->isset_in_group('CONNECTED', $owner_id_user)) {   // отсутствует на сайте в любом из чатов на текущий момент
                                    $txt_mess = "Сообщние в чат: \r\n*" . $data['body']['text'] . "*\r\nhttps://kokonk.com/messenger?room=" . $ROOM;
                                    TELE::send_at_user_id($owner_id_user, $txt_mess);
                                } else {  // присутствует в каком то чате на сайте
//                                    $client_id = $red->obj_get('ID-CONNECT', $owner_id_user);  //  $client_id - это id WsWorker
                                    $dt = [
                                            'type' => 'alert',
                                            'text' => "Сообщение в чат - \r\n" . $data['body']['text'],
                                            'room_id' => $ROOM,
                                    ];
                                    $itter = get_all_connections_of_user($owner_id_user);
                                    foreach($itter as $num) {
                                        $wsWorker->connections[$num]->send(json_encode($dt, JSON_UNESCAPED_UNICODE));
                                    }
                                }
                                $ask = q("SELECT * FROM `alerts` WHERE `id_user`=" . $owner_id_user . " AND `room_id`='" . $ROOM . "' ");
                                if ($ask->num_rows) {
                                    if ($ID != $owner_id_user) {
                                        $row = $ask->fetch_assoc();
                                        q("UPDATE `alerts` SET `datatime`='" . date('Y-m-d H:i:s') . "', `showed`=0 WHERE `id`=" . (int) $row['id']);
                                    }
                                } else {
                                    q("
                                    INSERT INTO `alerts` SET 
                                    `id_user`=" . $owner_id_user . ",
                                    `room_id`='" . $ROOM . "',
                                    `datatime`='" . date('Y-m-d H:i:s') . "', 
                                    `showed`=0 
                                    ");
                                }
                            }
                        } else {    //  СООБЩЕНИЕ НЕ ОТНОСИТСЯ К ЧАТУ ПРОДАВЕЦ ПОКУПАТЕЛЬ
                            $query_row = SUBD::getLineDB('rooms', 'room_id', $data['room']);
                            if(is_array($query_row)) {
                                switch($query_row['type']) {
                                    case 'support':
                                        $calls = $red->obj_all('CALLS_SUPPORT');
                                        $dt_buff = $data;
                                        $dt_buff['type'] = 'alert';
                                        $dt_buff['call'] = 'support';
                                        foreach($calls as $v_itm) {
                                            if ($red->isset_in_group('CONNECTED', $v_itm)) {
                                                $group_sup = get_all_connections_of_user($v_itm);
                                                foreach ($group_sup as $ii) {
                                                    $wsWorker->connections[(int) $ii]->send(json_encode($dt_buff, JSON_UNESCAPED_UNICODE));
                                                }
                                            } else {
                                                $us = SUBD::getLineDB('users', 'id', (int)$v_itm);
                                                if($us['tele'] !== '' && $us['tele'] !== '-') {
                                                    $code = crypter("wer".rand(1000, 9999));
                                                    Access::set_system_message($code, $us['login'], ActionsList::AUTH, (int)$v_itm, 60);
                                                    TELE::send_at_user_name($us['login'], "Запрос в поддержку:\r\n".$data['body']['text']."\r\nhttps://kokonk.com/admin?auto_auth=".$code."&room=".$query_row['room_id']);
                                                }
                                            }
                                        }
//                                        if (!$red->isset_in_group('CONNECTED', $client_id_user)) {
                                            $row_a = SUBD::getLineDB('alerts', 'room_id', $data['room']);
                                            if (is_array($row_a)) {
                                                q("UPDATE `alerts` SET `showed`=0, `datatime`='".date('Y-m-d H:i:s')."' WHERE `id`=" . (int)$row_a['id']);
                                            } else {
                                                q("
                                            INSERT INTO `alerts` SET 
                                            `id_user`=-1,
                                            `room_id`='" . db_secur($data['room']) . "',
                                            `datatime`='" . date('Y-m-d H:i:s') . "',
                                            `showed`=0
                                            ");
                                            }
//                                        }
                                        break;
                                }
                            }
                        }

                        if(isset($P)) {
                            $img = '/IMG/img100x100/' . $P->get_field('avatar');
                            if (!file_exists(__DIR__ . '/..' . $img)) {
                                $img = '/IMG/SYS/user.svg';
                            }
                        } else {
                            $img = '/IMG/SYS/user.svg';
                        }

                        $data = [
                            'type'=>'list',
                            'arr'=>[$img.'~~'.$ID.'~~'.date('Y-m-d H:i:s').'~~t~~'.$data['body']['text']],
                        ];

                        foreach($rooms[$ROOM] as $v) {
                            if(isset($wsWorker->connections[$v])) {
                                $wsWorker->connections[$v]->send(json_encode($data, JSON_UNESCAPED_UNICODE));
                            }
                        }
                    }
                }
//            }
        }
    };
    $red->inc('connections');
    echo "Connection OPEN \n";
};

$wsWorker->onClose = function($connection) use ($wsWorker, $red) {
    global $clients;
    global $rooms;
        if(isset($clients[$connection->id])) {
            $sha = $clients[$connection->id]['SHA'];
            $scan_id_user = $clients[$connection->id]['scan_id_user'];
            $P = new PROFIL((int) $clients[$connection->id]['user_id']);
            $red->obj_del_item('SHA', [$sha]);
            $room = $rooms[$clients[$connection->id]['room']];
//    TELE::send_at_user_name('robin', '+'.$sha);
            $red->rem_from_group('CONNECTED', (int) $clients[$connection->id]['user_id']);
            $red->rem_from_group('CONNECTED', '+' . $sha);
            $red->obj_del_item('ID-CONNECT', $scan_id_user);
            $img = '/IMG/img100x100/' . $P->get_field('avatar');
            if (!file_exists('.' . $img)) {
                $img = '/IMG/SYS/user.svg';
            }
            $data = [
                    'type' => 'list',
                    'arr' => [$img . '~~-1~~' . date('Y-m-d H:i:s') . '~~t~~Покинул (а) чат...✋'],
            ];
            foreach ($room as $v) {
                if (isset($wsWorker->connections[$v])) {
                    $wsWorker->connections[$v]->send(json_encode($data, JSON_UNESCAPED_UNICODE));
                }
            }
            unset($room[array_search($connection->id, $room)]);
            if (count($room) === 0) {
                unset($rooms[$clients[$connection->id]['room']]);
            } else {
                $rooms[$clients[$connection->id]['room']] = $room;
            }
            unset($clients[$connection->id]);
        } else {
//            TELE::send_at_user_name('robin', 'ERROR connection (look SAY) id_connection='.$connection->id);
        }
    echo "Connection CLOSE \n";
};

$wsWorker->onWorkerStart = function($wsWorker) use ($red) {
    Timer::add(60, function() use ($wsWorker, $red) {
        try {
            $test_row = SUBD::getLineDB('users', 'login', 'robin');
            if(is_array($test_row)) {
                $red->set('RBS', date('H:i:s d.m.Y'));
            }
        } catch (Exception $e) {
            $red->inc('ERROR');
        }
    });

    Timer::add(10, function() use ($wsWorker, $red) {
        global $clients;
        global $rooms;
        foreach($rooms as $k=>$v) {
            if(count($v) === 0) {
                unset($rooms[$k]);
            }
        }
        foreach($clients as $k=>$v) {
            if(isset($wsWorker->connections[$k]) && (int)$v['connection_id'] !== (int)$k) {
                TELE::send_at_user_name('robin', "DEL USER - ".(int)$v['user_id']);
                $red->obj_del_item('SHA', [$v['SHA']]);
                $red->rem_from_group('CONNECTED', (int)$v['user_id']);
                $red->rem_from_group('CONNECTED', '+'.$v['SHA']);
                $red->obj_del_item('ID-CONNECT', (int)$v['user_id']);
                unset($clients[$k]);
            }
        }
    });

    Timer::add(1, function() use ($wsWorker, $red) {
        if($red->count_in_group('STACK') > 0) {
            $arr = $red->group_all('STACK'); // id~~self_del_count~~com~~target~~data
            foreach($arr as $v) {
                $row = explode('~~', $v);
                $ans = [];
                if(is_array($row) && count($row) === 5) {
                    $id_sended = (int)$row[3];
                    $connect = (int)$red->obj_get('ID-CONNECT', $id_sended);
                    if((int)$row[1] > 0 && $connect > 0) {
                        switch ($row[2]) {
                            case 'get_all_connected':
                                $ans = $red->obj_all('ID-CONNECT');
                                break;
                            default:
                                $comms = explode('|~|', $row[2]);
                                if(count($comms) > 1 && $comms[0] === 'cycle') {   //require_once __DIR__.'/../vendor/
                                    if(file_exists(__DIR__.'/../CONTROLLERS/SCR/'.$comms[1].'.php')) {
                                        include_once __DIR__.'/../CONTROLLERS/SCR/'.$comms[1].'.php';
                                        $ans = main($red);
                                    }
                                }
                                break;
                        }

                        $ans['itter'] = (int)$row[1];
                        $dt = [
                            'type' => 'array',
                            'body' => $ans
                        ];
                        if(isset($wsWorker->connections[$connect])) {
                            $wsWorker->connections[$connect]->send(json_encode($dt, JSON_UNESCAPED_UNICODE));
                        }

                        $red->rem_from_group('STACK', $v);
                        $row[1] = ((int)$row[1]) - 1;
                        $red->add_in_group('STACK', implode('~~', $row));
                    } else {
                        $red->rem_from_group('STACK', $v);
                    }
                } else {
                    $red->rem_from_group('STACK', $v);
                }
            }
        }
    });
};

//$wsWorker->onMessage = function($connection, $data) use ($wsWorker, $red) {
//    global $clients;
//    $sha = $clients[$connection->id]['data']['SHA'];
//    $id = (int)$red->obj_get('SHA', $sha);
//    TELE::send_at_user_name('robin', $id);
//    foreach($wsWorker->connections as $clientConnection) {
//        $clientConnection->send($data);
//    }
//};

function get_chat($room_mess_all) {
    $profils = [];
    foreach($room_mess_all as $k=>$v) {
        $arrm = explode('~~', $v);
        if(!in_array($arrm[0], $profils)) {
            if($arrm[0] != -1) {
                $P = new PROFIL((int)$arrm[0]);
                $profils[(int)$arrm[0]] = '/IMG/img100x100/'.$P->get_field('avatar');
            } else {
                $profils[-1] = '/IMG/SYS/user.svg';
            }
        }
        $room_mess_all[$k] = $profils[(int)$arrm[0]].'~~'.$v;
    }
    return [
            'type'=>'list',
            'arr'=>$room_mess_all
    ];
}

function change_room($client_id, $new_room, $red): bool
{
    $arr = SUBD::getLineDB('rooms', 'room_id', $new_room);
    if($red->get('BB') === '1') {
        say('CHANGER ROOM - '.$new_room);
        say($arr);
    }
    if(!is_array($arr)) {
        return false;
    }
    q("UPDATE `rooms` SET `created` = '".date('Y-m-d H:i:s')."' WHERE `id`=".(int)$arr['id']);
    global $rooms;
    global $clients;
    $id_old_room = $clients[$client_id]['room'];
    $room = $rooms[$id_old_room];
    unset($room[array_search($client_id, $room)]);
    $rooms[$id_old_room] = $room;
    $rooms[$new_room][] = $client_id;
    $clients[$client_id]['room'] = $new_room;
    return true;
}

function get_all_connections_of_user($ID): array
{
    $ans = [];
    global $clients;
    foreach($clients as $k=>$v) {
        if($v['user_id'] == $ID || $v['SHA'] == $ID) {
            $ans[] = (int)$k;
        }
    }
    return $ans;
}

Worker::runAll();