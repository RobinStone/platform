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
    case 'transform_text':
        if(isset($_POST['transform_text'])) {
            $ans = [
                    'status'=>'error',
                    'text'=>'Error'
            ];
            if(Access::scanLevel() >= 5) {
                $ans['status'] = 'ok';
                $ans['html'] = MAILER_BUILDER::transform_template($_POST['transform_text']);
            } else {
                $ans['text'] = 'Level access low...';
            }
            echo json_encode($ans, JSON_UNESCAPED_UNICODE);
            exit;
        }
        break;
    case 'get_mail':
        echo MAILER_BUILDER::renderer($_POST['get_mail']);
        exit;
        break;
    case 'del':
        if(isset($_POST['del'])) {
            if(q("DELETE FROM `mailer` WHERE `name` = '".db_secur($_POST['del'])."' ")) {
                echo 'ok';
            } else {
                echo 'Не удалось удалить шаблон... :(';
            }
            exit;
        }
        break;
    case 'sender':
            $P = PROFIL::init(Access::userID());
            $arr[$post['email']] = [
                'name'=>$P->get('name'),
                'login'=>$P->get_field('login'),
                'password'=>'robin-bobin',
                'link'=>'https://rumbra.ru?status=ok',
            ];
            MAILER_BUILDER::send_template_mail('Регистрация', 'Регистрация на сайте', $arr);
            echo 'ok';
            exit;
        break;
    case 'copyd':
        if(isset($_POST['copyd'], $_POST['new_name'])) {
            $row = SUBD::getLineDB('mailer', 'name', $_POST['copyd']);
            if(is_array($row)) {
                q("INSERT INTO `mailer` SET `name` = '".db_secur($_POST['new_name'])."', `html` = '".db_secur($row['html'])."' ");
                echo 'ok';
            } else {
                echo 'Ошибка копирования, что-то не так с именами...';
            }
            exit;
        }
        break;
    case 'save':
        if(isset($_POST['save'], $_POST['html'])) {
            $row = SUBD::getLineDB('mailer', 'name', $_POST['save']);
            if(is_array($row)) {
                q("UPDATE `mailer` SET `html` = '".db_secur($_POST['html'])."' WHERE `name` = '".db_secur($_POST['save'])."' ");
                echo 'Шаблон перезаписан.';
            } else {
                q("INSERT INTO `mailer` SET `name` = '".db_secur($_POST['save'])."', `html` = '".db_secur($_POST['html'])."' ");
                echo 'Новый шаблон успешно сохранён.';
            }
            exit;
        }
        break;
    case 'getTable':
        if(isset($_POST['getTable'])) {
            ob_start();
            $arr = TABLE::get_table_datas($_POST['getTable']);
            if($_POST['getTable'] === 'lessons') {
                usort($arr, function ($a, $b) {
                    return ($a['sequence']['val'] - $b['sequence']['val']);
                });
            }
            try {
                echo TABLE::render_lines($arr, $_POST['getTable']);
            } catch (Exception $e) {
                wtf($e,1);
            }
            $ans = ob_get_contents();
            ob_clean();
            echo $ans;
            exit;
        }
        break;
}

echo json_encode($ans, JSON_UNESCAPED_UNICODE);
exit;
