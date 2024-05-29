<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0" nonce="abc123"></script>

<div class="wrapper">
    <?php echo "<div id='alerts'>" . render('alerts') . "</div>"?>
</div>

<section class="wrapper product">
<?=render('bread-crumbs', ['items'=>$items]);?>

    <h1><?php
        if($SEO['h1'] === '-') {
            echo $row['NAME'];
        } else {
            echo $SEO['h1'];
        }
        ?></h1>

    <div class="product-card">
        <div class="left-side">
            <div class="img-table b-2">
                <?php
                if(count($slides) > 0) {
                    echo render('carusel_prod', ['slides'=>$slides]);
                }
                ?>
            </div>
            <div style="margin: 20px 0" class="flex between align-center map-controlers">
                <div class="flex gap-5 align-center">
                    <span class="svg-wrapper" style="display: inline-block; width: 20px; height: 20px"><?=RBS::SVG('location')?></span><span><?=$place?></span></div>
                <?php if(isset($row['SHOP']['CITY_ALL'])) {  // тут готовится карта и иконка магазина к ней
                    if(file_exists('./IMG/img100x100/'.$row['SHOP']['logo'])) {
                        $img = $row['SHOP']['logo'];
                    } else {
                        $img = '20230721-222633_id-2-878998.svg';
                    }
                    ?>
                    <div class="flex gap-5 align-center action-btn map-shower-wrapper" onclick="map_coder_init(<?=$row["SHOP"]["CITY_ALL"]["shirota"]?>,
                    <?=$row["SHOP"]["CITY_ALL"]["dolgota"]?>,
                            '<?=$img?>',
                            '<?=$place?>',
                            '<?=$row['SHOP']['name']?>',
                            '<?=$row['SHOP']['descr']?>')"><span id="map-shower" class="svg-wrapper" style="display: inline-block; width: 20px; height: 20px"><?=RBS::SVG('map')?></span><span>Посмотреть на карте</span></div>
                <?php } else { ?>
                    <div class="flex gap-5 align-center action-btn"><span class="svg-wrapper" style="display: inline-block; width: 20px; height: 20px"><?=RBS::SVG('map')?></span><span>Посмотреть на карте</span></div>
                <?php } ?>
            </div>
            <?=render('subscriptions', ['viber'=>str_replace("&", "&amp;", urlencode($_SERVER['REQUEST_URI']))])?>
        </div>

        <div id="map-place-mobile"><button onclick="$('#map-place-mobile').toggleClass('opened');" class="not-border action-btn svg-red"><?=RBS::SVG('20230606-112003_id-2-898918.svg')?></button></div>
        <div class="right-side">
            <?php if(!SHOP::is_my_shop($shop_id) && Access::userID() > 0) { ?>
            <div class="flex between wrap">
                <button onclick="add_rem_favorite(this, <?=$shop_id?>, <?=$product_id?>)" class="favourites not-border action-btn flex align-center gap-10">
                    <span class="svg-wrapper <?=$best?>"><?=RBS::SVG('hart_white')?></span>
                    <span>Добавить в избранное</span>
                </button>
                <button onclick="add_rem_basket(this, '<?=$code_product?>')" class="favourites not-border action-btn flex align-center gap-10">
                    <span class="svg-wrapper"><?=$in_basket_svg?></span>
                    <span>Положить в корзину</span>
                </button>
            </div>
            <?php } ?>
            <div class="price b-2 <?php if($discount_price > 0) { echo 'changed'; } ?>">
                <span><?=VALUES::price_format($price)?> P</span>
                <span>
                <?php
                if($discount_price > 0) { echo VALUES::price_format($discount_price)." P"; }
                ?>
                </span>
            </div>
            <div class="flex align-center gap-20">
                <button onclick="show_number(this, '<?=$phone?>')" class="btn" style="white-space: nowrap">Показать номер</button>
                <?php if(SHOP::is_my_shop($shop_id)) { ?>
                    <button class="btn btn-white disabled">Вы - ПРОДАВЕЦ</button>
                <?php } else { ?>
                    <button onclick="show_main_chat(); begin_chat_product();" class="btn btn-white">Написать продавцу</button>
                <?php } ?>
            </div>
            <?=render('user-micro-card', ['login'=>$row['SHOP']['owner']])?>
            <div class="btns-cont flex column gap-10 b-2">
                <button onclick="$(this).find('.absolute-text').toggleClass('showed'); $(this).toggleClass('margin-bottom')" class="favourites flex align-center gap-10 not-border action-btn hider-text">
                    <span class="svg-wrapper"><?=RBS::SVG('ok')?></span>
                    <span>Безопасная сделка</span>
                    <div class="absolute-text">
                        Деньги за оплату товара продавец получит только после того, как вы подтвердите успешное получение товара.
                    </div>
                </button>
                <button onclick="$(this).find('.absolute-text').toggleClass('showed'); $(this).toggleClass('margin-bottom')" class="favourites flex align-center gap-10 not-border action-btn hider-text">
                    <span class="svg-wrapper"><?=RBS::SVG('big-car')?></span>
                    <span>Возможна доставка</span>
                    <div class="absolute-text">
                        Договаривайтесь с продавцом о месте и времени передачи товара самостоятельно<br>
                        или курьером продавца (согласовать с продавцом дату и время)
                    </div>
                </button>
            </div>
