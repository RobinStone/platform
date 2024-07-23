<?phpclass SORT {    static function array_sort_by_column($array, $col, $dir=SORT_ASC) {        $sort_col = array();        foreach ($array as $key=> $row) {            $sort_col[$key] = $row[$col];        }        array_multisort($sort_col, $dir, $array);//        ob_start();//        wtf($array, 1);//        $cont = ob_get_contents();//        ob_end_clean();//        Mail::send($cont);        reset($array);        return $array;    }    static function customMultiSort($array,$field, $desc='ASC') {        if(is_array($array) && count($array) > 1) {            $sortArr = array();            foreach ($array as $key => $val) {                $sortArr[$key] = $val[$field];            }            array_multisort($sortArr, $array);            if($desc === 'DESC') {                $array = array_reverse($array, true);            }        }        reset($array);        return $array;    }    public static function find_in_array_recursive($array, $searchValue): int|string|null {        foreach ($array as $key => $value) {            if (is_array($value)) {                $result = self::find_in_array_recursive($value, $searchValue);                if ($result !== null) {                    return $key;                }            } elseif ($value === $searchValue) {                return $key;            }        }        return null;    }    /**     * Метод меняет первичный ключ [$new_name_for_old_keys] массива на имя указанное в $new_name_for_old_keys     * поле указанное в [$sorted_field_name] - станет первичным ключом     * сам массив который будет изменён     *     * @param array $array     * @param string $sorted_field_name     * @param string $new_name_for_old_keys     * @param bool $only_required     * @return void     */    public static function change_preview_key(array &$array, string $sorted_field_name, string $new_name_for_old_keys='old_key', bool $only_required=false) {        foreach($array as $k=>$v) {            $v[$new_name_for_old_keys] = $k;            if($only_required === false) {                $array[$v[$sorted_field_name]] = $v;            } elseif($v['required'] === 1) {                $array[$v[$sorted_field_name]] = $v;            }            unset($array[$k]);        }    }}