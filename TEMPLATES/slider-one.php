<div class="slider" data-x="0">
    <button onclick="big_size(this)" class="big-sizer svg-wrapper action-btn"><?=RBS::SVG('big-size')?></button>
    <button onclick="slide_to('left', this)" class="rows left-row svg-wrapper action-btn"><?=RBS::SVG('row-left')?></button>
    <button onclick="slide_to('right', this)" class="rows right-row svg-wrapper action-btn"><?=RBS::SVG('row-right')?></button>
    <ul class="sliders">
        <?php foreach($slides as $k=>$v) { ?>
        <li class="slider-one">
            <?php if(isMobile()) { ?>
                <img width="200" height="200" src="/IMG/img300x300/<?=$v['VALUE']?>">
            <?php } else {
                $name = $v['VALUE'];
                if(!file_exists('./RESURSES/BUFF_IMGS/'.date('Y_m_d'))) {
                    mkdir('./RESURSES/BUFF_IMGS/'.date('Y_m_d'));
                }
                if(file_exists('./RESURSES/BUFF_IMGS/'.date('Y_m_d').'/'.$name)) {
                    $buff_img = '/RESURSES/BUFF_IMGS/'.date('Y_m_d').'/'.$name;
                } else {
                    $img = new S_IMG(Core::$DOWNLOAD . $name);
                    $img->bestFit(700, 400)->toFile('./RESURSES/BUFF_IMGS/' . date('Y_m_d') . '/' . $name);
                    $buff_img = '/RESURSES/BUFF_IMGS/'.date('Y_m_d').'/'.$name;
                }
                ?>
                <img width="200" height="200" src="<?=$buff_img?>">
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
</div>

<script>
    setTimeout(function() {
        if($('ul.sliders li').length <= 1) {
            $('.left-row').addClass('disabled');
            $('.right-row').addClass('disabled');
        }
    }, 1);

    function slide_to(side='right', obj) {
        let w = $(obj).closest('.slider').get(0).getBoundingClientRect().width;
        let xx = parseInt($(obj).closest('.slider').attr('data-x'));
        let all_w = +w-$(obj).closest('.slider').find('.sliders').find('li').length * w;
        console.log(all_w);
        if(side === 'right') {
            xx -= w;
        } else {
            xx += w;
        }
        if(xx > 0) { xx = 0; }
        if(xx < all_w) { xx = all_w; }
        $(obj).closest('.slider').find('.sliders').css('transform', 'translateX('+xx+'px)');
        $(obj).closest('.slider').attr('data-x', xx);
    }

    function big_size(obj) {
        $(obj).closest('.slider').toggleClass('big-size-slider');
        let src = '';
        $('.slider img').each(function(e,t) {
            src = $(t).attr('src');
            src = src.split('/')[src.split('/').length-1];
            $(t).attr('src', '/DOWNLOAD/'+src);
        });
    }
</script>
