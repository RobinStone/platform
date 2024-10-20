<?php
include_once './APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
include_once './APPLICATIONS/SHOPS/libs/class_SHOP.php';
include_once './APPLICATIONS/SHOPS/libs/class_PROPS_COMMANDER.php';

Access::auto_cookies_auth();

include_JS('checker-toggler');

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

if(!empty($preview_products)) {
    INCLUDE_CLASS('shops', 'favorite');
    $preview_products = FAVORITE::verify_like_products($preview_products, Access::userID(), 'shop_id', 'prod_id');

    if(Access::scanLevel() < 1) {
        $basket = $_COOKIE['basket'] ?? '';
    } else {
        $basket = $_COOKIE['basket-id-user'] ?? '';
    }

    $basket_arr = $preview_products;
    $preview_products = [];
    foreach($basket_arr as $v) {
        $preview_products[$v['code']] = $v;
    }

    if(isset($basket) && $basket !== '') {
        $B = new BASKET($basket);
        $preview_products = $B->verify_in_basket_product($preview_products);
    } else {
        foreach($preview_products as $k=>$v) {
            $v['IN_BASKET'] = 0;
            $preview_products[$k] = $v;
        }
    }
}

$shop_id = (int)$_GET['s'];
$product_id = (int)$_GET['prod'];

$row['SHOP']['CITY_ALL'] = SUBD::getLineDB('cities', 'name', $row['SHOP']['city']);

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

if(isset($basket[$code_product])) {
    $in_basket_svg = RBS::SVG('basket_on.svg');
}

$data_created = explode('-', explode(' ', $row['created'])[0]);
$time_created = explode(':', explode(' ', $row['created'])[1]);

$data_created = $data_created[2]." ".VALUES::intToMonth($data_created[1], true).", ".$data_created[0];
$time_created = $time_created[0].":".$time_created[1];

$showed = SHOP::get_showed_count($row['ID']);

SHOP::add_showed_count_plus($row['ID']);

if(!empty($row)) {
    $schema = get_product_schema();

    foreach($row['PROPS'] as $one_prop) {
        if($one_prop[0]['schema_id'] <= 17) {
            $schema[$one_prop[0]['field_name']]['value'] = $one_prop[0]['value'];
            $schema[$one_prop[0]['field_name']]['id_i'] = $one_prop[0]['id'];
        }
    }
    $additional_fields = SHOP::get_additional_fields_for_cats(
        $row['main_cat_id'],
        $row['under_cat_id'],
        $row['action_list_id']
    );

    if(is_array($additional_fields)) {
        PROPS_COMMANDER::aply_values_for_additional_schema($additional_fields, $row['PROPS']);
    }

    $CAT = CATALOGER::INIT();

    SORT::change_preview_key($schema, 'alias', 'field_name');
    $schema['product_name']['value'] = $row['name'];
    $schema['price']['value'] = $row['price'];
    $schema['count']['value'] = $row['count'];

    $schema['category']['value'] = $row['main_cat'];
    $schema['category']['real'] = $CAT->main_cat_name_to_id($row['main_cat']);

    $schema['under-cat']['value'] = $row['under_cat'];
    $schema['under-cat']['real'] = $CAT->under_cat_name_to_id($schema['category']['real'], $row['under_cat']);

    $schema['action-list']['value'] = $row['action_list'];
    $schema['action-list']['real'] = $CAT->action_list_name_to_id($row['action_list']);

    SORT::change_preview_key($schema, 'field_name', 'alias');

    if(isset($additional_fields) && is_array($additional_fields)) {
        $schema = array_merge($schema, $additional_fields);
    }

    $not_show_fields = [
        'Название товара',
        'Стоимость',
        'Описание',
        'Телефон заказа',
        'Скидка %',
    ];

//    wtf($schema);
}

