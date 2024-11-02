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
}

echo json_encode($ans, JSON_UNESCAPED_UNICODE);
exit;
