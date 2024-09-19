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
    case 'find_in_shop':
        $err = isset_columns($_POST, ['words_arr', 'shop_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }

        $querys = [];

        foreach($post['words_arr'] as $v) {
            if(mb_strlen($v) >= 3) {
                $txt = db_secur(mb_strtolower($v));
                $querys[] = " (`name` LIKE '%" . $txt . "%' OR `keys` LIKE '%" . $txt . "%') ";
            }
        }

        setcookie('shop_arr_codes', '', time() + 31556926, '/');
        setcookie('shop_arr_codes_text', '', time() + 31556926, '/');

        if(count($querys) > 0) {
            INCLUDE_CLASS('shops', 'shop');
            $rows = SQL_ROWS(q("
            SELECT CONCAT(shop_id, '_', prod_id) AS CODE FROM indexer
            WHERE 
              shop_id=".(int)$post['shop_id']." AND 
              active=1 AND 
              status='active' AND 
              ( ".implode(' OR ', $querys)." ) 
              LIMIT 30
            "));
            if(count($rows) > 0) {
                $arr = array_column($rows, 'CODE');
                setcookie('shop_arr_codes_text', implode(' ', $post['words_arr']) , time() + 31556926, '/');
                setcookie('shop_arr_codes', implode("|", $arr), time() + 31556926, '/');
            }
            ans('ok', $rows);
        } else {
            ans('ok', []);
        }
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
