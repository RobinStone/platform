<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        $err = isset_columns($_POST, ['']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'get_points':
        $err = isset_columns($_POST, ['cdek_city_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        ans('ok', CDEK::get_points($post['cdek_city_id']));
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
