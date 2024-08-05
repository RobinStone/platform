<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'get_list_all':
        $err = isset_columns($_POST, ['under_cat_id', 'main_cat_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }

        $main_cat_id = (int)$post['main_cat_id'];
        $id = (int)$post['under_cat_id'];

        INCLUDE_CLASS('shops', 'cataloger');
        $CAT = CATALOGER::INIT();

        $name_under_cat = $CAT->id2under_cat($id);

        $rows = SQL_ROWS(q("
        SELECT id, ".$main_cat_id." AS `main_id`, ".$id." AS `under_id`, 'list' AS `type`, 
        `lists` AS `name`, '".$name_under_cat."' AS `under_name` 
        FROM `shops_lists` WHERE 
        `undercat`=".$id." AND `main_cat`=".$main_cat_id."
        "));

        ans($rows);
        break;
    case 'get_under_all':
        $err = isset_columns($_POST, ['main_cat_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $id = (int)$post['main_cat_id'];

        INCLUDE_CLASS('shops', 'cataloger');
        $CAT = CATALOGER::INIT();

        $name_main_cat = $CAT->id2main_cat($id);

        $rows = SQL_ROWS(q("
        SELECT id, ".$id." AS `main_id`, 'under' AS `type`, 
        `under_cat` AS `name`, '".$name_main_cat."' AS `main_name` 
        FROM `shops_undercats` WHERE 
        `category`=".$id." 
        "));

        ans($rows);
        break;
    case 'scan':
        $err = isset_columns($_POST, ['txt']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $post['txt'] = mb_strtolower($post['txt']);

        $find_array = explode(' ', $post['txt']);
        foreach($find_array as $k=>$v) {
            if(mb_strlen($v) < 2) {
                unset($find_array[$k]);
            }
        }

        $all = [];
        if(!$all = CACHE::get_from_cache('cats_finder')) {

            INCLUDE_CLASS('shops', 'cataloger');
            $CAT = CATALOGER::INIT();

            $all = SQL_ROWS(q("SELECT `id`, 'main' AS `type`, `category` AS `name`, CONCAT(`category`, ' ', COALESCE(`keywords`, '')) AS `index` FROM `shops_categorys`"));
            $unsercats = SQL_ROWS(q("SELECT `id`, `category` AS `main_id` , 'under' AS `type`, `under_cat` AS `name`, CONCAT(`under_cat`, ' ', COALESCE(`keywords`, '')) AS `index` FROM `shops_undercats`"));

            foreach($unsercats as &$v) {
                $v['main_name'] = $CAT->id2main_cat($v['main_id']);
            }

            $lists = SQL_ROWS(q("SELECT `id`, `main_cat` AS `main_id`, `undercat` AS `under_id`, 'list' AS `type`, `lists` AS `name`, CONCAT(`lists`, ' ', COALESCE(`keywords`, '')) AS `index` FROM `shops_lists`"));

            foreach($lists as &$v) {
                $v['under_name'] = $CAT->id2under_cat($v['under_id']);
            }

            $all = array_merge($all, $unsercats);
            $all = array_merge($all, $lists);
            CACHE::add_in_cache('cats_finder', $all);
        }

        $ans = [];
        $max_count = 11;
        foreach($find_array as $txt) {
            foreach ($all as $v) {
                if (stripos(mb_strtolower($v['index']), $txt) !== false) {
                    $ans[] = $v;
                    if ($max_count-- <= 0) {
                        break;
                    }
                }
            }
        }
        ans($ans);
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
