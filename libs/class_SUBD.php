<?phpclass SUBD {    public $subds = array();    public function __construct() {    }    public static function getColumnsName($DBName) {        $names = array();            $ask = q("            SHOW COLUMNS FROM `" . db_secur($DBName) . "`                    ");            if ($ask->num_rows) {                while ($row = $ask->fetch_assoc()) {                    $names[] = $row['Field'];                }            }            return ($names);    }    public static function get_column($table_name, $column_name): array    {        $deny_tables = ['access', 'users'];        if(in_array($table_name, $deny_tables)) {            Message::addError('Запрещена операция с этой таблицей');            return [];        }        return SQL_ROWS(q("SELECT `".db_secur($column_name)."` FROM `".db_secur($table_name)."`"));    }    public static function get_last_row($table_name): bool|array {        if(self::existsTable($table_name)) {            return SQL_ROWS(q("SELECT * FROM `".db_secur($table_name)."` ORDER BY `id` DESC LIMIT 1 "))[0];        }        return false;    }    public static function access($tableName, $typeScan='edit') {        $type = 'accessEdit';        switch($typeScan) {            case('edit'):                $type = 'accessEdit';                break;            case('del'):                $type = 'accessDel';                break;            case('add'):                $type = 'accessAdd';                break;            case('fixed'):                $type = 'fixed';                break;        }        $ask = q("        SELECT * FROM `access` WHERE        `tableName`      = '".db_secur($tableName)."' AND        `columnName`     = '".$type."'        LIMIT 1    ");        if($ask->num_rows) {            $row = $ask->fetch_assoc();            if ($row['access'] == '1') {                return(true);            }        }        return(false);    }    public function get_pages_attributes($module_name) {        if(self::existsTable('pages')) {            foreach($this->subds['pages'] as $v) {                if($v['module'] === $module_name) {                    return $v;                }            }        }        return false;    }    public static function getDistinct($tableName, $column) {        $ask = q("        SELECT DISTINCT ".db_secur($column)." FROM `".db_secur($tableName)."`         ");        $array = [];        if($ask->num_rows) {            while($row = $ask->fetch_assoc()) {                $array[] = $row[$column];            }        }        return $array;    }    public static function getUnique($tableName, $column, $filter_column='') {        $ask = q("        SELECT * FROM `".db_secur($tableName)."`        ");        $array = [];        $arr = [];        if($ask->num_rows) {            while($row = $ask->fetch_assoc()) {                if(!in_array($row[$column], $arr)) {                    $arr[] = $row[$column];                    $array[] = $row;                }            }        }        if($filter_column !== '') {            $arr = [];            foreach($array as $v) {                if(isset($v[$filter_column])) {                    $arr[] = $v[$filter_column];                }            }            $array = $arr;        }        return $array;    }    public static function countRows($tableName, $column_name='', $filter_value=''): int {        $row = 0;        if($column_name !== '' && $filter_value !== '') {            $ask = q("                    SELECT COUNT(*) FROM `".db_secur($tableName)."` WHERE `id` > 0 AND `".db_secur($column_name)."` = '".db_secur($filter_value)."'                    ");        } else {            $ask = q("                    SELECT COUNT(*) FROM `".db_secur($tableName)."` WHERE `id` > 0                    ");        }        if($ask->num_rows) {            $row = $ask->fetch_assoc();        }        return (int)implode('|', $row);    }    public static function get_column_type($table_name, $column_name) {        $ask = q("SELECT `".db_secur($column_name)."` FROM `".db_secur($table_name)."` LIMIT 1");        $ans = mysqli_fetch_field($ask)->type;        switch($ans) {            case 3 : $ans = 'int'; break;            case 253 : $ans = 'varchar'; break;            case 252 : $ans = 'text'; break;            case 1 : $ans = 'tinyint'; break;            case 12 : $ans = 'datetime'; break;            default: break;        }        return $ans;    }    public static function exists_column_in_table($tableName, $columnName) {        $ask = q("        SELECT 1 FROM information_schema.COLUMNS        WHERE TABLE_SCHEMA = '".db_secur(Core::$DB_NAME)."'            AND TABLE_NAME = '".db_secur($tableName)."'            AND COLUMN_NAME = '".db_secur($columnName)."'        ");        if($ask->num_rows) {            return true;        } else {            return false;        }    }    public static function getAllDB($DB_name) {        $DB = new SUBD();        $DB->enabledTable($DB_name);        return($DB->getAll($DB_name));    }    public static function getLineDB($DBname, $DBcolumn, $filter, $marked = false) {        if($marked) {            say('MARK = '.$DBname.' - '.$DBcolumn.' - '.$filter);        }        $DB = new SUBD();        $DB->enabledTable($DBname);        return($DB->getLine($DBname, $DBcolumn, $filter));    }    public static function get_last_id() {        return (int)DB::_()->insert_id;    }    public static function find_lines_at_param($table_name, $column_name, $param_name) {        if(self::existsTable($table_name) && self::exists_column_in_table($table_name, $column_name)) {            $ask = q("SELECT * FROM `".$table_name."` WHERE `".$column_name."` LIKE '%".db_secur($param_name)."=%'  ");            return SQL_ROWS($ask);        } else {            return false;        }    }    public static function find_lines_at_argum($table_name, $column_name, $argum) {        if(self::existsTable($table_name) && self::exists_column_in_table($table_name, $column_name)) {            $ask = q("SELECT * FROM `".$table_name."` WHERE `".$column_name."` LIKE '%=".db_secur($argum)."%'  ");            return SQL_ROWS($ask);        } else {            return false;        }    }    public static function find_lines_at_param_argum($table_name, $column_name, $param, $argum) {        if(self::existsTable($table_name) && self::exists_column_in_table($table_name, $column_name)) {            $ask = q("SELECT * FROM `".$table_name."` WHERE `".$column_name."` LIKE '%".db_secur($param).'='.db_secur($argum)."%'  ");            return SQL_ROWS($ask);        } else {            return false;        }    }    public static function getAllLinesDB($DBname, $DBcolumn, $filter) {        $DB = new SUBD();        $DB->enabledTable($DBname);        return($DB->getAllLines($DBname, $DBcolumn, $filter));    }    public static function existsTable($tableName) {        $ask = q("        SELECT * FROM information_schema.tables WHERE table_name = '".db_secur($tableName)."' LIMIT 1        ");        if($ask->num_rows) {            return true;        } else {            return false;        }    }    public static function insertInto($tableName, $arrayKey2Val) {        if(self::existsTable($tableName)) {            $arr = [];            foreach($arrayKey2Val as $k=>$v) {                $arr[] = "`".db_secur($k)."` = '".db_secur($v)."'";            }            q("            INSERT INTO `".$tableName."` SET ".implode(', ', $arr)."            ");        }    }    public static function update($tableName, $setArray, $whereArray='') {        if(self::existsTable($tableName)) {                $arr = [];                foreach($setArray as $k=>$v) {                    $arr[] = "`".db_secur($k)."` = '".db_secur($v)."'";                    }                $arr2 = [];                if($whereArray != '') {                    foreach($whereArray as $k=>$v) {                        $arr2[] = "`".db_secur($k)."` = '".db_secur($v)."'";                    }                }                q("                UPDATE `".$tableName."` SET ".implode(', ', $arr)." WHERE ".implode(' AND ', $arr2)."                ");            }        }    public static function add_param_in_item($db_name, $param, $argum, $find_filter_column_name='id', $filter='0') {        $row = self::getLineDB($db_name, $find_filter_column_name, $filter);        if(is_array($row)) {// как-нибудь допишу            return true;        } else {            return false;        }    }    public function enabledTable($tableName) {        $ask2 = q("SHOW TABLES LIKE '".db_secur($tableName)."'");        if($ask2->num_rows) {            $ask = q("            SELECT * FROM `" . db_secur($tableName) . "`        ");            $arr = array();            if ($ask->num_rows) {                while ($row = $ask->fetch_assoc()) {                    $arr[] = $row;               }            } else {//            echo ('Table '.$tableName.' is NOT EXISTS...');            }            $this->subds[$tableName] = $arr;        } else {            Message::addError('Указаную таблицу "'.$tableName.'"<br>не удалось найти в текущей БД...');        }    }        public function getAllLines($tableName, $columnName, $filter) {            if(isset($this->subds[$tableName])) {                if(count($this->subds[$tableName]) > 0) {                    if(isset($this->subds[$tableName][0][$columnName])) {                        $ans = Array();                        foreach($this->subds[$tableName] as $itm) {                            if($itm[$columnName] == $filter) {                                $ans[] = $itm;                            }                        }                        return $ans;                    } else {                        return 'Wrong COLUMN NAME parameter...';                    }                } else {                    return 'Not rows in this table!..';                }            }        }       public function DBinArray($DBname) {            if(isset($this->subds[$DBname])) {                if(count($this->subds[$DBname]) > 0) {                    return ($this->subds[$DBname]);                } else {                    return 'Not rows in this table!..';                }            }        }    public function exists($tableName, $column, $param) {        if(isset($this->subds[$tableName], $this->subds[$tableName][0][$column])) {            foreach($this->subds[$tableName] as $item) {                if($item[$column] == $param) {                    return true;                }            }            return false;        }        return false;    }    public function getAll($tableName) {        if(isset($this->subds[$tableName])) {            return $this->subds[$tableName];        } else {            return false;        }    }    public function getLine($tableName, $column, $param) {        if(isset($this->subds[$tableName]) && isset($this->subds[$tableName][0][$column])) {            foreach($this->subds[$tableName] as $item) {                if($item[$column] == $param) {                    return $item;                }            }            return false;        } else {            return false;        }    }    public function getParam($param) {        $val =SUBD::getLineDB('main', 'param', $param);        if(is_array($val)) {            return $val['argum'];        } else {            return false;        }    }    public function setParam($param, $argum) {        $p = $this->getParam($param);        if($p == '-') {            q("             INSERT INTO `main` SET              `param`             = '".db_secur($param)."',              `argum`             = '".db_secur($argum)."',              `paramDescription`  = '-'            ");        } else {            q("            UPDATE `main` SET             `argum`             = '".db_secur($argum)."'            WHERE `param`       = '".db_secur($param)."'            ");        }    }    public function createTableFromDB($DBName, $paginator_num=1, $column_sorted='id', $asc_or_desc='ASC', $enable_script=true, $text_search='', $not_rendering=false, $text_search_all=false) {        if($paginator_num === -1) {            $show_all_rows = true;        } else {            $show_all_rows = false;        }        $show_only_self_files = (int)$this->getParam('show_only_self_files');        $hidden_sys_files = (int)$this->getParam('hidden_sys_files');        if($asc_or_desc !== 'ASC') { $asc_or_desc = 'DESC'; }        if (isset($this->subds[$DBName])) {            $ask = q("            SHOW COLUMNS FROM `" . $DBName . "`        ");            if ($ask->num_rows) {                $columns = [];                while ($row = $ask->fetch_assoc()) {                    $columns[$row['Field']] = [                        'type'=>$row['Type'],                        'default'=>$row['Default'],                        'name'=>'-',                    ];                }                $ask2 = q("                    SELECT * FROM `access` WHERE                    `table_name`   = '".db_secur($DBName)."'                ");                $access = [];                if($ask2->num_rows) {                    while($row = $ask2->fetch_assoc()) {                        $access[$row['column_name']] = $row;                    }                }                $i = 9999;                foreach($columns as $k=>$v) {                    if(isset($access[$k])) {                        $columns[$k]['title'] = $access[$k]['column_title'];                        $columns[$k]['order'] = (int)$access[$k]['ord'];                        $columns[$k]['type_info'] = $access[$k]['type'];                        $columns[$k]['params'] = $access[$k]['params'];                        $columns[$k]['edited'] = $access[$k]['edited'];                        $columns[$k]['showed'] = $access[$k]['showed'];                        $columns[$k]['values'] = [];                        $unser = (string)$access[$k]['params'];                        if(mb_strlen($unser) >= 3) {                            $columns[$k]['default'] = unserialize($unser);                        } else {                            $columns[$k]['default'] = [];                        }                    } else {                        $columns[$k]['title'] = '-';                        $columns[$k]['order'] = ++$i;                        $columns[$k]['type_info'] = '-';                        $columns[$k]['params'] = '-';                        $columns[$k]['edited'] = 1;                        $columns[$k]['showed'] = 1;                        $columns[$k]['values'] = [];                        $columns[$k]['default'] = [];                    }                }                $showed_rows = (int)$this->getParam('showed_rows');                if($showed_rows === 0) { $showed_rows =  Core::$showed_rows; }                $aask = q("SELECT COUNT(*) FROM `".db_secur($DBName)."`");                $count = 0;                if($aask->num_rows) {                    $count = (int)$aask->fetch_assoc()['COUNT(*)'];                }                $columns = SORT::array_sort_by_column($columns, 'order', SORT_ASC);                if($show_all_rows) { $showed_rows = 999999999; }                if($count > 0 && $showed_rows > 0) {                    $paginator = [                        'count_rows' => $count,                        'paginator_num' => (int)$paginator_num,                        'paginator_items' => ceil($count / $showed_rows),                    ];                    if($count > $showed_rows) {                        $paginator_num -=1;                        if($paginator_num < 0) { $paginator_num = 0; }                        $showed_rows = $showed_rows*$paginator_num.", ".$showed_rows;                        if($show_all_rows) { $showed_rows = 999999999; }                    }                    $ord = "ORDER BY `".db_secur($column_sorted)."` ".$asc_or_desc." LIMIT ".$showed_rows." ";                } else {                    $paginator = [];                    $ord = "ORDER BY `".db_secur($column_sorted)."` ".$asc_or_desc." LIMIT ".$showed_rows." ";                }                $where = "";                if($text_search !== '') {                    if($DBName === 'file' && $show_only_self_files === 1) {                        $where = "WHERE `owner`=".Access::userID()." AND `".db_secur($column_sorted)."` LIKE '%".db_secur($text_search)."%'";                    } else {                        if($columns[$column_sorted]['type_info'] === 'select' && $not_rendering === false) {                            $table = $columns[$column_sorted]['default']['table'];                            $field = $columns[$column_sorted]['default']['field'];                            $assk = q("                            SELECT ".db_secur($DBName).".*, ".$table.".".$field." AS s_sort_column FROM `".db_secur($DBName)."`                             LEFT JOIN `".$table."`                             ON ".db_secur($DBName).".".db_secur($column_sorted)." = ".$table.".id                             WHERE ".$table.".".$field." LIKE '%".db_secur($text_search)."%'                             ORDER BY ".$table.".".$field." ".$asc_or_desc." LIMIT ".$showed_rows."                            ");                        } else {                            if($text_search_all === false) {                                $where = "WHERE `".db_secur($column_sorted)."` LIKE '%".db_secur($text_search)."%'";                            } else {                                $where = "WHERE `".db_secur($column_sorted)."` = '".db_secur($text_search)."'";                            }                        }                    }                } else {                    if($hidden_sys_files === 1) {                        $inst = " AND `sys`=1 ";                    } else {                        $inst = "";                    }                    if($DBName === 'file' && $show_only_self_files === 1) {                        $where = "WHERE `owner`=".Access::userID()." ".$inst;                    }                }                if(!isset($assk)) {                    $assk = q("SELECT * FROM `".db_secur($DBName)."` ".$where." ".$ord." ");                    $all = SQL_ROWS($assk);                } else {                    $all = SQL_ROWS($assk);//                    say($all);                    foreach($all as $k2=>$v2) {                        unset($v2['s_sort_column']);                        $all[$k2] = $v2;                    }                }                foreach($all as $v) {                    foreach($v as $k=>$itm) {                        $columns[$k]['values'][] = $itm;                    }                }                if($not_rendering) {                    //////////////////////////////////////////////////////////////////////////////////                    if(count($all) > 0) {                        $arr = $columns;                        foreach ($arr as $k => $v) {                            if ($v['type_info'] === 'select') {                                foreach ($v['values'] as $kkk => $vvv) {                                    $v['values'][$kkk] = (int)$vvv;                                }                                if (SUBD::existsTable($v['default']['table'])) {                                    $ask = q("SELECT " . db_secur($v['default']['table']) . ".id, " . db_secur($v['default']['table']) . "." . db_secur($v['default']['field']) . " FROM `" . db_secur($v['default']['table']) . "` WHERE `id` IN (" . implode(',', $v['values']) . ")");                                    $values_list = SQL_ROWS_FIELD($ask, 'id');                                    $lst = $v['values'];                                    foreach ($lst as $kk => $vv) {                                        if (isset($values_list[$vv])) {                                            $lst[$kk] = [                                                'id' => $vv,                                                'value' => $values_list[$vv]                                            ];                                        }                                    }                                    $arr[$k]['values'] = $lst;                                } else {                                    Message::addError('При формировании одного из поля, оказалось что оно ссылается на несуществующую таблицу "' . $v['default']['table'] . '"');                                }                            }                        }                        $columns = $arr;                    }                    //////////////////////////////////////////////////////////////////////////////////                    return $columns;                }//                if(count($all) < $showed_rows_buff) {//                    $paginator = [];//                }                global $PERM;                $table = render('tableCreator', [                        'table'=>$DBName,                        'title'=>$PERM->get_table_title($DBName),                        'columns'=>$columns,                        'paginator'=>$paginator,                        'target_column'=>$column_sorted,                        'direct'=>$asc_or_desc,                        'search_text'=>$text_search,                        'ico'=>$PERM->get_table_ico($DBName),                        ]);                if($enable_script) {                    $table .= '<script src="/JS/tableEditor.js?'.filemtime("./JS/tableEditor.js").'"></script>';                }                return $table;            }            return false;        } else {            ERRS::add_error('Таблица '.$DBName.' не существует...', 3);            return 'Table '.$DBName.' is not exists...';        }    }    /**     * @param $table_name     * @param $column     * @param $filter     * @param bool $all_filter_text - тут если true то будет искаться полное совпадение а не вхождение     * @return array     */    public static function get_executed_rows($table_name, $column, $filter, bool $all_filter_text=false): array {        $ans = [];        $d = new SUBD();        $d->enabledTable($table_name);        $rows = $d->createTableFromDB($table_name, 1, $column, 'ASC', false, $filter, true, $all_filter_text);//        say($rows);        if(is_array($rows) && count($rows) > 0) {            $count = count($rows['id']['values']);            for($i=0;$i<$count;++$i) {                foreach($rows as $k=>$v) {                    if($v['type_info'] === 'select') {                        if(isset($v['values'][$i]['value'][$v['default']['field']])) {                            $v['values'][$i] = $v['values'][$i]['value'][$v['default']['field']];                        } else {                            $v['values'][$i] = '-';                        }                    } elseif($v['type_info'] === 'tinyint') {                        if((int)$v['values'][$i] === 1) {                            $val = $v['default']['true'];                        } else {                            $val = $v['default']['false'];                        }                        $v['values'][$i] = $val;                    }                    $ans[$rows['id']['values'][$i]][$k] = $v['values'][$i];                }            }        }        return $ans;    }    public static function createTableFromARRAY($array, $paginator_num=1, $column_sorted='id', $asc_or_desc='ASC', $enable_script=true): bool|string {        if($asc_or_desc === 'ASC') {            $sort = 4;        } else {            $sort = 3;        }        $columns = [];        if($column_sorted !== 'id') {            $array = SORT::array_sort_by_column($array, $column_sorted, $sort);        }        foreach($array[0] as $k=>$v) {            $columns[$k] = [                'type'=>'varchar',                'default'=>'',                'name'=>'-',            ];        }        $access = [];        $i = 9999;        foreach($columns as $k=>$v) {            if(isset($access[$k])) {                $columns[$k]['title'] = $access[$k]['column_title'];                $columns[$k]['order'] = (int)$access[$k]['ord'];                $columns[$k]['type_info'] = $access[$k]['type'];                $columns[$k]['params'] = $access[$k]['params'];                $columns[$k]['edited'] = $access[$k]['edited'];                $columns[$k]['showed'] = $access[$k]['showed'];                $columns[$k]['values'] = [];                $columns[$k]['default'] = unserialize($access[$k]['params']);            } else {                $columns[$k]['title'] = '-';                $columns[$k]['order'] = ++$i;                $columns[$k]['type_info'] = '-';                $columns[$k]['params'] = '-';                $columns[$k]['edited'] = 1;                $columns[$k]['showed'] = 1;                $columns[$k]['values'] = [];                $columns[$k]['default'] = [];            }        }        $columns = SORT::array_sort_by_column($columns, 'order');        foreach($array as $v) {            foreach($v as $k=>$itm) {                $columns[$k]['values'][] = $itm;            }        }        $table = render('tableCreator', [                'table'=>'COMPILED',                'columns'=>$columns,                'paginator'=>$paginator_num,                'target_column'=>$column_sorted,                'direct'=>$asc_or_desc,                'search_text'=>''        ]);        if($enable_script) {            $table .= '<script src="/JS/tableEditor.js?'.filemtime("./JS/tableEditor.js").'"></script>';        }        return $table;    }    public static function get_max_id($table_name): bool|int {        $ask = q("SELECT `id` FROM `".db_secur($table_name)."` ORDER BY id DESC LIMIT 1");        if($ask->num_rows) {            return (int)$ask->fetch_assoc()['id'];        } else {            return false;        }    }    private static function change_column_info($table_name, $column_name, $title, $ord, $type, $default): bool {        if(q("        UPDATE `access` SET        `column_title` = '".db_secur($title)."',        `ord` = ".(int)$ord.",        `type` = '".db_secur($type)."',        `params` = '".serialize($default)."'        WHERE `table_name` = '".db_secur($table_name)."' AND `column_name` = '".db_secur($column_name)."'        ")) {            return true;        }        return false;    }    public static function create_column($table_name, $column_name, $default, $title='', $ord=0, $types_column='', $edited=false, $old_column_name='') {        $not_null = " NOT NULL ";        $deff = "";        switch($types_column) {            case 'tinyint':                $types = "TINYINT(1)";                $default_value = "DEFAULT ".(int)$default['value'];                break;            case 'int':                $types = "INT(11)";                $default_value = "DEFAULT ".(int)$default;                break;            case 'double':                $types = "DECIMAL(11,6)";                $default_value = "DEFAULT ".round((float)$default, 6);                break;            case 'varchar':                $types = "VARCHAR(255)";                $default_value = "DEFAULT '".$default."'";                break;            case 'datetime':                $types = "DATETIME";                if($default === '0000-00-00 00:00:00') {                    $default_value = "DEFAULT '0000-00-00 00:00:00'";                } else {                    $default_value = "DEFAULT '".date('Y-m-d H:i:s', strtotime($default))."'";                }                break;            case 'date':                $types = "DATE";                if($default === '0000-00-00 00:00:00') {                    $default_value = "DEFAULT '0000-00-00'";                } else {                    $default_value = "DEFAULT '".date('Y-m-d H:i:s', strtotime($default))."'";                }                break;            case 'time':                $types = "TIME";                if($default === '0000-00-00 00:00:00') {                    $default_value = "DEFAULT '00:00:00'";                } else {                    $default_value = "DEFAULT '".date('H:i:s', strtotime($default))."'";                }                break;            case 'text':                $types = "TEXT";                $default_value = "";                if(is_array($default)) {                    $deff = db_secur($default['value']);                } else {                    $deff = db_secur($default);                }                break;            case 'select':                $types = "INT(11)";                $default_value = "DEFAULT ".(int)$default['value'];                break;//            case 'enum'://                $types = "VARCHAR(255)";//                $default_value = "DEFAULT '".$default['value']."'";//                break;            case 'enum':                $b_enum = [];                foreach($_POST['default']['list'] as $k=>$v) {                    $b_enum[] = "'".$v."'";                }                $types = "ENUM(".implode(',',$b_enum).")";                $default_value = "DEFAULT ".$b_enum[0]." ";                break;            case 'file':                $types = "VARCHAR(255)";                $default_value = "DEFAULT '-'";                break;            default:                $types_column = 'null';                if($title === '') { $title = $column_name; }                $default_value = db_secur($default);                break;        }        if($types_column === 'null') {            return 'Не найден, предложенный к созданию, тип столбца.';        }        $action = "ADD";        if($edited) {            $action = "MODIFY COLUMN";        }        if($old_column_name !== '' && $column_name !== $old_column_name) {            q("ALTER TABLE `".db_secur($table_name)."` CHANGE `".db_secur($old_column_name)."` `".$column_name."` ".$types);        }        if($types === "TEXT" && $default_value === '') {            $not_null = " NULL";        }        if(q("ALTER TABLE `".db_secur($table_name)."` ".$action." `".db_secur($column_name)."` ".$types." ".$not_null." ".$default_value)) {            if($types === 'TEXT' && $deff !== '') {                q("UPDATE `".db_secur($table_name)."` SET `".db_secur($column_name)."` = '".$deff."' WHERE `".db_secur($column_name)."` = '' ");            }            if($types_column === 'enum') {                $lst = $default['list'] ?? [];                foreach($lst as $k=>$v) {                    $lst[$k] = "'".$v."'";                }                if(count($lst) > 0) {//                    $default['value'] = $lst[0];//                    $default_value = "DEFAULT ".$lst[0];//                    q("ALTER TABLE `".db_secur($table_name)."` MODIFY COLUMN `".db_secur($column_name)."` ENUM (".implode(',', $lst).") ".$default_value);                    q("UPDATE `".db_secur($table_name)."` SET `".db_secur($column_name)."` = ".$lst[0]." WHERE `".db_secur($column_name)."` = '' ");                }            }            if(change_column_info($table_name, $column_name, $title, $ord, $types_column, $default)) {                return true;            } else {                return('Не удалось изменить параметры столбца, таблицы Access');            }        } else {            return('Не удалось создать столбец');        }    }    public static function add_row_in_table($table_name, $arr_new_fields=[]) {        if(empty($arr_new_fields)) {        }        if(q("INSERT INTO `".db_secur($table_name)."` () values() ")) {            return true;        } else {            return false;        }    }    public static function delete_arr_rows_from_table($table_name, $arr_of_id) {        if($table_name === 'file') {            $ask = q("SELECT * FROM `file` WHERE `id` IN (".implode(',', $arr_of_id).")");            $del = SQL_ROWS($ask);            foreach($del as $v) {                if(file_exists('./DOWNLOAD/'.$v['sys_name'])) {                    if($v['sys'] == '0') {                        unset($arr_of_id[array_search($v['id'], $arr_of_id)]);                    } else {                        $type = RBS::get_extention($v['sys_name']);                        if (!unlink('./DOWNLOAD/' . $v['sys_name'])) {                            unset($arr_of_id[array_search($v['id'], $arr_of_id)]);                        } else {                            switch ($type) {                                case 'audio':                                    AUDIO::delete($v['sys_name']);                                    break;                                case 'image':                                    unlink('./IMG/img100x100/' . $v['sys_name']);                                    unlink('./IMG/img300x300/' . $v['sys_name']);                                    break;                                case 'video':                                    $nm = explode('.', $v['sys_name']);                                    $name_file = $nm[count($nm) - 2];                                    if (file_exists('./IMG/VIDEO_PREVIEW/' . $name_file . '.jpg')) {                                        unlink('./IMG/VIDEO_PREVIEW/' . $name_file . '.jpg');                                    }                                    break;                                default:                                    break;                            }                        }                    }                }            }        }        if($table_name === 'tables_list') {            $ask = q("SELECT * FROM `tables_list` WHERE `id` IN (".implode(',', $arr_of_id).") ");            $rows = SQL_ROWS_FIELD($ask, 'table_name');            global $PERM;            foreach($rows as $k=>$v) {                if($PERM->get_permission($k, Permiss::TABLE_DEL)) {                    if($k !== '-') {                        q("DROP TABLE `" . db_secur($k) . "`");                        q("DELETE FROM `access` WHERE `table_name` = '".db_secur($k)."'");                    }                } else {                    unset($arr_of_id[array_search($v['id'], $arr_of_id)]);                }            }        }        if($table_name === 'users') {            foreach($arr_of_id as $k=>$v) {                if((int)$v === Access::userID()) {                    $us = SUBD::getLineDB('users', 'id' , (int)$v);                    if(is_array($us)) {                        if(Access::get_access($us['login'], 'self-edit') === false) {                            unset($arr_of_id[$k]);                            Message::addError('Удаление собственного профиля заблокирует вам доступ к системе. Если вы всё-же хотите совершить это действие -<br>нажмике на "КЛЮЧ" в нижней панели.');                        }                    }                }            }        }        if(count($arr_of_id) > 0) {            if(q("DELETE FROM `".db_secur($table_name)."` WHERE `id` IN (".implode(',', $arr_of_id).") ")) {                return true;            }            return false;        }    }    public static function delete_table($table_name): bool {        if(q("DROP TABLE `".db_secur($table_name)."`")) {            q("DELETE FROM `access` WHERE `table_name`='".db_secur($table_name)."'  ");            q("DELETE FROM `tables_list` WHERE `table_name`='".db_secur($table_name)."'  ");            return true;        } else {            return false;        }    }    public static function get_all_tables(): array {        $arr = [];        if(getParam('show_only_sys_tables') == '1') {            $rows = SUBD::getAllDB('tables_list');            foreach($rows as $v) {                $arr[] = $v['table_name'];            }        } else {            $ask = q("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . Core::$DB_NAME . "'");            foreach (SQL_ROWS($ask) as $v) {                if (!in_array($v['TABLE_NAME'], $arr)) {                    $arr[] = $v['TABLE_NAME'];                }            }        }        return $arr;    }    public static function get_enum_from_table($table_name, $column_name): array {        $ask = q("SELECT COLUMN_TYPE from information_schema.columns where table_schema='".Core::$DB_NAME."' and table_name='".db_secur($table_name)."' and COLUMN_NAME='".db_secur($column_name)."'");        if($ask->num_rows) {            $row = $ask->fetch_assoc()['COLUMN_TYPE'];            $arr = [];            preg_match_all("~'(.*?)(?:'|$)|([^']+)~",$row,$arr,PREG_SET_ORDER);            $row = [];            foreach($arr as $k=>$v) {                if(isset($v[1]) && $v[1] == '') {                } else {                    $row[] = $v[1];                }            }            return $row;        }        return [];    }    public static function get_field_from_table($table_name, $column_name, $filter_column, $filter_value, $if_not_exists='') {        $ans = $if_not_exists;        $row = SUBD::getLineDB($table_name, $filter_column, $filter_value);        if(is_array($row)) {            if(isset($row[$column_name])) {                $ans = $row[$column_name];            }        }        return $ans;    }    public static function isset_value($table, $column, $value) {        if($row = SUBD::getLineDB(db_secur($table), db_secur($column), db_secur($value))) {            return $row;        }        return false;    }    public static function set($table, $column, $value, $WHERE=''): bool|int {        if($WHERE === '') {            q("INSERT INTO `".db_secur($table)."` SET `".db_secur($column)."`='".db_secur($value)."' ");            return SUBD::get_last_id();        } else {            q("UPDATE `".db_secur($table)."` SET `".db_secur($column)."`='".db_secur($value)."' WHERE ".db_secur($WHERE)." ");            return true;        }    }    public static function OPTIMIZE_TABLE($table_name) {        if(q("OPTIMIZE TABLE `".db_secur($table_name)."`")) {            return true;        }    }    public static function ANALYZE_TABLE($table_name) {        if(q("ANALYZE TABLE `$table_name`")) {            return true;        }    }}