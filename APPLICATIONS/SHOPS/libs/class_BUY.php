<?php
use JetBrains\PhpStorm\ArrayShape;

class BUY {
    public static function execute_buy_from_cash($arr): array
    {
        $result = self::preparation($arr);
        if(count($result['errors']) === 0) {
            $arr = $result['arr'];
            $arr['payed'] = $arr['total_summ'];
            if (count($arr['products']) > 0) {

                // Проверяем всё по оплатам СДЭК-а
                if(isset($arr['obtaining']) && $arr['obtaining'] === 'places') {
                    if(isset($arr['cdek_city_points']) &&
                        !empty($arr['cdek_city_points']['cdek_city_code_from']) &&
                        !empty($arr['cdek_city_points']['cdek_city_code_to'])) {

                        if($delivery_price = CDEK2::get_tarif_price_from_cities(
                            $arr['cdek_city_points']['cdek_city_code_from'],
                            $arr['cdek_city_points']['cdek_city_code_to'],
                            500
                        )) {
                            // тут добавляем цену доставки по СДЭК, если всё хорошо
                            (float)$arr['payed'] += $delivery_price;
                            $arr['delivery_price'] = (int)$delivery_price;
                        } else {
                            error('Ошибка расчёта цены доставки');
                        }

                    } else {
                        error('Ошибка. Не коректно указаны точки отправки и доставки посылки');
                    }
                }

                if(PAY::is_payed_correct_summ(Access::userID(), (float)$arr['payed'])) {
                    $codes_prods = [];
                    foreach($arr['products'] as $v) {
                        $codes_prods[$arr['shop_id']."_".$v['product_id']] = 0;
                    }
                    $id = self::create_end_order_pay($arr);
                    self::set_status_order($id, 'оплачен');
                    PAY::buy(Access::userID(), (float)$arr['payed'], 'Покупка товара', 'Кошелёк', self::create_description_order($arr)."\r\nНомер операции: ".$id);
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

    /**
     * @param $arr
     * @param bool $dont_see_at_count - функция будет игнорировать, если товаров уже нет === 0
     * @return array
     */
    #[ArrayShape(['errors' => "array", 'arr' => "array"])]
    public static function preparation($arr, bool $dont_see_at_count=false): array
    {
        $errors = [];
        $ids = array_column($arr['products'], 'product_id');
        $rows = SHOP::get_products_list_at_id((int)$arr['shop_id'], $ids, true);
        $ids = array_column($rows, 'id_product');
        $rows = array_combine($ids, $rows);

        $total_summ = 0;

        foreach($arr['products'] as $k=>$v) {
            if(isset($rows[$v['product_id']])) {
                $obj = $rows[$v['product_id']];
                $count_all = (int)$obj['count'];
                if($count_all === -1 || $count_all >= (int)$v['count'] || $dont_see_at_count) {
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
                    $img = PROPS_COMMANDER::get_prop($shop_id, $prod_id, 'Изображение');
                    $price = round((float)$obj['price'], 2);
                    $v['price'] = $price;
                    $v['img'] = $img;
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

        $total_complite_summ = (float)$arr['payed'];

        $order_code = [
            'products'=>$arr['products'],
            'delivery_info'=>[
                'promocode'=>$arr['promocode'] ?? '-',
                'type_pay'=>$arr['type_pay'] ?? '-',
                'city'=>$arr['city'] ?? '-',
                'type_obtaining'=>$arr['obtaining'] ?? '-',
                'address_of_delivery'=>$arr['address'] ?? '-',
                'comment'=>$arr['comment'] ?? '-',
                'total_summ'=>$arr['total_summ'] ?? '-',
                'fio'=>$arr['fio'] ?? '-',
                'phone'=>$arr['phone'] ?? '-',
            ]
        ];

        if(isset($arr['cdek_city_points'])) {
            $order_code['delivery_info']['cdek_city_points'] = $arr['cdek_city_points'];
            $order_code['delivery_info']['cdek_point_to'] = SITE::$profil->get_attachment('delivery_info.cdek');
            PROFIL::get_profil(
                SHOP::get_owner_of_shop((int)$arr['shop_id']))->add_alert(ALERT_TYPE::ATTANTION,
                ['text'=>'Поступил новый заказ, пожалуйста уточните точку отправки посылки.', 'link'=>'/profil?title=shop_orders'],
                'new_order'
            );
            $order_code['delivery_info']['cdek_point_from'] = [];
            $order_code['delivery_info']['delivery_price'] = $arr['delivery_price'];
        }

        q("
        INSERT INTO orders SET
        descr_order='".db_secur(self::create_description_order($arr))."',
        id_user=".Access::userID().",
        status='создан',
        type_pay='".$arr['type_pay']."',
        datatime='".date('Y-m-d H:i:s')."',
        shop_id=".(int)$arr['shop_id'].",
        total_summ=".(float)$arr['total_summ'].",
        payed=".$total_complite_summ.",
        order_code='".serialize($order_code)."'
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