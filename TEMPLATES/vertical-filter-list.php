<?php
INCLUDE_CLASS('shops', 'favorite');
$arr = FAVORITE::verify_like_products($arr, Access::userID(), 'id_shop', 'id_product');

if(Access::scanLevel() < 1) {
    $basket = $_COOKIE['basket'] ?? '';
} else {
    $basket = $_COOKIE['basket-id-user'] ?? '';
}

if(isset($basket) && $basket !== '') {
    $B = new BASKET($basket);
    $arr = $B->verify_in_basket_product($arr);
} else {
    foreach($arr as $k=>$v) {
        $v['IN_BASKET'] = 0;
        $arr[$k] = $v;
    }
}
?>
<ul class="flex column vertical-list-products">
    <?php foreach($arr as $k=>$v) {
        $shop_id = (int)explode('_', $k)[0];
        $product_id = (int)$v['id_product'];
        $link = '/'.$v['main_cat_trans'].'/'.$v['under_cat_trans'].'/'.VALUES::translit($v['action_list_trans']).'/'.$v['trans'].'?s='.$shop_id.'&prod='.$product_id;
        ?>
    <li class="vertical-list-item">
        <div class="flex center img-wrapper">
            <img loading="lazy" src="/IMG/img100x100/<?=$v['FILES'][0]?>" width="100" height="100">
        </div>
        <div class="text-block">
            <div class="product-name">
                <a class="action-btn" href="<?=$link?>">
                    <?=$v['name']?>
                </a>
            </div>
            <div class="location"><?=$v['PLACE']?></div>
            <div class="descr"><?=$v['DESCR']?></div>
        </div>
        <div class="controls flex between column">
            <div class="price"><?=VALUES::price_format($v['PRICE'])?> P</div>
            <a href="<?=$link?>" class="btn action-btn">Подробнее</a>
        </div>
        <div class="likes">
            <?php
            $hart = '';
            if($v['LIKE'] == 1) {
                $hart = 'hart-red';
            }
            if(!SHOP::is_my_shop($shop_id) && Access::userID() > 0) {
                echo '<button onclick="favorite_check(this, ' . $shop_id . ', ' . $product_id . ')" class=" action-btn like"><span class="svg-wrapper ' . $hart . '">' . RBS::SVG('hart_white') . '</span></button>';
            } else {
                echo '<button class="like disabled" style="opacity: 0.2"><span class="svg-wrapper ">' . RBS::SVG('hart_white') . '</span></button>';
            }
            if(!SHOP::is_my_shop($shop_id)) {
                if (isset($v['IN_BASKET'])) {
                    if ($v['IN_BASKET'] === 0) {
                        echo '<button onclick="in_basket(\'' . $k . '\', this)" class="basket action-btn">' . RBS::SVG('bag_box.svg') . '</button>';
                    } else {
                        echo '<button onclick="in_basket(\'' . $k . '\', this)" class="basket action-btn">' . RBS::SVG('basket_on.svg') . '</button>';
                    }
                }
            }
            ?>
        </div>
    </li>
    <?php } ?>
</ul>