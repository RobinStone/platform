<?php
$text = $text ?? '';
$type = $type ?? '';

$options = [
    'phone'=>'номеру телефона',
    'login'=>'телеграму',
    'email'=>'почте',
];
?>
<style>
    .message, .message * {
        box-sizing: border-box;
    }
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
    .phone-changer input {
        color: #000000!important;
        font-weight: 600;
    }
    #type-reset {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        border-radius: 200px;
        padding: 0 7px 2px;
        cursor: pointer;
    }
    input[name="field"] {
        font-size: 20px;
        padding: 3px 10px 5px;
        font-weight: 600;
        display: inline-block;
        width: 100%;
        border-radius: 30px;
    }
</style>

<section class="message">
    <h2 style="padding-right: 30px; margin-bottom: 0.5em" class="h2">Восстановление доступа</h2>
    <div class="circle"></div>
    <div class="circle blue"></div>
    <div class="flex column gap-5" style="align-items: flex-start;">
        <div class="flex gap-10 center">
            <span>Восстанавливаем пароль по </span>
            <select onchange="change_type_reset(this)" id="type-reset">
                <?php foreach($options as $k=>$v) {
                    if($type === $k) {
                        echo '<option selected value="'.$k.'">'.$v.'</option>';
                    } else {
                        echo '<option value="'.$k.'">'.$v.'</option>';
                    }
                } ?>
            </select>
        </div>
        <div style="width: 100%">
            <input placeholder="<?=$type?>" name="field" type="text" value="<?=$text?>">
        </div>
        <div class="flex" style="width: 100%">
            <button onclick="reset_pass()" id="reset-btn" style="margin-left: auto" class="btn-just"></button>
        </div>
    </div>
</section>

<script>
    let mode_pass = 'telegramm';

    setTimeout(function() {
        change_type_reset('-');
    }, 100);

    function change_type_reset(obj) {
        let type = '<?=$type?>';
        let inpt = $('input[name="field"]');
        let btn = $('#reset-btn');
        if(obj === '-') {
            setTimeout(function() {
                $('input[name="field"]').val('<?=$text?>');
            }, 200);
        } else {
            type = $(obj).val();
            $('input[name="field"]').val('');
        }
        switch(type) {
            case 'phone':
                inpt.attr('placeholder', 'ваш номер телефона');
                inpt.focus();
                btn.text('отправить СМС с кодом');
                mode_pass = 'sms';
                break;
            case 'login':
                inpt.attr('placeholder', '');
                inpt.addClass('diabled');
                btn.text('Вход через телеграм');
                mode_pass = 'telegram';
                break;
            case 'email':
                inpt.attr('placeholder', 'ваша почта');
                inpt.focus();
                btn.text('отправить КОД на Email');
                mode_pass = 'email';
                break;
        }
    }

    function reset_pass() {
        let field = $('input[name="field"]').val();
        switch(mode_pass) {
            case 'telegram':
                tele_reg_auth();
                say('tele');
                break;
            case 'sms':
                if(VALUES.email_phone_string(field) !== 'phone') {
                    say('Введите корректный номер телефона');
                    return false;
                } else {
                    sms_reg(field);
                    $('#auth-form').addClass('open');
                    close_popup('forgot_pass');
                    setTimeout(function() {
                        setOverlayJust();
                    }, 500);
                }
                break;
            case 'email':
                if(VALUES.email_phone_string(field) !== 'email') {
                    say('Введите корректную почту, привязанную к вашему аккаунту');
                    return false;
                } else {
                    email_code_auth(field);
                    $('#auth-form').addClass('open');
                    close_popup('forgot_pass');
                    setTimeout(function() {
                        setOverlayJust();
                    }, 500);
                }
                break;
        }
    }
</script>

