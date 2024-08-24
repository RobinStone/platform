<?php
switch($_POST['com']) {
    case 'test':
        $err = isset_columns($_POST, ['']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'get_product_list':
        $err = isset_columns($_POST, ['arr']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        INCLUDE_CLASS('shops', 'shop');
        $arr = $post['arr'];
        $rows = SHOP::filter($arr);
        if(count($rows) > 0) {
            $rows = SHOP::get_products_list_at_code_array($rows, false);
        }
        ans($rows);
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
