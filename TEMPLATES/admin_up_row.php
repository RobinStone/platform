<table id="up-row" class="admin-down-row">
    <tr>
        <th>
            <div class="profil-info">

            </div>
        </th>
        <th style="width: 100%">
            <div class="minimaze-panel" style="justify-content: left; gap: 3px">
                <?php $apps->render_menu_apps();?>
            </div>
        </th>
        <th id="menu-support">
        </th>
        <th>
            <div class="flex gap-5 align-center mess-container">
                <?php if(Access::scanLevel() >= 6) { ?>
                        <?php if(Access::scanModality() === 'super-admin') { ?>
                            <button onclick="open_table('access')" title="Поля ACCESS" class="mess-alert btn-gray btn-gray-text not-border micro-btn">
                                <img width="20" height="20" src="/DOWNLOAD/20240510-182350_id-2-874745.svg">
                            </button>
                        <?php } ?>
                <button onclick="access_manager()" title="Менеджер доступов" class="mess-alert btn-gray btn-gray-text not-border micro-btn">
                    <img width="20" height="20" src="/DOWNLOAD/20240506-210053_id-2-590915.svg">
                </button>
                <button onclick="open_table('file')" title="Таблица файлов" class="mess-alert btn-gray btn-gray-text not-border micro-btn">
                    <img width="20" height="20" src="/DOWNLOAD/20230505-205450_id-2-356305.svg">
                </button>
                <?php }
                if(Access::scanModality() === 'super-admin') { ?>
                    <button onclick="open_table('main')" title="Переменные среды" class="mess-alert btn-gray btn-gray-text not-border micro-btn">
                        <img width="20" height="20" src="/DOWNLOAD/20230505-205115_id-2-938272.svg">
                    </button>
                    <button onclick="open_table('tables_list')" title="Параметры таблиц" class="mess-alert btn-gray btn-gray-text not-border micro-btn">
                        <img width="20" height="20" src="/DOWNLOAD/e96873fcb7c02ac28c0534d907ffe37e.svg">
                    </button>
                <?php }
                if(Access::scanLevel() >= 6) { ?>
                    <button onclick="open_table('online')" title="Сейчас на сайте" class="mess-alert btn-gray btn-gray-text not-border micro-btn">
                        <img width="20" height="20" src="/DOWNLOAD/20240108-141103_id-2-690378.svg">
                    </button>
                <?php }
                ?>
            </div>
        </th>
        <th>
            <div class="table-edit-panel icons-panel" style="justify-content: space-between">
                <button onclick="edit_profil()" class="btn-gray btn-gray-text not-border micro-btn btn-img-with-text">
                    <img width="20" height="20" src="<?=$avatar?>">
                    <span style="margin-right: auto"><?=$P->get_field('login');?></span>
                </button>
                <button title="Выход из админ-панели" onclick="location.href='/auth/exit'" class="btn-gray btn-gray-text not-border micro-btn"><img src="./IMG/SYS/exit.svg"></button>
            </div>
        </th>
    </tr>
</table>