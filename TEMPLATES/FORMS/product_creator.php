<?php
include_once './APPLICATIONS/SHOPS/libs/class_SHOP.php';

$cats = SUBD::getAllDB('shops_categorys');
$cats = SORT::array_sort_by_column($cats, 'category');
array_unshift($cats, [
    'id'=>-1,
    'category'=>'-',
]);
$props = SHOP::get_all_props((int)$_SESSION['shop_id']);
$list_places = explode('~~', $P->get_sys_param('list_places'));
//include_JS_once("/APPLICATIONS/SHOPS/JS/product_creator.js");

if(isset($order['VALS'])) {
    INCLUDE_CLASS('shops', 'PROPS_COMMANDER');
    $PR = new PROPS_COMMANDER($order['VALS']);
    $place = $PR->get_all_props_at_field_name('Расположение', true)['VALUE'];
} else {
    $place = '';
}

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
//wtf($order, 1);

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
                    <?
                    if(isset($imgs) && count($imgs) > 0) {
                        foreach($imgs as $itm) {
                            $src = $itm['VALUE'];
                            echo '<button data-old-props-id="'.$itm['ID'].'" onclick="del_img(this)" data-name="'.$src.'" class="img-btn action-btn" title="Удалить изображение">';
                            echo '<img src="./IMG/img300x300/'.$src.'" data-src="'.$src.'">';
                            echo '</button>';
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr data-field="sys-name">
            <td>Название товара</td>
            <td colspan="2"><input data-name="name" value="<?=$order['NAME'] ?? '';?>" type="text"></td>
        </tr>
        <tr data-type="number" <? if(isset($price)) { echo 'data-old-props-id="'.$price['ID'].'"'; } ?>>
            <td>Стоимость</td>
            <td colspan="2">
                <input placeholder="число" value="<? if(isset($price)) { echo $price['VALUE']; } ?>" type="number">
            </td>
        </tr>
        <tr data-type="string" <? if(isset($addr)) { echo 'data-old-props-id="'.$addr['ID'].'"'; } ?>>
            <td>Расположение</td>
            <td colspan="2" style="position: relative" class="flex gap-5">
                <input id="address" placeholder="Введите новое место сделки" data-name="place" value="<?=$place?>" type="text">
                <button onclick="show_list()" class="svg-wrapper inpt-btn not-border action-btn"><?=RBS::SVG('20230530-211804_id-2-236725.svg')?></button>
            </td>
        </tr>
        <tr data-field="sys-caterory">
            <td>Тип (категория)</td>
            <td colspan="2">
                <select onchange="change_main_cat(this)" data-name="type">
                    <?
                    foreach($cats as $v) {
                        if(isset($order['CAT'])) {
                            if($order['CAT'] === $v['category']) {
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
        <tr data-field="sys-under-cat">
            <td>Подкатегория</td>
            <td colspan="2">
                <select onchange="change_under_cat(this)" id="under-cat" data-name="type">
                    <?
                    if(isset($order['UNDERCAT'])) {
                        echo '<option value="'.$order['UNDERCAT'].'">'.$order['UNDERCAT'].'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr data-field="sys-action-list">
            <td>Множество</td>
            <td colspan="2">
                <select onchange="change_action_list(this)" id="action-list" data-name="type">
                    <?
                    if(isset($order['LIST'])) {
                        echo '<option value="'.$order['LIST'].'">'.$order['LIST'].'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr data-field="sys-count">
            <td>Количество</td>
            <td colspan="2" class="flex gap-10">
                <div class="switcher flex between"><span onclick="set_count_type(true)" class="checked">Ограничено</span><span onclick="set_count_type(false)" class="checked">Неогранич.</span></div>
                <input min="-1" oninput="check_count_type()" data-name="count" value="<?=$count?>" type="number">
            </td>
        </tr>
        <tr data-type="text" <? if(isset($descr)) { echo 'data-old-props-id="'.$descr['ID'].'"'; } ?>>
            <td>Описание</td>
            <td colspan="2">
<!--                <textarea placeholder="текст">--><?// if(isset($descr)) { echo $descr['VALUE']; } ?><!--</textarea>-->
                <?php
                if(isset($descr)) { $d_text = $descr['VALUE']; } else { $d_text = ''; }
                echo render('tiny', [
                        'visible'=>true,
                        'text'=>$d_text,
                        'width'=>'60vw',
                        'not_closed'=>true,
                        'hidden_save'=>true,
                ]);
                ?>
            </td>
        </tr>
    </table>
</div>
<table class="set-to_bottom">
    <tr class="gray-box">
        <?php if(isset($_GET['com']) && $_GET['com'] === 'edit') { ?>
            <td><button onclick="save_product()" class="btn-just btn" style="background-color: #adffad; padding: 2px 10px">Сохранить изменения</button></td>
        <?php } else { ?>
        <td><button onclick="save_product()" class="btn-just btn" style="background-color: #adffad; padding: 2px 10px">Создать товар</button></td>
        <td><button id="cleator" onclick="clear_all_forms()" class="btn-just btn big-paddings" style="background-color: #adffad; padding: 2px 10px; display: none">Очистить всё</button></td>
<!--        <td><button onclick="load_buff()" class="btn-just btn" style="background-color: #adffad; padding: 2px 10px">востановить</button></td>-->
        <?php } ?>
        <td></td>
        <td style="text-align: right">
            <button onclick="add_properties()" class="btn-just" style="padding: 2px 10px">+ Добавить свойство</button>
        </td>
    </tr>
</table>

<?php
foreach($props as $k=>$v) {
    echo '<datalist id="list-'.$k.'">';
    foreach($v as $itm) {
        echo '<option>'.$itm.'</option>';
    }
    echo '</datalist>';
}
?>

<script>
    <? // тут происходит загрузка в JS всех свойств (если редактирование происходит)
    if(isset($order, $order['VALS'])) {
        $arrs =  $order['VALS'];
        foreach($arrs as $k=>$v) {
            if($v['field_type'] === 'list') {
                $v['VALUE'] = unserialize($v['VALUE']);
                $v['fixed'] = 1;
            }
            $arrs[$k] = $v;
        }

        echo 'order_vals='. json_encode($arrs, JSON_UNESCAPED_UNICODE);
    } else {  // ЕСЛИ ЭТО НОВЫЙ ORDER, ТО ТУТ есть возможность дополнить поля
        $ADDED_FIELDS = [
            0 => [
                'VALUE'=>'',
                'field_type'=>'number',
                'name'=>'Широта',
                'types'=>'val_float',
                'visible'=>0,
                'block'=>1,
            ],
            1 => [
                'VALUE'=>'',
                'field_type'=>'number',
                'name'=>'Долгота',
                'types'=>'val_float',
                'visible'=>0,
                'block'=>1,
            ],
            2 => [
                'VALUE'=>'',
                'field_type'=>'number',
                'name'=>'IDcity',
                'types'=>'val_int',
                'visible'=>0,
                'block'=>1,
            ],
            3 => [
                'VALUE'=>'',
                'field_type'=>'number',
                'name'=>'IDcountry',
                'types'=>'val_int',
                'visible'=>0,
                'block'=>1,
            ],
            4 => [
                'VALUE'=>'',
                'field_type'=>'list',
                'name'=>'Телефон заказа',
                'types'=>'val_text',
                'visible'=>1,
                'block'=>0,
                'fixed'=>1,
            ],
            5 => [
                'VALUE'=>'0',
                'field_type'=>'number',
                'name'=>'Скидка %',
                'types'=>'val_int',
                'visible'=>1,
                'block'=>0,
                'fixed'=>1,
            ],
        ];
        echo 'order_vals='. json_encode($ADDED_FIELDS, JSON_UNESCAPED_UNICODE);
    }
    ?>

    list_places = <?=json_encode($list_places, JSON_UNESCAPED_UNICODE)?>;
    phones = <?=json_encode($phones, JSON_UNESCAPED_UNICODE)?>;

    upload_files = [];  // обнуляет файлы при загрузке
    load_JSscript_once('/APPLICATIONS/SHOPS/JS/product_creator.js?<?=filemtime('./APPLICATIONS/SHOPS/JS/product_creator.js')?>');

    console.log('PHONES');
    console.dir(phones);
</script>