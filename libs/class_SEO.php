<?php
use JetBrains\PhpStorm\Pure;
class SEO {
    public static string $template_name = '';
    public function __construct(array $get=[], array $route=[]) {
        return self::get_info($get, $route);
    }

    public static function get_all_pages() {
        $pages = SUBD::getAllDB('pages');
        return SORT::array_sort_by_column($pages, 'order');
    }

    public static function get_info($get=[], $route=[], $type_page='') {
        $name = 'main';
        $params = [];
        Core::$meta_local = [
            'title'=>'global',
            'description'=>'global',
            'keywords'=>'global',
            'h1'=>'global',
        ];
//        INCLUDE_CLASS('shops', 'cataloger');
//        $CATS = new CATALOGER();
        if(count($route) > 0) {
            $name = end($route);
            if($name === '') {
                $name = 'main';
            }
        }
        if(isset($get['title'])) {
            $name = $get['title'];
        }
        $row = self::transform_meta(SUBD::getLineDB('pages', 'template', db_secur($name)));
//        say(['GET'=>$get, 'route'=>$route, 'type_page'=>$type_page]);
        $row = self::upgrade_row($row, $type_page);

//        say($row);
        $path = $route[0] ?? '';

        $access = ['MAIN-CAT', 'UNDER-CAT', 'ACTION-LIST', 'PRODUCT'];
        if(in_array($type_page, $access)) {
            $params['path'] = $path;
                if($type_page !== 'PRODUCT') {
                    INCLUDE_CLASS('shops', 'cataloger');
                    $CATS = new CATALOGER();
                    $local_meta = $CATS->path_to_cats_names($path);
//                    say($local_meta);
                    $arr_path = explode('/', $path);
                    if(count($arr_path) === 1) {
                        $row = self::meta_local_apply($row, 'mainCat', $local_meta);
                    }
                    if(count($arr_path) === 2) {
                        $row = self::meta_local_apply($row, 'underCat', $local_meta);
                    }
                    if(count($arr_path) === 3) {
                        $row = self::meta_local_apply($row, 'actionList', $local_meta);
                    }
                }
            }

//        say($row);

        set_title(self::decode_paterns($row['title'], $params));
        set_description(self::decode_paterns($row['description'], $params));
        set_keywords(self::decode_paterns($row['keywords'], $params));
        set_h1(self::decode_paterns($row['h1'], $params));
        return $row;
    }

    private static function not_empty($meta_tag): bool {
        if($meta_tag !== null && $meta_tag !== '' && $meta_tag !== '-') {
            return true;
        }
        return false;
    }

