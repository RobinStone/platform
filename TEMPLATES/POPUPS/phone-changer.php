<?php

?>
<style>
    .circle {
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
    .circle.blue {
        background-color: #002480;
        left: 65%;
        top: 10%;
    }
    .phone-changer {
        font-size: 20px;
    }
    .phone-changer input {
        color: #000000!important;
        font-weight: 600;
    }

    @media screen and (max-width: 950px) {
        .phone-changer {
            max-width: 100vw;
            max-height: calc(100vh - 70px);
            overflow-y: auto;
        }
    }

</style>

<section class="phone-changer">
    <h2 style="padding-right: 30px" class="h2">Изменение телефонного номера</h2>
    <div class="circle"></div>
    <div class="circle blue"></div>
    <div>
        <p>
            Изменение номера телефона.<br>(изменить номер телефона можно только 2 раза в сутки)
        </p>
        <ol style="list-style-type: decimal;">
            <li style="list-style-type: decimal">Введите номер телефона в поле <b>"Новый номер"</b></li>
            <li style="list-style-type: decimal">Нажмите на кнопку <b>"Получить код"</b></li>
            <li style="list-style-type: decimal"><b>Дождитесь СМС</b> с кодом подтверждения</li>
            <li style="list-style-type: decimal">Введите полученный код в поле <b>"Код подтверждения"</b></li>
            <li style="list-style-type: decimal">Нажмите на кнопку <b>"Изменить номер"</b></li>
        </ol>
        <fieldset>
            <legend>Введите номер телефона</legend>
            <div class="flex between gap-10 flex-wrap">
                <input id="new-phone" class="input input-phone" type="tel" name="phone" placeholder="Новый номер">
                <button onclick="access_to_change_phone()" type="button" class="btn-just">Получить код</button>
            </div>
        </fieldset>
        <fieldset id="code" class="disabled" style="margin-top: 10px">
            <legend>Введите код подтверждения</legend>
            <div class="flex between gap-10 flex-wrap">
                <input class="input" type="number" name="phone" placeholder="Код подтверждения">
                <button onclick="change_phone()" type="button" class="btn-just">Изменить номер</button>
            </div>
        </fieldset>
    </div>
</section>

<script>
    let buffer_phone_number = '';
    function access_to_change_phone() {
        let number = $('#new-phone').val();
        number = number.replace(/\D/g, '');
        if(number.length >= 11 && number.length <= 12) {
            say('Ожидайте...');
            SENDER('access_to_change_phone', {phone: number}, function(mess) {
                mess_executer(mess, function(mess) {
                    buffer_phone_number = number;
                    $('#code').removeClass('disabled');
                    $('#code input').focus();
                });
            });
        } else {
            say('Не верный формат номера!..', 2);
        }
    }

    function change_phone() {
        let number = $('#code input').val();
        number = number.replace(/\D/g, '');
        if(number.length === 5) {
            say('Ожидайте...');
            SENDER('change_phone', {code: number}, function(mess) {
                mess_executer(mess, function(mess) {
                    close_popup('phone-changer');
                    say('Номер успешно подтверждён...');
                    $('td[data-name="phone"] span').text(buffer_phone_number);
                });
            });
        } else {
            say('Не верный формат кода!..', 2);
        }
    }
</script>