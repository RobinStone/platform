<?phpclass Core {    static array $INIT = [];    static string $DOMAIN = 'https://rumbra.ru/';    static string $SiteName = 'RUMBRA';    static string $DB_NAME = 'renigate_db';    static string $DB_LOGIN = 'ren-robin';    static string $DB_PASS = 'rL0mY8qH1m';    static string $DB_LOCAL = 'localhost';    static string $DOWNLOAD = './DOWNLOAD/';    static string $CRYPTER_SALT_1 = '34gh923ibt94egkshksd';    static string $CRYPTER_SALT_2 = 'df9gmdfyder8fg47bl3';    static string $CANONICAL = '';    static array $META = [];    static array $CSS = [];    static array $JS = [];    static array $LINKS_JS = [];    static array $LINKS_START_JS = [];    static string $LANG = 'ru';    static string $REDISAUTH = 'OuH8nA360b9PHpNxm3e86gvj3Psw8WlO';    static string $YANDEXGEOCODER = '';    static string $SUGGEST_GEOCODER = '';    static string $SESSIONCODE = '';    // SEO //    static string $title = '';    static string $description = '';    static string $keywords = '';    static string $h1 = '';    static string $TYPE_PAGE = '';    static array $meta_local = [            'title'=>'global',            'description'=>'global',            'keywords'=>'global',            'h1'=>'global',        ];    static string $META_TYPE = '';    // SEO //    static int $showed_rows = 50;    static string $OWNER_MAIL_LOGIN = 'regigate@renigate.site';  // Ваш логин от почты с которой будут отправляться письма    static string $OWNER_PASS_MAIL = 'hx$z1g_4jzFDUPFI';   // Ваш пароль от почты с которой будут отправляться письма    static string $OWNER_MAIL = 'regigate@renigate.site';        // от кого будет уходить письмо?   //  hx$z1g_4jzFDUPFI    static array $ADMIN = [];    static string $DT = "";}