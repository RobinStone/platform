<?php
class INDEXER {

    /**
     * Полностью пересматривает таблицу INDEXER изменяя категории и хозяина того или иного лота
     * @return void
     */
    public static function update() {
        $rows = SUBD::getAllDB('indexer');
        foreach($rows as $k=>$v) {
            q("
            UPDATE `indexer` SET 
            `shops_categorys` = (SELECT `main_cat` FROM `products_".$v['shop_id']."` WHERE `id`=".$v['prod_id']."),
            `shops_undercats` = (SELECT `under_cat` FROM `products_".$v['shop_id']."` WHERE `id`=".$v['prod_id']."),
            `shops_lists` = (SELECT `action_list` FROM `products_".$v['shop_id']."` WHERE `id`=".$v['prod_id']."),
            `owner_id` = (SELECT `owner` FROM `shops` WHERE `id`=".$v['shop_id']." LIMIT 1)
            WHERE `id` = ".$v['id']."
            ");
        }
    }

    public static function get_next_cards(int $count, array $isset, array $params=[]) {
        $count = max(1, min($count, 20));

        $isset = VALUES::array_to_int($isset);
        $queries = [];
        $query = "";
        if(!empty($params['owner_id'])) {
            $queries[] = " `owner_id`=".(int)$params['owner_id']." ";
        }
        if(!empty($params['owner_login'])) {
            $queries[] = " `owner_id`=".USERS::login_to_id($params['owner_login'])." ";
        }
        if(!empty($params['main_cat'])) {
            $queries[] = " `shops_categorys`=".(int)$params['main_cat']." ";
        }
        if(!empty($params['under_cat'])) {
            $queries[] = " `shops_undercats`=".(int)$params['under_cat']." ";
        }
        if(!empty($params['action_list'])) {
            $queries[] = " `shops_lists`=".(int)$params['action_list']." ";
        }
        if(!empty($params['city_id'])) {
            $queries[] = " `city_id`=".(int)$params['city_id']." ";
        }
        if(!empty($queries)) {
            $query = " AND ".implode(" AND ", $queries);
        }

        if(!empty($isset)) {
            $rows = SQL_ROWS_FIELD(q("
            SELECT 
            CONCAT(`shop_id`,'_',`prod_id`) as code,
            `id`, `shop_id`, `prod_id` as product_id FROM `indexer`
            WHERE
            `active`=1 AND `id` NOT IN (" . implode(',', $isset) . ") " . $query . " 
            ORDER BY RAND() LIMIT " . $count . "
            "), 'code');
        } else {
            $rows = SQL_ROWS_FIELD(q("
            SELECT 
            CONCAT(`shop_id`,'_',`prod_id`) as code,
            `id`, `shop_id`, `prod_id` as product_id FROM `indexer`
            WHERE
            `active`=1 " . $query . " 
            ORDER BY RAND() LIMIT " . $count . "
            "), 'code');
        }
        return $rows;
    }
}

// смотри класс SEARCH

