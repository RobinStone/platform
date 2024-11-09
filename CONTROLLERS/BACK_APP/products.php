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
    case 'render_filter_products':
        $err = isset_columns($_POST, ['arr']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        INCLUDE_CLASS('shops', 'shop');
        $arr = $post['arr'];

        $sorted_by = $post['sorted_by'] ?? '';
        $sorte_direct = $post['sorted_direct'] ?? '';
        $items_type = $post['items_type'] ?? 'list';

        if($items_type !== 'card' && $items_type !== 'list') {
            $items_type = 'list';
        }

        $sorte_direct = db_secur($sorte_direct);

        /**
         * Позволяет внедрить часть запроса в фильтр выдачи товаров,
         * для отсечения результатов.
         * (в данном случае отбираются только активные товары, количество которых или
         * бесконечное или > 0).
         * После применения фильтра он сразу сбрасывается.
         */
        $_SESSION['injected_filter_query_array'][] = "(indexer.active=1 AND indexer.status='active' AND (indexer.count>0 OR indexer.count=-1))";

        $rows = SHOP::filter($arr, "", [0,50], [0,50], [$sorted_by=>$sorte_direct]);

        if(count($rows) > 0) {
            $rows = SHOP::get_products_list_at_code_array($rows, true, $sorted_by, $sorte_direct, [0, 50]);

            if(!empty($rows)) {
                INCLUDE_CLASS('shops', 'favorite');
                $rows = FAVORITE::verify_like_products($rows, Access::userID(), 'shop_id', 'prod_id');
                if(Access::scanLevel() < 1) {
                    $basket = $_COOKIE['basket'] ?? '';
                } else {
                    $basket = $_COOKIE['basket-id-user'] ?? '';
                }

                $basket_arr = $rows;
                $rows = [];
                foreach($basket_arr as $v) {
                    $rows[$v['shop_id']."_".$v['prod_id']] = $v;
                }

                if(isset($basket) && $basket !== '') {
                    $B = new BASKET($basket);
                    $rows = $B->verify_in_basket_product($rows);
                } else {
                    foreach($rows as $k=>$v) {
                        $v['IN_BASKET'] = 0;
                        $rows[$k] = $v;
                    }
                }
                if(count($rows) > 0) {
                    if($items_type === 'list') {
                        ans('ok', render('vertical-filter-list', ['arr'=>$rows]));
                    }elseif($items_type === 'card') {
                        ob_start();
                        foreach($rows as $k=>$v) {
                            $shop_id = $v['shop_id'];
                            $product_id = $v['prod_id'];
                            $discount = $v['PROPS']['discount'][0]['value'];
                            include './TEMPLATES/one-cart.php';
                        }
                        ans('ok', ob_get_clean());
                    }
                } else {
                    ans('not content', '');
                }
            }
        } else {
            ans('not content', '');
        }
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
