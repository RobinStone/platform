<?phpclass Access {    private array $tables = [];    public static array $exception_tables = [        'price_cities'=>[            'columns'=>['city_id', 'price'],            'min-level'=>6,        ],    ];    public static array $access_table = [        'seo-operator'=>[            'pages',        ],    ];    function __construct() {        $this->tables = SQL_ROWS_FIELD(q("SELECT * FROM `tables_list`"), 'table_name');//        say($this->tables);    }    public static function access_to_table($table_name): bool    {        if(isset(self::$access_table[Access::scanModality()]) && in_array($table_name, self::$access_table[Access::scanModality()])) {            return true;        }        return false;    }    public static function access_exception_table(string $table_name, string $column_name, int $actor_level):bool {        if(isset(self::$exception_tables[$table_name]) &&            in_array($column_name, self::$exception_tables[$table_name]['columns']) && self::$exception_tables[$table_name]['min-level'] <= $actor_level) {            return true;        }        return false;    }    public function get_all_permissions_for($table_name): array {        $ans = [            'column_add' => false,            'column_del' => false,            'column_edit' => false,            'fields_edit' => false,            'row_add' => false,            'row_del' => false,            'table_del' => false,        ];        if(isset($this->tables[$table_name])) {            $ans = [                'column_add' => (int)$this->tables[$table_name]['column_add'],                'column_del' => (int)$this->tables[$table_name]['column_del'],                'column_edit' => (int)$this->tables[$table_name]['column_edit'],                'fields_edit' => (int)$this->tables[$table_name]['fields_edit'],                'row_add' => (int)$this->tables[$table_name]['row_add'],                'row_del' => (int)$this->tables[$table_name]['row_del'],                'table_del' => (int)$this->tables[$table_name]['table_del'],            ];        }        return $ans;    }    /**     * Эта функция проверяет можно ли с таблицой проводить те или иные действия     *     * @param string $table_name Имя таблицы, для которой запрашивается разрешение     * @param string $permission Для задания этой переменной используем класс Permis (на подобие ENUM)     */    public function get_permission(string $table_name, string $permission): bool {        if(isset($this->tables[$table_name])) {            if($this->tables[$table_name][$permission] === '1') { return true; }        }        return false;    }    public function get_table_ico($table_name): string {        if(isset($this->tables[$table_name])) {            $img = $this->tables[$table_name]['ico'];            if(file_exists('./DOWNLOAD/'.$img)) {                return './DOWNLOAD/'.$img;            }        }        return './IMG/SYS/table.svg';    }    public function get_table_title($table_name) {        if(isset($this->tables[$table_name])) {            return $this->tables[$table_name]['title'];        }        return $table_name;    }    static function userName() {        if(isset($_SESSION['user'])) {            return $_SESSION['user']['login'];        } else {            return false;        }    }    static function userID(): int {        if(isset($_SESSION['user'])) {            return (int)$_SESSION['user']['id'];        } else {            return -1;        }    }    static function root($level): bool {        if(isset($_SESSION['user']) && $_SESSION['user']['level'] >= (int)$level) {            return true;        } elseif($level === 0) {            return true;        } else {            return false;        }    }    static function edit($tableName, $column): bool {        $ask = q("        SELECT * FROM `access` WHERE        `tableName`      = '".db_secur($tableName)."' AND        `columnName`     = '".db_secur($column)."'        LIMIT 1    ");        if($ask->num_rows) {            $row = $ask->fetch_assoc();            $access = (int)$row['access'];            $com = $row['com'];            if($access === 1 && $com != 'notvisible') {                return true;            }        }        return false;    }    static function scanLevel(): int {        if(isset($_SESSION['user']['level'])) {            return (int)$_SESSION['user']['level'];        } else {            return 0;        }    }    static function scanModality($id_user='current') {        if($id_user === 'current') {            if(isset($_SESSION['user']['modality'])) {                return $_SESSION['user']['modality'];            } else {                return 'empty';            }        } else {            $row = SUBD::getLineDB('users', 'id', (int)$id_user);            if(is_array($row)) {                return $row['modality'];            } else {                return 'empty';            }        }    }    static function user($userName): bool {        if(isset($_SESSION['user']) && $_SESSION['user']['login'] === $userName) {            return true;        } else {            return false;        }    }    static function scan($level) {        if(isset($_SESSION['user'])) {            if($_SESSION['user']['level'] < (int)$level) {                header('Location: /auth/');                exit();            }        } else {            header('Location: /auth/');            exit();        }}    static function scanIP() {        $client  = @$_SERVER['HTTP_CLIENT_IP'];        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];        $remote  = @$_SERVER['REMOTE_ADDR'];        if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;        elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;        else $ip = $remote;        return $ip;    }    static function scanCountry($ip): bool|string {        return geoip_country_code_by_name($ip);//          return 'OO';    }    static function old($datetime): int {        $diff = strtotime(date('Y-m-d H:i:s')) - strtotime($datetime);        return (int)$diff;    }    /**     * Этот метод позволяет узнать разрешено ли изменение тех или иных полей определённой таблицы     */    static public function access_to_change_column($table_name, $column_name): bool {        $ask = q("SELECT * FROM `access` WHERE `table_name` = '".db_secur($table_name)."' AND `column_name` = '".db_secur($column_name)."' LIMIT 1 ");        if($ask->num_rows) {            $row = $ask->fetch_assoc();            if($row['edited'] == '1') {                return true;            } else {                return false;            }        } else {            return true;        }    }    static public function get_type_field($table_name, $column_name): string {        $ask = q("        SELECT access.type FROM `access` WHERE        `table_name`        = '".db_secur($table_name)."' AND        `column_name`       = '".db_secur($column_name)."' LIMIT 1        ");        if($ask->num_rows) {            $row = $ask->fetch_assoc();            return $row['type'];        } else {            return false;        }    }    public static function set_tele_quest($target_id, $quest_text, $function_name, $seconds_expiration=20, $datas=''): string {        $P = PROFIL::init($target_id);        q("        INSERT INTO `messages` SET        `active`   = 1,        `datatime` = '".VALUES::plus_seconds($seconds_expiration)."',        `actor`    = '".db_secur($function_name)."',        `action`   = 'разрешение',        `target`   = '".db_secur($P->get_field('login'))."',        `params`   = '',        `datas`    = '".db_secur($datas)."'        ");        $last_id = SUBD::get_last_id();        $hash = md5($function_name.'sfs34frkk'.date('H:i:s').$last_id);        $arr = [                'Выдать разрешение'=>'tele_access_'.$last_id,                'ОТМЕНА' =>'abort'        ];        q("UPDATE `messages` SET `params`='".$hash."' WHERE `id`=".$last_id);        TELE::send_at_user_id((int)$target_id, "❓ ".$quest_text, 'btn', $arr);        return $hash;    }    public static function get_tele_answer($hesh): string {        $rows = SQL_ROWS(q("SELECT * FROM `messages` WHERE `params`='".db_secur($hesh)."' LIMIT 1"));        if(count($rows) > 0) {            if($rows[0]['target'] === $hesh) {                switch($rows[0]['actor']) {                    case 'add_pay':                        $target_id = (int)VALUES::getParamFromString($rows[0]['datas'], 'target_id');                        $summ = (int)VALUES::getParamFromString($rows[0]['datas'], 'summ');                        include_once './APPLICATIONS/SHOPS/libs/class_PAY.php';                        PAY::add_cash($target_id, $summ);                        break;                }                return 'ok';            }            if($rows[0]['target'] === 'abort') {                return 'neg';            }            return 'wait';        } else {            return 'neg';        }    }    /**     * ПРОВЕРЯЕТ НАЛИЧИЕ SYS.MESSAGE ТИПА "разрешение"     * @param $target_name - в таблице messages COLUMN = target     * @param $permission_name - в таблице messages COLUMN = params (string)     * @return bool     */    static public function get_access($target_name, $permission_name): bool {        $ask = q("        SELECT * FROM `messages` WHERE         `active` = 1 AND        `datatime` >= '".date('Y-m-d H:i:s')."' AND        `action` = 'разрешение' AND        `target` = '".db_secur($target_name)."' AND        `params` = '".$permission_name."' LIMIT 1        ");        if($ask->num_rows) {            return true;        }        return false;    }    static public function get_event($target_name, $event=ActionsList::AUTH): bool|array {        $ask = q("        SELECT * FROM `messages` WHERE         `active` = 1 AND        `datatime` >= '".date('Y-m-d H:i:s')."' AND        `action` = '".db_secur($event)."' AND        `target` = '".db_secur($target_name)."' LIMIT 1        ");        if($ask->num_rows) {            return $ask->fetch_assoc();        }        return false;    }    static function set_access($target_name, $permission_name, $actor_name='system', $action_time_seconds=60): bool {        if(Access::get_access($target_name, $permission_name) === false) {            q("            INSERT INTO `messages` SET            `active` = 1,            `datatime` = '".VALUES::plus_seconds($action_time_seconds)."',            `actor` = '".db_secur($actor_name)."',            `action` = 'разрешение',            `target` = '".db_secur($target_name)."',            `params` = '".$permission_name."',            `datas`  = '-'               ");            return true;        }        return false;    }    /**     * @param $target_name - на кого нацелено или предназначено сообщение     * @param $actor_name - кто инициирует сообщение     * @param $action - событие     * @param $params - обычно тектовое поле     * @param $seconds_for_active - время действия или активации     * @return bool     */    static function set_system_message($target_name, $actor_name, $action=ActionsList::PERMISSION, $params='', $seconds_for_active=30): bool {        q("        INSERT INTO `messages` SET        `active` = 1,        `datatime` = '".VALUES::plus_seconds($seconds_for_active)."',        `actor` = '".db_secur($actor_name)."',        `action` = '".db_secur($action)."',        `target` = '".db_secur($target_name)."',        `params` = '".db_secur($params)."'        ");        return true;    }    static function delete_system_message(int $id_message): bool    {        q("DELETE FROM `messages` WHERE id=".$id_message);        return true;    }    static function change_system_value(int $id_sys_message, string $column, string $param): bool    {        if(q("UPDATE messages SET `".db_secur($column)."`='".db_secur($param)."' WHERE id=".(int)$id_sys_message)) {            return true;        }        return false;    }    static function get_system_message($actor, $action=ActionsList::PERMISSION): bool|array    {        $rows = SQL_ROWS(q("SELECT * FROM `messages` WHERE `actor`='".db_secur($actor)."' AND `action`='".$action."'"));        if(count($rows) > 0) {            return $rows;        }        return false;    }    static function get_all_sys_messages_for($target_name): array {        $ask = q("        SELECT * FROM `messages` WHERE         `target` = '".db_secur($target_name)."' AND        `active` = 1 AND        `datatime` >= '".date('Y-m-d H:i:s')."' AND        `action` = '".ActionsList::COM_TO_FRONT."'        ");        return SQL_ROWS_FIELD($ask, 'id');    }    static function get_fields_params_for_table($table_name): array {        if(!SUBD::existsTable($table_name)) {            return [];        }        $ans = SQL_ROWS_FIELD(q("SELECT * FROM `access` WHERE `table_name`='".db_secur($table_name)."'"), 'column_name');        foreach($ans as $k=>$v) {            if($v['params'] !== null) {                $unser = $v['params'];                if(mb_strlen($unser) >= 3) {                    $v['params'] = unserialize($unser);                } else {                    $v['params'] = [];                }                $ans[$k] = $v;            }        }        return $ans;    }    static function get_indexes_from_table($table_name): array    {        return SQL_ROWS_FIELD(q("SELECT DISTINCT index_name, GROUP_CONCAT(column_name) AS columns        FROM information_schema.statistics        WHERE table_schema = '".Core::$DB_NAME."' AND table_name = '".db_secur($table_name)."'        GROUP BY index_name"), 'columns');    }}