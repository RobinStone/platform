<?php
use JetBrains\PhpStorm\ArrayShape;
class GEONAMER {
    #[ArrayShape(['city_id' => "int", 'country_id' => "int"])] public static function get_id_from_city_country_name($city_name, $country_name): array {
        $ans = [
            'city_id'=>-1,
            'country_id'=>-1
        ];
        $rows = SQL_ROWS(q("
        SELECT city.id_city FROM city WHERE LOWER(city.name) = '".db_secur(mb_strtolower($city_name))."'
        "));
        if(count($rows) > 0) {$ans['city_id'] = (int)$rows[0]['id_city']; }
        $rows = SQL_ROWS(q("
        SELECT country.id_country FROM country WHERE LOWER(country.name) = '".db_secur(mb_strtolower($country_name))."'
        "));
        if(count($rows) > 0) { $ans['country_id'] = (int)$rows[0]['id_country']; }
        return $ans;
    }

    public static function id_citys_to_names($id_citys=[]): array {
        $rows = [];
        foreach($id_citys as $k=>$v) {
            $id_citys[$k] = (int)$v;
            if($v <= 0) {
                unset($id_citys[$k]);
            }
        }
        if(count($id_citys) > 0) {
            $rows = SQL_ROWS_FIELD(q("SELECT * FROM city WHERE id_city IN (".implode(',',$id_citys).")"), 'id_city');
        }
        return $rows;
    }

    public static function city_find($name, $region=true, $country=true, $limit=10): array {
        $limit = (int)$limit;
        $ans = [];
        if($region && $country) {
            $ans = SQL_ROWS(q("
                SELECT city.name, region.name AS region, country.name AS country FROM city 
                LEFT JOIN region ON 
                region.id_region = city.id_region
                LEFT JOIN country ON
                country.id_country = city.id_country   
                WHERE LOWER(city.name) LIKE '%" . db_secur($name) . "%' ORDER BY city.name LIMIT ".$limit."
                "));
        } elseif($region) {
            $ans = SQL_ROWS(q("
                SELECT city.name, region.name AS region, FROM city 
                LEFT JOIN region ON 
                region.id_region = city.id_region 
                WHERE LOWER(city.name) LIKE '%" . db_secur($name) . "%' ORDER BY city.name LIMIT ".$limit."
                "));
        } elseif($country) {
            $ans = SQL_ROWS(q("
                SELECT city.name, country.name AS country FROM city 
                LEFT JOIN country ON
                country.id_country = city.id_country   
                WHERE LOWER(city.name) LIKE '%" . db_secur($name) . "%' ORDER BY city.name LIMIT ".$limit."
                "));
        } else {
            $ans = SQL_ROWS(q("
                SELECT city.name AS country FROM city 
                WHERE LOWER(city.name) LIKE '%" . db_secur($name) . "%' ORDER BY city.name LIMIT ".$limit."
                "));
        }
        return $ans;
    }

    public static function get_current_position(): array
    {
        $geo = $_COOKIE['geo'] ?? '-';
        if($geo === '-') {
            $my_place = SITE::$my_place;
            $city = $my_place[0]; // Название населенного пункта
            $row = SUBD::getLineDB('cities', 'name', $city);
            if(!is_array($row)) {
//                t('Геокодер определил - '.$city);
                if($city !== '') {
                    $apiKey = Core::$YANDEXGEOCODER; // Ваш API-ключ Яндекс Геокодера
                    $url = "https://geocode-maps.yandex.ru/1.x/?apikey=$apiKey&format=json&geocode=" . urlencode($city);
                    $response = file_get_contents($url);
                    $data = json_decode($response, true);
                    // Получаем координаты первого найденного объекта
                    $coords = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
                    // Разбиваем координаты на широту и долготу
                    list($longitude, $latitude) = explode(' ', $coords);
                    q("
                INSERT INTO `cities` SET 
                `name` = '" . db_secur($city) . "',
                `shirota` = '" . db_secur($latitude) . "',
                `dolgota` = '" . db_secur($longitude) . "'
                ");
                    setcookie('geo', $latitude . "|" . $longitude, time() + 2592000, '/');
                    $_COOKIE['geo'] = $latitude . "|" . $longitude;
                    return [$latitude, $longitude];
                } else {
//                    t('Ошибка определения места...');
                    return [0, 0];
                }
            } else {
//                t('Из базы - '.$row['name']);
                return [$row['shirota'], $row['dolgota']];
            }
        } else {
//            t('Координаты получены из $_COOKIES');
            return explode('|', $geo);
        }
    }
}