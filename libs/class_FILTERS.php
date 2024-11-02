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
        $last_id = SUBD::get_next_id('filters', true);
        $last_id_buff = $last_id;

        self::get_field($row, $result, $last_id);

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

                q("UPDATE `".$group->value."` SET `json` = '".serialize($row)."' WHERE `id`=".$id_group);

                $querys = [];
                foreach($result as $k=>$v) {
                    $querys[] = "(
                    ".$last_id_buff++.",
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
                INSERT INTO `filters` (`id`, `alias`,`order`,`type`,`field`,`default`,`block`,`visible`,`required`,`field_name`) 
                VALUES ".implode(',', $querys)." 
                ");
            }
        }
        return $result;
    }

    private static function get_field(&$arr, &$result, &$last_id=-1)
    {
        foreach($arr as $k=>&$v) {
            if(is_array($v) && isset($v['alias'])) {
                if(isset($v['ITEMS'])) {
                    foreach($v['ITEMS'] as $kkk=>&$vvv) {
                        if($last_id !== -1 && !isset($vvv['id'])) {
                            $vvv['id'] = $last_id++;
                        }
                        $result[$kkk] = $vvv;
                    }
                    if($last_id === -1) {
                        unset($v['ITEMS']);
                    }
                }
                if($last_id !== -1 && !isset($v['id'])) {
                    $v['id'] = $last_id++;
                }
                $result[$k] = $v;
            } else {
                if(is_array($v)) {
                    self::get_field($v, $result, $last_id);
                }
            }
        }
    }

    public static function get_schema_from_additional_fields_array(array $additional_fields):array {
        foreach($additional_fields as $k=>$v) {
            $additional_fields[$k] = db_secur($v);
        }
        $additional_fields = VALUES::wrap_array_elements_around($additional_fields);
        $rows = SQL_ROWS_FIELD(q("
        SELECT * FROM filters WHERE 
        field_name IN (".implode(',', $additional_fields).") 
        "), "field_name");

//        foreach($rows as $k=>$v) {
//            $rows[$k]['']
//        }

        return $rows;
    }

    /**
     * Возвращает все вложенные поля в виде обычного массива
     *
     * @param $arr
     * @return array
     */
    public static function get_all_nesting_fields($arr): array
    {
        $result = [];
        self::get_field($arr, $result);
        return $result;
    }

    public static function render($main_cat_id, $under_cat_id, $action_list_id) {
        $schema = get_product_schema();
        $additional_fields = SHOP::get_additional_fields_for_cats($main_cat_id, $under_cat_id, $action_list_id);
        if($additional_fields) {
            $rows = array_merge($schema, $additional_fields);
        } else {
            $rows = $schema;
        }
        echo '<table class="filter-table">';
        self::render_tmp($rows);
        echo '<tr style="position: sticky; bottom: 10px"><td></td><td style="padding-top: 15px"><button onclick="apply_filter()" class="btn-send-filter">Применить фильтр</button></td></tr>';
        echo '</table>';
    }

    private static function render_tmp(array $rows, $descr='') {
        global $place;
        ob_start();
        foreach($rows as $field_name=>$params) {

            if(isset($params['filter']) && $params['filter'] == 0) {
                continue;
            }

            $params['field_name'] = $field_name;

//            if(isset($params['name2'])) {
//                $field_name = $params['name2'];
//            }

            $params['id_i'] = $params['id_i'] ?? '';

            $class = "";

            if(is_array($params) && isset($params['alias'])) {
                if($params['visible'] != 1) {
                    $class = "invisible";
                }
                if(isset($params['disabled']) && $params['disabled'] == 1) {
                    $class .= " disabled ";
                }

                if(isset($params['ITEMS'])) {
                    echo '<tr class="clear-row up"><td colspan="2"><div></div></td></tr>';
                }

                switch($params['field']) {
                    case 'input':
                        $compare = "";
                        $compare_field = "";
                        if(isset($params['compare'])) {
                            $compare = 'data-compare="'.$params['compare'].'"';
                        }
                        if(isset($params['compare_field_from'])) {
                            $compare_field = 'data-compare-field="'.$params['compare_field_from'].'"';
                        }
                        if($params['type'] === 'string') { ?>
                            <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" <?=$compare?> <?=$compare_field?> data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default']?>">
                                <td><?=$field_name?></td>
                                <td colspan="2"><input value="" type="text"></td>
                            </tr>
                        <?php } elseif($params['type'] === 'int' || $params['type'] === 'float') { ?>
                            <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" <?=$compare?> <?=$compare_field?> data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default']?>">
                                <td><?=$field_name?></td>
                                <td colspan="2"><input placeholder="число" value="" type="number"></td>
                            </tr>
                        <?php }
                        break;
                    case 'input-object-place':
                        ?>
                        <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default']?>">
                            <td><?=$field_name?></td>
                            <td colspan="2" style="position: relative" class="flex gap-5">
                                <input id="address" placeholder="Введите новое место сделки" data-name="place" value="<?=$params['value'] ?? $place?>" type="text">
                                <button onclick="show_list()" class="svg-wrapper inpt-btn not-border action-btn"><?=RBS::SVG('20230530-211804_id-2-236725.svg')?></button>
                            </td>
                        </tr>
                        <?php
                        break;
                    case 'bool':
                        $state = $params['value'] ?? $params['default']['preset'];
                        if(isset($params['checker']) && $params['checker'] == 1) {
                            $class .= " checker-toggler ";
                        }
                        $state = -1;
                        ?>
                        <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$state?>">
                            <td><?=$field_name?></td>
                            <td colspan="2" style="position: relative" class="flex gap-5">
                                <div class="flex toggler">
                                    <button onclick="change_bool_state(this, true)" class="toggler-item on <?php if($state == 1) { echo 'sel'; } ?>"><?=$params['default']['states'][0]?></button>
                                    <button onclick="change_bool_state(this, false)" class="toggler-item off <?php if($state == 0) { echo 'sel'; } ?>"><?=$params['default']['states'][1]?></button>
                                </div>
                            </td>
                        </tr>
                        <?php
                        break;
                    case 'input-object-counter':
                        ?>
                        <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default']?>">
                            <td><?=$field_name?></td>
                            <td colspan="2" class="flex gap-10">
                                <div class="switcher flex between"><span onclick="set_count_type(true)" class="checked">Ограничено</span><span onclick="set_count_type(false)" class="checked">Неогранич.</span></div>
                                <input min="-1" oninput="check_count_type()" data-name="count" value="<?=$params['value'] ?? $count?>" type="number">
                            </td>
                        </tr>
                        <?php
                        break;
                    case 'tiny':
                        ?>
                        <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=urlencode($params['value'] ?? $params['default'])?>">
                            <td><?=$field_name?></td>
                            <td colspan="2">
                                <textarea>
                                <?php
                                $d_text = $descr ?? '';
                                echo $d_text;
                                ?>
                                </textarea>
                            </td>
                        </tr>
                        <?php
                        break;
                    case 'list':
                        ?>
                        <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-list="<?=implode('|', $params['default'])?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default'][0]?>">
                            <td><?=$field_name?></td>
                            <td colspan="2">
                            <div class="flex column">
                            <?php
                            foreach($params['default'] as $param_item) {
                                echo "<label class='flex gap-5 check-input'><input type='checkbox'><span>".$param_item."</span></label>";
                            }
                            ?>
                            </div>
                            </td>
                        </tr>
                        <?php
                        break;
                }

                if(isset($params['ITEMS'])) {
                    self::render_tmp($params['ITEMS']);
                    echo '<tr class="clear-row down"><td colspan="2"><div></div></td></tr>';
                }

            } else {
                if(!isset($params['no-title']) || $params['no-title'] != 1) {
                    echo '<tr class="title-header-row"><td></td><td><h1 style="font-size: 22px; font-weight: 800; text-align: left">' . $field_name . '</h1></td></tr>';
                }
                foreach($params as $key_param=>$param2) {
                    if(isset($param2['alias'])) {
                        $param2['field_name'] = $key_param;
                    } else {
                        unset($params[$key_param]);
                    }
                }
                self::render_tmp($params);
            }

        }
        echo ob_get_clean();
    }

    public static function create_new_from_json(array $arr): bool|int
    {
        if($arr['field'] !== 'input' && $arr['field'] !== 'text') {
            $arr['default'] = serialize($arr['default']);
        }
        $access = ['alias','order','type','field','default','block','visible','required','field_name'];
        if(isset($arr['id'])) {
            unset($arr['id']);
        }

        $sch = get_product_schema();
        foreach($sch as $item) {
            if($item['field_name'] === $arr['field_name'] || $item['alias'] === $arr['alias']) {
                Message::addError('Параметр с такими ALIAS или FIELD_NAME - существует (система).');
                return false;
            }
        }

        if(VALUES::isset_columns($arr, $access)) {
            if(!self::isset($arr)) {
                return SQL_INSERT_ROW('filters', $arr);
            } else {
                Message::addError('Параметр с такими ALIAS или FIELD_NAME - существует. Или указанные поля не были переданы...');
                return false;
            }
        } else {
            Message::addError('Не переданы нужные параметры...');
            return false;
        }
    }

    public static function update_filter_from_json(array $arr): bool|int
    {
        if($arr['field'] !== 'input' && $arr['field'] !== 'text') {
            $arr['default'] = serialize($arr['default']);
        }
        $access = ['id','alias','order','type','field','default','block','visible','required','field_name'];
        if(VALUES::isset_columns($arr, $access)) {
            return SQL_UPDATE_ROW('filters', $arr);
        } else {
            Message::addError('Не переданы нужные параметры...');
            return false;
        }
    }

    public static function isset(array $arr): bool
    {
        if(isset($arr['alias']) && isset($arr['field_name'])) {
            if($id = SQL_ONE_FIELD(q("SELECT id FROM filters WHERE `alias`='".db_secur($arr['alias'])."' OR `field_name`='".db_secur($arr['field_name'])."' LIMIT 1"))) {
                return true;
            }
            return false;
        }
        return true;
    }
}

