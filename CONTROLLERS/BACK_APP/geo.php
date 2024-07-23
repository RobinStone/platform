<?php
switch($_POST['com']) {
    case 'test':
        $err = isset_columns($_POST, ['user_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'correct_geo_set':
        $err = isset_columns($_POST, ['city_name']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $id_city = GEONAMER::city_name_to_id($post['city_name']);
        if($id_city !== -1) {
            $row = SQL_ONE_ROW(q("SELECT * FROM city WHERE id=".$id_city." LIMIT 1"));
            $city_name = $row['name'];
            $country_name = GEONAMER::country_id_to_name($row['id_country']);
        } else {
            $city_name = SITE::$IP_region_fool['city']['name_ru'];
            $country_name = SITE::$IP_region_fool['country']['name_ru'];
            $my_place = SITE::$IP_region_fool['city']['name_ru'] . "|" . mb_strtolower(SITE::$IP_region_fool['city']['name_en']);
            setcookie('my_place', $my_place, time() + 31556926, '/');
        }
        if(Access::userID() > 0) {
            $P = PROFIL::init(Access::userID());
            $P->set('city', $city_name, false);
            $P->set('country', $country_name);
        }
        ans('ok', ['city_name'=>$city_name]);
        break;


}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
