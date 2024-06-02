<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'get_support_users':
        ans(SUPPORT::get_active_service());
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
