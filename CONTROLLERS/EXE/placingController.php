<?php
include_CSS('profil');
include_CSS('basket');

$B = new BASKET($_SESSION['basket'] ?? '');
$P = PROFIL::init(Access::userID());
$place = SITE::localization(true);

if(Access::scanLevel() > 0) {
    $cash = round((float) $P->get('cash', 0), 2);
} else {
    $cash = 0;
}

$arr = $B->get_basket();
$items = [];

$shop_id = $_GET['shop'] ?? -1;
$shop_id = (int)$shop_id;

//wtf($arr);

if($shop_id !== -1) {
    foreach($arr as $k => $v) {
        if((int)$v['ITEM']['PROPS']['discount'][0]['value'] > 0) {
            $price = round($v['ITEM']['price'], 2);
            $v['ITEM']['price'] = $price - ($price*((int)$v['ITEM']['PROPS']['discount'][0]['value'])/100);
        }
        if ((int)$v['shop_id'] === $shop_id) {
            $items[$v['product_id']] = $v;
        }
    }
}

$types_pay = SUBD::get_enum_from_table('orders', 'type_pay');
//wtf($items, 1);
array_shift($types_pay);
//wtf($types_pay, 1);