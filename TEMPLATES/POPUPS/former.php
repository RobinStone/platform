<?php
$title = 'UNKNOWN...';
$access = true;
$items = [];
$read_only = true;

switch ($type ?? '') {
    case 'delivery_info':
        $title = 'Информация о доставке';
        if($row = SQL_ONE_ROW(q("SELECT order_code, id_user, shop_id, status FROM orders WHERE id=".(int)($order_id ?? -1)))) {
            $info = $row;
            $row = unserialize($row['order_code'])['delivery_info'];
            $order_status = $info['status'];
//            wtf($order_status, 1);

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

                if(isset($row['cdek_point_from']) && empty($row['cdek_point_from'])) {
                    $items[] = [
                        'type'=>'button',
                        'title'=>'Укажите точку СДЭК, откуда будет отправлена посылка.',
                        'name'=>'button',
                        'placeholder'=>'Выбрать пункт СДЭК',
                        'value'=>'Указать точку СДЭК',
                        'params'=>[
                            'action'=>'set_cdek_start_point('.$order_id.')',
                            'ico'=>'/IMG/img100x100/20240420-115255_id-2-488961.png'
                        ]
                    ];
                }
                if(isset($row['cdek_point_from']) && !empty($row['cdek_point_from'])) {

                    $fragment = "";

                    // разрешает менять точку старта СДЭК-заказа только если подходит статус
                    $access_statuses = [ORDER_STATUS::CREATED->value, ORDER_STATUS::PAYED->value];
                    if(in_array($order_status, $access_statuses)) {
                        $fragment = "<button style='margin-top: 15px; margin-left: auto' onclick='set_cdek_start_point(".$order_id.")' class='btn btn-just'>Изменить</button>";
                    }

                    $items[] = [
                        'type'=>'info',
                        'title'=>'Отправка по СДЭК',
                        'name'=>'panel',
                        'placeholder'=>'Информация о доставке через СДЭК',
                        'value'=> "<div style='padding: 5px 10px; background-color: #ffffb0'>Необходимо отнести посылку по адресу:<br><b>" .$row['cdek_point_from']['address']."</b>".$fragment."</div><br><br>Посылка будет доставлена по адресу:<br><b>".$row['cdek_point_to']['address']."</b>",
                        'params'=>[]
                    ];
                }

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
        render_row($item['type'], $item['name'], $item['value'], $item['title'], $item['placeholder'], $item['params'] ?? []);
    }
?>
</section>
<?php } else { echo '<section class="former-cont"><h1>'.$title.'</h1></section>'; } ?>


<?php
function render_row(string $type, string $name, string $value, $title='', $placeholder='', $params=[]) {
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
        case 'button': ?>
            <fieldset>
                <legend><?=$title ?? ''?></legend>
                <div class="flex between" style="border: 3px solid yellow; box-sizing: border-box; width: 100%; background-color: #ffb2b2; padding-right: 10px">
                    <button onclick="<?=$params['action']?>" class="btn btn-just" title="<?=$placeholder?>"><?=$value?></button>
                    <img class="shake" width="30" height="25" src="<?=$params['ico']?>">
                </div>
            </fieldset>
            <?php break;
        case 'info': ?>
            <fieldset>
                <legend><?=$title ?? ''?></legend>
                <div class=""><?=$value?></div>
            </fieldset>
            <?php break;
    }
}
?>
