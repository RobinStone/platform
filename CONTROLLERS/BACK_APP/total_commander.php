<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'update_total':
        $err = isset_columns($_POST, ['left', 'right']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $ans = ['left'=>'-', 'right'=>'-'];
        if($post['left'] !== '') {
            $ans['left'] = TOTALCOMANDER::get_dir_catalog_of_user($post['left']);
        }
        if($post['right'] !== '') {
            $ans['right'] = TOTALCOMANDER::get_dir_catalog_of_user($post['right']);
        }
        ans($ans);
        break;

}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
