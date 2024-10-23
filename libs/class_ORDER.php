<?php

use JetBrains\PhpStorm\ArrayShape;

class ORDER {
    private array $order = [];
    public int $id = -1;

    public function __construct(int $order_id)
    {
        if($row = SQL_ONE_ROW(q("SELECT * FROM orders WHERE id=".$order_id))) {
            $row['order_code'] = unserialize($row['order_code']);
            $this->order = $row;
            $this->id = (int)$row['id'];
        } else {
            return false;
        }
    }

    public function change_descr(string $txt="", bool $auto_save=true) {
        $this->order['descr_order'] = $txt;
        if($auto_save) $this->save();
    }

    /**
     * Позволяет пересчитать общую стоимость заказа
     *
     * @param bool $products - пересчитывать ли актуальные цены товаров и их скидки
     * @param bool $delivery - пересчитывать ли доставку
     * @param bool $change_order_in_db - записывать ли результат в БД
     * @return array
     */
    #[ArrayShape(['errors' => "array|mixed", 'arr' => "mixed"])] public function recalculate(
        bool $products=true, bool $delivery=true, bool $change_order_in_db=true): array
    {
        $errors = [];
        INCLUDE_CLASS('shops', 'buy');
        INCLUDE_CLASS('shops', 'shop');
        INCLUDE_CLASS('shops', 'props_commander');
        $arr = $this->order['order_code'];
        $arr['descr_order'] = $this->get_field_descr();
        $arr['closed'] = (int)$this->get_field_closed();
        $arr['id_user_creator'] = $this->get_field_user_creator_id();
        $arr['status'] = $this->get_field_status();
        $arr['type_pay'] = $this->get_field_type_pay();
        $arr['type_delivery'] = '-';
        $arr['shop_id'] = $this->get_field_shop_id();
        $arr['total_summ'] = $this->get_field_total_summ();
        $arr['delivery_summ'] = $this->order['order_code']['delivery_info']['delivery_price'] ?? 0;

        if(isset($arr['delivery_info']['cdek_point_from']) && !empty($arr['delivery_info']['cdek_point_from'])) {
            $arr['type_delivery'] = 'CDEK';
            if($delivery) {
                if(isset($arr['delivery_info']['cdek_city_points'])) {
                    if(isset($arr['delivery_info']['cdek_city_points']['cdek_city_code_from']) &&
                        isset($arr['delivery_info']['cdek_city_points']['cdek_city_code_to'])) {
                        $from = (int)$arr['delivery_info']['cdek_city_points']['cdek_city_code_from'];
                        $to = (int)$arr['delivery_info']['cdek_city_points']['cdek_city_code_to'];
                        $arr['delivery_summ'] = CDEK2::get_tarif_price_from_cities($from, $to, 500);
                    }
                }
            }
        }

        if($products) {
            $ans = BUY::preparation($arr, true);
            $errors = $ans['errors'];
            $arr = $ans['arr'];
        }

        if($change_order_in_db) {
            $this->set_param('delivery_info.delivery_price', $arr['delivery_summ'], false);
            $this->change_total_summ($arr['total_summ'], false);
            $this->save();
        }

        return ['errors'=>$errors, 'arr'=>$arr];
    }

    public function change_total_summ(float $summ, bool $auto_save=true) {
        $this->order['total_summ'] = $summ;
        if($auto_save) $this->save();
    }

    public function change_status(ORDER_STATUS $status, bool $auto_save=true) {
        $this->order['status'] = $status->value;
        if($status->name === 'CLOSED' || $status->name === 'ABORTED') {
            $this->order['closed'] = 1;
            $this->order['data_closed'] = date('Y-m-d H:i:s');
        } else {
            $this->order['closed'] = 0;
        }
        if($auto_save) $this->save();
    }

    public static function get_order(int $order_id): ORDER
    {
        return new ORDER($order_id);
    }

    public function save() {
        if(!empty($this->order)) {
            $this->order['order_code'] = serialize($this->order['order_code']);
            q("
            UPDATE orders SET 
            `descr_order`           = '".db_secur($this->get_field_descr())."',
            `closed`                = ".(int)$this->get_field_closed().",
            `id_user`               = ".(int)$this->get_field_user_creator_id().",
            `status`                = '".db_secur($this->get_field_status())."',
            `type_pay`              = '".db_secur($this->get_field_type_pay())."',
            `data_closed`           = '".db_secur($this->order['data_closed'])."',
            `total_summ`            = ".(float)$this->get_field_total_summ().",
            `order_code`            = '".db_secur($this->order['order_code'])."'
            WHERE id=".$this->id."
            ");
        }
    }

    /**
     * Вернёт искомый параметр, в виде строки или массива или числа
     *
     * @param string $attachment_param - вид переменной напр. (system.users.name)
     * @param $if_not_exist
     * @return bool|array|string
     */
    public function get_param(string $attachment_param, $if_not_exist=''): bool|array|string
    {
        if(!empty($this->order['order_code'])) {
            return get_attachment_value($this->order['order_code'], $attachment_param, $if_not_exist);
        }
        return false;
    }

    /**
     * Добавит параметр в заказ по пути $attachment
     *
     * @param string $attachment - вид переменной напр. (system.users.name)
     * @param $argum
     * @param bool $auto_save - по умолчанию (true) автозапись в БД происходит сразу после изменения параметра
     * @return void
     */
    public function set_param(string $attachment, $argum, bool $auto_save=true) {
        $this->order['order_code'] = set_attachment_value($this->order['order_code'], $attachment, $argum);
        if($auto_save) $this->save();
    }

    /**
     * Удаляет параметр из заказа по пути $attachment
     *
     * @param string $attachment
     * @param bool $auto_save
     * @return void
     */
    public function del_param(string $attachment, bool $auto_save=true) {
        delete_attachment_key($this->order, $attachment);
        if($auto_save) $this->save();
    }

    /**
     * Проверяет принодлежность данного заказа к продавцу или покупателю
     * в случае если введённый $owner_or_recipient_id присутствует в этом заказе
     * метод вернёт - true, иначе - false
     *
     * @param int $owner_or_recipient_id
     * @return bool
     */
    public function is_my_order(int $owner_or_recipient_id=-1): bool
    {
        if($owner_or_recipient_id === -1) {
            $owner_or_recipient_id = SITE::$user_id;
        }
        if($owner_or_recipient_id === -1) {
            return false;
        }
        if($owner_or_recipient_id === SITE::$user_id) {
            return true;
        }
        INCLUDE_CLASS('shops', 'shop');
        if(SHOP::get_owner_of_shop($this->get_field_shop_id()) === $owner_or_recipient_id) {
            return true;
        }
        return false;
    }

    public function get_field_descr():string|bool {
        if(!empty($this->order['descr_order'])) {
            return $this->order['descr_order'];
        }
        return false;
    }
    public function get_field_closed():int|bool {
        if(!empty($this->order['closed'])) {
            return (int)$this->order['closed'];
        }
        return false;
    }
    public function get_field_user_creator_id():int|bool {
        if(!empty($this->order['id_user'])) {
            return (int)$this->order['id_user'];
        }
        return false;
    }
    public function get_field_status():string|bool {
        if(!empty($this->order['status'])) {
            return $this->order['status'];
        }
        return false;
    }
    public function get_field_type_pay():string|bool {
        if(!empty($this->order['type_pay'])) {
            return $this->order['type_pay'];
        }
        return false;
    }
    public function get_field_data_created():string|bool {
        if(!empty($this->order['datatime'])) {
            return $this->order['datatime'];
        }
        return false;
    }
    public function get_field_data_closed():string|bool {
        if(!empty($this->order['data_closed'])) {
            return $this->order['data_closed'];
        }
        return false;
    }
    public function get_field_shop_id():int|bool {
        if(!empty($this->order['shop_id'])) {
            return (int)$this->order['shop_id'];
        }
        return false;
    }
    public function get_field_total_summ():int|bool {
        if(!empty($this->order['total_summ'])) {
            return (int)$this->order['total_summ'];
        }
        return false;
    }
    public function get_all_params():array|bool {
        if(!empty($this->order['order_code'])) {
            return $this->order['order_code'];
        }
        return false;
    }

}