<!--            <button onclick="buy_form($shop_id, $product_id, order_params)" class="btn btn-shop" style="width: 215px">Купить</button>-->
            <?php if(!SHOP::is_my_shop($shop_id)) { ?>
            <button onclick="in_basket_and_execute()" class="btn btn-shop" style="width: 215px">Купить</button>
            <?php } else { ?>
                <button class="btn btn-shop disabled" style="width: 215px">Купить</button>
            <?php } ?>
            <ul class="user-params b-2" style="margin-top: 20px">
                <?php foreach($PRO->get_only_non_static_params() as $k=>$v) {
                    echo '<li class="flex column param-item">';
                    switch($v['field_type']) {
                        case 'link':
                            echo '<a class="action-btn" style="display: inline-block; max-width: fit-content; font-size: 20px; color: blue;" target="_blank" href="'.$v['VALUE'].'">'.$k.'</a>';
                            break;
                        case 'number':
                            echo '<div style="font-size: 20px; font-weight: 800; color: green">'.$k.'</div>';
                            $summ = $v['VALUE'];
                            if((float)$summ == (int)$summ) {
                                $summ = (int)$summ;
                            }
                            echo '<div style="padding-left: 10px; margin-top: 5px">'.$summ.'</div>';
                            break;
                        default: ?>
                            <div style="font-size: 20px; font-weight: 800; color: green"><?=$k?></div>
                            <div style="padding-left: 10px; margin-top: 5px"><?=$v['VALUE']?></div>
                            <?php break;
                    }
                    echo '</li>';
                } ?>
            </ul>
        </div>
    </div>

    <div id="map-place" class="map-place wrapper"><button onclick="$('#map-place').toggleClass('opened');" class="not-border action-btn svg-red"><?=RBS::SVG('20230606-112003_id-2-898918.svg')?></button></div>
    <div class="descr-wrapper flex column gap-10 b-2 list-closer">
        <div class="descr">Описание</div>
        <div data-id="<?=$descr_id?>" class="descr-text"><?=$descr?></div>
        <button onclick="$(this).parent().toggleClass('full-height')" class="closer-btn">Читать всё</button>
    </div>
    <header class="header-product-card flex gap-40 align-center b-2">
        <div class="num-product">№ <?=$row['ID']?></div>
        <div><?=$data_created."г. ".$time_created?></div>
        <div>Просмотров (<b id="showed" style="font-weight: 600"><?=$showed?></b>)</div>
        <div class="in-right">
            <button onclick="complaint()" class="underline" style="background: none; border: none; margin-left: auto; text-decoration: dashed; display: inline-block; max-width: fit-content">Пожаловаться </button>
        </div>
    </header>
</section>
<div style="min-height: 50px"></div>

<?php
    echo render('list_cards_product', ['title'=>'Ещё товары этого продавца', 'preview_cards_product'=>$preview_products]);
    echo render_app_template('shops', 'buy_form', ['arr'=>$row]);
?>

<?php //echo render('robot', [
//    'room'=>md5($shop_id."__".$product_id."__".$data_created),
//    'type_room'=>'product',
//    'params'=>[
//        'id_shop'=>$shop_id,
//        'id_product'=>$product_id,
//    ]
//]);
//?>

<!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params-->

<?php echo render('robot', [
    'header_controller'=>true,  // настройка прячет phone-book в header
    'room'=>'',
    'type_room'=>'personal',
    'params'=>[]
]); ?>

<script>
    <?php
    INCLUDE_CLASS('shops', 'favorite');
    echo FAVORITE::render_JS_execute();
    ?>

    order_params = <?=json_encode($row, JSON_UNESCAPED_UNICODE)?>;

    console.log('ORDER_PARAMS');
    console.dir(order_params);

    function begin_chat_product() {
        header_footer_set(false);
        sender_com('close_session');
        setTimeout(function() {
            let arr = {
                token: chat_token,
                type: 'begin',
                room: '<?=md5($shop_id."__".$product_id."__".$data_created)?>',
                type_room: 'product',
                params: <?=json_encode(['id_shop'=>$shop_id, 'id_product'=>$product_id], JSON_UNESCAPED_UNICODE)?>,
            };
            ws.send(JSON.stringify(arr));
        }, 400);
    }

    function in_basket_and_execute() {
        let code_p = '<?=$code_product?>';
        SENDER('in_basket', {code: code_p, only_add: true}, function(mess) {
            mess_executer(mess, function(mess) {
                console.dir(mess);
                if(mess.params === '+') {
                    location.href='/basket';
                }
            });
        });
    }
</script>