<?php
class FILTERS {
    public static function find_filter_preset(string $alias_or_field_name): array
    {
        $rows = SQL_ROWS_FIELD(q("
            SELECT * FROM `filters` WHERE 
            (`alias` LIKE ('%".db_secur($alias_or_field_name)."%')) OR
            (`field_name` LIKE ('%".db_secur($alias_or_field_name)."%')) LIMIT 10
            ", 'field_name'), 'field_name');

        foreach($rows as &$v) {
            $v['default'] = unserialize($v['default']);
        }

        return $rows;
    }

    public static function get_json_from_filters_hub(int $id): array
    {
        $ans = [];
        if($row = SQL_ONE_ROW(q("SELECT * FROM `filters` WHERE id=".$id." LIMIT 1"))) {
            $row['default'] = unserialize($row['default']);
            $ans = $row;
        }
        return $ans;
    }

    public static function update_filter_item_group(int $id_group, PRODUCT_GROUP $group=PRODUCT_GROUP::MAIN_CAT, bool $update=false): array
    {
        $schema = get_product_schema();
        SORT::change_preview_key($schema, 'alias', 'field_name');
        if($row = SQL_ONE_ROW(q("SELECT `json` FROM `".$group->value."` WHERE `id`=".$id_group))) {
            $row = unserialize($row['json']);
        }
        $result = [];
        self::get_field($row, $result);

        if(count($result) > 0) {
            foreach($result as $kk=>$vv) {
                if(isset($schema[$kk])) {
                    unset($result[$kk]);
                    wtf('Ошибка, такое поле существует.');
                }
            }

            $aliases = array_column($result, 'alias');
            $field_names = array_keys($result);

            $isseter = SQL_ROWS(q("
            SELECT `alias`, `field_name` FROM `filters` WHERE 
            `alias` IN (".implode(',', VALUES::wrap_array_elements_around($aliases, '\'')).") OR
            `field_name` IN (".implode(',', VALUES::wrap_array_elements_around($field_names, '\'')).") 
            "));

            if(!empty($isseter)) {
                $isset_field_names = array_column($isseter, 'field_name');
                foreach($isset_field_names as $vv) {
                    unset($result[$vv]);
                }
            }

            if(count($result) > 0) {
                $querys = [];
                foreach($result as $k=>$v) {
                    $querys[] = "(
                    '".db_secur($v['alias'])."',
                    ".(int)$v['order'].",
                    '".$v['type']."',
                    '".$v['field']."',
                    '".serialize($v['default'])."',
                    ".(int)$v['block'].",
                    ".(int)$v['visible'].",
                    ".(int)$v['required'].",
                    '".db_secur($k)."'
                    )";
                }

                q("
                INSERT INTO `filters` (`alias`,`order`,`type`,`field`,`default`,`block`,`visible`,`required`,`field_name`) 
                VALUES ".implode(',', $querys)." 
                ");
            }
        }
        return $result;
    }

    private static function get_field($arr, &$result)
    {
        foreach($arr as $k=>$v) {
            if(is_array($v) && isset($v['alias'])) {
                $result[$k] = $v;
            } else {
                if(is_array($v)) {
                    self::get_field($v, $result);
                }
            }
        }
    }
}