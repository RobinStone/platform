<?php
class FAVORITE {
    public array $all_favorite_products = [];

    public function __construct(int $user_id) {
        $this->all_favorite_products = SUBD::getAllLinesDB('best_prod', 'user_id', $user_id);
    }

    public function status($shop_id, $product_id):bool|int {
        foreach($this->all_favorite_products as $v) {
            if($v['shop_id'] == $shop_id && $v['product_id'] == $product_id) {
                return (int)$v['id'];
            }
        }
        return false;
    }

    public static function toggle_favorite_status(int $id_shop, int $id_product, int $id_user): ?bool
    {
        $row = SQL_ROWS(q("
                SELECT * FROM `best_prod` WHERE 
                `user_id`=".$id_user." AND
                `shop_id`=".$id_shop." AND
                `product_id`=".$id_product." LIMIT 1
                "));
        if(count($row) > 0) {
            q("DELETE FROM `best_prod` WHERE
            `user_id`=".$id_user." AND
            `shop_id`=".$id_shop." AND
            `product_id`=".$id_product."
            ");
            return false;
        } else {
            q("
            INSERT INTO `best_prod` SET 
            `user_id`=".$id_user.",
            `shop_id`=".$id_shop.",
            `product_id`=".$id_product.",
            `datatime`='".date('Y-m-d H:i:s')."'
            ");
            return true;
        }
    }

    public static function render_JS_execute() {
        ob_start();
        ?>
        function add_rem_favorite(obj, id_shop, id_product) {
            buffer_app = 'SHOPS';
            SENDER_APP('add_rem_favorite', {id_shop: id_shop, id_product: id_product}, function(mess) {
                mess_executer(mess, function(mess) {
                    if(mess.params === true) {
                        $(obj).find('span.svg-wrapper').addClass('hart-red');
                    } else {
                        $(obj).find('span.svg-wrapper').removeClass('hart-red');
                    }
                });
            });
        }
    <?
    return ob_get_clean();
    }

    public static function get_favorite_list_orders(int $id_user, $offset_count=[0, 20]): array {
        $ans = [];
        $rows = SQL_ROWS_FIELD(q("
            SELECT best_prod.id, 
                   best_prod.shop_id, 
                   best_prod.product_id,
                   best_prod.room_id,
                   best_prod.datatime 
            FROM best_prod WHERE 
                 `user_id`=".$id_user." 
                 ORDER BY best_prod.datatime 
                 DESC LIMIT ".(int)$offset_count[0].",".(int)$offset_count[1]." 
                 "), 'id');
        if(count($rows) > 0) {
            $ans = $rows;
        }
        return $ans;
    }

    public static function get_count_favorite_orders(int $id_user):int {
        return SUBD::countRows('best_prod', 'user_id', $id_user);
    }


    /**
     * @param $arr - массив с товарами
     * @param $user_id - id пользователя
     * @param $name_field_of_ID_SHOP - имя поля для ID магазина в массиве которое нужно обрабатывать
     * @param $name_field_of_ID_PRODUCT - имя поля ID товара в массиве которое нужно обрабатывать
     */
    public static function verify_like_products($arr, $user_id, $name_field_of_ID_SHOP, $name_field_of_ID_PRODUCT) {
        $finders = [];
        foreach($arr as $k=>$v) {
            if(isset($v[$name_field_of_ID_SHOP], $v[$name_field_of_ID_PRODUCT])) {
                $finders[] = "(
                SELECT * FROM `best_prod` WHERE
                `user_id`=".(int)$user_id." AND 
                `shop_id` = ".(int)$v[$name_field_of_ID_SHOP]." AND
                `product_id` = ".(int)$v[$name_field_of_ID_PRODUCT]."
                )";
            }
            $arr[$k]['LIKE'] = 0;
        }
        if(count($finders) > 0) {
            $rows = SQL_ROWS_FIELD(q(implode(' UNION ', $finders)), 'id');
            foreach($rows as $k=>$v) {
                $rows[$v['shop_id']."_".$v['product_id']] = $v;
                unset($rows[$k]);
            }
            foreach($arr as $k=>$v) {
                if(isset($rows[$v[$name_field_of_ID_SHOP]."_".$v[$name_field_of_ID_PRODUCT]])) {
                    $arr[$k]['LIKE'] = 1;
                } else {
                    $arr[$k]['LIKE'] = 0;
                }
            }
        }
        return $arr;
    }
}