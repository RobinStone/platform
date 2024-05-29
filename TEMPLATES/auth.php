<style>
    #body {
        margin: 0;
    }
    #username,
    #password {
        border-radius: 3px!important;
        padding: 15px 15px 15px 35px!important;
    }
</style>

<section class="auth-section" style="background-image: url('<?=RBS::img('20210817-115550_id-2-170227.png')?>')">
    <div class="card center">
        <form id="login" method="post" class="front" action="/auth">
            <h1><?=Core::$SiteName?></h1>
            <fieldset class="inputs">
                <label class="<?=@$err['login']?>">
                    <?=RBS::SVG('smile.svg')?>
                    <input id="username" type="text" name="login" placeholder="Логин" value="<?=@$_POST['login']?>" autofocus required>
                </label>
                <label class="<?=@$err['password']?>">
                    <?=RBS::SVG('key')?>
                    <input id="password" type="password" name="pass" placeholder="Пароль" value="<?=@$_POST['pass']?>" required>
                </label>
            </fieldset>
            <?php
            if(isset($_POST['auth-wrong'])) {
                echo '<p class="wrong-auth">Таких сочетаний ЛОГИН-ПАРОЛЬ не обнаружено...</p>';
            }
            ?>
            <fieldset class="actions">
                <input type="submit" name="auth" id="submit" value="Вход">
                <button onclick="tele_auth()" type="button" name="tele-auth" id="submit-auth">Вход по <img style="transform: translateY(2px)" width="16" height="16" src="/IMG/SYS/telegramm.svg"></button>
                <?php if(1===1) { ?>
                    <a onclick="return reg_form()" href="">Регистрация</a>
                <?php } ?>
            </fieldset>
        </form>
            <form id="login2" method="post" class="back">
                <div class="hidden-wrapper">
                    <h1>Регистрация</h1>
                    <fieldset class="inputs flex between radio-modul flex-wrap">
                        <label class="sel">
                            <input type="radio" checked="checked" name="type">
                            <span>Мне нужен личный кабинет для покупок</span>
                        </label>
                        <label>
                            <input type="radio" name="type">
                            <span>Я - хочу продать 1-2 товара</span>
                        </label>
                        <label>
                            <input type="radio" name="type">
                            <span>Хочу площадку для рекламы моего ассортимента</span>
                        </label>
                        <label>
                            <input type="radio" name="type">
                            <span>Мне нужен полноценный мгазин для продажи моих товаров</span>
                        </label>
                    </fieldset>
                    <fieldset class="actions flex between gap-10" style="align-items: flex-end; background-color: #fff; padding: 5px 10px 10px; border-radius: 5px">
                        <div onclick="qr_click()" id="qr"></div>
                        <div class="flex column between" style="align-items: end; height: 165px">
                            <span style="font: 15px Arial, Helvetica; line-height: 20px; text-align: right">Для прохождения регистрации, у вас <i style="font: 15px Arial, Helvetica; font-size: 14px; font-style: normal; font-weight: 900">должен быть установлен Telegram.</i> Наведите камеру смартфона на QR-код и запустите бот.</span>
                            <a onclick="$('.card').removeClass('reg'); del_qr(); return false;" href="">Авторизация</a>
                        </div>
                    </fieldset>
                </div>
            </form>
    </div>
</section>
