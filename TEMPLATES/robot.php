<?php
$rows = SQL_ROWS(q("SELECT DISTINCT hu_in_room.id_room AS id_room FROM hu_in_room WHERE id_user = ".Access::userID()));
$rooms_ids = array_column($rows, 'id_room');

$phone_book = SQL_ROWS(q("
SELECT id, logo, title, room, id_hu_in_room AS id_liver, 'phone_book' AS id_user, datatime FROM phone_book 
WHERE id_user=".Access::userID()." ORDER BY datatime DESC")
);

$allerts = [];

if(count($rooms_ids) > 0) {
    $allerts = SQL_ROWS(q("
    SELECT 
    main_chat.mess AS title, 
    main_chat.datatime, 
    main_chat.id_room, 
    main_chat.type_mess AS type, 
    'ring' AS ring,
    hu_in_room.id_user, 
    hu_in_room.id AS id_liver, 
    
    room.room,
    
    users.avatar AS logo,
    users.login
    
    FROM main_chat
    JOIN hu_in_room ON main_chat.id_room = hu_in_room.id_room 
    LEFT JOIN room ON room.id = hu_in_room.id_room
    LEFT JOIN users ON users.id = hu_in_room.id_user
    WHERE
    hu_in_room.id_user <> " . Access::userID() . " AND
    hu_in_room.id = main_chat.id_hu_in_room AND
    hu_in_room.id_room IN (".implode(',', $rooms_ids).") AND
    main_chat.showed = 0
    "));
}

//wtf($allerts, 1);

$arr = array_merge($phone_book, $allerts);
$header = $header_controller ?? false;
$page = $page ?? false;
$admin_page = $admin_page ?? false;

$phone_book = [];
foreach($arr as $v) {
    $phone_book[$v['room']] = $v;
}

$phone_book = SORT::array_sort_by_column($phone_book, 'datatime', SORT_DESC);

//wtf($phone_book, 1);
?>
<div class="phone-book <?php if(Access::scanLevel() === 0) { echo 'invisible'; } ?>">
    <h4 class="draggable-mob">PHONE BOOK<div class="indicator"><span></span></div></h4>
    <ul class="flex column phone-book-list" style="gap: 2px">
        <?php foreach($phone_book as $v) {
            if($v['logo'] == '') {
                $v['logo'] = '20240113-181427_id-2-236369.png';
            }
            $static = "static-chat";
            if($v['id_user'] == -1) {
                $static = "";
            }
            $lock = "";
            if($v['id_user'] === 'phone_book') {
                $lock = "<button onclick='edit_phone_book(this, ".(int)$v['id'].")' title='Редактировать запись'><img width='20' height='20' src='/DOWNLOAD/20240114-115227_id-2-494634.svg'></button>";
            }

            $txt = $v['title'];
            $login = $v['login'] ?? $txt;
            if(isset($v['type']) && $v['type'] === 'img') {
                $txt = 'Изображение';
            }
            if(isset($v['type']) && $v['type'] === 'answer') {
                $txt = 'text';
            }
            if(str_contains($txt, '<div class="changer-row">Удален')) {
                $txt = 'Информация удалена';
            }
            ?>
        <li title="<?=$txt?>" data-login="<?=$login?>" class="<?=$static?> <?php if(!isset($v['ring'])) { echo 'saved'; } ?>" data-room="<?=$v['room']?>" data-liver="<?=$v['id_liver']?>">
            <img class="liver-avatar" src="/IMG/img100x100/<?=$v['logo']?>" width="20" height="20">
            <span onclick="set_new_room(this, '<?=$v['room']?>')" class="count-lines-1 action-btn"><?=$txt?></span>
            <?php if(isset($v['ring'])) { ?>
            <button onclick="set_new_room(this, '<?=$v['room']?>')" title="Ответить на сообщение" class="ring">
                <img src="/DOWNLOAD/20240113-195449_id-2-443919.gif" width="20" height="20">
            </button>
            <?php } ?>
            <button onclick="delete_contact_from_phone_book(this)" title="Удалить чат" class="krest">
                <img src="/DOWNLOAD/ec578cdcc885e82e09a1347fafc079bc.svg" width="20" height="20">
            </button>
            <?=$lock?>
        </li>
        <?php } ?>
    </ul>
</div>

<audio id="messageSound">
    <source src="/DOWNLOAD/20240122-190331_id-2-115843.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

<template id="main-chat-temp">
    <div class="main-chat-wrapper flex between column" style="gap: 7px">
        <ul class="message-field scroll-config"></ul>
        <div class="controlls-row ">
            <div class="flex between">
                <div class="flex column" style="gap: 3px; position: relative; min-height: 63px; justify-content: flex-start">
                    <div class="basket-cont baket-not-show svg-wrapper"><?=RBS::SVG('20231008-182216_id-2-210778.svg')?></div>
                    <button class="btn" onclick="add_file()">+</button>
                    <button draggable="true" id="rec" class="btn svg-wrapper"><?=RBS::SVG('20240203-113736_id-2-480635.svg')?></button>
                </div>
                <canvas id="audioVisualizerCanvas"></canvas>
                <textarea class="input-field"></textarea>
                <button class="btn" id="sender-chat"><?=RBS::SVG('9a36a830b20ee8008ea1344b4928d8dc.svg')?></button>
                <button class="invisible" onclick="get_dump()">COM</button>
            </div>
        </div>
    </div>
</template>

<script>
    let header_mode = false;   // true - означает что чат загружен в режиме header-controller
    let page_mode = false;   // true - означает что чат загружен в режиме page-controller

    let chat_token = '<?=Core::$SESSIONCODE?>';
    active_chat('<?=$room?>', '<?=$type_room?>', <?=json_encode($params)?>);

    if(!isset_script('general_files')) {
        include_js_script('general_files');
    }

    function send_test() {
        let arr = {
            name: 'robin',
            status: 'filer NDR 730 k12'
        }
        send_info(arr);
    }

    function play_message_come() {
        let sound = document.getElementById("messageSound");
        sound.play();
        message_shake();
    }

    function message_shake() {
        if(header_mode === true) {
            setTimeout(function() {
                let count_ring = $('.ring').length;
                $('#count-alert').text(count_ring);
                if(count_ring > 0) {
                    $('#mail-ico').addClass('shake');
                    $('#q-mess').addClass('shake');
                }
            }, 700);
        }
    }

    <?php
        if($page) { ?>
            page_mode = true;
            $('.menu-left-profil').append($('.phone-book'));
            $(document).ready(function() {
                setTimeout(function() {
                    $('.column-right').append($('.main-chat'));
                }, 500);
            })
        <?php }

        if($header) { ?>
            $('#count-alert').closest('li').append($('.phone-book'));
            $('.quick_access').append($('.phone-book'));
            header_mode = true;
        <?php }
        if(count($allerts) > 0) { ?>
            message_shake();
        <?php } ?>

        <?php if($admin_page) { ?>
            setTimeout(function() {
                console.log('attach');
                $('#actors-pnl').append($('.phone-book'));
            }, 100);
        <?php } ?>

</script>