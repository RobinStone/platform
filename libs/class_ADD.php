<?php
class ADD {
    private string $table_name = '';
    public array $arr = [];
    public static int $last_id_insert = -1;
    public function __construct(string $table_name='') {
        if(SUBD::existsTable($table_name)) {
            $this->table_name = $table_name;
            $this->arr = SQL_ROWS_FIELD(q("SELECT * FROM props WHERE `table`='".db_secur($table_name)."'"), 'id');
            foreach($this->arr as $k=>$v) {
                $this->arr[$k]['st7'] = 0; // (0) - существует в базе и не изменена, (-1) - новая запись, (1) - запись есть в базе но нужно обновить, (2) - на удаление
            }
        }
    }

    public function insert(string $column_name, int $id_parent, string $value='', $auto_save=true): bool
    {
        $this->arr[] = [
            'table'=>$this->table_name,
            'col'=>db_secur($column_name),
            'id_item'=>$id_parent,
            'val'=>db_secur($value),
            'st7'=>-1,
        ];
        if($auto_save) {
            $this->save();
        }
        return true;
    }

    public function update(int $id, string $value='|',bool $auto_save=true, string $column_name='|', int|string $id_parent='not change'): bool
    {
        if(isset($this->arr[$id])) {
            if($value !== '|') {
                $this->arr[$id]['val'] = db_secur($value);
                $this->arr[$id]['st7'] = 1;
            }
            if($column_name !== '|') {
                $this->arr[$id]['col'] = db_secur($column_name);
                $this->arr[$id]['st7'] = 1;
            }
            if($id_parent !== 'not change') {
                $this->arr[$id]['id_item'] = (int)$id_parent;
                $this->arr[$id]['st7'] = 1;
            }
            if($auto_save) {
                $this->save();
            }
        } else {
            Message::addError('Ошибка обновления в таблице "props". Не найден ID обновления.');
        }
        return true;
    }

    public static function EXEC(string $table_name): ADD
    {
        return new ADD($table_name);
    }

    public function enrich(array &$arr_with_id, string $column_name='', bool $transform_column_to_array=false) {
        if(count($arr_with_id) > 0 && isset($arr_with_id[array_key_first($arr_with_id)]['id'])) {
            $quer = "";
            $ids = array_column($arr_with_id, 'id');
            foreach($ids as $k=>$v) {
                $ids[$k] = (int)$v;
            }
            if ($column_name !== '') {
                $quer = " `col`='" . db_secur($column_name) . "' AND ";
            }
            $rows = SQL_ROWS_FIELD(q("SELECT * FROM `props` WHERE `table`='".$this->table_name."' AND ".$quer." `id` IN (".implode(',', $ids).") "), 'id_item');
            foreach($arr_with_id as $k=>$v) {
                if(isset($rows[$v['id']])) {
                    if($transform_column_to_array && $column_name !== '' && isset($v[$column_name])) {
                        $arr = array_column($rows[$v['id']], 'val');
                        $arr[] = $v[$column_name];
                        $arr_with_id[$k][$column_name] = $arr;
                    } else {
                        $arr_with_id[$k]['PROPS'] = $rows[$v['id']];
                    }
                }
            }
        }
    }

    public static function get_additional_props(string $table_name, int $id_parent, string $column_name='', $only_values=false): array
    {
        if($column_name !== '') {
            $column_name = " AND `col`='".db_secur($column_name)."' ";
        }
        $rows = [];
        if($only_values) {
            $rows = SQL_ROWS_FIELD(q("SELECT `val` FROM `props` WHERE `table`='".db_secur($table_name)."' AND `id_item`=".$id_parent." ".$column_name." "), 'id');
            $rows = array_column($rows, 'val');
        } else {
            $rows = SQL_ROWS_FIELD(q("SELECT * FROM `props` WHERE `table`='".db_secur($table_name)."' AND `id_item`=".$id_parent." ".$column_name." "), 'id');
        }
        return $rows;
    }

    /**
     * Позволяет удалить один (INT) или массив из (INTs) по ID
     * @param array|int $id_prop_OR_arr_props
     * @param bool $auto_save
     * @return bool
     */
    public function delete_additional_props(array|int $id_prop_OR_arr_props, bool $auto_save=true): bool
    {
        if(!is_array($id_prop_OR_arr_props)) {
            $id_prop_OR_arr_props = [$id_prop_OR_arr_props];
        }
        if(!empty($id_prop_OR_arr_props)) {
            foreach($id_prop_OR_arr_props as $v) {
                if(isset($this->arr[$v]) && $this->arr[$v]['st7'] !== -1) {
                    $this->arr[$v]['st7'] = 2;  // индекс на удаление
                }
            }
            if($auto_save) {
                $this->save();
            }
        }
        return true;
    }

    public function save() {
        $start = microtime(true);
        $update = [];
        $new = [];
        $for_del = [];
        foreach($this->arr as $k=>$v) {
            if($v['st7'] === 1) {
                unset($v['st7']);
                $update[$v['id']] = " WHEN ".$v['id']." THEN '".$v['val']."' ";
            } elseif($v['st7'] === -1) {
                unset($v['st7']);
                $new[] = " ('".$v['table']."', '".$v['col']."', ".$v['id_item'].", '".$v['val']."') ";
            } elseif($v['st7'] === 2) {
                unset($v['st7']);
                $for_del[] = (int)$v['id'];
            }
        }
        if(count($update) > 0) {
            $ids = array_column($update, 'id');
            q("UPDATE `props` SET `val` = CASE id ".implode(' ', $update)." ELSE `val` END WHERE `id` IN (".implode(',', $ids).") ");
        }
        if(count($new) > 0) {
            q("INSERT INTO `props` (`table`, `col`, `id_item`, `val`) VALUES ".implode(',', $new)." ");
            self::$last_id_insert = SUBD::get_last_id();
        }
        if(count($for_del) > 0) {
            q("DELETE FROM `props` WHERE `id` IN (".implode(',', $for_del).")");
        }
        $tim = microtime(true) - $start;
        if($tim > 0.5) {
            t("время сохранения доп. = ".$tim);
        }
    }
}