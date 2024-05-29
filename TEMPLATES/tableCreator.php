<?php
global $PERM;

$indexes = Access::get_indexes_from_table($table);
if($script = SUBD::getLineDB('tables_list', 'table_name', db_secur($table))) {
    $script = $script['script'];
}
if(!is_null($script)) {
    $script = explode('|', $script);
} else {
    $script = [];
}
//say($script);

$not_edit = '';
if($PERM->get_permission($table, Permiss::FIELDS_EDIT) === false && Access::get_access(Access::userName(), 'self-edit') === false) {
    $not_edit = ' not-edit-row ';
}
$title = $title ?? 'NOT TITLE';
?>
<table class="table-db" data-name="<?=$table?>" data-title="<?=$title?>" data-ico="<?php if(isset($ico)) { echo $ico; } ?>">
    <tr class="title-row">
        <th onclick="sel_all(this)"  style="box-shadow: none; padding: 7px 5px; border: none; margin: 0;" class=" center checker"><input class="not-active" type="checkbox"></th>
        <?php
//say($columns);
foreach($columns as $k=>$v) {
    $alts = "";
    $index = "";
    $pro_index = "";
    if(isset($indexes[$k])) {
        $index = " index-key ";
        $pro_index = " - есть индекс";
    }
    $title = $k;
    if($v['title'] !== '-' && $v['title'] !== '') {
        $title = $v['title'];
    }
    $type = $v['type'];
    if($v['type_info'] !== '-' && $v['type_info'] !== '') {
        $type = $v['type_info'];
    }

    $values_list = [];
    if($v['type_info'] === 'select') {
        $deff = $v['default'];
        $alts = " data-table-name=\"".$deff['table']."\" data-field-name=\"".$deff['field']."\" ";
        foreach($v['values'] as $itm) {
            if(!in_array($itm, $values_list)) {
                $values_list[] = (int)$itm;
            }
        }
        if(is_array($values_list) && count($values_list) > 0) {
            if(SUBD::existsTable($deff['table'])) {
                $ask = q("SELECT " . db_secur($deff['table']) . ".id, " . db_secur($deff['table']) . "." . db_secur($deff['field']) . " FROM `" . db_secur($deff['table']) . "` WHERE `id` IN (" . implode(',', $values_list) . ")");
                $values_list = SQL_ROWS_FIELD($ask, 'id');
            } else {
                ERRS::add_error('Данная таблица ссылается на другую таблицу «'.$deff['table'].'», которая, по каким-то причинам отсутствует!..');
                $values_list = [];
            }
        } else {
            $values_list = [];
        }
        foreach($v['values'] as $kk=>$vv) {
            if(isset($values_list[$vv])) {
                $v['values'][$kk] = [
                        'id' => $values_list[$vv]['id'],
                        'value' => '<span class="list-item">'.$values_list[$vv][$deff['field']].'</span>'
                ];
            } else {
                $v['values'][$kk] = [
                        'id' => -1,
                        'value' => '<span class="list-item">НЕ ВЫБРАНО</span>'
                ];
            }
        }
        $columns[$k]['values'] = $v['values'];
    }

    $dir_ASC = '';
    $dir_DESC = '';
    if($k === $target_column) {
        if($direct === 'DESC') {
            $dir_DESC = 'sel-sort';
        } else {
            $dir_ASC = 'sel-sort';
        }
    }

    if($v['showed'] == '1') {
        echo '<th class="'.$index.'" title="' . $k . $pro_index . '" draggable="true" data-order="' . $v['order'] . '" data-column="' . $k . '" data-type="' . $type . '" data-edited="' . $v['edited'] . '" ' . $alts . ' style="white-space: nowrap"><div class="flex column rows-block-th"><button class="direct-up ' . $dir_ASC . ' " onclick="sorted(this, \'ASC\')">▲</button><button class="direct-down ' . $dir_DESC . ' " onclick="sorted(this, \'DESC\')">▼</button></div><span class="title-name">' . $title . '</span><div onclick="search_field(this)" class="search-field"><span style="display: inline-block; width: 20px; height: 20px" class="img-wrapper"><img src="./IMG/SYS/search.svg"></span></div></th>';
    } else {
        echo '<th class="'.$index.'" style="display: none" title="' . $k . $pro_index . '" draggable="true" data-order="' . $v['order'] . '" data-column="' . $k . '" data-type="' . $type . '" data-edited="' . $v['edited'] . '" ' . $alts . ' style="white-space: nowrap"><div class="flex column rows-block-th"><button class="direct-up ' . $dir_ASC . ' " onclick="sorted(this, \'ASC\')">▲</button><button class="direct-down ' . $dir_DESC . ' " onclick="sorted(this, \'DESC\')">▼</button></div><span class="title-name">' . $title . '</span><div onclick="search_field(this)" class="search-field"><span style="display: inline-block; width: 20px; height: 20px" class="img-wrapper"><img src="./IMG/SYS/search.svg"></span></div></th>';
    }
}
echo '</tr>';
$key= array_key_first($columns);
$i = count($columns[$key]['values']);

