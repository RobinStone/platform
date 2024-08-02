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

    public static function isset(int $shop_id): bool|array
    {
        return SQL_ONE_ROW(q("SELECT * FROM shops WHERE id=".$shop_id));
    }

    public static function product_code_to_indexer_ids(array $product_codes): array
    {
        $arr = VALUES::wrap_array_elements_around($product_codes);
        $arr = SQL_ROWS(q("SELECT id FROM indexer WHERE CONCAT(shop_id, '_', prod_id) IN (".implode(',',$arr).")"));
        return array_column($arr, 'id');
    }

    public function create($name_shop, $logo, $city_id, $country_id, $address, $title='', $descr=''): bool {
        if($title === '') { $title = $name_shop; }
        if($name_shop === '') {
            $this->error('Название магазина не может быть пустым.');
            return false;
        }
        $translit = VALUES::translit($name_shop);
        if($this->isset_shop($name_shop, $city_id, $address)) {
            $this->error('Такой магазин уже присутствует.');
            return false;
        }

        q("
        INSERT INTO `shops` SET 
        `name`          = '".db_secur($name_shop)."',
        `domain`        = '".db_secur($translit)."',
        `logo`          = '".db_secur($logo)."',
        `country_id`    = '".db_secur($country_id)."',
        `city`          = '".db_secur($city_id)."',
        `owner`         = ".Access::userID().",
        `address`       = '".db_secur($address)."',
        `title`         = '".db_secur($title)."',
        `active_to`     = '".VALUES::plus_days(30)."',
        `created`       = '".date('Y-m-d H:i:s')."',
        `descr`         = '".db_secur($descr)."'
        ");

        $id = SUBD::get_last_id();
        $_SESSION['shop_id'] = $id;
        return false;
    }

    public static function get_shop($id_shop) {
        $row = SUBD::getLineDB('shops', 'id', (int)$id_shop);
        if(is_array($row)) {
            $row['owner'] = (array)SUBD::getLineDB('users', 'id', (int)$row['owner']);
            $row['city'] = (array)SUBD::getLineDB('city', 'id', (int)$row['city']);
            // тут нужно вставить скан для определения широты и долготы магазина по умолчанию
            if($row_c = SQL_ONE_ROW(q("SELECT shirota, dolgota FROM cities WHERE name = '".db_secur($row['city']['name'])."' LIMIT 1"))) {
                $row['city']['shirota'] = $row_c['shirota'];
                $row['city']['dolgota'] = $row_c['dolgota'];
            } else {
                $row['city']['shirota'] = -1;
                $row['city']['dolgota'] = -1;
            }
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

    public static function change_product_in_shop(int $shop_id, int $product_id, $props=[]): bool {
        if($shop_id < 1) {
            Message::addError('Не передан ID магазина');
            return false;
        }



        if(!SHOP::is_my_shop($shop_id, SITE::$user_id)) {
            Message::addError('Нельзя работать с товарами не своего магазина!..');
            return false;
        }

        SORT::change_preview_key($props, 'alias', 'field_name');

        $code = $shop_id."_".$product_id;
        $old = self::get_one_product_at_code_if_isset($code);

        $querys = [];

        $coords = [];

//        say($props);

        foreach($props as $alias=>$prop_item) {
            switch($alias) {
                case 'images':
                    $buff = [];
                    $del_arr = [];
                    $add_arr = [];
                    foreach($old['PROPS']['images'] as $img_item) {
                        if(!in_array($img_item['value'], $prop_item['value'])) {
                            $del_arr[] = $img_item['id'];
                        }
                        $buff[] = $img_item['value'];
                    }
                    foreach($prop_item['value'] as $v) {
                        if(!in_array($v, $buff)) {
                            $add_arr[] = $v;
                        }
                    }
                    if(!empty($del_arr)) {
                        q("DELETE FROM i_string WHERE id IN (".implode(',',$del_arr).") ");
                    }
                    if(!empty($add_arr)) {
                        foreach($add_arr as $v) {
                            q("
                            INSERT INTO i_string SET 
                            indexer_id=".(int)$old['id'].",
                            props_id=1,
                            val='".db_secur($v)."'
                            ");
                        }
                    }
                    break;
                case 'product_name':
                    $querys[] = " `name`='".db_secur($prop_item['value'])."' ";
                    break;
                case 'price':
                    $querys[] = " `price`='".(float)($prop_item['value'])."' ";
                    break;
                case 'count':
                    $querys[] = " `count`='".(float)($prop_item['value'])."' ";
                    break;
                case 'city_id':
                    $querys[] = " `city_id`=".(int)$prop_item['value']." ";
                    $coords[] = " `city_id`=".(int)$prop_item['value']." ";
                    break;
                case 'country_id':
                    $coords[] = " `country_id`=".(int)$prop_item['value']." ";
                    break;
                case 'latitude':
                    $coords[] = " `lat`='".(float)($prop_item['value'])."' ";
                    break;
                case 'longitude':
                    $coords[] = " `lng`='".(float)($prop_item['value'])."' ";
                    break;
                case 'category':
                    $querys[] = " `shops_categorys`='".(int)($prop_item['value'])."' ";
                    break;
                case 'under-cat':
                    $querys[] = " `shops_undercats`='".(int)($prop_item['value'])."' ";
                    break;
                case 'action-list':
                    $querys[] = " `shops_lists`='".(int)($prop_item['value'])."' ";
                    break;
                default:
                    switch($prop_item['type']) {
                        case 'bool':
                            if(!empty($prop_item['id_i'])) {
                                q("
                                UPDATE i_bool SET 
                                val=".(int)$prop_item['value']." 
                                WHERE id=".(int)$prop_item['id_i']." AND indexer_id=".(int)$old['id']);
                            }
                            break;
                        case 'json':
                            if(!empty($prop_item['id_i'])) {
                                q("
                                UPDATE i_json SET 
                                val='".serialize($prop_item['value'])."'
                                WHERE id=".(int)$prop_item['id_i']." AND indexer_id=".(int)$old['id']);
                            }
                            break;
                        case 'char':
                            if(!empty($prop_item['id_i'])) {
                                q("
                                UPDATE i_char SET 
                                val='".mb_strtoupper($prop_item['value'])."' 
                                WHERE id=".(int)$prop_item['id_i']." AND indexer_id=".(int)$old['id']);
                            }
                            break;
                        case 'string':
                            if(!empty($prop_item['id_i'])) {
                                q("
                                UPDATE i_string SET 
                                val='".db_secur($prop_item['value'])."' 
                                WHERE id=".(int)$prop_item['id_i']." AND indexer_id=".(int)$old['id']);
                            }
                            break;
                        case 'float':
                            if(!empty($prop_item['id_i'])) {
                                q("
                                UPDATE i_float SET 
                                val='".(float)($prop_item['value'])."' 
                                WHERE id=".(int)$prop_item['id_i']." AND indexer_id=".(int)$old['id']);
                            }
                            break;
                        case 'text':
                            if(!empty($prop_item['id_i'])) {
                                q("
                                UPDATE i_text SET 
                                val='".db_secur($prop_item['value'])."' 
                                WHERE id=".(int)$prop_item['id_i']." AND indexer_id=".(int)$old['id']);
                            }
                            break;
                        case 'int':
                            if(!empty($prop_item['id_i'])) {
                                q("
                                UPDATE i_int SET 
                                val=".(int)$prop_item['value']." 
                                WHERE id=".(int)$prop_item['id_i']." AND indexer_id=".(int)$old['id']);
                            }
                            break;
                    }
                    break;
            }
        }

        if(!empty($querys)) {
            q("UPDATE indexer SET ".implode(',',$querys).", changed='".SITE::$dt."' WHERE id=".(int)$old['id']);
        }

        if(!empty($coords)) {
            q("UPDATE coords SET ".implode(',',$coords).", changed='".date('Y-m-d')."' WHERE id=".(int)$old['id']);
        }

        return true;
    }

    public static function create_product_in_shop($shop_id, $props=[]): bool {
        $shop_id = (int)$shop_id;

        if($shop_id < 1) {
            Message::addError('Не передан ID магазина');
            return false;
        }

        if(!SHOP::is_my_shop($shop_id, SITE::$user_id)) {
            Message::addError('Нельзя работать с товарами не своего магазина!..');
            return false;
        }

        self::insert_products_array([$props], $shop_id);

        return true;
    }

    private static function insert_products_array(array $arr, int $shop_id): void
    {
        $schema = get_product_schema();
        SORT::change_preview_key($schema, 'alias', 'field_name', true);

        $address = [];

        $shop = SHOP::get_shop($shop_id);
        if(empty($shop['city']) || empty($shop['city']['shirota']) || empty($shop['city']['id'])) {
            Message::addError('Не удалось определить координаты города в магазине');
            return;
        } else {
            $address = [
                'city_id'=>($shop['city']['id'] ?? -1),
                'latitude'=>$shop['city']['shirota'],
                'longitude'=>$shop['city']['dolgota'],
                'country_id'=>GEONAMER::country_name_to_id(($shop['city']['id_country'] ?? '-')),
            ];
        }

        $main_table = [];
        $coords = [];

        $i_string = [];
        $i_float = [];
        $i_int = [];
        $i_text = [];
        $i_bool = [];
        $i_json = [];
        $i_char = [];

        $first_id = SUBD::get_next_id_for_column('indexer', 'prod_id', 'shop_id', $shop_id);
        $indexer_next_id = SUBD::get_next_id('indexer', true);

        $product_id = $first_id;

        if(!isset($arr[0]) || !isset($arr[0]['product_name'])) {
            t('Передан некоректный массив с товарами');
            Message::addError('Передан некоректный массив с товарами');
            return;
        }

        // Собирается основная таблица товаров

        foreach($arr as $k=>$v) {
            $country_id = (int)($v['country_id']['value'] ?? ($address['country_id'] ?? -1));
            $city_id = (int)($v['city_id']['value'] ?? ($address['city_id'] ?? -1));
            $latitude = (float)($v['latitude']['value'] ?? -1);
            $longitude = (float)($v['longitude']['value'] ?? -1);

            $main_table[$product_id] = "(
            0, 'not_show', 
            '".($v['product_name']['value'] ?? '-')."',
            '".(float)($v['price']['value'] ?? 0)."',
            '".(float)($v['count']['value'] ?? 0)."',
            ".$product_id.",
            ".$shop_id.",
            ".SITE::$user_id++.",
            '".$city_id."',
            0,
            '".(int)($v['category']['value'] ?? -1)."',
            '".(int)($v['under-cat']['value'] ?? -1)."',
            '".(int)($v['action-list']['value'] ?? -1)."',
            '".SITE::$dt."',
            '".SITE::$dt."'
            )";

            unset($v['product_name'], $v['price'], $v['count'], $v['country_id'], $v['city_id'],
                $v['category'], $v['under-cat'], $v['action-list'], $v['latitude'], $v['longitude']);

            // Собираются поля для таблицы coords

            $coords[] = "(".$indexer_next_id.",'".$latitude."','".$longitude."',".$country_id.",".$city_id.",".$shop_id.",".$product_id.",0,'".SITE::$dt."')";

            // Собираются все другие свойства в поля

            foreach($v as $props) {
                switch($props['type']) {
                    case 'string':
                        $value = $props['value'] ?? '';
                        if(is_array($value)) {
                            foreach($value as $item_string) {
                                if(!empty($item_string)) {
                                    $i_string[] = "(".$indexer_next_id.",".(int)$props['id'].",'".db_secur($item_string)."')";
                                }
                            }
                        } else {
                            $i_string[] = "(".$indexer_next_id.",".(int)$props['id'].",'".$value."')";
                        }
                        break;
                    case 'float':
                        $value = (float)($props['value'] ?? 0);
                        $i_float[] = "(".$indexer_next_id.",".(int)$props['id'].",'".$value."')";
                        break;
                    case 'int':
                        $value = (int)($props['value'] ?? 0);
                        $i_int[] = "(".$indexer_next_id.",".(int)$props['id'].",'".$value."')";
                        break;
                    case 'text':
                        $value = db_secur($props['value'] ?? '');
                        $i_text[] = "(".$indexer_next_id.",".(int)$props['id'].",'".$value."')";
                        break;
                    case 'json':
                        $value = serialize(($props['json'] ?? ''));
                        $i_json[] = "(".$indexer_next_id.",".(int)$props['id'].",'".$value."')";
                        break;
                    case 'char':
                        $value = ((mb_strtoupper($props['char']) ?? '-'));
                        $i_char[] = "(".$indexer_next_id.",".(int)$props['id'].",'".$value."')";
                        break;
                    case 'bool':
                        $value = (int)(db_secur($props['value'] ?? 0));
                        $i_bool[] = "(".$indexer_next_id.",".(int)$props['id'].",'".$value."')";
                        break;
                }
            }

            ++$product_id;
            ++$indexer_next_id;
        }

        // Инстантирование подготовленных данных в таблицы

        start_transaction();
        q("INSERT INTO `indexer` (`active`, `status`, `name`, `price`, `count`, `prod_id`, `shop_id`, 
        `owner_id`, `city_id`, `showed`, `shops_categorys`, `shops_undercats`, `shops_lists`, `created`, `active_to`) 
        VALUES ".implode(',',$main_table)." ");

        if(!empty($i_string)) {
            q("INSERT INTO `i_string` (`indexer_id`, `props_id`, `val`) VALUES ".implode(',',$i_string)." ");
        }
        if(!empty($i_float)) {
            q("INSERT INTO `i_float` (`indexer_id`, `props_id`, `val`) VALUES ".implode(',',$i_float)." ");
        }
        if(!empty($i_int)) {
            q("INSERT INTO `i_int` (`indexer_id`, `props_id`, `val`) VALUES ".implode(',',$i_int)." ");
        }
        if(!empty($i_text)) {
            q("INSERT INTO `i_text` (`indexer_id`, `props_id`, `val`) VALUES ".implode(',',$i_text)." ");
        }
        if(!empty($i_bool)) {
            q("INSERT INTO `i_bool` (`indexer_id`, `props_id`, `val`) VALUES ".implode(',',$i_bool)." ");
        }
        if(!empty($i_json)) {
            q("INSERT INTO `i_json` (`indexer_id`, `props_id`, `val`) VALUES ".implode(',',$i_json)." ");
        }
        if(!empty($i_char)) {
            q("INSERT INTO `i_char` (`indexer_id`, `props_id`, `val`) VALUES ".implode(',',$i_char)." ");
        }

        // Собираются данные в таблицу координат

        q("INSERT INTO `coords` (`id`, `lat`, `lng`, `country_id`, `city_id`, `shop_id`, `product_id`, 
        `active`, `changed`) VALUES ".implode(',',$coords)." ");
        end_transaction();

    }

    public static function delete_products_arr($shop_id, $arr=[]): bool {
        $codes = [];
        if(!empty($arr)) {
            foreach($arr as $k=>$v) {
                $codes[] = $shop_id."_".$v;
            }

            $indexer_ids = SHOP::product_code_to_indexer_ids($codes);

            q("DELETE FROM `indexer` WHERE `id` IN (".implode(',',$indexer_ids).") ");
            q("DELETE FROM `coords` WHERE `id` IN (".implode(',',$indexer_ids).") ");

            q("DELETE FROM i_string WHERE `indexer_id` IN (".implode(',',$indexer_ids).")");
            q("DELETE FROM i_float WHERE `indexer_id` IN (".implode(',',$indexer_ids).")");
            q("DELETE FROM i_int WHERE `indexer_id` IN (".implode(',',$indexer_ids).")");
            q("DELETE FROM i_text WHERE `indexer_id` IN (".implode(',',$indexer_ids).")");
            q("DELETE FROM i_bool WHERE `indexer_id` IN (".implode(',',$indexer_ids).")");

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

    public static function get_only_main_parameters_at_product_code(string $product_code): bool|array
    {
        return SQL_ONE_ROW(q("SELECT * FROM indexer WHERE CONCAT(shop_id, '_', prod_id) = '".db_secur($product_code)."'"));
    }

    public static function get_one_product_at_code_if_isset(string $product_code="234_34"): bool|array
    {
        $count = (int)SQL_ONE_ROW(q("SELECT COUNT(*) AS count FROM indexer WHERE CONCAT(shop_id, '_', prod_id) = '".db_secur($product_code)."' "))['count'];
        if($count > 0) {
            $product = self::get_products_list_at_code_array([$product_code], true, 'name', 'ASC', [0,1])[0];
            $coords_props = GEONAMER::generate_scheama_array($product['id']);

            foreach($coords_props as $k=>$v) {
                $product['PROPS'][$k] = $v;
            }
            return $product;
        }
        return false;
    }

    public static function get_products_list_at_code_array(array $code_of_products, bool $with_categorys=true, string $sorted_by='name', string $direct='ASC', array $limit=[0, 50]): array
    {
        if($direct !== 'ASC') {
            $direct = 'DESC';
        }
        $limit = " LIMIT ".(int)$limit[0].",".(int)$limit[1];
        $ids = "id > 0 ";
        if(!empty($code_of_products)) {

            $code_of_products = array_map(function($value) {
                return "'" . $value . "'";
            }, $code_of_products);

            $ids .= " AND CONCAT(indexer.shop_id, '_', indexer.prod_id) IN (".implode(',', $code_of_products).") ";
        }
        switch($sorted_by) {
            case 'id':
            case 'code':
            case 'city_id':
            case 'shop_id':
            case 'prod_id':
            case 'count':
            case 'created':
            case 'active_to':

                break;
            default: $sorted_by = 'name';
        }

        $sorted_by = " ORDER BY ".$sorted_by;

        $rows = SQL_ROWS(q("
        SELECT 
        indexer.id,
        CONCAT(indexer.shop_id, '_', indexer.prod_id) AS code,
        indexer.city_id,
        indexer.shop_id,
        indexer.prod_id,
        indexer.status, 
        indexer.name, 
        indexer.price, 
        indexer.showed, 
        indexer.shops_categorys AS main_cat, 
        indexer.shops_undercats AS under_cat, 
        indexer.shops_lists AS action_list,
        indexer.count,
        indexer.created,
        indexer.active_to FROM indexer
        WHERE ".$ids." ".$sorted_by." ".$direct." ".$limit."
        "));

        $cat = null;
        if($with_categorys) {
//            include_once './APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
            include_once Core::$path.'/APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
            $cat = new CATALOGER();
        }

        foreach($rows as $k=>$v) {
            if ($with_categorys) {
                $v['main_cat_trans'] = $cat->id2main_cat($v['main_cat'], true);
                $v['under_cat_trans'] = $cat->id2under_cat($v['under_cat'], true);
                $v['action_list_trans'] = VALUES::translit($cat->id2action_list($v['action_list']), true);

                $v['main_cat_id'] = $v['main_cat'];
                $v['main_cat'] = $cat->id2main_cat($v['main_cat']);

                $v['under_cat_id'] = $v['under_cat'];
                $v['under_cat'] = $cat->id2under_cat($v['under_cat']);

                $v['action_list_id'] = $v['action_list'];
                $v['action_list'] = $cat->id2action_list($v['action_list']);
                $rows[$k] = $v;
            }
        }

//        INCLUDE_CLASS('shops', 'props_commander');
        include_once Core::$path.'/APPLICATIONS/SHOPS/libs/class_PROPS_COMMANDER.php';

        $PROPS = new PROPS_COMMANDER(array_column($rows, 'id'));
        $pr = $PROPS->get_all_props();
        foreach($rows as &$v) {
            $v['PROPS'] = $pr[$v['id']];
        }

        return $rows;
    }

    public static function get_products_list_at_indexer_ids(array $indexer_ids, bool $with_categorys=true, string $sorted_by='name', string $direct='ASC', array $limit=[0, 50]):array {
        if($direct !== 'ASC') {
            $direct = 'DESC';
        }
        $limit = " LIMIT ".(int)$limit[0].",".(int)$limit[1];
        $ids = "id > 0 ";
        if(!empty($indexer_ids)) {

            $indexer_ids = array_map(function($value) {
                return "'" . $value . "'";
            }, $indexer_ids);

            $ids .= " AND indexer.id IN (".implode(',', $indexer_ids).") ";
        }
        switch($sorted_by) {
            case 'id':
            case 'code':
            case 'city_id':
            case 'shop_id':
            case 'prod_id':
            case 'count':
            case 'created':
            case 'active_to':

                break;
            default: $sorted_by = 'name';
        }

        $sorted_by = " ORDER BY ".$sorted_by;

        $rows = SQL_ROWS(q("
        SELECT 
        indexer.id,
        CONCAT(indexer.shop_id, '_', indexer.prod_id) AS code,
        indexer.city_id,
        indexer.shop_id,
        indexer.prod_id,
        indexer.status, 
        indexer.name, 
        indexer.price, 
        indexer.showed, 
        indexer.shops_categorys AS main_cat, 
        indexer.shops_undercats AS under_cat, 
        indexer.shops_lists AS action_list,
        indexer.count,
        indexer.created,
        indexer.active_to FROM indexer
        WHERE ".$ids." ".$sorted_by." ".$direct." ".$limit."
        "));

        $cat = null;
        if($with_categorys) {
            include_once './APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
            $cat = new CATALOGER();
        }

        foreach($rows as $k=>$v) {
            if ($with_categorys) {
                $v['main_cat_trans'] = $cat->id2main_cat($v['main_cat'], true);
                $v['under_cat_trans'] = $cat->id2under_cat($v['under_cat'], true);
                $v['action_list_trans'] = VALUES::translit($cat->id2action_list($v['action_list']), true);

                $v['main_cat_id'] = $v['main_cat'];
                $v['main_cat'] = $cat->id2main_cat($v['main_cat']);

                $v['under_cat_id'] = $v['under_cat'];
                $v['under_cat'] = $cat->id2under_cat($v['under_cat']);

                $v['action_list_id'] = $v['action_list'];
                $v['action_list'] = $cat->id2action_list($v['action_list']);
                $rows[$k] = $v;
            }
        }

        INCLUDE_CLASS('shops', 'props_commander');

        $PROPS = new PROPS_COMMANDER(array_column($rows, 'id'));
        $pr = $PROPS->get_all_props();
        foreach($rows as &$v) {
            $v['PROPS'] = $pr[$v['id']];
        }

        return $rows;
    }


    public static function get_products_list_at_id($shop_id, $id_arr=[], $with_categorys=false, $sorted_by="name", $askOrDesc="ASC", $limit="0,50"): array {
        if($askOrDesc !== 'ASC') {
            $askOrDesc = 'DESC';
        }
        $limit = " LIMIT ".$limit;
        $ids = "";
        if(!empty($id_arr)) {
            $ids = " AND indexer.prod_id IN (".implode(',', $id_arr).") ";
        }
        switch($sorted_by) {
            case 'id':

                break;
            default: $sorted_by = 'name';
        }
        $shop = SUBD::get_executed_rows('shops', 'id', (int)$shop_id)[$shop_id];

        $rows = SQL_ROWS(q("
        SELECT 
        indexer.id,
        CONCAT(indexer.shop_id, '_', indexer.prod_id) AS code,
        '".$shop['city']."' AS city,
        indexer.shop_id AS id_shop,
        indexer.prod_id AS id_product,
        indexer.status, 
        indexer.name, 
        indexer.price, 
        indexer.shops_categorys AS main_cat, 
        indexer.shops_undercats AS under_cat, 
        indexer.shops_lists AS action_list,
        indexer.count,
        indexer.created,
        indexer.active_to AS to_active FROM indexer
        WHERE indexer.shop_id = ".$shop_id.$ids." ".$limit."
        "));

        $indexer_ids = array_column($rows, 'id');
        $schema = get_product_schema();
        SORT::change_preview_key($schema, 'id', 'field_name');



        $cat = null;
        if($with_categorys) {
            include_once './APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
            $cat = new CATALOGER();
        }

        foreach($rows as $k=>$v) {
            if ($with_categorys) {
                $v['main_cat_trans'] = $cat->id2main_cat($v['main_cat'], true);
                $v['under_cat_trans'] = $cat->id2under_cat($v['under_cat'], true);
                $v['action_list_trans'] = VALUES::translit($cat->id2action_list($v['action_list']), true);
                $v['main_cat'] = $cat->id2main_cat($v['main_cat']);
                $v['under_cat'] = $cat->id2under_cat($v['under_cat']);
                $v['action_list'] = $cat->id2action_list($v['action_list']);
                $rows[$k] = $v;
            }
        }
        return $rows;
    }

    public static function get_random_products_from_one_shop($shop_id, $random=false, $count=-1, $main_cat_id=-1, $under_cat_id=-1, $action_list_id=-1, $only_active=false):array {
        return self::get_mix_products_at_all_shops($random, $count, -1, $main_cat_id, $under_cat_id, $action_list_id, [$shop_id], $only_active);
    }

    public static function get_mix_products_at_all_shops(bool $random=false, int|array $count=10, int $city_id= -1,
                     int $main_cat_id=-1, int $under_cat_id=-1, int $action_list_id=-1, array $shops_id_arr=[]): array
    {
        $rows = [];
        if($shops_id_arr === []) {
            $shops = SQL_ROWS(q("SELECT id FROM `shops` WHERE `active`=1 "));
        } else {
            if (is_array($shops_id_arr) && count($shops_id_arr) > 0) {
                $shops = SQL_ROWS(q("SELECT id FROM shops WHERE id IN (".implode(',',$shops_id_arr).") AND active = 1"));
            } else {
                Message::addError('Не найдены id магазинов на которые ссылается метод...');
                exit;
            }
        }
        $shops = array_column($shops, 'id');
        if(count($shops) > 0) {

            $end = "";
            if(is_array($count)) {
                $end = " LIMIT ".$count[0].",".$count[1];
            } else {
                $end = " LIMIT ".$count;
            }

            if($random) {
                $end = " ORDER BY RAND() ".$end;
            }

            $filter_city = "";
            if($city_id !== -1) {
                $filter_city = " AND city_id = ".$city_id." ";
            }
            $filter_maincat = "";
            if($main_cat_id !== -1) {
                $filter_maincat = " AND shops_categorys = ".$main_cat_id." ";
            }
            $filter_undercat = "";
            if($under_cat_id !== -1) {
                $filter_undercat = " AND shops_undercats = ".$under_cat_id." ";
            }
            $filter_actionlist = "";
            if($action_list_id !== -1) {
                $filter_actionlist = " AND shops_lists = ".$action_list_id." ";
            }

            $rows = SQL_ROWS(q("
            SELECT id FROM indexer WHERE 
            shop_id IN (".implode(',',$shops).") AND 
            status = 'active'
            ".$filter_city."
            ".$filter_maincat."
            ".$filter_undercat."
            ".$filter_actionlist." ".$end."
            "));
            $rows = array_column($rows, 'id');

            if(empty($rows)) {
                return [];
            }

            $rows = self::get_products_list_at_indexer_ids($rows);
        }
        return $rows;
    }

    public static function get_count_products($id_owner): int
    {
        return (int)SQL_ONE_ROW(q("SELECT COUNT(*) FROM `indexer` WHERE `owner_id`=".Access::userID()))['COUNT(*)'];
    }

    public static function get_count_status_all_my_products($id_owner, $main_cat_id=-1) {
        $main_cat_id = (int)$main_cat_id;
        $rows = [
            'active'=>[],
            'not_show'=>[],
            'backdrop'=>[],
            'archive'=>[]
        ];
            $dt = SITE::$dt;
            q("UPDATE indexer SET `status` = 'archive' WHERE `active_to` < '".$dt."' AND `status` = 'active' ");
            $all = SQL_ROWS(q("SELECT prod_id, shop_id, status, shops_categorys FROM indexer WHERE owner_id=".Access::userID()));
            foreach($all as $vv) {
                if($main_cat_id === -1) {
                    $rows[$vv['status']][] = [
                        'SHOP' => $vv['shop_id'],
                        'ID_PRODUCT' => $vv['prod_id']
                    ];
                } else {
                    if($main_cat_id === (int)$vv['shops_categorys']) {
                        $rows[$vv['status']][] = [
                            'SHOP' => $vv['shop_id'],
                            'ID_PRODUCT' => $vv['prod_id']
                        ];
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

    public static function set_active_status_products($products_arr=[], $count_hours=1): bool {
        if(count($products_arr) <= 0) {
            Message::addError('Товаров не может быть < или = 0');
            return false;
        }
        foreach($products_arr as $k=>$v) {
            $products_arr[$k] = (int)$v;
        }

        $dt = VALUES::plus_hours($count_hours);
        $status = 'not_show';
        if(date('Y-m-d H:i:s') <= $dt) { $status = 'active'; }
        $hrs = "";
        if($count_hours > 0) {
            $hrs = " `active_to`='".$dt."', ";
        }
        q("
        UPDATE `indexer` SET
        `status`='".$status."',
        `active`=1,
        ".$hrs."
        `changed`='".date('Y-m-d H:i:s')."'
        WHERE `id` IN (".implode(',',$products_arr).")
        ");

        q("
        UPDATE `coords` SET `active`=1 WHERE
        `id` IN (".implode(',',$products_arr).")
        ");

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
        if(!SHOP::isset($id_shop)) {
            Message::addError('Не удалось найти площадку с таким ID - '.$id_shop);
            return false;
        }
        $status = 'not_show';
        q("
        UPDATE `indexer` SET
        `status`='".$status."',
        `changed`='".date('Y-m-d H:i:s')."'
        WHERE `shop_id`=".$id_shop." AND `prod_id` IN (".implode(',',$products_arr).")
        ");

        q("
        UPDATE `coords` SET `active`=0 WHERE
        `shop_id`=".$id_shop." AND
        `product_id` IN (".implode(',',$products_arr).")
        ");

        return true;
    }

    public static function archive_timeout_orders(int $id_shop, bool $without_if=false) {
        if($without_if) {
            $rows = SQL_ROWS(q("SELECT id FROM `indexer` WHERE `shop_id`=".$id_shop." AND `status` = 'active' "));
        } else {
            $rows = SQL_ROWS(q("SELECT id FROM `indexer` WHERE `shop_id`=".$id_shop." AND `active_to` < '".date('Y-m-d H:i:s')."' AND `status` = 'active' "));
        }
        if(count($rows) > 0) {
            $ids = array_column($rows, 'id');
            q("UPDATE `indexer` SET `active`=0, `status`='not_show' WHERE `id` IN (".implode(',',$ids).") ");
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
        $rows = SQL_ROWS(q("SELECT id FROM `indexer` WHERE `shop_id`=".$id_shop." AND `active_to` > '".date('Y-m-d H:i:s')."' AND `status` <> 'active' "));
        if(count($rows) > 0) {
            $ids = array_column($rows, 'id');
            q("UPDATE `indexer` SET `active`=1, `status` = 'active' WHERE `id` IN (".implode(',',$ids).") ");
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
        foreach($orders_array as $v) {
            $arr[] = "'".$v["shop_id"]."_".$v['item_id']."'";
        }
        $ask = q("SELECT id, active_to AS PUBLISHED_TIME FROM indexer WHERE CONCAT(shop_id, '_', prod_id) IN (".implode(',', $arr).")");
        return SQL_ROWS($ask);
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
    public static function get_products_list($array=[]): array
    {
        $codes = [];
        foreach($array as $v) {
            $codes[] = $v['shop_id']."_".$v['product_id'];
        }
        $indexer_prods = self::get_products_list_at_code_array($codes);
        $prods = [];
        foreach($indexer_prods as $v) {
            $prods[$v['code']] = $v;
        }

        unset($indexer_prods);

        foreach($array as $k=>$v) {
            $code = $v['shop_id']."_".$v['product_id'];
            if(isset($prods[$code])) {
                $array[$k]['ITEM'] = $prods[$code];
            }
        }
        return $array;
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

        $product_ids_for_delete = SQL_ROWS(q("SELECT id FROM indexer WHERE shop_id=".$id_shop));
        $product_ids_for_delete = array_column($product_ids_for_delete, 'id');

        q("DELETE FROM indexer WHERE shop_id=".$id_shop);

        q("DELETE FROM `coords` WHERE `shop_id`=".$id_shop);
        q("DELETE FROM `best_prod` WHERE `shop_id`=".$id_shop);
        q("DELETE FROM `reviews_prod` WHERE `shop_id`=".$id_shop);

        q("DELETE FROM `i_bool` WHERE `indexer_id` IN (".implode(',', $product_ids_for_delete).")");
        q("DELETE FROM `i_float` WHERE `indexer_id` IN (".implode(',', $product_ids_for_delete).")");
        q("DELETE FROM `i_int` WHERE `indexer_id` IN (".implode(',', $product_ids_for_delete).")");
        q("DELETE FROM `i_json` WHERE `indexer_id` IN (".implode(',', $product_ids_for_delete).")");
        q("DELETE FROM `i_string` WHERE `indexer_id` IN (".implode(',', $product_ids_for_delete).")");
        q("DELETE FROM `i_text` WHERE `indexer_id` IN (".implode(',', $product_ids_for_delete).")");

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
        if($row = SQL_ONE_ROW(q("
            SELECT id, count FROM indexer WHERE 
            shop_id=".$shop_id." AND 
            prod_id=".$product_id." LIMIT 1
            "))) {
            $count_now = (int)$row['count'];
            if($count_now !== -1) {
                if ($count_plus > 0) {
                    $count_now += $count_plus;
                } else {
                    if($count_now+$count_plus >= 0) {
                        $count_now += $count_plus;
                    }
                }
                q("UPDATE indexer SET count=".$count_now." WHERE id=".(int)$row['id']);
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
     * @param array $shops_limit (ПАГИНАЦИЯ ДЛЯ МАГАЗИНОВ) прим. - [0, 50]
     * @param array $products_limit (ПАГИНАЦИЯ ДЛЯ ТОВАРОВ) прим. - [0, 50]
     * @return array (возвращает массив кодов товаров вида) - ["205_34", "123_44", "12_54"] (где первая цифра id-магазина, 2-ая id-товара)
     */
    public static function filter(array $arr, string $query_shops="", array $shops_limit=[0,50], array $products_limit=[0,50]): array
    {
        $buff = [];
        $querys = [];
        $joins = [];
        $schema = get_product_schema();
        $pattern = '/\((.*?)\)\s*(>=|<=|=|>|<|in)\s*(\(?[^)]*\)?)/';

        if($query_shops !== "") {
            $query_shops = " WHERE ".db_secur($query_shops);
        }
        $shops_limit = $shops_limit[0].",".$shops_limit[1];
        $shops_limit = " LIMIT ".$shops_limit;

        $products_limit = $products_limit[0].",".$products_limit[1];
        $products_limit = " LIMIT ".$products_limit;

        foreach ($arr as $condition) {
            if (preg_match($pattern, $condition, $matches)) {
                $field = trim($matches[1]);
                $operator = trim($matches[2]);
                $value = trim($matches[3]);

                if ($operator === 'in') {
                    $operator = " LIKE '%" . db_secur(self::remove_surrounding_brackets($value)) . "%' ";
                } else {
                    $operator = $operator . "'" . db_secur(self::remove_surrounding_brackets($value)) . "' ";
                }

                if(isset($schema[$field])) {
                    $column = $schema[$field]['column'];
                    $is_main_table = 0;
                    $table = "i_".$schema[$field]['type'];
                    if($column !== '') {
                        $is_main_table = 1;
                        $table = 'indexer';
                    }

                    $buff[] = [
                        'prop_id'=>$schema[$field]['id'],
                        'field_name'=>$field,
                        'value'=>$operator,
                        'type'=>$table,
                        'is_main_table'=>$is_main_table,
                        'column'=>$column,
                    ];
                }
            }
        }

        foreach($buff as $v) {
            if($v['is_main_table'] === 1) {
                $querys[] = "indexer.".$v['column']." ".$v['value'];
            } else {
                $querys[] = "".$v['type'].".val".$v['value'];
                if(!isset($joins[$v['type']])) {
                    $joins[$v['type']] = " LEFT JOIN ".$v['type']." ON ".$v['type'].".indexer_id=indexer.id AND ".$v['type'].".props_id=".$v['prop_id']." ";
                }
            }
        }

        $query = "
        SELECT CONCAT(indexer.shop_id, '_', indexer.prod_id) AS CODE FROM indexer
        ".implode(' ', $joins)."
        WHERE
        ".implode(' AND ', $querys)."
        LIMIT 0, 50
        ";

        $rows = SQL_ROWS(q($query));
        if(count($rows) > 0) {
            return array_column($rows, 'CODE');
        }

        return [];
    }

    private static function remove_surrounding_brackets($str) {
        if (preg_match('/^\((.+)\)$/', $str, $matches)) {
            return $matches[1];
        } else {
            return $str;
        }
    }
}