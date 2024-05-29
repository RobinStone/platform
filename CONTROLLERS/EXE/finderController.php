<?php
$txt = $_GET['text'] ?? '';

if(mb_strlen($txt) >= 3) {
    INCLUDE_CLASS('shops', 'shop');
    INCLUDE_CLASS('shops', 'cataloger');
    $rows = SEARCH::search_context($txt, true, 20);

    $CAT = new CATALOGER();

    $arr = SHOP::get_products_list($rows);

    foreach($arr as $k=>$v) {
        $key = $v['shop_id']."_".$v['product_id'];

        $arr[$k]['trans'] = $v['SHOP']['trans'];
        $arr[$k]['PRICE'] = $v['SHOP']['PRICE'];
        $arr[$k]['PLACE'] = $v['SHOP']['PLACE'];
        $arr[$k]['DISCOUNT'] = $v['SHOP']['DISCOUNT'];
        $arr[$k]['FILES'][0] = $v['SHOP']['IMG'];
        $arr[$k]['id_product'] = $v['product_id'];
        $arr[$k]['main_cat_trans'] = $CAT->id2main_cat($v['SHOP']['main_cat'], true);
        $arr[$k]['under_cat_trans'] = $CAT->id2under_cat($v['SHOP']['under_cat'], true);
        $arr[$k]['action_list_trans'] = $CAT->id2action_list($v['SHOP']['action_list'], true);

        $arr[$key] = $arr[$k];
        unset($arr[$k]);
    }

//    wtf($arr, 1);

} else {
    $rows = [];
}