//say($columns);

if($i > 0) {
    for($f=0;$f<$i;++$f) {
        echo '<tr class="'.$not_edit.'"><td class="checker"><input class="not-active checker-inpt" type="checkbox"></td>';
        foreach($columns as $key=>$item) {
            $val = $item['values'][$f];
            $default = $item['default'];
//            say($default);
            if($item['type_info'] === '') {
                $item['type_info'] = $item['type'];
            }
            $invis = '';
            if($item['showed'] == '0') {
                $invis = ' not-visible ';
            }
                switch ($item['type_info']) {
                    case 'tinyint':
                        $add_class = '';
                        $color = '';
                        $txt = '<i>Нет значения</i>';
                        if ($val === '1' && is_array($default)) {
                            $txt = $default['true'];
                            $color = 'status-positive';
                        }
                        if ($val === '0' && is_array($default)) {
                            $txt = $default['false'];
                            $color = 'status-negative';
                        }
                        if (is_array($default)) {
                            echo '<td class="text-center pointer ' .$invis . $color . ' " data-value="' . $val . '" data-name="' . $key . '" data-true="' . $default['true'] . '" data-false="' . $default['false'] . '">' . $txt . '</td>';
                        } else {
                            echo '<td class="text-center pointer ' .$invis . $color . ' " data-value="' . $val . '" data-name="' . $key . '" data-true="-" data-false="-">' . $txt . '</td>';
                        }
                        break;
                    case 'int':
                        echo '<td class="count-int '.$invis.'" data-name="' . $key . '">' . (int)$val . '</td>';
                        break;
                    case 'select':
                        echo '<td class="sell-cell '.$invis.'" data-name="' . $key . '" data-id="' . $val['id'] . '">' . $val['value'] . '</td>';
                        break;
                    case 'datetime':
                        if ($val === '0000-00-00 00:00:00') {
                            $dt = '00.00.0000 00:00:00';
                        } else {
                            $dt = date('d.m.Y H:i:s', strtotime($val));
                        }
                        echo '<td class="text-center '.$invis.'" data-name="' . $key . '">' . $dt . '</td>';
                        break;
                    case 'date':
                        if ($val === '0000-00-00') {
                            $dt = '00.00.0000';
                        } else {
                            $dt = date('d.m.Y', strtotime($val));
                        }
                        echo '<td class="text-center '.$invis.'" data-name="' . $key . '">' . $dt . '</td>';
                        break;
                    case 'time':
                        if ($val === '00:00:00') {
                            $dt = '00:00:00';
                        } else {
                            $dt = date('H:i:s', strtotime($val));
                        }
                        echo '<td class="text-center '.$invis.'" data-name="' . $key . '">' . $dt . '</td>';
                        break;
                    case 'enum':
                        echo '<td class="enum-cell '.$invis.'" data-enum="' . $table . '-' . $key . '" data-name="' . $key . '">' . $val . '</td>';
                        break;
                    case 'double':
                        echo '<td class="count-int '.$invis.'" data-name="' . $key . '">' . round((float)$val, 2) . '</td>';
                        break;
                    case 'file':
                        $title = ' title="Перетащите сюда файл, либо кликните по полю дважды." ';
                        if ($not_edit === '') {
                            echo '<td '.$title.' class="input-img '.$invis.'" data-name="' . $key . '">';
                        } else {
                            echo '<td class="input-img-not_edit '.$invis.'" data-name="' . $key . '">';
                        }
                        if ($val === '-') {
                            $lister = $item['default']['list'] ?? [];
                            echo '<i class="'.$invis.'" style="font-weight: 100; opacity: 0.4">' . implode(',', $lister) . '</i>';
                        } else {
                            $op = "";
                            $nm = "";
                            switch (RBS::get_extention($val)) {
                                case 'image':
                                case 'svg':
                                    if (!file_exists('./DOWNLOAD/' . $val)) {
                                        $val = '/IMG/SYS/img_not_exists.svg';
                                        $op = " opacity ";
                                        $sizer = '';
                                        echo '<div '.$title.' class="micro-img-wrapper '.$invis.'"><img draggable="false" data-name="' . $nm . '" class="' . $op . '" width="100" height="80" src="' . $val . '">' . $sizer . '</div>';
                                    } else {
                                        $nm = $val;
                                        if (RBS::get_extention($val) !== 'svg') {
                                            $val = '/IMG/img100x100/' . $val;
                                            $editor = 'image';
                                        } else {
                                            $val = '/DOWNLOAD/' . $val . '?' . filemtime('./DOWNLOAD/' . $val);
                                            $editor = 'svg';
                                        }
                                        $op = " buffer-file-dragger ";
                                        $sizer = '<button class="sizer-btn" onclick="sizer($(this).parent().find(\'img\'), \'' . $editor . '\')"></button>';
                                        echo '<div '.$title.' class="micro-img-wrapper '.$invis.'"><img draggable="true" data-name="' . $nm . '" class="' . $op . '" width="100" height="80" src="' . $val . '">' . $sizer . '</div>';
                                    }
                                    break;
                                case 'video':
                                    if (file_exists('./DOWNLOAD/' . $val)) {
                                        $nm = explode('.', $val);
                                        $name_file = $nm[count($nm) - 2];
                                        $prev = "";
                                        $fn = './IMG/VIDEO_PREVIEW/' . $name_file . '.jpg';
                                        if (file_exists($fn)) {
                                            $prev = "<img style='pointer-events: none' src='" . $fn . "?" . filemtime($fn) . "'>";
                                        }
                                        echo '<div '.$title.' draggable="true" data-name="' . $val . '" class="buffer-file-dragger micro-video-wrapper '.$invis.'">' . $prev . '<button class="sizer-btn sizer-video" onclick="sizer($(this).parent(), \'video\')"></button></div>';
                                    } else {
                                        echo '<i '.$title.' class="'.$invis.'" style="font-weight: 100; opacity: 0.4">unknown file<br>' . $val . '</i>';
                                    }
                                    break;
                                case 'audio':
//                                    echo '<div '.$title.' draggable="true" data-name="' . $val . '" class="buffer-file-dragger audio-control '.$invis.'"><audio controls="true" src="./DOWNLOAD/' . $val . '"></audio></div>';
                                    $name_audiograph = "./IMG/AUDIO/".explode('.', $val)[0].".png";
                                    echo '<div '.$title.' draggable="true" data-name="' . $val . '" class="buffer-file-dragger audio-control '.$invis. '"><button onclick="create_player(this, \''.$val.'\')" style="margin: 0 0 0 15px; background-color: #ffffff00; height: 30px; border: none;" class="audio action-btn audio-creator flex align-center gap-5"><img src="'.$name_audiograph.'" class=""></button></div>';
                                    break;
                                case 'txt':
                                    echo '<div '.$title.' draggable="true" data-name="' . $val . "?" . filemtime("./DOWNLOAD/" . $val) . '" class="buffer-file-dragger '.$invis.'"><img draggable="false" width="60" height="70" src="./IMG/SYS/txt.svg"><button style="left: 45px; top: 3px; right: unset" class="sizer-btn sizer-video" onclick="sizer($(this).closest(\'.buffer-file-dragger\'), \'txt\')"></button></div>';
                                    break;
                                case 'pdf':
                                    echo '<div '.$title.' draggable="true" data-name="' . $val . '" class="buffer-file-dragger '.$invis.'"><img draggable="false" width="60" height="70" src="./IMG/SYS/pdf.svg"><button style="left: 45px; top: 3px; right: unset" class="sizer-btn sizer-video" onclick="sizer($(this).closest(\'.buffer-file-dragger\'), \'pdf\')"></button></div>';
                                    break;
                                case 'word':
                                case 'tabs':
                                    if (file_exists('./DOWNLOAD/' . $val)) {
                                        switch (RBS::get_extention($val)) {
                                            case 'word':
                                                $fil = './IMG/SYS/word.svg';
                                                break;
                                            case 'tabs':
                                                $fil = './IMG/SYS/tabs.svg';
                                                break;
                                        }
                                        echo '<div '.$title.' draggable="true" data-name="' . $val . '" class="buffer-file-dragger '.$invis.'"><img draggable="false" width="60" height="70" src="' . $fil . '"><button style="left: 45px; top: 3px; right: unset" class="sizer-btn sizer-video" onclick="sizer($(this).closest(\'.buffer-file-dragger\'), \'word\')"></button></div>';
                                    } else {
                                        echo '<i '.$title.' class="'.$invis.'" style="font-weight: 100; opacity: 0.4">unknown file<br>' . $val . '</i>';
                                    }
                                    break;
                                default:
                                    if (stripos($val, '<svg') !== false) {
                                        echo '<div '.$title.' class="svg-wrapper '.$invis.'">' . $val . '</div>';
                                    } else {
                                        echo '<i '.$title.' class="'.$invis.'" style="font-weight: 100; opacity: 0.4">unknown file<br>' . $val . '</i>';
                                    }
                                    break;
                            }
                        }
                        echo '</td>';
                        break;
                    case 'text':
                        $type_text = $default['type'] ?? 'text';
                        switch($type_text) {
                            case 'params':
                                echo '<td class="'.$invis.' y-scroller" data-type-text="'.$type_text.'" data-name="' . $key . '">';
                                echo '<table class="micro-table">';
                                foreach(explode('|',$val) as $v) {
                                    $sh = explode('=',$v);
                                    if(count($sh) === 2) {
                                        echo '<tr><td>'.$sh[0].'</td><td>'.$sh[1].'</td></tr>';
                                    }
                                }
                                echo '</table></td>';
                                break;
                            case 'json': 
								echo '<td class="json-td '.$invis.'" data-type-text="'.$type_text.'" data-name="' . $key . '"><button class="action-btn">JSON</button></td>';
                                break;
                            default:
                                $val = $val ?? '';
                                echo '<td class="'.$invis.'" data-type-text="'.$type_text.'" data-name="' . $key . '">' . mb_strimwidth(out_secur($val), 0, 50, "...") . '</td>';
                                break;
                        }
                        break;
                    default:
                        $val = $val ?? '';
                        echo '<td class="'.$invis.'" data-name="' . $key . '">' . mb_strimwidth($val, 0, 50, "...") . '</td>';
                        break;
                }
        }
        echo '</tr>';
    }
}

