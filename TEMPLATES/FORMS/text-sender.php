<style>
    .form {
        padding: 15px;
    }
    .form input {
        padding: 4px 10px!important;
    }
    .form textarea {
        padding: 10px;
    }
</style>

<div style="width: 400px;" class="form">
    <div class="flex column gap-10">
        <label class="flex gap-10 align-center">
            <span class="">Последний ID телеграмм-польз.</span>
            <input type="text" value="<?=SUBD::get_last_row('tele')['sender']?>">
        </label>
        <textarea style="resize: none; display: inline-block; max-width: 370px; height: 100px;" placeholder="Ваш ответ"></textarea>
        <div class="flex gap-10 between">
            <button class="btn-just btn-sender">Отправить</button>
            <button onclick="open_table('tele')" class="btn-just Telegramm flex gap-5 align-center">Открыть<img width="25" height="25" src="/IMG/SYS/telegramm.svg"></button>
        </div>
    </div>
</div>