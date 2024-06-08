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
            $only_exists = $post['only_exists'] ?? false;
            $P = PROFIL::init(Access::userID());
            $response = $post['get'];
            foreach($response as $k=>$v) {
                $ans = $P->get($k, $v);
                if(($only_exists && $ans !== $v) || !$only_exists) {
                    $response[$k] = $ans;
                } else {
                    unset($response[$k]);
                }
            }
            ans('ok', $response);
        }
        if(!empty($post['set'])) {

            ans('ok');
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
