<table id="down-row" class="admin-down-row">
    <tr>
        <th>
            <div class="minimaze-panel">
                <?php if(Access::scanLevel() >= 6) { ?>
                <button onclick="clear_cache()" title="Очистка кеша" class="btn-gray btn-gray-text not-border micro-btn"><img src="./DOWNLOAD/20240513-195650_id-2-387235.svg"></button>
                <button onclick="send_in_chat()" title="Отправка сообщения в чат" class="btn-gray btn-gray-text not-border micro-btn"><img src="<?=RBS::img('editor.svg')?>"></button>
                <button onclick="send_tele()" title="Отправка сообщения в бот" class="btn-gray btn-gray-text not-border micro-btn"><img src="<?=RBS::img('telegramm.svg')?>"></button>
                <button onclick="create_hesh()" title="Создание хэш-кода для значения" class="btn-gray btn-gray-text not-border micro-btn"><img src="<?=RBS::img('pass_code.svg')?>"></button>
                <button onclick="open_self_edit_access()" title="Временная разблокировка редактирования своей учётной записи" class="btn-gray btn-gray-text not-border micro-btn"><img src="<?=RBS::img('key_access.svg')?>"></button>
                <button onclick="open_cmd()" title="CMD" class="btn-gray btn-gray-text not-border micro-btn"><img src="<?=RBS::img('cmd.svg')?>"></button>
                <?php } ?>
            </div>
        </th>
        <th>
            <div class="profil-info-down">

            </div>
        </th>
        <th>
           <div class="ico-panel" style="min-width: 40vw">

           </div>
        </th>
        <th>
            <div id="actors-pnl" class="flex gap-5 align-center">
                <?php if(Access::scanLevel() >= 6) { ?>
                <button onclick="chat_activator(); $('.phone-book').toggleClass('showed')" class="svg-wrapper btn-gray btn-gray-text not-border micro-btn"><?=RBS::SVG('20230608-152503_id-2-821538.svg')?></button>
                <button onclick="sms_agregator()" title="Остаток балланса по SMS-AERO" class="svg-wrapper btn-gray btn-gray-text not-border micro-btn"><?=RBS::SVG('20240416-185410_id-2-813950.svg')?></button>
                <?php } ?>
            </div>
        </th>
        <th>
            <div class="table-edit-panel icons-panel" style="justify-content: right; gap: 0">
                <?php if(Access::scanLevel() >= 6) { ?>
                <button onclick="change_table('NEW TABLE', null, {x: window.innerWidth-150, y: window.innerHeight-150})" class="btn-gray btn-gray-text not-border micro-btn" title="Создаёт новую таблицу"><span class="img-wrapper"><img src="./IMG/SYS/plus_rect.svg"></span></button>
                <button onclick="tables_list(transform_pos('center'))" class="btn-gray btn-gray-text not-border micro-btn" title="Открывает список доступных таблиц"><span class="img-wrapper"><img src="./IMG/SYS/table.svg"></span></button>
                <? } ?>
            </div>
        </th>
    </tr>
</table>