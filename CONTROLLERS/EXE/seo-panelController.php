<?php
$pages = SEO::get_all_pages();
$commands = SEO::get_all_comms();
$commands = SORT::array_sort_by_column($commands, 'order');

//$global_meta = SUBD::getLineDB('pages', 'template', Core::$META_TYPE);

$route = explode('/', $_GET['route']);

switch(Core::$META_TYPE) {
    case 'MAIN-CAT':
        if(isset($route[0])) {
            $row_arr = SUBD::getLineDB('shops_categorys', 'cat_trans', db_secur($route[0]));
            if(is_array($row_arr)) {
                $local_meta = [
                    'type_category'=>'mainCat',
                    'id'=>(int)$row_arr['id'],
                    'title' => $row_arr['title'],
                    'description' => $row_arr['descr'],
                    'keywords' => $row_arr['keywords'],
                    'h1' => $row_arr['h1'],
                ];
            }
        }
        break;
    case 'UNDER-CAT':
        if(isset($route[1])) {
            INCLUDE_CLASS('shops', 'cataloger');
            $CAT = new CATALOGER();
            $all = $CAT->path_to_cats_names($_GET['route']);
            $local_meta = [
                    'type_category'=>'underCat',
                    'id'=>(int)$all['underCat']['id'],
                    'title' => $all['underCat']['title'],
                    'description' => $all['underCat']['descr'],
                    'keywords' => $all['underCat']['keywords'],
                    'h1' => $all['underCat']['h1'],
            ];
        }
        break;
    case 'ACTION-LIST':
        if(isset($route[2])) {
            INCLUDE_CLASS('shops', 'cataloger');
            $CAT = new CATALOGER();
            $all = $CAT->path_to_cats_names($_GET['route']);
            $local_meta = [
                    'type_category'=>'actionList',
                    'id'=>(int)$all['actionList']['id'],
                    'title' => $all['actionList']['title'],
                    'description' => $all['actionList']['descr'],
                    'keywords' => $all['actionList']['keywords'],
                    'h1' => $all['actionList']['h1'],
            ];
        }
        break;
    default:
        $local_meta = [];
        break;
}
$page = [];
foreach($pages as $v) {
    if($v['template'] === Core::$META_TYPE) {
        $page = $v;
    }
}

