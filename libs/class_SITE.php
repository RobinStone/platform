<?phpclass SITE {    public static ?PROFIL $profil = null;    public static array $my_place = ['', ''];    public static array $params = [];    public static int $user_id = -1;    public static string $dt = '0000-00-00 00:00:00';    public static string $ip = '000.000.000.000';    public static string $place = '-';    public static string $base_dir = '/';    public static string $personal = '-';    public static string $JS = "";    public static array $GEO = [0, 0];    public static function localization($rus=false): string    {        if(self::$my_place[1] === '') {            return 'all';        }        if($rus) {            return mb_strtolower(self::$my_place[0]);        }        return mb_strtolower(self::$my_place[1]);    }    public static function set($param, $argum) {        self::$params[$param] = $argum;    }    public static function isset_param($param): bool    {        if(isset(self::$params[$param])) {            return true;        }        return false;    }    public static function get($param, $if_not_isset='') {        if(isset(self::$params[$param])) {            return self::$params[$param];        } else {            if($if_not_isset !== '') {                return $if_not_isset;            } else {                return null;            }        }    }}