<div class="wrapper">
    <?php echo "<div id='alerts'>" . render('alerts') . "</div>"?>
</div>
<div class="wrapper banner-wrapper flex gap-20" style="margin-bottom: 20px; margin-top: 20px">
    <?=render('banner');?>
</div>

<section class="new-slider wrapper">
    <?php f_('h2', './TEMPLATES/main.php'); ?>
    <h2 class="h2 visually-hidden">Выберите категорию</h2>
    <?php f_end('h2'); ?>
    <div class="swiper cats">
        <div class="swiper-wrapper">
            <?php foreach($main_cats as $v) {
                $name = $v['logo_img'];
                if(!file_exists('./DOWNLOAD/'.$name)) {
                    $name = '20231112-201418_id-2-820538.png';
                }
                ?>
                <div class="swiper-slide" style="overflow: hidden">
                    <?php if(isMobile()) { ?>
                        <a href="/<?=$v['cat_trans']?>"></a>
                        <img alt="<?=$v['category']?>" loading="lazy" width="200" height="200" src="/IMG/img300x300/<?=$name?>">
                    <?php } else { ?>
                        <?php if(editor()) { echo '<div data-id="'.$v['id'].'" data-type="maincat" class="editor-place" title="Для изменения названия кликните по метке, для изменения картинки, перетащите её (картинку) на метку (.jpeg | .png | .webp)"></div>'; } ?>
                        <a href="/<?=$v['cat_trans']?>"></a>
                        <img alt="<?=$v['category']?>" width="200" height="200" loading="lazy" src="/IMG/img300x300/<?=$name?>">
                    <?php } ?>
                    <div class="name-cat count-lines-2"><?=$v['category']?></div>
                </div>
            <?php } ?>
        </div>
        <div class="swiper-pagination"></div>

        <button class="swiper-button-prev btn circle-arrows svg-wrapper"><?=RBS::SVG('right-arrow.svg')?></button>
        <button class="swiper-button-next btn circle-arrows svg-wrapper"><?=RBS::SVG('right-arrow.svg')?></button>

        <div class="swiper-scrollbar"></div>
    </div>
</section>

<?php
echo render('shops');
?>

<!--<button title="Написать в тех-поддержку" onclick="show_hidden_chat()" class="btn-chat btn svg-wrapper">--><?//=RBS::SVG('20230811-114741_id-2-793590.svg')?><!--</button>-->


<?php echo render('list_cards_product', ['preview_cards_product'=>$preview_products]); ?>
<?php


echo render('player');
?>

<!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params-->

<?php echo render('robot', [
    'header_controller'=>true,  // настройка прячет phone-book в header
    'room'=>'',
    'type_room'=>'personal',
    'params'=>[]
]); ?>

<script>
    $(document).ready(function() {
        $(document).ready(function () {
            let swiper = new Swiper('.cats', {
                direction: 'horizontal',
                loop: false,
                slidesPerView: 3,
                slidesPerGroup: 3,
                grid: {
                    rows: 2,
                },
                breakpoints: {
                    1390: {
                        slidesPerView: 10,
                        slidesPerGroup: 10,
                        grid: {
                            rows: 1,
                        },
                    },
                    1200: {
                        slidesPerView: 8,
                        slidesPerGroup: 8,
                        grid: {
                            rows: 1,
                        },
                    },
                    1000: {
                        slidesPerView: 6,
                        slidesPerGroup: 6,
                        grid: {
                            rows: 1,
                        },
                    },
                    950: {
                        slidesPerView: 3,
                        slidesPerGroup: 3,
                        grid: {
                            rows: 2,
                        },
                    },
                },

                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },

            });
        });
    });
</script>