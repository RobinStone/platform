<div class="wrapper">    <?php echo "<div id='alerts'>" . render('alerts') . "</div>"?></div><section class="wrapper product">    <?=render('bread-crumbs', ['items'=>$items]);?>    <h1><?=Core::$h1;?></h1>    <div class="flex align-center gap-20 filter-row">        <div class="flex align-center gap-15 item-info">            <?php echo render('banner', ['current'=>$current]); ?>        </div>        <div class="filter-buttons-wraper flex gap-20 align-center">            <button onclick="sort_open()" class="round-btn flex align-center gap-20 action-btn">                <span>Сортировка</span>                <span class="svg-wrapper"><?=RBS::SVG('up_down_rows')?></span>            </button>            <button onclick="filter_open()" class="round-btn flex align-center gap-20 action-btn">                <span>Фильтры</span>                <span class="svg-wrapper"><?=RBS::SVG('rollers')?></span>            </button>            <div class="filter-buttons-content">                <ul class="flex column gap-15 invisible">                    <li onclick="set_filter('date')" data-filter-name="date"><label class="action-btn filter-input-row flex gap-10 align-center"><input <? if($filter_name==='date'){echo'checked';} ?> name="filter-order" type="radio"><span>По дате обновления</span></label></li>                    <li onclick="set_filter('place')" data-filter-name="place"><label class="action-btn filter-input-row flex gap-10 align-center"><input <? if($filter_name==='place'){echo'checked';} ?> name="filter-order" type="radio"><span>Ближе всех</span></label></li>                    <li onclick="set_filter('price_min')" data-filter-name="price_min"><label class="action-btn filter-input-row flex gap-10 align-center"><input <? if($filter_name==='price_min'){echo'checked';} ?> name="filter-order" type="radio"><span>По цене мин.</span></label></li>                    <li onclick="set_filter('price_max')" data-filter-name="price_max"><label class="action-btn filter-input-row flex gap-10 align-center"><input <? if($filter_name==='price_max'){echo'checked';} ?> name="filter-order" type="radio"><span>По цене макс.</span></label></li>                </ul>                <div id="mobile-side-menu"></div>            </div>        </div>    </div>    <sectopn class="swiper" style="margin-top: 20px">        <div class="swiper-wrapper">            <?php foreach($menu_items as $k=>$v) { ?>                <div class="swiper-slide">                    <?php if(editor()) { echo '<div data-id="'.$v['ID'].'" data-type="'.$v['TYPE'].'" class="editor-place" title="Для изменения названия кликните по метке, для изменения картинки, перетащите её (картинку) на метку (.png | .webp)"></div>'; } ?>                    <a style="font-size: 14px; font-weight: 600" href="<?=$before_addr.$v['TRANS']?>">                        <?php if($v['LOGO_IMG'] !== '-' && $v['LOGO_IMG'] !== '20230508-154105_id-2-312764.svg') { ?>                            <img loading="lazy" src="/IMG/img300x300/<?=$v['LOGO_IMG']?>">                        <?php } ?>                        <span class="count-lines-3"><?=$v['NAME']?></span>                    </a>                </div>            <?php } ?>        </div>        <button class="swiper-button-prev btn circle-arrows svg-wrapper"><?=RBS::SVG('right-arrow.svg')?></button>        <button class="swiper-button-next btn circle-arrows svg-wrapper"><?=RBS::SVG('right-arrow.svg')?></button>    </sectopn></section><?php if(isMobile()) {    echo render('list_cards_product', ['preview_cards_product'=>$preview_products, 'title'=>'']);} else { ?>    <section class="wrapper vertical-list">        <h2 class="h2">Мы подобрали для Вас:</h2>        <div class="columns flex gap-15">            <div class="filter grey-panel">                <img src="/DOWNLOAD/20230609-201051_id-2-217564.gif" width="50" height="50">            </div>            <?=render('vertical-filter-list', ['arr'=>$preview_products]); ?>        </div>    </section><?php } ?><!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params--><?php echo render('robot', [    'header_controller'=>true,  // настройка прячет phone-book в header    'room'=>'',    'type_room'=>'personal',    'params'=>[]]); ?><script>    let filter_type = '<?=$type_page?>';    $(document).ready(function() {        $('#place-for-insert-filter').append($('.filter-buttons-wraper'));    });    function sort_open() {        $('.filter-buttons-content').toggleClass('open');        if($('.filter-buttons-content ul').hasClass('invisible')) {            $('.filter-buttons-content ul').removeClass('invisible');        } else {            setTimeout(function() {                $('.filter-buttons-content ul').addClass('invisible');            }, 1000);        }    }    function filter_open() {        $('.filter-buttons-content').toggleClass('open');        if($mobile) {            upload_side_menu_filter('mobile');        }    }    $(document).ready(function() {        $(document).ready(function () {            let swiper = new Swiper('.swiper', {                direction: 'horizontal',                loop: false,                slidesPerView: 3,                breakpoints: {                    1390: {                        slidesPerView: 10,                        slidesPerGroup: 10,                    },                    1200: {                        slidesPerView: 8,                        slidesPerGroup: 8,                    },                    1000: {                        slidesPerView: 6,                        slidesPerGroup: 6,                    },                    300: {                        slidesPerView: 3,                        slidesPerGroup: 3,                        grid: {                            rows: 2,                        },                    },                },                navigation: {                    nextEl: '.swiper-button-next',                    prevEl: '.swiper-button-prev',                },            });        });    });</script>