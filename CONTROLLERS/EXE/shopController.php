<?phpAccess::auto_cookies_auth();INCLUDE_CLASS('shops', 'shop');$shop = SHOP::get_shop_domain($_GET['route'] ?? '');//wtf($shop);if($shop === false) {    header('Location: /404');    exit;}if(!file_exists('./IMG/img100x100/'.$shop['logo'])) {    $shop['logo'] = 'r.svg';    $owner_shop_id = SHOP::get_owner_of_shop($shop['id']);    PROFIL::get_profil($owner_shop_id)->add_alert(        ALERT_TYPE::ATTANTION,        [            'text'=>'Похоже у Вашей площадки нет логотипа? Это может отрицательно сказаться на её продажах.<br>Рекомендуем, добавить логотип по этой ссылке.',            'link'=>'/profil?title=my_shops'        ],   'not_shop_logo'    );}$us = SUBD::getLineDB('users', 'id', $shop['owner']['id']);if(is_array($us)) {    $P = PROFIL::create($us['login'], PROFIL_TYPE::login);} else {    Message::addError('Неудалось найти пользователя создавшего товар...');    header('Location: /404');}$saler_name = $P->get('name');if($saler_name === '') {    $saler_name = 'НЕТ ИМЕНИ';}$img = $P->get_field('avatar');if($img === '-' || $img === '' || !file_exists('./IMG/img100x100/'.$img)) {    $img = '/DOWNLOAD/20230531-120816_id-2-498113.svg';} else {    $img = '/IMG/img100x100/'.$img;}$P->set('avatar', $img);$P->set('saler_name', $saler_name);$data_reg = $P->get_field('dataReg');if($data_reg === '-' || $data_reg === '') {    $data_reg = date('d.m.Y');} else {    $data_reg = date('d.m.Y', strtotime($data_reg));}$score = round((float)$P->get('score'), 2);$reviews = 0;$id_write_card = (int)$P->get_field('id', -1);$subs = SQL_ONE_ROW(q("SELECT * FROM `subscriptions` WHERE `self_id`=".Access::userID()." AND `subscr_id`=".$id_write_card));if($subs === false) {    $subs = [];}$writer_text = "Подписаться";if(is_array($subs)) {    $writer_text = "Отписаться";}$id = (int)$P->get_field('id');$rows = SQL_ROWS(q("SELECT stars FROM reviews_prod WHERE owner_product=".$id));$stars = 0;foreach($rows as $v) {    $stars += (float)$v['stars'];}$reviews = count($rows);if($reviews > 0) {    $stars = round($stars / $reviews, 2);}$CAT = new CATALOGER();//say($CAT->cat);$main_cats = $CAT->main_cats;$filter = [];if(!empty($_COOKIE['shop_arr_codes'])) {    $arr = explode('|', $_COOKIE['shop_arr_codes']);    $preview_products = SHOP::get_products_list_at_code_array($arr);} else {    $preview_products = SHOP::get_mix_products_at_all_shops(false, [0,8], -1, -1, -1, -1, [$shop['id']]);}//wtf($preview_products);//foreach($preview_products as $k=>$v) {//    $preview_products[$k]['main_cat_trans'] = $CAT->id2main_cat($v['main_cat'], true);//    $preview_products[$k]['under_cat_trans'] = $CAT->id2under_cat($v['under_cat'], true);//    $preview_products[$k]['action_list_trans'] = $CAT->id2action_list($v['action_list'], true);//}//wtf($preview_products);if(!empty($preview_products)) {    INCLUDE_CLASS('shops', 'favorite');    $preview_products = FAVORITE::verify_like_products($preview_products, Access::userID(), 'shop_id', 'prod_id');    if(Access::scanLevel() < 1) {        $basket = $_COOKIE['basket'] ?? '';    } else {        $basket = $_COOKIE['basket-id-user'] ?? '';    }    $basket_arr = $preview_products;    $preview_products = [];    foreach($basket_arr as $v) {        $preview_products[$v['shop_id']."_".$v['prod_id']] = $v;    }    if(isset($basket) && $basket !== '') {        $B = new BASKET($basket);        $preview_products = $B->verify_in_basket_product($preview_products);    } else {        foreach($preview_products as $k=>$v) {            $v['IN_BASKET'] = 0;            $preview_products[$k] = $v;        }    }}//wtf($preview_products);//wtf(SITE::$ip, 1);//wtf(SITE::$place, 1);