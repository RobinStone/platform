<?php
$v['trans'] = VALUES::translit($v['name']);
//wtf($v, 1);
echo '<li data-indexer="'.$v['id'].'" data-id="'.$shop_id.'_'.$product_id.'" data-name="'.VALUES::translit($v['name']).'" data-list="'.$v['action_list_trans'].'">';
if(isset($v['PROPS']['images'][0]['value'])) {
    echo '<img loading="lazy" width="333" height="226" src="/IMG/img300x300/'.$v['PROPS']['images'][0]['value'].'">';
}
$hart = '';
if($v['LIKE'] == 1) {
    $hart = 'hart-red';
}

echo '<div class="text-info">';
echo '<div class="wrapper-link flex between gap-5"><a href="/'.$v['main_cat_trans'].'/'.$v['under_cat_trans'].'/'.VALUES::translit($v['action_list_trans']).'/'.$v['trans'].'?s='.$shop_id.'&prod='.$product_id.'">'.$v['name'].'</a>';
if(!SHOP::is_my_shop($shop_id) && Access::userID() > 0) {
    echo '<button onclick="favorite_check(this, ' . $shop_id . ', ' . $product_id . ')" class=" action-btn like"><span class="svg-wrapper ' . $hart . '">' . RBS::SVG('hart_white') . '</span></button>';
} else {
    echo '<button class="like disabled" style="opacity: 0.2"><span class="svg-wrapper ">' . RBS::SVG('hart_white') . '</span></button>';
}
if(!SHOP::is_my_shop($shop_id)) {
    if (isset($v['IN_BASKET'])) {
        if ($v['IN_BASKET'] == 0) {
            echo '<button onclick="in_basket(\'' . $shop_id.'_'.$product_id . '\', this)" class="basket action-btn">' . RBS::SVG('bag_box.svg') . '</button>';
        } else {
            echo '<button onclick="in_basket(\'' . $shop_id.'_'.$product_id . '\', this)" class="basket action-btn">' . RBS::SVG('basket_on.svg') . '</button>';
        }
    }
}
echo '</div>';
if($discount < 1) {
    echo '<div class="price">'.VALUES::price_format($v['price']).' Р</div>';
} else {
    $price = round($v['price'], 2);
    $discount_price = $price - ($discount/100*$price);
    echo '<div class="price">'.VALUES::price_format($discount_price).' Р</div>';
    echo '<div class="price">'.VALUES::price_format($v['price']).' Р</div>';
    echo '<div class="discount">- '.$discount.' %</div>';
}
echo '<div class="location">'.$v['PROPS']['place']['0']['value'].'</div>';
echo '</div></li>';