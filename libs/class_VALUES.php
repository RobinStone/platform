<?phpclass VALUES {    static function dateToFormat($datetime, $with_time=true): string    {        $d = strtotime($datetime);        $ans = (int)date('d', $d).' '.(VALUES::intToMonth(date('m', $d), true).', '.date('Y', $d));        if($with_time) {            $ans .= ' в '.date('H:i', $d);        }        return $ans;    }    static function intToMonth($numberMonth, $pad=false): string {        $num = (int)$numberMonth;        if($num < 1 || $num > 12) {            $num = 0;        }        if($pad === true) {            $months = ['', 'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];        } else {            $months = Array(0=>'-', 1=>'Январь', 2=>'Февраль', 3=>'Март', 4=>'Апрель', 5=>'Май', 6=>'Июнь', 7=>'Июль', 8=>'Август', 9=>'Сентябрь', 10=>'Октябрь', 11=>'Ноябрь', 12=>'Декабрь');        }        return $months[$num];    }    static function array_to_int(array $arr): array    {        return array_map('intval', $arr);    }    static function monthToInt($nameMonth) {        $nameMonth = mb_strtolower($nameMonth);        $months = Array('январь'=>1, 'февраль'=>2, 'март'=>3, 'апрель'=>4, 'май'=>5, 'июнь'=>6, 'июль'=>7, 'август'=>8, 'сентябрь'=>9, 'октябрь'=>10, 'ноябрь'=>11, 'декабрь'=>12);        return $months[$nameMonth];    }    static function resetDate($errorDate) {        $dt = explode(' ', $errorDate);        $cor = explode('-', $dt[0]);        $cor[2] = (int)$cor[2];        if(strlen($cor[2]) < 2) { $cor[2] = '0'.$cor[2]; }        $dt = implode('-', $cor).' '.$dt[1];        return ($dt);    }    static function decodePhone($phoneOld) {        $tel = preg_replace('~\D+~','', $phoneOld);        return($tel);    }    static function price_format($price, $zero_count=2): string    {        return number_format($price, (int)$zero_count, '.', ' ');    }    static function dateToDayOfWeek($YYYYmmdd) {        $dat = new DateTime($YYYYmmdd);        $dw = (int)date('w', strtotime($YYYYmmdd));        $arr = [0=>'Воскресение', 1=>'Понедельник', 2=>'Вторник', 3=>'Среда', 4=>'Четверг', 5=>'Пятница', 6=>'Суббота'];        return $arr[$dw];    }    static function howMuchDaysInMonth($YYYYmm) {        $dat = new DateTime($YYYYmm);        $numbers = cal_days_in_month(CAL_GREGORIAN, (int)$dat->format('m'), (int)$dat->format('Y'));        return $numbers;    }    static function zero_plus($val, $need_length = 0) {        while(mb_strlen($val) < $need_length) {            $val = '0'.$val;        }        return $val;    }    static function translit($s) {        $s = (string) $s; // преобразуем в строковое значение        $s = strip_tags($s); // убираем HTML-теги        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы        $s = trim($s); // убираем пробелы в начале и конце строки        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус        return $s; // возвращаем результат    }    static function setTitle($val) {        $flag = false;        foreach(Core::$META as $k=>$itm) {            if(preg_match('/<title>/', $itm)) {                $flag = true;                Core::$META[$k] = '<title>'.out_secur($val).'</title>';            }        }        if($flag == false) {            Core::$META[] = '<title>'.out_secur($val).'</title>';        }    }    static function setDescription($val) {        $flag = false;        foreach(Core::$META as $k=>$itm) {            if(preg_match('/<meta name="description"/', $itm)) {                $flag = true;                Core::$META[$k] = '<meta name="description" content="'.out_secur($val).'"/>';            }        }        if($flag == false) {            Core::$META[] = '<meta name="description" content="'.out_secur($val).'"/>';        }    }    static function setKeywords($val) {        $flag = false;        foreach(Core::$META as $k=>$itm) {            if(preg_match('/<meta name="keywords" content="/', $itm)) {                $flag = true;                Core::$META[$k] = '<meta name="keywords" content="'.out_secur($val).'"/>';            }        }        if($flag == false) {            Core::$META[] = '<meta name="keywords" content="'.out_secur($val).'"/>';        }    }    static function setParam($param, $argum) {        if(Access::root(1)) {            $val = $_SESSION['user']['params'];            $val = self::addParamToString($val, $param, $argum);            $_SESSION['user']['params'] = $val;            q("            UPDATE `users` SET            `params`         = '".db_secur($val)."'            WHERE `id`       = ".(int)Access::userID()."            ");        } else {//            Message::addError('Error... not logining set to change old param');            return false;        }    }    static function remooveParam($param) {        if(Access::root(1)) {            $val = $_SESSION['user']['params'];            $val = self::remooveParamFromString($val, $param);            $_SESSION['user']['params'] = $val;            q("            UPDATE `users` SET            `params`         = '".db_secur($val)."'            WHERE `id`       = ".(int)Access::userID()."            ");        } else {//            Message::addError('Error... not logining set to change old param');            return false;        }    }    static function getParam($param) {        if(Access::root(1)) {            $val = $_SESSION['user']['params'];            return self::getParamFromString($val, $param);        } else {//            Message::addError('Error... not logining set to get Old');            return false;        }    }    static function addParamToString($allString, $param, $argum) {        $lst = explode('|', $allString);        $yes = false;        foreach($lst as $k=>$itm) {            if(explode('=',$itm)[0].'=' == $param.'=') {                $yes = true;                $lst[$k] = $param.'='.$argum;            }        }        if($yes == false) {            $lst[] = $param.'='.$argum;        }        return implode('|', $lst);    }    static function remooveParamFromString($allString, $param) {        $lst = explode('|', $allString);        foreach($lst as $k=>$itm) {            if(explode('=',$itm)[0].'=' == $param.'=') {                unset($lst[$k]);            }        }        return implode('|', $lst);    }    static function getParamFromString($allString, $param, $if_not_exists=''): string {        $lst2 = explode('|', $allString);        foreach($lst2 as $k=>$itm) {            if(explode('=',$itm)[0].'=' == $param.'=') {                return explode('=',$itm)[1];            }        }        if($if_not_exists !== '') {            return $if_not_exists;        }        return '';    }    /**     * @throws Exception     */    static function add_param_to_DB($table_name, $column_name, $id, $param, $argum) {        $types = Access::get_type_field($table_name, $column_name)['com'];        if($types === 'params') {            $row = SUBD::getLineDB($table_name, 'id', $id);            if(is_array($row)) {                $all = $row[$column_name];                $all = VALUES::addParamToString($all, $param, $argum);                q("UPDATE `".db_secur($table_name)."` SET `".db_secur($column_name)."` = '".db_secur($all)."' WHERE `id` = ".(int)$id." ");                return true;            } else {                return false;            }        } else {            throw new Exception('Поле таблицы ['.$table_name.'] в которое вы хотите добавить ПАРАМЕТР имеет ['.$types.'], а не [params]');        }    }    /**     * @throws Exception     */    static function get_param_from_DB($table_name, $column_name, $id, $param) {        $types = Access::get_type_field($table_name, $column_name)['com'];        if($types === 'params') {            $row = SUBD::getLineDB($table_name, 'id', $id);            if(is_array($row)) {                $all = $row[$column_name];                return VALUES::getParamFromString($all, $param);            } else {                return '';            }        } else {            throw new Exception('Поле таблицы ['.$table_name.'] в которое вы хотите добавить ПАРАМЕТР имеет ['.$types.'], а не [params]');        }    }    static function plus_days($count): string {        $date = date('Y-m-d H:i:s');        return date('Y-m-d H:i:s', strtotime($date . ' +'.(int)$count.' days'));    }    static function minus_days($count): string {        $date = date('Y-m-d H:i:s');        return date('Y-m-d H:i:s', strtotime($date . ' -'.(int)$count.' days'));    }    static function plus_hours($count): string {        $date = date('Y-m-d H:i:s');        return date('Y-m-d H:i:s', strtotime($date . ' +'.(int)$count.' hours'));    }    static function plus_minutes($count): string {        $date = date('Y-m-d H:i:s');        return date('Y-m-d H:i:s', strtotime($date . ' +'.(int)$count.' minutes'));    }    static function plus_seconds($count): string {        $date = date('Y-m-d H:i:s');        return date('Y-m-d H:i:s', strtotime($date . ' +'.(int)$count.' seconds'));    }    static function extracting($all_text) {        preg_match('/\((.*?)\)/', $all_text, $matches);        return $matches[1] ?? '';    }    /**     * @throws Exception     */    static function days_between($YYYYMMDDHHMMSS_1, $YYYYMMDDHHMMSS_2): bool|int    {        $date1 = new DateTime($YYYYMMDDHHMMSS_1);        $date2 = new DateTime($YYYYMMDDHHMMSS_2);        // Вычисляем интервал между датами        $interval = $date1->diff($date2);        // Получаем количество дней        return $interval->days;    }    /**     * Переименовывает главные ключи массива, используя данные у указанного item     */    static function rename_array_keys_from_item_field($arr, $new_key_name, $random_count=-1): array {        $ans = [];        $errors = [];        $i = 0;        foreach($arr as $k=>$v) {            if(isset($v[$new_key_name])) {                $ans[$v[$new_key_name]] = $v;                if($random_count !== -1) {                    ++$i;                    if($i > $random_count) {                        break;                    }                }            } else {                $errors[] = $v;            }        }        if(count($errors) > 0) {            say($errors);            Message::addError('При работе метода "rename_array_keys_from_item_field" возникли ошибки, которые могли привести, что несколько элементов не попали в конечную выборку. Элементы, не попавшие туда, были помещены в say.txt');        }        return $ans;    }    /**     * @param $array     * @param $find_str_in_key     * @return array     * Метод, который проверяет вхождение подстроки в ключах переданного массива.     * Если совпадения нет - система элемент массива удаляется из списка.     */    static function exist_in_array_keys($array, $find_str_in_key):array {        $find_str_in_key = mb_strtolower($find_str_in_key);        foreach($array as $k=>$v) {            if(stripos(mb_strtolower($k), $find_str_in_key) === false) {                unset($array[$k]);            }        }        return $array;    }    /**     * @param $summ     * @param $percents     * @return float     */    public static function minus_percent($summ, $percents): float {        return round((float)$summ - ((int)$percents / 100) * (float)$summ, 2);    }    public static function is_phone(string $str): bool    {        $phone_pattern = '/^(\+\d{1,3}[- ]?)?\(?\d{1,4}\)?[- ]?\d{3}[- ]?\d{2}[- ]?\d{2}$/';        if (preg_match($phone_pattern, $str)) {            return true;        }        return false;    }}