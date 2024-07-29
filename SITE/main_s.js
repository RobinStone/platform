$(document).on('click', 'input[name="code-r"]', function(e) {    let obj = this;    clipboard_get(function(clip) {        $(obj).val(clip);    });});function auth() {    $('.auth-form').removeClass('invisible');    setTimeout(function() {        $('.auth-form').addClass('open');        setTimeout(function() {            setOverlayJust();            $('main').addClass('blur');        }, 100);    }, 10);}function hide_auth() {    $('.auth-form').removeClass('open');    delOvelay();    setTimeout(function() {        $('main').removeClass('blur');        $('.auth-form').addClass('invisible');    }, 300);}function pass_no_pass(obj) {    let types = $(obj).parent().find('input').attr('type');    if(types === 'password') {        $(obj).parent().find('input').attr('type', 'text');    } else {        $(obj).parent().find('input').attr('type', 'password');    }}function auth_send() {    let arr = {        'email-r':$('.auth-form input[name="email-r"]').val(),        'password-r':$('.auth-form input[name="password-r"]').val(),    };    console.dir(arr);    AJAX('/', {'email-r': arr['email-r'], 'password-r': arr['password-r'], 'auth-r':'rbs'}, function(mess) {        switch(mess) {            case 'admin':                location.href = '/admin';                break;            case 'ok':                location.href = '/';                break;            case 'profil':                location.href = '/profil';                break;            default:                say('Таких авторизационных данных<br>в нашей базе - обнаружено...', 2);                break;        }    });    return false;}function reg() {    let form = $('<form id="reg-form" class="auth-form open" method="post" action="/"></form>');    $('#body-s').append(form);    loadTemplateIn('reg-form', 'reg-form', {}, function(e) {        $('#auth-form').addClass('invisible');        $('#auth-form').removeClass('open');    });}function reg_send() {    AJAX('/', {'phone-r':$('#reg-form input[name="phone-r"]').val(), 'name-r':$('#reg-form input[name="name-r"]').val(), 'email-r':$('#reg-form input[name="email-r"]').val(), 'password-r':$('#reg-form input[name="password-r"]').val(), 'reg-r':'rbs'}, function(mess) {        mess_executer(mess, function() {            // say('Для активации вашей учётной записи - перейдите в вашу почту, найдите письмо от нас и пройдите по предложенной там ссылке!', 4);            $('#container').remove();            $('#reg-form').remove();            open_popup('message', {text: 'Для активации вашей учётной записи - перейдите в вашу почту,<br>найдите письмо от нас и пройдите по предложенной там ссылке!'});        });    });    return false;}function email_code_auth(email='') {    if(email === '') {        email = $('input[name="email-r"]').val();    }    $('.auth-form-inner .auth-field:nth-child(3)').addClass('invisible');    $('.auth-form-inner label:nth-child(4)').addClass('invisible');    $('.auth-form-inner .forgot-pass').addClass('invisible');    $('.auth-form-inner .btn:nth-child(6)').addClass('invisible');    $('.auth-form-inner .auth-field:nth-child(2) legend').text('Введите ваш E-Mail');    if(email.length < 5) {        rubber($('input[name="email-r"]'));    } else {        buffer_app = 'SHOPS';        SENDER_APP('auth_email_code', {email: email}, function(mess) {            mess_executer(mess, function(mess) {                $('.auth-form-inner p:first-child').html('На указанную почту вам был отправлен код,<br>введите его в поле ниже и нажмите<br>"ВОЙТИ ПО КОДУ".');                $('.auth-form-inner .auth-field:nth-child(2) legend').text('5-ти значный код из E-Mail');                $('input[name="email-r"]').attr('type', 'number');                $('input[name="email-r"]').attr('name', 'code-r');                $('input[name="code-r"]').val('');                $('input[name="code-r"]').attr('placeholder', '');                setTimeout(function() {                    rubber($('input[name="code-r"]'));                }, 500);                $('div.paddinger-5px').addClass('invisible');                $('div.paddinger-5px').after($('<button onclick="auth_code()" type="button" class="btn flex align-center between gap-15 svg-white" style="padding: 17px 12px; margin-bottom: 15px">ВОЙТИ ПО КОДУ</button>'));            });        });    }}function auth_code() {    let code = $('input[name="code-r"]').val();    if(code.length === 5) {        buffer_app = 'SHOPS';        SENDER_APP('auth_code', {code: code}, function(mess) {            mess_executer(mess, function(mess) {                location.href = 'https://rumbra.ru/profil?title=account';            });        });    }}function sms_reg(phone='') {    $('#krest').attr('onclick', 'location.reload()');    if(phone === '') {        $('.auth-form-inner p:first-child').html('Если нет аккаунта<br>Введите ваш номер телефона,<br>(обязательно с кодом страны), напр. 7 9232811515');        $('#auth-form header span:first-child').text('Регистрация');    } else {        $('.auth-form-inner p:first-child').html('Ожидайте пожалуйста...');        $('#auth-form header span:first-child').text('Авторизация по номеру');    }    $('.auth-form-inner fieldset:nth-child(2) legend').text('Ваш номер телефона');    $('.auth-form-inner fieldset:nth-child(2) input').val('');    $('.auth-form-inner fieldset:nth-child(2) input').attr('type', 'tel');    $('.auth-form-inner fieldset:nth-child(2) input').attr('name', 'phone');    $('.auth-form-inner fieldset:nth-child(2) input').attr('autocomplete', 'off');    $('.auth-form-inner fieldset:nth-child(2) input').addClass('phone-corrector');    $('.auth-form-inner fieldset:nth-child(2) input').attr('placeholder', '+7 XXXXXXXXXX');    $('.auth-form-inner fieldset:nth-child(3)').remove();    $('.forgot-pass').remove();    $('.auth-form-inner button:nth-child(3)').remove();    $('.auth-form-inner .paddinger-5px button:first-child').remove();    $('.auth-form-inner .paddinger-5px button').addClass('disabled');    $('.auth-form-inner .paddinger-5px button').addClass('btn-send-sms');    $('.auth-form-inner .paddinger-5px button svg').after('<span>Получить код</span>');    $('.auth-form-inner label').remove();    $('.btn-send-sms').attr('onclick', 'registred_sms()');    $('.btn-send-sms').text('Отправить');    if(phone !== '') {        $('.auth-form-inner fieldset:nth-child(2) input').val(VALUES.clear_phone(phone));        $('.btn-send-sms').text('ПОДТВЕРДИТЬ');        setTimeout(function() {            auth_sms();        }, 500);    }    // $('.auth-form-inner .paddinger-5px').append('<button type="button" onclick="coder_inputer()">CODE</button>');}function tele_reg_auth() {    location.href = 'https://t.me/rumbra_bot?start=auth';}let correct = ['0','1','2','3','4','5','6','7','8','9']; // +375 44 772-59-49$(document).on('keydown', '.phone-corrector', function(e) {    if(!correct.includes(e.key) && e.keyCode !== 8 && e.keyCode !== 37 && e.keyCode !== 39) {        e.preventDefault();    }});$(document).on('input', '.phone-corrector', function(e) {    let txt = $('.phone-corrector').val();    let cleanedText = txt.replace(/\D/g, '');    $('.phone-corrector').val(cleanedText);    setTimeout(function() {        if($('.phone-corrector').val().length >= 11 && $('.phone-corrector').val().length <= 12) {            $('.btn-send-sms').removeClass('disabled');        } else {            $('.btn-send-sms').addClass('disabled');        }    }, 100);});$(document).on('keyup', '.phone-corrector', function(e) {    if($('.phone-corrector').val().length >= 11 && $('.phone-corrector').val().length <= 12) {        $('.btn-send-sms').removeClass('disabled');    } else {        $('.btn-send-sms').addClass('disabled');    }});function registred_sms() {    let phone = $('.phone-corrector').val();    if(phone.length >= 11 && phone.length <=12) {        SENDER('registred_sms', {phone: phone}, function(mess) {            mess_executer(mess, function(mess) {                coder_inputer(phone);            });        });    }}function auth_sms() {    let phone = $('.phone-corrector').val();    if(phone.length >= 11 && phone.length <=12) {        SENDER('auth_sms', {phone: phone}, function(mess) {            mess_executer(mess, function(mess) {                coder_inputer(phone, true);            });        });    }}function coder_inputer(phone='000', auth=false) {    if(auth) {        $('.auth-form-inner p:first-child').html('На номер <b>'+phone+'</b> отправлен 5-тизначный код.<br>Для входа в систему, введите его в поле ниже.');    } else {        $('.auth-form-inner p:first-child').html('На номер <b>'+phone+'</b> отправлен 5-тизначный код.<br>Для успешного завершения регистрации, введите его в поле ниже.');    }    let inpt = $('.auth-form-inner fieldset:nth-child(2) input');    inpt.val('');    inpt.attr('type', 'number');    inpt.attr('name', 'code');    inpt.removeClass('phone-corrector');    inpt.attr('placeholder', 'XXXXX');    $('.auth-field legend').text('XXXXX - код из SMS');    let btn = $('.btn-send-sms');    if(auth) {        btn.find('span').text('ВХОД');        btn.attr('onclick', 'complite_registred_sms(true)');    } else {        btn.find('span').text('Завершить регистрацию');        btn.attr('onclick', 'complite_registred_sms()');    }    $('.btn-send-sms').removeClass('disabled');}function complite_registred_sms(auth = false) {    let code = parseInt($('.auth-form-inner fieldset:nth-child(2) input').val());    if(code < 10000 || code > 99999) {        say('Формат кода не верный!..', 2);        return false;    }    $('.btn-send-sms').addClass('disabled');    SENDER('complite_registred_sms', {code: code, auth: auth}, function(mess) {        mess = JSON.parse(mess);        if(mess.status === 'error') {            say(mess.body, 2);            setTimeout(function() {                location.reload();            }, 7000);        } else if(mess.status === 'ok') {            if(auth) {                location.href = '/profil?title=account&message=autorized';            } else {                location.href = '/profil?title=account&message=registrated';            }            say('Успешно.<br>Сейчас вы будете перенаправлены на ваш профиль');        }    });}function forgot_pass() {    let log = $('input[name="email-r"]').val();    if(log.length < 3) {        say('Введите в поле<br>Ваш телефон, логин или e-mail<br>что-то из этого...<br>Пароль можно не заполнять.', 2);        return false;    }    $('#auth-form.open').removeClass('open');    setTimeout(function() {        delOvelay();        setTimeout(function() {            open_popup('forgot_pass', {type: VALUES.email_phone_string(log), text: log});        }, 100);    }, 300);}setTimeout(function() {    if(isset_param_in_address_row('auth')) {        auth();    }}, 500);
setTimeout(function() {    let url = new URL(location.href);    if(url.searchParams.get('auth') === 'true') {        console.log('ok');        auth();    }}, 50);function show_hidden_chat() {    if($('.btn-chat').is(':visible')) {        status_chat = 'visible';        $('.btn-chat').css('display', 'none');        $('.chat-m-wrapper').addClass('slowly');        setTimeout(function() {            $('.chat-m-wrapper').removeClass('hidden-in-right');            $('.status.action-btn').click();            setTimeout(function() {                $('.chat-m-wrapper').removeClass('slowly');            }, 500);        }, 10);        $('#support-btn').css('display', 'none');        $('header.drag-field > div span:nth-child(2)').text('Ожидаем ответа');        if($mobile) {            $(document).on('focus', '#message-m', function(e) {                $('.chat-m-wrapper').addClass('vh50');                scroll_to_bottom($('.chat-m-content'));            });            $(document).on('click', '.chat-m-content', function(e) {                $('.chat-m-wrapper').removeClass('vh50');            });        }    } else {        status_chat = 'hidden';        $('.btn-chat').css('display', 'flex');        $('.chat-m-wrapper').addClass('hidden-in-right');    }}window.addEventListener('scroll', function() {    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {        // Пользователь прокрутил до конца        console.log('Прокручено до конца!');        show_next_pagination_cards(8);    }});// let initialPoint;// let finalPoint;// document.addEventListener('touchstart', function (event) {//     // event.preventDefault();//     // event.stopPropagation();//     initialPoint = event.changedTouches[0];// }, false);// document.addEventListener('touchend', function (event) {//     // event.preventDefault();//     // event.stopPropagation();//     finalPoint = event.changedTouches[0];//     let xAbs = Math.abs(initialPoint.pageX - finalPoint.pageX);//     let yAbs = Math.abs(initialPoint.pageY - finalPoint.pageY);//     if (xAbs > 120) {//         if (xAbs > yAbs) {//             if (finalPoint.pageX < initialPoint.pageX) {//                 swipeLeft();//             }//         }//     }// }, false);// function swipeLeft() {//     say('inleft');// }// function swipeRight() {//// }
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

$(document).on('click', '.products-list li button.like', function(e) {    e.stopPropagation();    e.preventDefault();});$(document).on('click', '.products-list li button.basket', function(e) {    e.stopPropagation();    e.preventDefault();});$(document).on('click', '.products-list li', function(e) {    location.href = $(this).find('a').attr('href');});function favorite_check(obj, id_shop, id_product) {    buffer_app = 'SHOPS';    SENDER_APP('add_rem_favorite', {id_shop: id_shop, id_product: id_product}, function(mess) {        mess_executer(mess, function(mess) {            if(mess.params === true) {                $(obj).find('span.svg-wrapper').addClass('hart-red');                on_hart(obj);            } else {                $(obj).find('span.svg-wrapper').removeClass('hart-red');                off_hart(obj);            }        });    });}function on_hart(obj) {    let hart = $('<img class="hart inline-block" width="30" height="30" src="/DOWNLOAD/20230824-171803_id-2-114735.svg">');    $(obj).append(hart);    setTimeout(function() {        $(hart).addClass('min-hart');        setTimeout(function() {            $(hart).remove();        }, 500);    }, 20);}function off_hart(obj) {    let hart = $('<img class="hart-2 inline-block" width="30" height="30" src="/DOWNLOAD/20230824-171803_id-2-114735.svg">');    $(obj).append(hart);    setTimeout(function() {        $(hart).addClass('max-hart');        setTimeout(function() {            $(hart).remove();        }, 500);    }, 20);}function in_basket(code_product, obj) {    SENDER('in_basket', {code: code_product}, function(mess) {        mess_executer(mess, function(mess) {            console.dir(mess);            if(mess.params === '+') {                basket_set('+');                $(obj).html('<img width="20" height="20" src="/DOWNLOAD/20231007-181635_id-2-704258.svg">');            }            if(mess.params === '-') {                basket_set('-');                $(obj).html('<img width="20" height="20" src="/DOWNLOAD/20230609-193649_id-2-923913.svg">');            }        });    });}let access_for_next_cards = true;/** * Показывает следующую порцию карточек с учётом уже показанных * @param count_cards * @param params * @param isset_ids_arr */function show_next_pagination_cards(count_cards, params={}, isset_ids_arr=[]) {    if(access_for_next_cards) {        access_for_next_cards = false;        let arr = $('li[data-indexer]').map(function () {            return parseInt($(this).attr('data-indexer'));        }).toArray();        console.dir(arr);        if(isset_ids_arr.length === 0) {            isset_ids_arr = arr;        }        params['count'] = count_cards;        params['isset'] = isset_ids_arr;        buffer_app = 'SHOPS';        SENDER_APP('get_next_cards', params, function(mess) {            mess_executer(mess, function(mess) {                $('.products-list ul').append(mess.params.list);                if(mess.params.count > 0) {                    access_for_next_cards = true;                }            })        });    }}
/* * jPlayer Plugin for jQuery JavaScript Library * http://www.jplayer.org * * Copyright (c) 2009 - 2014 Happyworm Ltd * Licensed under the MIT license. * http://opensource.org/licenses/MIT * * Author: Mark J Panaghiston * Version: 2.9.2 * Date: 14th December 2014 *//* Support for Zepto 1.0 compiled with optional data module. * For AMD or NODE/CommonJS support, you will need to manually switch the related 2 lines in the code below. * Search terms: "jQuery Switch" and "Zepto Switch" --- */$(document).on('click', '#jp_container_1 .close-map-btn', function () {    $(this).closest('#jp_container_1').toggleClass('opener');});$(document).on('dragover', '.player-footer', function (e) {    $(this).addClass('load-in');    e.preventDefault();    e.stopPropagation();});$(document).on('dragstart', '.buffer-file-dragger', function (e) {});$(document).on('dragleave', '.player-footer', function (e) {    $(this).removeClass('load-in');    e.preventDefault();    e.stopPropagation();});$(document).on('drop', '.player-footer', function (e) {    $('.load-in').removeClass('load-in');    let o = this;    let dt = null;    if (typeof e.originalEvent === 'object') {        dt = e.originalEvent.dataTransfer;    } else {        dt = e.clipboardData;    }    e.stopPropagation();    e.preventDefault();    if (!dt && !dt.files) {        return false;    }    // Получить список загружаемых файлов    let files = dt.files;    for (let file of files) {        upload__File(file);    }    console.dir(files);});function open_close_jplayer() {    $('#jp_container_1').toggleClass('opener');}function render_open_folder(arr) {    console.dir(arr);    $('#folders').empty();    $('#folders').append(create_folder('Моя музыка', arr));    setTimeout(function() {        load_map();    }, 300);}function create_folder(folder_name, obj) {    let name = folder_name.split('/')[folder_name.split('/').length-1];    let drag = '';    if(name !== 'Моя музыка') {        drag = ' draggable="true" ';    }    let cont = $('<details'+drag+' class="folder" data-path="'+folder_name+'"><summary>'+name+'<div class="btns"><button title="Воспроизводит все файлы в папке и её подпапках" onclick="play_album(this)"><img style="height: 14px" src="/DOWNLOAD/20231108-103036_id-2-192700.svg"></button><button title="Создаёт новую папку в этой папке" onclick="add_folder_manual(this)"><img src="/DOWNLOAD/20231108-210257_id-2-734270.svg"></button><button title="Удалить папку со всеми вложениями" onclick="del_folder_quest(this)"><img src="/DOWNLOAD/20231108-100115_id-2-999083.svg"></button></div></summary></details>');    if (typeof obj['folders'] !== 'undefined' && Object.keys(obj['folders']).length > 0) {        for(let i in obj['folders']) {            $(cont).append(create_folder(i, obj['folders'][i]));        }    }    let ul = $('<ul class="play-list-folder"></ul>');    if (typeof obj['audio'] !== 'undefined' && Object.keys(obj['audio']).length > 0) {        for(let i in obj['audio']) {            $(ul).append('<li draggable="true" class="music-item flex between" data-id="'+obj['audio'][i]['id']+'" data-src="/DOWNLOAD/'+obj['audio'][i]['sys_name']+'">'+obj['audio'][i]['user_name']+'<div class="btns"><button title="Удалить композицию из трек-листа" onclick="del_track(this)"><img src="/DOWNLOAD/20231108-100115_id-2-999083.svg"></button></div></li>');        }    }    $(cont).append(ul);    return cont;}function play_one_track(obj) {    let name = $(obj).text();    let id = $(obj).attr('data-id');    let src = 'https://rumbra.ru'+$(obj).attr('data-src');    let playlist = jplayer;    clear_play_list();    setTimeout(function() {        addTrackToPlaylist(name, src, id);        setTimeout(function() {            // playlist.play(0);            $('.jp-play').click();            setTimeout(function() {                // $('.jp-play').click();                playlist.play(0);            }, 500);        }, 100);    }, 300);}function clear_play_list() {    let playlist = jplayer;    // playlist.pause();    playlist.setPlaylist([]);    // $("#jquery_jplayer_1").jPlayer("stop");}function play_album(obj) {    // console.log(obj);    let path = $(obj).closest('.folder').attr('data-path');    // let lst = $('#folders .folder[data-path="'+path+'"] > ul.play-list-folder > li');    let lst = $('#folders .folder[data-path="'+path+'"] ul.play-list-folder li');    console.dir(lst);    let playlist = jplayer;    clear_play_list();    setTimeout(function() {        $(lst).each(function(e,t) {            addTrackToPlaylist($(t).text(), $(t).attr('data-src'), $(t).attr('data-id'));        })        setTimeout(function() {            playlist.play(0);            // $('.jp-play').click();            setTimeout(function() {                playlist.play(0);                $('.jp-play').click();            }, 500);        }, 100);    }, 300);}function update_tree_player() {    buffer_app = 'MUSIC';    SENDER_APP('get_media_folder', {}, function(mess) {        mess_executer(mess, function(mess) {            console.dir(mess);            render_open_folder(mess.params);        });    })}function del_folder(folder_name) {    buffer_app = 'MUSIC';    SENDER_APP('del_folder', {name: folder_name}, function(mess) {        mess_executer(mess, function(mess) {            update_tree_player();        });    })}function add_folder(parent_folder_path, new_folder_name) {    buffer_app = 'MUSIC';    SENDER_APP('add_folder', {parent_folder_path: parent_folder_path, new_folder_name: new_folder_name}, function(mess) {        mess_executer(mess, function(mess) {            update_tree_player();        });    })}function add_folder_manual(obj) {    let parent_folder = $(obj).closest('.folder').attr('data-path');    info_inputString(undefined, function() {        add_folder(parent_folder, bufferText);    }, 'Создание новой папки внутри <span style="color:red;">'+parent_folder+'</span>]', 'Новая папка', 'Создать');}function del_folder_quest(obj) {    let path = $(obj).closest('.folder').attr('data-path');    info_qest(undefined, function() {        del_folder(path);    }, function() {    }, 'Подтвердите полное удаление<br>папки [<span style="color:red;">'+path+'</span>] со всеми подпапками и<br>со всеми трэками внутри ?', 'Да - всё удалить', 'Не удалять');}function save_map() {    setTimeout(function() {        let arr = [];        $('#folders .folder').each(function(e,t) {            if($(t).hasAttr('open')) {                arr.push($(t).attr('data-path'));            }        });        localStorage.setItem('playlist_map', JSON.stringify(arr));    }, 50);}function load_map() {    if(typeof localStorage.getItem('playlist_map') !== 'undefined') {        let arr = JSON.parse(localStorage.getItem('playlist_map'));        for(let i of arr) {            console.log(i);            $('#folders').find('.folder[data-path="'+i+'"]').prop('open', true);        }    }}// $(document).on('contextmenu', '.jp-playlist li', function(e) {//         let obj = this;//         e.preventDefault();//         // let currentTrack = jplayer.current();//         // let currentTrackId = currentTrack.data("id");//         // say($(obj).data('id'));//         // console.dir($(obj).data());//         console.dir(jplayer);//         console.dir(jplayer.playlist);// });(function (root, factory) {    if (typeof define === 'function' && define.amd) {        // AMD. Register as an anonymous module.        define(['jquery'], factory); // jQuery Switch        // define(['zepto'], factory); // Zepto Switch    } else if (typeof exports === 'object') {        // Node/CommonJS        factory(require('jquery')); // jQuery Switch        //factory(require('zepto')); // Zepto Switch    } else {        // Browser globals        if (root.jQuery) { // Use jQuery if available            factory(root.jQuery);        } else { // Otherwise, use Zepto            factory(root.Zepto);        }    }}(this, function ($, undefined) {    // Adapted from jquery.ui.widget.js (1.8.7): $.widget.bridge - Tweaked $.data(this,XYZ) to $(this).data(XYZ) for Zepto    $.fn.jPlayer = function (options) {        var name = "jPlayer";        var isMethodCall = typeof options === "string",            args = Array.prototype.slice.call(arguments, 1),            returnValue = this;        // allow multiple hashes to be passed on init        options = !isMethodCall && args.length ?            $.extend.apply(null, [true, options].concat(args)) :            options;        // prevent calls to internal methods        if (isMethodCall && options.charAt(0) === "_") {            return returnValue;        }        if (isMethodCall) {            this.each(function () {                var instance = $(this).data(name),                    methodValue = instance && $.isFunction(instance[options]) ?                        instance[options].apply(instance, args) :                        instance;                if (methodValue !== instance && methodValue !== undefined) {                    returnValue = methodValue;                    return false;                }            });        } else {            this.each(function () {                var instance = $(this).data(name);                if (instance) {                    // instance.option( options || {} )._init(); // Orig jquery.ui.widget.js code: Not recommend for jPlayer. ie., Applying new options to an existing instance (via the jPlayer constructor) and performing the _init(). The _init() is what concerns me. It would leave a lot of event handlers acting on jPlayer instance and the interface.                    instance.option(options || {}); // The new constructor only changes the options. Changing options only has basic support atm.                } else {                    $(this).data(name, new $.jPlayer(options, this));                }            });        }        return returnValue;    };    $.jPlayer = function (options, element) {        // allow instantiation without initializing for simple inheritance        if (arguments.length) {            this.element = $(element);            this.options = $.extend(true, {},                this.options,                options            );            var self = this;            this.element.bind("remove.jPlayer", function () {                self.destroy();            });            this._init();        }    };    // End of: (Adapted from jquery.ui.widget.js (1.8.7))    // Zepto is missing one of the animation methods.    if (typeof $.fn.stop !== 'function') {        $.fn.stop = function () {        };    }    // Emulated HTML5 methods and properties    $.jPlayer.emulateMethods = "load play pause";    $.jPlayer.emulateStatus = "src readyState networkState currentTime duration paused ended playbackRate";    $.jPlayer.emulateOptions = "muted volume";    // Reserved event names generated by jPlayer that are not part of the HTML5 Media element spec    $.jPlayer.reservedEvent = "ready flashreset resize repeat error warning";    // Events generated by jPlayer    $.jPlayer.event = {};    $.each(        [            'ready',            'setmedia', // Fires when the media is set            'flashreset', // Similar to the ready event if the Flash solution is set to display:none and then shown again or if it's reloaded for another reason by the browser. For example, using CSS position:fixed on Firefox for the full screen feature.            'resize', // Occurs when the size changes through a full/restore screen operation or if the size/sizeFull options are changed.            'repeat', // Occurs when the repeat status changes. Usually through clicks on the repeat button of the interface.            'click', // Occurs when the user clicks on one of the following: poster image, html video, flash video.            'error', // Event error code in event.jPlayer.error.type. See $.jPlayer.error            'warning', // Event warning code in event.jPlayer.warning.type. See $.jPlayer.warning            // Other events match HTML5 spec.            'loadstart',            'progress',            'suspend',            'abort',            'emptied',            'stalled',            'play',            'pause',            'loadedmetadata',            'loadeddata',            'waiting',            'playing',            'canplay',            'canplaythrough',            'seeking',            'seeked',            'timeupdate',            'ended',            'ratechange',            'durationchange',            'volumechange'        ],        function () {            $.jPlayer.event[this] = 'jPlayer_' + this;        }    );    $.jPlayer.htmlEvent = [ // These HTML events are bubbled through to the jPlayer event, without any internal action.        "loadstart",        // "progress", // jPlayer uses internally before bubbling.        // "suspend", // jPlayer uses internally before bubbling.        "abort",        // "error", // jPlayer uses internally before bubbling.        "emptied",        "stalled",        // "play", // jPlayer uses internally before bubbling.        // "pause", // jPlayer uses internally before bubbling.        "loadedmetadata",        // "loadeddata", // jPlayer uses internally before bubbling.        // "waiting", // jPlayer uses internally before bubbling.        // "playing", // jPlayer uses internally before bubbling.        "canplay",        "canplaythrough"        // "seeking", // jPlayer uses internally before bubbling.        // "seeked", // jPlayer uses internally before bubbling.        // "timeupdate", // jPlayer uses internally before bubbling.        // "ended", // jPlayer uses internally before bubbling.        // "ratechange" // jPlayer uses internally before bubbling.        // "durationchange" // jPlayer uses internally before bubbling.        // "volumechange" // jPlayer uses internally before bubbling.    ];    $.jPlayer.pause = function () {        $.jPlayer.prototype.destroyRemoved();        $.each($.jPlayer.prototype.instances, function (i, element) {            if (element.data("jPlayer").status.srcSet) { // Check that media is set otherwise would cause error event.                element.jPlayer("pause");            }        });    };    // Default for jPlayer option.timeFormat    $.jPlayer.timeFormat = {        showHour: false,        showMin: true,        showSec: true,        padHour: false,        padMin: true,        padSec: true,        sepHour: ":",        sepMin: ":",        sepSec: ""    };    var ConvertTime = function () {        this.init();    };    ConvertTime.prototype = {        init: function () {            this.options = {                timeFormat: $.jPlayer.timeFormat            };        },        time: function (s) { // function used on jPlayer.prototype._convertTime to enable per instance options.            s = (s && typeof s === 'number') ? s : 0;            var myTime = new Date(s * 1000),                hour = myTime.getUTCHours(),                min = this.options.timeFormat.showHour ? myTime.getUTCMinutes() : myTime.getUTCMinutes() + hour * 60,                sec = this.options.timeFormat.showMin ? myTime.getUTCSeconds() : myTime.getUTCSeconds() + min * 60,                strHour = (this.options.timeFormat.padHour && hour < 10) ? "0" + hour : hour,                strMin = (this.options.timeFormat.padMin && min < 10) ? "0" + min : min,                strSec = (this.options.timeFormat.padSec && sec < 10) ? "0" + sec : sec,                strTime = "";            strTime += this.options.timeFormat.showHour ? strHour + this.options.timeFormat.sepHour : "";            strTime += this.options.timeFormat.showMin ? strMin + this.options.timeFormat.sepMin : "";            strTime += this.options.timeFormat.showSec ? strSec + this.options.timeFormat.sepSec : "";            return strTime;        }    };    var myConvertTime = new ConvertTime();    $.jPlayer.convertTime = function (s) {        return myConvertTime.time(s);    };    // Adapting jQuery 1.4.4 code for jQuery.browser. Required since jQuery 1.3.2 does not detect Chrome as webkit.    $.jPlayer.uaBrowser = function (userAgent) {        var ua = userAgent.toLowerCase();        // Useragent RegExp        var rwebkit = /(webkit)[ \/]([\w.]+)/;        var ropera = /(opera)(?:.*version)?[ \/]([\w.]+)/;        var rmsie = /(msie) ([\w.]+)/;        var rmozilla = /(mozilla)(?:.*? rv:([\w.]+))?/;        var match = rwebkit.exec(ua) ||            ropera.exec(ua) ||            rmsie.exec(ua) ||            ua.indexOf("compatible") < 0 && rmozilla.exec(ua) ||            [];        return {browser: match[1] || "", version: match[2] || "0"};    };    // Platform sniffer for detecting mobile devices    $.jPlayer.uaPlatform = function (userAgent) {        var ua = userAgent.toLowerCase();        // Useragent RegExp        var rplatform = /(ipad|iphone|ipod|android|blackberry|playbook|windows ce|webos)/;        var rtablet = /(ipad|playbook)/;        var randroid = /(android)/;        var rmobile = /(mobile)/;        var platform = rplatform.exec(ua) || [];        var tablet = rtablet.exec(ua) ||            !rmobile.exec(ua) && randroid.exec(ua) ||            [];        if (platform[1]) {            platform[1] = platform[1].replace(/\s/g, "_"); // Change whitespace to underscore. Enables dot notation.        }        return {platform: platform[1] || "", tablet: tablet[1] || ""};    };    $.jPlayer.browser = {};    $.jPlayer.platform = {};    var browserMatch = $.jPlayer.uaBrowser(navigator.userAgent);    if (browserMatch.browser) {        $.jPlayer.browser[browserMatch.browser] = true;        $.jPlayer.browser.version = browserMatch.version;    }    var platformMatch = $.jPlayer.uaPlatform(navigator.userAgent);    if (platformMatch.platform) {        $.jPlayer.platform[platformMatch.platform] = true;        $.jPlayer.platform.mobile = !platformMatch.tablet;        $.jPlayer.platform.tablet = !!platformMatch.tablet;    }    // Internet Explorer (IE) Browser Document Mode Sniffer. Based on code at:    // http://msdn.microsoft.com/en-us/library/cc288325%28v=vs.85%29.aspx#GetMode    $.jPlayer.getDocMode = function () {        var docMode;        if ($.jPlayer.browser.msie) {            if (document.documentMode) { // IE8 or later                docMode = document.documentMode;            } else { // IE 5-7                docMode = 5; // Assume quirks mode unless proven otherwise                if (document.compatMode) {                    if (document.compatMode === "CSS1Compat") {                        docMode = 7; // standards mode                    }                }            }        }        return docMode;    };    $.jPlayer.browser.documentMode = $.jPlayer.getDocMode();    $.jPlayer.nativeFeatures = {        init: function () {            /* Fullscreen function naming influenced by W3C naming.			 * No support for: Mozilla Proposal: https://wiki.mozilla.org/Gecko:FullScreenAPI			 */            var d = document,                v = d.createElement('video'),                spec = {                    // http://www.w3.org/TR/fullscreen/                    w3c: [                        'fullscreenEnabled',                        'fullscreenElement',                        'requestFullscreen',                        'exitFullscreen',                        'fullscreenchange',                        'fullscreenerror'                    ],                    // https://developer.mozilla.org/en-US/docs/DOM/Using_fullscreen_mode                    moz: [                        'mozFullScreenEnabled',                        'mozFullScreenElement',                        'mozRequestFullScreen',                        'mozCancelFullScreen',                        'mozfullscreenchange',                        'mozfullscreenerror'                    ],                    // http://developer.apple.com/library/safari/#documentation/WebKit/Reference/ElementClassRef/Element/Element.html                    // http://developer.apple.com/library/safari/#documentation/UserExperience/Reference/DocumentAdditionsReference/DocumentAdditions/DocumentAdditions.html                    webkit: [                        '',                        'webkitCurrentFullScreenElement',                        'webkitRequestFullScreen',                        'webkitCancelFullScreen',                        'webkitfullscreenchange',                        ''                    ],                    // http://developer.apple.com/library/safari/#documentation/AudioVideo/Reference/HTMLVideoElementClassReference/HTMLVideoElement/HTMLVideoElement.html                    // https://developer.apple.com/library/safari/samplecode/HTML5VideoEventFlow/Listings/events_js.html#//apple_ref/doc/uid/DTS40010085-events_js-DontLinkElementID_5                    // Events: 'webkitbeginfullscreen' and 'webkitendfullscreen'                    webkitVideo: [                        'webkitSupportsFullscreen',                        'webkitDisplayingFullscreen',                        'webkitEnterFullscreen',                        'webkitExitFullscreen',                        '',                        ''                    ],                    ms: [                        '',                        'msFullscreenElement',                        'msRequestFullscreen',                        'msExitFullscreen',                        'MSFullscreenChange',                        'MSFullscreenError'                    ]                },                specOrder = [                    'w3c',                    'moz',                    'webkit',                    'webkitVideo',                    'ms'                ],                fs, i, il;            this.fullscreen = fs = {                support: {                    w3c: !!d[spec.w3c[0]],                    moz: !!d[spec.moz[0]],                    webkit: typeof d[spec.webkit[3]] === 'function',                    webkitVideo: typeof v[spec.webkitVideo[2]] === 'function',                    ms: typeof v[spec.ms[2]] === 'function'                },                used: {}            };            // Store the name of the spec being used and as a handy boolean.            for (i = 0, il = specOrder.length; i < il; i++) {                var n = specOrder[i];                if (fs.support[n]) {                    fs.spec = n;                    fs.used[n] = true;                    break;                }            }            if (fs.spec) {                var s = spec[fs.spec];                fs.api = {                    fullscreenEnabled: true,                    fullscreenElement: function (elem) {                        elem = elem ? elem : d; // Video element required for webkitVideo                        return elem[s[1]];                    },                    requestFullscreen: function (elem) {                        return elem[s[2]](); // Chrome and Opera want parameter (Element.ALLOW_KEYBOARD_INPUT) but Safari fails if flag used.                    },                    exitFullscreen: function (elem) {                        elem = elem ? elem : d; // Video element required for webkitVideo                        return elem[s[3]]();                    }                };                fs.event = {                    fullscreenchange: s[4],                    fullscreenerror: s[5]                };            } else {                fs.api = {                    fullscreenEnabled: false,                    fullscreenElement: function () {                        return null;                    },                    requestFullscreen: function () {                    },                    exitFullscreen: function () {                    }                };                fs.event = {};            }        }    };    $.jPlayer.nativeFeatures.init();    // The keyboard control system.    // The current jPlayer instance in focus.    $.jPlayer.focus = null;    // The list of element node names to ignore with key controls.    $.jPlayer.keyIgnoreElementNames = "A INPUT TEXTAREA SELECT BUTTON";    // The function that deals with key presses.    var keyBindings = function (event) {        var f = $.jPlayer.focus,            ignoreKey;        // A jPlayer instance must be in focus. ie., keyEnabled and the last one played.        if (f) {            // What generated the key press?            $.each($.jPlayer.keyIgnoreElementNames.split(/\s+/g), function (i, name) {                // The strings should already be uppercase.                if (event.target.nodeName.toUpperCase() === name.toUpperCase()) {                    ignoreKey = true;                    return false; // exit each.                }            });            if (!ignoreKey) {                // See if the key pressed matches any of the bindings.                $.each(f.options.keyBindings, function (action, binding) {                    // The binding could be a null when the default has been disabled. ie., 1st clause in if()                    if (                        (binding && $.isFunction(binding.fn)) &&                        ((typeof binding.key === 'number' && event.which === binding.key) ||                            (typeof binding.key === 'string' && event.key === binding.key))                    ) {                        event.preventDefault(); // Key being used by jPlayer, so prevent default operation.                        binding.fn(f);                        return false; // exit each.                    }                });            }        }    };    $.jPlayer.keys = function (en) {        var event = "keydown.jPlayer";        // Remove any binding, just in case enabled more than once.        $(document.documentElement).unbind(event);        if (en) {            $(document.documentElement).bind(event, keyBindings);        }    };    // Enable the global key control handler ready for any jPlayer instance with the keyEnabled option enabled.    $.jPlayer.keys(true);    $.jPlayer.prototype = {        count: 0, // Static Variable: Change it via prototype.        version: { // Static Object            script: "2.9.2",            needFlash: "2.9.0",            flash: "unknown"        },        options: { // Instanced in $.jPlayer() constructor            swfPath: "js", // Path to jquery.jplayer.swf. Can be relative, absolute or server root relative.            solution: "html, flash", // Valid solutions: html, flash, aurora. Order defines priority. 1st is highest,            supplied: "mp3", // Defines which formats jPlayer will try and support and the priority by the order. 1st is highest,            auroraFormats: "wav", // List the aurora.js codecs being loaded externally. Its core supports "wav". Specify format in jPlayer context. EG., The aac.js codec gives the "m4a" format.            preload: 'metadata',  // HTML5 Spec values: none, metadata, auto.            volume: 0.8, // The volume. Number 0 to 1.            muted: false,            remainingDuration: false, // When true, the remaining time is shown in the duration GUI element.            toggleDuration: false, // When true, clicks on the duration toggle between the duration and remaining display.            captureDuration: true, // When true, clicks on the duration are captured and no longer propagate up the DOM.            playbackRate: 1,            defaultPlaybackRate: 1,            minPlaybackRate: 0.5,            maxPlaybackRate: 4,            wmode: "opaque", // Valid wmode: window, transparent, opaque, direct, gpu.            backgroundColor: "#000000", // To define the jPlayer div and Flash background color.            cssSelectorAncestor: "#jp_container_1",            cssSelector: { // * denotes properties that should only be required when video media type required. _cssSelector() would require changes to enable splitting these into Audio and Video defaults.                videoPlay: ".jp-video-play", // *                play: ".jp-play",                pause: ".jp-pause",                stop: ".jp-stop",                seekBar: ".jp-seek-bar",                playBar: ".jp-play-bar",                mute: ".jp-mute",                unmute: ".jp-unmute",                volumeBar: ".jp-volume-bar",                volumeBarValue: ".jp-volume-bar-value",                volumeMax: ".jp-volume-max",                playbackRateBar: ".jp-playback-rate-bar",                playbackRateBarValue: ".jp-playback-rate-bar-value",                currentTime: ".jp-current-time",                duration: ".jp-duration",                title: ".jp-title",                fullScreen: ".jp-full-screen", // *                restoreScreen: ".jp-restore-screen", // *                repeat: ".jp-repeat",                repeatOff: ".jp-repeat-off",                gui: ".jp-gui", // The interface used with autohide feature.                noSolution: ".jp-no-solution" // For error feedback when jPlayer cannot find a solution.            },            stateClass: { // Classes added to the cssSelectorAncestor to indicate the state.                playing: "jp-state-playing",                seeking: "jp-state-seeking",                muted: "jp-state-muted",                looped: "jp-state-looped",                fullScreen: "jp-state-full-screen",                noVolume: "jp-state-no-volume"            },            useStateClassSkin: false, // A state class skin relies on the state classes to change the visual appearance. The single control toggles the effect, for example: play then pause, mute then unmute.            autoBlur: true, // GUI control handlers will drop focus after clicks.            smoothPlayBar: false, // Smooths the play bar transitions, which affects clicks and short media with big changes per second.            fullScreen: false, // Native Full Screen            fullWindow: false,            autohide: {                restored: false, // Controls the interface autohide feature.                full: true, // Controls the interface autohide feature.                fadeIn: 200, // Milliseconds. The period of the fadeIn anim.                fadeOut: 600, // Milliseconds. The period of the fadeOut anim.                hold: 1000 // Milliseconds. The period of the pause before autohide beings.            },            loop: false,            repeat: function (event) { // The default jPlayer repeat event handler                if (event.jPlayer.options.loop) {                    $(this).unbind(".jPlayerRepeat").bind($.jPlayer.event.ended + ".jPlayer.jPlayerRepeat", function () {                        $(this).jPlayer("play");                    });                } else {                    $(this).unbind(".jPlayerRepeat");                }            },            nativeVideoControls: {                // Works well on standard browsers.                // Phone and tablet browsers can have problems with the controls disappearing.            },            noFullWindow: {                msie: /msie [0-6]\./,                ipad: /ipad.*?os [0-4]\./,                iphone: /iphone/,                ipod: /ipod/,                android_pad: /android [0-3]\.(?!.*?mobile)/,                android_phone: /(?=.*android)(?!.*chrome)(?=.*mobile)/,                blackberry: /blackberry/,                windows_ce: /windows ce/,                iemobile: /iemobile/,                webos: /webos/            },            noVolume: {                ipad: /ipad/,                iphone: /iphone/,                ipod: /ipod/,                android_pad: /android(?!.*?mobile)/,                android_phone: /android.*?mobile/,                blackberry: /blackberry/,                windows_ce: /windows ce/,                iemobile: /iemobile/,                webos: /webos/,                playbook: /playbook/            },            timeFormat: {                // Specific time format for this instance. The supported options are defined in $.jPlayer.timeFormat                // For the undefined options we use the default from $.jPlayer.timeFormat            },            keyEnabled: false, // Enables keyboard controls.            audioFullScreen: false, // Enables keyboard controls to enter full screen with audio media.            keyBindings: { // The key control object, defining the key codes and the functions to execute.                // The parameter, f = $.jPlayer.focus, will be checked truethy before attempting to call any of these functions.                // Properties may be added to this object, in key/fn pairs, to enable other key controls. EG, for the playlist add-on.                play: {                    key: 80, // p                    fn: function (f) {                        if (f.status.paused) {                            f.play();                        } else {                            f.pause();                        }                    }                },                fullScreen: {                    key: 70, // f                    fn: function (f) {                        if (f.status.video || f.options.audioFullScreen) {                            f._setOption("fullScreen", !f.options.fullScreen);                        }                    }                },                muted: {                    key: 77, // m                    fn: function (f) {                        f._muted(!f.options.muted);                    }                },                volumeUp: {                    key: 190, // .                    fn: function (f) {                        f.volume(f.options.volume + 0.1);                    }                },                volumeDown: {                    key: 188, // ,                    fn: function (f) {                        f.volume(f.options.volume - 0.1);                    }                },                loop: {                    key: 76, // l                    fn: function (f) {                        f._loop(!f.options.loop);                    }                }            },            verticalVolume: false, // Calculate volume from the bottom of the volume bar. Default is from the left. Also volume affects either width or height.            verticalPlaybackRate: false,            globalVolume: false, // Set to make volume and muted changes affect all jPlayer instances with this option enabled            idPrefix: "jp", // Prefix for the ids of html elements created by jPlayer. For flash, this must not include characters: . - + * / \            noConflict: "jQuery",            emulateHtml: false, // Emulates the HTML5 Media element on the jPlayer element.            consoleAlerts: true, // Alerts are sent to the console.log() instead of alert().            errorAlerts: false,            warningAlerts: false        },        optionsAudio: {            size: {                width: "0px",                height: "0px",                cssClass: ""            },            sizeFull: {                width: "0px",                height: "0px",                cssClass: ""            }        },        optionsVideo: {            size: {                width: "480px",                height: "270px",                cssClass: "jp-video-270p"            },            sizeFull: {                width: "100%",                height: "100%",                cssClass: "jp-video-full"            }        },        instances: {}, // Static Object        status: { // Instanced in _init()            src: "",            media: {},            paused: true,            format: {},            formatType: "",            waitForPlay: true, // Same as waitForLoad except in case where preloading.            waitForLoad: true,            srcSet: false,            video: false, // True if playing a video            seekPercent: 0,            currentPercentRelative: 0,            currentPercentAbsolute: 0,            currentTime: 0,            duration: 0,            remaining: 0,            videoWidth: 0, // Intrinsic width of the video in pixels.            videoHeight: 0, // Intrinsic height of the video in pixels.            readyState: 0,            networkState: 0,            playbackRate: 1, // Warning - Now both an option and a status property            ended: 0            /*		Persistant status properties created dynamically at _init():			width			height			cssClass			nativeVideoControls			noFullWindow			noVolume			playbackRateEnabled // Warning - Technically, we can have both Flash and HTML, so this might not be correct if the Flash is active. That is a niche case.*/        },        internal: { // Instanced in _init()            ready: false            // instance: undefined            // domNode: undefined            // htmlDlyCmdId: undefined            // autohideId: undefined            // mouse: undefined            // cmdsIgnored        },        solution: { // Static Object: Defines the solutions built in jPlayer.            html: true,            aurora: true,            flash: true        },        // 'MPEG-4 support' : canPlayType('video/mp4; codecs="mp4v.20.8"')        format: { // Static Object            mp3: {                codec: 'audio/mpeg',                flashCanPlay: true,                media: 'audio'            },            m4a: { // AAC / MP4                codec: 'audio/mp4; codecs="mp4a.40.2"',                flashCanPlay: true,                media: 'audio'            },            m3u8a: { // AAC / MP4 / Apple HLS                codec: 'application/vnd.apple.mpegurl; codecs="mp4a.40.2"',                flashCanPlay: false,                media: 'audio'            },            m3ua: { // M3U                codec: 'audio/mpegurl',                flashCanPlay: false,                media: 'audio'            },            oga: { // OGG                codec: 'audio/ogg; codecs="vorbis, opus"',                flashCanPlay: false,                media: 'audio'            },            flac: { // FLAC                codec: 'audio/x-flac',                flashCanPlay: false,                media: 'audio'            },            wav: { // PCM                codec: 'audio/wav; codecs="1"',                flashCanPlay: false,                media: 'audio'            },            webma: { // WEBM                codec: 'audio/webm; codecs="vorbis"',                flashCanPlay: false,                media: 'audio'            },            fla: { // FLV / F4A                codec: 'audio/x-flv',                flashCanPlay: true,                media: 'audio'            },            rtmpa: { // RTMP AUDIO                codec: 'audio/rtmp; codecs="rtmp"',                flashCanPlay: true,                media: 'audio'            },            m4v: { // H.264 / MP4                codec: 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',                flashCanPlay: true,                media: 'video'            },            m3u8v: { // H.264 / AAC / MP4 / Apple HLS                codec: 'application/vnd.apple.mpegurl; codecs="avc1.42E01E, mp4a.40.2"',                flashCanPlay: false,                media: 'video'            },            m3uv: { // M3U                codec: 'audio/mpegurl',                flashCanPlay: false,                media: 'video'            },            ogv: { // OGG                codec: 'video/ogg; codecs="theora, vorbis"',                flashCanPlay: false,                media: 'video'            },            webmv: { // WEBM                codec: 'video/webm; codecs="vorbis, vp8"',                flashCanPlay: false,                media: 'video'            },            flv: { // FLV / F4V                codec: 'video/x-flv',                flashCanPlay: true,                media: 'video'            },            rtmpv: { // RTMP VIDEO                codec: 'video/rtmp; codecs="rtmp"',                flashCanPlay: true,                media: 'video'            }        },        _init: function () {            var self = this;            this.element.empty();            this.status = $.extend({}, this.status); // Copy static to unique instance.            this.internal = $.extend({}, this.internal); // Copy static to unique instance.            // Initialize the time format            this.options.timeFormat = $.extend({}, $.jPlayer.timeFormat, this.options.timeFormat);            // On iOS, assume commands will be ignored before user initiates them.            this.internal.cmdsIgnored = $.jPlayer.platform.ipad || $.jPlayer.platform.iphone || $.jPlayer.platform.ipod;            this.internal.domNode = this.element.get(0);            // Add key bindings focus to 1st jPlayer instanced with key control enabled.            if (this.options.keyEnabled && !$.jPlayer.focus) {                $.jPlayer.focus = this;            }            // A fix for Android where older (2.3) and even some 4.x devices fail to work when changing the *audio* SRC and then playing immediately.            this.androidFix = {                setMedia: false, // True when media set                play: false, // True when a progress event will instruct the media to play                pause: false, // True when a progress event will instruct the media to pause at a time.                time: NaN // The play(time) parameter            };            if ($.jPlayer.platform.android) {                this.options.preload = this.options.preload !== 'auto' ? 'metadata' : 'auto'; // Default to metadata, but allow auto.            }            this.formats = []; // Array based on supplied string option. Order defines priority.            this.solutions = []; // Array based on solution string option. Order defines priority.            this.require = {}; // Which media types are required: video, audio.            this.htmlElement = {}; // DOM elements created by jPlayer            this.html = {}; // In _init()'s this.desired code and setmedia(): Accessed via this[solution], where solution from this.solutions array.            this.html.audio = {};            this.html.video = {};            this.aurora = {}; // In _init()'s this.desired code and setmedia(): Accessed via this[solution], where solution from this.solutions array.            this.aurora.formats = [];            this.aurora.properties = [];            this.flash = {}; // In _init()'s this.desired code and setmedia(): Accessed via this[solution], where solution from this.solutions array.            this.css = {};            this.css.cs = {}; // Holds the css selector strings            this.css.jq = {}; // Holds jQuery selectors. ie., $(css.cs.method)            this.ancestorJq = []; // Holds jQuery selector of cssSelectorAncestor. Init would use $() instead of [], but it is only 1.4+            this.options.volume = this._limitValue(this.options.volume, 0, 1); // Limit volume value's bounds.            // Create the formats array, with prority based on the order of the supplied formats string            $.each(this.options.supplied.toLowerCase().split(","), function (index1, value1) {                var format = value1.replace(/^\s+|\s+$/g, ""); //trim                if (self.format[format]) { // Check format is valid.                    var dupFound = false;                    $.each(self.formats, function (index2, value2) { // Check for duplicates                        if (format === value2) {                            dupFound = true;                            return false;                        }                    });                    if (!dupFound) {                        self.formats.push(format);                    }                }            });            // Create the solutions array, with prority based on the order of the solution string            $.each(this.options.solution.toLowerCase().split(","), function (index1, value1) {                var solution = value1.replace(/^\s+|\s+$/g, ""); //trim                if (self.solution[solution]) { // Check solution is valid.                    var dupFound = false;                    $.each(self.solutions, function (index2, value2) { // Check for duplicates                        if (solution === value2) {                            dupFound = true;                            return false;                        }                    });                    if (!dupFound) {                        self.solutions.push(solution);                    }                }            });            // Create Aurora.js formats array            $.each(this.options.auroraFormats.toLowerCase().split(","), function (index1, value1) {                var format = value1.replace(/^\s+|\s+$/g, ""); //trim                if (self.format[format]) { // Check format is valid.                    var dupFound = false;                    $.each(self.aurora.formats, function (index2, value2) { // Check for duplicates                        if (format === value2) {                            dupFound = true;                            return false;                        }                    });                    if (!dupFound) {                        self.aurora.formats.push(format);                    }                }            });            this.internal.instance = "jp_" + this.count;            this.instances[this.internal.instance] = this.element;            // Check the jPlayer div has an id and create one if required. Important for Flash to know the unique id for comms.            if (!this.element.attr("id")) {                this.element.attr("id", this.options.idPrefix + "_jplayer_" + this.count);            }            this.internal.self = $.extend({}, {                id: this.element.attr("id"),                jq: this.element            });            this.internal.audio = $.extend({}, {                id: this.options.idPrefix + "_audio_" + this.count,                jq: undefined            });            this.internal.video = $.extend({}, {                id: this.options.idPrefix + "_video_" + this.count,                jq: undefined            });            this.internal.flash = $.extend({}, {                id: this.options.idPrefix + "_flash_" + this.count,                jq: undefined,                swf: this.options.swfPath + (this.options.swfPath.toLowerCase().slice(-4) !== ".swf" ? (this.options.swfPath && this.options.swfPath.slice(-1) !== "/" ? "/" : "") + "jquery.jplayer.swf" : "")            });            this.internal.poster = $.extend({}, {                id: this.options.idPrefix + "_poster_" + this.count,                jq: undefined            });            // Register listeners defined in the constructor            $.each($.jPlayer.event, function (eventName, eventType) {                if (self.options[eventName] !== undefined) {                    self.element.bind(eventType + ".jPlayer", self.options[eventName]); // With .jPlayer namespace.                    self.options[eventName] = undefined; // Destroy the handler pointer copy on the options. Reason, events can be added/removed in other ways so this could be obsolete and misleading.                }            });            // Determine if we require solutions for audio, video or both media types.            this.require.audio = false;            this.require.video = false;            $.each(this.formats, function (priority, format) {                self.require[self.format[format].media] = true;            });            // Now required types are known, finish the options default settings.            if (this.require.video) {                this.options = $.extend(true, {},                    this.optionsVideo,                    this.options                );            } else {                this.options = $.extend(true, {},                    this.optionsAudio,                    this.options                );            }            this._setSize(); // update status and jPlayer element size            // Determine the status for Blocklisted options.            this.status.nativeVideoControls = this._uaBlocklist(this.options.nativeVideoControls);            this.status.noFullWindow = this._uaBlocklist(this.options.noFullWindow);            this.status.noVolume = this._uaBlocklist(this.options.noVolume);            // Create event handlers if native fullscreen is supported            if ($.jPlayer.nativeFeatures.fullscreen.api.fullscreenEnabled) {                this._fullscreenAddEventListeners();            }            // The native controls are only for video and are disabled when audio is also used.            this._restrictNativeVideoControls();            // Create the poster image.            this.htmlElement.poster = document.createElement('img');            this.htmlElement.poster.id = this.internal.poster.id;            this.htmlElement.poster.onload = function () { // Note that this did not work on Firefox 3.6: poster.addEventListener("onload", function() {}, false); Did not investigate x-browser.                if (!self.status.video || self.status.waitForPlay) {                    self.internal.poster.jq.show();                }            };            this.element.append(this.htmlElement.poster);            this.internal.poster.jq = $("#" + this.internal.poster.id);            this.internal.poster.jq.css({'width': this.status.width, 'height': this.status.height});            this.internal.poster.jq.hide();            this.internal.poster.jq.bind("click.jPlayer", function () {                self._trigger($.jPlayer.event.click);            });            // Generate the required media elements            this.html.audio.available = false;            if (this.require.audio) { // If a supplied format is audio                this.htmlElement.audio = document.createElement('audio');                this.htmlElement.audio.id = this.internal.audio.id;                this.html.audio.available = !!this.htmlElement.audio.canPlayType && this._testCanPlayType(this.htmlElement.audio); // Test is for IE9 on Win Server 2008.            }            this.html.video.available = false;            if (this.require.video) { // If a supplied format is video                this.htmlElement.video = document.createElement('video');                this.htmlElement.video.id = this.internal.video.id;                this.html.video.available = !!this.htmlElement.video.canPlayType && this._testCanPlayType(this.htmlElement.video); // Test is for IE9 on Win Server 2008.            }            this.flash.available = this._checkForFlash(10.1);            this.html.canPlay = {};            this.aurora.canPlay = {};            this.flash.canPlay = {};            $.each(this.formats, function (priority, format) {                self.html.canPlay[format] = self.html[self.format[format].media].available && "" !== self.htmlElement[self.format[format].media].canPlayType(self.format[format].codec);                self.aurora.canPlay[format] = ($.inArray(format, self.aurora.formats) > -1);                self.flash.canPlay[format] = self.format[format].flashCanPlay && self.flash.available;            });            this.html.desired = false;            this.aurora.desired = false;            this.flash.desired = false;            $.each(this.solutions, function (solutionPriority, solution) {                if (solutionPriority === 0) {                    self[solution].desired = true;                } else {                    var audioCanPlay = false;                    var videoCanPlay = false;                    $.each(self.formats, function (formatPriority, format) {                        if (self[self.solutions[0]].canPlay[format]) { // The other solution can play                            if (self.format[format].media === 'video') {                                videoCanPlay = true;                            } else {                                audioCanPlay = true;                            }                        }                    });                    self[solution].desired = (self.require.audio && !audioCanPlay) || (self.require.video && !videoCanPlay);                }            });            // This is what jPlayer will support, based on solution and supplied.            this.html.support = {};            this.aurora.support = {};            this.flash.support = {};            $.each(this.formats, function (priority, format) {                self.html.support[format] = self.html.canPlay[format] && self.html.desired;                self.aurora.support[format] = self.aurora.canPlay[format] && self.aurora.desired;                self.flash.support[format] = self.flash.canPlay[format] && self.flash.desired;            });            // If jPlayer is supporting any format in a solution, then the solution is used.            this.html.used = false;            this.aurora.used = false;            this.flash.used = false;            $.each(this.solutions, function (solutionPriority, solution) {                $.each(self.formats, function (formatPriority, format) {                    if (self[solution].support[format]) {                        self[solution].used = true;                        return false;                    }                });            });            // Init solution active state and the event gates to false.            this._resetActive();            this._resetGate();            // Set up the css selectors for the control and feedback entities.            this._cssSelectorAncestor(this.options.cssSelectorAncestor);            // If neither html nor aurora nor flash are being used by this browser, then media playback is not possible. Trigger an error event.            if (!(this.html.used || this.aurora.used || this.flash.used)) {                this._error({                    type: $.jPlayer.error.NO_SOLUTION,                    context: "{solution:'" + this.options.solution + "', supplied:'" + this.options.supplied + "'}",                    message: $.jPlayer.errorMsg.NO_SOLUTION,                    hint: $.jPlayer.errorHint.NO_SOLUTION                });                if (this.css.jq.noSolution.length) {                    this.css.jq.noSolution.show();                }            } else {                if (this.css.jq.noSolution.length) {                    this.css.jq.noSolution.hide();                }            }            // Add the flash solution if it is being used.            if (this.flash.used) {                var htmlObj,                    flashVars = 'jQuery=' + encodeURI(this.options.noConflict) + '&id=' + encodeURI(this.internal.self.id) + '&vol=' + this.options.volume + '&muted=' + this.options.muted;                // Code influenced by SWFObject 2.2: http://code.google.com/p/swfobject/                // Non IE browsers have an initial Flash size of 1 by 1 otherwise the wmode affected the Flash ready event.                if ($.jPlayer.browser.msie && (Number($.jPlayer.browser.version) < 9 || $.jPlayer.browser.documentMode < 9)) {                    var objStr = '<object id="' + this.internal.flash.id + '" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="0" height="0" tabindex="-1"></object>';                    var paramStr = [                        '<param name="movie" value="' + this.internal.flash.swf + '" />',                        '<param name="FlashVars" value="' + flashVars + '" />',                        '<param name="allowScriptAccess" value="always" />',                        '<param name="bgcolor" value="' + this.options.backgroundColor + '" />',                        '<param name="wmode" value="' + this.options.wmode + '" />'                    ];                    htmlObj = document.createElement(objStr);                    for (var i = 0; i < paramStr.length; i++) {                        htmlObj.appendChild(document.createElement(paramStr[i]));                    }                } else {                    var createParam = function (el, n, v) {                        var p = document.createElement("param");                        p.setAttribute("name", n);                        p.setAttribute("value", v);                        el.appendChild(p);                    };                    htmlObj = document.createElement("object");                    htmlObj.setAttribute("id", this.internal.flash.id);                    htmlObj.setAttribute("name", this.internal.flash.id);                    htmlObj.setAttribute("data", this.internal.flash.swf);                    htmlObj.setAttribute("type", "application/x-shockwave-flash");                    htmlObj.setAttribute("width", "1"); // Non-zero                    htmlObj.setAttribute("height", "1"); // Non-zero                    htmlObj.setAttribute("tabindex", "-1");                    createParam(htmlObj, "flashvars", flashVars);                    createParam(htmlObj, "allowscriptaccess", "always");                    createParam(htmlObj, "bgcolor", this.options.backgroundColor);                    createParam(htmlObj, "wmode", this.options.wmode);                }                this.element.append(htmlObj);                this.internal.flash.jq = $(htmlObj);            }            // Setup playbackRate ability before using _addHtmlEventListeners()            if (this.html.used && !this.flash.used) { // If only HTML                // Using the audio element capabilities for playbackRate. ie., Assuming video element is the same.                this.status.playbackRateEnabled = this._testPlaybackRate('audio');            } else {                this.status.playbackRateEnabled = false;            }            this._updatePlaybackRate();            // Add the HTML solution if being used.            if (this.html.used) {                // The HTML Audio handlers                if (this.html.audio.available) {                    this._addHtmlEventListeners(this.htmlElement.audio, this.html.audio);                    this.element.append(this.htmlElement.audio);                    this.internal.audio.jq = $("#" + this.internal.audio.id);                }                // The HTML Video handlers                if (this.html.video.available) {                    this._addHtmlEventListeners(this.htmlElement.video, this.html.video);                    this.element.append(this.htmlElement.video);                    this.internal.video.jq = $("#" + this.internal.video.id);                    if (this.status.nativeVideoControls) {                        this.internal.video.jq.css({'width': this.status.width, 'height': this.status.height});                    } else {                        this.internal.video.jq.css({'width': '0px', 'height': '0px'}); // Using size 0x0 since a .hide() causes issues in iOS                    }                    this.internal.video.jq.bind("click.jPlayer", function () {                        self._trigger($.jPlayer.event.click);                    });                }            }            // Add the Aurora.js solution if being used.            if (this.aurora.used) {                // Aurora.js player need to be created for each media, see setMedia function.            }            // Create the bridge that emulates the HTML Media element on the jPlayer DIV            if (this.options.emulateHtml) {                this._emulateHtmlBridge();            }            if ((this.html.used || this.aurora.used) && !this.flash.used) { // If only HTML, then emulate flash ready() call after 100ms.                setTimeout(function () {                    self.internal.ready = true;                    self.version.flash = "n/a";                    self._trigger($.jPlayer.event.repeat); // Trigger the repeat event so its handler can initialize itself with the loop option.                    self._trigger($.jPlayer.event.ready);                }, 100);            }            // Initialize the interface components with the options.            this._updateNativeVideoControls();            // The other controls are now setup in _cssSelectorAncestor()            if (this.css.jq.videoPlay.length) {                this.css.jq.videoPlay.hide();            }            $.jPlayer.prototype.count++; // Change static variable via prototype.        },        destroy: function () {            // MJP: The background change remains. Would need to store the original to restore it correctly.            // MJP: The jPlayer element's size change remains.            // Clear the media to reset the GUI and stop any downloads. Streams on some browsers had persited. (Chrome)            this.clearMedia();            // Remove the size/sizeFull cssClass from the cssSelectorAncestor            this._removeUiClass();            // Remove the times from the GUI            if (this.css.jq.currentTime.length) {                this.css.jq.currentTime.text("");            }            if (this.css.jq.duration.length) {                this.css.jq.duration.text("");            }            // Remove any bindings from the interface controls.            $.each(this.css.jq, function (fn, jq) {                // Check selector is valid before trying to execute method.                if (jq.length) {                    jq.unbind(".jPlayer");                }            });            // Remove the click handlers for $.jPlayer.event.click            this.internal.poster.jq.unbind(".jPlayer");            if (this.internal.video.jq) {                this.internal.video.jq.unbind(".jPlayer");            }            // Remove the fullscreen event handlers            this._fullscreenRemoveEventListeners();            // Remove key bindings            if (this === $.jPlayer.focus) {                $.jPlayer.focus = null;            }            // Destroy the HTML bridge.            if (this.options.emulateHtml) {                this._destroyHtmlBridge();            }            this.element.removeData("jPlayer"); // Remove jPlayer data            this.element.unbind(".jPlayer"); // Remove all event handlers created by the jPlayer constructor            this.element.empty(); // Remove the inserted child elements            delete this.instances[this.internal.instance]; // Clear the instance on the static instance object        },        destroyRemoved: function () { // Destroy any instances that have gone away.            var self = this;            $.each(this.instances, function (i, element) {                if (self.element !== element) { // Do not destroy this instance.                    if (!element.data("jPlayer")) { // Check that element is a real jPlayer.                        element.jPlayer("destroy");                        delete self.instances[i];                    }                }            });        },        enable: function () { // Plan to implement            // options.disabled = false        },        disable: function () { // Plan to implement            // options.disabled = true        },        _testCanPlayType: function (elem) {            // IE9 on Win Server 2008 did not implement canPlayType(), but it has the property.            try {                elem.canPlayType(this.format.mp3.codec); // The type is irrelevant.                return true;            } catch (err) {                return false;            }        },        _testPlaybackRate: function (type) {            // type: String 'audio' or 'video'            var el, rate = 0.5;            type = typeof type === 'string' ? type : 'audio';            el = document.createElement(type);            // Wrapping in a try/catch, just in case older HTML5 browsers throw and error.            try {                if ('playbackRate' in el) {                    el.playbackRate = rate;                    return el.playbackRate === rate;                } else {                    return false;                }            } catch (err) {                return false;            }        },        _uaBlocklist: function (list) {            // list : object with properties that are all regular expressions. Property names are irrelevant.            // Returns true if the user agent is matched in list.            var ua = navigator.userAgent.toLowerCase(),                block = false;            $.each(list, function (p, re) {                if (re && re.test(ua)) {                    block = true;                    return false; // exit $.each.                }            });            return block;        },        _restrictNativeVideoControls: function () {            // Fallback to noFullWindow when nativeVideoControls is true and audio media is being used. Affects when both media types are used.            if (this.require.audio) {                if (this.status.nativeVideoControls) {                    this.status.nativeVideoControls = false;                    this.status.noFullWindow = true;                }            }        },        _updateNativeVideoControls: function () {            if (this.html.video.available && this.html.used) {                // Turn the HTML Video controls on/off                this.htmlElement.video.controls = this.status.nativeVideoControls;                // Show/hide the jPlayer GUI.                this._updateAutohide();                // For when option changed. The poster image is not updated, as it is dealt with in setMedia(). Acceptable degradation since seriously doubt these options will change on the fly. Can again review later.                if (this.status.nativeVideoControls && this.require.video) {                    this.internal.poster.jq.hide();                    this.internal.video.jq.css({'width': this.status.width, 'height': this.status.height});                } else if (this.status.waitForPlay && this.status.video) {                    this.internal.poster.jq.show();                    this.internal.video.jq.css({'width': '0px', 'height': '0px'});                }            }        },        _addHtmlEventListeners: function (mediaElement, entity) {            var self = this;            mediaElement.preload = this.options.preload;            mediaElement.muted = this.options.muted;            mediaElement.volume = this.options.volume;            if (this.status.playbackRateEnabled) {                mediaElement.defaultPlaybackRate = this.options.defaultPlaybackRate;                mediaElement.playbackRate = this.options.playbackRate;            }            // Create the event listeners            // Only want the active entity to affect jPlayer and bubble events.            // Using entity.gate so that object is referenced and gate property always current            mediaElement.addEventListener("progress", function () {                if (entity.gate) {                    if (self.internal.cmdsIgnored && this.readyState > 0) { // Detect iOS executed the command                        self.internal.cmdsIgnored = false;                    }                    self._getHtmlStatus(mediaElement);                    self._updateInterface();                    self._trigger($.jPlayer.event.progress);                }            }, false);            mediaElement.addEventListener("loadeddata", function () {                if (entity.gate) {                    self.androidFix.setMedia = false; // Disable the fix after the first progress event.                    if (self.androidFix.play) { // Play Android audio - performing the fix.                        self.androidFix.play = false;                        self.play(self.androidFix.time);                    }                    if (self.androidFix.pause) { // Pause Android audio at time - performing the fix.                        self.androidFix.pause = false;                        self.pause(self.androidFix.time);                    }                    self._trigger($.jPlayer.event.loadeddata);                }            }, false);            mediaElement.addEventListener("timeupdate", function () {                if (entity.gate) {                    self._getHtmlStatus(mediaElement);                    self._updateInterface();                    self._trigger($.jPlayer.event.timeupdate);                }            }, false);            mediaElement.addEventListener("durationchange", function () {                if (entity.gate) {                    self._getHtmlStatus(mediaElement);                    self._updateInterface();                    self._trigger($.jPlayer.event.durationchange);                }            }, false);            mediaElement.addEventListener("play", function () {                if (entity.gate) {                    self._updateButtons(true);                    self._html_checkWaitForPlay(); // So the native controls update this variable and puts the hidden interface in the correct state. Affects toggling native controls.                    self._trigger($.jPlayer.event.play);                }            }, false);            mediaElement.addEventListener("playing", function () {                if (entity.gate) {                    self._updateButtons(true);                    self._seeked();                    self._trigger($.jPlayer.event.playing);                }            }, false);            mediaElement.addEventListener("pause", function () {                if (entity.gate) {                    self._updateButtons(false);                    self._trigger($.jPlayer.event.pause);                }            }, false);            mediaElement.addEventListener("waiting", function () {                if (entity.gate) {                    self._seeking();                    self._trigger($.jPlayer.event.waiting);                }            }, false);            mediaElement.addEventListener("seeking", function () {                if (entity.gate) {                    self._seeking();                    self._trigger($.jPlayer.event.seeking);                }            }, false);            mediaElement.addEventListener("seeked", function () {                if (entity.gate) {                    self._seeked();                    self._trigger($.jPlayer.event.seeked);                }            }, false);            mediaElement.addEventListener("volumechange", function () {                if (entity.gate) {                    // Read the values back from the element as the Blackberry PlayBook shares the volume with the physical buttons master volume control.                    // However, when tested 6th July 2011, those buttons do not generate an event. The physical play/pause button does though.                    self.options.volume = mediaElement.volume;                    self.options.muted = mediaElement.muted;                    self._updateMute();                    self._updateVolume();                    self._trigger($.jPlayer.event.volumechange);                }            }, false);            mediaElement.addEventListener("ratechange", function () {                if (entity.gate) {                    self.options.defaultPlaybackRate = mediaElement.defaultPlaybackRate;                    self.options.playbackRate = mediaElement.playbackRate;                    self._updatePlaybackRate();                    self._trigger($.jPlayer.event.ratechange);                }            }, false);            mediaElement.addEventListener("suspend", function () { // Seems to be the only way of capturing that the iOS4 browser did not actually play the media from the page code. ie., It needs a user gesture.                if (entity.gate) {                    self._seeked();                    self._trigger($.jPlayer.event.suspend);                }            }, false);            mediaElement.addEventListener("ended", function () {                if (entity.gate) {                    // Order of the next few commands are important. Change the time and then pause.                    // Solves a bug in Firefox, where issuing pause 1st causes the media to play from the start. ie., The pause is ignored.                    if (!$.jPlayer.browser.webkit) { // Chrome crashes if you do this in conjunction with a setMedia command in an ended event handler. ie., The playlist demo.                        self.htmlElement.media.currentTime = 0; // Safari does not care about this command. ie., It works with or without this line. (Both Safari and Chrome are Webkit.)                    }                    self.htmlElement.media.pause(); // Pause otherwise a click on the progress bar will play from that point, when it shouldn't, since it stopped playback.                    self._updateButtons(false);                    self._getHtmlStatus(mediaElement, true); // With override true. Otherwise Chrome leaves progress at full.                    self._updateInterface();                    self._trigger($.jPlayer.event.ended);                }            }, false);            mediaElement.addEventListener("error", function () {                if (entity.gate) {                    self._updateButtons(false);                    self._seeked();                    if (self.status.srcSet) { // Deals with case of clearMedia() causing an error event.                        clearTimeout(self.internal.htmlDlyCmdId); // Clears any delayed commands used in the HTML solution.                        self.status.waitForLoad = true; // Allows the load operation to try again.                        self.status.waitForPlay = true; // Reset since a play was captured.                        if (self.status.video && !self.status.nativeVideoControls) {                            self.internal.video.jq.css({'width': '0px', 'height': '0px'});                        }                        if (self._validString(self.status.media.poster) && !self.status.nativeVideoControls) {                            self.internal.poster.jq.show();                        }                        if (self.css.jq.videoPlay.length) {                            self.css.jq.videoPlay.show();                        }                        self._error({                            type: $.jPlayer.error.URL,                            context: self.status.src, // this.src shows absolute urls. Want context to show the url given.                            message: $.jPlayer.errorMsg.URL,                            hint: $.jPlayer.errorHint.URL                        });                    }                }            }, false);            // Create all the other event listeners that bubble up to a jPlayer event from html, without being used by jPlayer.            $.each($.jPlayer.htmlEvent, function (i, eventType) {                mediaElement.addEventListener(this, function () {                    if (entity.gate) {                        self._trigger($.jPlayer.event[eventType]);                    }                }, false);            });        },        _addAuroraEventListeners: function (player, entity) {            var self = this;            //player.preload = this.options.preload;            //player.muted = this.options.muted;            player.volume = this.options.volume * 100;            // Create the event listeners            // Only want the active entity to affect jPlayer and bubble events.            // Using entity.gate so that object is referenced and gate property always current            player.on("progress", function () {                if (entity.gate) {                    if (self.internal.cmdsIgnored && this.readyState > 0) { // Detect iOS executed the command                        self.internal.cmdsIgnored = false;                    }                    self._getAuroraStatus(player);                    self._updateInterface();                    self._trigger($.jPlayer.event.progress);                    // Progress with song duration, we estimate timeupdate need to be triggered too.                    if (player.duration > 0) {                        self._trigger($.jPlayer.event.timeupdate);                    }                }            }, false);            player.on("ready", function () {                if (entity.gate) {                    self._trigger($.jPlayer.event.loadeddata);                }            }, false);            player.on("duration", function () {                if (entity.gate) {                    self._getAuroraStatus(player);                    self._updateInterface();                    self._trigger($.jPlayer.event.durationchange);                }            }, false);            player.on("end", function () {                if (entity.gate) {                    // Order of the next few commands are important. Change the time and then pause.                    self._updateButtons(false);                    self._getAuroraStatus(player, true);                    self._updateInterface();                    self._trigger($.jPlayer.event.ended);                }            }, false);            player.on("error", function () {                if (entity.gate) {                    self._updateButtons(false);                    self._seeked();                    if (self.status.srcSet) { // Deals with case of clearMedia() causing an error event.                        self.status.waitForLoad = true; // Allows the load operation to try again.                        self.status.waitForPlay = true; // Reset since a play was captured.                        if (self.status.video && !self.status.nativeVideoControls) {                            self.internal.video.jq.css({'width': '0px', 'height': '0px'});                        }                        if (self._validString(self.status.media.poster) && !self.status.nativeVideoControls) {                            self.internal.poster.jq.show();                        }                        if (self.css.jq.videoPlay.length) {                            self.css.jq.videoPlay.show();                        }                        self._error({                            type: $.jPlayer.error.URL,                            context: self.status.src, // this.src shows absolute urls. Want context to show the url given.                            message: $.jPlayer.errorMsg.URL,                            hint: $.jPlayer.errorHint.URL                        });                    }                }            }, false);        },        _getHtmlStatus: function (media, override) {            var ct = 0, cpa = 0, sp = 0, cpr = 0;            // Fixes the duration bug in iOS, where the durationchange event occurs when media.duration is not always correct.            // Fixes the initial duration bug in BB OS7, where the media.duration is infinity and displays as NaN:NaN due to Date() using inifity.            if (isFinite(media.duration)) {                this.status.duration = media.duration;            }            ct = media.currentTime;            cpa = (this.status.duration > 0) ? 100 * ct / this.status.duration : 0;            if ((typeof media.seekable === "object") && (media.seekable.length > 0)) {                sp = (this.status.duration > 0) ? 100 * media.seekable.end(media.seekable.length - 1) / this.status.duration : 100;                cpr = (this.status.duration > 0) ? 100 * media.currentTime / media.seekable.end(media.seekable.length - 1) : 0; // Duration conditional for iOS duration bug. ie., seekable.end is a NaN in that case.            } else {                sp = 100;                cpr = cpa;            }            if (override) {                ct = 0;                cpr = 0;                cpa = 0;            }            this.status.seekPercent = sp;            this.status.currentPercentRelative = cpr;            this.status.currentPercentAbsolute = cpa;            this.status.currentTime = ct;            this.status.remaining = this.status.duration - this.status.currentTime;            this.status.videoWidth = media.videoWidth;            this.status.videoHeight = media.videoHeight;            this.status.readyState = media.readyState;            this.status.networkState = media.networkState;            this.status.playbackRate = media.playbackRate;            this.status.ended = media.ended;        },        _getAuroraStatus: function (player, override) {            var ct = 0, cpa = 0, sp = 0, cpr = 0;            this.status.duration = player.duration / 1000;            ct = player.currentTime / 1000;            cpa = (this.status.duration > 0) ? 100 * ct / this.status.duration : 0;            if (player.buffered > 0) {                sp = (this.status.duration > 0) ? (player.buffered * this.status.duration) / this.status.duration : 100;                cpr = (this.status.duration > 0) ? ct / (player.buffered * this.status.duration) : 0;            } else {                sp = 100;                cpr = cpa;            }            if (override) {                ct = 0;                cpr = 0;                cpa = 0;            }            this.status.seekPercent = sp;            this.status.currentPercentRelative = cpr;            this.status.currentPercentAbsolute = cpa;            this.status.currentTime = ct;            this.status.remaining = this.status.duration - this.status.currentTime;            this.status.readyState = 4; // status.readyState;            this.status.networkState = 0; // status.networkState;            this.status.playbackRate = 1; // status.playbackRate;            this.status.ended = false; // status.ended;        },        _resetStatus: function () {            this.status = $.extend({}, this.status, $.jPlayer.prototype.status); // Maintains the status properties that persist through a reset.        },        _trigger: function (eventType, error, warning) { // eventType always valid as called using $.jPlayer.event.eventType            var event = $.Event(eventType);            event.jPlayer = {};            event.jPlayer.version = $.extend({}, this.version);            event.jPlayer.options = $.extend(true, {}, this.options); // Deep copy            event.jPlayer.status = $.extend(true, {}, this.status); // Deep copy            event.jPlayer.html = $.extend(true, {}, this.html); // Deep copy            event.jPlayer.aurora = $.extend(true, {}, this.aurora); // Deep copy            event.jPlayer.flash = $.extend(true, {}, this.flash); // Deep copy            if (error) {                event.jPlayer.error = $.extend({}, error);            }            if (warning) {                event.jPlayer.warning = $.extend({}, warning);            }            this.element.trigger(event);        },        jPlayerFlashEvent: function (eventType, status) { // Called from Flash            if (eventType === $.jPlayer.event.ready) {                if (!this.internal.ready) {                    this.internal.ready = true;                    this.internal.flash.jq.css({'width': '0px', 'height': '0px'}); // Once Flash generates the ready event, minimise to zero as it is not affected by wmode anymore.                    this.version.flash = status.version;                    if (this.version.needFlash !== this.version.flash) {                        this._error({                            type: $.jPlayer.error.VERSION,                            context: this.version.flash,                            message: $.jPlayer.errorMsg.VERSION + this.version.flash,                            hint: $.jPlayer.errorHint.VERSION                        });                    }                    this._trigger($.jPlayer.event.repeat); // Trigger the repeat event so its handler can initialize itself with the loop option.                    this._trigger(eventType);                } else {                    // This condition occurs if the Flash is hidden and then shown again.                    // Firefox also reloads the Flash if the CSS position changes. position:fixed is used for full screen.                    // Only do this if the Flash is the solution being used at the moment. Affects Media players where both solution may be being used.                    if (this.flash.gate) {                        // Send the current status to the Flash now that it is ready (available) again.                        if (this.status.srcSet) {                            // Need to read original status before issuing the setMedia command.                            var currentTime = this.status.currentTime,                                paused = this.status.paused;                            this.setMedia(this.status.media);                            this.volumeWorker(this.options.volume);                            if (currentTime > 0) {                                if (paused) {                                    this.pause(currentTime);                                } else {                                    this.play(currentTime);                                }                            }                        }                        this._trigger($.jPlayer.event.flashreset);                    }                }            }            if (this.flash.gate) {                switch (eventType) {                    case $.jPlayer.event.progress:                        this._getFlashStatus(status);                        this._updateInterface();                        this._trigger(eventType);                        break;                    case $.jPlayer.event.timeupdate:                        this._getFlashStatus(status);                        this._updateInterface();                        this._trigger(eventType);                        break;                    case $.jPlayer.event.play:                        this._seeked();                        this._updateButtons(true);                        this._trigger(eventType);                        break;                    case $.jPlayer.event.pause:                        this._updateButtons(false);                        this._trigger(eventType);                        break;                    case $.jPlayer.event.ended:                        this._updateButtons(false);                        this._trigger(eventType);                        break;                    case $.jPlayer.event.click:                        this._trigger(eventType); // This could be dealt with by the default                        break;                    case $.jPlayer.event.error:                        this.status.waitForLoad = true; // Allows the load operation to try again.                        this.status.waitForPlay = true; // Reset since a play was captured.                        if (this.status.video) {                            this.internal.flash.jq.css({'width': '0px', 'height': '0px'});                        }                        if (this._validString(this.status.media.poster)) {                            this.internal.poster.jq.show();                        }                        if (this.css.jq.videoPlay.length && this.status.video) {                            this.css.jq.videoPlay.show();                        }                        if (this.status.video) { // Set up for another try. Execute before error event.                            this._flash_setVideo(this.status.media);                        } else {                            this._flash_setAudio(this.status.media);                        }                        this._updateButtons(false);                        this._error({                            type: $.jPlayer.error.URL,                            context: status.src,                            message: $.jPlayer.errorMsg.URL,                            hint: $.jPlayer.errorHint.URL                        });                        break;                    case $.jPlayer.event.seeking:                        this._seeking();                        this._trigger(eventType);                        break;                    case $.jPlayer.event.seeked:                        this._seeked();                        this._trigger(eventType);                        break;                    case $.jPlayer.event.ready:                        // The ready event is handled outside the switch statement.                        // Captured here otherwise 2 ready events would be generated if the ready event handler used setMedia.                        break;                    default:                        this._trigger(eventType);                }            }            return false;        },        _getFlashStatus: function (status) {            this.status.seekPercent = status.seekPercent;            this.status.currentPercentRelative = status.currentPercentRelative;            this.status.currentPercentAbsolute = status.currentPercentAbsolute;            this.status.currentTime = status.currentTime;            this.status.duration = status.duration;            this.status.remaining = status.duration - status.currentTime;            this.status.videoWidth = status.videoWidth;            this.status.videoHeight = status.videoHeight;            // The Flash does not generate this information in this release            this.status.readyState = 4; // status.readyState;            this.status.networkState = 0; // status.networkState;            this.status.playbackRate = 1; // status.playbackRate;            this.status.ended = false; // status.ended;        },        _updateButtons: function (playing) {            if (playing === undefined) {                playing = !this.status.paused;            } else {                this.status.paused = !playing;            }            // Apply the state classes. (For the useStateClassSkin:true option)            if (playing) {                this.addStateClass('playing');            } else {                this.removeStateClass('playing');            }            if (!this.status.noFullWindow && this.options.fullWindow) {                this.addStateClass('fullScreen');            } else {                this.removeStateClass('fullScreen');            }            if (this.options.loop) {                this.addStateClass('looped');            } else {                this.removeStateClass('looped');            }            // Toggle the GUI element pairs. (For the useStateClassSkin:false option)            if (this.css.jq.play.length && this.css.jq.pause.length) {                if (playing) {                    this.css.jq.play.hide();                    this.css.jq.pause.show();                } else {                    this.css.jq.play.show();                    this.css.jq.pause.hide();                }            }            if (this.css.jq.restoreScreen.length && this.css.jq.fullScreen.length) {                if (this.status.noFullWindow) {                    this.css.jq.fullScreen.hide();                    this.css.jq.restoreScreen.hide();                } else if (this.options.fullWindow) {                    this.css.jq.fullScreen.hide();                    this.css.jq.restoreScreen.show();                } else {                    this.css.jq.fullScreen.show();                    this.css.jq.restoreScreen.hide();                }            }            if (this.css.jq.repeat.length && this.css.jq.repeatOff.length) {                if (this.options.loop) {                    this.css.jq.repeat.hide();                    this.css.jq.repeatOff.show();                } else {                    this.css.jq.repeat.show();                    this.css.jq.repeatOff.hide();                }            }        },        _updateInterface: function () {            if (this.css.jq.seekBar.length) {                this.css.jq.seekBar.width(this.status.seekPercent + "%");            }            if (this.css.jq.playBar.length) {                if (this.options.smoothPlayBar) {                    this.css.jq.playBar.stop().animate({                        width: this.status.currentPercentAbsolute + "%"                    }, 250, "linear");                } else {                    this.css.jq.playBar.width(this.status.currentPercentRelative + "%");                }            }            var currentTimeText = '';            if (this.css.jq.currentTime.length) {                currentTimeText = this._convertTime(this.status.currentTime);                if (currentTimeText !== this.css.jq.currentTime.text()) {                    this.css.jq.currentTime.text(this._convertTime(this.status.currentTime));                }            }            var durationText = '',                duration = this.status.duration,                remaining = this.status.remaining;            if (this.css.jq.duration.length) {                if (typeof this.status.media.duration === 'string') {                    durationText = this.status.media.duration;                } else {                    if (typeof this.status.media.duration === 'number') {                        duration = this.status.media.duration;                        remaining = duration - this.status.currentTime;                    }                    if (this.options.remainingDuration) {                        durationText = (remaining > 0 ? '-' : '') + this._convertTime(remaining);                    } else {                        durationText = this._convertTime(duration);                    }                }                if (durationText !== this.css.jq.duration.text()) {                    this.css.jq.duration.text(durationText);                }            }        },        _convertTime: ConvertTime.prototype.time,        _seeking: function () {            if (this.css.jq.seekBar.length) {                this.css.jq.seekBar.addClass("jp-seeking-bg");            }            this.addStateClass('seeking');        },        _seeked: function () {            if (this.css.jq.seekBar.length) {                this.css.jq.seekBar.removeClass("jp-seeking-bg");            }            this.removeStateClass('seeking');        },        _resetGate: function () {            this.html.audio.gate = false;            this.html.video.gate = false;            this.aurora.gate = false;            this.flash.gate = false;        },        _resetActive: function () {            this.html.active = false;            this.aurora.active = false;            this.flash.active = false;        },        _escapeHtml: function (s) {            return s.split('&').join('&amp;').split('<').join('&lt;').split('>').join('&gt;').split('"').join('&quot;');        },        _qualifyURL: function (url) {            var el = document.createElement('div');            el.innerHTML = '<a href="' + this._escapeHtml(url) + '">x</a>';            return el.firstChild.href;        },        _absoluteMediaUrls: function (media) {            var self = this;            $.each(media, function (type, url) {                if (url && self.format[type] && url.substr(0, 5) !== "data:") {                    media[type] = self._qualifyURL(url);                }            });            return media;        },        addStateClass: function (state) {            if (this.ancestorJq.length) {                this.ancestorJq.addClass(this.options.stateClass[state]);            }        },        removeStateClass: function (state) {            if (this.ancestorJq.length) {                this.ancestorJq.removeClass(this.options.stateClass[state]);            }        },        setMedia: function (media) {            /*	media[format] = String: URL of format. Must contain all of the supplied option's video or audio formats.			 *	media.poster = String: Video poster URL.			 *	media.track = Array: Of objects defining the track element: kind, src, srclang, label, def.			 *	media.stream = Boolean: * NOT IMPLEMENTED * Designating actual media streams. ie., "false/undefined" for files. Plan to refresh the flash every so often.			 */            var self = this,                supported = false,                posterChanged = this.status.media.poster !== media.poster; // Compare before reset. Important for OSX Safari as this.htmlElement.poster.src is absolute, even if original poster URL was relative.            this._resetMedia();            this._resetGate();            this._resetActive();            // Clear the Android Fix.            this.androidFix.setMedia = false;            this.androidFix.play = false;            this.androidFix.pause = false;            // Convert all media URLs to absolute URLs.            media = this._absoluteMediaUrls(media);            $.each(this.formats, function (formatPriority, format) {                var isVideo = self.format[format].media === 'video';                $.each(self.solutions, function (solutionPriority, solution) {                    if (self[solution].support[format] && self._validString(media[format])) { // Format supported in solution and url given for format.                        var isHtml = solution === 'html';                        var isAurora = solution === 'aurora';                        if (isVideo) {                            if (isHtml) {                                self.html.video.gate = true;                                self._html_setVideo(media);                                self.html.active = true;                            } else {                                self.flash.gate = true;                                self._flash_setVideo(media);                                self.flash.active = true;                            }                            if (self.css.jq.videoPlay.length) {                                self.css.jq.videoPlay.show();                            }                            self.status.video = true;                        } else {                            if (isHtml) {                                self.html.audio.gate = true;                                self._html_setAudio(media);                                self.html.active = true;                                // Setup the Android Fix - Only for HTML audio.                                if ($.jPlayer.platform.android) {                                    self.androidFix.setMedia = true;                                }                            } else if (isAurora) {                                self.aurora.gate = true;                                self._aurora_setAudio(media);                                self.aurora.active = true;                            } else {                                self.flash.gate = true;                                self._flash_setAudio(media);                                self.flash.active = true;                            }                            if (self.css.jq.videoPlay.length) {                                self.css.jq.videoPlay.hide();                            }                            self.status.video = false;                        }                        supported = true;                        return false; // Exit $.each                    }                });                if (supported) {                    return false; // Exit $.each                }            });            if (supported) {                if (!(this.status.nativeVideoControls && this.html.video.gate)) {                    // Set poster IMG if native video controls are not being used                    // Note: With IE the IMG onload event occurs immediately when cached.                    // Note: Poster hidden by default in _resetMedia()                    if (this._validString(media.poster)) {                        if (posterChanged) { // Since some browsers do not generate img onload event.                            this.htmlElement.poster.src = media.poster;                        } else {                            this.internal.poster.jq.show();                        }                    }                }                if (typeof media.title === 'string') {                    if (this.css.jq.title.length) {                        this.css.jq.title.html(media.title);                    }                    if (this.htmlElement.audio) {                        this.htmlElement.audio.setAttribute('title', media.title);                    }                    if (this.htmlElement.video) {                        this.htmlElement.video.setAttribute('title', media.title);                    }                }                this.status.srcSet = true;                this.status.media = $.extend({}, media);                this._updateButtons(false);                this._updateInterface();                this._trigger($.jPlayer.event.setmedia);            } else { // jPlayer cannot support any formats provided in this browser                // Send an error event                this._error({                    type: $.jPlayer.error.NO_SUPPORT,                    context: "{supplied:'" + this.options.supplied + "'}",                    message: $.jPlayer.errorMsg.NO_SUPPORT,                    hint: $.jPlayer.errorHint.NO_SUPPORT                });            }        },        _resetMedia: function () {            this._resetStatus();            this._updateButtons(false);            this._updateInterface();            this._seeked();            this.internal.poster.jq.hide();            clearTimeout(this.internal.htmlDlyCmdId);            if (this.html.active) {                this._html_resetMedia();            } else if (this.aurora.active) {                this._aurora_resetMedia();            } else if (this.flash.active) {                this._flash_resetMedia();            }        },        clearMedia: function () {            this._resetMedia();            if (this.html.active) {                this._html_clearMedia();            } else if (this.aurora.active) {                this._aurora_clearMedia();            } else if (this.flash.active) {                this._flash_clearMedia();            }            this._resetGate();            this._resetActive();        },        load: function () {            if (this.status.srcSet) {                if (this.html.active) {                    this._html_load();                } else if (this.aurora.active) {                    this._aurora_load();                } else if (this.flash.active) {                    this._flash_load();                }            } else {                this._urlNotSetError("load");            }        },        focus: function () {            if (this.options.keyEnabled) {                $.jPlayer.focus = this;            }        },        play: function (time) {            var guiAction = typeof time === "object"; // Flags GUI click events so we know this was not a direct command, but an action taken by the user on the GUI.            if (guiAction && this.options.useStateClassSkin && !this.status.paused) {                this.pause(time); // The time would be the click event, but passing it over so info is not lost.            } else {                time = (typeof time === "number") ? time : NaN; // Remove jQuery event from click handler                if (this.status.srcSet) {                    this.focus();                    if (this.html.active) {                        this._html_play(time);                    } else if (this.aurora.active) {                        this._aurora_play(time);                    } else if (this.flash.active) {                        this._flash_play(time);                    }                } else {                    this._urlNotSetError("play");                }            }        },        videoPlay: function () { // Handles clicks on the play button over the video poster            this.play();        },        pause: function (time) {            time = (typeof time === "number") ? time : NaN; // Remove jQuery event from click handler            if (this.status.srcSet) {                if (this.html.active) {                    this._html_pause(time);                } else if (this.aurora.active) {                    this._aurora_pause(time);                } else if (this.flash.active) {                    this._flash_pause(time);                }            } else {                this._urlNotSetError("pause");            }        },        tellOthers: function (command, conditions) {            var self = this,                hasConditions = typeof conditions === 'function',                args = Array.prototype.slice.call(arguments); // Convert arguments to an Array.            if (typeof command !== 'string') { // Ignore, since no command.                return; // Return undefined to maintain chaining.            }            if (hasConditions) {                args.splice(1, 1); // Remove the conditions from the arguments            }            $.jPlayer.prototype.destroyRemoved();            $.each(this.instances, function () {                // Remember that "this" is the instance's "element" in the $.each() loop.                if (self.element !== this) { // Do not tell my instance.                    if (!hasConditions || conditions.call(this.data("jPlayer"), self)) {                        this.jPlayer.apply(this, args);                    }                }            });        },        pauseOthers: function (time) {            this.tellOthers("pause", function () {                // In the conditions function, the "this" context is the other instance's jPlayer object.                return this.status.srcSet;            }, time);        },        stop: function () {            if (this.status.srcSet) {                if (this.html.active) {                    this._html_pause(0);                } else if (this.aurora.active) {                    this._aurora_pause(0);                } else if (this.flash.active) {                    this._flash_pause(0);                }            } else {                this._urlNotSetError("stop");            }        },        playHead: function (p) {            p = this._limitValue(p, 0, 100);            if (this.status.srcSet) {                if (this.html.active) {                    this._html_playHead(p);                } else if (this.aurora.active) {                    this._aurora_playHead(p);                } else if (this.flash.active) {                    this._flash_playHead(p);                }            } else {                this._urlNotSetError("playHead");            }        },        _muted: function (muted) {            this.mutedWorker(muted);            if (this.options.globalVolume) {                this.tellOthers("mutedWorker", function () {                    // Check the other instance has global volume enabled.                    return this.options.globalVolume;                }, muted);            }        },        mutedWorker: function (muted) {            this.options.muted = muted;            if (this.html.used) {                this._html_setProperty('muted', muted);            }            if (this.aurora.used) {                this._aurora_mute(muted);            }            if (this.flash.used) {                this._flash_mute(muted);            }            // The HTML solution generates this event from the media element itself.            if (!this.html.video.gate && !this.html.audio.gate) {                this._updateMute(muted);                this._updateVolume(this.options.volume);                this._trigger($.jPlayer.event.volumechange);            }        },        mute: function (mute) { // mute is either: undefined (true), an event object (true) or a boolean (muted).            var guiAction = typeof mute === "object"; // Flags GUI click events so we know this was not a direct command, but an action taken by the user on the GUI.            if (guiAction && this.options.useStateClassSkin && this.options.muted) {                this._muted(false);            } else {                mute = mute === undefined ? true : !!mute;                this._muted(mute);            }        },        unmute: function (unmute) { // unmute is either: undefined (true), an event object (true) or a boolean (!muted).            unmute = unmute === undefined ? true : !!unmute;            this._muted(!unmute);        },        _updateMute: function (mute) {            if (mute === undefined) {                mute = this.options.muted;            }            if (mute) {                this.addStateClass('muted');            } else {                this.removeStateClass('muted');            }            if (this.css.jq.mute.length && this.css.jq.unmute.length) {                if (this.status.noVolume) {                    this.css.jq.mute.hide();                    this.css.jq.unmute.hide();                } else if (mute) {                    this.css.jq.mute.hide();                    this.css.jq.unmute.show();                } else {                    this.css.jq.mute.show();                    this.css.jq.unmute.hide();                }            }        },        volume: function (v) {            this.volumeWorker(v);            if (this.options.globalVolume) {                this.tellOthers("volumeWorker", function () {                    // Check the other instance has global volume enabled.                    return this.options.globalVolume;                }, v);            }        },        volumeWorker: function (v) {            v = this._limitValue(v, 0, 1);            this.options.volume = v;            if (this.html.used) {                this._html_setProperty('volume', v);            }            if (this.aurora.used) {                this._aurora_volume(v);            }            if (this.flash.used) {                this._flash_volume(v);            }            // The HTML solution generates this event from the media element itself.            if (!this.html.video.gate && !this.html.audio.gate) {                this._updateVolume(v);                this._trigger($.jPlayer.event.volumechange);            }        },        volumeBar: function (e) { // Handles clicks on the volumeBar            if (this.css.jq.volumeBar.length) {                // Using $(e.currentTarget) to enable multiple volume bars                var $bar = $(e.currentTarget),                    offset = $bar.offset(),                    x = e.pageX - offset.left,                    w = $bar.width(),                    y = $bar.height() - e.pageY + offset.top,                    h = $bar.height();                if (this.options.verticalVolume) {                    this.volume(y / h);                } else {                    this.volume(x / w);                }            }            if (this.options.muted) {                this._muted(false);            }        },        _updateVolume: function (v) {            if (v === undefined) {                v = this.options.volume;            }            v = this.options.muted ? 0 : v;            if (this.status.noVolume) {                this.addStateClass('noVolume');                if (this.css.jq.volumeBar.length) {                    this.css.jq.volumeBar.hide();                }                if (this.css.jq.volumeBarValue.length) {                    this.css.jq.volumeBarValue.hide();                }                if (this.css.jq.volumeMax.length) {                    this.css.jq.volumeMax.hide();                }            } else {                this.removeStateClass('noVolume');                if (this.css.jq.volumeBar.length) {                    this.css.jq.volumeBar.show();                }                if (this.css.jq.volumeBarValue.length) {                    this.css.jq.volumeBarValue.show();                    this.css.jq.volumeBarValue[this.options.verticalVolume ? "height" : "width"]((v * 100) + "%");                }                if (this.css.jq.volumeMax.length) {                    this.css.jq.volumeMax.show();                }            }        },        volumeMax: function () { // Handles clicks on the volume max            this.volume(1);            if (this.options.muted) {                this._muted(false);            }        },        _cssSelectorAncestor: function (ancestor) {            var self = this;            this.options.cssSelectorAncestor = ancestor;            this._removeUiClass();            this.ancestorJq = ancestor ? $(ancestor) : []; // Would use $() instead of [], but it is only 1.4+            if (ancestor && this.ancestorJq.length !== 1) { // So empty strings do not generate the warning.                this._warning({                    type: $.jPlayer.warning.CSS_SELECTOR_COUNT,                    context: ancestor,                    message: $.jPlayer.warningMsg.CSS_SELECTOR_COUNT + this.ancestorJq.length + " found for cssSelectorAncestor.",                    hint: $.jPlayer.warningHint.CSS_SELECTOR_COUNT                });            }            this._addUiClass();            $.each(this.options.cssSelector, function (fn, cssSel) {                self._cssSelector(fn, cssSel);            });            // Set the GUI to the current state.            this._updateInterface();            this._updateButtons();            this._updateAutohide();            this._updateVolume();            this._updateMute();        },        _cssSelector: function (fn, cssSel) {            var self = this;            if (typeof cssSel === 'string') {                if ($.jPlayer.prototype.options.cssSelector[fn]) {                    if (this.css.jq[fn] && this.css.jq[fn].length) {                        this.css.jq[fn].unbind(".jPlayer");                    }                    this.options.cssSelector[fn] = cssSel;                    this.css.cs[fn] = this.options.cssSelectorAncestor + " " + cssSel;                    if (cssSel) { // Checks for empty string                        this.css.jq[fn] = $(this.css.cs[fn]);                    } else {                        this.css.jq[fn] = []; // To comply with the css.jq[fn].length check before its use. As of jQuery 1.4 could have used $() for an empty set.                    }                    if (this.css.jq[fn].length && this[fn]) {                        var handler = function (e) {                            e.preventDefault();                            self[fn](e);                            if (self.options.autoBlur) {                                $(this).blur();                            } else {                                $(this).focus(); // Force focus for ARIA.                            }                        };                        this.css.jq[fn].bind("click.jPlayer", handler); // Using jPlayer namespace                    }                    if (cssSel && this.css.jq[fn].length !== 1) { // So empty strings do not generate the warning. ie., they just remove the old one.                        this._warning({                            type: $.jPlayer.warning.CSS_SELECTOR_COUNT,                            context: this.css.cs[fn],                            message: $.jPlayer.warningMsg.CSS_SELECTOR_COUNT + this.css.jq[fn].length + " found for " + fn + " method.",                            hint: $.jPlayer.warningHint.CSS_SELECTOR_COUNT                        });                    }                } else {                    this._warning({                        type: $.jPlayer.warning.CSS_SELECTOR_METHOD,                        context: fn,                        message: $.jPlayer.warningMsg.CSS_SELECTOR_METHOD,                        hint: $.jPlayer.warningHint.CSS_SELECTOR_METHOD                    });                }            } else {                this._warning({                    type: $.jPlayer.warning.CSS_SELECTOR_STRING,                    context: cssSel,                    message: $.jPlayer.warningMsg.CSS_SELECTOR_STRING,                    hint: $.jPlayer.warningHint.CSS_SELECTOR_STRING                });            }        },        duration: function (e) {            if (this.options.toggleDuration) {                if (this.options.captureDuration) {                    e.stopPropagation();                }                this._setOption("remainingDuration", !this.options.remainingDuration);            }        },        seekBar: function (e) { // Handles clicks on the seekBar            if (this.css.jq.seekBar.length) {                // Using $(e.currentTarget) to enable multiple seek bars                var $bar = $(e.currentTarget),                    offset = $bar.offset(),                    x = e.pageX - offset.left,                    w = $bar.width(),                    p = 100 * x / w;                this.playHead(p);            }        },        playbackRate: function (pbr) {            this._setOption("playbackRate", pbr);        },        playbackRateBar: function (e) { // Handles clicks on the playbackRateBar            if (this.css.jq.playbackRateBar.length) {                // Using $(e.currentTarget) to enable multiple playbackRate bars                var $bar = $(e.currentTarget),                    offset = $bar.offset(),                    x = e.pageX - offset.left,                    w = $bar.width(),                    y = $bar.height() - e.pageY + offset.top,                    h = $bar.height(),                    ratio, pbr;                if (this.options.verticalPlaybackRate) {                    ratio = y / h;                } else {                    ratio = x / w;                }                pbr = ratio * (this.options.maxPlaybackRate - this.options.minPlaybackRate) + this.options.minPlaybackRate;                this.playbackRate(pbr);            }        },        _updatePlaybackRate: function () {            var pbr = this.options.playbackRate,                ratio = (pbr - this.options.minPlaybackRate) / (this.options.maxPlaybackRate - this.options.minPlaybackRate);            if (this.status.playbackRateEnabled) {                if (this.css.jq.playbackRateBar.length) {                    this.css.jq.playbackRateBar.show();                }                if (this.css.jq.playbackRateBarValue.length) {                    this.css.jq.playbackRateBarValue.show();                    this.css.jq.playbackRateBarValue[this.options.verticalPlaybackRate ? "height" : "width"]((ratio * 100) + "%");                }            } else {                if (this.css.jq.playbackRateBar.length) {                    this.css.jq.playbackRateBar.hide();                }                if (this.css.jq.playbackRateBarValue.length) {                    this.css.jq.playbackRateBarValue.hide();                }            }        },        repeat: function (event) { // Handle clicks on the repeat button            var guiAction = typeof event === "object"; // Flags GUI click events so we know this was not a direct command, but an action taken by the user on the GUI.            if (guiAction && this.options.useStateClassSkin && this.options.loop) {                this._loop(false);            } else {                this._loop(true);            }        },        repeatOff: function () { // Handle clicks on the repeatOff button            this._loop(false);        },        _loop: function (loop) {            if (this.options.loop !== loop) {                this.options.loop = loop;                this._updateButtons();                this._trigger($.jPlayer.event.repeat);            }        },        // Options code adapted from ui.widget.js (1.8.7).  Made changes so the key can use dot notation. To match previous getData solution in jPlayer 1.        option: function (key, value) {            var options = key;            // Enables use: options().  Returns a copy of options object            if (arguments.length === 0) {                return $.extend(true, {}, this.options);            }            if (typeof key === "string") {                var keys = key.split(".");                // Enables use: options("someOption")  Returns a copy of the option. Supports dot notation.                if (value === undefined) {                    var opt = $.extend(true, {}, this.options);                    for (var i = 0; i < keys.length; i++) {                        if (opt[keys[i]] !== undefined) {                            opt = opt[keys[i]];                        } else {                            this._warning({                                type: $.jPlayer.warning.OPTION_KEY,                                context: key,                                message: $.jPlayer.warningMsg.OPTION_KEY,                                hint: $.jPlayer.warningHint.OPTION_KEY                            });                            return undefined;                        }                    }                    return opt;                }                // Enables use: options("someOptionObject", someObject}).  Creates: {someOptionObject:someObject}                // Enables use: options("someOption", someValue).  Creates: {someOption:someValue}                // Enables use: options("someOptionObject.someOption", someValue).  Creates: {someOptionObject:{someOption:someValue}}                options = {};                var opts = options;                for (var j = 0; j < keys.length; j++) {                    if (j < keys.length - 1) {                        opts[keys[j]] = {};                        opts = opts[keys[j]];                    } else {                        opts[keys[j]] = value;                    }                }            }            // Otherwise enables use: options(optionObject).  Uses original object (the key)            this._setOptions(options);            return this;        },        _setOptions: function (options) {            var self = this;            $.each(options, function (key, value) { // This supports the 2 level depth that the options of jPlayer has. Would review if we ever need more depth.                self._setOption(key, value);            });            return this;        },        _setOption: function (key, value) {            var self = this;            // The ability to set options is limited at this time.            switch (key) {                case "volume" :                    this.volume(value);                    break;                case "muted" :                    this._muted(value);                    break;                case "globalVolume" :                    this.options[key] = value;                    break;                case "cssSelectorAncestor" :                    this._cssSelectorAncestor(value); // Set and refresh all associations for the new ancestor.                    break;                case "cssSelector" :                    $.each(value, function (fn, cssSel) {                        self._cssSelector(fn, cssSel); // NB: The option is set inside this function, after further validity checks.                    });                    break;                case "playbackRate" :                    this.options[key] = value = this._limitValue(value, this.options.minPlaybackRate, this.options.maxPlaybackRate);                    if (this.html.used) {                        this._html_setProperty('playbackRate', value);                    }                    this._updatePlaybackRate();                    break;                case "defaultPlaybackRate" :                    this.options[key] = value = this._limitValue(value, this.options.minPlaybackRate, this.options.maxPlaybackRate);                    if (this.html.used) {                        this._html_setProperty('defaultPlaybackRate', value);                    }                    this._updatePlaybackRate();                    break;                case "minPlaybackRate" :                    this.options[key] = value = this._limitValue(value, 0.1, this.options.maxPlaybackRate - 0.1);                    this._updatePlaybackRate();                    break;                case "maxPlaybackRate" :                    this.options[key] = value = this._limitValue(value, this.options.minPlaybackRate + 0.1, 16);                    this._updatePlaybackRate();                    break;                case "fullScreen" :                    if (this.options[key] !== value) { // if changed                        var wkv = $.jPlayer.nativeFeatures.fullscreen.used.webkitVideo;                        if (!wkv || wkv && !this.status.waitForPlay) {                            if (!wkv) { // No sensible way to unset option on these devices.                                this.options[key] = value;                            }                            if (value) {                                this._requestFullscreen();                            } else {                                this._exitFullscreen();                            }                            if (!wkv) {                                this._setOption("fullWindow", value);                            }                        }                    }                    break;                case "fullWindow" :                    if (this.options[key] !== value) { // if changed                        this._removeUiClass();                        this.options[key] = value;                        this._refreshSize();                    }                    break;                case "size" :                    if (!this.options.fullWindow && this.options[key].cssClass !== value.cssClass) {                        this._removeUiClass();                    }                    this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.                    this._refreshSize();                    break;                case "sizeFull" :                    if (this.options.fullWindow && this.options[key].cssClass !== value.cssClass) {                        this._removeUiClass();                    }                    this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.                    this._refreshSize();                    break;                case "autohide" :                    this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.                    this._updateAutohide();                    break;                case "loop" :                    this._loop(value);                    break;                case "remainingDuration" :                    this.options[key] = value;                    this._updateInterface();                    break;                case "toggleDuration" :                    this.options[key] = value;                    break;                case "nativeVideoControls" :                    this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.                    this.status.nativeVideoControls = this._uaBlocklist(this.options.nativeVideoControls);                    this._restrictNativeVideoControls();                    this._updateNativeVideoControls();                    break;                case "noFullWindow" :                    this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.                    this.status.nativeVideoControls = this._uaBlocklist(this.options.nativeVideoControls); // Need to check again as noFullWindow can depend on this flag and the restrict() can override it.                    this.status.noFullWindow = this._uaBlocklist(this.options.noFullWindow);                    this._restrictNativeVideoControls();                    this._updateButtons();                    break;                case "noVolume" :                    this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.                    this.status.noVolume = this._uaBlocklist(this.options.noVolume);                    this._updateVolume();                    this._updateMute();                    break;                case "emulateHtml" :                    if (this.options[key] !== value) { // To avoid multiple event handlers being created, if true already.                        this.options[key] = value;                        if (value) {                            this._emulateHtmlBridge();                        } else {                            this._destroyHtmlBridge();                        }                    }                    break;                case "timeFormat" :                    this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.                    break;                case "keyEnabled" :                    this.options[key] = value;                    if (!value && this === $.jPlayer.focus) {                        $.jPlayer.focus = null;                    }                    break;                case "keyBindings" :                    this.options[key] = $.extend(true, {}, this.options[key], value); // store a merged DEEP copy of it, incase not all properties changed.                    break;                case "audioFullScreen" :                    this.options[key] = value;                    break;                case "autoBlur" :                    this.options[key] = value;                    break;            }            return this;        },        // End of: (Options code adapted from ui.widget.js)        _refreshSize: function () {            this._setSize(); // update status and jPlayer element size            this._addUiClass(); // update the ui class            this._updateSize(); // update internal sizes            this._updateButtons();            this._updateAutohide();            this._trigger($.jPlayer.event.resize);        },        _setSize: function () {            // Determine the current size from the options            if (this.options.fullWindow) {                this.status.width = this.options.sizeFull.width;                this.status.height = this.options.sizeFull.height;                this.status.cssClass = this.options.sizeFull.cssClass;            } else {                this.status.width = this.options.size.width;                this.status.height = this.options.size.height;                this.status.cssClass = this.options.size.cssClass;            }            // Set the size of the jPlayer area.            this.element.css({'width': this.status.width, 'height': this.status.height});        },        _addUiClass: function () {            if (this.ancestorJq.length) {                this.ancestorJq.addClass(this.status.cssClass);            }        },        _removeUiClass: function () {            if (this.ancestorJq.length) {                this.ancestorJq.removeClass(this.status.cssClass);            }        },        _updateSize: function () {            // The poster uses show/hide so can simply resize it.            this.internal.poster.jq.css({'width': this.status.width, 'height': this.status.height});            // Video html or flash resized if necessary at this time, or if native video controls being used.            if (!this.status.waitForPlay && this.html.active && this.status.video || this.html.video.available && this.html.used && this.status.nativeVideoControls) {                this.internal.video.jq.css({'width': this.status.width, 'height': this.status.height});            } else if (!this.status.waitForPlay && this.flash.active && this.status.video) {                this.internal.flash.jq.css({'width': this.status.width, 'height': this.status.height});            }        },        _updateAutohide: function () {            var self = this,                event = "mousemove.jPlayer",                namespace = ".jPlayerAutohide",                eventType = event + namespace,                handler = function (event) {                    var moved = false,                        deltaX, deltaY;                    if (typeof self.internal.mouse !== "undefined") {                        //get the change from last position to this position                        deltaX = self.internal.mouse.x - event.pageX;                        deltaY = self.internal.mouse.y - event.pageY;                        moved = (Math.floor(deltaX) > 0) || (Math.floor(deltaY) > 0);                    } else {                        moved = true;                    }                    // store current position for next method execution                    self.internal.mouse = {                        x: event.pageX,                        y: event.pageY                    };                    // if mouse has been actually moved, do the gui fadeIn/fadeOut                    if (moved) {                        self.css.jq.gui.fadeIn(self.options.autohide.fadeIn, function () {                            clearTimeout(self.internal.autohideId);                            self.internal.autohideId = setTimeout(function () {                                self.css.jq.gui.fadeOut(self.options.autohide.fadeOut);                            }, self.options.autohide.hold);                        });                    }                };            if (this.css.jq.gui.length) {                // End animations first so that its callback is executed now.                // Otherwise an in progress fadeIn animation still has the callback to fadeOut again.                this.css.jq.gui.stop(true, true);                // Removes the fadeOut operation from the fadeIn callback.                clearTimeout(this.internal.autohideId);                // undefine mouse                delete this.internal.mouse;                this.element.unbind(namespace);                this.css.jq.gui.unbind(namespace);                if (!this.status.nativeVideoControls) {                    if (this.options.fullWindow && this.options.autohide.full || !this.options.fullWindow && this.options.autohide.restored) {                        this.element.bind(eventType, handler);                        this.css.jq.gui.bind(eventType, handler);                        this.css.jq.gui.hide();                    } else {                        this.css.jq.gui.show();                    }                } else {                    this.css.jq.gui.hide();                }            }        },        fullScreen: function (event) {            var guiAction = typeof event === "object"; // Flags GUI click events so we know this was not a direct command, but an action taken by the user on the GUI.            if (guiAction && this.options.useStateClassSkin && this.options.fullScreen) {                this._setOption("fullScreen", false);            } else {                this._setOption("fullScreen", true);            }        },        restoreScreen: function () {            this._setOption("fullScreen", false);        },        _fullscreenAddEventListeners: function () {            var self = this,                fs = $.jPlayer.nativeFeatures.fullscreen;            if (fs.api.fullscreenEnabled) {                if (fs.event.fullscreenchange) {                    // Create the event handler function and store it for removal.                    if (typeof this.internal.fullscreenchangeHandler !== 'function') {                        this.internal.fullscreenchangeHandler = function () {                            self._fullscreenchange();                        };                    }                    document.addEventListener(fs.event.fullscreenchange, this.internal.fullscreenchangeHandler, false);                }                // No point creating handler for fullscreenerror.                // Either logic avoids fullscreen occurring (w3c/moz), or their is no event on the browser (webkit).            }        },        _fullscreenRemoveEventListeners: function () {            var fs = $.jPlayer.nativeFeatures.fullscreen;            if (this.internal.fullscreenchangeHandler) {                document.removeEventListener(fs.event.fullscreenchange, this.internal.fullscreenchangeHandler, false);            }        },        _fullscreenchange: function () {            // If nothing is fullscreen, then we cannot be in fullscreen mode.            if (this.options.fullScreen && !$.jPlayer.nativeFeatures.fullscreen.api.fullscreenElement()) {                this._setOption("fullScreen", false);            }        },        _requestFullscreen: function () {            // Either the container or the jPlayer div            var e = this.ancestorJq.length ? this.ancestorJq[0] : this.element[0],                fs = $.jPlayer.nativeFeatures.fullscreen;            // This method needs the video element. For iOS and Android.            if (fs.used.webkitVideo) {                e = this.htmlElement.video;            }            if (fs.api.fullscreenEnabled) {                fs.api.requestFullscreen(e);            }        },        _exitFullscreen: function () {            var fs = $.jPlayer.nativeFeatures.fullscreen,                e;            // This method needs the video element. For iOS and Android.            if (fs.used.webkitVideo) {                e = this.htmlElement.video;            }            if (fs.api.fullscreenEnabled) {                fs.api.exitFullscreen(e);            }        },        _html_initMedia: function (media) {            // Remove any existing track elements            var $media = $(this.htmlElement.media).empty();            // Create any track elements given with the media, as an Array of track Objects.            $.each(media.track || [], function (i, v) {                var track = document.createElement('track');                track.setAttribute("kind", v.kind ? v.kind : "");                track.setAttribute("src", v.src ? v.src : "");                track.setAttribute("srclang", v.srclang ? v.srclang : "");                track.setAttribute("label", v.label ? v.label : "");                if (v.def) {                    track.setAttribute("default", v.def);                }                $media.append(track);            });            this.htmlElement.media.src = this.status.src;            if (this.options.preload !== 'none') {                this._html_load(); // See function for comments            }            this._trigger($.jPlayer.event.timeupdate); // The flash generates this event for its solution.        },        _html_setFormat: function (media) {            var self = this;            // Always finds a format due to checks in setMedia()            $.each(this.formats, function (priority, format) {                if (self.html.support[format] && media[format]) {                    self.status.src = media[format];                    self.status.format[format] = true;                    self.status.formatType = format;                    return false;                }            });        },        _html_setAudio: function (media) {            this._html_setFormat(media);            this.htmlElement.media = this.htmlElement.audio;            this._html_initMedia(media);        },        _html_setVideo: function (media) {            this._html_setFormat(media);            if (this.status.nativeVideoControls) {                this.htmlElement.video.poster = this._validString(media.poster) ? media.poster : "";            }            this.htmlElement.media = this.htmlElement.video;            this._html_initMedia(media);        },        _html_resetMedia: function () {            if (this.htmlElement.media) {                if (this.htmlElement.media.id === this.internal.video.id && !this.status.nativeVideoControls) {                    this.internal.video.jq.css({'width': '0px', 'height': '0px'});                }                this.htmlElement.media.pause();            }        },        _html_clearMedia: function () {            if (this.htmlElement.media) {                this.htmlElement.media.src = "about:blank";                // The following load() is only required for Firefox 3.6 (PowerMacs).                // Recent HTMl5 browsers only require the src change. Due to changes in W3C spec and load() effect.                this.htmlElement.media.load(); // Stops an old, "in progress" download from continuing the download. Triggers the loadstart, error and emptied events, due to the empty src. Also an abort event if a download was in progress.            }        },        _html_load: function () {            // This function remains to allow the early HTML5 browsers to work, such as Firefox 3.6            // A change in the W3C spec for the media.load() command means that this is no longer necessary.            // This command should be removed and actually causes minor undesirable effects on some browsers. Such as loading the whole file and not only the metadata.            if (this.status.waitForLoad) {                this.status.waitForLoad = false;                this.htmlElement.media.load();            }            clearTimeout(this.internal.htmlDlyCmdId);        },        _html_play: function (time) {            var self = this,                media = this.htmlElement.media;            this.androidFix.pause = false; // Cancel the pause fix.            this._html_load(); // Loads if required and clears any delayed commands.            // Setup the Android Fix.            if (this.androidFix.setMedia) {                this.androidFix.play = true;                this.androidFix.time = time;            } else if (!isNaN(time)) {                // Attempt to play it, since iOS has been ignoring commands                if (this.internal.cmdsIgnored) {                    media.play();                }                try {                    // !media.seekable is for old HTML5 browsers, like Firefox 3.6.                    // Checking seekable.length is important for iOS6 to work with setMedia().play(time)                    if (!media.seekable || typeof media.seekable === "object" && media.seekable.length > 0) {                        media.currentTime = time;                        media.play();                    } else {                        throw 1;                    }                } catch (err) {                    this.internal.htmlDlyCmdId = setTimeout(function () {                        self.play(time);                    }, 250);                    return; // Cancel execution and wait for the delayed command.                }            } else {                media.play();            }            this._html_checkWaitForPlay();        },        _html_pause: function (time) {            var self = this,                media = this.htmlElement.media;            this.androidFix.play = false; // Cancel the play fix.            if (time > 0) { // We do not want the stop() command, which does pause(0), causing a load operation.                this._html_load(); // Loads if required and clears any delayed commands.            } else {                clearTimeout(this.internal.htmlDlyCmdId);            }            // Order of these commands is important for Safari (Win) and IE9. Pause then change currentTime.            media.pause();            // Setup the Android Fix.            if (this.androidFix.setMedia) {                this.androidFix.pause = true;                this.androidFix.time = time;            } else if (!isNaN(time)) {                try {                    if (!media.seekable || typeof media.seekable === "object" && media.seekable.length > 0) {                        media.currentTime = time;                    } else {                        throw 1;                    }                } catch (err) {                    this.internal.htmlDlyCmdId = setTimeout(function () {                        self.pause(time);                    }, 250);                    return; // Cancel execution and wait for the delayed command.                }            }            if (time > 0) { // Avoids a setMedia() followed by stop() or pause(0) hiding the video play button.                this._html_checkWaitForPlay();            }        },        _html_playHead: function (percent) {            var self = this,                media = this.htmlElement.media;            this._html_load(); // Loads if required and clears any delayed commands.            // This playHead() method needs a refactor to apply the android fix.            try {                if (typeof media.seekable === "object" && media.seekable.length > 0) {                    media.currentTime = percent * media.seekable.end(media.seekable.length - 1) / 100;                } else if (media.duration > 0 && !isNaN(media.duration)) {                    media.currentTime = percent * media.duration / 100;                } else {                    throw "e";                }            } catch (err) {                this.internal.htmlDlyCmdId = setTimeout(function () {                    self.playHead(percent);                }, 250);                return; // Cancel execution and wait for the delayed command.            }            if (!this.status.waitForLoad) {                this._html_checkWaitForPlay();            }        },        _html_checkWaitForPlay: function () {            if (this.status.waitForPlay) {                this.status.waitForPlay = false;                if (this.css.jq.videoPlay.length) {                    this.css.jq.videoPlay.hide();                }                if (this.status.video) {                    this.internal.poster.jq.hide();                    this.internal.video.jq.css({'width': this.status.width, 'height': this.status.height});                }            }        },        _html_setProperty: function (property, value) {            if (this.html.audio.available) {                this.htmlElement.audio[property] = value;            }            if (this.html.video.available) {                this.htmlElement.video[property] = value;            }        },        _aurora_setAudio: function (media) {            var self = this;            // Always finds a format due to checks in setMedia()            $.each(this.formats, function (priority, format) {                if (self.aurora.support[format] && media[format]) {                    self.status.src = media[format];                    self.status.format[format] = true;                    self.status.formatType = format;                    return false;                }            });            this.aurora.player = new AV.Player.fromURL(this.status.src);            this._addAuroraEventListeners(this.aurora.player, this.aurora);            if (this.options.preload === 'auto') {                this._aurora_load();                this.status.waitForLoad = false;            }        },        _aurora_resetMedia: function () {            if (this.aurora.player) {                this.aurora.player.stop();            }        },        _aurora_clearMedia: function () {            // Nothing to clear.        },        _aurora_load: function () {            if (this.status.waitForLoad) {                this.status.waitForLoad = false;                this.aurora.player.preload();            }        },        _aurora_play: function (time) {            if (!this.status.waitForLoad) {                if (!isNaN(time)) {                    this.aurora.player.seek(time);                }            }            if (!this.aurora.player.playing) {                this.aurora.player.play();            }            this.status.waitForLoad = false;            this._aurora_checkWaitForPlay();            // No event from the player, update UI now.            this._updateButtons(true);            this._trigger($.jPlayer.event.play);        },        _aurora_pause: function (time) {            if (!isNaN(time)) {                this.aurora.player.seek(time * 1000);            }            this.aurora.player.pause();            if (time > 0) { // Avoids a setMedia() followed by stop() or pause(0) hiding the video play button.                this._aurora_checkWaitForPlay();            }            // No event from the player, update UI now.            this._updateButtons(false);            this._trigger($.jPlayer.event.pause);        },        _aurora_playHead: function (percent) {            if (this.aurora.player.duration > 0) {                // The seek() sould be in milliseconds, but the only codec that works with seek (aac.js) uses seconds.                this.aurora.player.seek(percent * this.aurora.player.duration / 100); // Using seconds            }            if (!this.status.waitForLoad) {                this._aurora_checkWaitForPlay();            }        },        _aurora_checkWaitForPlay: function () {            if (this.status.waitForPlay) {                this.status.waitForPlay = false;            }        },        _aurora_volume: function (v) {            this.aurora.player.volume = v * 100;        },        _aurora_mute: function (m) {            if (m) {                this.aurora.properties.lastvolume = this.aurora.player.volume;                this.aurora.player.volume = 0;            } else {                this.aurora.player.volume = this.aurora.properties.lastvolume;            }            this.aurora.properties.muted = m;        },        _flash_setAudio: function (media) {            var self = this;            try {                // Always finds a format due to checks in setMedia()                $.each(this.formats, function (priority, format) {                    if (self.flash.support[format] && media[format]) {                        switch (format) {                            case "m4a" :                            case "fla" :                                self._getMovie().fl_setAudio_m4a(media[format]);                                break;                            case "mp3" :                                self._getMovie().fl_setAudio_mp3(media[format]);                                break;                            case "rtmpa":                                self._getMovie().fl_setAudio_rtmp(media[format]);                                break;                        }                        self.status.src = media[format];                        self.status.format[format] = true;                        self.status.formatType = format;                        return false;                    }                });                if (this.options.preload === 'auto') {                    this._flash_load();                    this.status.waitForLoad = false;                }            } catch (err) {                this._flashError(err);            }        },        _flash_setVideo: function (media) {            var self = this;            try {                // Always finds a format due to checks in setMedia()                $.each(this.formats, function (priority, format) {                    if (self.flash.support[format] && media[format]) {                        switch (format) {                            case "m4v" :                            case "flv" :                                self._getMovie().fl_setVideo_m4v(media[format]);                                break;                            case "rtmpv":                                self._getMovie().fl_setVideo_rtmp(media[format]);                                break;                        }                        self.status.src = media[format];                        self.status.format[format] = true;                        self.status.formatType = format;                        return false;                    }                });                if (this.options.preload === 'auto') {                    this._flash_load();                    this.status.waitForLoad = false;                }            } catch (err) {                this._flashError(err);            }        },        _flash_resetMedia: function () {            this.internal.flash.jq.css({'width': '0px', 'height': '0px'}); // Must do via CSS as setting attr() to zero causes a jQuery error in IE.            this._flash_pause(NaN);        },        _flash_clearMedia: function () {            try {                this._getMovie().fl_clearMedia();            } catch (err) {                this._flashError(err);            }        },        _flash_load: function () {            try {                this._getMovie().fl_load();            } catch (err) {                this._flashError(err);            }            this.status.waitForLoad = false;        },        _flash_play: function (time) {            try {                this._getMovie().fl_play(time);            } catch (err) {                this._flashError(err);            }            this.status.waitForLoad = false;            this._flash_checkWaitForPlay();        },        _flash_pause: function (time) {            try {                this._getMovie().fl_pause(time);            } catch (err) {                this._flashError(err);            }            if (time > 0) { // Avoids a setMedia() followed by stop() or pause(0) hiding the video play button.                this.status.waitForLoad = false;                this._flash_checkWaitForPlay();            }        },        _flash_playHead: function (p) {            try {                this._getMovie().fl_play_head(p);            } catch (err) {                this._flashError(err);            }            if (!this.status.waitForLoad) {                this._flash_checkWaitForPlay();            }        },        _flash_checkWaitForPlay: function () {            if (this.status.waitForPlay) {                this.status.waitForPlay = false;                if (this.css.jq.videoPlay.length) {                    this.css.jq.videoPlay.hide();                }                if (this.status.video) {                    this.internal.poster.jq.hide();                    this.internal.flash.jq.css({'width': this.status.width, 'height': this.status.height});                }            }        },        _flash_volume: function (v) {            try {                this._getMovie().fl_volume(v);            } catch (err) {                this._flashError(err);            }        },        _flash_mute: function (m) {            try {                this._getMovie().fl_mute(m);            } catch (err) {                this._flashError(err);            }        },        _getMovie: function () {            return document[this.internal.flash.id];        },        _getFlashPluginVersion: function () {            // _getFlashPluginVersion() code influenced by:            // - FlashReplace 1.01: http://code.google.com/p/flashreplace/            // - SWFObject 2.2: http://code.google.com/p/swfobject/            var version = 0,                flash;            if (window.ActiveXObject) {                try {                    flash = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");                    if (flash) { // flash will return null when ActiveX is disabled                        var v = flash.GetVariable("$version");                        if (v) {                            v = v.split(" ")[1].split(",");                            version = parseInt(v[0], 10) + "." + parseInt(v[1], 10);                        }                    }                } catch (e) {                }            } else if (navigator.plugins && navigator.mimeTypes.length > 0) {                flash = navigator.plugins["Shockwave Flash"];                if (flash) {                    version = navigator.plugins["Shockwave Flash"].description.replace(/.*\s(\d+\.\d+).*/, "$1");                }            }            return version * 1; // Converts to a number        },        _checkForFlash: function (version) {            var flashOk = false;            if (this._getFlashPluginVersion() >= version) {                flashOk = true;            }            return flashOk;        },        _validString: function (url) {            return (url && typeof url === "string"); // Empty strings return false        },        _limitValue: function (value, min, max) {            return (value < min) ? min : ((value > max) ? max : value);        },        _urlNotSetError: function (context) {            this._error({                type: $.jPlayer.error.URL_NOT_SET,                context: context,                message: $.jPlayer.errorMsg.URL_NOT_SET,                hint: $.jPlayer.errorHint.URL_NOT_SET            });        },        _flashError: function (error) {            var errorType;            if (!this.internal.ready) {                errorType = "FLASH";            } else {                errorType = "FLASH_DISABLED";            }            this._error({                type: $.jPlayer.error[errorType],                context: this.internal.flash.swf,                message: $.jPlayer.errorMsg[errorType] + error.message,                hint: $.jPlayer.errorHint[errorType]            });            // Allow the audio player to recover if display:none and then shown again, or with position:fixed on Firefox.            // This really only affects audio in a media player, as an audio player could easily move the jPlayer element away from such issues.            this.internal.flash.jq.css({'width': '1px', 'height': '1px'});        },        _error: function (error) {            this._trigger($.jPlayer.event.error, error);            if (this.options.errorAlerts) {                this._alert("Error!" + (error.message ? "\n" + error.message : "") + (error.hint ? "\n" + error.hint : "") + "\nContext: " + error.context);            }        },        _warning: function (warning) {            this._trigger($.jPlayer.event.warning, undefined, warning);            if (this.options.warningAlerts) {                this._alert("Warning!" + (warning.message ? "\n" + warning.message : "") + (warning.hint ? "\n" + warning.hint : "") + "\nContext: " + warning.context);            }        },        _alert: function (message) {            var msg = "jPlayer " + this.version.script + " : id='" + this.internal.self.id + "' : " + message;            if (!this.options.consoleAlerts) {                alert(msg);            } else if (window.console && window.console.log) {                window.console.log(msg);            }        },        _emulateHtmlBridge: function () {            var self = this;            // Emulate methods on jPlayer's DOM element.            $.each($.jPlayer.emulateMethods.split(/\s+/g), function (i, name) {                self.internal.domNode[name] = function (arg) {                    self[name](arg);                };            });            // Bubble jPlayer events to its DOM element.            $.each($.jPlayer.event, function (eventName, eventType) {                var nativeEvent = true;                $.each($.jPlayer.reservedEvent.split(/\s+/g), function (i, name) {                    if (name === eventName) {                        nativeEvent = false;                        return false;                    }                });                if (nativeEvent) {                    self.element.bind(eventType + ".jPlayer.jPlayerHtml", function () { // With .jPlayer & .jPlayerHtml namespaces.                        self._emulateHtmlUpdate();                        var domEvent = document.createEvent("Event");                        domEvent.initEvent(eventName, false, true);                        self.internal.domNode.dispatchEvent(domEvent);                    });                }                // The error event would require a special case            });            // IE9 has a readyState property on all elements. The document should have it, but all (except media) elements inherit it in IE9. This conflicts with Popcorn, which polls the readyState.        },        _emulateHtmlUpdate: function () {            var self = this;            $.each($.jPlayer.emulateStatus.split(/\s+/g), function (i, name) {                self.internal.domNode[name] = self.status[name];            });            $.each($.jPlayer.emulateOptions.split(/\s+/g), function (i, name) {                self.internal.domNode[name] = self.options[name];            });        },        _destroyHtmlBridge: function () {            var self = this;            // Bridge event handlers are also removed by destroy() through .jPlayer namespace.            this.element.unbind(".jPlayerHtml"); // Remove all event handlers created by the jPlayer bridge. So you can change the emulateHtml option.            // Remove the methods and properties            var emulated = $.jPlayer.emulateMethods + " " + $.jPlayer.emulateStatus + " " + $.jPlayer.emulateOptions;            $.each(emulated.split(/\s+/g), function (i, name) {                delete self.internal.domNode[name];            });        }    };    $.jPlayer.error = {        FLASH: "e_flash",        FLASH_DISABLED: "e_flash_disabled",        NO_SOLUTION: "e_no_solution",        NO_SUPPORT: "e_no_support",        URL: "e_url",        URL_NOT_SET: "e_url_not_set",        VERSION: "e_version"    };    $.jPlayer.errorMsg = {        FLASH: "jPlayer's Flash fallback is not configured correctly, or a command was issued before the jPlayer Ready event. Details: ", // Used in: _flashError()        FLASH_DISABLED: "jPlayer's Flash fallback has been disabled by the browser due to the CSS rules you have used. Details: ", // Used in: _flashError()        NO_SOLUTION: "No solution can be found by jPlayer in this browser. Neither HTML nor Flash can be used.", // Used in: _init()        NO_SUPPORT: "It is not possible to play any media format provided in setMedia() on this browser using your current options.", // Used in: setMedia()        URL: "Media URL could not be loaded.", // Used in: jPlayerFlashEvent() and _addHtmlEventListeners()        URL_NOT_SET: "Attempt to issue media playback commands, while no media url is set.", // Used in: load(), play(), pause(), stop() and playHead()        VERSION: "jPlayer " + $.jPlayer.prototype.version.script + " needs Jplayer.swf version " + $.jPlayer.prototype.version.needFlash + " but found " // Used in: jPlayerReady()    };    $.jPlayer.errorHint = {        FLASH: "Check your swfPath option and that Jplayer.swf is there.",        FLASH_DISABLED: "Check that you have not display:none; the jPlayer entity or any ancestor.",        NO_SOLUTION: "Review the jPlayer options: support and supplied.",        NO_SUPPORT: "Video or audio formats defined in the supplied option are missing.",        URL: "Check media URL is valid.",        URL_NOT_SET: "Use setMedia() to set the media URL.",        VERSION: "Update jPlayer files."    };    $.jPlayer.warning = {        CSS_SELECTOR_COUNT: "e_css_selector_count",        CSS_SELECTOR_METHOD: "e_css_selector_method",        CSS_SELECTOR_STRING: "e_css_selector_string",        OPTION_KEY: "e_option_key"    };    $.jPlayer.warningMsg = {        CSS_SELECTOR_COUNT: "The number of css selectors found did not equal one: ",        CSS_SELECTOR_METHOD: "The methodName given in jPlayer('cssSelector') is not a valid jPlayer method.",        CSS_SELECTOR_STRING: "The methodCssSelector given in jPlayer('cssSelector') is not a String or is empty.",        OPTION_KEY: "The option requested in jPlayer('option') is undefined."    };    $.jPlayer.warningHint = {        CSS_SELECTOR_COUNT: "Check your css selector and the ancestor.",        CSS_SELECTOR_METHOD: "Check your method name.",        CSS_SELECTOR_STRING: "Check your css selector is a string.",        OPTION_KEY: "Check your option name."    };}));
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


