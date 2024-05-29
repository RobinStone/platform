<?php
if(Access::scanLevel() > 0) {
    $P = PROFIL::init(Access::userID());
    $cash = $P->get('cash', '0');
} else {
    $cash = 0;
}

//wtf($arr, 1);
$PROPS = new PROPS_COMMANDER($arr['arr']['VALS']);

//$country = Access::scanCountry($_SERVER['REMOTE_ADDR']);
//t($country);
$arrm = GEO2IP::get_info($_SERVER['REMOTE_ADDR']);
$code = 'ru';
if(isset($arrm['country']['iso'])) {
    $code = $arrm['country']['iso'];
}
?>


<div class="back-fone flex center">
    <div class="buy-form">
        <div class="header-line flex align-center flex between">
            <span>Детали покупки <?=$code?></span>
            <button onclick="closed_form()" class="action-btn" style="color: #fff; margin-right: 10px; font-size: 2em; background: none; border: none">🞩</button>
        </div>
        <div class="buy-form-content">
            <div class="map" id="order-map"></div>
            <div class="flex phone-inputer-block">
                <span class="table-font">Телефон</span>
                <input id="phoner" type="tel">
            </div>
            <ul class="flex between gap-40 filter-block">
                <li>
                    <table class="fields-format">
                        <tr>
                            <td>Получатель</td>
                            <td><input type="text"></td>
                        </tr>
                        <tr id="data-address" style="display: none">
                            <td>Адрес</td>
                            <td><input type="text"></td>
                        </tr>
                        <tr id="data-ofice" style="display: none">
                            <td>Кв./оф.</td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td>Комментарий</td>
                            <td><textarea></textarea></td>
                        </tr>
                    </table>
                </li>
                <li>
                    <ul class="fields-format">
                        <li>
                            <div data-type="self" class="time-delivery">
                                <label class="flex between">
                                    <button title="Покупатель самостоятельно приходит в магазин или пункт выдачи и забирает свой товар." onclick="show_title(this)">?</button>
                                    <span>Самовывоз</span>
                                    <input checked="checked" type="radio" name="pay-prefferences">
                                </label>
                            </div>
                            <div data-type="courier" class="time-delivery">
                                <label class="flex between">
                                    <button title="Договаривайтесь с продавцом о месте и времени передачи товара самостоятельно. Деньги продавец получит только после того, как вы подтвердите успешное получение товара." onclick="show_title(this)">?</button>
                                    <span>Курьером продавца</span>
                                    <input type="radio" name="pay-prefferences">
                                </label>
                            </div>
                            <div data-type="service" class="time-delivery">
                                <label class="flex between">
                                    <button title="Деятельность, оказываемая одним лицом (исполнителем) другому лицу (заказчику) с целью удовлетворения его потребностей или решения определенной проблемы." onclick="show_title(this)">?</button>
                                    <span>Это услуга</span>
                                    <input type="radio" name="pay-prefferences">
                                </label>
                            </div>
                            <div id="cont">Покупатель самостоятельно приходит в магазин или пункт выдачи и забирает свой товар.</div>
                        </li>
                    </ul>
                </li>
                <li class="flex column between" style="align-items: flex-start">
                    <div class="price" style="font-size: 24px; line-height: 24px">К оплате - <?=VALUES::price_format($PROPS->get_all_props_at_field_name('Стоимость', true)['VALUE'])?> P</div>
                    <div>Способ оплаты</div>
                    <div>
                        <select>
                            <option>Со счёта - <?=$cash?>Р</option>
                            <option>Банковской картой</option>
                            <option>Наличными в пункте выдачи</option>
                            <option>Наличными курьеру</option>
                        </select>
                    </div>
                    <button class="btn btn-shop">Купить</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="complaint">
        <header class="flex between">
            <span>Жалоба на объявление</span>
            <button onclick="closed_complaint_form()" class="close-map-btn"></button>
        </header>
        <ul>
            <li>
                <label class="flex gap-15">
                    <input name="comp" type="radio">
                    <span>Товар продан</span>
                </label>
            </li>
            <li>
                <label class="flex gap-15">
                    <input name="comp" type="radio">
                    <span>Неверная цена</span>
                </label>
            </li>
            <li>
                <label class="flex gap-15">
                    <input name="comp" type="radio">
                    <span>Неверное описание, фото</span>
                </label>
            </li>
            <li>
                <label class="flex gap-15">
                    <input name="comp" type="radio">
                    <span>Неверный адрес</span>
                </label>
            </li>
            <li>
                <label class="flex gap-15">
                    <input name="comp" type="radio">
                    <span>Не дозвониться</span>
                </label>
            </li>
            <li>
                <label class="flex gap-15">
                    <input name="comp" type="radio">
                    <span>Объявление нарушает правила <?=Core::$SiteName?></span>
                </label>
            </li>
            <li>
                <label class="flex gap-15">
                    <input name="comp" type="radio">
                    <span>Мошенник</span>
                </label>
            </li>
            <li onclick="$(this).find('.text-comp').addClass('vis')">
                <label class="flex gap-15">
                    <input name="comp" type="radio">
                    <span>Другое</span>
                </label>
                <textarea class="text-comp"></textarea>
            </li>
        </ul>
        <div class="flex" style="width: 100%; ">
            <button onclick="send_compare(this)" style="margin-left: auto; margin-right: 15px" class="btn disabled">Отправить</button>
        </div>
    </div>
</div>

<script>
    code = '<?=$code?>';

    $(document).ready(function() {
        console.log(phone_field);
    });

    phone_field = $('.phone-inputer-block');
    table_fields = $('.fields-format');

    function change_size_fields() {
        phone_field.css('max-width', table_fields.width());
    }

    $(window).resize(function() {
        console.log("Размер окна браузера был изменен");
        change_size_fields();
    });
</script>