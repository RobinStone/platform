<?php
$my_place = SITE::$my_place;
?>
<header class="header wrapper">
    <div class="up-row-header flex align-center between">
        <div class="location flex align-center gap-5 action-btn">
            <img width="15" height="15" src="/DOWNLOAD/20230530-194740_id-2-624999.svg">
            <span style="display: inline-block; min-width: 170px; position: relative" onclick="change_my_place(this)" title="Город указан не верно?" data-my_place="<?=$my_place[1]?>">г. <?=$my_place[0]?></span>
        </div>
        <div class="logo-row">
            <a href="/" class="svg-wrapper" style="display: inline-block; height: 26px"><?=RBS::SVG('20230814-102825_id-2-423088.svg')?></a>
        </div>
        <ul class="action-menu flex align-center gap-20">
            <li class="action-btn" onclick="theme_checker()">
                <img width="30" height="30" src="/DOWNLOAD/20240705-121447_id-2-736237.svg">
            </li>
            <?php if(Access::scanLevel() > 0) { ?>
<!--            <li onclick="$(this).toggleClass('show-phone-book')" class="flex align-center gap-10 action-btn li-phone-book" style="position: relative">-->
            <li onclick="location.href='/chat'" class="flex align-center gap-10 action-btn li-phone-book" style="position: relative">
                <img onclick="$(this).removeClass('shake')" id="mail-ico" width="30" height="30" src="/DOWNLOAD/20230530-211804_id-2-746301.svg">
                <div>(<span id="count-alert">0</span>)</div>
            </li>
            <?php if(Access::scanLevel() >= 1) { ?>
            <li onclick="location.href='/profil?title=favorite'" class="flex align-center gap-10 action-btn">
                <img width="30" height="30" src="/DOWNLOAD/20230530-225837_id-2-950529.svg">
                <span>(<?=$favorite_count?>)</span>
            </li>
            <?php } ?>
            <li onclick="location.href='/basket'" class="flex align-center gap-10 action-btn">
                <img width="30" height="30" src="/DOWNLOAD/20230609-193649_id-2-923913.svg">
                <span>(<b id="basket-count"><?=$count_in_basket?></b>)</span>
            </li>
            <li onclick="location.href='/profil'" class="flex align-center gap-10 action-btn">
                <img style="object-fit: cover" width="30" height="30" src="<?=$user_img?>">
                <span>Профиль</span>
            </li>
            <?php } else { ?>
                <li onclick="location.href='/basket'" class="flex align-center gap-10 action-btn">
                    <img width="30" height="30" src="/DOWNLOAD/20230609-193649_id-2-923913.svg">
                    <span>Корзина (<b id="basket-count"><?=$count_in_basket?></b>)</span>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="bottom-row-header flex align-center between gap-15">
        <button id="menus" onclick="show_hide_menu()" class="btn flex align-center between gap-15">
            <img width="30" height="30" src="/DOWNLOAD/20230530-211804_id-2-236725.svg">
            <span>Категории</span>
        </button>
        <div class="find-field flex align-center between gap-10" style="position: relative">
            <input id="finder" oninput="finder(this)" placeholder="Поиск по сайту" type="text">
            <button onclick="search_context(this)" class="btn flex align-center between gap-15">Найти</button>
        </div>
        <?php
        $access_arr = [
            'MAIN-CAT',
            'UNDER-CAT',
            'ACTION-LIST',
        ];
        if(!in_array(Core::$META_TYPE, $access_arr)) { ?>
        <button id="order-creator" onclick="create_my_order()" class="btn flex align-center between gap-15">Разместить объявление</button>
        <?php } else { ?>
            <div id="place-for-insert-filter"></div>
        <?php } ?>
        <?php
        if(Access::scanLevel() > 0) {
            echo '<button onclick="location.href=\'/auth/exit\'" class="btn flex align-center between gap-15">Выход</button>';
        } else {
            echo '<button onclick="auth()" class="btn flex align-center between gap-15">Войти</button>';
        }
        ?>
    </div>
<!--    --><?php //include_once './TEMPLATES/menu.php'; ?>
</header>

<script>
    $(document).on('click', '.menu-wrapper.flex.between.align-center.wrapper.opener', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', '.overlay-gray.glass', function(e) {
        show_hide_menu();
    });

    function theme_checker() {
        if($('#body-s').hasClass('dark')) {
            setCookies('theme', '');
        } else {
            setCookies('theme', 'dark');
        }
        $('#body-s').toggleClass('dark');
    }
</script>