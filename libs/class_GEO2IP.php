<?phpclass GEO2IP {    public static function get_info($IP): bool|array {        include_once './RESURSES/SxGeo.php';        if(!isset($SxGeo)) {            $SxGeo = new SxGeo('./RESURSES/SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY);        }        return $SxGeo->getCityFull($IP);    }    public static function IP2STRING($ip): string {        $arr = self::get_info($ip);        $country = $arr['country']['name_ru'] ?? ' ';        $region = $arr['region']['name_ru'] ?? ' ';        $city = $arr['city']['name_ru'] ?? '';        if($city === $region) {            $ans = $country.", ".$city;        } else {            $ans = $country.", ".$region.", ".$city;        }        return $ans;    }}