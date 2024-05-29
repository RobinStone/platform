<?php
class DISTANCE {
    // gravisinus
    public static function find_near_points($lat, $lng, $count=10): array
    {
        $ask = q("
        SELECT *, (6371 * acos(cos(radians(lat)) * cos(radians(".$lat.")) * cos(radians(".$lng.") - radians(lng)) + sin(radians(lat)) * sin(radians(".$lat.")))) AS distance 
        FROM coords 
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