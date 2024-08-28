<?php

?>

    <?php foreach($arr as $k=>$v) {
        $shop_id = (int)$v['shop_id'];
        $product_id = (int)$v['prod_id'];

        $chat_hash = md5($shop_id."__".$product_id."__".$v['created']);

        $link = '/'.$v['main_cat_trans'].'/'.$v['under_cat_trans'].'/'.VALUES::translit($v['action_list_trans']).'/'.VALUES::translit($v['name']).'?s='.$shop_id.'&prod='.$product_id;
        ?>
    <li class="vertical-list-item" data-hash="<?=$chat_hash?>" data-shop-id="<?=$shop_id?>" data-product-id="<?=$product_id?>">
        <div class="flex center img-wrapper">
            <img loading="lazy" src="/IMG/img300x300/<?=$v['PROPS']['images'][0]['value']?>" width="200" height="150">
        </div>
        <div class="text-block">
            <div class="product-name">
                <a class="" href="<?=$link?>">
                    <?=$v['name']?>
                </a>
            </div>
            <div class="location"><?=$v['PROPS']['place'][0]['value']?></div>
            <div class="descr"><?=$v['PROPS']['descr'][0]['value']?></div>
        </div>
        <div class="controls flex between column" style="gap: 3px">
            <div class="price"><?=VALUES::price_format($v['price'])?> P</div>
            <button style="margin-top: auto" class="btn round-btn">Показать телефон</button>
            <button onclick="show_main_chat(); begin_chat_product(this);" class="btn round-btn">Написать</button>
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
                    if ($v['IN_BASKET'] == 0) {
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


<script>
    function begin_chat_product(obj) {

        let shop_id = $(obj).closest('.vertical-list-item').attr('data-shop-id');
        let product_id = $(obj).closest('.vertical-list-item').attr('data-shop-id');
        let hash = $(obj).closest('.vertical-list-item').attr('data-hash');

        header_footer_set(false);
        sender_com('close_session');
        setTimeout(function() {
            let arr = {
                token: chat_token,
                type: 'begin',
                room: hash,
                type_room: 'product',
                params: {
                    'id_shop': shop_id,
                    'id_product': product_id
                },
            };
            ws.send(JSON.stringify(arr));
        }, 400);
    }
</script>