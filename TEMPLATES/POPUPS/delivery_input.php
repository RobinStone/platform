<?php
$city = $city ?? GEO2IP::IP2STRING(SITE::$ip);
$delivery = PROFIL::init(Access::userID())->get_attachment('delivery_info', '');
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
</style>

<div class="form flex gap-5 column">
    <h1 style="padding-right: 60px; margin-bottom: 1em">Ваши данные по доставке, курьеру</h1>
    <div class="circle"></div>
    <div class="circle blue"></div>
    <div class="flex column align-top gap-5 delivery-form">
        <input class="delivery-field fields" type="text" placeholder="ФИО *" name="fio" value="<?=@$delivery['fio']?>">
        <input class="delivery-field fields" type="tel" placeholder="Номер телефона *" name="phone" value="<?=@$delivery['phone']?>">
        <input class="delivery-field fields" type="text" list="citys" placeholder="Город *" name="city" value="<?=$city?>">
        <textarea class="delivery-field fields" placeholder="Адрес доставки *" name="address-dell"><?=@$delivery['address-dell']?></textarea>
        <textarea class="fields" placeholder="Коментарий к заказу" name="comment"><?=@$delivery['comment']?></textarea>
        <div class="flex" style="width: 100%; margin-top: 1em; justify-content: right">
            <button onclick="save_delivery(this)" class="btn-just disabled" type="button">Подтвердить</button>
        </div>
    </div>
</div>

<script>
    scan_delivery_fields();
</script>