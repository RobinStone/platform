<?php
include_JS('swipper');
include_CSS('swipper');

$pages = $_SERVER['REDIRECT_URL'] ?? '/';
if($pages === '/') {
    $level = 0;
} else {
    $pages = explode('/', $pages);
    $level = count($pages);
}

//wtf($current, 1);
//t($level);

$index_type_filter = $current['id'] ?? -1;

//0 - на главной
//1 - в категории
//2 - подкатегории
//3 - активные листы
switch($level) {
    case 0:
        $rows = SQL_ROWS_FIELD(q("
        SELECT * FROM `banner` WHERE 
        (`show_main`=1 OR `for_all`=1) AND 
        `payed` <> -1 AND 
        `verified`=1 AND
        `action_to` >= '".date('Y-m-d H:i:s')."'
        ORDER BY `order` ASC
        "), 'id');
        break;
    case 2:
        $rows = SQL_ROWS_FIELD(q("
        SELECT * FROM `banner` WHERE 
        (`main_cat`=".(int)$index_type_filter." OR `for_all`=1) AND 
        `payed` <> -1 AND 
        `verified`=1 AND
        `action_to` >= '".date('Y-m-d H:i:s')."'
        ORDER BY `order` ASC"), 'id');
        break;
    case 3:
        $rows = SQL_ROWS_FIELD(q("
        SELECT * FROM `banner` WHERE 
        (`under_cat`=".(int)$index_type_filter." OR `for_all`=1) AND 
        `payed` <> -1 AND 
        `verified`=1 AND
        `action_to` >= '".date('Y-m-d H:i:s')."'
        ORDER BY `order` ASC"), 'id');
        break;
    case 4:
        $rows = SQL_ROWS_FIELD(q("
        SELECT * FROM `banner` WHERE 
        (`action_list`=".(int)$index_type_filter." OR `for_all`=1) AND 
        `payed` <> -1 AND 
        `verified`=1 AND
        `action_to` >= '".date('Y-m-d H:i:s')."'
        ORDER BY `order` ASC"), 'id');
        break;
}