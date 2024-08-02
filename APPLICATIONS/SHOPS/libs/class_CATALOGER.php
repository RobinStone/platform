<?php
class CATALOGER {
    public array $cat=[];
    public mixed $main_cats=[];
    public mixed $under_cats=[];
    public mixed $action_lists=[];

    function __construct() {
//        $page_start = microtime(true);

        $main_cats = SQL_ROWS_FIELD(q("SELECT id, `order`, category, logo, logo_img, cat_trans, title, descr, keywords, h1 FROM `shops_categorys` ORDER BY `order` ASC"), 'id');
        $this->main_cats = $main_cats;
        $under_cats = SQL_ROWS_FIELD(q("SELECT id, `order`, category, logo, logo_img, under_cat, undercat_trans, title, descr, keywords, h1 FROM `shops_undercats` ORDER BY `order` ASC"), 'id');
        $this->under_cats = $under_cats;
        $action_lists = SQL_ROWS_FIELD(q("SELECT id, `order`, main_cat, undercat, logo, logo_img, lists, translit, title, descr, keywords, h1 FROM `shops_lists` ORDER BY `order` ASC"), 'id');
        $this->action_lists = $action_lists;

        foreach($action_lists as $k=>$v) {
            $under_cats[$v['undercat']]['LISTS'][$k] = $v;
        }

        foreach($under_cats as $k=>$v) {
            $main_cats[$v['category']]['UNDERCATS'][$k] = $v;
        }

        $this->cat = $main_cats;
//        $page_delta = microtime(true) - $page_start;
    }

    public static function INIT() {
        unset($_SESSION['cataloger']);
        if(file_exists(Core::$path.'/RESURSES/cataloger.txt')) {
            return unserialize(file_get_contents(Core::$path.'/RESURSES/cataloger.txt'));
        } else {
            $CAT = new CATALOGER();
            file_put_contents(Core::$path.'/RESURSES/cataloger.txt', serialize($CAT));
            return $CAT;
        }
//        if(isset($_SESSION['cataloger'])) {
//            return unserialize($_SESSION['cataloger']);
//        } else {
//            $CAT = new CATALOGER();
//            $_SESSION['cataloger'] = serialize($CAT);
//            return $CAT;
//        }
    }

    public function get_id_from_cat_at_field($field_value, $column_name, $type='main_cat|under_cat|action_list'):int {
        $ans = -1;
        $arr = [];
        switch($type) {
            case 'main_cat':
                $arr = $this->main_cats;
                break;
            case 'under_cat':
                $arr = $this->under_cats;
                break;
            case 'action_list':
                $arr = $this->action_lists;
                break;
            default:
                return $ans;
        }
        foreach($arr as $v) {
            if($v[$column_name] === $field_value) {
                $ans = $v['id'];
            }
        }
        return $ans;
    }

