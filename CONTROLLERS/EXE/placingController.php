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

$city_location_id = -1;

$cdek_city_id_from = -1;
$cdek_city_id_to = -1;

INCLUDE_CLASS('shops', 'props_commander');

if($shop_id !== -1) {
    foreach($arr as $k => $v) {
        if((int)$v['ITEM']['PROPS']['discount'][0]['value'] > 0) {
            $price = round($v['ITEM']['price'], 2);
            $v['ITEM']['price'] = $price - ($price*((int)$v['ITEM']['PROPS']['discount'][0]['value'])/100);
        }
        if ((int)$v['shop_id'] === $shop_id) {
            $items[$v['product_id']] = $v;
        }
        if($city_location_id === -1) {
            // тут происходит вычисление ID города(CDEK) откуда будет отправляться посылка CDEK
            $city_location_id = PROPS_COMMANDER::get_prop($v['shop_id'], $v['product_id'], 'city_id');
            $cdek_city = GEONAMER::id_citys_to_names([$city_location_id]);
            if(isset($cdek_city[$city_location_id])) {
                if($cdek_city = CDEK2::get_CDEK_city($cdek_city[$city_location_id]['name'])) {
                    $cdek_city_id_from = (int)$cdek_city['code'];
                }
            }
        }
    }
}

$types_pay = SUBD::get_enum_from_table('orders', 'type_pay');

array_shift($types_pay);

include_CDN_JS('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey='.Core::$YANDEXGEOCODER.'&suggest_apikey='.Core::$SUGGEST_GEOCODER);

$cdek = [];
if(SITE::$profil) {
    $cdek = PROFIL::init(Access::userID())->get_attachment('delivery_info.cdek', '');
    if(is_array($cdek)) {
        include_JS('maps_controller');
        $cdek_city_id_to = (int)$cdek['city_code'];
        if($cdek_city_id_from !== -1 && $cdek_city_id_to !== -1) {
            $cdek_price_list = CDEK2::get_tarif($cdek_city_id_from, $cdek_city_id_to, 500);
            if(isset($cdek_price_list['Экспресс склад-склад'])) {
                $cdek_price_params = $cdek_price_list['Экспресс склад-склад'];
            }
        }
    }
}

