<?php
$ans = [];
include_once './CONTROLLERS/ajax_before.php';
$path = basename(__DIR__, '.php');
if(is_dir('./APPLICATIONS/'.$path.'/libs')) {
    $files = glob('./APPLICATIONS/'.$path.'/libs/*.php');
    foreach($files as $v) {
        include_once $v;
    }
}
////////////////////////////////////////////////////////////////////////////////////////
///////////////////////// КОД ВЫШЕ ДОБАВЛЯЕМ ВО ВСЕ ajax ///////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////


switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'append_banner_for':
        if(Access::scanLevel() <= 0) {
            error('Только для авторизованных...');
        }
        $err = isset_columns($_POST, ['main_cat', 'under_cat', 'action_list', 'id_banner', 'payed_for']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $banner = SUBD::getLineDB('banner', 'id', (int)$post['id_banner']);
        if(!is_array($banner)) {
            error('Ошибка поиска баннера');
        }
        if(Access::userID() !== (int)$banner['owner']) {
            error('Запрещено изменять положение чужого баннера.');
        }
        $txt = "";
        $summ = (int)getParam('banner_'.$post['payed_for'].'_price', -1);
        switch($post['payed_for']) {
            case 'main_cat':
                $post['under_cat'] = -1;
                $post['action_list'] = -1;
                $txt = "основной категории, за ".$summ." Р";
                break;
            case 'under_cat':
                $post['action_list'] = -1;
                $txt = "под-категории, за ".$summ." Р";
                break;
            case 'action_list':
                $txt = "под-под категории, за ".$summ." Р";
                break;
        }
        if($summ === -1) {
            error('Ошибка определения назначения платежа...');
        }
        INCLUDE_CLASS('shops', 'pay');
        if(PAY::buy(Access::userID(), $summ, 'Продление баннера', 'По тарифу', $txt)) {
            $banner_pays = (int)$banner['payed'];
            if($banner_pays === -1) { $banner_pays = $summ; } else { $banner_pays += $summ; }
            $new_data = VALUES::plus_days(getParam('count_days_for_banner', 30));
            q("
            UPDATE `banner` SET 
            `payed`=".$banner_pays.",
            `action_to`='".$new_data."',
            `main_cat`=".(int)$post['main_cat'].",
            `under_cat`=".(int)$post['under_cat'].",
            `action_list`=".(int)$post['action_list']."
            WHERE `id`=".(int)$post['id_banner']."
            ");
            Message::addMessage("Оплата за размещение баннера в ".$txt." - подтверждена");
            ans('ok');
        } else {
            error('Не достаточно средств...');
        }
        break;
    case 'banner_show_at_main_page':
        if(Access::scanLevel() <= 0) {
            error('Только для авторизованных...');
        }
        $err = isset_columns($_POST, ['id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $banner = SUBD::getLineDB('banner', 'id', (int)$post['id']);
        if(!is_array($banner)) {
            error('Ошибка поиска баннера');
        }
        if(Access::userID() !== (int)$banner['owner']) {
            error('Запрещено изменять атрибуты у чужого баннера.');
        }
        $summ = (int)getParam('summ_show_at_main');
        INCLUDE_CLASS('shops', 'pay');
        if(PAY::buy(Access::userID(), $summ, 'Баннер на главной', 'По тарифу')) {
            $banner_pays = (int)$banner['payed'];
            if($banner_pays === -1) { $banner_pays = $summ; } else { $banner_pays += $summ; }
            q("
            UPDATE `banner` SET 
            `show_main`=1,
            `payed`=".$banner_pays."
            WHERE `id`=".(int)$post['id']."
            ");
            Message::addMessage('Размещение баннера на главной странице - подтверждено');
            ans('ok');
        } else {
            error('Не достаточно средств...');
        }
        break;
    case 'banner_hidden_from_main_page':
        if(Access::scanLevel() <= 0) {
            error('Только для авторизованных...');
        }
        $err = isset_columns($_POST, ['id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $banner = SUBD::getLineDB('banner', 'id', (int)$post['id']);
        if(!is_array($banner)) {
            error('Ошибка поиска баннера');
        }
        if(Access::userID() !== (int)$banner['owner']) {
            error('Запрещено изменять атрибуты у чужого баннера.');
        }
        q("
        UPDATE `banner` SET 
        `show_main`=0
        WHERE `id`=".(int)$post['id']."
        ");
        ans('ok');
        break;
    case 'set_banner_attr':
        $err = isset_columns($_POST, ['id', 'attr']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $row = SUBD::getLineDB('banner', 'id', (int)$post['id']);
        if(is_array($row) && (int)$row['owner'] === Access::userID()) {
            q("UPDATE `banner` SET `css`='".serialize($post['attr'])."' WHERE `id`=".(int)$post['id']);
            ans('ok');
        }
        error('Такой баннер отсутствует или вы не являетесь его владельцем.');
        break;
    case 'change_content_banner':
        $err = isset_columns($_POST, ['id', 'content', 'types']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $row = SUBD::getLineDB('banner', 'id', (int)$post['id']);
        $access = ['title', 'descr', 'link'];
        if(in_array($post['types'], $access) && is_array($row) && (int)$row['owner'] === Access::userID()) {
            q("UPDATE `banner` SET `".$post['types']."`='".db_secur($post['content'])."' WHERE `id`=".(int)$post['id']);
            ans('ok');
        } else {
            error('Такой баннер отсутствует или вы не являетесь его владельцем.');
        }
        break;
    case 'create_banner':
        if(Access::scanLevel() <= 0) {
            error('Только для авторизованных...');
        }
        if(count(SQL_ROWS(q("SELECT * FROM `banner` WHERE `owner`=".Access::userID()))) >= 5) {
            error('Максимально-допустимое колличество баннеров = 5');
        }
        q("INSERT INTO `banner` SET `owner`=".Access::userID().", `action_to`='".date('Y-m-d H:i:s')."' ");
        ans('ok');
        break;
    case 'del_banner':
        if(Access::scanLevel() <= 0) {
            error('Только для авторизованных...');
        }
        $err = isset_columns($_POST, ['id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $row = SUBD::getLineDB('banner', 'id', (int)$post['id']);
        if(is_array($row) && (int)$row['owner'] === Access::userID()) {
            q("DELETE FROM `banner` WHERE `id`=".(int)$post['id']);
            FILER::delete($row['img']);
            ans('ok');
        }
        error('Такого баннера не существует или не вы являетесь его владельцем.');
        break;
}

echo json_encode($ans, JSON_UNESCAPED_UNICODE);
exit;
