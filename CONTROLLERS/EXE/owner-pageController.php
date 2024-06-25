<?php
include_CSS('shop');

$login = $_GET['o'] ?? '';

if(empty($login)) {
    header('Location: /');
    exit;
}

if($row = SQL_ONE_ROW(q("SELECT id from users WHERE login='".db_secur($login)."' LIMIT 1"))) {

} else {
    header('Location: /');
    exit;
}

$P = PROFIL::create($login, PROFIL_TYPE::login);


$shops = SQL_ROWS(q("
SELECT * FROM `shops` WHERE
`owner`         = ".(int)$P->get_field('id')." AND
`title`        <> 'Бесплатный' AND
`active_to`     > '".SITE::$dt."'
"));

//wtf($shops);

INCLUDE_CLASS('shops', 'shop');

//$prods = SHOP::get_mix_products_at_all_shops()