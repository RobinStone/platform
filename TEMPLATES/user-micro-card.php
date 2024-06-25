<div class="user-micro-card flex gap-20 b-2" data-id="<?=$id?>">
    <img src="<?=$img?>" width="50" height="50" style="display: inline-block; border-radius: 50%; object-fit: cover">
    <div class="user-card-content">
        <div class="user-card-name">
            <a style="color: #0F9640;" href="/owner?o=<?=$P->get_field('login')?>">
                <?=$saler_name?>
            </a>
        </div>
        <div class="on-site-from">На сайте с <?=$data_reg?></div>
        <?php if(isset($self)) { ?>
            <div onclick="open_tab('subscribes_mans')" style="font-size: 13px" class="on-site-from underline">Подписчики (<?=$subs_count?>)</div>
            <div onclick="open_tab('subscribes')" style="font-size: 13px" class="on-site-from underline">Подписок (<?=$my_subscr_count?>)</div>
        <?php } ?>
        <div class="rewiews-block flex gap-5 align-center">
            <span><?=$stars?></span>
            <span class="svg-wrapper"><?=RBS::SVG('star')?></span>
            <a href="/reviews?for=<?=$id?>" style="color: #1F1A1A; text-decoration: none"><span class="review-counts underline"><?=$reviews?> отзыв (ов)</span></a>
            <?php if(!isset($self) && Access::userID() > 0) { ?>
            <button onclick="subscribes(<?=$id_write_card?>)" style="color: #018536; margin-left: auto; font-size: 16px; font-weight: 600" class="btn-link underline"><?=$writer_text?></button>
            <?php } ?>
        </div>
    </div>
</div>