$tb_name = $table;
if(isset($table_compile)) {
    $tb_name = '<i title="Данная таблица являестя сгенерированной и не имеей своей копии в общей БД" style="background-color: yellow; padding: 1px 3px; border-radius: 30px">'.$table.'</i>';
}
?>
    <tr>
        <table class="info-table" style="position: sticky; bottom: -3px; left: 1px">
            <tr>
                <th data-table="<?=$table?>" style="position: relative">
                    <div class="table-edit-panel info-table-panel flex gap-5">
                        <button onclick="show_all_tables(this)" class="btn-gray btn-gray-text not-border"><span>Таблица:</span><?=$tb_name?></button>
                        <button onclick="delete_table('<?=$table?>')" class="btn-gray not-border" style="font-size: 9px" title="Удаляет текущую таблицу">❌</button>
                        <button onclick="change_table('NEW TABLE')" style="margin-left: auto" class="btn-gray not-border">Новая<span style="display: inline-block; width: 12px; height: 12px; margin-left: 5px" class="img-wrapper"><img src="./IMG/SYS/table.svg"></span></button>
                        <button onclick="copy_table(this)" style="margin-left: 5px" class="btn-gray not-border">Copy<span style="display: inline-block; width: 12px; height: 12px; margin-left: 5px" class="img-wrapper"><img src="./IMG/SYS/table.svg"></span></button>
                    </div>
                </th>
                <th>
                    <?php if(isset($paginator) && !empty($paginator) && $paginator['paginator_items'] > 1) {
                    echo '<div data-table-name="'.$table.'" class="paginator" data-paginator-item="'.(int)$paginator['paginator_num'].'">';
                    $all = $paginator['paginator_items'];
                    $flow = $paginator['paginator_num'];

                    if($flow > 5) {
                        $begin = $flow-4;
                        echo '<button>1</button>';
                        echo '...';
                    } else {
                        $begin = 1;
                    }
                    for($i=$begin;$i<$flow;++$i) {
                        echo '<button>' . $i . '</button>';
                    }

                    echo '<button class="now">'.$flow.'</button>';

                    if($flow+4 < $all) {
                        $end = $flow+4;
                    } else {
                        $end = $all;
                    }
                    for($i=$flow+1;$i<=$end;++$i) {
                        echo '<button>' . $i . '</button>';
                    }
                    if($end !== $all) {
                        echo '...';
                        echo '<button>' . $all . '</button>';
                    }
                    echo '<select class="all-pags" onchange="$(this).parent().attr(\'data-paginator-item\', $(this).val()); $(this).closest(\'tr\').find(\'.refresh-table\').click()">';
                    for ($i = 1; $i <= $all; ++$i) {
                        if ($i === $flow) {
                            echo '<option selected>' . $i . '</option>';
                        } else {
                            echo '<option>' . $i . '</option>';
                        }
                    }
                    echo '</select>';
                    echo '</div>';
                    } ?>
                </th>
                <th>
                    <div class="table-edit-panel flex gap-5">
                        <button onclick="add_new_row('<?=$table?>')" class="btn-gray <? if($PERM->get_permission($table, Permiss::ROW_ADD) === false && Access::get_access(Access::userName(), 'self-edit') === false) { echo ' global-disabled '; } ?>" title="Добавляет пустую запись в таблицу">✚<span style="padding-left: 5px">Доб.</span></button>
                        <button onclick="dell_sel_rows('<?=$table?>')" class="btn-gray disabled del-row-btn  <?php if($PERM->get_permission($table, Permiss::ROW_DEL) === false && Access::get_access(Access::userName(), 'self-edit') === false) { echo ' global-disabled '; } ?>" title="Удаляет отмеченные записи">✖<span style="padding-left: 5px">Удал.</span></button>
                        <?php if($table !== 'file') { ?>
                        <button onclick="copy_sel_rows('<?=$table?>')" class="btn-gray disabled del-row-btn  <?php if($PERM->get_permission($table, Permiss::ROW_ADD) === false && Access::get_access(Access::userName(), 'self-edit') === false) { echo ' global-disabled '; } ?>" title="Копирует отмеченные записи">&#128459;<span style="align-self: self-end; padding-left: 5px">Копир.</span></button>
                        <?php } ?>
                        <?php
                        if(count($script) >= 3) { ?>
                            <button onclick="on_off('<?=$table?>')" class="btn-gray del-row-btn  margin-left-auto <?php if($not_edit !== '') { echo 'lime-back'; } else { echo 'red-back'; } ?>" title="Превращает таблицу в активную панель">☈<span style="padding-left: 5px">SCR</span></button>
                        <?php } ?>
                    </div>
                </th>
                <th>
                    <div class="minimaze">
                        <button onclick="add_rows_from_text(this)" title="Добавление в базу из *.txt файла, с разделителем - TAB" class="btn-gray not-border ico-btn">></button>
                    </div>
                </th>
                <th style="position: relative">
                    <div class="table-edit-panel icons-panel">
                        <button onclick="tables_list()" class="btn-gray not-border ico-btn" title="Открывает список доступных таблиц"><span style="display: inline-block; width: 12px; height: 12px" class="img-wrapper"><img src="./IMG/SYS/table.svg"></span></button>
                        <button onclick="refrash_table(this)" class="btn-gray not-border ico-btn refresh-table" title="Обновить таблицу"><span style="display: inline-block; width: 12px; height: 12px" class="img-wrapper"><img src="./IMG/SYS/circler.svg"></span></button>
                    </div>
                </th>
            </tr>
        </table>
    </tr>
</table>

<?php
foreach($columns as $k=>$v) {
    if($v['type_info'] === 'enum') {
        echo '<template id="'.$table.'-'.$k.'">';
        foreach($v['default']['list'] as $itm) {
            echo '<option>'.$itm.'</option>';
        }
        echo '</template>';
    }
}

if(count($script) >= 3) {
    $param1 = "";
    switch($script[0]) {
        case 'row':
            $param1 = "tr.not-edit-row";
            break;
    }
?>
<script>
    function_name = '<?=implode('-', $script)?>';
    if(!functions.includes(function_name)) {
        $(document).on('<?=$script[1]?>', '.table-db[data-name="<?=$table?>"] <?=$param1?>', function (e) {
            e.stopPropagation();
            e.preventDefault();
            <?=$script[2]?>
        });
        functions.push(function_name);
    }
</script>
<?php } ?>