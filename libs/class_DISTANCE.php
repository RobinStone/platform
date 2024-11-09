<?php
class DISTANCE {
    // gravisinus
    public static function find_near_points($lat, $lng, $count=10, int $main_cat=-1, int $under_cat=-1, int $action_list=-1, bool $only_active=true): array
    {
        $categorizator = [];
        if($main_cat > -1) {
            $categorizator[] = " indexer.shops_categorys=".$main_cat." ";
        }
        if($under_cat > -1) {
            $categorizator[] = " indexer.shops_undercats=".$under_cat." ";
        }
        if($action_list > -1) {
            $categorizator[] = " indexer.shops_lists=".$action_list." ";
        }
        if($only_active === true) {
            $categorizator[] = " indexer.status='active' ";
        }

        if(!empty($categorizator)) {
            $categorizator = " WHERE ".implode(" AND ", $categorizator);
        } else {
            $categorizator = "";
        }

        $ask = q("
        SELECT 
        coords.id AS id_indexer, 
        (6371 * acos(cos(radians(lat)) * cos(radians(".$lat.")) * cos(radians(".$lng.") - radians(lng)) + sin(radians(lat)) * sin(radians(".$lat.")))) AS distance,
        CONCAT (coords.shop_id, '_', coords.product_id) AS CODE,
        indexer.name
        FROM coords LEFT JOIN indexer
        ON coords.id = indexer.id ".$categorizator."
        ORDER BY distance 
        LIMIT ".(int)$count."
        ");
        return SQL_ROWS($ask);
    }

    public static function create_new_product_coords($lat, $lng, $country_id, $city_id, $shop_id, $product_id): bool
    {
        if(q("
        INSERT INTO `coords` SET 
        `lat`=".(float)$lat.",
        `lng`=".(float)$lng.",
        `country_id`=".(int)$country_id.",
        `city_id`=".(int)$city_id.",
        `shop_id`=".(int)$shop_id.",
        `product_id`=".(int)$product_id.",
        `changed`='".date('Y-m-d')."'
        ")) {
            return true;
        }
        return false;
    }

    public static function change_product_coords($lat, $lng, $country_id, $city_id, $shop_id, $product_id): bool
    {
        if(q("
        UPDATE `coords` SET 
        `lat`=".(float)$lat.",
        `lng`=".(float)$lng.",
        `country_id`=".(int)$country_id.",
        `city_id`=".(int)$city_id.",
        `changed`='".date('Y-m-d')."' 
        WHERE 
        `shop_id`=".(int)$shop_id." AND 
        `product_id`=".(int)$product_id."
        ")) {
            return true;
        }
        return false;
    }

    public static function get_field_value_from_array($field, $arr) {
        foreach($arr as $v) {
            if(isset($v['field']) && $v['field'] === $field) {
                return $v['value'];
            }
        }
        return false;
    }
}