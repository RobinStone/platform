<?php$start_time = microtime(true);if(isset($_POST['com'])) {    SITE::$user_id = Access::userID();    ONLINE::INIT();    if(isset($_POST['app'])) {        if(file_exists('./APPLICATIONS/'.$_POST['app'].'/ajax.php')) {            if(is_dir('./APPLICATIONS/'.$_POST['app'].'/libs')) {                    $files = glob('./APPLICATIONS/'.$_POST['app'].'/libs/*.php');                    foreach($files as $v) {                        include_once $v;                    }                }            include_once('./APPLICATIONS/'.$_POST['app'].'/ajax.php');        } else {            Message::addError('Не найден обработчик AJAX<br>./APPLICATIONS/'.$_POST['app'].'/ajax.php');        }    } else {        include_once('./CONTROLLERS/ajax.php');    }    exit;}$body = '';$type_pages_arr = [    'main'=>'Титульная станица',    'profil'=>'Профиль пользователя',    'reviews'=>'Отзывы',    'create'=>'Создание объявления',    'messenger'=>'Сообщения',];$_GET['route'] = $_GET['route'] ?? '';$dt = date('Y-m-d H:i:s');$route = explode('/', $_GET['route']);foreach($route as $k=>$v) {    if($v === '' || stristr($v, '.')) {        unset($route[$k]);    }}$api = [];if(count($route) === 0) {    $route = 'main';    Core::$META_TYPE = 'main';} else {    if(isset($_GET['title'])) {        Core::$META_TYPE = $_GET['title'];    } else {        Core::$META_TYPE = end($route);    }    if(isset($route[0]) && $route[0] === 'api') {        include_once './CONTROLLERS/API/settings.php';        API::INIT();        print_response_status(Response::STATUS_BAD_REQUEST);    }    $route = implode('/', $route);}//wtf($_GET);//wtf('API 0.0.1', 1);//wtf('service unavailable');//t($route);switch($route) {    case 'cron':        include_once('./CONTROLLERS/EXE/cronController.php');        exit;    case 'events':        include_once('./CONTROLLERS/EXE/eventsController.php');        exit;    case 'q_cass':        include_once('./CONTROLLERS/EXE/q_cassController.php');        exit;    case 'tele':        include_once('./CONTROLLERS/EXE/teleController.php');        exit;    case 'mega':        include_once('./CONTROLLERS/EXE/megaauthController.php');        exit;    case 'main':    case 'auth':    case 'profil':    case 'basket':    case 'reviews':    case 'chat':    case 'speaker':    case 'finder':    case 'placing':    case 'create':    case 'socket':    case 'messenger':        SITE::$IP_region_fool = GEO2IP::get_info();        SITE::$user_id = Access::userID();        ONLINE::INIT();        include_CSS('seo-panel');        if(isset($type_pages_arr[$route])) {            set_type_page($type_pages_arr[$route]);        }        SEO::$template_name = $route;        $SEO = new SEO($_GET, [$route]);        $body = template($route, 'header', 'footer');        break;    case 'test':        SITE::$IP_region_fool = GEO2IP::get_info();        SITE::$user_id = Access::userID();        ONLINE::INIT();        include_CSS('seo-panel');        if(isset($type_pages_arr[$route])) {            set_type_page($type_pages_arr[$route]);        }        SEO::$template_name = $route;        $SEO = new SEO($_GET, [$route]);        $body = template('test', 'header', 'footer');        break;    case 'player':        $body = template('player_one', 'header', 'footer');        break;    case 'owner':        SITE::$user_id = Access::userID();        ONLINE::INIT();        include_CSS('seo-panel');        if(isset($type_pages_arr[$route])) {            set_type_page($type_pages_arr[$route]);        }        SEO::$template_name = $route;        $SEO = new SEO($_GET, [$route]);        $body = template('owner-page', 'header', 'footer');        break;    case 'app':        $body = template('app', '', '');        break;    case 'auth/exit':        PROFIL::out_login();        Message::addMessage('Осуществлён выход из системы...');        header('Location: /');        exit;    case 'admin':        SITE::$user_id = Access::userID();        ONLINE::INIT();        RBS::js_script_link_before_once("./JS/deamon.js");        RBS::js_script_link_before_once('./JS/memory_pull.js');        $body = template('admin', 'adm-header', 'adm-footer');        break;    case 'less':        $body = template('less', '', '');        break;    default:        SITE::$user_id = Access::userID();        ONLINE::INIT();        INCLUDE_CLASS('shops', 'shop');        if(isset($_GET['s'], $_GET['prod'])) {            if(!$product = SHOP::get_one_product_at_code_if_isset((int)$_GET['s']."_".(int)$_GET['prod'], true)) {                $body = template('404', 'header', 'footer');            } else {                include_CSS('seo-panel');                set_type_page('Страница товара');                $S = SEO::get_info($_GET, [$route], 'PRODUCT');                Core::$META_TYPE = 'PRODUCT';                SEO::$template_name = 'product';                $row = $product;                $row['ID'] = (int)$_GET['s'] . '_' . (int)$_GET['prod'];                $row['SHOP'] = SUBD::get_executed_rows('shops', 'id', (int)$_GET['s'])[(int)$_GET['s']];                Core::$CANONICAL = Core::$DOMAIN . $row['main_cat_trans'] . "/" . $row['under_cat_trans'] . "/" . $row['under_cat_trans'] . "/" . VALUES::translit($row['name']) . "?s=" . (int)$_GET['s'] . "&prod=" . (int)$_GET['prod'];                $body = template('product', 'header', 'footer', ['row' => $row, 'SEO' => $S]);            }        } else {            $cat_path = explode('/', $route);            include_CSS('seo-panel');            INCLUDE_CLASS('shops', 'cataloger');            $CT = new CATALOGER();            switch(count($cat_path)) {                case 1:                    $row = SUBD::getLineDB('shops_categorys', 'cat_trans', $cat_path[0]);                    if(is_array($row)) {                        set_type_page('Основная категория');                        SEO::$template_name = 'category';                        SEO::get_info($_GET, [$route], 'MAIN-CAT');                        Core::$META_TYPE = 'MAIN-CAT';                        $body = template('filter', 'header', 'footer', [                                'type_page'=>'main_cat',                                'current'=>$row,                                'body_arr'=>$CT->get_all_under_cats($row['id'])                        ]);                    } else {                        /**                         * ПОДКЛЮЧЕНИЕ К МАГАЗИНУ (домен 2-ого уровня)                         */                        if($row = SUBD::getLineDB('shops', 'domain', db_secur($route))) {                            SEO::$template_name = 'shop';                            if($dt < $row['active_to']) {                                switch ($row['title']) {                                    case 'Интернет-магазин':                                        set_type_page('shop');                                        SEO::get_info($_GET, [$route], 'shop');                                        Core::$META_TYPE = 'shop';                                        set_title($row['name']);                                        set_description($row['descr']);                                        $body = template('shop', 'header', 'footer');                                        break;                                    case 'Витрина':                                        set_type_page('shop');                                        SEO::get_info($_GET, [$route], 'shop');                                        Core::$META_TYPE = 'shop';                                        set_title($row['name']);                                        set_description($row['descr']);                                        $body = template('shop', 'header', 'footer');                                        break;                                }                            }                        }                    }                    break;                case 2:                    $main_cat = $CT->get_id_from_cat_at_field($cat_path[0], 'cat_trans', 'main_cat');                    if($main_cat !== -1) {                        $lst = $CT->get_all_under_cats($main_cat);                        foreach($lst as $v) {                            if($v['undercat_trans'] === $cat_path[1]) {                                $row = SUBD::getLineDB('shops_undercats', 'undercat_trans', $cat_path[1]);                                set_type_page('Под-категория');                                SEO::$template_name = 'undercat';                                SEO::get_info($_GET, [$route], 'UNDER-CAT');                                Core::$META_TYPE = 'UNDER-CAT';                                $body = template('filter', 'header', 'footer', [                                        'main_cat'=>$CT->main_cats[$main_cat],                                        'before_path'=>'/'.$cat_path[0].'/'.$cat_path[1].'/',                                        'type_page'=>'under_cat',                                        'current'=>$row,                                        'body_arr'=>$CT->get_all_action_list($main_cat, $v['id']),                                ]);                            }                        }                    }                    break;                case 3:                    $main_cat = $CT->get_id_from_cat_at_field($cat_path[0], 'cat_trans', 'main_cat');                    $under_cat = $CT->get_id_from_cat_at_field($cat_path[1], 'undercat_trans', 'under_cat');                    if($main_cat !== -1 && $under_cat !== -1) {//                        $row = SUBD::getLineDB('shops_lists', 'translit', $cat_path[2]);                        $row = SQL_ONE_ROW(q("                            SELECT * FROM shops_lists WHERE                             main_cat=".$main_cat." AND                             undercat=".$under_cat." AND                             translit='".db_secur($cat_path[2])."'                             LIMIT 1                             "));                        set_type_page('Список в подкатегории');                        SEO::$template_name = 'actionlist';                        SEO::get_info($_GET, [$route], 'ACTION-LIST');                        Core::$META_TYPE = 'ACTION-LIST';                        $body = template('filter', 'header', 'footer', [                                'main_cat'=>$CT->main_cats[$main_cat],                                'under_cat'=>$CT->under_cats[$under_cat],                                'before_path'=>'/'.$cat_path[0].'/'.$cat_path[1].'/'.$cat_path[2],                                'type_page'=>'action_list',                                'current'=>$row,                                'body_arr'=>[],                        ]);                    }                    break;                default:                    SEO::get_info($_GET, [$route]);                    break;            }        }    break;}if($body === '') { $body = template('404', 'header', 'footer'); }echo $body;//TELE::send_at_user_name('robin', microtime(true) - $start_time);