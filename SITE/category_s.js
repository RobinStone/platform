$(document).on('click', 'input[name="code-r"]', function(e) {    let obj = this;    clipboard_get(function(clip) {        $(obj).val(clip);    });});function auth() {    $('.auth-form').removeClass('invisible');    setTimeout(function() {        $('.auth-form').addClass('open');        setTimeout(function() {            setOverlayJust();            $('main').addClass('blur');        }, 100);    }, 10);}function hide_auth() {    $('.auth-form').removeClass('open');    delOvelay();    setTimeout(function() {        $('main').removeClass('blur');        $('.auth-form').addClass('invisible');    }, 300);}function pass_no_pass(obj) {    let types = $(obj).parent().find('input').attr('type');    if(types === 'password') {        $(obj).parent().find('input').attr('type', 'text');    } else {        $(obj).parent().find('input').attr('type', 'password');    }}function auth_send() {    let arr = {        'email-r':$('.auth-form input[name="email-r"]').val(),        'password-r':$('.auth-form input[name="password-r"]').val(),    };    console.dir(arr);    AJAX('/', {'email-r': arr['email-r'], 'password-r': arr['password-r'], 'auth-r':'rbs'}, function(mess) {        switch(mess) {            case 'admin':                location.href = '/admin';                break;            case 'ok':                location.href = '/';                break;            case 'profil':                location.href = '/profil';                break;            default:                say('Таких авторизационных данных<br>в нашей базе - обнаружено...', 2);                break;        }    });    return false;}function reg() {    let form = $('<form id="reg-form" class="auth-form open" method="post" action="/"></form>');    $('#body-s').append(form);    loadTemplateIn('reg-form', 'reg-form', {}, function(e) {        $('#auth-form').addClass('invisible');        $('#auth-form').removeClass('open');    });}function reg_send() {    AJAX('/', {'phone-r':$('#reg-form input[name="phone-r"]').val(), 'name-r':$('#reg-form input[name="name-r"]').val(), 'email-r':$('#reg-form input[name="email-r"]').val(), 'password-r':$('#reg-form input[name="password-r"]').val(), 'reg-r':'rbs'}, function(mess) {        mess_executer(mess, function() {            // say('Для активации вашей учётной записи - перейдите в вашу почту, найдите письмо от нас и пройдите по предложенной там ссылке!', 4);            $('#container').remove();            $('#reg-form').remove();            open_popup('message', {text: 'Для активации вашей учётной записи - перейдите в вашу почту,<br>найдите письмо от нас и пройдите по предложенной там ссылке!'});        });    });    return false;}function email_code_auth(email='') {    if(email === '') {        email = $('input[name="email-r"]').val();    }    $('.auth-form-inner .auth-field:nth-child(3)').addClass('invisible');    $('.auth-form-inner label:nth-child(4)').addClass('invisible');    $('.auth-form-inner .forgot-pass').addClass('invisible');    $('.auth-form-inner .btn:nth-child(6)').addClass('invisible');    $('.auth-form-inner .auth-field:nth-child(2) legend').text('Введите ваш E-Mail');    if(email.length < 5) {        rubber($('input[name="email-r"]'));    } else {        buffer_app = 'SHOPS';        SENDER_APP('auth_email_code', {email: email}, function(mess) {            mess_executer(mess, function(mess) {                $('.auth-form-inner p:first-child').html('На указанную почту вам был отправлен код,<br>введите его в поле ниже и нажмите<br>"ВОЙТИ ПО КОДУ".');                $('.auth-form-inner .auth-field:nth-child(2) legend').text('5-ти значный код из E-Mail');                $('input[name="email-r"]').attr('type', 'number');                $('input[name="email-r"]').attr('name', 'code-r');                $('input[name="code-r"]').val('');                $('input[name="code-r"]').attr('placeholder', '');                setTimeout(function() {                    rubber($('input[name="code-r"]'));                }, 500);                $('div.paddinger-5px').addClass('invisible');                $('div.paddinger-5px').after($('<button onclick="auth_code()" type="button" class="btn flex align-center between gap-15 svg-white" style="padding: 17px 12px; margin-bottom: 15px">ВОЙТИ ПО КОДУ</button>'));            });        });    }}function auth_code() {    let code = $('input[name="code-r"]').val();    if(code.length === 5) {        buffer_app = 'SHOPS';        SENDER_APP('auth_code', {code: code}, function(mess) {            mess_executer(mess, function(mess) {                location.href = 'https://rumbra.ru/profil?title=account';            });        });    }}function sms_reg(phone='') {    $('#krest').attr('onclick', 'location.reload()');    if(phone === '') {        $('.auth-form-inner p:first-child').html('Если нет аккаунта<br>Введите ваш номер телефона,<br>(обязательно с кодом страны), напр. 7 9232811515');        $('#auth-form header span:first-child').text('Регистрация');    } else {        $('.auth-form-inner p:first-child').html('Ожидайте пожалуйста...');        $('#auth-form header span:first-child').text('Авторизация по номеру');    }    $('.auth-form-inner fieldset:nth-child(2) legend').text('Ваш номер телефона');    $('.auth-form-inner fieldset:nth-child(2) input').val('');    $('.auth-form-inner fieldset:nth-child(2) input').attr('type', 'tel');    $('.auth-form-inner fieldset:nth-child(2) input').attr('name', 'phone');    $('.auth-form-inner fieldset:nth-child(2) input').attr('autocomplete', 'off');    $('.auth-form-inner fieldset:nth-child(2) input').addClass('phone-corrector');    $('.auth-form-inner fieldset:nth-child(2) input').attr('placeholder', '+7 XXXXXXXXXX');    $('.auth-form-inner fieldset:nth-child(3)').remove();    $('.forgot-pass').remove();    $('.auth-form-inner button:nth-child(3)').remove();    $('.auth-form-inner .paddinger-5px button:first-child').remove();    $('.auth-form-inner .paddinger-5px button').addClass('disabled');    $('.auth-form-inner .paddinger-5px button').addClass('btn-send-sms');    $('.auth-form-inner .paddinger-5px button svg').after('<span>Получить код</span>');    $('.auth-form-inner label').remove();    $('.btn-send-sms').attr('onclick', 'registred_sms()');    $('.btn-send-sms').text('Отправить');    if(phone !== '') {        $('.auth-form-inner fieldset:nth-child(2) input').val(VALUES.clear_phone(phone));        $('.btn-send-sms').text('ПОДТВЕРДИТЬ');        setTimeout(function() {            auth_sms();        }, 500);    }    // $('.auth-form-inner .paddinger-5px').append('<button type="button" onclick="coder_inputer()">CODE</button>');}function tele_reg_auth() {    location.href = 'https://t.me/rumbra_bot?start=auth';}let correct = ['0','1','2','3','4','5','6','7','8','9']; // +375 44 772-59-49$(document).on('keydown', '.phone-corrector', function(e) {    if(!correct.includes(e.key) && e.keyCode !== 8 && e.keyCode !== 37 && e.keyCode !== 39) {        e.preventDefault();    }});$(document).on('input', '.phone-corrector', function(e) {    let txt = $('.phone-corrector').val();    let cleanedText = txt.replace(/\D/g, '');    $('.phone-corrector').val(cleanedText);    setTimeout(function() {        if($('.phone-corrector').val().length >= 11 && $('.phone-corrector').val().length <= 12) {            $('.btn-send-sms').removeClass('disabled');        } else {            $('.btn-send-sms').addClass('disabled');        }    }, 100);});$(document).on('keyup', '.phone-corrector', function(e) {    if($('.phone-corrector').val().length >= 11 && $('.phone-corrector').val().length <= 12) {        $('.btn-send-sms').removeClass('disabled');    } else {        $('.btn-send-sms').addClass('disabled');    }});function registred_sms() {    let phone = $('.phone-corrector').val();    if(phone.length >= 11 && phone.length <=12) {        SENDER('registred_sms', {phone: phone}, function(mess) {            mess_executer(mess, function(mess) {                coder_inputer(phone);            });        });    }}function auth_sms() {    let phone = $('.phone-corrector').val();    if(phone.length >= 11 && phone.length <=12) {        SENDER('auth_sms', {phone: phone}, function(mess) {            mess_executer(mess, function(mess) {                coder_inputer(phone, true);            });        });    }}function coder_inputer(phone='000', auth=false) {    if(auth) {        $('.auth-form-inner p:first-child').html('На номер <b>'+phone+'</b> отправлен 5-тизначный код.<br>Для входа в систему, введите его в поле ниже.');    } else {        $('.auth-form-inner p:first-child').html('На номер <b>'+phone+'</b> отправлен 5-тизначный код.<br>Для успешного завершения регистрации, введите его в поле ниже.');    }    let inpt = $('.auth-form-inner fieldset:nth-child(2) input');    inpt.val('');    inpt.attr('type', 'number');    inpt.attr('name', 'code');    inpt.removeClass('phone-corrector');    inpt.attr('placeholder', 'XXXXX');    $('.auth-field legend').text('XXXXX - код из SMS');    let btn = $('.btn-send-sms');    if(auth) {        btn.find('span').text('ВХОД');        btn.attr('onclick', 'complite_registred_sms(true)');    } else {        btn.find('span').text('Завершить регистрацию');        btn.attr('onclick', 'complite_registred_sms()');    }    $('.btn-send-sms').removeClass('disabled');}function complite_registred_sms(auth = false) {    let code = parseInt($('.auth-form-inner fieldset:nth-child(2) input').val());    if(code < 10000 || code > 99999) {        say('Формат кода не верный!..', 2);        return false;    }    $('.btn-send-sms').addClass('disabled');    SENDER('complite_registred_sms', {code: code, auth: auth}, function(mess) {        mess = JSON.parse(mess);        if(mess.status === 'error') {            say(mess.body, 2);            setTimeout(function() {                location.reload();            }, 7000);        } else if(mess.status === 'ok') {            if(auth) {                location.href = '/profil?title=account&message=autorized';            } else {                location.href = '/profil?title=account&message=registrated';            }            say('Успешно.<br>Сейчас вы будете перенаправлены на ваш профиль');        }    });}function forgot_pass() {    let log = $('input[name="email-r"]').val();    if(log.length < 3) {        say('Введите в поле<br>Ваш телефон, логин или e-mail<br>что-то из этого...<br>Пароль можно не заполнять.', 2);        return false;    }    $('#auth-form.open').removeClass('open');    setTimeout(function() {        delOvelay();        setTimeout(function() {            open_popup('forgot_pass', {type: VALUES.email_phone_string(log), text: log});        }, 100);    }, 300);}setTimeout(function() {    if(isset_param_in_address_row('auth')) {        auth();    }}, 500);
$(document).on('click', 'details', function(e) {    let obj = this;    setTimeout(function() {        let html = '';        if($mobile) {            html = $('#mobile-side-menu').html();            localStorage.removeItem('create-form');        localStorage.setItem('side-menu', html);        } else {            html = $(obj).closest('.filter.grey-panel').html();            localStorage.removeItem('create-form');        localStorage.setItem('side-menu', html);        }    }, 200);});function set_filter(filter_name) {    setCookies('filter_name', filter_name);    location.reload();}function render_filter_items(type_filter='main_cat|under_cat|action_list', limit=[0,8], append_mode='append|replace') {}$(window).on('load', function() {    if(!$mobile) {        upload_side_menu_filter('desctop');    }});function save_map_side_menu() {    let html = $('.filter.grey-panel').html();    localStorage.removeItem('create-form');    localStorage.setItem('side-menu', html);}function upload_side_menu_filter(type='desctop | mobile') {    let buff_side_menu = localStorage.getItem('side-menu');    if(buff_side_menu === 'undefined' || buff_side_menu === '' || localStorage.getItem('time_update_side_menu') !== time_update_side_menu) {        localStorage.setItem('time_update_side_menu', time_update_side_menu);        console.log('---------------');        console.dir(caterorys_all);        let pnl = $('<ul class="side-menu"></ul>');        let cat = null;        let under_cat = null;        let list = null;        for (let i in caterorys_all) {            cat = caterorys_all[i];            let cat_item = $('<details type="main-cat" data-id="' + cat['id'] + '"><summary>' + cat['category'] + '</summary><ul class="under-cat-list"><li><a class="first-link" href="/' + cat['cat_trans'] + '">Перейти в "' + cat['category'] + '"</a></li></ul></details>');            if (typeof cat['UNDERCATS'] !== 'undefined') {                for (let ii in cat['UNDERCATS']) {                    under_cat = cat['UNDERCATS'][ii];                    let under_cat_item = $('<li><details type="under-cat" data-id="' + under_cat['id'] + '"><summary>' + under_cat['under_cat'] + '</summary><ul class="action-lists"><li><a class="first-link" href="/' + cat['cat_trans'] + '/' + under_cat['undercat_trans'] + '">Перейти в "' + under_cat['under_cat'] + '"</a></li></ul></details></li>');                    if (typeof under_cat['LISTS'] !== 'undefined') {                        for (let iii in under_cat['LISTS']) {                            list = under_cat['LISTS'][iii];                            let action_list_item = $('<li type="action-list" data-id="' + under_cat['id'] + '"><a class="action-list-item" href="/' + cat['cat_trans'] + '/' + under_cat['undercat_trans'] + '/' + list['translit'] + '">' + list['lists'] + '</a></li>');                            $(under_cat_item).find('.action-lists').append(action_list_item);                        }                    }                    $(cat_item).find('.under-cat-list').append(under_cat_item);                }            }            $(pnl).append(cat_item);        }        if(type === 'desctop') {            $('.filter').empty();            $('.filter').append(pnl);        } else {            $('#mobile-side-menu').empty();            $('#mobile-side-menu').append(pnl);        }        save_map_side_menu();        console.log('side-menu сгенерировано как новое');    } else {        if(type === 'desctop') {            $('.filter.grey-panel').html(buff_side_menu);        } else {            $('#mobile-side-menu').html(buff_side_menu);        }        console.log('side-menu вызвано из буфера');    }}
$(document).ready(function () {
    let swiper_banner = new Swiper('#banner', {
        direction: 'horizontal',
        loop: true,
        slidesPerView: 1,
        slidesPerGroup: 1,
        navigation: {
            nextEl: '.swiper-button-next-banner',
            prevEl: '.swiper-button-prev-banner',
        },
        autoplay: {
            delay: 6000, // Интервал в 3 секунды
        },
    });
});
chat = new Event();
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
    ws = new WebSocket('wss://rumbra.ru:2348');
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
    $('.main-chat').css('left', '50vw').css('top', '50vh').addClass('in-center');
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
stay_visible = new Event();stay_hidden = new Event();global_pos = {    latitude:0,    longitude:0,};navigator.geolocation.getCurrentPosition(function(position) {    let latitude = position.coords.latitude;    let longitude = position.coords.longitude;    console.log('latitude='+latitude);    console.log('longitude='+longitude);    global_pos.latitude = latitude;    global_pos.longitude = longitude;    setCookies('geo', latitude+'|'+longitude);}, function(err) {    if(err.code === 2) {        console.log('Системе не удалось точно определить ваш адрес... :(');    } else {        console.info('Без разрешения к текущему местоположению, системе будет сложно показывать ближайшие к вам магазины...');    }});$(document).on('blur', '#finder', function(e) {    setTimeout(function() {        $('#results').remove();        $('#finder').val('');    }, 200);});/** * Загружает меню или по требованию или спустя время */$(function() {    console.log('--- HEADER ---');    setTimeout(function() {        get_template('menu').then(function(tpl) {            $('header').append(tpl);        }).catch(function(err) {            console.error(err);        });    }, 500);    if(is_mobile()) {        let header = $('.up-row-header');        let finder = $('.find-field');        let logo = $('.logo-row');        let op = 1;        let search_in_header = false;        $(window).scroll(function () {            let st = $(this).scrollTop();            op = 1-(st/250);            if(op < 0) { op = 0; }            logo.css('opacity', op);            if(st >= 250) {                if(!search_in_header) {                    find_row_set_heared();                }            } else if(st < 200) {                if(search_in_header) {                    find_row_set_heared(false);                }            }        });        function find_row_set_heared(setter=true) {            if(setter) {                $('#body-s main').addClass('header-padding');                finder.addClass('trans-up');                header.find('.profil-btn-action').before(finder);                header.find('.logo-row').addClass('visually-hidden');                search_in_header = true;                setTimeout(function() {                    finder.addClass('showed');                }, 30);            } else {                $('#body-s main').removeClass('header-padding');                search_in_header = false;                finder.removeClass('showed');                header.closest('header').append(finder);                setTimeout(function() {                    finder.removeClass('trans-up');                    header.find('.logo-row').removeClass('visually-hidden');                }, 100);            }        }    }});system_come = new Event();document.addEventListener('DOMContentLoaded', function() {    system_come.subscribe(function(mess) {        if($('#results').length === 0) {            $('#finder').parent().append('<div id="results"></div>');        }        if(mess.RESULT !== null) {            $('#results').empty();            let obj = mess.RESULT;            let div = '';            console.dir(obj);            let link = '';            for(let i in obj) {                switch(obj[i]['TYPE']) {                    case 'PRODUCT':                        link = '/'+obj[i]['MAIN_CAT_TRANS'];                        if(obj[i]['UNDER_CAT_TRANS'] !== '') { link += '/'+obj[i]['UNDER_CAT_TRANS']; }                        if(obj[i]['ACTION_LIST_TRANS'] !== '') { link += '/'+obj[i]['ACTION_LIST_TRANS']; }                        link += '?s='+obj[i]['SHOP_ID']+'&prod='+obj[i]['ID'];                        div = $('<div class="find-row flex between"><a href="'+link+'">'+obj[i]['NAME']+'</a><span onclick="location.href=\'/'+obj[i]['MAIN_CAT_TRANS']+'\'" class="cat-link">'+obj[i]['MAIN_CAT']+'</span></div>');                        $('#results').append(div);                        break;                    case 'LINK':                        link = obj[i]['LINK'];                        div = $('<div title="'+obj[i]['HINT']+'" class="find-row flex between"><a class="cat-link-one" href="'+obj[i]['LINK']+'">'+obj[i]['NAME']+'</a></div>');                        $('#results').append(div);                        break;                }            }        } else {            $('#results').remove();        }    });    let url = new URL(location.href);    if(url.searchParams.get('title') === 'favorite') {        $('#q-best').addClass('sel');    } else {        // say(url.pathname);        switch(url.pathname) {            case '/': $('#q-home').addClass('sel'); break;            case '/profil': $('#q-profil').addClass('sel'); break;            case '/messenger': $('#q-mess').addClass('sel'); break;        }    }});$(document).on('keyup', '#finder-sity', function(e) {    // citys-list    let txt = $(this).val();    if(txt.length > 0) {        SENDER('get_citys', {txt: txt}, function (mess) {            mess_executer(mess, function (mess) {                console.dir(mess);                update_places_list(mess.params);            });        });    }});$(document).on('blur', '#finder-sity', function(e) {    $(this).remove();});$(document).on('change', '#finder-sity', function(e) {    let obj = this;    let txt = $(this).val().split(', ')[0];    setCookies('my_place', txt+'|'+translit(txt));    $('span[data-my_place]').text('г. '+txt);    $('span[data-my_place]').attr('data-my_place', translit(txt));    setTimeout(function() {        $(obj).remove();        location.reload();    }, 30);});startY = null;document.addEventListener('touchstart', function (e) {    startY = e.touches[0].clientY; // сохраняем координаты Y при касании});document.addEventListener('touchend', function (e) {    if($mobile) {        let endY = e.changedTouches[0].clientY;        let diff = endY - startY;        // if (diff > 0) {        //     $('.quick_access').removeClass('show');        // } else if (diff < 0) {        //     $('.quick_access').addClass('show');        // }    }});function create_my_order() {    if(user_id === -1) {        say('Подача объявления доступна только зарегистрированным пользователям!..', 2);    } else {        buffer_app = 'SHOPS';        SENDER_APP('scan_and_set_shop_add', {}, function(mess) {            mess = JSON.parse(mess);            if(mess.status !== 'ok') {                error_executing(mess);            } else {                location.href='/create';            }        });    }}document.addEventListener("visibilitychange", function() {    let url = new URL(location.href);    if (document.hidden) {        console.log("Вкладка стала скрытой");        stay_hidden.action(url);    } else {        console.log("Вкладка стала видимой");        stay_visible.action(url);    }});window.addEventListener('load', function() {    console.log('Before window included...');    if(typeof new_alert === 'undefined') {        new_alert = new Event();    }    new_alert.subscribe(function(mess) {        let url = new URL(location.href);        // status_chat = visible or hidden        if(url.pathname !== '/messenger' && current_room !== mess.room_id) {            $('.action-menu li:first-child').addClass('alert');        }    });});function change_my_place(obj) {    if($(obj).find('input').length === 0) {        let inpt = $('<input id="finder-sity" type="text" list="citys-list" placeholder="' + $(obj).text() + '">');        $(obj).append(inpt);        $(inpt).focus();    }    if($('#citys-list').length === 0) {        let tmp = $('<datalist id="citys-list"></datalist>');        $('.header').append(tmp);    }}function update_places_list(lst) {    $('#citys-list').empty();    for(let i in lst) {        $('#citys-list').append('<option data-city="'+lst[i]['name']+'">'+lst[i]['name']+', '+lst[i]['country']+'</option>');    }}function finder(obj) {    let par = $(obj).parent();    let txt = $(obj).val();    if(txt.length > 0) {        let arr = {            com: 'finder',            text: txt        }        system_send(arr);    } else {        $('#results').remove();    }}function basket_set(item, type='basket') {    let tp = '#basket-count';    if(type === 'deffer') {        tp = '#deffer-count';    }    if(type === 'compare') {        tp = '#compare-count';    }    if(item === '+') {        let count = parseInt($(tp).text());        ++count;        $(tp).text(count);    } else if(item === '-') {        let count = parseInt($(tp).text());        --count;        if(count > 0) {            $(tp).text(count);        } else {            $(tp).text(0);        }    } else {        item = parseInt(item);        $(tp).text(item);    }}$(document).on('keydown', '#finder', function(e) {    let obj = $(this).closest('.find-field').find('button');    if(e.keyCode === 13) {        search_context(obj);    }});function search_context(obj) {    let txt = $(obj).closest('.find-field').find('input').val();    location.href='/finder?text='+txt;}function toggle_controls() {    say('work');    if($('.quick_access').hasClass('hidden-header')) {        header_footer_set(true);    } else {        header_footer_set(false);    }}
console.log('ADMIN_PANEL - is ACTIVE');
let buffer_type = '';
let buffer_obj = null;
let buffer_id = -1;
let buffer_type_loading = '';

$(document).ready(function() {
    if($('#uploader-loader').length > 0) {
        document.querySelector('#uploader-loader').onchange = function (event) {
            const files = event.target.files;
            for (let file of files) {
                if (file.size <= 11 * 1024 * 1024) {
                    console.dir(file);
                    uploader(file, buffer_obj, buffer_type, buffer_id);
                } else {
                    say("Разрешено загружать файлы до 11 Мб", 2);
                }
            }
        };
    }
});


$(document).on('mouseenter', '.editor-place', function(e) {
    e.stopPropagation();
    e.preventDefault();
});
$(document).on('click', '.editor-place', function(e) {
    let obj = this;
    let id = $(obj).attr('data-id');
    let types = $(obj).attr('data-type');
    let txt = $(obj).parent().find('a span').text();
    if(txt === '') {
        txt = $(obj).parent().find('.name-cat').text();
    }
    e.stopPropagation();
    e.preventDefault();
    let lst = {
        'Изменить название': function() {
            info_inputString(undefined, function() {
                SENDER('change_cataloger_item_name', {type: $(obj).attr('data-type'), id: id, new_name: bufferText}, function(mess) {
                    mess_executer(mess, function(mess) {
                        location.reload();
                    });
                });
            }, 'Введите новое значение', txt, 'Изменить');
        },
        'Загрузить новое изображение': function() {
            buffer_obj = obj;
            buffer_type = types;
            buffer_id = id;
            $('#uploader-loader').click();
        },
        'Загрузить БАННЕР': function() {
            buffer_obj = obj;
            buffer_type = types;
            buffer_id = id;
            buffer_type_loading = 'banner';
            console.log('buffer_type='+buffer_type);
            console.log('buffer_id='+buffer_id);
        },
    };
    info_variants(undefined, lst);
});

$(document).on('dragover', '.editor-place', function (e) {
    $(this).addClass('load-in');
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('dragleave', '.editor-place', function (e) {
    $(this).removeClass('load-in');
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('drop', '.editor-place', function (e) {
    buffer_obj = $(this).closest('.swiper-slide');
    buffer_type = $(this).attr('data-type');
    buffer_id = $(this).attr('data-id');

    $('.load-in').removeClass('load-in');
    let o = this;
    let dt = null;
    if (typeof e.originalEvent === 'object') {
        dt = e.originalEvent.dataTransfer;
    } else {
        dt = e.clipboardData;
    }
    e.stopPropagation();
    e.preventDefault();
    if (!dt && !dt.files) {
        return false;
    }
    // Получить список загружаемых файлов
    let files = dt.files;
    for (let file of files) {
        uploader(file, buffer_obj, buffer_type, buffer_id);
    }
    console.dir(files);
});

function uploader(file, obj, type, id) {
    console.log('Файл ------------');
    if(file.type !== 'image/png' && file.type !== 'image/webp' && file.type !== 'image/jpeg') {
        say('Для данной загрузки необходимы только файлы (.png или .webp)', 2);
        return false;
    }
    console.dir(file);
    let ind = $('<div class="ind"><span>'+file.name+'</span><div class="ind-ind"></div></div>')
    $(obj).append(ind);

    const formData = new FormData();
    formData.append("userfile", file);
    formData.append("com", 'files');
    formData.append("table", 'file');
    formData.append("column", 'none');
    formData.append("change_cataloger_image", type);
    formData.append("cataloger_id", id);
    formData.append("id", -1);

    const xhr = new XMLHttpRequest();
    xhr.upload.onprogress = function(event) {
        let percent = 100 - Math.round(event.total - event.loaded) / event.total * 100;
        percent = Math.round(percent);
        $(ind).find('.ind-ind').css('width', percent+'%');
        // console.log('Загрузка данных: '+percent+' %');
    };
    xhr.onload = xhr.onerror = function() {
        if (this.status === 200) {
            let mess = JSON.parse(xhr.response);
            console.dir(mess);
            if(mess.status === 'ok') {
                $(ind).remove();
                if($(buffer_obj).find('a img').length > 0) {
                    $(buffer_obj).find('a img').remove();
                }
                $(buffer_obj).find('a').append('<img src="/IMG/img300x300/'+mess.sys_name+'">');
                say('Изображение загружено, если ничего не изменилось - обновите страницу.');
            }
        } else {
            say('Не удалось отправить файл');
        }
    };
    xhr.open("POST", domain, true);
    xhr.responseType = 'text';
    xhr.send(formData);
}