    #[Pure] private static function meta_local_apply($row, $type_meta, $local_arr) {
//        say($row);
//        say($type_meta);
//        say($local_arr);
        if(self::not_empty($local_arr[$type_meta]['title'])) {
            $row['title'] = $local_arr[$type_meta]['title'];
            Core::$meta_local['title'] = 'local';
        }
        if(self::not_empty($local_arr[$type_meta]['descr'])) {
            $row['description'] = $local_arr[$type_meta]['descr'];
            Core::$meta_local['description'] = 'local';
        }
        if(self::not_empty($local_arr[$type_meta]['keywords'])) {
            $row['keywords'] = $local_arr[$type_meta]['keywords'];
            Core::$meta_local['keywords'] = 'local';
        }
        if(self::not_empty($local_arr[$type_meta]['h1'])) {
            $row['h1'] = $local_arr[$type_meta]['h1'];
            Core::$meta_local['h1'] = 'local';
        }
        return $row;
    }

    public static function transform_meta($row_from_db): array
    {
        if(is_array($row_from_db)) {
            $row_from_db['title'] = $row_from_db['title'] ?? Core::$SiteName;
            $row_from_db['description'] = $row_from_db['description'] ?? Core::$SiteName;
            $row_from_db['keywords'] = $row_from_db['keywords'] ?? Core::$SiteName;
            $row_from_db['h1'] = $row_from_db['h1'] ?? Core::$SiteName;
            return $row_from_db;
        } else {
            return ['id'=>-1, 'template'=>'', 'title'=>'', 'description'=>'', 'keywords'=>'', 'h1'=>''];
        }
    }

    public static function upgrade_row(mixed $row, string $type_page): mixed
    {
        if($type_page !== '') {
            $access = ['MAIN-CAT', 'UNDER-CAT', 'ACTION-LIST', 'PRODUCT'];
            if(in_array($type_page, $access)) {
                $row_pre = SUBD::getLineDB('pages', 'template', $type_page);
                if(is_array($row_pre)) {
                    if($row['title'] === '' || $row['title'] === '-') {
                        $row['title'] = $row_pre['title'];
                        if($row['title'] === null) { $row['title'] = '-'; }
                    }
                    if($row['description'] === '' || $row['description'] === '-') {
                        $row['description'] = $row_pre['description'];
                        if($row['description'] === null) { $row['description'] = '-'; }
                    }
                    if($row['keywords'] === '' || $row['keywords'] === '-') {
                        $row['keywords'] = $row_pre['keywords'];
                        if($row['keywords'] === null) { $row['keywords'] = '-'; }
                    }
                    if($row['h1'] === '' || $row['h1'] === '-') {
                        $row['h1'] = $row_pre['h1'];
                        if($row['h1'] === null) { $row['h1'] = '-'; }
                    }
                }
            } elseif($type_page === 'shop') {
                $row['title'] = '';
                $row['description'] = '';
                $row['keywords'] = '';
                $row['h1'] = '';
            }
        }
        return $row;
    }

    public static function decode_paterns(string $all_line, $params=[]): string {
        $count = 100;
        $name_auth_user = '';
        if(Access::scanLevel() > 0) {
            $P = PROFIL::init(Access::userID());
            $name_auth_user = $P->get('name', Access::userName());
        }
        while (preg_match('/\[(.*?)\]/u', $all_line, $matches)) {
            $res = $matches[1];
            switch ($res) {
                case 'название сайта': $res = Core::$SiteName; break;
                case 'дата': $res = date('d.m.Y'); break;
                case 'дата/время': $res = date('d.m.Y H:i:s'); break;
                case 'текущий город': $res = SITE::$my_place[0]; break;
                case 'категория':
                    if(isset($params['path'])) {
                        $CAT = CATALOGER::INIT();
                        $arr = $CAT->path_to_cats_names($params['path']);
                        if(isset($arr['mainCat'])) {
                            $res = $arr['mainCat']['category'];
                        }
                    } else {
                        Message::addError('Попытка вызвать переменную [ '.$res.' ] из некорректного места!');
                    }
                    break;
                case 'под-категория':
                    if(isset($params['path'])) {
                        $CAT = CATALOGER::INIT();
                        $arr = $CAT->path_to_cats_names($params['path']);
                        if (isset($arr['underCat'])) {
                            $res = $arr['underCat']['under_cat'];
                        }
                    } else {
                        Message::addError('Попытка вызвать переменную [ '.$res.' ] из некорректного места!');
                    }
                    break;
                case 'активный список':
                    if(isset($params['path'])) {
                        $CAT = CATALOGER::INIT();
                        $arr = $CAT->path_to_cats_names($params['path']);
                        if (isset($arr['actionList'])) {
                            $res = $arr['actionList']['lists'];
                        }
                    } else {
                        Message::addError('Попытка вызвать переменную [ '.$res.' ] из некорректного места!');
                    }
                    break;
                case 'название продукта':
                    if(isset($_GET['s'], $_GET['prod'])) {
                        if($row = SHOP::get_only_main_parameters_at_product_code((int)$_GET['s']."_".(int)$_GET['prod'])) {
                            $res = strip_tags($row['name']);
                        }
                    }
                    break;
                case 'описание продукта':
                case 'расположение':
                    if(isset($_GET['s'], $_GET['prod'])) {
                        INCLUDE_CLASS('shops', 'shop');
                        INCLUDE_CLASS('shops', 'props_commander');
                        if($res === 'описание продукта') {
                            $res = strip_tags(PROPS_COMMANDER::get_prop($_GET['s'], $_GET['prod'], 'Описание'));
                        }
                        if($res === 'расположение') {
                            $res = strip_tags(PROPS_COMMANDER::get_prop($_GET['s'], $_GET['prod'], 'Расположение'));
                        }
                    }
                    break;
                case 'список категорий':
                    $res = [];
                    INCLUDE_CLASS('shops', 'cataloger');
                    $CAT = CATALOGER::INIT();
                    foreach($CAT->main_cats as $v) {
                        $res[] = $v['category'];
                    }
                    $res = implode(', ', $res);
                    break;
                case 'список подкатегорий для текущей категории':
                    if(!isset($params['path'])) {
                        Message::addError('Переменная внутри одного из Мета-тегов вне диапазона.');
                        $res = '';
                    } else {
                        INCLUDE_CLASS('shops', 'cataloger');
                        $CAT = CATALOGER::INIT();
                        $arr = explode('/', $params['path']);
                        if (count($arr) >= 1) {
                            $res = [];
                            if(isset($CAT->path_to_cats_names($params['path'])['mainCat'])) {
                                foreach ($CAT->path_to_cats_names($params['path'])['mainCat']['UNDERCATS'] as $v) {
                                    $res[] = $v['under_cat'];
                                }
                            }
                            $res = implode(', ', $res);
                        }
                    }
                    break;
                case 'список внутри текущей под-категории':
                    INCLUDE_CLASS('shops', 'cataloger');
                    $CAT = new CATALOGER();
                    $arr = explode('/', $params['path']);
//                    say($CAT->path_to_cats_names($params['path']));
                $llist = $CAT->path_to_cats_names($params['path'])['underCat']['LISTS'] ?? [];
                    if(count($arr) >= 2 && count($llist) > 0) {
                        $res = [];
                        foreach($llist as $v) {
                            $res[] = $v['lists'];
                        }
                        $res = implode(', ', $res);
                    }
                    break;
                case 'имя пользователя': $res = $name_auth_user; break;
                default: $res = '-';
            };
            $all_line = str_replace($matches[0], $res, $all_line);
            $count--;
            if ($count <= 0) break;
        }
        return $all_line;
    }

    public static function get_all_comms() {
        return SUBD::getAllDB('seo_variables');
    }

    public static function get_all_global_meta($path) {

    }

    public static function get_layer_items_for(int $id_item, string $layer='mainCat|underCat|actionList'): bool|array
    {
        $access_layer_name = ['mainCat','underCat','actionList'];
        if(!in_array($layer, $access_layer_name)) {
            return false;
        }
        $items = [];
        switch($layer) {
            case 'mainCat':
                $items = array_column(SQL_ROWS(q("SELECT category FROM shops_categorys")), 'category');
                break;
            case 'underCat':
                $row = SQL_ONE_ROW(q("SELECT * FROM shops_undercats WHERE id=".$id_item." LIMIT 1"));
                $items = array_column(SQL_ROWS(q("SELECT under_cat FROM shops_undercats WHERE category=".(int)$row['category'])), 'under_cat');
                break;
            case 'actionList':
                $row = SQL_ONE_ROW(q("SELECT * FROM shops_lists WHERE id=".$id_item." LIMIT 1"));
                $items = array_column(SQL_ROWS(q("
                    SELECT lists FROM shops_lists WHERE 
                    main_cat=".(int)$row['main_cat']." AND 
                    undercat=".(int)$row['undercat']."
                    ")), 'lists');
                break;
        }
        return $items;
    }

    public static function set_value_for_all_layer($arr): bool
    {
        $access_array = ['title', 'description', 'keywords', 'h1'];
        if(!in_array($arr['type'], $access_array)) {
            Message::addError('Не правильный тип мета-тега');
            return false;
        }
        switch($arr['type_meta']) {
            case 'actionList':
                if($row = SQL_ONE_ROW(q("SELECT * FROM shops_lists WHERE id=".(int)$arr['id']." LIMIT 1"))) {
                    $ids = SQL_ROWS(q("
                             SELECT id FROM shops_lists WHERE 
                             main_cat=".(int)$row['main_cat']." AND
                             undercat=".(int)$row['undercat']."
                             "));
                    if(count($ids)> 0) {
                        if($arr['type'] === 'description') { $arr['type'] = 'descr'; }
                        $ids = array_column($ids, 'id');
                        q("UPDATE shops_lists SET `".$arr['type']."`='".db_secur($arr['text'])."' WHERE id IN (".implode(',',$ids).") ");
                        return true;
                    }
                }
                break;
            case 'underCat':
                if($row = SQL_ONE_ROW(q("SELECT * FROM shops_undercats WHERE id=".(int)$arr['id']." LIMIT 1"))) {
                    $ids = SQL_ROWS(q("
                             SELECT id FROM shops_undercats WHERE 
                             category=".(int)$row['category']."
                             "));
                    if(count($ids)> 0) {
                        if($arr['type'] === 'description') { $arr['type'] = 'descr'; }
                        $ids = array_column($ids, 'id');
                        q("UPDATE shops_undercats SET `".$arr['type']."`='".db_secur($arr['text'])."' WHERE id IN (".implode(',',$ids).") ");
                        return true;
                    }
                }
                break;
            default:
                Message::addError('Неучтённый тип. Доступно только для ActionList и underCat');
                return false;
                break;
        }
        return false;
    }
}