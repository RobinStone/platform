chat = new EventRBS();
target_room = ''; // КОД КОМНАТЫ В КОТОРОЙ ПРОХОДИТ ОБЩЕНИЕ
buffer_chat_restart = {}; // ПАРАМЕТРЫ ДЛЯ ОБНОВЛЕНИЯ ЧАТА, ЧТО Б ПОДКЛЮЧИЛСЯ С НАСТРОЙКАМИ ИЗ СТАРТА
field = null; // ЛЕЖИТ УКАЗАТЕЛЬ НА ПОЛЕ ЧАТА
livers = {}; // ПАРАМЕТРЫ ЛЮДЕЙ, КОТОРЫЕ ПОДКЛЮЧЕНЫ id_hu_in_room - {avatar, hu_in_room, login}
writed = '5r9b2s6hk4hh98tergsesfk03tdkgy';  // СВОЯ ПОДПИСЬ
visible_chat = false;
edit_id_mess = -1;

// place_consol = 'body'; // МОЖЕТ БЫТЬ - chat|body|none
place_consol = 'none'; // МОЖЕТ БЫТЬ - chat|body|none

phone_book = document.querySelector('.phone-book');

/////////////////////////////////////////////////////////////
//      ПРИНЯТИЕ СООБЩЕНИЙ
/////////////////////////////////////////////////////////////
chat.subscribe(function(answer) {
    console.log('--- FROM SERVER ---');
    console.dir(answer);
    console.log('');

    let com = answer.com || '';
    switch(answer.type) {
        case 'action':
            switch(answer.com) {
                case 'connected':
                    consol('CONECTED TO ['+answer.result+']', 1);
                    target_room = answer.result;
                    writed = answer.writed;
                    // set_title(target_room);
                    get_room(target_room);
                    consol(answer.com, 4);
                    $('.indicator').addClass('online');
                    requestNotificationPermission();
                    break;
                case 'unconnected':
                    consol(answer.result);
                    consol(answer.com, 4);
                    break;
                case 'logout':
                    location.href='/auth/exit';
                    consol(answer.com, 4);
                    break;
                case 'change_message':
                    $('.row-line[id="'+answer.additional.id+'"]').find('span:nth-child(1)').html(answer.additional.value+'<div class="changer-row">Изменено (только что)</div>');
                    consol(answer.com, 4);
                    break;
                case 'set_emojy':
                    $('.row-line[id="'+answer.additional.id+'"]').find('.emojy').text(answer.additional.emojy);
                    $('.row-line[id="'+answer.additional.id+'"]').find('.emojy').addClass('visible').css('transform', 'scale(2.5)');
                    setTimeout(function() {
                        $('.row-line[id="'+answer.additional.id+'"]').find('.emojy').css('transition', '0.5s').css('transform', 'scale(1)');
                    }, 250);
                    consol(answer.com, 4);
                    break;
                case 'delete_message':
                    $('.row-line[id="'+answer.additional+'"]').find('img').remove();
                    $('.row-line[id="'+answer.additional+'"]').find('button').remove();
                    setTimeout(function() {
                        $('.row-line[id="'+answer.additional+'"]').find('span:first-child:not(.user-login)').html('<div class="changer-row">Удалено (только что)</div>');
                    }, 10);
                    consol(answer.com, 4);
                    break;
                case 'test':
                    consol('TEST', 1);
                    console.dir(answer.result);
                    consol(answer.com, 4);
                    break;
                case 'update_contact':
                    consol('update_contact', 1);
                    let data_liver = $('li[data-liver="'+answer.result.id+'"]');
                    if(answer.result.avatar !== null) {
                        data_liver.addClass('static-chat');
                        data_liver.find('.liver-avatar').attr('src', '/IMG/img100x100/'+answer.result.avatar);
                    } else {

                    }
                    consol(answer.com, 4);
                    break;
                case 'set_livers_parameters':
                    consol(answer.com, 4);
                    console.log('--- livers ---');
                    console.dir(answer.result);
                    livers = answer.result;
                    $('.phone-book-list li').each(function(e,t) {
                        let liver_id = parseInt($(t).attr('data-liver'));
                        if(liver_id in livers) {
                            livers[liver_id]['avatar'] = $(t).find('.liver-avatar').attr('src').split('/')[3];
                            let user_login = $(t).attr('data-login');
                            if(typeof user_login !== 'undefined') {
                                livers[liver_id]['login'] = user_login;
                            }
                        }
                    });
                    upgrade_chat(livers);
                    break;
                default:
                    consol('unknown com at action..', 2);
                    break;
            }
            break;
        case 'text':
            consol('MY     - '+target_room, 2);
            consol('SERVER - '+answer.target, 2);
            if(target_room !== answer.target) {
                info_qest(undefined, function() {
                    target_room = answer.target;
                    say('Успешно');
                }, function() {

                }, 'Сообщение, пришедшее вам было отослано из другой комнаты, перейти ?');
            }

            $('.message-field').append('<li>'+answer.result+'</li>');
            setTimeout(function() {
                scrollToBottom($('.message-field'));
            }, 1);
            break;
        case 'list':
            consol('list come now', 4);
            // console.log('--- SENDED LIST ---');
            // console.dir(answer);
            // answer.writed - приходит с каждым сообщением, обычно показывает кто инициировал это сообщение
            // и просто writed - подпись которая наша и получается при первом открытии чата

            let mess_params = answer.result[0] || [];  // тут берёт первое сообщение из списка на публикацию ТУТ ВНИМАТЕЛЬНО !!!
                                                 // когда публикуется одно сообщение
            let from_room = mess_params.room || '';    // ИЗ КАКОЙ КОМНАТЫ ПРИШЛИ СООБЩЕНИЯ

            let is_list = answer.additional || '';

            if(answer.writed === writed) {         // ЕСЛИ ПОДПИСИ СОВПАДАЮТ и
                if(from_room === target_room || is_list === 'islist') {    // ...комнаты совпадают (тут из-за не вполне определённой переменной mess_params)
                    drawChat(answer.result);
                } else {
                    consol('Пропущено собственное сообщение', 3);
                }
            } else {                                // ЕСЛИ ПОДПИСИ НЕ СОВПАДАЮТ и
                if(from_room === target_room && visible_chat) {     // ...комнаты совпадают
                    drawChat(answer.result);
                    if(is_list !== 'islist') {
                        play_message_come();
                        let id_mess = parseInt(answer.result[0]['id']);
                        consol('Message is reading send for = '+id_mess);
                        message_is_reading(id_mess); // ОТПРАВЛЯЕТ СЕРВЕРУ, ЧТО СООБЩЕНИЕ ПРОЧИТАНО И ДОСТАВЛЕНО
                        if(answer.result[0]['room'] === target_room) {
                            consol('Пришо сообщение, из текущей комнаты<br>'+target_room);
                        }
                    }
                } else {
                    play_message_come();
                    consol('запрос на добавление контакта', 1);
                    console.log('NEW contact');
                    console.dir(mess_params);
                    add_in_phone_book(mess_params);
                }
            }
            break;
        case 'info':
            say(answer.result);
            break;
        case 'error':
            consol(answer.result, 3);
            say(answer.result, 3);
            break;
    }
    // consol('sended type ['+answer.type+']');
});

