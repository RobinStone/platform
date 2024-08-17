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
    case 'get_citys_list':
        $err = isset_columns($_POST, ['txt']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $rows = SQL_ROWS_FIELD(q("SELECT id, name FROM city WHERE name LIKE ('%".db_secur($post['txt'])."%') LIMIT 15"), 'name');
        ans($rows);
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
