<?php
include_once './APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
include_once './APPLICATIONS/SHOPS/libs/class_SHOP.php';
include_once './APPLICATIONS/SHOPS/libs/class_PROPS_COMMANDER.php';


include_CDN_JS('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey='.Core::$YANDEXGEOCODER.'&suggest_apikey='.Core::$SUGGEST_GEOCODER);

include_JS('map_coder');
//wtf($row);
$items = [
    $row['main_cat'] => $row['main_cat_trans'],
    $row['under_cat'] => $row['under_cat_trans'],
    $row['action_list'] => $row['action_list_trans'],
];

$img_proff = $row['PROPS']['images'][0]['value'];
add_META('<meta property="og:image" content="'.Core::$DOMAIN.'IMG/img100x100/'.$img_proff.'">');
add_META('<meta property="og:title" content="'.$row['name'].'">');

$descr_id = $row['PROPS']['descr'][0]['id'];
$descr = $row['PROPS']['descr'][0]['value'];
$place = $row['PROPS']['place'][0]['value'];
$discount = (int)$row['PROPS']['discount'][0]['value'];
$discount_price = 0;

$price = round((float)$row['price'], 2);

if($discount > 0) {
    $discount_price = $price - ($discount/100*$price);
}

$slides = [];
foreach($row['PROPS']['images'] as $imgs) {
    $slides[] = $imgs['value'];
}


$preview_products = SHOP::get_random_products_from_one_shop((int)$row['SHOP']['id'], true, 8, -1, -1, -1, true);

foreach($preview_products as $k=>$v) {
    if($row['ID'] === $k) {
        unset($preview_products[$k]);
    }
}

//wtf($preview_products);

$shop_id = (int)$_GET['s'];
$product_id = (int)$_GET['prod'];

$row['SHOP']['CITY_ALL'] = SUBD::getLineDB('cities', 'name', $row['SHOP']['city']);
if($row['SHOP']['CITY_ALL'] === false) {
    $row_c = SQL_ROWS(q("SELECT * FROM `coords` WHERE `shop_id`=".$shop_id." AND `product_id`=".$product_id." LIMIT 1"));
    if(count($row_c) > 0) {
        $address = explode(', ', $PRO->get_all_props_at_field_name('Расположение', true)['VALUE']);
        $address = array_reverse($address);
        $row['SHOP']['CITY_ALL'] = [
            'id'=>-1,
            'shirota'=>$row_c[0]['lat'],
            'dolgota'=>$row_c[0]['lng'],
        ];
        if(is_array($address)) {
            $row['SHOP']['CITY_ALL']['name'] = $address[0];
            if(count($address) >= 2) {
                $row['SHOP']['CITY_ALL']['region'] = $address[1];
            }
            if(count($address) >= 3) {
                $row['SHOP']['CITY_ALL']['country'] = $address[2];
            }
        } else {
            $row['SHOP']['CITY_ALL']['name'] = $address;
        }
    }
}

if($place === '') {
    $place = $row['SHOP']['city'];
}

$best = SQL_ROWS(q("SELECT * FROM `best_prod` WHERE `user_id`=".Access::userID()." AND `shop_id`=".$shop_id." AND `product_id`=".$product_id." LIMIT 1"));
if(count($best) > 0) {
    $best = ' hart-red ';
} else {
    $best = '';
}

$phone = $row['PROPS']['phone'][0]['value'];

$B = new BASKET($_SESSION['basket'] ?? '');
$basket = $B->get_basket();

$in_basket_svg = RBS::SVG('bag_box.svg');
$code_product = $shop_id."_".$product_id;
//t($code);
if(isset($basket[$code_product])) {
    $in_basket_svg = RBS::SVG('basket_on.svg');
}

//wtf($row, 1);
//wtf($PRO->get_only_non_static_params(), 1);
//wtf($SEO, 1);

//wtf($basket, 1);
//wtf($_SERVER, 1);

$data_created = explode('-', explode(' ', $row['created'])[0]);
$time_created = explode(':', explode(' ', $row['created'])[1]);

$data_created = $data_created[2]." ".VALUES::intToMonth($data_created[1], true).", ".$data_created[0];
$time_created = $time_created[0].":".$time_created[1];


//wtf($popular, 1);
$showed = SHOP::get_showed_count($row['ID']);

SHOP::add_showed_count_plus($row['ID']);

//wtf($showed, 1);
//wtf($slides, 1);