function requestNotificationPermission() {
    // Проверяем поддерживает ли браузер API запроса уведомлений
    if ('Notification' in window) {
        Notification.requestPermission().then(function (permission) {
            if (permission === 'granted') {
                // Разрешение на воспроизведение звука получено
                console.log('Разрешение на воспроизведение звука получено');
            }
        });
    }
}

$(document).on('click', '.answer', function(e) {
    let id_target = $(this).attr('data-id-target');
    let childElement = document.getElementById(id_target);
    if(childElement === null) {
        say('Это сообщение было удалено модератором', 2);
        return false;
    }
    let parentElement = document.querySelector('.message-field');
    let offsetTop = childElement.offsetTop - parentElement.offsetTop;
    parentElement.scrollTo({
        top: offsetTop-20,
        behavior: 'smooth'
    });
    setTimeout(function() {
        $(childElement).addClass('flash-row');
        $(childElement).css('transition', '1s');
        setTimeout(function() {
            $(childElement).removeClass('flash-row');
        }, 1000);
    }, 400);
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('dragover', '.input-field', function(e) {
    $(this).addClass('load-in');
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('dragleave', '.input-field', function(e) {
    $(this).removeClass('load-in');
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('drop', '.input-field', function(e) {
    $(this).removeClass('load-in');
    set_accept_types_loading('image');
    final_loading_ok = function(messer) {
        try {
            console.dir(messer);
            send_file_in_room(messer);
        } catch (e) {
            console.log('На этой странице обработчик не работает');
        }
    };
    place_to_indicators_container_tag = '.message-field';
    // console.log('@@@@@@@@@@@@@@@@@@@@@@@');
    // console.dir(e);
    insert_file(e, $('.content .main-chat-wrapper'));
    e.preventDefault();
    e.stopPropagation();
});

$(document).on('contextmenu', '.row-line.in-left', function(e) {
    let txt = $(this).find('span:first-child').text();
    let id = $(this).attr('id');
    e.stopPropagation();
    e.preventDefault();
    let lst = {
        'Ответить': function() {
            let pnl = info_inputText(undefined, function() {
                if(bufferText.length >= 1) {
                    sender_chat(bufferText, 'answer', {room: target_room, id_mess: id});
                }
            }, '<span style="font-size: 15px;">«'+txt+'»</span>', '', 'Ответить');
            console.log(pnl);
            $(pnl).find('textarea').on('keydown', function(e) {
                let in_text = $(pnl).find('textarea').val();
                if((e.ctrlKey || e.metaKey || e.shiftKey) && e.key === 'Enter') {
                    $(pnl).find('textarea').val(in_text+'\n');
                    e.preventDefault();
                } else {
                    if(e.key === 'Enter') {
                        $(pnl).find('button.presser').click();
                    }
                }
            });
        },
    };
    let arr_emo = ['🙂', '😍', '🧡', '❗', '😎', '🙁', '😥', '😭', '😡', '🙏', '👍', '👎', '🔥'];
    for(let i in arr_emo) {
        lst[arr_emo[i]] = function() {
            sender_com('set_emojy', {id: id, emojy: arr_emo[i]});
        };
    }
    info_variants(undefined, lst, '', 'reactions');
});

$(document).on('contextmenu', '.in-right', function(e) {
    let obj = this;
    let id_mess = $(obj).attr('id');
    $('.main-chat').removeClass('table-focus');
    e.preventDefault();
    let lst = {
        'Удалить сообщение': function() {
            sender_com('delete_message', id_mess);
        },
    };
    if($(obj).attr('data-type-mess') === 'text') {
        edit_id_mess = parseInt(id_mess);
        lst['Изменить сообщение'] = function() {
            let txt = $(obj).find('span:first-child').text();
            if(txt.includes('Изменено (')) {
                txt = txt.substr(0, txt.indexOf('Изменено ('))
            }
            $('.input-field').val(txt);
            $('.input-field').focus();
        }
    }
    info_variants(undefined, lst, 'Что сделать ?');
});
$(document).on('dblclick', '.img-mess', function(e) {
    let img = $(this).find('img').attr('src').split('/')[3];
    open_popup('imger', {img: img});
});

function add_in_phone_book(arr) {
    let phones = $('.phone-book-list');
    let txt = arr.mess;
    if(arr.type === 'img') {
        txt = 'Изображение...';
    }
    if(phones.find('li[data-room="'+(arr.room)+'"]').length === 0) {
        phones.prepend('<li data-liver="'+arr.id_liver+'" data-li="'+(arr.id_liver+'-'+arr.id_room)+'" data-room="'+arr.room+'">' +
            '<img class="liver-avatar" src="/IMG/img100x100/20240113-181427_id-2-236369.png" width="20" height="20">' +
            '<span onclick="set_new_room(this, \''+arr.room+'\')" class="count-lines-1 action-btn">'+txt+'</span>' +
            '<button onclick="set_new_room(this, \''+arr.room+'\')" title="Ответить на сообщение" class="ring">' +
            '<img src="/DOWNLOAD/20240113-195449_id-2-443919.gif" width="20" height="20">' +
            '</button>' +
            '<button onclick="delete_contact_from_phone_book(this)" title="Удалить чат" class="krest"><img src="/DOWNLOAD/ec578cdcc885e82e09a1347fafc079bc.svg" width="20" height="20"></button></li>');
            sender_chat('update_contact', 'com', arr.id_liver+'-'+arr.id_room);
    } else {
        if(phones.find('li[data-room="'+(arr.room)+'"] .ring').length === 0) {
            phones.find('li[data-room="' + (arr.room) + '"] .krest').before('<button onclick="set_new_room(this, \'' + arr.room + '\')" title="Ответить на сообщение" class="ring">' +
                '<img src="/DOWNLOAD/20240113-195449_id-2-443919.gif" width="20" height="20">' +
                '</button>');
        }
        phones.find('li[data-room="'+(arr.room)+'"] .action-btn').text(arr.mess);
        $('.phone-book-list').prepend(phones.find('li[data-room="'+(arr.room)+'"]'));
    }
}

function set_new_room(obj_btn, room) {
    header_footer_set(false);
    show_main_chat();
    obj_btn = $(obj_btn).closest('li');
    if(obj_btn.length > 0) {
        if (!obj_btn.hasClass('saved')) {
            console.log(obj_btn);
            save_new_contact($(obj_btn).attr('data-liver'), $(obj_btn).attr('data-room'));
        } else {
            consol('Этот контакт уже сохранён');
        }
        if (obj_btn.hasAttr('data-login')) {
            obj_btn.find('span').text(obj_btn.attr('data-login'));
        }
        $(obj_btn).find('.ring').remove();
    } else {
        say('Тут обращение на определённый номер = ['+room+']');
    }
    target_room = room;
    get_room(room);
}

function save_new_contact(liver_id, room) {
    consol('сохранение -<br>liver_id='+liver_id +'<br>room='+ room, 2);
    $('.phone-book-list li[data-room="'+room+'"]').addClass('saved');
    sender_chat('save_contact', 'com', {liver_id: liver_id, room: room});
}

function delete_contact_from_phone_book(obj) {
    let room = $(obj).closest('li').attr('data-room');
    let txt = $(obj).closest('li').find('span').text();
    info_qest(undefined, function() {
        sender_chat('delete_contact', 'com', room);
        $(obj).closest('li').remove();
    }, function() {

    }, 'Подтвердите удаление из контактов<br><b style="color: rgb(45,121,30)">"'+txt+'"</b> ?', 'Да - удалить', 'Нет - пусть остаётся');
}

function send_info(info_arr={}) {
    let arr = {
        token: chat_token,
        type: 'info',
        data: info_arr,
    };
    ws.send(JSON.stringify(arr));
}

function get_room(room) {
    clear_chat();
    consol('get room ['+room+']', 4);
    sender_chat('get_room', 'com', {room: room, count: 30});
}

/**
 * Основная функция передачи данных
 * @param txt - любой тип данных, поддерживаемый на сервере
 * @param type - может быть (com, info, mess)
 * @param target - цель может не передаваться в зависимости от формата необх. данных на сервере
 */
function sender_chat(txt, type='text|com|info|mess|file', target='') {
    if(type === 'text|com|info|mess|file') { type = 'text'; }
    consol('My token: '+chat_token, 1);
    if(target_room !== '' && target === '') {
        target = target_room;
    }
    let arr = {
        token: chat_token,
        target: target,
        type: type,
        text: txt,
    };
    if(txt.length >= 1) {
        ws.send(JSON.stringify(arr));
    } else {
        consol('Мало текста', 2);
    }
}
function sender_com(comand, datas={}) {
    let arr = {
        token: chat_token,
        target: target_room,
        type: 'comand',
        text: comand,
        datas: datas,
    };
    if(comand.length >= 1) {
        ws.send(JSON.stringify(arr));
    } else {
        consol('Мало текста', 2);
    }
}
function send_file_in_room(arr){
    arr['target_room'] = target_room;
    sender_chat(arr.sys_name, 'file', arr);
}

/**
 *
 * @param room - код комнаты
 * @param type_room - тип комнаты
 * @param params - параметры для комнаты (например id_shop или id_product)
 */
function active_chat(room, type_room='free|shop|product|personal', params={}) {
    clear_chat();
    buffer_chat_restart = {
        room: room,
        type_room: type_room,
        params: params,
    }
    ws = new WebSocket('wss://kokonk.com:2348');
    ws.onopen = function (e) {
        consol('Чат - запущен', 1);
        let arr = {
            token: chat_token,
            type: 'begin',
            room: room,
            type_room: type_room,
            params: params,
        };
        ws.send(JSON.stringify(arr));
    };

    ws.onmessage = function (event) {
        let mess = JSON.parse(event.data);
        chat.action(mess);
    };

    ws.onclose = function (event) {
        if (event.wasClean) {
            console.log(`Connection closed cleanly, code=${event.code} reason=${event.reason}`);
            consol('Чат - остановлен', 2);
        } else {
            console.log('Connection unexpectedly closed');
            consol('Ошибка. Сервер не готов...', 3);
            $('div.status').text('SERVER OFF');
        }
        $('.indicator').removeClass('online');
    };

    ws.onerror = function (error) {
        console.log(`Error: ${error.message}`);
        consol('Ошибка чата', 3);
    };
}

function exit_chat() {
    ws.close();
    target_room = '';
    visible_chat = false;
    setTimeout(function() {
        ws = null;
    },10);
}

function clear_chat() {
    livers = {};
    $('.message-field').empty();
}

function update_main_chat() {
    clear_chat();
    ws.close();
    setTimeout(function() {
        ws = null;
        active_chat(buffer_chat_restart.room, buffer_chat_restart.type_room, buffer_chat_restart.params);
    }, 100);
}

function hidden_chat() {
    header_footer_set(true);
    target_room = '';
    $('.main-chat').removeClass('final');
    visible_chat = false;
    setTimeout(function() {
        $('.main-chat').removeClass('in-center');
        $('.main-chat').addClass('invisible');
    }, 1000);
}

$(document).ready(function() {
    let win = create_window(transform_pos('center'), 'ROBOT', function(mess) {
        setTimeout(function(mess) {
            let console_chat = $('<div onclick="$(this).empty()" class="console-chat"></div>');
            let win_glo = win.closest('.window');
            // win_glo.addClass('invisible');
            win_glo.css('background', '#eaeaea');
            win_glo.addClass('main-chat')
            win_glo.find('h4 button:nth-child(2)').remove();
            win_glo.find('h4 button.close-window-btn').attr('onclick', 'exit_chat(); close_window($(this).parent().parent())');
            win_glo.find('h4 button').before('<button id="update-main-chat" class="header-btn" style="right: 29px"><img width="15" height="15" src="/DOWNLOAD/20231204-130112_id-2-713791.svg"></button>');

            win_glo.find('h4 button').before('<div class="rec-ind">REC</div>');

            win_glo.find('h4 button').before('<button id="adder-room" class="header-btn invisible" style="right: 54px"><img width="15" height="15" src="/DOWNLOAD/c2344a43c9d85954891eb821e2d26303.svg"></button>');

            win_glo.find('h4 button.close-window-btn').attr('onclick', 'hidden_chat()');

            win_glo.find('h4').append('<div class="indicator"><span></span></div>');

            switch(place_consol) {
                case 'chat':
                    win.append(console_chat);
                    break;
                case 'body':
                    $('body').append(console_chat);
                    $('.console-chat').css('left', 'unset').
                    css('right', '0').css('top', 'unset').css('max-height', 'unset').
                    css('bottom', '0').css('width', '300px').
                    css('padding', '4px').css('position', 'fixed').
                    css('box-sizing', 'bobder-box');
                    break;
            }


            if($('.main-chat').find('.main-chat-wrapper').length === 0) {
                $('.main-chat .content').append(document.getElementById('main-chat-temp').content.cloneNode(true));
            }

            $(document).on('click', '#update-main-chat', function(e) {
                update_main_chat();
            });
/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
            $(document).on('keydown', '.input-field', function(e) {
                let obj = this;
                let in_text = $(obj).val();
                if((e.ctrlKey || e.metaKey || e.shiftKey) && e.key === 'Enter') {
                    $(obj).val(in_text+'\n');
                    e.preventDefault();
                } else {
                    if(e.key === 'Enter') {
                        let s = $('.controlls-row textarea');
                        if(edit_id_mess === -1) {
                            sender_chat(s.val().trimRight().trimLeft(), 'mess');
                        } else {
                            sender_com('change_message', {id: edit_id_mess, value: s.val().trimRight().trimLeft()});
                            edit_id_mess = -1;
                        }
                        s.val('');
                    }
                }
            });
            $(document).on('click', '#sender-chat', function(e) {
                let s = $('.controlls-row textarea');
                if(edit_id_mess === -1) {
                    sender_chat(s.val().trimRight().trimLeft(), 'mess');
                } else {
                    sender_com('change_message', {id: edit_id_mess, value: s.val().trimRight().trimLeft()});
                    edit_id_mess = -1;
                }
                s.val('');
            });
/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
            field = $('.message-field');

            //
            //    ЗАГРУЗКА ИЗОБРАЖЕНИЙ ПРИ ПОМОЩИ CTRL + V
            //
            let field_insert = document.querySelector('.input-field');
            field_insert.addEventListener('paste', function(e){
                final_loading_ok = function(messer) {
                    try {
                        console.dir(messer);
                        send_file_in_room(messer);
                    } catch (e) {
                        console.log('На этой странице обработчик не работает');
                    }
                };
                place_to_indicators_container_tag = '.message-field';
                insert_file(e, $('.content .main-chat-wrapper'));
            });

            function show_hidden_chat() {
                info_inputString(transform_pos('center'), function(e) {

                });
            }

        }, 0);
    }, 'invisible');

    dragElement($('.phone-book'));

    // ПОСЫЛАЕТ СИГНАЛ, ЧТО ЧАТ АКТИВЕН
    //
    setInterval(function() {
        if(target_room !== '') {
            sender_chat('update_last_use', 'com', target_room);
        }
    }, 60000);

    setInterval(function() {
        SENDER('UPDATE_TOKEN_CHAT', {}, function(mess){});
    }, 30000);

});
///////////////////////////////////////////////////////////////////
//                   DRAGABLE ON MOBILE                          //
///////////////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function() {
    document.addEventListener("touchstart", function(e) {
        let parentDraggableMob = e.target.closest('.draggable-mob');
        if (parentDraggableMob) {
            dragStart(e, parentDraggableMob.parentElement);
        }
    }, { passive: false });

    document.addEventListener("touchend", function(e) {
        if (activeItem) {
            dragEnd();
        }
    }, { passive: false });

    document.addEventListener("touchmove", function(e) {
        if (activeItem) {
            drag(e);
        }
    }, { passive: false });

    let activeItem = null;
    let initialX;
    let initialY;
    let xOffset = 0;
    let yOffset = 0;

    function dragStart(e, parentDraggableMob) {
        activeItem = parentDraggableMob;
        e.preventDefault();
        initialX = e.touches[0].clientX - xOffset;
        initialY = e.touches[0].clientY - yOffset;
    }

    function dragEnd() {
        initialX = xOffset;
        initialY = yOffset;
        activeItem = null;
    }

    function drag(e) {
        e.preventDefault();
        xOffset = e.touches[0].clientX - initialX;
        yOffset = e.touches[0].clientY - initialY;
        setTranslate(xOffset, yOffset, activeItem);
    }

    function setTranslate(xPos, yPos, el) {
        el.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";
    }
});
///////////////////////////////////////////////////////////////////
//                   END DRAGABLE ON MOBILE                      //
///////////////////////////////////////////////////////////////////

function consol(txt, type=0) {
    if(place_consol !== 'none') {
        let col = 'color: #000;';
        switch (type) {
            case 1:
                col = 'color: green; background-color: lime;';
                break;
            case 2:
                col = 'color: black; background-color: yellow;';
                break;
            case 3:
                col = 'color: yellow; background-color: red;';
                break;
            case 4:
                col = 'background-color: #57d5ff;';
                break;
        }
        $('.console-chat').append('<div style="' + col + '">' + txt + '</div>');
        setTimeout(function () {
            scrollToBottom($('.console-chat'));
        }, 2);
    }
}

function scrollToBottom(scroll_block) {
    $(scroll_block).animate({
        scrollTop: $(scroll_block).prop("scrollHeight")
    }, 300);
}
function drawChat(llist, canvas=null) {
    if(canvas === null) {
        canvas = $('.message-field');
    }
    for(let i in llist) {
        add_message(llist[i], canvas);
    }
    setTimeout(function() {
        scrollToBottom($('.message-field'));
        update_livers_parameters();
    }, 10);
}
function add_message(arr, canvas) {
    let classes = '';
    let direct = 'in-left';
    if(arr.SELF === arr.id_liver) {
        direct = 'in-right';
    }
    classes += direct;
    let visible = '';
    if(arr.reaction !== '-') {
        visible = 'visible ';
    }
    let lst = {};
    switch(arr.type) {
        case 'img':
            lst = arr.mess.split('|');
            if(lst.length >= 4) {
                canvas.append('<li id="'+arr.id+'" data-label="'+arr.label+'" data-type-mess="'+arr.type+'" data-showed="'+arr.showed+'" data-id-file="'+lst[0]+'" data-id="'+arr.id_liver+'" data-room="'+arr.room+'" class="row-line '+classes+' flex column img-mess"><img class="img-chat" src="/IMG/img300x300/'+lst[2]+'">' +
                    '<span class="img-name count-lines-1">'+lst[3]+'</span><span class="time">'+arr.datatime.split(" ")[1].substring(0, 5)+'</span><p class="emojy '+visible+'">'+arr.reaction+'</p></li>');
            } else {
                consol('Ошибка формата данных сообщения',3);
            }
            break;
        case 'voice':
            let audiograph = '/IMG/AUDIO/'+arr.mess.split('.')[0]+'.png';
            canvas.append('<li id="'+arr.id+'" data-label="'+arr.label+'" data-type-mess="'+arr.type+'" data-showed="'+arr.showed+'" data-id-file="'+lst[0]+'" data-id="'+arr.id_liver+'" data-room="'+arr.room+'" class="row-line '+classes+' flex column voice-mess">' +
            '<button onclick="voice_play(this, \''+arr.mess+'\')" class="voice-play-btn action-btn"><img src="'+audiograph+'"></button>' +
            '<span class="img-name count-lines-1"></span><span class="time">'+arr.datatime.split(" ")[1].substring(0, 5)+'</span><p class="emojy '+visible+'">'+arr.reaction+'</p></li>');
            break;
        default:
            canvas.append('<li id="'+arr.id+'" data-label="'+arr.label+'" data-type-mess="'+arr.type+'" data-showed="'+arr.showed+'" data-id="'+arr.id_liver+'" data-room="'+arr.room+'" class="row-line '+classes+'">' +
                '<span>'+arr.mess+'</span><span class="time">'+arr.datatime.split(" ")[1].substring(0, 5)+'</span><p class="emojy '+visible+'">'+arr.reaction+'</p></li>');
            break;
    }
}
function update_livers_parameters() {
    if(target_room !== '') {
        if(isEmpty(livers)) {
            sender_chat('get_livers_parameters', 'com', target_room);
            consol('updated livers from SERVER', 4);
        } else {
            upgrade_chat(livers);
            consol('updated livers from BUFFER', 4);
        }
    } else {
        consol('Отсутствует идеинтефикатор комнаты', 2);
    }
}
function upgrade_chat(llist) {
    let first = -1;
    let id = -1;
    livers = llist;
    $('.row-line').each(function(e,t) {
        id = $(t).attr('data-id');
        if(first !== id) {
            if($(t).find('.user-info').length === 0) {
                let img = '20240113-181427_id-2-236369.png';
                if(typeof llist[id] !== 'undefined' && llist[id]['avatar'] !== null) {
                    img = llist[id]['avatar'];
                }
                let login = 'новый';
                if(typeof llist[id] !== 'undefined' && llist[id]['login'] !== null) {
                    login = llist[id]['login'];
                }
                $(t).append('<div class="user-info flex gap-5"><img src="/IMG/img100x100/' + img + '" width="40" height="40"><span class="user-login count-lines-1">' + login + '</span></div>');
                $(t).addClass('marg-top');
            }
            first = id;
        }
    });
}
function show_main_chat() {
    $('.main-chat').css('left', 'calc(100vw - 200px)').css('top', 'calc(100vh - 288px)').addClass('in-center');
    $('.main-chat').removeClass('invisible');
    visible_chat = true;
    setTimeout(function() {
        $('.main-chat').addClass('final');
        border_corector($('.main-chat'));
        scrollToBottom($('.message-field'));
    }, 10);
}
function edit_phone_book(obj, id_phone_book_item) {
    let txt = $(obj).closest('li').find('span').text();
    let lst = {
        'Подпись контакта': function() {
            info_inputString(undefined, function() {
                if(bufferText.length >= 1) {
                    change_title_for_phone_book(id_phone_book_item, bufferText);
                    $(obj).closest('li').find('span').text(bufferText);
                    $(obj).closest('li').attr('data-login', bufferText);
                    $(obj).closest('li').attr('title', bufferText);
                }
            }, 'Изменить подпись в книге', txt);
        },
        'Изображение контакта': function() {
            let llist = {
                'Загрузить новое': function() {
                    set_accept_types_loading('img');
                    final_loading_ok = function(messer) {
                        try {
                            change_image_for_phone_book(id_phone_book_item, messer.sys_name);
                            setTimeout(function() {
                                $(obj).closest('li').find('.liver-avatar').attr('src', '/IMG/img100x100/'+messer.sys_name);
                            }, 1000);
                        } catch (e) {
                            console.log('На этой странице обработчик не работает');
                        }
                    };
                    $('#general-input-file').click();
                },
                'Использовать свои': function() {
                    open_popup('self_imgs_gallery', {id_shop: -1}, function() {
                        buffer_obj = obj;
                        $('.img-gallery button').each(function(e,t) {
                            $(t).attr('onclick', 'select_img_for_phone_book(this, '+id_phone_book_item+')');
                        });
                    });
                }
            };
            info_variants(undefined, llist, 'Какое изображение использовать');
        }
    };
    info_variants(undefined, lst, 'Редактор<br><b style="font-size: 17px; font-weight: 200">Вы хотите изменить:</b>');
}

function add_file() {
    let lst = {
        'С устройства': function() {
            final_loading_ok = function(messer) {
                try {
                    console.dir(messer);
                    send_file_in_room(messer);
                } catch (e) {
                    console.log('На этой странице обработчик не работает');
                }
            };
            place_to_indicators_container_tag = '.message-field';
            set_accept_types_loading('img');
            $('#general-input-file').click();
        },
        'Из загруженных': function() {
            open_popup('self_imgs_gallery', {}, function() {
                $('.img-gallery button').each(function(e,t) {
                    $(t).attr('onclick', 'sender_self_img_file(this)');
                });
            });
        },
    };
    info_variants(undefined, lst, 'Откуда взять файл ?')
}
function sender_self_img_file(obj) {
    let arr = {
        type: 'file',
        sys_name: $(obj).attr('data-src'),
        user_name: $(obj).attr('data-user-name'),
        insert_last_id: $(obj).attr('id'),
        type_file: 'image',
    };
    send_file_in_room(arr);
    setTimeout(function() {
        close_popup('self_imgs_gallery');
    }, 100);
}
function chat_in_down() {
    setTimeout(function() {
        scrollToBottom($('.message-field'));
        update_livers_parameters();
    }, 10);
}
function begin_chat_with(id_user, params={}) {
    header_footer_set(false);
    sender_com('close_session');
    show_main_chat();
    setTimeout(function() {
        let arr = {
            token: chat_token,
            type: 'begin',
            room: '',
            type_room: 'one2one',
            params: {id_target: id_user},
        };
        ws.send(JSON.stringify(arr));
    }, 400);
}
//////////////////////////////////////////////////////////////
///                      AUDIO
//////////////////////////////////////////////////////////////
setTimeout(function() {
    if($mobile) {
        console.log('IS - MOBILE');
        let access_rec = false;
        let startX, startY;
        $(document).on('touchstart', '#rec', function(e) {
            startRecording();
            access_rec = true;
            record_show(true);
            $('.basket-cont').removeClass('baket-not-show');
            startX = e.originalEvent.touches[0].clientX;
            startY = e.originalEvent.touches[0].clientY;
        });
        $(document).on('touchmove', '#rec', function(e) {
            let touch = e.originalEvent.touches[0];
            let posX = touch.clientX;
            let posY = touch.clientY;

            let distanceX = Math.abs(posX - startX);
            let distanceY = Math.abs(posY - startY);

            drawing_mode = false;

            if (distanceX > 20 || distanceY > 20) {
                access_rec = false;
                record_show(false);
            }
            $('#rec').css({
                'position': 'absolute',
                'left': posX + 'px',
                'top': posY + 'px',
                'z-index': '99999999',
            });
        });
        $(document).on('touchend', '#rec', function(e) {
            if(access_rec) {
                stopRecording();
            }
            drawing_mode = false;
            access_rec = false;
            record_show(false);
            $('.basket-cont').addClass('baket-not-show');
            $('#rec').css({
                'position': 'relative',
                'left': 'unset',
                'top': 'unset',
                'z-index': '',
            });
        });
    } else {
        console.log('not-mobile');
        $(document).on('mousedown', '#rec', function(e) {
            console.log('START');
            startRecording();
            record_show(true);
            $('.basket-cont').removeClass('baket-not-show');
        });
        $(document).on('dragstart', '#rec', function(e) {
            record_show(false);
        });
        $(document).on('dragover', '.basket-cont', function(e) {
            e.preventDefault();
            $(this).addClass('hover');
        });
        $(document).on('dragleave', '.basket-cont', function(e) {
            $(this).removeClass('hover');
        });
        $(document).on('dragend', '#rec', function(e) {
            $('.basket-cont').addClass('baket-not-show');
            $('.basket-cont').removeClass('hover');
            record_show(false);
            drawing_mode = false;
        });
        $(document).on('mouseup', '#rec', function(e) {
            console.log('END');
            $('.basket-cont').removeClass('hover');
            stopRecording();
            drawing_mode = false;
            record_show(false);
        });
    }
}, 2000);


let mediaRecorder;
let chunks = [];
let timer = null;

function startRecording() {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function(stream) {
            drawing_mode = true;
            start_drawin_audio();
            mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });

            mediaRecorder.ondataavailable = function(e) {
                chunks.push(e.data);
            }

            mediaRecorder.onstop = function() {
                let blob = new Blob(chunks, { type: 'audio/webm' });
                // Здесь можно выполнить действия с записанным аудио, например, отправить на сервер
                drawing_mode = false;
                chunks = [];
            }

            mediaRecorder.start();
            timer = new Date().getTime();
        })
        .catch(function(err) {
            console.log('Невозможно получить доступ к микрофону: ' + err);
        });
}

function stopRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        let arr = {};
        drawing_mode = false;
        final_loading_ok = function(mess) {
            try {
                arr = {
                    type: 'file',
                    sys_name: mess.sys_name,
                    user_name: mess.user_name,
                    insert_last_id: parseInt(mess.insert_last_id),
                    type_file: 'voice',
                    long: mess.user_params.long,
                };
                send_file_in_room(arr);
            } catch (e) {
                console.log('На этой странице обработчик не работает --- ');
            }
        };
        let placer = $('.message-field');
        mediaRecorder.stop();
        $('.basket-cont').addClass('baket-not-show');
        $('.basket-cont').removeClass('hover');
        mediaRecorder.ondataavailable = function(e) {
            chunks.push(e.data);
        }
        mediaRecorder.onstop = function(e) {
            drawing_mode = false;
            let blob = new Blob(chunks, { type: 'audio/webm' });

            let audioURL = URL.createObjectURL(blob);
            let audio = new Audio(audioURL);
            // Получаем время записи
            let recordingTime = (new Date().getTime() - timer) * 1000;
            params_general['long'] = recordingTime;  // добавляем время записи в милисекундах
            chunks = [];
            let file = new File([blob], 'voice.mp3', { type: 'audio/mp3' });
            upload_start(file, placer);
            setTimeout(function() {
                mediaRecorder = null;
                chunks = [];
            }, 1);
        }
    }
}

