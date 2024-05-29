<?php
include_once './CONTROLLERS/ajax_before.php';
if(isset($_GET['activate'])) {
    $ip = Access::scanIP();
    $row = SUBD::getLineDB('garbage', 'id', (int)$_GET['activate']);
    if(is_array($row)) {
        $arr = explode('~~', $row['resurs']);
        if($row['ip'] === $ip) {
            $params = [
                'email='.$arr[2],
                'name='.$arr[0],
                'phone='.VALUES::decodePhone($arr[1])
            ];
            q("
            INSERT INTO `users` SET 
            `avatar` = '20230608-152016_id-2-950116.svg',
            `login` = '".db_secur($arr[2])."',
            `status` = 1,
            `level` = '1',
            `modality` = 'user',
            `tele` = '-',
            `phone` = '".VALUES::decodePhone($arr[1])."',
            `email` = '".db_secur($arr[2])."',
            `password` = '".crypter($arr[3])."',
            `params` = '".db_secur(implode('|', $params))."'
            ");
            $last = SUBD::get_last_id();
            $arr = SUBD::getLineDB('users', 'id', $last);
            $_SESSION['user'] = $arr;
            $P = new PROFIL($last);
            $P->add_alert(ALERT_TYPE::MESSAGE, ['text'=>'Обратите внимание на секцию ОПОВЕЩЕНИЯ, если вы продавец - она вам будет полезна', 'link'=>'/profil?title=account#alerts'], 'attantion_on_notification');
            q("DELETE FROM `garbage` WHERE `id`=".(int)$row['id']);
            Message::addMessage('Регистрация прошла успешно!..');
            header('Location: /profil?title=account');
            exit;
        }
    }
}

if(isset($_POST['reg-r'])) {
    if(isset($_POST['name-r'], $_POST['phone-r'], $_POST['email-r'], $_POST['password-r'])) {
        $_POST['name-r'] = trim($_POST['name-r']);
        $_POST['phone-r'] = trim($_POST['phone-r']);
        $_POST['email-r'] = trim($_POST['email-r']);
        $_POST['password-r'] = trim($_POST['password-r']);

        $arr[] = $_POST['name-r'];
        $arr[] = $_POST['phone-r'];
        $arr[] = $_POST['email-r'];
        $arr[] = $_POST['password-r'];

        if(mb_strlen($_POST['name-r']) < 2) {
            error('Имя должно состоять минимум из 2-ух букв');
        }
        if(mb_strlen($_POST['phone-r']) < 7) {
            error('Номер телефона необходимо вводить в соответствии с правилами (никаких букв и определённая длинна)');
        }
        $email = SUBD::getLineDB('users', 'email', $_POST['email-r']);
        if(is_array($email)) {
            error('Пользователь с таким E-Mail уже присутствует в системе...');
        }
        q("
        INSERT INTO `garbage` SET
        `ip` = '".Access::scanIP()."',
        `load_time` = '".date('Y-m-d H:i:s')."',
        `resurs` = '".db_secur(implode('~~', $arr))."'
        ");
        $id = SUBD::get_last_id();
//        $headers = 'MIME-Version: 1.0' . "\r\n";
//        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
//        $body = "<div><h1>".Core::$SiteName."</h1><div>Для активации вашей учётной записи - пройдите по <a href='".Core::$DOMAIN."/?activate=".$id."'>этой ссылке</a>.</div></div>";
//        mail($_POST['email-r'], 'Активация учётной записи.', $body, $headers);
        $arrm[$_POST['email-r']] = [
            'name'=>$_POST['name-r'],
            'login'=>$_POST['email-r'],
            'password'=>$_POST['password-r'],
            'link'=>Core::$DOMAIN.'/?activate='.$id,
        ];
        INCLUDE_CLASS('mailer', 'MAILER_BUILDER');
        $ans = MAILER_BUILDER::send_template_mail('Регистрация', 'Активация учётной записи', $arrm);
        ans('ok');
    } else {
        error('Все поля должны быть заполнены!..');
    }
}

if(isset($_POST['email-r'], $_POST['password-r'], $_POST['auth-r'])) {
    $_POST = trimAll($_POST);
    $login = db_secur($_POST['email-r']);
    if(VALUES::is_phone($login)) {
        $login = VALUES::decodePhone($login);
    }
    $ask = q("
        SELECT * FROM `users` WHERE 
        (`email` = '".$login."' OR `login`='".$login."' OR `phone` = '".$login."') 
        AND `password` = '".crypter($_POST['password-r'])."' LIMIT 1");
    if($ask->num_rows) {
        $row = $ask->fetch_assoc();
        $code = crypter(date('d-m-Y H:i:s') . crypter(date('d-m-Y H:i:s')) . $row['id']);
        setcookie('autoauth', $code, time() + 31556926, '/');
        q("
            UPDATE `users` SET
            `operatorHesh`        = '" . $code . "',
            `ip`                  = '" . Access::scanIP() . "',
            `dataLast`            = '" . date('Y-m-d H:i:s') . "'
            WHERE 
            `id`                  = " . (int)$row['id'] . "
           ");

        $_SESSION['user'] = $row;
        if(Access::scanLevel() >= 6) {
            if(PROFIL::init(Access::userID())->get_attachment('admin_panel.open-auth') === 'open') {
                echo 'admin';
            } else {
                echo 'profil';
            }
        } else {
            Message::addMessage('Авторизация - успешна!..');
            echo 'ok';
        }
    } else {
        echo 'no';
    }
    exit;
}

include_JS('swipper');
include_CSS('swipper');

include_once './APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
include_once './APPLICATIONS/SHOPS/libs/class_SHOP.php';

$dt = Core::$DT;
$CAT = new CATALOGER();
$main_cats = $CAT->main_cats;
//wtf($CAT->main_cats, 1);

$start_micro = microtime(true);

$filter = [];

//$filter = [
//    'self'=>[
//        'changed'=>'DESC',
//    ],
//];

$preview_products = SHOP::get_mix_products_at_all_shops(true, [0,8], -1, -1, -1, -1, [], true, $filter);
foreach($preview_products as $k=>$v) {
    $preview_products[$k]['main_cat_trans'] = $CAT->id2main_cat($v['main_cat'], true);
    $preview_products[$k]['under_cat_trans'] = $CAT->id2under_cat($v['under_cat'], true);
    $preview_products[$k]['action_list_trans'] = $CAT->id2action_list($v['action_list'], true);
}

$my_place = SITE::$my_place;

//wtf($preview_products, 1);

//say($preview_products);

//wtf($my_place, 1);

$poss = GEONAMER::get_current_position();

if($poss === [0, 0]) {
    wtf('Пока закрыто для всяких левых...', 1);
}
//TELE::send_at_user_name('robin', (microtime(true) - $start_micro));
//wtf($poss, 1);

//$P = PROFIL::init(Access::userID());
//$P->add_sys_param('tester', [
//    'param1'=>'val1',
//    'param2'=>[
//        'robin'=>'bobin',
//        'andron'=>'base',
//        'bubu'=>[
//            0=>'one',
//            1=>'two',
//            2=>'true',
//        ],
//        'copy2'=>123,
//    ],
//    'param3'=>'val3',
//]);