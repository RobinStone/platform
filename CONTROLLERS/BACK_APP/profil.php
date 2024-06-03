<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
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
