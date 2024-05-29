<?php
$admin_panel = 0;
if(Access::scanLevel() > 0) {
    $P = new PROFIL(Access::userID());
    if($P->get('ADMIN', '0') !== '0') {
        $admin_panel = (int)$P->get('ADMIN', '0');
        $admin_panel_array = $P->get_sys_param('admin_panel');
//        wtf($admin_panel_array, 1);
    }
    $user_img = $P->get_field('avatar');
    if($user_img === '-' || $user_img === '' || !file_exists('./DOWNLOAD/'.$user_img)) {
        $user_img = '/DOWNLOAD/20230531-120816_id-2-498113.svg';
    } else {
        if(file_exists('./IMG/img100x100/'.$user_img)) {
            $user_img = '/IMG/img100x100/'.$user_img;
        } else {
            $user_img = '/DOWNLOAD/20230531-120816_id-2-498113.svg';
        }
    }
} else {
    $user_img = '/DOWNLOAD/20230531-120816_id-2-498113.svg';
}

include_JS('sys_mess');

$my_place = SITE::$my_place;

INCLUDE_CLASS('shops', 'favorite');
$favorite_count = FAVORITE::get_count_favorite_orders(Access::userID());

$count_in_basket = 0;
if(Access::scanLevel() < 1) {
    $basket = $_COOKIE['basket'] ?? '';
} else {
    $cookies = BASKET::get_cookies_code_from_user_id(Access::userID());
    if($cookies !== '') {
        $basket = $cookies;
        setcookie('basket-id-user', $cookies, time() + 31556926, '/');
    } else {
        $basket = $_COOKIE['basket-id-user'] ?? '';
    }
}
//wtf($basket, 1);
if($basket !== '') {
    $B = new BASKET($basket);
    $count_in_basket = $B->get_count();
    $_SESSION['basket'] = $basket;
} else {
    unset($_SESSION['basket']);
}
$_SESSION['count_in_basket'] = $count_in_basket;

