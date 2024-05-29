<div class="chat-m-wrapper <? if(isset($hidden_in_right)) { echo 'hidden-in-right'; } ?>">
    <header class="drag-field">
        <div style="pointer-events: none" class="user flex align-center gap-15">
            <span class="svg-wrapper" style="display: inline-block; width: 20px; height: 20px"><?=RBS::SVG('20230714-122439_id-2-462942.svg')?></span>
            <span class="user-login">Master</span>
        </div>
        <div style="cursor:pointer" data-status="off" onclick="transmision(this, '<?=$call_room ?? 'self';?>')" class="status action-btn">Offline</div>
        <div id="support-btn" title="Написать в тех-поддержку" style="cursor:pointer; margin-left: auto; width: 25px;height: 25px; margin-right: 15px" onclick="" class="action-btn svg-wrapper"><?=RBS::SVG('20230811-114741_id-2-793590.svg');?></div>
        <div style="cursor:pointer; width: 20px;height: 20px" onclick="show_hidden_chat()" class="action-btn svg-wrapper"><?=RBS::SVG('20230606-112003_id-2-898918.svg');?></div>
    </header>
    <div class="chat-m-content">

    </div>
    <footer class="flex gap-10 between">
        <div class="special-btns">
            <button onclick="show_chat_menu()" class="svg-wrapper action-btn green-svg"><?=RBS::SVG('burger.svg')?></button>
            <button onclick="$('.smile-btns').toggleClass('opened')" class="svg-wrapper action-btn"><?=RBS::SVG('smile')?></button>
            <? if(Access::scanLevel() >= 1) { ?>
            <button onclick="upload_file_from_chat()" class="svg-wrapper action-btn"><?=RBS::SVG('20230714-122351_id-2-630852.svg')?></button>
            <? } ?>
        </div>
        <textarea id="message-m" placeholder="Ваше сообщение"></textarea>
        <button class="chat-sender-btn svg-wrapper sender-btn action-btn"><?=RBS::SVG('tele')?></button>
    </footer>
    <div class="smile-btns special-btns flex center flex-wrap gap-5" style="flex-wrap: wrap">
        <button class="svg-wrapper action-btn">😃</button>
        <button class="svg-wrapper action-btn">😂</button>
        <button class="svg-wrapper action-btn">😁</button>
        <button class="svg-wrapper action-btn">🙏</button>
        <button class="svg-wrapper action-btn">😘</button>
        <button class="svg-wrapper action-btn">😊</button>
        <button class="svg-wrapper action-btn">😍</button>
        <button class="svg-wrapper action-btn">🔥</button>
        <button class="svg-wrapper action-btn">👍</button>
        <button class="svg-wrapper action-btn">💰</button>
        <button class="svg-wrapper action-btn">❗</button>
        <button class="svg-wrapper action-btn">💥</button>
        <button class="svg-wrapper action-btn">😫</button>
        <button class="svg-wrapper action-btn">😱</button>
        <button class="svg-wrapper action-btn">😐</button>
        <button class="svg-wrapper action-btn">😠</button>
        <button class="svg-wrapper action-btn">😭</button>
        <button class="svg-wrapper action-btn">😡</button>
    </div>
</div>

<template id="loader-indicate">
    <div class="circle-bar" style="transform: rotate(-90deg);">
        <svg width="100%" height="100%" viewBox="0 0 42 42" class="donut">
            <circle class="donut-hole" cx="21" cy="21" r="15.91549430918954" fill="red"></circle>
            <circle class="donut-ring" cx="21" cy="21" r="15.91549430918954" fill="red" stroke="#301152" stroke-width="0"></circle>
            <circle style="transition: 0.001s" class="donut-segment" cx="21" cy="21" r="15.91549430918954"  stroke="#509d0b" stroke-width="10" stroke-dasharray="0 100" stroke-dashoffset="0"></circle>
        </svg>
        <div class="timer-value">00</div>
    </div>
</template>

<script>
    <?
    if(isset($hidden_auto_start) && $hidden_auto_start === true) { ?>
    setTimeout(function() {
        transmision($('.chat-m-wrapper div.status'), 'a122f83629ba3cf34c5953c1f6a6511b', true);
    }, 500);
    <?php }

    if(isset($auto_start) && $auto_start === true) { ?>
    setTimeout(function() {
        transmision($('.chat-m-wrapper div.status'), 'self', true);
    }, 500);
    <?php }

    if(Access::scanLevel() >= 7) { ?>
        function sys_com(command, params='') {
            $('#message-m').val('>'+command+' '+params);
            $('.chat-sender-btn').click();
        }

        function connect_next_room() {
            info_inputString(undefined, function(mess) {
                sys_com('change', bufferText);
            }, 'ID-ROOM INPUT', '', 'CHANGE');
        }
    <?php } ?>


    function show_chat_menu() {
        let lst = {};
        <?php
        if(Access::scanLevel() > 0) { ?>
        lst['Отправить свою папку с музыкой'] = function() {
            buffer_app = 'MUSIC';
            SENDER_APP('get_my_folders_with_tracks', {}, function(mess) {
                mess_executer(mess, function(mess) {
                    console.dir(mess);
                    let llist = {};
                    for(let i in mess.params) {
                        llist[i] = function() {
                            $('#message-m').val('++>audio('+i+')['+mess.params[i].join(",")+']');
                            $('.chat-sender-btn').click();
                        };
                    }
                    info_variants(undefined, llist, 'Какую сборку отправить?')
                });
            })
        };
        <?php } ?>
        info_variants(undefined, lst);
    }
</script>