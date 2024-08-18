<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'save':
        $err = isset_columns($_POST, ['path', 'content']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $path_arr = explode("/", $post['path']);
        if(!isset($path_arr[4]) || (int)$path_arr[4] !== SITE::$user_id) {
            error("Запрещена запись посторонним лицам...");
        }

        file_put_contents($post['path'], $post['content']);
        ans('ok');
        break;
    case 'moove_in':
        $err = isset_columns($_POST, ['arr', 'folder_in']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        TOTALCOMANDER::moove_items_to_folder($post['arr'], $post['folder_in']);
        ans('ok');
        break;
    case 'del_items':
        $err = isset_columns($_POST, ['arr']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $errors = [];
        foreach($post['arr'] as $k=>$v) {
            if(isset($v['SYSIMGS'])) {
                $errors[] = $v['name'];
                unset($post['arr'][$k]);
            }
        }
        TOTALCOMANDER::delete_items($post['arr']);
        if(count($errors) > 0) {
            ans('ok', ['errors'=>$errors]);
        }
        ans('ok');
        break;
    case 'add_folder':
        $err = isset_columns($_POST, ['path', 'name']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        TOTALCOMANDER::add_folder($post['path'], $post['name']);
        ans('ok');
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
