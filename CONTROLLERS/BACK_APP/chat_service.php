<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'call':
        $err = isset_columns($_POST, ['user_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        if($row = SUPPORT::user_is_support($post['user_id'])) {
            TELE::send_at_user_id($post['user_id'], "**Обращение в поддержку**\r\n".Access::create_access_link('chat', $post['user_id'], 60));
            ans('ok');
        }
        error('Мы сожалеем, но все операторы на данный момент заняты. Попробуйте обратиться позже...');
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
