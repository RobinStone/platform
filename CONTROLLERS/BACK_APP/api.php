<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'token_create':
        if(Access::scanLevel() < 5) {
            error('Ошибка доступа. Требуется уровень 5+');
        }
        $err = isset_columns($_POST, ['login', 'password']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        if(mb_strlen($post['login']) < 4) {
            error('Логин не менеее 4-ёх символов');
        }
        if(mb_strlen($post['password']) < 4) {
            error('Пароль не менеее 10-ти символов');
        }
        if(Access::update_token($post['login'], $post['password'], ['user'=>Access::userID()], 30)) {
            ans('ok');
        }
        error('Токен - не создан!..');
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
