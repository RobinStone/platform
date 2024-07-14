<?php
class SHOP {
    public $shops = [];
    public $errors = [];

    function __construct() {
        $this->shops = SUBD::getAllDB('shops');
    }

    private function error($text) {
        $this->errors[] = $text;
    }

    private function isset_shop($name_shop, $city, $address): bool {
        foreach($this->shops as $v) {
            if(VALUES::translit($name_shop) === $v['domain'] || ($name_shop === $v['name'] && $city === $v['city'] && $address === $v['address'])) {
                $this->error('Магазин с таким именем и адресом уже существует.');
                return true;
            }
        }
        return false;
    }

    public function create($name_shop, $logo, $city, $address, $title='', $descr=''): bool {
        if($title === '') { $title = $name_shop; }
        if($name_shop === '') {
            $this->error('Название магазина не может быть пустым.');
            return false;
        }
        $translit = VALUES::translit($name_shop);
        if($this->isset_shop($name_shop, $city, $address)) {
            $this->error('Такой магазин уже присутствует.');
            return false;
        }

        q("
        INSERT INTO `shops` SET 
        `name`          = '".db_secur($name_shop)."',
        `domain`        = '".db_secur($translit)."',
        `logo`          = '".db_secur($logo)."',
        `city`          = '".db_secur($city)."',
        `owner`         = ".Access::userID().",
        `address`       = '".db_secur($address)."',
        `title`         = '".db_secur($title)."',
        `active_to`     = '".VALUES::plus_days(30)."',
        `created`       = '".date('Y-m-d H:i:s')."',
        `descr`         = '".db_secur($descr)."'
        ");

        $id = SUBD::get_last_id();
        $_SESSION['shop_id'] = $id;
        if($this->create_props_tabs($id)) {
            return true;
        }
        return false;
    }

    private function create_props_tabs($id_table): bool {
        start_transaction();
        if (!SUBD::existsTable('products_'.$id_table)) {
            q("
            CREATE TABLE `products_".$id_table."` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `status` ENUM ('active', 'not_show', 'backdrop', 'archive') NOT NULL DEFAULT 'not_show' COMMENT 'Статус товара',
                `name` VARCHAR(255) NOT NULL DEFAULT '-' COMMENT 'Название товара',
                `trans` VARCHAR(255) NOT NULL DEFAULT '-' COMMENT 'Транслитерация названия товара',
                `count` INT(11) NOT NULL DEFAULT -1 COMMENT 'Количество товара (-1)',
                `main_cat` INT(11) NOT NULL DEFAULT -1 COMMENT 'Категория товара',
                `under_cat` INT(11) NOT NULL DEFAULT -1 COMMENT 'Под-категория товара',
                `action_list` INT(11) NOT NULL DEFAULT -1 COMMENT 'Множество',
                `created` DATETIME NOT NULL DEFAULT NOW(),
                `changed` DATETIME NOT NULL DEFAULT NOW(),
                `to_active` DATETIME NOT NULL DEFAULT NOW() COMMENT 'Время после которого товар переходит в неактивное состояние',
                PRIMARY KEY (`id`),
                INDEX name (name),
                INDEX trans (trans)
            )
            ");
//            Message::addMessage('Создана таблица "products"');
        }
        if (!SUBD::existsTable('props_'.$id_table)) {
            if(!q("
            CREATE TABLE `props_".$id_table."` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `types` ENUM('val_int', 'val_float', 'val_bool', 'val_string', 'val_text', 'val_file') NOT NULL,
                `field_type` ENUM('number', 'string', 'text', 'link', 'image', 'doc', 'file', 'list') NOT NULL,
                `props_name` VARCHAR(255) NOT NULL,
                `visible` TINYINT(1) DEFAULT 1,
                `block` TINYINT(1) DEFAULT 0,
                PRIMARY KEY (`id`)
            )
            ")) {
                error_transaction();
                Message::addError('Не уникальное свойство. Транзакция отменена.');
                return false;
            }
//            Message::addMessage('Создана таблица "props"');
        }
        if (!SUBD::existsTable('val_'.$id_table)) {
            q("
            CREATE TABLE `val_".$id_table."` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `product_id` INT(11) NOT NULL DEFAULT -1,
                `props_id` INT(11) NOT NULL DEFAULT -1,
                `props_props_id` INT(11) NOT NULL DEFAULT -1,
                `val_bool` TINYINT(1) NOT NULL DEFAULT 0,
                `val_int` INT(11) NOT NULL DEFAULT 0,
                `val_float` DECIMAL(20,8) NOT NULL DEFAULT '0.00',
                `val_string` VARCHAR(255) NOT NULL DEFAULT '-',
                `val_text` TEXT NULL,
                `val_file` VARCHAR(255) NOT NULL DEFAULT '-',
                PRIMARY KEY (`id`),
                INDEX product_id (product_id),
                INDEX props_id (props_id),
                INDEX props_props_id (props_props_id)
            )
            ");
//            Message::addMessage('Создана таблица "values"');
        }
        end_transaction();
        return true;
    }

    public static function get_shop($id_shop) {
        $row = SUBD::getLineDB('shops', 'id', (int)$id_shop);
        if(is_array($row)) {
            $row['owner'] = (array)SUBD::getLineDB('users', 'id', (int)$row['owner']);
            $row['city'] = (array)SUBD::getLineDB('cities', 'id', (int)$row['city']);
            return $row;
        }
        return false;
    }

    public static function get_shop_domain(string $domain) {
        $row = SUBD::getLineDB('shops', 'domain', db_secur($domain));
        if(is_array($row)) {
            $row['owner'] = (array)SUBD::getLineDB('users', 'id', (int)$row['owner']);
            $row['city'] = (array)SUBD::getLineDB('cities', 'id', (int)$row['city']);
            return $row;
        }
        return false;
    }

    public static function get_undercats_from_cat_id($id_cat): array {
        $arr = SQL_ROWS_FIELD(q("SELECT * FROM `shops_undercats` WHERE `category`=".(int)$id_cat." "), 'id');
        return SORT::array_sort_by_column($arr, 'under_cat');
    }
    public static function get_action_list_from_undercat_id($id_cat, $id_undercat): array {
        if($id_undercat === -1) {
            $ask = q("SELECT * FROM `shops_lists` WHERE `main_cat`=".(int)$id_cat." AND `undercat`=-1");
        } else {
            $ask = q("SELECT * FROM `shops_lists` WHERE `main_cat`=".(int)$id_cat." AND `undercat`=".(int)$id_undercat);
        }
        $arr = SQL_ROWS_FIELD($ask, 'id');
        return SORT::array_sort_by_column($arr, 'lists');
    }

    public static function id_shop_2_id_owner($id_shop): int {
        $row = SUBD::getLineDB('shops', 'id', (int)$id_shop);
        if(is_array($row)) {
            return (int)$row['owner'];
        }
        return -1;
    }

    public static function create_product_in_shop($shop_id, $product_id, $product_name, $main_cat_id, $under_cat_id, $action_list_id, $files=[], $props_arr=[], int $count_products=-1): bool {
        $shop_id = (int)$shop_id;
        $product_id = (int)$product_id;
        $count_prods = $count_products;
        $main_cat_id = (int)$main_cat_id;
        $under_cat_id = (int)$under_cat_id;
        $action_list_id = (int)$action_list_id;
        $product_name = db_secur($product_name);

        $lat = (float)DISTANCE::get_field_value_from_array('Широта', $props_arr);
        $lng = (float)DISTANCE::get_field_value_from_array('Долгота', $props_arr);

//        t('lat='.$lat.' lng='.$lng);

        $country_id = (int)DISTANCE::get_field_value_from_array('IDcountry', $props_arr);
        $city_id = (int)DISTANCE::get_field_value_from_array('IDcity', $props_arr);

        if($product_id !== -1 && is_array(SUBD::getLineDB('products_'.$shop_id, 'id', $product_id))) {
            $querys = [];
            if($main_cat_id !== -1) {
                $querys[] = "`main_cat`=".$main_cat_id;
            }
            if($under_cat_id !== -1) {
                $querys[] = "`under_cat`=".$under_cat_id;
            }
            if($action_list_id !== -1) {
                $querys[] = "`action_list`=".$action_list_id;
            }
            $qr = "";
            if(count($querys) > 0) {
                $qr = implode(',',$querys).", ";
            }

            $old_item = SHOP::get_products_list_at_id($shop_id, [$product_id], true, 'name', 'ASC')[$product_id];
            $all = [
                'shop_id'=>$shop_id,
                'product_id'=>$product_id,
                'main_cat_id'=>$main_cat_id,
                'under_cat_id'=>$under_cat_id,
                'action_list_id'=>$action_list_id,
                'product_name'=>$product_name,
                'files'=>$files,
                'props'=>$props_arr
            ];

            q("
            UPDATE `products_".$shop_id."` SET
            `name`='".$product_name."',
            `trans`='".VALUES::translit($product_name)."',
            `count`=".$count_prods.",
            ".$qr."
            `changed`='".date('Y-m-d H:i:s')."'
            WHERE `id`=".$product_id."
            ");

            SEARCH::set_finder_index($shop_id, $product_id, $product_name, $city_id);
            SEARCH::set_categories_parameters($shop_id, $product_id, $main_cat_id, $under_cat_id, $action_list_id);
            DISTANCE::change_product_coords($lat, $lng, $country_id, $city_id, $shop_id, $product_id);

            $create = false;
        } else {
            q("
            INSERT INTO `products_".$shop_id."` SET
            `name`='".$product_name."',
            `trans`='".VALUES::translit($product_name)."',
            `main_cat`=".$main_cat_id.",
            `under_cat`=".$under_cat_id.",
            `action_list`=".$action_list_id.",
            `count`=".$count_prods.",
            `created`='".date('Y-m-d H:i:s')."'
            ");
            $product_id = SUBD::get_last_id();

//            say($props_arr);

            $shop_id = (int)$shop_id;
            $product_id = (int)$product_id;

            SEARCH::set_finder_index($shop_id, $product_id, $product_name, $city_id);
            SEARCH::set_categories_parameters($shop_id, $product_id, $main_cat_id, $under_cat_id, $action_list_id);
            DISTANCE::create_new_product_coords($lat, $lng, $country_id, $city_id, $shop_id, $product_id);

            $create = true;
        }

        if(!$create) {
            $props_arr = PROPS_COMMANDER::compare_old_and_new_props($all['shop_id'], $old_item['VALS'], $all['props']);
        }

        if(count($files) > 0) {
            foreach($files as $v) {
                $props_arr[] = [
                    'id'=>-1,
                    'type'=>'file',
                    'field'=>'Изображение (фото)',
                    'value'=>$v
                ];
            }
        }

//        say($props_arr);

        if(count($props_arr) > 0) {
            foreach($props_arr as $v) {
                switch($v['type']) {
                    case 'number':
                        $v['value'] = round((float)$v['value'], 8);
                        $v['db_column'] = 'val_float';
                        break;
                    case 'string':
                    case 'link':
                        $v['value'] = db_secur($v['value']);
                        $v['db_column'] = 'val_string';
                        break;
                    case 'text':
                        $v['value'] = db_secur($v['value']);
                        $v['db_column'] = 'val_text';
                        break;
                    case 'list':
                        $v['value'] = serialize($v['value']);
                        $v['db_column'] = 'val_text';
                        break;
                    case 'file':
                        $v['value'] = db_secur($v['value']);
                        $v['db_column'] = 'val_file';
                        break;
                    default:
                        TELE::send_at_user_name('robin', 'Неизвестный тип...['.$v['type'].']');
                        Message::addError('Передан неизвестный тип данных...');
                        return false;
                        break;
                }

                $visible = 1;
                $block = 0;
                if(isset($v['visible']) && (int)$v['visible'] === 0) {
                    $visible = 0;
                }
                if(isset($v['block']) && (int)$v['block'] === 1) {
                    $block = 1;
                }

                    // помимо получения PROPS ниже мы его и устанавливаем, если нету такого
                    $props_id = self::get_props_id($shop_id, $v['db_column'], $v['type'], $v['field'], $visible, $block);

                    switch($v['type']) {
                        case 'number':
                            q("
                            INSERT INTO `val_".$shop_id."` SET 
                            `product_id`=".$product_id.",
                            `props_id`=".$props_id.",
                            `".$v['db_column']."` = ".$v['value']);
                            break;
                        case 'string':
                        case 'link':
                        case 'file':
                        case 'list':
                        case 'text':
                            q("
                            INSERT INTO `val_".$shop_id."` SET 
                            `product_id`=".$product_id.",
                            `props_id`=".$props_id.",
                            `".$v['db_column']."` = '".$v['value']."'
                            ");
                            break;
                    }
            }
        }
        return true;
    }

    public static function delete_products_arr($shop_id, $arr=[]): bool {
        if(!empty($arr)) {
            foreach($arr as $k=>$v) {
                $arr[$k] = (int)$v;
            }
            $lst_files_drop = SQL_ROWS_FIELD(q("SELECT DISTINCT `val_file` FROM `val_".(int)$shop_id."` WHERE `product_id` IN (".implode(',',$arr).") AND `val_file` != ''"), 'val_file');
            $names = [];
            foreach($lst_files_drop as $k=>$v) {
                $names[] = $k;
            }
            FILER::delete($names);
            q("DELETE FROM `products_".(int)$shop_id."` WHERE `id` IN (".implode(',',$arr).") ");
            q("DELETE FROM `val_".(int)$shop_id."` WHERE `product_id` IN (".implode(',',$arr).") ");
            q("DELETE FROM `indexer` WHERE `shop_id`=".(int)$shop_id." AND `prod_id` IN (".implode(',',$arr).") ");
            q("DELETE FROM `best_prod` WHERE `shop_id`=".(int)$shop_id." AND `product_id` IN (".implode(',',$arr).") ");

            REVIEWS_PROD::delete_reviews_array($shop_id, $arr);
        }
        return true;
    }

    public static function delete_param_from_product($shop_id, $param_id): bool {
        if(!self::is_my_shop($shop_id)) {
            Message::addError('Разрешено работать только со своей площадкой');
            return false;
        }
        $row = SUBD::getLineDB('val_'.(int)$shop_id, 'id', (int)$param_id);
        if(is_array($row)) {
            if($row['val_file'] !== '' && $row['val_file'] !== '-') {
                FILER::delete($row['val_file']);
            }
            q("DELETE FROM `val_".(int)$shop_id."` WHERE `id`=".(int)$param_id);
            return true;
        }
        Message::addError('Не найден параметр с искомым ID');
        return false;
    }

    public static function get_props_id($shop_id, $types, $type_field, $props_name, $visible=1, $block=0): int {
        if(!isset($_SESSION['props_'.$shop_id])) {
            $_SESSION['props_'.$shop_id] = SQL_ROWS_FIELD(q("SELECT * FROM `props_".$shop_id."` "), 'props_name');
            $props = $_SESSION['props_'.$shop_id];
        } else {
            $props = $_SESSION['props_'.$shop_id];
        }
        if(isset($props[$props_name]) && $props[$props_name]['types'] === $types) {
            return (int)$props[$props_name]['id'];
        } else {
            q("
            INSERT INTO `props_".$shop_id."` SET
            `types`='".db_secur($types)."',
            `field_type`='".db_secur($type_field)."',
            `props_name`='".db_secur($props_name)."',
            `visible`=".(int)$visible.",
            `block`=".(int)$block."
            ");
            $last_id = SUBD::get_last_id();
            $_SESSION['props_'.$shop_id] = SQL_ROWS_FIELD(q("SELECT * FROM `props_".$shop_id."` "), 'props_name');
            return $last_id;
        }
    }

    public static function get_all_props($id_shop): array
    {
        $id_shop = (int)$id_shop;
        if(SUBD::existsTable('props_'.$id_shop)) {
            $arr = SUBD::getAllDB('props_'.$id_shop);
            $res = [];
            foreach($arr as $v) {
                $res[$v['field_type']][] = $v['props_name'];
            }
            return $res;
        }
        return [];
    }

    public static function get_cataloger_shop($id_shop) {
        if(SUBD::existsTable('products_'.$id_shop)) {
            $arr = SUBD::getAllDB('products_'.$id_shop);
            $arr = SORT::array_sort_by_column($arr, 'name');
        } else {
            Message::addError('Площадка products_'.$id_shop.' отсутствует!..');
            return false;
        }
    }

    public static function get_products_list_at_id($shop_id, $id_arr=[], $with_categorys=false, $sorted_by="name", $askOrDesc="ASC"): array {
        if($askOrDesc !== 'ASC') {
            $askOrDesc = 'DESC';
        }
        switch($sorted_by) {
            case 'id':

                break;
            default: $sorted_by = 'name';
        }
        $shop = SUBD::get_executed_rows('shops', 'id', (int)$shop_id)[$shop_id];
        $res = [];
        $PR = "props_".(int)$shop_id;
        $PROPS = SQL_ROWS_FIELD(q("SELECT * FROM `".$PR."`"), 'id');
        $PN = "products_".(int)$shop_id;
        $VAL = "val_".(int)$shop_id;
        $rows =SQL_ROWS(q("
        SELECT 
        '".$shop['city']."' AS CITY,
        ".$PN.".id AS ID_product, 
        ".$PN.".status, 
        ".$PN.".name, 
        ".$PN.".trans, 
        ".$PN.".main_cat, 
        ".$PN.".under_cat, 
        ".$PN.".action_list,
        ".$PN.".count,
        ".$PN.".created,
        ".$PN.".changed,
        ".$PN.".to_active,
        ".$VAL.".* FROM `".$PN."` LEFT JOIN `".$VAL."`
        ON ".$VAL.".product_id = ".$PN.".id WHERE 
        ".$PN.".id IN (".implode(',',$id_arr).") ORDER BY ".$PN.".".$sorted_by." ".$askOrDesc."
        "));
//        say($rows);

        $cat = null;
        if($with_categorys) {
            include_once './APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
            $cat = new CATALOGER();
        }

        foreach($rows as $v) {
            $val = [];
            if(!isset($res[$v['ID_product']])) {
                $val = [
                    'STATUS'=>$v['status'],
                    'CITY'=>$v['CITY'],
                    'NAME'=>$v['name'],
                    'TRANS'=>$v['trans'],
                    'COUNT'=>$v['count'],
                    'CREATED'=>$v['created'],
                    'CHANGED'=>$v['changed'],
                    'TO_ACTIVE'=>$v['to_active'],
                ];
                if($with_categorys) {
                    $val['CAT'] = $cat->id2main_cat($v['main_cat']);
                    $val['CAT_trans'] = $cat->id2main_cat($v['main_cat'], true);
                    $val['UNDERCAT'] = $cat->id2under_cat($v['under_cat']);
                    $val['UNDERCAT_trans'] = $cat->id2under_cat($v['under_cat'], true);
                    $val['LIST'] = $cat->id2action_list($v['action_list']);
                    $val['LIST_trans'] = VALUES::translit($cat->id2action_list($v['action_list']), true);
                }

                $val['VALS'] = [];

                $val['VALS'][$v['id']] = [
                    'name'=>$PROPS[$v['props_id']]['props_name'],
                    'field_type'=>$PROPS[$v['props_id']]['field_type'],
                    'types'=>$PROPS[$v['props_id']]['types'],
                    'VALUE'=>$v[$PROPS[$v['props_id']]['types']],
                    'visible'=>$PROPS[$v['props_id']]['visible'],
                    'block'=>$PROPS[$v['props_id']]['block'],
                ];
                $res[$v['ID_product']] = $val;
            } else {
                $res[$v['ID_product']]['VALS'][$v['id']] = [
                    'name'=>$PROPS[$v['props_id']]['props_name'],
                    'field_type'=>$PROPS[$v['props_id']]['field_type'],
                    'types'=>$PROPS[$v['props_id']]['types'],
                    'VALUE'=>$v[$PROPS[$v['props_id']]['types']],
                    'visible'=>$PROPS[$v['props_id']]['visible'],
                    'block'=>$PROPS[$v['props_id']]['block'],
                ];
            }
        }
        return $res;
    }

    public static function get_random_products_from_one_shop($shop_id, $random=false, $count=-1, $main_cat_id=-1, $under_cat_id=-1, $action_list_id=-1, $only_active=false):array {
        return self::get_mix_products_at_all_shops($random, $count, -1, $main_cat_id, $under_cat_id, $action_list_id, [$shop_id], $only_active);
    }

    public static function get_mix_products_at_all_shops(bool $random=false, int|array $count=10, int $city_id= -1, int $main_cat_id=-1, int $under_cat_id=-1, int $action_list_id=-1, array $shops_id_arr=[], bool $only_active=false, array $filters_props=[]): array
    {
//        say(debug_backtrace());
//        say($filters_props);
//        $start = microtime(true);
        if($shops_id_arr === []) {
            $shops = SQL_ROWS_FIELD(q("SELECT * FROM `shops` WHERE `active`=1 "), 'id');
        } else {
            if(is_array($shops_id_arr) && count($shops_id_arr) > 0) {
                foreach($shops_id_arr as $kkk=>$vvv) {
                    $shops_id_arr[$kkk] = (int)$vvv;
                }
            } else {
                Message::addError('Не найдены id магазинов на которые ссылается метод...');
                exit;
            }
            $shops = SQL_ROWS_FIELD(q("SELECT * FROM `shops` WHERE `id` IN (".implode(',',$shops_id_arr).") AND `active`=1 "), 'id');
        }
        if(empty($shops)) {
            return [];
        }
        $cities = SQL_ROWS_FIELD(q("SELECT * FROM `cities`"), 'id');
        $querys = [];
        $arr = [];
        if($city_id !== -1) { $arr[] = " `city`=".$city_id." "; }
        if($main_cat_id !== -1) { $arr[] = " `main_cat`=".$main_cat_id." "; }
        if($under_cat_id !== -1) { $arr[] = " `under_cat`=".$under_cat_id." "; }
        if($action_list_id !== -1) { $arr[] = " `action_list`=".$action_list_id." "; }

        if($only_active === true) {
            $dt_now = date('Y-m-d H:i:s');
            $arr[] = " `status`='active' AND `to_active` > '".$dt_now."' AND (`count` > 0 OR `count` = -1) ";
        }

        $finder = "";
        if(count($arr) > 0) {
            $finder = "WHERE ".implode(" AND ", $arr);
        }
        foreach($shops as $k=>$v) {
            $querys[] = "SELECT id as id_product, changed, 'products_".$k."' as table_name, '".$v['name']."' as shop_name FROM `products_".$k."` ".$finder." ";
        }
        if($random) {
            $rnd = " ORDER BY RAND() ";
        } else {
            if(count($filters_props) > 0 && isset($filters_props['self'])) {
                if(isset($filters_props['self']['changed'])) {
                    $dir = "ASC";
                    if($filters_props['self']['changed'] === 'DESC') {
                        $dir = "DESC";
                    }
                    $rnd = " ORDER BY `changed` ".$dir;
                }
            } else {
                $rnd = "";
            }
        }

//        say(implode(" UNION ALL ", $querys)." ".$rnd." LIMIT ".(int)$count[0].",".(int)$count[1]);

        if(is_array($count) && count($count) === 2) {
            $ask = q(implode(" UNION ALL ", $querys)." ".$rnd." LIMIT ".(int)$count[0].",".(int)$count[1]);
        } else {
            $ask = q(implode(" UNION ALL ", $querys)." ".$rnd." LIMIT ".(int)$count);
        }

        $rows = SQL_ROWS($ask);


        $sh = [];
        foreach($rows as $v) {
            $sh[$v['table_name']][] = $v['id_product'];
        }

//        say($sh);
//        say($finder);

        $querys = [];
        foreach($sh as $k=>$v) {
            if($finder === '') {
                $finder_all = " WHERE ".$k.".id IN (".implode(',',$v).") ";
            } else {
                $finder_all = $finder." AND ".$k.".id IN (".implode(',',$v).") ";
            }

//            say($finder_all);

            $num_shop = (int)explode('_', $k)[1];
            $shop_name = $shops[$num_shop]['name'];

            $CITY = $cities[$shops[$num_shop]['city']]['name'] ?? '-';

            $querys[] = "
            SELECT '".$num_shop."' as ID_TABLPRODUCT, 
            '".$shop_name."' as SHOP,
            '".$CITY."' as CITY,
            ".$k.".id as id_product,  
            ".$k.".status,
            ".$k.".name,
            ".$k.".trans,
            ".$k.".main_cat,
            ".$k.".under_cat,
            ".$k.".action_list,
            ".$k.".count,
            ".$k.".created,
            ".$k.".changed,
            ".$k.".to_active,
            val_".$num_shop.".id as ID_VAL,
            val_".$num_shop.".val_float as PRICE,
            val_".$num_shop.".val_float as DISCOUNT,
            val_".$num_shop.".val_file as IMG,
            val_".$num_shop.".val_string as PLACE,
            val_".$num_shop.".val_text as DESCR,
            props_".$num_shop.".props_name
            FROM `products_".$num_shop."` LEFT JOIN `val_".$num_shop."` ON
            val_".$num_shop.".product_id = products_".$num_shop.".id INNER JOIN `props_".$num_shop."` ON
            (props_".$num_shop.".props_name = 'Расположение' AND val_".$num_shop.".props_id = props_".$num_shop.".id) OR 
            (props_".$num_shop.".props_name = 'Стоимость' AND val_".$num_shop.".props_id = props_".$num_shop.".id) OR 
            (props_".$num_shop.".props_name = 'Описание' AND val_".$num_shop.".props_id = props_".$num_shop.".id) OR 
            (props_".$num_shop.".props_name = 'Скидка %' AND val_".$num_shop.".props_id = props_".$num_shop.".id) OR 
            (props_".$num_shop.".props_name = 'Изображение (фото)' AND val_".$num_shop.".props_id = props_".$num_shop.".id) ".$finder_all." 
            ";
        }

        if(empty($querys)) {
            return [];
        }

//        say($querys);
// ROBIN   тут добавил .$rnd

        if($rnd === '') {
            if(count($filters_props) > 0 && isset($filters_props['params'])) {
                if(isset($filters_props['params']['price']) && $filters_props['params']['price'] === 'DESC') {
                    $rnd = " ORDER BY PRICE DESC";
                }
            }
        }

//        say(implode(" UNION ALL ", $querys).$rnd);

        $ask = q(implode(" UNION ALL ", $querys).$rnd);
        $arr = SQL_ROWS($ask);
        $rows = [];

//        say($arr);
        $arr = SORT::array_sort_by_column($arr, 'ID_VAL');

        foreach($arr as $kk=>$v) {
            $key = $v['ID_TABLPRODUCT'].'_'.$v['id_product'];
            $props_name = $v['props_name'];
            $price = $v['PRICE'];
            $discount = (int)$v['DISCOUNT'];
            $img = $v['IMG'];
            $place = $v['PLACE'];
            $descr = $v['DESCR'];

            if(isset($rows[$key])) {
                if(!isset($rows[$key]['PRICE']) && $props_name === 'Стоимость' && $price !== '0.00') {
                    $rows[$key]['PRICE'] = $price;
                }
                if(!isset($rows[$key]['DISCOUNT']) && $props_name === 'Скидка %' && $discount !== 0) {
                    $rows[$key]['DISCOUNT'] = $discount;
                }
                if(!isset($rows[$key]['PLACE']) && $props_name === 'Расположение' && $place !== '-') {
                    $rows[$key]['PLACE'] = $place;
                }
                if(!isset($rows[$key]['DESCR']) && $props_name === 'Описание' && $descr !== '') {
                    $rows[$key]['DESCR'] = $descr;
                }
                if($img !== '' && $img !== '-' && !in_array($img,  $rows[$key]['FILES'])) {
                    $rows[$key]['FILES'][] = $img;
                }
            } else {
                $rows[$key] = [
                    'id_shop'=>explode('_', $key)[0],
                    'SHOP_NAME'=>$v['SHOP'],
                    'CITY'=>$v['CITY'],
                    'id_product'=>$v['id_product'],
                    'STATUS'=>$v['status'],
                    'name'=>$v['name'],
                    'trans'=>$v['trans'],
                    'main_cat'=>$v['main_cat'],
                    'under_cat'=>$v['under_cat'],
                    'action_list'=>$v['action_list'],
                    'COUNT'=>$v['count'],
                    'CREATED'=>$v['created'],
                    'CHANGED'=>$v['changed'],
                    'TO_ACTIVE'=>$v['to_active'],
                    'FILES'=>[],
                    'DESCR'=>$v['DESCR'],  // тут правил последнее описание
                ];
                if($props_name === 'Стоимость' && $price !== '0.00') {
                    $rows[$key]['PRICE'] = $price;
                }
                if($props_name === 'Скидка %' && $discount !== 0) {
                    $rows[$key]['DISCOUNT'] = $discount;
                }
                if($props_name === 'Расположение' && $place !== '-') {
                    $rows[$key]['PLACE'] = $place;
                }
                if($img !== '' && $img !== '-') {
                    $rows[$key]['FILES'][] = $img;
                }
            }
//            if($rows[$key]['CITY'] === '-') {
//                if(isset($rows[$key]['PLACE'])) {
//                    $rows[$key]['CITY'] = $rows[$key]['PLACE'];
//                }
//            }

        }

//        say($filters_props);

        if($rnd === '') {
            if(count($filters_props) > 0 && isset($filters_props['params'])) {
                if(isset($filters_props['params']['price'])) {
                    if($filters_props['params']['price'] === 'ASC') {
                        $rows = SORT::customMultiSort($rows, 'PRICE');
                    }
                }
            }
        } elseif($rnd ===' ORDER BY PRICE DESC') {
            if($filters_props['params']['price'] === 'DESC') {
                $rows = SORT::customMultiSort($rows, 'PRICE', 'DESC');
            }
        }
//        t('EXEC='.microtime(true)-$start);
        return $rows;
    }

    public static function get_count_products($id_owner): int
    {
        $count = 0;
        $arr = SQL_ROWS_FIELD(q("SELECT id FROM `shops` WHERE `owner`=".(int)$id_owner), 'id');
        if(count($arr) > 0) {
            foreach($arr as $k=>$v) {
                $count += SUBD::countRows('products_'.$k);
            }
        }
        return $count;
    }

    public static function get_count_status_all_my_products($id_owner, $main_cat_id=-1) {
        $main_cat_id = (int)$main_cat_id;
        $arr = SQL_ROWS_FIELD(q("SELECT id FROM `shops` WHERE `owner`=".(int)$id_owner), 'id');
        $rows = [
            'active'=>[],
            'not_show'=>[],
            'backdrop'=>[],
            'archive'=>[]
        ];
        if(count($arr) > 0) {
            $dt = date('Y-m-d H:i:s');
            foreach($arr as $k=>$v) {
                q("UPDATE `products_".$k."` SET `status` = 'archive' WHERE `to_active` < '".$dt."' AND `status` = 'active' ");
                $all = SUBD::getAllDB('products_'.$k);
                foreach($all as $kk=>$vv) {
                    if($main_cat_id === -1) {
                        $rows[$vv['status']][] = [
                            'SHOP' => $k,
                            'ID_PRODUCT' => $vv['id']
                        ];
                    } else {
                        if($main_cat_id === (int)$vv['main_cat']) {
                            $rows[$vv['status']][] = [
                                'SHOP' => $k,
                                'ID_PRODUCT' => $vv['id']
                            ];
                        }
                    }
                }
            }
        }
        return $rows;
    }

    public static function get_all_my_shops($id_owner) {
        $arr = SUBD::getAllLinesDB('shops', 'owner', (int)$id_owner);
        if(is_array($arr)) {
            return $arr;
        }
        return [];
    }

    public static function set_active_status_products($id_shop, $products_arr=[], $count_hours=1): bool {
        $id_shop = (int)$id_shop;
        if(count($products_arr) <= 0) {
            Message::addError('Товаров не может быть < или = 0');
            return false;
        }
        foreach($products_arr as $k=>$v) {
            $products_arr[$k] = (int)$v;
        }
        if(!SUBD::existsTable('products_'.$id_shop)) {
            Message::addError('Не удалось найти площадку с таким ID - '.$id_shop);
            return false;
        }
        $dt = VALUES::plus_hours($count_hours);
        $status = 'not_show';
        if(date('Y-m-d H:i:s') <= $dt) { $status = 'active'; }
        $hrs = "";
        if($count_hours > 0) {
            $hrs = " `to_active`='".$dt."', ";
        }
        q("
        UPDATE `products_".$id_shop."` SET
        `status`='".$status."',
        ".$hrs."
        `changed`='".date('Y-m-d H:i:s')."'
        WHERE `id` IN (".implode(',',$products_arr).")
        ");

        q("
        UPDATE `coords` SET `active`=1 WHERE
        `shop_id`=".$id_shop." AND
        `product_id` IN (".implode(',',$products_arr).")
        ");

        q("UPDATE `indexer` SET `active`=1 WHERE `shop_id`=".(int)$id_shop." AND `prod_id` IN (".implode(',',$products_arr).") ");
        return true;
    }

    public static function set_deactive_status_products($id_shop, $products_arr=[]): bool {
        $id_shop = (int)$id_shop;
        if(count($products_arr) <= 0) {
            Message::addError('Товаров не может быть < или = 0');
            return false;
        }
        foreach($products_arr as $k=>$v) {
            $products_arr[$k] = (int)$v;
        }
        if(!SUBD::existsTable('products_'.$id_shop)) {
            Message::addError('Не удалось найти площадку с таким ID - '.$id_shop);
            return false;
        }
        $status = 'not_show';
        q("
        UPDATE `products_".$id_shop."` SET
        `status`='".$status."',
        `changed`='".date('Y-m-d H:i:s')."'
        WHERE `id` IN (".implode(',',$products_arr).")
        ");

        q("
        UPDATE `coords` SET `active`=0 WHERE
        `shop_id`=".$id_shop." AND
        `product_id` IN (".implode(',',$products_arr).")
        ");

        q("UPDATE `indexer` SET `active`=0 WHERE `shop_id`=".(int)$id_shop." AND `prod_id` IN (".implode(',',$products_arr).") ");
        return true;
    }

    public static function archive_timeout_orders($id_shop, $without_if=false) {
        if($without_if) {
            $rows = SQL_ROWS(q("SELECT id FROM `products_".(int)$id_shop."` WHERE `status` = 'active' "));
        } else {
            $rows = SQL_ROWS(q("SELECT id FROM `products_".(int)$id_shop."` WHERE `to_active` < '".date('Y-m-d H:i:s')."' AND `status` = 'active' "));
        }
        if(count($rows) > 0) {
            $ids = array_column($rows, 'id');
            q("UPDATE `products_" . (int)$id_shop . "` SET `status` = 'archive' WHERE `id` IN (" . implode(',', $ids) . ") ");
            q("UPDATE `indexer` SET `active`=0 WHERE `shop_id`=".(int)$id_shop." AND `prod_id` IN (".implode(',',$ids).") ");
        }
    }

    public static function set_indexer_status_prods(int $id_shop, array $ids_of_prods, string $status): bool
    {
        if($status === 'true') {
            $status = 1;
            $prod_stat = 'active';
        } else {
            $status = 0;
            $prod_stat = 'backdrop';
        }
        foreach($ids_of_prods as $k=>$v) {
            $ids_of_prods[$k] = (int)$v;
        }

        q("UPDATE `indexer` SET `active`=".$status." WHERE `shop_id`=".$id_shop." AND `prod_id` IN (".implode(',',$ids_of_prods).") ");
        q("UPDATE `products_".$id_shop."` SET `status`='".$prod_stat."' WHERE `id` IN (".implode(',',$ids_of_prods).") ");
        return true;
    }

    public static function active_notimeout_orders($id_shop) {
        $rows = SQL_ROWS(q("SELECT id FROM `products_".(int)$id_shop."` WHERE `to_active` > '".date('Y-m-d H:i:s')."' AND `status` <> 'active' "));
        if(count($rows) > 0) {
            $ids = array_column($rows, 'id');
            q("UPDATE `products_" . (int)$id_shop . "` SET `status` = 'active' WHERE `id` IN (" . implode(',', $ids) . ") ");
            q("UPDATE `indexer` SET `active`=1 WHERE `shop_id`=".(int)$id_shop." AND `prod_id` IN (".implode(',',$ids).") ");
        }
    }

    public static function is_my_shop($id_shop, $id_owner='self'): bool {
        if($id_owner !== 'self') {
            $id_owner = (int)$id_owner;
        } else {
            $id_owner = Access::userID();
        }
//        t($id_shop." - ".$id_owner);
        $rows = SQL_ROWS(q("SELECT * FROM `shops` WHERE `id`=".(int)$id_shop." AND `owner`=".$id_owner." "));
        if(count($rows) > 0) {
            return true;
        }
        return false;
    }

    public static function get_published_time_for($orders_array): array
    {
        $arr = [];
        $querys = [];
        foreach($orders_array as $v) {
            $arr[$v['shop_id']][] = $v['item_id'];
        }
        foreach($arr as $k=>$v) {
            $querys[] = "SELECT '".$k."' AS shop_id, id AS item_id, to_active AS PUBLISHED_TIME FROM `products_".$k."` WHERE `id` IN (".implode(',',$v).")";
        }
        $ask = q(implode(" UNION ALL ", $querys));
        return SQL_ROWS($ask);
    }

    public static function sorted_orders_at_shops($orders_array): array {
        $ans = [];
        foreach($orders_array as $v) {
            $ans[$v['shop_id']][$v['item_id']] = [
                    'shop_id'=>$v['shop_id'],
                    'item_id'=>$v['item_id'],
                    'PUBLISHED_TIME'=>$v['PUBLISHED_TIME'],
            ];
        }
        return $ans;
    }

    /**
     * Нужно дописать этот метод...
     */
    public static function get_near_position_products_list($lat, $lng, $count=10): array
    {
        $arr = DISTANCE::find_near_points($lat, $lng, $count);
        return $arr;
    }

    /**
     * В метод отправляется любой массив но с обязательными полями shop_id и product_id
     */
    public static function get_products_list($array=[], $with_params_user=false): array
    {
        $llist = [];
        $shops_id = [];
        $users = [];
        $not_exists_shop = [];
        foreach($array as $v) {
            if(SHOP::get_shop($v['shop_id']) !== false) {
                if (isset($v['shop_id'], $v['product_id'])) {
                    $llist[$v['shop_id']][] = $v['product_id'];
                    $shops_id[] = (int) $v['shop_id'];
                }
            }
        }
        if($with_params_user) {
            $with_params_user = " users.params AS PARAMS, ";
        } else {
            $with_params_user = "";
        }
        if(count($llist) > 0) {
            $ans = [];

            $users = SQL_ROWS_FIELD(q("
            SELECT 
            shops.id AS SHOP_ID,
            shops.logo AS SHOP_LOGO,
            shops.name AS SHOP_NAME,
            shops.active AS SHOP_STATUS,
            users.id AS USERS_ID,
            users.avatar AS USERS_AVATAR,
            users.login AS USERS_LOGIN,
            ".$with_params_user."
            users.status AS USERS_STATUS 
            FROM shops LEFT JOIN users
            ON
            users.id = shops.owner
            WHERE shops.id IN (".implode(',',$shops_id).")
            "), 'SHOP_ID');

            foreach($llist as $k=>$v) {
                $k = (int)$k;
                $querys[] = "
                SELECT 
                ".$k." AS SHOP_ID,
                products_".$k.".id AS PRODUCT_ID,
                products_".$k.".name AS PRODUCT_NAME,
                products_".$k.".status,
                products_".$k.".trans,
                products_".$k.".main_cat,
                products_".$k.".under_cat,
                products_".$k.".action_list,
                products_".$k.".count AS COUNT,
                products_".$k.".to_active,  
                (CASE WHEN props_".$k.".props_name = 'Изображение (фото)' THEN val_".$k.".val_file END) AS IMG,
                (CASE WHEN props_".$k.".props_name = 'Стоимость' THEN val_".$k.".val_float END) AS PRICE,
                (CASE WHEN props_".$k.".props_name = 'Расположение' THEN val_".$k.".val_string END) AS PLACE,
                (CASE WHEN props_".$k.".props_name = 'Скидка %' THEN val_".$k.".val_float END) AS DISCOUNT,
                (CASE WHEN props_".$k.".props_name = 'Описание' THEN val_".$k.".val_text END) AS DESCR
                FROM products_".$k." 
                LEFT JOIN val_".$k." ON val_".$k.".product_id = products_".$k.".id
                LEFT JOIN props_".$k." ON val_".$k.".props_id = props_".$k.".id
                WHERE products_".$k.".id IN (".implode(',', $v).")
                ";
            }

            $ask = q(implode(" UNION ALL ", $querys));
            $rows = SQL_ROWS($ask);

            INCLUDE_CLASS('shops', 'cataloger');
            $CATS = new CATALOGER();

            foreach($rows as $v) {
                $key = $v['SHOP_ID']."_".$v['PRODUCT_ID'];
                if(!isset($ans[$key])) {
                    $ans[$key] = $v;
                } else {
                    if(!is_null($v['IMG']) && $v['IMG'] !== '') {
                        $ans[$key]['IMG'] = $v['IMG'];
                    }
                    if(isset($v['DESCR']) && mb_strlen($v['DESCR']) > 0) {
                        $ans[$key]['DESCR'] = $v['DESCR'];
                    }
                    if((float)$v['PRICE'] > 0) {
                        $ans[$key]['PRICE'] = $v['PRICE'];
                    }
                    if((float)$v['DISCOUNT'] > 0) {
                        $ans[$key]['DISCOUNT'] = (int)$v['DISCOUNT'];
                    }
                    if((string)$v['PLACE'] !== '') {
                        $ans[$key]['PLACE'] = (string)$v['PLACE'];
                    }
                }
            }

            foreach($array as $k=>$v) {
                $dt = date('Y-m-d H:i:s');
                $key = $v['shop_id']."_".$v['product_id'];
                if(isset($ans[$key])) {
                    if(isset($v['datatime'])) {
                        $ans[$key]['F_DATA'] = date('d.m.Y H:i', strtotime($v['datatime']));
                    } else {
                        $ans[$key]['F_DATA'] = $dt;
                    }
                    $ans[$key]['ACTION_LIST'] = VALUES::translit($CATS->id2action_list((int)$ans[$key]['action_list'], true));
                    $ans[$key]['INFO'] = $users[$v['shop_id']];
                    $array[$k]['SHOP'] = $ans[$key];
                } else {
                    $array[$k]['SHOP'] = [];
                }
            }
            return $array;
        }
        return [];
    }

    /**
     * Показывает  количество показов данного товара
     * @param string $shopID_prodID
     * @return int
     */
    public static function get_showed_count(string $shopID_prodID): int {
        $id_shop = (int)explode('_', $shopID_prodID)[0];
        $id_prod = (int)explode('_', $shopID_prodID)[1];
        $popular = SQL_ONE_ROW(q("
                                SELECT * FROM `indexer` WHERE 
                                `shop_id`=".$id_shop." AND 
                                `prod_id`=".$id_prod."
                                "));
        if($popular === false) {
            return 0;
        } else {
            return (int)$popular['showed'];
        }
    }

    public static function get_count_products_of_shop(int $id_shop): int
    {
        $rows = SQL_ONE_ROW(q("SELECT COUNT(*) FROM indexer WHERE shop_id=".$id_shop));
        return (int)$rows['COUNT(*)'];
    }

    public static function get_status_of_shop(int $id_shop): bool
    {
        return (bool)SQL_ONE_ROW(q("SELECT active FROM shops WHERE id=".$id_shop))['active'];
    }

    public static function get_list_products_of_shop(int $id_shop): array
    {
        return SQL_ROWS_FIELD(q("SELECT * FROM `indexer` WHERE `shop_id`=".$id_shop), 'id');
    }

    public static function add_showed_count_plus(string $shopID_prodID): int {
        $id_shop = (int)explode('_', $shopID_prodID)[0];
        $id_prod = (int)explode('_', $shopID_prodID)[1];
        $popular = SQL_ONE_ROW(q("
                                SELECT * FROM `indexer` WHERE 
                                `shop_id`=".$id_shop." AND 
                                `prod_id`=".$id_prod."
                                "));
        if($popular === false) {
            return -1;
        } else {
            $ip = Access::scanIP();
            $row = SQL_ONE_ROW(q("
            SELECT * FROM `showed_counter` WHERE
            `indexer_id`=".(int)$popular['id']." AND
            `ip`='".db_secur($ip)."'
            "));
            if($row === false) {
                q("INSERT INTO `showed_counter` SET `ip`='".db_secur($ip)."', `indexer_id`=".(int)$popular['id']." ");
            } else {
                if($row['day'] === date('Y-m-d')) {
                    return (int)$popular['showed'];
                }
            }
            $count = (int)$popular['showed'];
            ++$count;
            q("UPDATE `indexer` SET `showed`=".$count." WHERE `id`=".(int)$popular['id']);
            return $count;
        }
    }

    public static function change_name_shop(int $shop_id, string $new_name): bool
    {
        $trans = db_secur(VALUES::translit($new_name));
        $new_name = db_secur($new_name);
        if(mb_strlen($new_name) < 2) {
            return false;
        }
        if(!self::is_my_shop($shop_id)) {
            return false;
        }
        if(SUBD::getLineDB('shops', 'trans', $trans) === false) {
            q("
            UPDATE `shops` SET 
            `name`='".$new_name."',
            `trans`='".$trans."'
            WHERE `id`=".$shop_id."
            ");
            return true;
        }
        return false;
    }

    public static function delete_shop_complite(int $id_shop, bool $with_chats=false): bool {
        $sh = SHOP::get_shop($id_shop);
        FILER::delete($sh['logo']);
        q("DELETE FROM `shops` WHERE `id`=".$id_shop);
        if(SUBD::existsTable("products_".$id_shop)) { q("DROP TABLE `products_".$id_shop."`"); }
        if(SUBD::existsTable("props_".$id_shop)) { q("DROP TABLE `props_".$id_shop."`"); }
        if(SUBD::existsTable("val_".$id_shop)) {
            $lst_files_drop = SQL_ROWS_FIELD(q("SELECT DISTINCT `val_file` FROM `val_".$id_shop."` WHERE `val_file` != ''"), 'val_file');
            $names = [];
            foreach($lst_files_drop as $k=>$v) {
                $names[] = $k;
            }
            FILER::delete($names);
            q("DROP TABLE `val_".$id_shop."`");
        }
        q("DELETE FROM `access` WHERE `table_name` = 'products_".$id_shop."' ");
        q("DELETE FROM `access` WHERE `table_name` = 'props_".$id_shop."' ");
        q("DELETE FROM `access` WHERE `table_name` = 'val_".$id_shop."' ");
        q("DELETE FROM `coords` WHERE `shop_id`=".$id_shop);
        q("DELETE FROM `indexer` WHERE `shop_id`=".$id_shop);
        q("DELETE FROM `best_prod` WHERE `shop_id`=".$id_shop);
        q("DELETE FROM `reviews_prod` WHERE `shop_id`=".$id_shop);

        if($with_chats) {
            foreach(ROOM::get_all_rooms_from_shop_id($id_shop) as $k=>$v) {
                ROOM::delete_room($v['room_id']);
            }
        }
        return true;
    }

    public static function disabled_shops(int|array $arr_id_of_shops): bool
    {
        if(is_array($arr_id_of_shops)) {
            start_transaction();
            foreach($arr_id_of_shops as $k=>$v) {
                $arr_id_of_shops[$k] = (int)$v;
                self::archive_timeout_orders($v, true);
            }
            end_transaction();
            q("UPDATE `shops` SET `active`=0 WHERE `id` IN (".implode(',', $arr_id_of_shops).")");
        } else {
            q("UPDATE `shops` SET `active`=0 WHERE `id`=".$arr_id_of_shops);
            q("UPDATE `indexer` SET `active`=0 WHERE `shop_id`=".$arr_id_of_shops);
            self::archive_timeout_orders($arr_id_of_shops, true);
        }
        return true;
    }
    public static function enabled_shops(int|array $arr_id_of_shops): bool
    {
        if(is_array($arr_id_of_shops)) {
            foreach($arr_id_of_shops as $k=>$v) {
                $arr_id_of_shops[$k] = (int)$v;
            }
            q("UPDATE `shops` SET `active`=1 WHERE `id` IN (".implode(',', $arr_id_of_shops).")");
            start_transaction();
            foreach($arr_id_of_shops as $v) {
                self::active_notimeout_orders($v);
            }
            end_transaction();
        } else {
            q("UPDATE `shops` SET `active`=1 WHERE `id`=".$arr_id_of_shops);
            self::active_notimeout_orders($arr_id_of_shops);
        }
        return true;
    }
    public static function extend_shop_plan(int $id_shop, int $count_days): bool {
        $shop = self::get_shop($id_shop);
        switch($shop['title']) {
            case 'Интернет-магазин':
                $tarif = (int)getParam('plane_shop', 1000);
                break;
            case 'Витрина':
                $tarif = (int)getParam('plane_showcase', 500);
                break;
            case 'Бесплатный':
                $tarif = 0;
                break;
            default:
                return false;
        }
        $add_period_days_plan = (int)getParam('add_period_days_plan', 30);
        $one_day_pay = round($tarif / $add_period_days_plan, 2);
        $summ = round($one_day_pay * $count_days);
        $ans = PAY::is_payed_correct_summ(Access::userID(), $summ);
        if($ans !== true) {
            return false;
        }
        if(PAY::buy(Access::userID(), $summ, 'Продление тарифа', 'По тарифу', 'Продление тарифного плана ['.$shop['title'].']')) {
            $dt = $shop['active_to'];
            if($dt < date('Y-m-d H:i:s')) {
                $dt = date('Y-m-d H:i:s');
            }
            $dt = date('Y-m-d H:i:s', strtotime($dt . ' +'.$count_days.' days'));
            q("UPDATE `shops` SET `active_to`='".$dt."', `active`=1 WHERE `id`=".(int)$shop['id']);
            return true;
        }
        return false;
    }
    public static function add_count_to_product(int $shop_id, int $product_id, int $count_plus) {
        if($row = SQL_ONE_ROW(q("SELECT count FROM products_".$shop_id." WHERE id=".$product_id." LIMIT 1"))) {
            $count_now = (int)$row['count'];
            if($count_now !== -1) {
                if ($count_plus > 0) {
                    $count_now += $count_plus;
                } else {
                    if($count_now+$count_plus >= 0) {
                        $count_now += $count_plus;
                    }
                }
                q("UPDATE products_".$shop_id." SET count=".$count_now." WHERE id=".$product_id);
                if($count_now === 0) {
                    SHOP::set_deactive_status_products($shop_id, [$product_id]);
                }
                return true;
            }
        }
    }
    public static function get_owner_of_shop(int $shop_id): bool|int
    {
        if($row = SQL_ONE_ROW(q("SELECT owner FROM shops WHERE id=".$shop_id." LIMIT 1"))) {
            return (int) $row['owner'];
        }
        return false;
    }
    public static function closed_payed_orders(array $ids_of_orders): bool
    {
        $id_user = Access::userID();
        $ids_of_orders = array_map('intval', $ids_of_orders);
        $rows = SQL_ROWS(q("SELECT * FROM orders WHERE id IN (".implode(',',$ids_of_orders).") AND status = 'оплачен'"));
            foreach($rows as $k=>$v) {
                if ((int)$v['id_user'] !== $id_user) {
                    unset($rows[$k]);
                }
            }
        if(count($rows) > 0) {
            $ids_of_orders = array_column($rows, 'id');
            q("
            UPDATE orders SET 
            closed=1, 
            status='получен', 
            data_closed='" . date('Y-m-d H:i:s') . "'
            WHERE id IN (" . implode(',',$ids_of_orders) . ")
            ");
            INCLUDE_CLASS('shops', 'pay');
            start_transaction();
            foreach($rows as $v) {
                if($owner_shop_id = SHOP::get_owner_of_shop((int)$v['shop_id'])) {
                    PAY::add_cash($owner_shop_id, (float)$v['total_summ'], 'Кошелёк', 'Поступила оплата заказа № ' . $v['id']);
                }
            }
            end_transaction();
            return true;
        }
        return false;
    }

    /**
     * Позволяет получать список кодов товаров исходя из параметров поиска,
     * например позволяет найти все товры со скидкой выше 10% и одновременно не дороже 1000 Р
     *
     * Поиск производится по названию полей в карточке создания товара т.е.
     * Так же можно и нужно ограничить выборку при поможи таблицы SHOPS
     * вначале отбираются магазины подошедшие под условие и только потом в этих магазинах происходит поиск
     * товаров подходящих под условие
     *
     *
     * @param array $arr (УСЛОВИЯ ОТБОРА ТОВАРОВ) прим. - ['(Название товара) in (роз)', '(Скидка %) >= 10, (Количество) < 5']
     * @param string $query_shops (УСЛОВИЯ ОТБОРА МАГАЗИНОВ) прим. - "owner = 2 AND active = 1"
     * @param string $shops_limit (ПАГИНАЦИЯ ДЛЯ МАГАЗИНОВ) прим. - "0, 50"
     * @param string $products_limit (ПАГИНАЦИЯ ДЛЯ ТОВАРОВ) прим. - "0, 50"
     * @return array (возвращает массив кодов товаров вида) - ["205_34", "123_44", "12_54"] (где первая цифра id-магазина, 2-ая id-товара)
     */
    public static function filter(array $arr, string $query_shops="", string $shops_limit="0, 50", string $products_limit="0, 50"): array
    {
        if($query_shops !== "") {
            $query_shops = " WHERE ".db_secur($query_shops);
        }
        $shops_limit = " LIMIT ".$shops_limit;
        $products_limit = " LIMIT ".$products_limit;

        $shops = SQL_ROWS(q("SELECT id FROM `shops` ".$query_shops." ".$shops_limit));
        $rows = [];
        if(!empty($shops)) {
            $shops = array_column($shops, 'id');
            $querys = [];
            foreach($shops as $shop_id) {
                $product_conditions = [];
                $prod_queries = "";
                $join = "";
                $query_part = self::re_parse($shop_id, $arr, $product_conditions);
                if(!empty($product_conditions)) {
                    $join = " LEFT JOIN products_".$shop_id." ON products_".$shop_id.".id = val_".$shop_id.".product_id ";
                    $prod_queries = implode(' AND ', $product_conditions)." AND ";
                }
                $querys[] = "
                SELECT 
                CONCAT(".$shop_id.", '_', val_".$shop_id.".product_id) AS CODE,
                CASE props_".$shop_id.".types
                    WHEN 'val_float' THEN val_".$shop_id.".val_float
                    WHEN 'val_string' THEN val_".$shop_id.".val_string
                    WHEN 'val_text' THEN val_".$shop_id.".val_text
                    WHEN 'val_file' THEN val_".$shop_id.".val_file
                END AS value
                FROM val_".$shop_id." 
                LEFT JOIN props_".$shop_id." ON
                props_".$shop_id.".id = val_".$shop_id.".props_id 
                ".$join."
                WHERE ".$prod_queries."
                ".implode(' AND ', $query_part)."
                ";
            }
            $ask = q(implode(" UNION ALL ", $querys)." ".$products_limit);
            $rows = SQL_ROWS($ask);
            if(!empty($rows)) {
                $rows = array_column($rows, 'CODE');
            }
        }
        return $rows;
    }

    private static function re_parse(int $shop_id, array $conditions, &$product_conditions): array {
        $ans = [];
        $product_fields = ['Название товара', 'Количество', 'Тип (категория)', 'Подкатегория', 'Множество'];
        $pattern = '/\((.*?)\)\s*(>=|<=|=|>|<|in)\s*(\(?[^)]*\)?)/';
        foreach ($conditions as $condition) {
            if (preg_match($pattern, $condition, $matches)) {
                $field = trim($matches[1]);
                $operator = trim($matches[2]);
                $value = trim($matches[3]);

                if ($operator === 'in') {
                    $operator = " LIKE '%" . db_secur(self::remove_surrounding_brackets($value)) . "%' ";
                } else {
                    $operator = $operator . "'" . db_secur(self::remove_surrounding_brackets($value)) . "' ";
                }

                if(in_array($field, $product_fields)) {
                    switch($field) {
                        case 'Название товара': $product_conditions[] = " products_".$shop_id.".name ".$operator ; break;
                        case 'Количество': $product_conditions[] = " products_".$shop_id.".count ".$operator ; break;
                        case 'Тип (категория)': $product_conditions[] = " products_".$shop_id.".main_cat ".$operator ; break;
                        case 'Подкатегория': $product_conditions[] = " products_".$shop_id.".under_cat ".$operator ; break;
                        case 'Множество': $product_conditions[] = " products_".$shop_id.".action_list ".$operator ; break;
                    }
                } else {
                    $ans[] = "
                    props_" . $shop_id . ".props_name = '" . db_secur($field) . "' AND (
                    (props_" . $shop_id . ".types = 'val_float' AND val_" . $shop_id . ".val_float " . $operator . ") OR
                    (props_" . $shop_id . ".types = 'val_string' AND val_" . $shop_id . ".val_string " . $operator . ") OR
                    (props_" . $shop_id . ".types = 'val_text' AND val_" . $shop_id . ".val_text " . $operator . ") OR
                    (props_" . $shop_id . ".types = 'val_file' AND val_" . $shop_id . ".val_file " . $operator . ")
                    )";
                }
            }
        }
        if(empty($ans)) {
            $ans[] = "props_" . $shop_id . ".props_name = 'Стоимость' AND props_" . $shop_id . ".types = 'val_float' AND val_" . $shop_id . ".val_float >= 0 ";
        }
        return $ans;
    }

    private static function remove_surrounding_brackets($str) {
        if (preg_match('/^\((.+)\)$/', $str, $matches)) {
            return $matches[1];
        } else {
            return $str;
        }
    }
}