function voice_play(obj, file_name) {
    let pl = null;
    let mark = null;
    let audio = null;
    let time = parseInt($(obj).closest('li').attr('data-label'))/1000000;


    $('.vert-line').css('width', '0');

    if($(obj).find('audio').length === 0) {
        pl = $('<audio class="auditor invisible" controls="controls" preload="metadata" src="/DOWNLOAD/' + file_name + '">');
        mark = $('<div class="vert-line"></div>');
        $(obj).append(pl);
        $(obj).append(mark);
        audio = pl.get(0);
    } else {
        pl = $(obj).find('audio');
        mark = $(obj).find('.vert-line');
        audio = pl.get(0);
    }

    audio.onended = function() {
        setTimeout(function() {
            mark.css('width', '0');
        }, 100);
    };

    if(audio.paused) {
        audio.play();
    } else {
        audio.pause();
    }

    audio.addEventListener('loadedmetadata', () => {
        let duration = time;
    });

    function updateProgress() {
        let percentage = (audio.currentTime / time) * 100; // Рассчет процента проигрывания
        // console.log(percentage);
        mark.css('width', percentage + '%');
        if (!audio.paused) {
            requestAnimationFrame(updateProgress); // Обновление прогресс-бара через requestAnimationFrame
        }
    }

    audio.addEventListener('play', () => {
        requestAnimationFrame(updateProgress); // Начать обновление прогресс-бара при начале воспроизведения
    });
}

