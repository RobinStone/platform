<?php
//wtf($rows, 1);
foreach($rows as $k=>$v) {
    if($v['css'] !== null) {
        $rows[$k]['css'] = unserialize($v['css']);
    } else {
        $rows[$k]['css'] = [];
    }
}
shuffle($rows);
?>
<section class="banner">
    <h2 class="visually-hidden">Банер на первой странице</h2>
    <div id="banner" class="flex gap-30 banner-wrapper swiper" style="position: relative">
        <ul class="banners-list swiper-wrapper">
            <?php
            if(isset($rows)) {
            foreach($rows as $v) { ?>
                <li data-id="<?=$v['id']?>" class="showed swiper-slide banner-item" style="background-image: url(/DOWNLOAD/<?=$v['img']?>)">
                <a href="<?=$v['link']?>" class="banner-item">
                    <div class="banner-content">
                        <?php if(mb_strlen($v['title']) > 2) { ?>
                        <h3 class="text-shadow-white"><?=$v['title']?></h3>
                        <?php } ?>
                        <?php if($v['descr'] && mb_strlen($v['descr']) > 2) { ?>
                        <div class="descr text-shadow-white count-lines-2"><?=$v['descr']?></div>
                        <?php } ?>
                    </div>
                </a>
                </li>
            <?php
            }
            }
            ?>
        </ul>
        <div class="swiper-pagination"></div>

        <button style="z-index: 100; background: none; border: none; outline: none" class="swiper-button-prev swiper-button-prev-banner"></button>
        <button style="z-index: 100; background: none; border: none; outline: none" class="swiper-button-next swiper-button-next-banner"></button>

        <div class="swiper-scrollbar"></div>
    </div>
</section>

<script>
    let banners = <?=json_encode($rows, JSON_UNESCAPED_UNICODE)?>;

    //filter: drop-shadow(5px 5px 5px rgba(0, 0, 0, 0.5));

    for(let i in banners) {
        let css = banners[i]['css'];
        let ban = $('.banner-item[data-id="'+i+'"]').find('a');
        ban.css('justify-content', css['justify']).css('align-items', css['align']).css('padding', css['padding']);
        ban.find('.banner-content').css('text-align', css['text_align']);
        ban.find('.banner-content h3').css('color', css['color']);
        ban.find('.banner-content .descr').css('color', css['color']);
        ban.closest('li').css('filter', css['filter']);
    }
</script>