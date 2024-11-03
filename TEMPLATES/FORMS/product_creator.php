<?php
include_once './APPLICATIONS/SHOPS/libs/class_SHOP.php';
include_JS('checker-toggler');

$cats = SUBD::getAllDB('shops_categorys');
$cats = SORT::array_sort_by_column($cats, 'category');
array_unshift($cats, [
    'id'=>-1,
    'category'=>'-',
]);
$list_places = explode('~~', $P->get_sys_param('list_places'));
//include_JS_once("/APPLICATIONS/SHOPS/JS/product_creator.js");

$place = '';

$schema = get_product_schema();

if(!empty($order)) {
//    wtf($order);
    foreach($order['PROPS'] as $one_prop) {
        if($one_prop[0]['schema_id'] <= 17) {
            $schema[$one_prop[0]['field_name']]['value'] = $one_prop[0]['value'];
            $schema[$one_prop[0]['field_name']]['id_i'] = $one_prop[0]['id'];
        }
    }
//wtf($schema);
//wtf($order['PROPS']);
    $additional_fields = SHOP::get_additional_fields_for_cats(
            $order['main_cat_id'],
            $order['under_cat_id'],
            $order['action_list_id']
    );

    if(is_array($additional_fields)) {
        PROPS_COMMANDER::aply_values_for_additional_schema($additional_fields, $order['PROPS']);
    }

    $CAT = CATALOGER::INIT();

    SORT::change_preview_key($schema, 'alias', 'field_name');
    $schema['product_name']['value'] = $order['name'];
    $schema['price']['value'] = $order['price'];
    $schema['count']['value'] = $order['count'];

    $schema['category']['value'] = $order['main_cat'];
    $schema['category']['real'] = $CAT->main_cat_name_to_id($order['main_cat']);

    $schema['under-cat']['value'] = $order['under_cat'];
    $schema['under-cat']['real'] = $CAT->under_cat_name_to_id($schema['category']['real'], $order['under_cat']);

    $schema['action-list']['value'] = $order['action_list'];
    $schema['action-list']['real'] = $CAT->action_list_name_to_id($order['action_list']);

    SORT::change_preview_key($schema, 'field_name', 'alias');
}

if(isset($_GET['main'])) {
    $CAT = CATALOGER::INIT();
    SORT::change_preview_key($schema, 'alias', 'field_name');

    $schema['category']['value'] = $CAT->id2main_cat((int)$_GET['main']);
    $schema['category']['real'] = (int)$_GET['main'];

    if (isset($_GET['under'])) {
        $schema['under-cat']['value'] = $CAT->id2under_cat((int)$_GET['under']);
        $schema['under-cat']['real'] = (int)$_GET['under'];
    } else {
        $schema['under-cat']['value'] = '-';
        $schema['under-cat']['real'] = -1;
    }
    if (isset($_GET['list'])) {
        $schema['action-list']['value'] = $CAT->id2action_list((int)$_GET['list']);
        $schema['action-list']['real'] = (int)$_GET['list'];
    } else {
        $schema['action-list']['value'] = '-';
        $schema['action-list']['real'] = -1;
    }

    $additional_fields = SHOP::get_additional_fields_for_cats((int)$_GET['main'], $schema['under-cat']['real'], $schema['action-list']['real']);

    SORT::change_preview_key($schema, 'field_name', 'alias');
}
//wtf($order, 1);
//wtf($schema, 1);

$phones = SUBD::getAllLinesDB('phones', 'user_id', Access::userID());
if(!is_array($phones)) {
    $phones = [];
}
$P = PROFIL::init(Access::userID());
array_unshift($phones, [
    'id'=>-1,
    'number'=>$P->get('phone', 'НЕТ НОМЕРА'),
    'user_id'=>Access::userID(),
    'descr'=>'Номер аккаунта',
]);

$count = $order['COUNT'] ?? -1;
$count = (int)$count;
//wtf($schema, 1);

