<?php
$params = $_POST['params'] ?? [];
//wtf($params, 1);
?>
<style>
    .manager-card {
        padding: 5px 10px;
    }
    .manager-card textarea {
        padding: 3px 5px;
        border-radius: 5px;
        display: inline-block;
        min-width: 120px;
        min-height: 80px;
    }
</style>
<div class="flex manager-card column gap-10">
    <?php
    switch($action) {
        case 'разрешение': ?>
            <div class="flex align-center gap-5">
                <input type="text" placeholder="<?php if(!empty($params['target'][1])) { echo $params['target'][1]; } ?>">
            </div>
            <div class="flex" style="padding-bottom: 15px">
                <button onclick="access_to_del_complite(this)" class="margin-left-auto btn-just" style="padding: 2px 10px; font-weight: 100; font-size: 16px">Установить</button>
            </div>
            <?php break;
        case 'сообщение telegram': ?>
            <div class="flex align-center gap-5">
                <input type="text" placeholder="login">
            </div>
            <textarea placeholder="текст сообщения"></textarea>
            <div class="flex column gap-5">
                <div class="micro-text">Через сколько секунд отправить сообщение</div>
                <input type="number" placeholder="секунд">
            </div>
            <div class="flex" style="padding-bottom: 15px">
                <button onclick="add_tele_mess(this)" class="margin-left-auto btn-just" style="padding: 2px 10px; font-weight: 100; font-size: 16px">Установить</button>
            </div>
            <?php break;
        case 'токен': ?>
            <div class="flex align-center gap-5 token-creator">
                <fieldset>
                    <legend>Логин пользователя</legend>
                    <input type="text" placeholder="<?php if(!empty($params['target'][1])) { echo $params['target'][1]; } ?>">
                </fieldset>
                <fieldset>
                    <legend>Перегенерировать токен</legend>
                    <span>ВНИМАНИЕ ! Токен нигде не сохраняется, сохраните его в любом удобном месте</span>
                    <div class="flex gap-10 center">
                        <input style="display: inline-block; min-width: 235px; text-align: center" type="text" placeholder="*************************">
                        <button onclick="token_generate(this)" class="action-btn btn-just">Сгенерировать токен</button>
                    </div>
                </fieldset>

            </div>
            <div class="flex" style="padding-bottom: 15px">
                <button onclick="token_create(this)" class="margin-left-auto btn-just" style="padding: 2px 10px; font-weight: 100; font-size: 16px">Создать</button>
            </div>
            <?php break;
        default:
            echo '<h1>Ошибка определения типа параметра доступа...</h1>';
            break;
    }
    ?>
</div>

<script>
    function add_tele_mess(obj) {
        obj = $(obj).closest('.manager-card');
        let arr = {
            action: 'сообщение telegram',
            login: obj.find('input[type="text"]').val(),
            mess: obj.find('textarea').val(),
            time: obj.find('input[type="number"]').val(),
        };
        console.dir(arr);
        obj.closest('.window').find('.close-window-btn').click();
        SENDER('set_sys_mess', {params: arr}, function(mess) {
            mess_executer(mess, function(mess) {
            });
        });
    }
    function access_to_del_complite(obj) {
        obj = $(obj).closest('.manager-card');
        let arr = {
            action: 'разрешение',
            id: parseInt(obj.find('input[type="text"]').val()),
            key: '<?=$params['key']?>',
            limit: <?=$params['limit']?>,
        };
        console.log('-------------');
        console.dir(arr);
        obj.closest('.window').find('.close-window-btn').click();
        SENDER('set_sys_mess', {params: arr}, function(mess) {
            mess_executer(mess, function(mess) {
            });
        });
    }
    function token_generate(obj) {
        let inpt = $(obj).closest('fieldset').find('input');
        let rnd2 = rnd(10000,99999);
        let rnd3 = rnd(100000,999999);
        md6(rnd2.toString()+rnd3.toString()).then(function(mess) {
            inpt.val(mess);
        });
    }
    function token_create(obj) {
        let login = $(obj).closest('.manager-card').find('div:first-child').find('fieldset:first-child input').val();
        let token = $(obj).closest('.manager-card').find('div:first-child').find('fieldset:nth-child(2) input').val();
        if(login.length < 4) {
            say('Короткий логин!. Минимум 4 символа', 2);
        }

        BACK('api', 'token_create', {login: login, password: token}, function(mess) {
            mess_executer(mess, function(mess) {

            })
        })

    }
</script>