let flash = null;
function record_show(stat=true) {
    if(stat) {
        flash = setInterval(function() {
            if(!$('.rec-ind').hasClass('show')) {
                $('.rec-ind').addClass('show');
            } else {
                $('.rec-ind').removeClass('show');
            }
        }, 200);
    } else {
        clearInterval(flash);
        $('.rec-ind').removeClass('show');
        flash = null;
    }
}

//////////////////////////////////////////////////////////////

function select_img_for_phone_book(obj, id) {
    change_image_for_phone_book(id, $(obj).attr('data-src'));
    $(buffer_obj).closest('li').find('.liver-avatar').attr('src', '/IMG/img100x100/'+$(obj).attr('data-src'));
}
function set_title(html_fragment_string='ROBOT') {
    $('.main-chat h4 b').html(html_fragment_string);
}
function separate(mess_arr) {

}
function message_is_reading(message_id) {
    sender_chat('message_reading', 'info', message_id);
}
function get_dump() {
    sender_chat('get_dump', 'com');
}
function change_image_for_phone_book(id_phone_contact, img) {
    sender_chat('change_image_for_phone_book', 'com', {id: id_phone_contact, img: img});
    close_popup('self_imgs_gallery');
}
function change_title_for_phone_book(id_phone_contact, new_title) {
    sender_chat('change_title_for_phone_book', 'com', {id: id_phone_contact, title: new_title});
}
function get_info_about_room($room) {

}
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
//  AUDIO - WRITING - DRAW
let drawing_mode = true;
let size_win = $('.input-field').width();

