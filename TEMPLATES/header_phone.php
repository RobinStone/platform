<header class="header wrapper">
    <div class="up-row-header flex align-center between">
        <button onclick="show_hide_menu()" class="svg-wrapper btn btn-white visually-hidden">
            <?=RBS::SVG('burger.svg'); ?>
        </button>
        <a class="back-row" href="javascript:history.back()">
            <img src="/DOWNLOAD/20230623-135730_id-2-501077.svg">
        </a>
        <div class="logo-row">
            <a href="/" class="svg-wrapper" style="display: inline-block; height: 20px"><?=RBS::SVG('20240815-150558_id-2-611390.svg')?></a>
        </div>
        <?php if(Access::scanLevel() === 0) { ?>
        <button onclick="auth()" class="svg-wrapper btn btn-white svg-red profil-btn-action">
            <?=RBS::SVG('user.svg'); ?>
        </button>
        <?php } else { ?>
            <button onclick="location.href='/profil'" class="svg-wrapper btn btn-white profil-btn-action">
                <img src="<?=$user_img?>">
            </button>
        <?php } ?>
    </div>
    <?php
    if(isset(SITE::$params['TEMPLATES'][2]) && SITE::$params['TEMPLATES'][2] !== 'product') { ?>
    <div class="find-field flex align-center between gap-10">
        <input id="finder" oninput="finder(this)" placeholder="Поиск по сайту" type="text">
        <button id="btn-finder" onclick="search_context(this)" class="btn flex align-center between gap-15">Найти</button>
    </div>
    <?php } ?>
    <button onclick="create_my_order()" class="btn flex align-center between visually-hidden" style="width: 100%; min-height: 48px">Разместить объявление</button>
</header>

<div class="quick_access flex around align-center show">
    <a id="q-home" href="/" class="quick-btn svg-wrapper action-btn"><?=RBS::SVG('q-home.svg')?></a>
    <a id="q-best" href="/profil?title=favorite" class="quick-btn svg-wrapper action-btn"><?=RBS::SVG('q-best.svg')?></a>
    <a onclick="open_close_jplayer(); $(this).toggleClass('sel')" id="q-player" class="quick-btn svg-wrapper action-btn"><?=RBS::SVG('q-player.svg')?></a>
    <a onclick="$('.phone-book').toggleClass('showed-phone-book'); $(this).removeClass('shake'); $(this).toggleClass('sel')" id="q-mess" class="quick-btn svg-wrapper action-btn">
        <?=RBS::SVG('q-mess.svg')?>
        <span id="count-alert"></span>
    </a>
    <a id="q-profil" href="/basket" class="quick-btn svg-wrapper action-btn">
        <?=RBS::SVG('20230609-193649_id-2-923913.svg')?>
        <?php
        if($count_in_basket > 0) {
            echo '<span id="basket-count">'.$count_in_basket.'</span>';
        }
        ?>
    </a>
    <a class="action-btn" onclick="theme_checker()">
        <img width="30" height="30" src="/DOWNLOAD/20240705-121447_id-2-736237.svg">
    </a>
</div>

<script>
    function theme_checker() {
        if($('#body-s').hasClass('dark')) {
            setCookies('theme', '');
        } else {
            setCookies('theme', 'dark');
        }
        $('#body-s').toggleClass('dark');
    }
</script>