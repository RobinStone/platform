<section class="wrapper back shop">
<?php if(!isMobile()) { ?>
    <div class="flex gap-20">
        <img style="object-fit: contain" src="/IMG/img100x100/<?=$shop['logo']?>" width="88" height="88">
        <div>
            <h1><?=$shop['name']?></h1>
            <p class="text count-lines-2"><?=$shop['descr']?></p>
        </div>
        <div class="find-field flex align-center between gap-10" style="position: relative">
            <input id="finder-shop" placeholder="Поиск по площадке" value="<?=@$_COOKIE['shop_arr_codes_text']?>" type="text">
            <button onclick="search_context_in_shop(this)" class="btn flex align-center between gap-15">Найти</button>
        </div>
    </div>
    <div class="line"></div>
    <div class="flex align-center">
        <div class="time-block flex between">
            <div>
                <div style="margin-bottom: 5px">На сайте с <span><?=date('d.m.Y', strtotime($shop['created']))?></span></div>
                <div class="flex gap-5">
                    <img class="img-contain" src="./DOWNLOAD/20240106-160349_id-2-656415.svg" width="20" height="20" alt="clock">
                    <span><?=WORKTIME::format($shop['worktime'])?></span>
                </div>
            </div>
            <ul class="flex center align-center rating">
                <li class="flex column center">
                    <b class="green-text star"><?=$stars?></b>
                    <div>Рейтинг</div>
                </li>
                <li class="flex column center">
                    <b class="green-text"><?=$reviews?></b>
                    <div>Отзывов</div>
                </li>
                <li class="flex column center">
                    <b class="green-text"><?=count($subs)?></b>
                    <div>Подписчиков</div>
                </li>
            </ul>
            <div class="flex gap-15">
                <button class="btn flex align-center between gap-15"><img width="25" height="25" src="/DOWNLOAD/20240107-203056_id-2-860597.svg"><span>Показать номер</span></button>
                <button onclick="show_main_chat()" class="btn flex align-center between gap-15 btn-white"><img width="25" height="25" src="/DOWNLOAD/20230530-211804_id-2-746301.svg"><span>Написать сообщение</span></button>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="flex gap-15 align-center" style="margin-bottom: 20px">
        <img style="object-fit: contain" class="logo-shop" src="/IMG/img100x100/<?=$shop['logo']?>" width="88" height="88">
        <h1><?=$shop['name']?></h1>
    </div>
    <p class="text count-lines-2" style="margin-bottom: 20px"><?=$shop['descr']?></p>
    <div class="find-field flex align-center between gap-10" style="position: relative">
        <input id="finder-shop" placeholder="Поиск по площадке" value="<?=@$_COOKIE['shop_arr_codes_text']?>" type="text">
        <button onclick="search_context_in_shop(this)" class="btn flex align-center between gap-15">Найти</button>
    </div>
    <div class="flex gap-15 column" style="margin-bottom: 30px">
        <button class="btn flex align-center between gap-15"><img width="25" height="25" src="/DOWNLOAD/20240107-203056_id-2-860597.svg"><span>Показать номер</span></button>
        <button onclick="show_main_chat()" class="btn flex align-center between gap-15 btn-white"><img width="25" height="25" src="/DOWNLOAD/20230530-211804_id-2-746301.svg"><span>Написать сообщение</span></button>
    </div>
    <div class="flex align-center column">
        <div>
            <div style="margin-bottom: 15px">На сайте с <span><?=date('d.m.Y', strtotime($shop['created']))?></span></div>
            <div class="flex gap-5">
                <img src="./DOWNLOAD/20240106-160349_id-2-656415.svg" width="20" height="20" alt="clock">
                <span><?=WORKTIME::format($shop['worktime'])?></span>
            </div>
        </div>
        <div class="line"></div>
        <ul class="flex center align-center rating">
            <li class="flex column center">
                <b class="green-text star"><?=$stars?></b>
                <div>Рейтинг</div>
            </li>
            <li class="flex column center">
                <b class="green-text"><?=$reviews?></b>
                <div>Отзывов</div>
            </li>
            <li class="flex column center">
                <b class="green-text"><?=count($subs)?></b>
                <div>Подписчиков</div>
            </li>
        </ul>
    </div>
<?php } ?>
</section>

<section class="wrapper back">
    Фильтры
</section>

<?php echo render('list_cards_product', ['title'=>'', 'preview_cards_product'=>$preview_products]); ?>

<!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params-->

<?php
if(SHOP::is_my_shop($shop['id'])) {
    echo render('robot', [
        'header_controller'=>true,  // настройка прячет phone-book в header
        'room'=>'',
        'type_room'=>'personal',
        'params'=>[]
    ]);
} else {
    echo render('robot', [
        'header_controller'=>true,  // настройка прячет phone-book в header
        'room' => md5($shop['id'] . $shop['created']),
        'type_room' => 'shop',
        'params' => [
            'id_shop' => $shop['id']
        ]
    ]);
}
?>

<script>
    shop_id = <?=(int)$shop['id']?>;
</script>