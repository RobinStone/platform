<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'profil':
        if(Access::scanLevel() < 1) {
            error('Ошибка доступа. Требуется уровень 1+');
        }
        if(!empty($post['get'])) {
            $errors = [];
            $only_exists = $post['only_exists'] ?? false;
            $P = PROFIL::init(Access::userID());
            $response = $post['get'];
            if(!is_array($response)) {
                $errors[] = 'Не переданы параметры запроса';
                ans('ok', ['errors'=>$errors, 'response' => []]);
            }
            foreach($response as $k=>$v) {
                $ans = $P->get($k, $v);
                if(($only_exists && $ans !== $v) || !$only_exists) {
                    $response[$k] = $ans;
                } else {
                    unset($response[$k]);
                }
            }
            ans('ok', ['errors'=>$errors, 'response' => $response]);
        }
        if(!empty($post['set'])) {
            $P = PROFIL::init(Access::userID());
            $response = $post['set'];
            $resp = [];
            $errors = [];
            if(!is_array($response)) {
                error('Не переданы параметры установки');
            }
            if(empty($close_front_profil_fields)) {
                error('Не заполнена системная переменная в админ-панели << close_front_profil_fields >>');
            }
            $deny_fields = explode('|', $close_front_profil_fields);
            $changed_sys_access = false;
            if(Access::isset_permission(Access::userID(), 'access_change_profil_sys', ActionsList::PERMISSION)) {
                $changed_sys_access = true;
            }
            foreach($response as $k=>$v) {
                if(in_array($k, $deny_fields)) {
                    if($changed_sys_access) {
                        if($v !== '<-->') {
                            $P->set($k, $v, false);
                        } else {
                            $P->remoove($k, false);
                        }
                        $resp[$k] = $v;
                    } else {
                        $errors[] = 'Переменная ['.$k.'] - требуется разрешение << access_change_profil_sys >>';
                    }
                } else {
                    if($v !== '<-->') {
                        $P->set($k, $v, false);
                    } else {
                        $P->remoove($k, false);
                    }
                    $resp[$k] = $v;
                }
            }
            $P->set('updated', date('Y-m-d H:i:s'));
            ans('ok', ['errors'=>$errors, 'response'=>$resp]);
        }
        error('Не распознано обращение. Требуется get или set');
        break;
    case 'get_support_users':
        $rows = SUPPORT::get_active_service();
        if(count($rows) === 0) {
            TELE::send_for_all_users_min_level("**ВНИМАНИЕ**", 6);
        }
        ans($rows);
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