?>
<div class="product-creator" id="product-creator">
    <table>
        <tr data-type="image">
            <td>Изображение<br>(фото)</td>
            <td colspan="3" class="insert-files">
                <label class="action-btn">
                    <input id="file-input" type="file" accept=".jpg, .jpeg, .webp, .png, .gif" multiple="multiple">
                </label>
                <div class="list-prev-img flex gap-5">
                    <?php
                    if(isset($imgs) && count($imgs) > 0) {
                        foreach($imgs as $itm) {
                            $src = $itm;
                            echo '<button onclick="del_img(this)" data-name="'.$src.'" class="img-btn action-btn" title="Удалить изображение">';
                            echo '<img src="./IMG/img300x300/'.$src.'" data-src="'.$src.'">';
                            echo '</button>';
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>

        <?php
        $descr = $descr ?? "";

        if(isset($additional_fields) && is_array($additional_fields)) {
            $schema = array_merge($schema, $additional_fields);
        }

//        wtf($schema);
//        wtf($additional_fields);


        render_rows($schema, $cats, $descr);

        ?>
    </table>
</div>

<?php
function render_rows(array $rows, $cats, $descr='') {
    global $place;
    ob_start();
    foreach($rows as $field_name=>$params) {

        $params['field_name'] = $field_name;

        if(isset($params['psevdo']) && $params['psevdo'] == 1) {
            continue;
        }

        if(isset($params['name2'])) {
            $field_name = $params['name2'];
        }

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
                    if($params['type'] === 'string') { ?>
                        <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>"  data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default']?>">
                            <td><?=$field_name?></td>
                            <td colspan="2"><input value="<?=$params['value'] ?? $params['default'];?>" type="text"></td>
                        </tr>
                    <?php } elseif($params['type'] === 'int' || $params['type'] === 'float') { ?>
                        <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default']?>">
                            <td><?=$field_name?></td>
                            <td colspan="2"><input placeholder="число" value="<?=$params['value'] ?? $params['default'];?>" type="number"></td>
                        </tr>
                    <?php }
                    break;
                case 'text':
                    if($params['type'] === 'text') { ?>
                        <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>"  data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default']?>">
                            <td><?=$field_name?></td>
                            <td colspan="2"><textarea><?=$params['value'] ?? $params['default'];?></textarea></td>
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
                case 'input-object-cat':
                    if(isset($_GET['main'])) {
                        $class .= " disabled ";
                    }
                    ?>
                    <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['real'] ?? $params['default']?>">
                        <td><?=$field_name?></td>
                        <td colspan="2">
                            <select onchange="change_main_cat(this)" data-name="type">
                                <?php
                                foreach($cats as $v) {
                                    if(!empty($params['value'])) {
                                        if($params['value'] === $v['category']) {
                                            echo '<option selected data-id="'.$v['id'].'" value="'.$v['category'].'">'.$v['category'].'</option>';
                                        } else {
                                            echo '<option data-id="'.$v['id'].'" value="'.$v['category'].'">'.$v['category'].'</option>';
                                        }
                                    } else {
                                        echo '<option data-id="'.$v['id'].'" value="'.$v['category'].'">'.$v['category'].'</option>';
                                    }
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'input-object-undercat':
                    if(isset($_GET['under'])) {
                        $class .= " disabled ";
                    }
                    ?>
                    <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['real'] ?? $params['default']?>">
                        <td><?=$field_name?></td>
                        <td colspan="2">
                            <select onchange="change_under_cat(this)" id="under-cat" data-name="type">
                                <?php
                                if(!empty($params['value'])) {
                                    echo '<option value="'.$params['real'].'">'.$params['value'].'</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'input-object-actionlist':
                    if(isset($_GET['list'])) {
                        $class .= " disabled ";
                    }
                    ?>
                    <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-field="<?=$params['field']?>" data-real="<?=$params['real'] ?? $params['default']?>">
                        <td><?=$field_name?></td>
                        <td colspan="2">
                            <select onchange="change_action_list(this)" id="action-list" data-name="type">
                                <?php
                                if(!empty($params['value'])) {
                                    echo '<option value="'.$params['real'].'">'.$params['value'].'</option>';
                                }
                                ?>
                            </select>
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
                            <?php
                            $d_text = $descr ?? '';
                            echo render('quill', [
                                'visible'=>true,
                                'text'=>$d_text,
                                'width'=>'60vw',
                                'not_closed'=>true,
                                'hidden_save'=>true,
                            ]);
                            ?>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'list':
                    ?>
                    <tr class="<?=$class?>" data-id-i="<?=($params['id_i'] ?? '')?>" data-param-id="<?=$params['id']?>" data-list="<?=implode('|', $params['default'])?>" data-field="<?=$params['field']?>" data-real="<?=$params['value'] ?? $params['default'][0]?>">
                        <td><?=$field_name?></td>
                        <td colspan="2"><input value="<?=$params['value'] ?? $params['default'][0]?>" type="text"></td>
                    </tr>
                    <?php
                    break;
            }

            if(isset($params['ITEMS'])) {
                render_rows($params['ITEMS'], $cats);
                echo '<tr class="clear-row down"><td colspan="2"><div></div></td></tr>';
            }

        } else {
//            wtf($params, 1);
//            echo '<tr class="clear-row"><td colspan="2"><div></div></td></tr>';
            echo '<tr class="title-header-row"><td></td><td><h1 style="font-size: 22px; font-weight: 800; text-align: left">'.$field_name.'</h1></td></tr>';
            foreach($params as $key_param=>$param2) {
                if(isset($param2['alias'])) {
                    $param2['field_name'] = $key_param;
                } else {
                    unset($params[$key_param]);
                }
            }
            render_rows($params, $cats);
//            echo '<tr class="clear-row down"><td colspan="2"><div></div></td></tr>';
        }

    }
    echo ob_get_clean();
}
?>

<table class="set-to_bottom">
    <tr class="gray-box">
        <?php if(isset($_GET['com']) && $_GET['com'] === 'edit') { ?>
            <td><button onclick="save_product(false)" class="btn-just btn" style="background-color: #adffad; padding: 2px 10px">Сохранить изменения</button></td>
        <?php } else { ?>
        <td><button onclick="save_product()" class="btn-just btn" style="background-color: #adffad; padding: 2px 10px">Создать товар</button></td>
        <td><button id="cleator" onclick="clear_all_forms()" class="btn-just btn big-paddings" style="background-color: #adffad; padding: 2px 10px; display: none">Очистить всё</button></td>
<!--        <td><button onclick="load_buff()" class="btn-just btn" style="background-color: #adffad; padding: 2px 10px">востановить</button></td>-->
        <?php } ?>
        <td></td>
        <td style="text-align: right">
<!--            <button onclick="add_properties()" class="btn-just" style="padding: 2px 10px">+ Добавить свойство</button>-->
        </td>
    </tr>
</table>

<script>
    props_items = <?=json_encode(FILTERS::get_all_nesting_fields($schema), JSON_UNESCAPED_UNICODE)?>;

    list_places = <?=json_encode($list_places, JSON_UNESCAPED_UNICODE)?>;
    phones = <?=json_encode($phones, JSON_UNESCAPED_UNICODE)?>;

    upload_files = [];  // обнуляет файлы при загрузке
    load_JSscript_once('/APPLICATIONS/SHOPS/JS/product_creator.js?<?=filemtime('./APPLICATIONS/SHOPS/JS/product_creator.js')?>');

    console.log('PHONES');
    console.dir(phones);
</script>