$(document).on('resize', '.input-field', function(){
    size_win = $('.input-field').width();
    console.log('Размеры динамического блока изменились');
});

function start_drawin_audio() {
    size_win = $('.input-field').width();
    console.log('-------->>>'+size_win);
    arm = navigator.mediaDevices.getUserMedia({audio: true})
        .then(stream => {
            const audioContext = new AudioContext();
            const analyser = audioContext.createAnalyser();
            const microphone = audioContext.createMediaStreamSource(stream);
            const javascriptNode = audioContext.createScriptProcessor(2048, 1, 1);

            const canvas = document.getElementById('audioVisualizerCanvas');
            const canvasCtx = canvas.getContext('2d');
            canvasCtx.fillStyle = 'green';

            let i = 0;

            analyser.smoothingTimeConstant = 0.3;
            analyser.fftSize = 1024;

            microphone.connect(analyser);
            analyser.connect(javascriptNode);
            javascriptNode.connect(audioContext.destination);

            javascriptNode.onaudioprocess = function () {
                if(drawing_mode === false) {
                    canvasCtx.clearRect(0, 0, canvas.width, canvas.height);
                    i = 0;
                    arm = null;
                    return;
                }
                const array = new Uint8Array(analyser.frequencyBinCount);
                analyser.getByteFrequencyData(array);
                let average = array.reduce((a, b) => a + b) / array.length;
                average *= 1.5;
                if (average > canvas.height) {
                    average = canvas.height;
                }
                canvasCtx.fillRect(i, canvas.height - average, 1, canvas.height);
                ++i;
                if (i > size_win+98) {
                    say('ok');
                    canvasCtx.clearRect(0, 0, canvas.width, canvas.height);
                    canvasCtx.fillStyle = 'green';
                    i = 0;
                }
            };
        })
        .catch(error => {
            console.log('Ошибка при получении доступа к микрофону:', error);
        });
}
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////

function header_footer_set(showed = false) {
    if(is_mobile()) {
        if (showed) {
            $('header.header').removeClass('hidden-header');
            $('.quick_access').removeClass('hidden-header');
        } else {
            $('header.header').addClass('hidden-header');
            $('.quick_access').addClass('hidden-header');
        }
    }
}

// if(is_mobile()) {
//     say($('#body-s').height());
//     $(document).on('focus', 'textarea.input-field', function() {
//         setTimeout(function() {
//             say($('#body-s').height());
//         }, 1000);
//         // $('.main-chat').css('min-height', '57dvh').css('height', '57dvh');
//         // setTimeout(function() {
//         //     scrollToBottom($('.message-field'));
//         // }, 100)
//     });
//     $(document).on('blur', 'textarea.input-field', function() {
//         // say("Клавиатура скрылась");
//     });
// }