$(document).on('click', 'input[name="code-r"]', function(e) {    let obj = this;    clipboard_get(function(clip) {        $(obj).val(clip);    });});function auth() {    $('.auth-form').removeClass('invisible');    setTimeout(function() {        $('.auth-form').addClass('open');        setTimeout(function() {            setOverlayJust();            $('main').addClass('blur');        }, 100);    }, 10);}function hide_auth() {    $('.auth-form').removeClass('open');    delOvelay();    setTimeout(function() {        $('main').removeClass('blur');        $('.auth-form').addClass('invisible');    }, 300);}function pass_no_pass(obj) {    let types = $(obj).parent().find('input').attr('type');    if(types === 'password') {        $(obj).parent().find('input').attr('type', 'text');    } else {        $(obj).parent().find('input').attr('type', 'password');    }}function auth_send() {    let arr = {        'email-r':$('.auth-form input[name="email-r"]').val(),        'password-r':$('.auth-form input[name="password-r"]').val(),    };    console.dir(arr);    AJAX('/', {'email-r': arr['email-r'], 'password-r': arr['password-r'], 'auth-r':'rbs'}, function(mess) {        switch(mess) {            case 'admin':                location.href = '/admin';                break;            case 'ok':                location.href = '/';                break;            case 'profil':                location.href = '/profil';                break;            default:                say('Таких авторизационных данных<br>в нашей базе - обнаружено...', 2);                break;        }    });    return false;}function reg() {    let form = $('<form id="reg-form" class="auth-form open" method="post" action="/"></form>');    $('#body-s').append(form);    loadTemplateIn('reg-form', 'reg-form', {}, function(e) {        $('#auth-form').addClass('invisible');        $('#auth-form').removeClass('open');    });}function reg_send() {    AJAX('/', {'phone-r':$('#reg-form input[name="phone-r"]').val(), 'name-r':$('#reg-form input[name="name-r"]').val(), 'email-r':$('#reg-form input[name="email-r"]').val(), 'password-r':$('#reg-form input[name="password-r"]').val(), 'reg-r':'rbs'}, function(mess) {        mess_executer(mess, function() {            // say('Для активации вашей учётной записи - перейдите в вашу почту, найдите письмо от нас и пройдите по предложенной там ссылке!', 4);            $('#container').remove();            $('#reg-form').remove();            open_popup('message', {text: 'Для активации вашей учётной записи - перейдите в вашу почту,<br>найдите письмо от нас и пройдите по предложенной там ссылке!'});        });    });    return false;}function email_code_auth(email='') {    if(email === '') {        email = $('input[name="email-r"]').val();    }    $('.auth-form-inner .auth-field:nth-child(3)').addClass('invisible');    $('.auth-form-inner label:nth-child(4)').addClass('invisible');    $('.auth-form-inner .forgot-pass').addClass('invisible');    $('.auth-form-inner .btn:nth-child(6)').addClass('invisible');    $('.auth-form-inner .auth-field:nth-child(2) legend').text('Введите ваш E-Mail');    if(email.length < 5) {        rubber($('input[name="email-r"]'));    } else {        buffer_app = 'SHOPS';        SENDER_APP('auth_email_code', {email: email}, function(mess) {            mess_executer(mess, function(mess) {                $('.auth-form-inner p:first-child').html('На указанную почту вам был отправлен код,<br>введите его в поле ниже и нажмите<br>"ВОЙТИ ПО КОДУ".');                $('.auth-form-inner .auth-field:nth-child(2) legend').text('5-ти значный код из E-Mail');                $('input[name="email-r"]').attr('type', 'number');                $('input[name="email-r"]').attr('name', 'code-r');                $('input[name="code-r"]').val('');                $('input[name="code-r"]').attr('placeholder', '');                setTimeout(function() {                    rubber($('input[name="code-r"]'));                }, 500);                $('div.paddinger-5px').addClass('invisible');                $('div.paddinger-5px').after($('<button onclick="auth_code()" type="button" class="btn flex align-center between gap-15 svg-white" style="padding: 17px 12px; margin-bottom: 15px">ВОЙТИ ПО КОДУ</button>'));            });        });    }}function auth_code() {    let code = $('input[name="code-r"]').val();    if(code.length === 5) {        buffer_app = 'SHOPS';        SENDER_APP('auth_code', {code: code}, function(mess) {            mess_executer(mess, function(mess) {                location.href = 'https://rumbra.ru/profil?title=account';            });        });    }}function sms_reg(phone='') {    $('#krest').attr('onclick', 'location.reload()');    if(phone === '') {        $('.auth-form-inner p:first-child').html('Если нет аккаунта<br>Введите ваш номер телефона,<br>(обязательно с кодом страны), напр. 7 9232811515');        $('#auth-form header span:first-child').text('Регистрация');    } else {        $('.auth-form-inner p:first-child').html('Ожидайте пожалуйста...');        $('#auth-form header span:first-child').text('Авторизация по номеру');    }    $('.auth-form-inner fieldset:nth-child(2) legend').text('Ваш номер телефона');    $('.auth-form-inner fieldset:nth-child(2) input').val('');    $('.auth-form-inner fieldset:nth-child(2) input').attr('type', 'tel');    $('.auth-form-inner fieldset:nth-child(2) input').attr('name', 'phone');    $('.auth-form-inner fieldset:nth-child(2) input').attr('autocomplete', 'off');    $('.auth-form-inner fieldset:nth-child(2) input').addClass('phone-corrector');    $('.auth-form-inner fieldset:nth-child(2) input').attr('placeholder', '+7 XXXXXXXXXX');    $('.auth-form-inner fieldset:nth-child(3)').remove();    $('.forgot-pass').remove();    $('.auth-form-inner button:nth-child(3)').remove();    $('.auth-form-inner .paddinger-5px button:first-child').remove();    $('.auth-form-inner .paddinger-5px button').addClass('disabled');    $('.auth-form-inner .paddinger-5px button').addClass('btn-send-sms');    $('.auth-form-inner .paddinger-5px button svg').after('<span>Получить код</span>');    $('.auth-form-inner label').remove();    $('.btn-send-sms').attr('onclick', 'registred_sms()');    $('.btn-send-sms').text('Отправить');    if(phone !== '') {        $('.auth-form-inner fieldset:nth-child(2) input').val(VALUES.clear_phone(phone));        $('.btn-send-sms').text('ПОДТВЕРДИТЬ');        setTimeout(function() {            auth_sms();        }, 500);    }    // $('.auth-form-inner .paddinger-5px').append('<button type="button" onclick="coder_inputer()">CODE</button>');}function tele_reg_auth() {    location.href = 'https://t.me/rumbra_bot?start=auth';}let correct = ['0','1','2','3','4','5','6','7','8','9']; // +375 44 772-59-49$(document).on('keydown', '.phone-corrector', function(e) {    if(!correct.includes(e.key) && e.keyCode !== 8 && e.keyCode !== 37 && e.keyCode !== 39) {        e.preventDefault();    }});$(document).on('input', '.phone-corrector', function(e) {    let txt = $('.phone-corrector').val();    let cleanedText = txt.replace(/\D/g, '');    $('.phone-corrector').val(cleanedText);    setTimeout(function() {        if($('.phone-corrector').val().length >= 11 && $('.phone-corrector').val().length <= 12) {            $('.btn-send-sms').removeClass('disabled');        } else {            $('.btn-send-sms').addClass('disabled');        }    }, 100);});$(document).on('keyup', '.phone-corrector', function(e) {    if($('.phone-corrector').val().length >= 11 && $('.phone-corrector').val().length <= 12) {        $('.btn-send-sms').removeClass('disabled');    } else {        $('.btn-send-sms').addClass('disabled');    }});function registred_sms() {    let phone = $('.phone-corrector').val();    if(phone.length >= 11 && phone.length <=12) {        SENDER('registred_sms', {phone: phone}, function(mess) {            mess_executer(mess, function(mess) {                coder_inputer(phone);            });        });    }}function auth_sms() {    let phone = $('.phone-corrector').val();    if(phone.length >= 11 && phone.length <=12) {        SENDER('auth_sms', {phone: phone}, function(mess) {            mess_executer(mess, function(mess) {                coder_inputer(phone, true);            });        });    }}function coder_inputer(phone='000', auth=false) {    if(auth) {        $('.auth-form-inner p:first-child').html('На номер <b>'+phone+'</b> отправлен 5-тизначный код.<br>Для входа в систему, введите его в поле ниже.');    } else {        $('.auth-form-inner p:first-child').html('На номер <b>'+phone+'</b> отправлен 5-тизначный код.<br>Для успешного завершения регистрации, введите его в поле ниже.');    }    let inpt = $('.auth-form-inner fieldset:nth-child(2) input');    inpt.val('');    inpt.attr('type', 'number');    inpt.attr('name', 'code');    inpt.removeClass('phone-corrector');    inpt.attr('placeholder', 'XXXXX');    $('.auth-field legend').text('XXXXX - код из SMS');    let btn = $('.btn-send-sms');    if(auth) {        btn.find('span').text('ВХОД');        btn.attr('onclick', 'complite_registred_sms(true)');    } else {        btn.find('span').text('Завершить регистрацию');        btn.attr('onclick', 'complite_registred_sms()');    }    $('.btn-send-sms').removeClass('disabled');}function complite_registred_sms(auth = false) {    let code = parseInt($('.auth-form-inner fieldset:nth-child(2) input').val());    if(code < 10000 || code > 99999) {        say('Формат кода не верный!..', 2);        return false;    }    $('.btn-send-sms').addClass('disabled');    SENDER('complite_registred_sms', {code: code, auth: auth}, function(mess) {        mess = JSON.parse(mess);        if(mess.status === 'error') {            say(mess.body, 2);            setTimeout(function() {                location.reload();            }, 7000);        } else if(mess.status === 'ok') {            if(auth) {                location.href = '/profil?title=account&message=autorized';            } else {                location.href = '/profil?title=account&message=registrated';            }            say('Успешно.<br>Сейчас вы будете перенаправлены на ваш профиль');        }    });}function forgot_pass() {    let log = $('input[name="email-r"]').val();    if(log.length < 3) {        say('Введите в поле<br>Ваш телефон, логин или e-mail<br>что-то из этого...<br>Пароль можно не заполнять.', 2);        return false;    }    $('#auth-form.open').removeClass('open');    setTimeout(function() {        delOvelay();        setTimeout(function() {            open_popup('forgot_pass', {type: VALUES.email_phone_string(log), text: log});        }, 100);    }, 300);}setTimeout(function() {    if(isset_param_in_address_row('auth')) {        auth();    }}, 500);
buffer_app = 'SHOPS';$(document).on('dragover', '.loader-img-form', function(e) {    e.preventDefault();    $(this).addClass('outline-drop');});$(document).on('dragleave', '.loader-img-form', function(e) {    e.preventDefault();    $(this).removeClass('outline-drop');});$(document).on('drop', '.loader-img-form', function(e) {    $(this).removeClass('outline-drop');    let dt = null;    if(typeof e.originalEvent === 'object') {        dt = e.originalEvent.dataTransfer;    } else {        dt = e.clipboardData;    }    e.stopPropagation();    e.preventDefault();    if(!dt && !dt.files) { return false ; }    // Получить список загружаемых файлов    let files = dt.files;    let types = [];    for (let i = 0; i < files.length; i++) {        types = files[i].type.split('/')[0];        if(files[i].size<=1073741824 && types === 'image') {            //////////////////////////////////////////////////////////////////////////            console.dir(files[i]);            let reader = new FileReader();            let img = files[i];            reader.onload = function(event) {                const image = new Image();                image.src = event.target.result;                let fileSizeInBytes = img.size;                let fileSizeInMegabytes = fileSizeInBytes / (1024 * 1024);                console.log('Размер файла: ' + fileSizeInMegabytes + ' МБ');                if(fileSizeInMegabytes > 5) {                    say('Файл занимает '+fileSizeInMegabytes+' Мб. Это много. Поищите другую фотографию, пожалуйста...', 2);                } else {                    let btn = $('<button onclick="del_img(this)" data-name="'+img.name+'" class="img-btn action-btn" title="Удалить изображение"></button>');                    $(image).attr('data-src', img.name);                    $(btn).append(image);                    $('.list-prev-img').append(btn);                    upload_files.push(img);                    console.log('UPLOAD_FILES COUNT='+upload_files.length);                }            }            reader.readAsDataURL(img);            //////////////////////////////////////////////////////////////////////////        } else {            if(types !== 'image') {                say('Разрешено загружать только изображения!..', 2);            } else {                say('Максимальный размер загружаемого файла = 5Мб', 2);            }        }    }});setInterval(function() {    let count = $('.list-prev-img img').length;    $('#count-imgs').text('Загружено '+count+' из 5');    if(count >= 5) {        $('#add-btn').addClass('disabled');    } else {        $('#add-btn').removeClass('disabled');    }}, 1000);setTimeout(function() {    $('.product-creator table tr:nth-child(6)').addClass('disabled');    $('.product-creator table tr:nth-child(7)').addClass('disabled');}, 10)$(document).on('input', '.product-creator tr td:nth-child(2) *', function(e) {    $(this).closest('tr').attr('edited-old', 'true');});function load_new_img() {    $('.table-focus label.action-btn #file-input').click();}function change_shop(obj) {    buffer_app = 'SHOPS';    let id = $(obj).find('option:selected').attr('data-id');    SENDER_APP('set_add_shop', {id: id}, function(mess) {        mess = JSON.parse(mess);        if(mess.status === 'ok') {            location.reload();        } else {            error_executing(mess);        }    });}function buffer_method_start() {    location.href = '/profil?title=my_orders&filter=not_show';}
active_stars_scan = false;
$(document).on('mouseenter', '.stars', function(e) {
    active_stars_scan = true;
});
$(document).on('mouseleave', '.stars', function(e) {
    let st = this;
    active_stars_scan = true;
    let stars = parseFloat($(st).attr('data-star'));
    let percents = (parseFloat(stars)*100/5)+'%';
    $(st).find('.star-shore').css('width', percents);
});
$(document).on('mousemove', '.stars', function(e) {
    let st = this;
    console.log('32434234234234');
    if(active_stars_scan) {
        let x = e.pageX - $(st).offset().left;
        $(st).find('.star-shore').css('width', (x * 100 / 250) + '%');
    }
});
function update_stars_draw(obj) {
    let st = this;
    let stars = parseFloat($(obj).attr('data-star'));
    let percents = (stars*100/5)+'%';
    $(obj).find('.star-shore').css('width', percents);
}
$(document).on('touchmove', '.stars', function(e) {
    let st = this;
    let x = e.originalEvent.touches[0].pageX;
    let off = $(st).offset().left;
    let p = (x - off) * 100 / 250;
    if(p > 100) { p = 100; }
    if(p < 0) { p = 0; }
    $(st).find('.star-shore').css('width', p + '%');
    let stars = (parseFloat($(st).find('.star-shore').width())) * 5 / 250;
    $(st).attr('data-star', stars);
});
$(document).on('click', '.stars', function(e) {
    let st = this;
    active_stars_scan = false;
    let stars = (parseFloat($(st).find('.star-shore').width())) * 5 / 250;
    $(st).attr('data-star', stars);
});

function show_editor(html_text, stars=0.00) {
    $('.editor-wrapper').css('display', 'block');
    setTimeout(function() {
        let iframe = document.getElementById('field_ifr');
        let iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        let style = document.createElement('style');
        style.innerHTML = 'body p { margin:0!important; }';
        iframeDoc.head.appendChild(style);

        tinymce.get('field').setContent(html_text);
        $('.editor-wrapper').addClass('editor-visible');

        let percents = '0';
        if(parseFloat(stars) > 0) {
            percents = (parseFloat(stars)*100/5)+'%';
        }

        $('.star-shore').css('width', percents);
        $('.stars').attr('data-star', stars);
        tinymce.get('field').focus();
    }, 10);
}
function hide_editor() {
    $('.editor-wrapper').removeClass('editor-visible');
    setTimeout(function() {
        $('.editor-wrapper').css('display', 'none');
    }, 500);
}
function save_content() {
    let cont = tinymce.get('field').getContent();
    console.log(cont);
    bufferText = cont;
    save_tiny(cont);
}



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


