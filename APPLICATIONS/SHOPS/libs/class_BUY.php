<?php
use JetBrains\PhpStorm\ArrayShape;

class BUY {
    public static function execute_buy_from_cash($arr): array
    {
        $result = self::preparation($arr);
        if(count($result['errors']) === 0) {
            $arr = $result['arr'];
            if (count($arr['products']) > 0) {
                if(PAY::is_payed_correct_summ(Access::userID(), (float)$arr['total_summ'])) {
                    $codes_prods = [];
                    foreach($arr['products'] as $v) {
                        $codes_prods[$arr['shop_id']."_".$v['product_id']] = 0;
                    }
                    $id = self::create_end_order_pay($arr);
                    self::set_status_order($id, 'оплачен');
                    PAY::buy(Access::userID(), (float)$arr['total_summ'], 'Покупка товара', 'Кошелёк', self::create_description_order($arr)."\r\nНомер операции: ".$id);
                    if(Access::scanLevel() < 1) {
                        $basket = $_COOKIE['basket'] ?? '';
                    } else {
                        $basket = $_COOKIE['basket-id-user'] ?? '';
                    }
                    $B = new BASKET($basket);
                    foreach($codes_prods as $k=>$v) {
                        $B->change_count($k, (int)$v, false);
                    }
                    $B->save();
                    return ['errors'=>[]];
                } else {
                    return ['errors' => ['Не достаточно средств на счёте. Пополните счёт или выберете иной способ оплаты']];
                }
            }
            return ['errors' => ['Нет элементов для покупки. Проверьте,  возможно Ваш товар уже кем-то куплен']];
        } else {
            return ['errors'=>$result['errors']];
        }
    }

    #[ArrayShape(['errors' => "array", 'arr' => "array"])] public static function preparation($arr): array
    {
        $errors = [];
//        say($arr);
        $ids = array_column($arr['products'], 'product_id');
//        say($ids);
        $rows = SHOP::get_products_list_at_id((int)$arr['shop_id'], $ids, true);
        $ids = array_column($rows, 'id_product');
        $rows = array_combine($ids, $rows);

//        say($rows);
        $total_summ = 0;
//
//        say($arr['products']);
//        say($rows);

        foreach($arr['products'] as $k=>$v) {
            if(isset($rows[$v['product_id']])) {
                $obj = $rows[$v['product_id']];
                $count_all = (int)$obj['count'];
                if($count_all === -1 || $count_all >= (int)$v['count']) {
                    $v['name'] = $obj['name'];
                    $pages = [];
                    if (isset($obj['main_cat_trans']) && $obj['main_cat_trans'] !== '-' && $obj['main_cat_trans'] !== '') {
                        $pages[] = $obj['main_cat_trans'];
                    }
                    if (isset($obj['under_cat_trans']) && $obj['under_cat_trans'] !== '-' && $obj['under_cat_trans'] !== '') {
                        $pages[] = $obj['under_cat_trans'];
                    }
                    if (isset($obj['action_list_trans']) && $obj['action_list_trans'] !== '-' && $obj['action_list_trans'] !== '') {
                        $pages[] = $obj['action_list_trans'];
                    }
                    $pages[] = VALUES::translit($v['name']);
                    $v['link'] = implode('/', $pages) . "?s=" . $arr['shop_id'] . "&prod=" . $v['product_id'];

                    $shop_id = (int)$arr['shop_id'];
                    $prod_id = (int)$v['product_id'];

                    $discount = (int)PROPS_COMMANDER::get_prop($shop_id, $prod_id, 'Скидка %');
                    $price = round((float)$obj['price'], 2);
                    $v['price'] = $price;
                    $v['discount'] = $discount;
                    if ($discount > 0) {
                        $v['price_with_discount'] = $price - ($price * $discount / 100);
                    } else {
                        $v['price_with_discount'] = $price;
                    }
                    $total_summ += (int)$v['count'] * round((float)$v['price_with_discount'], 2);
                } else {
                    unset($arr['products'][$k]);
                    $errors[] = 'Запрошенное количество превышает существующее';
                }
            } else {
                unset($arr['products'][$k]);
                $errors[] = 'Запрошенный продукт - отсутствует';
            }
            $arr['products'][$k] = $v;
        }
        $arr['total_summ'] = $total_summ;
        return [
            'errors'=>$errors,
            'arr' => $arr,
        ];
    }

    public static function create_end_order_pay($arr): int
    {
        $types_pay = SUBD::get_enum_from_table('orders', 'type_pay');
        if(!in_array($arr['type_pay'], $types_pay)) {
            $arr['type_pay'] = '-';
        }
        start_transaction();
        foreach($arr['products'] as $v) {
            SHOP::add_count_to_product((int)$arr['shop_id'], (int)$v['product_id'], -(int)$v['count']);
        }
        end_transaction();

        q("
        INSERT INTO orders SET
        descr_order='".db_secur(self::create_description_order($arr))."',
        id_user=".Access::userID().",
        status='создан',
        type_pay='".$arr['type_pay']."',
        datatime='".date('Y-m-d H:i:s')."',
        shop_id=".(int)$arr['shop_id'].",
        total_summ=".(float)$arr['total_summ'].",
        order_code='".serialize($arr['products'])."'
        ");
        return SUBD::get_last_id();
    }

    public static function create_description_order($arr): string
    {
        $ans = [];
        foreach($arr['products'] as $v) {
            $ans[] = $v['name'];
        }
        return implode('|', $ans);
    }

    public static function set_status_order(int $id_order, $status='-|создан|оплачен|отправлен|получен'): bool
    {
        $arr = ['-', 'создан', 'оплачен', 'отправлен', 'получен'];
        if(in_array($status, $arr)) {
            q("UPDATE orders SET status='".$status."' WHERE id=".$id_order." LIMIT 1");
            return true;
        }
        return false;
    }
}