    public function main_cat_name_to_id($name_main_cat): int
    {
        foreach($this->main_cats as $k=>$v) {
            if($v['category'] === $name_main_cat) {
                return (int)$k;
            }
        }
        return -1;
    }
    public function main_cat_trans_to_name($trans_main_cat): string
    {
        foreach($this->main_cats as $k=>$v) {
            if($v['cat_trans'] === $trans_main_cat) {
                return $v['category'];
            }
        }
        return $trans_main_cat;
    }
    public function path_to_cats_names($path): array
    {
//        say($this->cat);
        $ans = [];
        $arr = explode('/', $path);
        if(count($arr) >= 0) {
            foreach($this->cat as $v) {
                if($v['cat_trans'] === $arr[0]) {
                    $ans['mainCat'] = $v;
                    if(count($arr) >= 1) {
                        if(isset($arr[1])) {
                            foreach ($v['UNDERCATS'] as $vv) {
                                if ($vv['undercat_trans'] === $arr[1]) {
                                    $ans['underCat'] = $vv;
                                    if (count($arr) >= 2) {
                                        if(isset($arr[2])) {
                                            if(isset($vv['LISTS'])) {
                                                foreach ($vv['LISTS'] as $vvv) {
                                                    if ($vvv['translit'] === $arr[2]) {
                                                        $ans['actionList'] = $vvv;
                                                        return $ans;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    return $ans;
                                }
                            }
                        }
                    }
                    return $ans;
                }
            }
        }
        return $ans;
    }
    public function under_cat_name_to_id($id_main_cat, $name_under_cat): int
    {
        foreach($this->under_cats as $k=>$v) {
            if($v['under_cat'] === $name_under_cat) {
                return (int)$k;
            }
        }
        return -1;
    }
    public function action_list_name_to_id($name_action_list): int
    {
        foreach($this->action_lists as $k=>$v) {
            if($v['lists'] === $name_action_list) {
                return (int)$k;
            }
        }
        return -1;
    }

    public function id2main_cat($id_main_cat, $translit=false) {
        if($translit) {
            if(isset($this->main_cats[$id_main_cat])) {
                return $this->main_cats[$id_main_cat]['cat_trans'];
            }
        } else {
            if(isset($this->main_cats[$id_main_cat])) {
                return $this->main_cats[$id_main_cat]['category'];
            }
        }
        return '';
    }
    public function id2under_cat($id_under_cat, $translit=false) {
        if($translit) {
            if (isset($this->under_cats[$id_under_cat])) {
                return $this->under_cats[$id_under_cat]['undercat_trans'];
            }
        } else {
            if (isset($this->under_cats[$id_under_cat])) {
                return $this->under_cats[$id_under_cat]['under_cat'];
            }
        }
        return '';
    }
    public function id2action_list($id_action_list, $translit=false) {
        if($translit) {
            if (isset($this->action_lists[$id_action_list])) {
                return $this->action_lists[$id_action_list]['translit'];
            }
        } else {
            if (isset($this->action_lists[$id_action_list])) {
                return $this->action_lists[$id_action_list]['lists'];
            }
        }
        return 'СПИСОК';
    }

    public function get_img_or_logo($id, $type='main|under|list', $img_or_logo=true) {
        $find_arr = [];
        switch($type) {
            case 'main':
                $find_arr = $this->main_cats;
                break;
            case 'under':
                $find_arr = $this->under_cats;
                break;
            case 'list':
                $find_arr = $this->action_lists;
                break;
            default:
                return 'img_not_exists.svg';
                break;
        }
        if(isset($find_arr[$id])) {
            if($img_or_logo) {
                return $find_arr[$id]['logo_img'];
            } else {
                return $find_arr[$id]['logo'];
            }
        }
        return 'img_not_exists.svg';
    }

    public function get_all_under_cats(int $id_main_cat):array {
        $ans = [];
        foreach($this->under_cats as $v) {
            if($v['category'] == $id_main_cat) {
                $v['ACTION_LIST'] = $this->get_all_action_list($id_main_cat, $v['id']);
                $ans[$v['id']] = $v;
            }
        }
        return $ans;
    }
    public function get_all_action_list(int $id_main_cat, $id_under_cat):array {
        $ans = [];
        foreach($this->action_lists as $v) {
            if($v['main_cat'] == $id_main_cat && $v['undercat'] == $id_under_cat) {
                $ans[$v['id']] = $v;
            }
        }
        return $ans;
    }

    public static function del_action_list($id_action_list): bool {
        q("DELETE FROM `shops_lists` WHERE `id`=".(int)$id_action_list);
        return true;
    }
    public static function del_undercat($id_undercat): bool {
        q("DELETE FROM `shops_undercats` WHERE `id`=".(int)$id_undercat);
        return true;
    }
    public static function del_main_cat($id_main_cat): bool {
        q("DELETE FROM `shops_categorys` WHERE `id`=".(int)$id_main_cat);
        return true;
    }

    public static function change_name($id_item, $new_name, $type_item='main_cat|under_cat|action_list'):bool {
        switch($type_item) {
            case 'main_cat':
                if((new CATALOGER)->id2main_cat($id_item) !== '') {
                    q("
                    UPDATE `shops_categorys` SET 
                    `category`='".db_secur($new_name)."', 
                    `cat_trans`='".db_secur(VALUES::translit($new_name))."' 
                    WHERE `id`=".(int)$id_item."
                    ");
                    return true;
                }
                break;
            case 'under_cat':
                if((new CATALOGER)->id2under_cat($id_item) !== '') {
                    q("
                    UPDATE `shops_undercats` SET 
                    `under_cat`='".db_secur($new_name)."', 
                    `undercat_trans`='".db_secur(VALUES::translit($new_name))."' 
                    WHERE `id`=".(int)$id_item."
                    ");
                    return true;
                }
                break;
            case 'action_list':
                if((new CATALOGER)->id2action_list($id_item) !== '') {
                    q("
                    UPDATE `shops_lists` SET 
                    `lists`='".db_secur($new_name)."', 
                    `translit`='".db_secur(VALUES::translit($new_name))."' 
                    WHERE `id`=".(int)$id_item."
                    ");
                    return true;
                }
                break;
            default:
                Message::addError('Не найден тип item для переименования');
                return false;
        }
    }

}