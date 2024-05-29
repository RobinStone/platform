<?php
$ans = [];
include_once './CONTROLLERS/ajax_before.php';
$path = basename(__DIR__, '.php');
if (is_dir('./APPLICATIONS/' . $path . '/libs')) {
    $files = glob('./APPLICATIONS/' . $path . '/libs/*.php');
    foreach ($files as $v) {
        include_once $v;
    }
}
////////////////////////////////////////////////////////////////////////////////////////
///////////////////////// КОД ВЫШЕ ДОБАВЛЯЕМ ВО ВСЕ ajax ///////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////


switch ($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'get_layer_items_for':
        $err = isset_columns($_POST, ['id', 'type']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        if(Access::scanLevel() < 3 || SITE::$profil->get('SEO', '0') !== '1') {
            error('Ваш уровень должен быть 3+ и в вашем профиле должно быть установлено разрешение на работу с SEO');
        }
        ans('ok', SEO::get_layer_items_for($post['id'], $post['type']));
        break;
    case 'save_meta':
        $err = isset_columns($_POST, ['id', 'type', 'text']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $P = SITE::$profil;
        if(Access::scanLevel() < 6 && $P->get('SEO') !== '1') {
            error('Уровень допуска ниже допустимого и (или) вы получили доступ к управлению SEO');
        }
        $access = ['title', 'description', 'keywords', 'h1'];
        if(!in_array($post['type'], $access)) {
            error('Ошибка задания типа, требуется title || description || keywords || h1');
        }
        q("UPDATE pages SET `".$post['type']."` = '".db_secur($post['text'])."' WHERE `id`=".(int)$post['id']);
        ans('ok');
        break;
    case 'save_meta_local':
        $err = isset_columns($_POST, ['id', 'type', 'type_meta', 'text']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $for_all = $_POST['for_all'] ?? false;
        if($for_all === 'true') {
            SEO::set_value_for_all_layer($post);
        }
        $P = SITE::$profil;
        if(Access::scanLevel() < 6 && $P->get('SEO') !== '1') {
            error('Уровень допуска ниже допустимого и (или) вы получили доступ к управлению SEO');
        }
        $access = ['title', 'description', 'keywords', 'h1'];
        if(!in_array($post['type'], $access)) {
            error('Ошибка задания типа, требуется title || description || keywords');
        }
        if($post['type'] === 'description') { $post['type'] = 'descr'; }
        switch($post['type_meta']) {
            case 'mainCat':
                q("UPDATE `shops_categorys` SET `".$post['type']."` = '".db_secur($post['text'])."' WHERE `id`=".(int)$post['id']);
                break;
            case 'underCat':
                q("UPDATE `shops_undercats` SET `".$post['type']."` = '".db_secur($post['text'])."' WHERE `id`=".(int)$post['id']);
                break;
            case 'actionList':
                q("UPDATE `shops_lists` SET `".$post['type']."` = '".db_secur($post['text'])."' WHERE `id`=".(int)$post['id']);
                break;
            default:
                error('Неизвестный тип мета-данных...');
                break;
        }
        ans('ok');
        break;
    case 'del_meta':
        $err = isset_columns($_POST, ['id_meta']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        if(Access::scanLevel() < 6) {
            error('Уровень допуска ниже допустимого. Требуется 6+');
        }
        $row = SUBD::getLineDB('pages', 'id', (int)$post['id_meta']);
        if(!is_array($row)) {
            error('Не неайден нужный указатель');
        }
        if((int)$row['dynamic'] !== 1) {
            error('Указанный блок не является динамическим и защищён от удаления.');
        }
        q("DELETE FROM pages WHERE id=".(int)$post['id_meta']);
        ans('ok');
        break;
    case 'send_robots':
        if(isset($_FILES) && !empty($_FILES)) {
            if (Access::scanLevel() >= 6 && isset($_FILES['textfile'])) {
                $access_changed_fields = ['robots.txt', 'sitemap.xml'];
                if(isset($_FILES['textfile']['type'], $_POST['file_name']) &&
                    $_FILES['textfile']['type'] === 'text/plain' &&
                    in_array($_POST['file_name'], $access_changed_fields)) {
                    if (move_uploaded_file($_FILES['textfile']['tmp_name'], './' . $_POST['file_name'])) {
                        $type_file = RBS::get_extention($_POST['file_name']);
                        t($type_file);
                        ans('ok');
                    }
                } else {
                    error('Не передан параметр "file_name" или данный "file_name" запрещён для загрузки');
                }
            }
            error('Низкий уровень допуска или передано недостаточных данных...');
        }
        break;
    case 'save_sitemap_xml':
        $err = isset_columns($_POST, ['txt']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        file_put_contents('./sitemap.xml', $post['txt']);
        ans('ok');
        break;
    case 'get_sitemap_xml':
        if(file_exists('./sitemap.xml')) {
            ans('ok', file_get_contents('./sitemap.xml'));
        }
        ans('ok', '');
        break;
    case 'save_robots_txt':
        $err = isset_columns($_POST, ['txt']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        file_put_contents('./robots.txt', $post['txt']);
        ans('ok');
        break;
    case 'get_robots_txt':
        if(file_exists('./robots.txt')) {
            ans('ok', file_get_contents('./robots.txt'));
        }
        ans('ok', '');
        break;
}

echo json_encode($ans, JSON_UNESCAPED_UNICODE);
exit;