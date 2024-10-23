<?php
if(Access::scanLevel() < 1) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        $err = isset_columns($_POST, ['']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'set_start_point_cdek':
        $err = isset_columns($_POST, ['point', 'order_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }

        if($O = ORDER::get_order($post['order_id'])) {
            $order_status = $O->get_field_status();
            $access_statuses = [ORDER_STATUS::CREATED->value, ORDER_STATUS::PAYED->value];
            if(!in_array($order_status, $access_statuses)) {
                error('На данном этапе, изменить точку отправки - не представляется возможным!..');
            }

            if($cdek = CDEK2::get_point_at_code($post['point'])) {
                $city_code_to = (int)$O->get_param('delivery_info.cdek_point_to.city_code');
                $arr = [
                    'point'=>$post['point'],
                    'address'=>$cdek['location']['address_full'],
                    'postal_code'=>$cdek['location']['postal_code'],
                    'longitude'=>$cdek['location']['longitude'],
                    'latitude'=>$cdek['location']['latitude'],
                    'city_code'=>$cdek['location']['city_code'],
                    'city'=>$cdek['location']['city'],
                ];
                $O->set_param('delivery_info.cdek_point_from', $arr, false);
                $O->set_param('delivery_info.cdek_city_points.cdek_city_code_from', $cdek['location']['city_code'], false);
                $O->change_status(ORDER_STATUS::CREATED, false);

                $price = CDEK2::get_tarif_price_from_cities($cdek['location']['city_code'], $city_code_to, 500);
                $O->set_param('delivery_info.delivery_price', $price);

                PROFIL::init(SITE::$user_id)->delete_alert(ALERT_TYPE::ATTANTION, 'new_order');
                ans('ok');
            }
        } else {
            error('Не найден запрашиваемый заказ...');
        }
        error('Не удалось создать точку отправления посылки...');
        break;
    case 'set_end_point_cdek':
        $err = isset_columns($_POST, ['point']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        if($cdek = CDEK2::get_point_at_code($post['point'])) {
            $arr = [
                'point'=>$post['point'],
                'address'=>$cdek['location']['address_full'],
                'postal_code'=>$cdek['location']['postal_code'],
                'longitude'=>$cdek['location']['longitude'],
                'latitude'=>$cdek['location']['latitude'],
                'city_code'=>$cdek['location']['city_code'],
                'city'=>$cdek['location']['city'],
            ];
            PROFIL::init(Access::userID())->set_attachment('delivery_info.cdek', $arr);
        }
        ans('ok', $arr);
        break;
    case 'get_points':
        $err = isset_columns($_POST, ['cdek_city_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $is_test = true;
        if($post['is_test'] === 'false') {
            $is_test = false;
        }
        $CD = new CDEK2($is_test);
        ans('ok', $CD->get_offices($post['cdek_city_id']));
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
