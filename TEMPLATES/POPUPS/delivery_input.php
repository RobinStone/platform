<?php
$city = $city ?? GEO2IP::IP2STRING(SITE::$ip);
if(SITE::$profil) {
    $delivery = PROFIL::init(Access::userID())->get_attachment('delivery_info', '');
} else {
    $delivery = '';
}
//wtf($delivery, 1);
if($delivery === '') {
    $delivery = [
        'fio'=>'',
        'phone'=>'',
        'address-dell'=>'',
        'comment'=>'',
    ];
}
if(isset($delivery['city'])) {
    $city = $delivery['city'];
}
if(!isset($delivery['fio'])) {
    $delivery['fio'] = '';
}
if(!isset($delivery['phone'])) {
    $delivery['phone'] = '';
}
if(!isset($delivery['address-dell'])) {
    $delivery['address-dell'] = '';
}
if(!isset($delivery['comment'])) {
    $delivery['comment'] = '';
}
?>
<style>
    .form {

    }
    .form input,
    .form textarea {
        background-color: rgb(203, 255, 223);
        display: inline-block;
        min-width: 100%;
        border-radius: 6px;
        border: none;
        padding: 5px 10px;
        box-sizing: border-box;
    }
    div.circle {
        position: absolute;
        z-index: -1;
        background-color: green;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        filter: blur(149px);
        left: -2%;
        top: 44%;
        pointer-events: none;
    }
    div.circle.blue {
        background-color: #002480;
        left: 65%;
        top: 10%;
    }
    .form fieldset {
        box-sizing: border-box;
        display: inline-block;
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, 0.24);
    }
    .form fieldset legend {
        font-size: 14px;
        padding: 3px 7px;
        background-color: #fff;
        border-radius: 40px;
    }
</style>

<div class="form flex gap-5 column">
    <h1 style="padding-right: 60px; margin-bottom: 1em">Ваши данные по доставке, курьеру</h1>
    <div class="circle"></div>
    <div class="circle blue"></div>
    <div class="flex column align-top gap-5 delivery-form">
        <fieldset data-num-id="1">
            <legend>ФИО</legend>
            <input class="delivery-field fields" type="text" placeholder="ФИО" name="fio" value="<?=@$delivery['fio']?>">
        </fieldset>
        <fieldset data-num-id="2">
            <legend>Номер телефона</legend>
            <input class="delivery-field fields" type="tel" placeholder="Номер телефона" name="phone" value="<?=@$delivery['phone']?>">
        </fieldset>
        <fieldset data-num-id="3">
            <legend>Город</legend>
            <input class="delivery-field fields" type="text" list="citys" placeholder="Город" name="city" value="<?=$city?>">
        </fieldset>
        <fieldset data-num-id="4">
            <legend>Адрес доставки</legend>
            <textarea class="delivery-field fields" placeholder="Адрес доставки" name="address-dell"><?=@$delivery['address-dell']?></textarea>
        </fieldset>
        <fieldset data-num-id="5">
            <legend>Коментарий к заказу</legend>
            <textarea class="fields" placeholder="Коментарий к заказу" name="comment"><?=@$delivery['comment']?></textarea>
        </fieldset>
        <fieldset data-num-id="6">
            <legend>Службы доставки</legend>
            <table class="delitery-list">
                <tr>
                    <td><input type="radio" name="delivery-item"></td>
                    <td><img src="./DOWNLOAD/20240930-122950_id-2-143035.png"></td>
                    <td>Служба доставки СДЭК</td>
                </tr>
            </table>
        </fieldset>

        <div class="flex" style="width: 100%; margin-top: 1em; justify-content: right">
            <button onclick="save_delivery(this)" class="btn-just disabled" type="button">Сохранить</button>
        </div>
    </div>
</div>

<script>
    scan_delivery_fields();
</script>