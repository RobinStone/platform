<div class="wrapper main-header">    <h1>Мои сообщения</h1></div><section class="wrapper profil">    <div class="columns column-left">        <div class="menu-left-profil flex column gap-10">        </div>    </div>    <div class="columns column-right">        <?php echo render('chat_m', ['style'=>'user_page', 'call_room'=>'self', 'auto_start'=>true]); ?>    </div></section><template id="mess">    <button data-room="self" onclick="open_chat(this, '')" class="chat-item action-btn flex gap-10 not-border">        <img class="btn-min-img" width="100" height="100" src="">        <div class="info-t flex column">            <span></span>            <span></span>            <span></span>        </div>        <div class="user-indicate flex column center">            <img width="30" height="30" alt="user" src="/DOWNLOAD/20230531-120816_id-2-498113.svg">            <span class="user-name-indicate">USER</span>        </div>    </button></template><?phpecho render('player');?><script>    let buff_btn = null;    $(document).on('contextmenu', 'button.chat-item', function(e) {        e.preventDefault();        let id_room = $(this).attr('data-room');        info_qest(undefined, function(){            SENDER('del_room', {id_room: id_room}, function(mess) {                mess_executer(mess, function() {                    if(current_room === id_room) {                        $('.chat-m-content').empty();                    }                    update_order_list();                });            });        }, function() {        }, 'Удалить безвозвратно<br>данный разговор?', 'Да - удалить', 'Нет - пусть остаётся');    });    new_alert.subscribe(function(mess) {        if(current_room !== mess.room_id) {            console.log('subscribe - work');            update_order_list();        }    });    function update_order_list() {        buffer_app = 'SHOPS';        SENDER_APP('get_messages_order_list', {}, function(mess) {            mess_executer(mess, function(mess) {                console.log('---MESSGAES---');                console.dir(mess);                $('.menu-left-profil').empty();                for(let i in mess.params) {                    let obj = mess.params[i];                    let temp = document.querySelector('#mess').content.cloneNode(true);                    $(temp).find('img.btn-min-img').attr('src', obj.img);                    $(temp).find('div.info-t span:nth-child(1)').text(obj.name);                    $(temp).find('div.info-t span:nth-child(2)').text(price_format(parseFloat(obj.price)));                    $(temp).find('div.info-t span:nth-child(3)').text(obj.f_date);                    if((obj.lid === 'to' && obj.showed === '0') || (obj.lid === 'from' && obj.showed_us === '0')) {                        // temp.querySelector('button').style.backgroundColor = '#e7ff005e';                        $(temp).find('button').addClass('not-read');                    }                    $(temp).find('button').attr('data-room', obj.room_id);                    $(temp).find('button').attr('onclick', 'open_chat(this, "'+obj.room_id+'")');                    $(temp).find('button').attr('data-accessory', obj.lid);                    if(obj.lid === 'from') {                        $(temp).find('button').append('<span class="accessory">Хочу купить</span>');                        if(obj.owner_array.id !== -1) {                            let namer = getParamFromString(obj.owner_array.params, 'name');                            if(namer === '') { namer = obj.owner_array.login; }                            $(temp).find('.user-indicate img').attr('src', '/IMG/img100x100/'+obj.owner_array.avatar);                            $(temp).find('.user-indicate span').text(namer);                        }                    }                    if(obj.lid === 'to') {                        $(temp).find('button').append('<span class="accessory">Я - продаю</span>');                        if(obj.client_id_array.id !== -1) {                            let namer = getParamFromString(obj.client_id_array.params, 'name');                            if(namer === '') { namer = obj.client_id_array.login; }                            $(temp).find('.user-indicate img').attr('src', '/IMG/img100x100/'+obj.client_id_array.avatar);                            $(temp).find('.user-indicate span').text(namer);                        }                    }                    $('.menu-left-profil').append(temp);                }            })        })    }    function open_chat(obj, id_room) {        swipe_panel();        if(status_chat === 'visible')        {            buff_btn = $(obj).closest('.action-btn');            // transmision($('.chat-m-wrapper div.status'), id_shop, false);            change_room(id_room);            $('.chat-m-wrapper footer').addClass('active-sender-chat');            SENDER('set_read_status_at_room', {id_room: id_room}, function(mess) {                mess_executer(mess, function() {                    $(obj).css('background-color', '');                });            });        }    }    open_closed_chat.subscribe(function(event) {        $(buff_btn).addClass('disabled');    });    connected_chat.subscribe(function() {        let url = new URL(location.href);        let addr = url.searchParams.get('room');        if(addr !== null) {            $('button[data-room="'+addr+'"]').click();        }        update_order_list();    });</script>