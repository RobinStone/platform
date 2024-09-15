<?php
$txt = $_GET['text'] ?? '';

if(mb_strlen($txt) >= 3) {
    INCLUDE_CLASS('shops', 'shop');
    INCLUDE_CLASS('shops', 'cataloger');
    $rows = SEARCH::search_context($txt, true, 20);

    $CAT = new CATALOGER();

    $arr = SHOP::get_products_list($rows);

    foreach($arr as &$v) {
        $v['code'] = $v['shop_id']."_".$v['product_id'];
        $v['prod_id'] = $v['product_id'];
        $v['main_cat_trans'] = $v['ITEM']['main_cat_trans'];
        $v['under_cat_trans'] = $v['ITEM']['under_cat_trans'];
        $v['action_list_trans'] = $v['ITEM']['action_list_trans'];
        $v['price'] = $v['ITEM']['price'];
        $v['PROPS'] = $v['ITEM']['PROPS'];
        unset($v['ITEM']['PROPS']);
    }
//wtf($arr);
    if(!empty($arr)) {
        INCLUDE_CLASS('shops', 'favorite');
        $arr = FAVORITE::verify_like_products($arr, Access::userID(), 'shop_id', 'prod_id');

        if(Access::scanLevel() < 1) {
            $basket = $_COOKIE['basket'] ?? '';
        } else {
            $basket = $_COOKIE['basket-id-user'] ?? '';
        }

        $basket_arr = $arr;
        $arr = [];
        foreach($basket_arr as $v) {
            $arr[$v['code']] = $v;
        }

        if(isset($basket) && $basket !== '') {
            $B = new BASKET($basket);
            $arr = $B->verify_in_basket_product($arr);
        } else {
            foreach($arr as $k=>$v) {
                $v['IN_BASKET'] = 0;
                $arr[$k] = $v;
            }
        }
    }

//    wtf($arr);

} else {
    $arr = [];
}