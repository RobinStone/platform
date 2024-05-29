<?php
class REVIEWS_PROD {
    public static function get_review($shop_id, $product_id, $author_id): array
    {
        $row = SQL_ONE_ROW(q("
                    SELECT * FROM `reviews_prod` WHERE 
                    `shop_id`=".(int)$shop_id." AND
                    `product_id` = ".(int)$product_id." AND 
                    `author_id`=".$author_id."
                    "));
        if(is_array($row)) {
            return $row;
        } else {
            return [];
        }
    }
    public static function delete_all_reviews_at_shop(int $shop_id): bool {
        q("DELETE FROM `reviews_prod` WHERE `shop_id`=".$shop_id);
        return true;
    }
    public static function delete_reviews_array(int $shop_id, array $products_ids): bool {
        foreach($products_ids as $k=>$v) {
            $products_ids[$k] = (int)$v;
        }
        if(count($products_ids) > 0) {
            q("DELETE FROM `reviews_prod` WHERE `shop_id`=".$shop_id." AND `product_id` IN (".implode(',',$products_ids).") ");
            return true;
        }
        return false;
    }
    public static function delete_all_reviews_of_shop(int $shop_id): bool {
        q("DELETE FROM `reviews_prod` WHERE `shop_id`=".$shop_id);
        return true;
    }

    public static function get_all_reviews_at_ouner_product_id(int $owner_product, int $from=0, int $count_rows=20): array
    {
        INCLUDE_CLASS('shops', 'cataloger');
        INCLUDE_CLASS('shops', 'shop');
        $CATS = new CATALOGER();
        $shops = [];
        $users = [];
        $prods = [];
        $querys = [];
//        $rows = SUBD::getAllLinesDB('reviews_prod', 'owner_product', $owner_product);
        $rows = SQL_ROWS(q("SELECT * FROM reviews_prod WHERE owner_product=".$owner_product." ORDER BY changed_review DESC LIMIT ".$from.",".$count_rows." "));
        foreach($rows as $v) {
            if(SHOP::get_shop($v['shop_id']) !== false) {
                $shops[$v['shop_id']][] = (int) $v['product_id'];
                $users[] = (int) $v['author_id'];
            }
        }
        foreach($shops as $k=>$v) {
            $querys[] = "SELECT ".$k." AS shop_id, id AS product_id, status, name, trans, main_cat, under_cat, action_list FROM products_".(int)$k." WHERE id IN (".implode(',', $v).")";
        }
        if(count($querys) === 0) {
            return [];
        }
        $ask = q(implode(" UNION ALL ", $querys));
        $pr = SQL_ROWS($ask);
        foreach($pr as $v) {
            $v['main_cat'] = $CATS->id2main_cat($v['main_cat'], true);
            $v['under_cat'] = $CATS->id2under_cat($v['under_cat'], true);
            $v['action_list'] = $CATS->id2action_list($v['action_list'], true);
            $prods[$v['shop_id']."_".$v['product_id']] = $v;
        }
        if(count($users) > 0) {
            $users = SQL_ROWS_FIELD(q("SELECT id, avatar, login FROM users WHERE id IN (".implode(',', $users).")"), 'id');
        }
        foreach($rows as $k=>$v) {
            $rows[$k]['dt'] = date('d.m.y H:i', strtotime($v['data_review']));
            if(isset($users[$v['author_id']])) {
                $rows[$k]['user'] = $users[$v['author_id']];
            }
            if(isset($prods[$v['shop_id']."_".$v['product_id']])) {
                $rows[$k]['product'] = $prods[$v['shop_id']."_".$v['product_id']];
            }
        }


        return $rows;
    }
    public static function set_review($shop_id, $product_id, $author_id, $html, $stars=0, $type_review='продукт|корзина|продавец'): bool
    {
        $stars = round((float)$stars, 2);
        if($stars < 0) { $stars = 0; }
        if($stars > 5) { $stars = 5; }
        switch($type_review) {
            case 'продукт':
            case 'корзина':
            case 'продавец':
                break;
            case 'ответ':
                if($row = SQL_ONE_ROW(q("
                SELECT * FROM reviews_prod WHERE
                id=".(int)$product_id." AND
                owner_product=".Access::userID()."
                LIMIT 1
                "))) {
                    q("
                    UPDATE reviews_prod SET
                    html_ans='".db_secur($html)."',
                    changed_review='".date('Y-m-d H:i:s')."' 
                    WHERE
                    id=".(int)$product_id." LIMIT 1
                    ");
                    return true;
                }
                return false;
                break;
            default:
                $type_review = 'продукт';
                break;
        }
        $row = SQL_ONE_ROW(q("
                    SELECT * FROM `reviews_prod` WHERE 
                    `shop_id`=".(int)$shop_id." AND
                    `type_review`='".$type_review."' AND
                    `product_id` = ".(int)$product_id." AND 
                    `author_id`=".$author_id."
                    "));
        if(is_array($row)) {
            q("
            UPDATE `reviews_prod` SET 
            `html`='".db_secur($html)."',
            `stars`=".round($stars, 2).",
            `changed_review`='".date('Y-m-d H:i:s')."'
            WHERE `id`=".$row['id']."
            ");
        } else {
            $owner_product = SUBD::getLineDB('shops', 'id', (int)$shop_id);
            if(is_array($owner_product)) { $owner_product = (int)$owner_product['owner']; } else { $owner_product = -1; }
            q("
            INSERT INTO `reviews_prod` SET 
            `shop_id`=".(int)$shop_id.",
            `product_id` = ".(int)$product_id.", 
            `type_review`='".$type_review."',
            `owner_product` = ".$owner_product.", 
            `author_id`=".(int)$author_id.",
            `html`='".db_secur($html)."',
            `stars`=".round($stars, 2).",
            `data_review`='".date('Y-m-d H:i:s')."',
            `changed_review`='".date('Y-m-d H:i:s')."'
            ");
        }
        return true;
    }
}