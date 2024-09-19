<?php
$title = 'UNKNOWN...';
$access = true;
$items = [];
$read_only = true;

switch ($type ?? '') {
    case 'delivery_info':
        $title = 'Информация о доставке';
        if($row = SQL_ONE_ROW(q("SELECT order_code, id_user, shop_id FROM orders WHERE id=".(int)($order_id ?? -1)))) {
            $info = $row;
            $row = unserialize($row['order_code'])['delivery_info'];
            INCLUDE_CLASS('shops', 'shop');
            if(Access::userID() === (int)$info['id_user'] || SHOP::is_my_shop($info['shop_id'])) {
                $items = [
                    [
                        'type'=>'phone',
                        'title'=>'Номер телефона',
                        'name'=>'phone',
                        'placeholder'=>'Номер телефона',
                        'value'=>$row['phone'] ?? '-',
                    ],
                    [
                        'type'=>'text',
                        'title'=>'Коментарий к заказу',
                        'name'=>'comment',
                        'placeholder'=>'Коментарий к заказу',
                        'value'=>$row['comment'] ?? '-',
                    ],
                ];
                switch($row['type_obtaining']) {
                    case 'self':
                        $items[] = [
                                'type'=>'string',
                                'title'=>'ФИО',
                                'name'=>'fio',
                                'placeholder'=>'ФИО',
                                'value'=>$row['fio'] ?? '-',
                            ];
                        break;
                    case 'courier':
                        $items[] = [
                                'type'=>'string',
                                'title'=>'ФИО',
                                'name'=>'fio',
                                'placeholder'=>'fio',
                                'value'=>$row['fio'] ?? '-',
                            ];
                        $items[] = [
                                'type'=>'string',
                                'title'=>'Город',
                                'name'=>'city',
                                'placeholder'=>'Город',
                                'value'=>$row['city'] ?? '-',
                            ];
                        $items[] = [
                                'type'=>'text',
                                'title'=>'Адрес доставки',
                                'name'=>'address_of_delivery',
                                'placeholder'=>'Адрес доставки',
                                'value'=>$row['address_of_delivery'] ?? '-',
                            ];
                        break;
                }
            } else {
                $access = false;
                $title = 'Запрещена работа с чужими данными...';
            }
        }
        break;
    default:
        $title = 'Не найден обработчик формы...';
        $access = false;
        break;
}
?>
<style>
    .former-cont {
        width: 700px;
        max-width: 80vw;
    }

    .former-cont input,
    .former-cont textarea {
        background-color: rgb(203, 255, 223);
        display: inline-block;
        min-width: 100%;
        border-radius: 6px;
        border: none;
        padding: 5px 10px;
        box-sizing: border-box;
    }
    .former-cont fieldset {
        box-sizing: border-box;
        display: inline-block;
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, 0.24);
    }
    .former-cont fieldset legend {
        font-size: 14px;
        padding: 3px 7px;
        background-color: #fff;
        border-radius: 40px;
    }

    @media screen and (max-width: 950px) {

    }

</style>

<?php if($access) { ?>
<section class="former-cont">
    <h1 style="margin-bottom: 1em"><?=$title?></h1> <?php
    foreach ($items as $item) {
        render_row($item['type'], $item['name'], $item['value'], $item['title'], $item['placeholder']);
    }
?>
</section>
<?php } else { echo '<section class="former-cont"><h1>'.$title.'</h1></section>'; } ?>


<?php
function render_row(string $type, string $name, string $value, $title='', $placeholder='') {
    global $read_only;
    switch ($type) {
        case 'string': ?>
            <fieldset>
                <legend><?=$title ?? ''?></legend>
                <input <?php if($read_only) { echo 'readonly'; } ?> class="delivery-field fields" type="text" placeholder="<?=$placeholder ?? ''?>" name="<?=$name?>" value="<?=$value?>">
            </fieldset>
            <?php break;
        case 'phone': ?>
            <fieldset>
                <legend><?=$title ?? ''?></legend>
                <input <?php if($read_only) { echo 'readonly'; } ?> class="delivery-field fields" type="tel" placeholder="<?=$placeholder ?? ''?>" name="<?=$name?>" value="<?=$value?>">
            </fieldset>
            <?php break;
        case 'text': ?>
            <fieldset>
                <legend><?=$title ?? ''?></legend>
                <textarea <?php if($read_only) { echo 'readonly'; } ?> class="delivery-field fields" placeholder="<?=$placeholder ?? ''?>" name="<?=$name?>"><?=$value?></textarea>
            </fieldset>
            <?php break;
        case 'number': ?>
            <fieldset>
                <legend><?=$title ?? ''?></legend>
                <input <?php if($read_only) { echo 'readonly'; } ?> class="delivery-field fields" type="number" placeholder="<?=$placeholder ?? ''?>" name="<?=$name?>" value="<?=$value?>">
            </fieldset>
            <?php break;
        case 'bool':

            break;
    }
}
?>
