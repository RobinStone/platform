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
