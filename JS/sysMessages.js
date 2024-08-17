conf = 0;
buff = null;
let url_loc = new URL(location.href);
domain_name = url_loc.protocol + '//' + url_loc.hostname;
console.log('sysMessage - active...');

$height = $(window).height();
$width = $(window).width();
$mobile = false;

function is_mobile() {
    return /Mobi/.test(navigator.userAgent) || (navigator.maxTouchPoints > 2) || (navigator.msMaxTouchPoints > 2);
}

$(document).ready(function(){
    // Координаты курсора относительно всего документа
    $(document).mousemove(function(event){
        cursorPos = {
            x:event.pageX,
            y:event.pageY-$(this).scrollTop()
        }

    });

    if($width <=670) {
        $mobile = true;
    } else {
        $mobile = false;
    }
});

function setMenuItem($name) {
    $('nav a[href="/'+$name+'"]').addClass('menu-item-active');
}

function ask(text, actionFunction, password, textAccess) {
    $('.qest-modul').remove();
    if(password == undefined) {
        password = false;
    }
    if(textAccess == undefined) {
        textAccess = false;
    }

    $('body').append('<div class="qest-modul back-overlay">'+text+'</div>');
    if(password) {
        $('.qest-modul').append('<div class="input-field"><input id="pswrd" required type="password" name="ask-text" value="" style="max-width: 100%;"></div>');
    } else {
        if(textAccess == false) {
            $('.qest-modul').append('<div class="input-field"><input id="pswrd" type="text" name="ask-text" value="" style="max-width: 100%;"></div>');
        } else {
            $('.qest-modul').append('<textarea id="pswrd" class="noinpt input-field">'+textAccess+'</textarea>');
        }
    }

    $('.qest-modul').append('<input onclick="no()" type="button" class="close-map-btn" style="top: 0;">');

    $('#pswrd').focus();

    $('body').append('<div class="overlay"></div>');
    $('.qest-modul').append('<div id="cont" style="padding: 5px 0 5px; display: flex; justify-content: center;"></div>')
    $('#cont').append('<input type="button" onclick="buffOk();" class="admin-btn" value="Ok">');
    actions = actionFunction;

    setTimeout(showed, 10);

    $('#pswrd').on('keypress',function(e) {
        if(!$('#pswrd').hasClass('noinpt')) {
            if (e.which == 13) {
                buffOk();
            }
        }
    });
}

function RND(min, max) {
    return (Math.floor(Math.random() * Math.floor(max)))+min;
}

function viewportToPixels(value) {
    var parts = value.match(/([0-9\.]+)(vh|vw)/)
    var q = Number(parts[1])
    var side = window[['innerHeight', 'innerWidth'][['vh', 'vw'].indexOf(parts[2])]]
    return side * (q/100)
}

function addLoader($el_obj, size=60) {
    $($el_obj).append('<img src="/IMG/SYS/loader.gif" style="z-index: 9999; display: inline-block; width: '+size+'px; height: '+size+'px; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">');
}

function setCookies($param, $argum) {
    $.cookie($param, $argum, {expires: 360, path: '/'});
}

function setLocationURL(curLoc){
    try {
        history.pushState(null, null, curLoc);
        return;
    } catch(e) {}
    location.hash = '#' + curLoc;
}

function getCookies($param) {
    if($.cookie($param) != null) {
        return($.cookie($param));
    } else {
        return '';
    }
}

function delCookies($param) {
    $.cookie($param, '', {expires: -1, path: '/'});
}

//  расширение JQUERY - позволяет проверять наличие аттрибута
$.fn.hasAttr = function(name) {
    return this.attr(name) !== undefined;
};

function qest(text, obj) {
    buff = obj;
    $('body').append('<div class="qest-modul">'+text+'</div>');
    $('body').append('<div class="overlay"></div>');
    $('.qest-modul').append('<div id="cont" style="padding: 15px 0 5px; display: flex; justify-content: center;"></div>')
    $('#cont').append('<input type="button" onclick="yes();" class="admin-btn" value="'+_yes+'">');
    $('#cont').append('<input type="button" onclick="no();" class="admin-btn" value="'+_no+'">');

    setTimeout(showed, 10);
    return false;
}

function qest2(text, nameFunction) {
    $('body').append('<div class="qest-modul tshadow back-overlay">'+text+'</div>');
    $('body').append('<div class="overlay"></div>');
    $('.qest-modul').append('<div id="cont" style="padding: 15px 0 5px; display: flex; justify-content: center;"></div>')
    $('#cont').append('<input type="button" onclick="yes2('+nameFunction+');" class="admin-btn" value="'+_yes+'">');
    $('#cont').append('<input type="button" onclick="no();" class="admin-btn" value="'+_no+'">');

    setTimeout(showed, 10);
    return false;
}

function variants(text, $array) {
    $('body').append('<div class="qest-modul back-overlay">'+text+'</div>');
    $('.qest-modul').append('<div class="caption"></div>');
    $('.caption').append('<input onclick="no()" class="close-map-btn" type="button">');
    $('body').append('<div class="overlay"></div>');
    var cont = $('<div id="cont" class="scroll" style="padding: 0 0 5px; display: flex; justify-content: center; margin-top: 20px"></div>')
    $.each($array, function($k, $v) {
        if($v.indexOf('(') == -1) {
            var $sko = '()';
        } else {
            var $sko = '';
        }
        $(cont).append('<input onclick="'+$v+$sko+'" class="admin-btn" type="button" value="'+$k+'">');
    });

    $('.qest-modul').append(cont);
    setTimeout(showed, 10);
    return false;
}

function showed(){
    $('.qest-modul').css('top', '15vh');
}

function yes2(funct) {
    setTimeout(funct, 10);
    no();
}

function yes() {
    // console.log(buff);
    if($(buff).hasAttr('data-action')) {
        com = $(buff).attr('data-action');
        w = setTimeout(com, 1);
    } else {
        a = $(buff).prop('href');
        if (a == undefined) {
            $(buff).prop('onclick', null);
            $(buff).click();
        } else {
            window.location = a;
        }
    }
    no();
}

function no() {
    $('.qest-modul').css('top', '-100vh');
    $('.overlay').css('opacity', '0');
    setTimeout(delOvelay, 1000);
}

function no2() {
    $('.overlay').remove();
    $('.qest-modul').remove();
    conf = 0;
    buff = null;
}

function buffOk() {
    bufferPass = $('#pswrd').val();
    setTimeout(actions, 1);
    no2();
}

function fullScreen() {
    var doc = window.document;
    var docEl = doc.documentElement;
    var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen;

    var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen;
    if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement) {
        requestFullScreen.call(docEl);
    }
    else {
        cancelFullScreen.call(doc);
    }
}

function setOverlay() {
    $('body').append('<div class="overlay-gray" id="container"></div>');
}

function setOverlayJust(invisible=false) {
    if(invisible) {
        $('body').append('<div class="overlay-gray glass" style="transition: 0.2s" id="container"></div>');
    } else {
        $('body').append('<div class="overlay-gray" style="transition: 0.2s" id="container"></div>');
    }
    $('body').css('overflow', 'hidden');
}

function setOverlayJust2() {
    let overl = $('<div class="overlay-gray-2" id="container-3"></div>');
    $('body').append(overl);
    $('body').addClass('blocker');
    setTimeout(function() {
        $('.overlay-gray-2').addClass('opened');
    }, 100);
    return overl;
}

function setOverlayFull() {
    $('body').append('<div id="container-full"></div>');
}

function delOvelay() {
    $('.overlay').remove();
    $('.overlay-gray').remove();
    $('.qest-modul').remove();
    conf = 0;
    buff = null;
    $('body').css('overflow', '');
}

function createTextPanel($txt, $functonOk) {
    switch($txt) {
        case 'true' : $txt = 'false'; break;
        case 'false' : $txt = 'true'; break;
    }
    let ov = $('<div class="txt-panel-overlay"></div>');
    let pnl= $('<div class="txt-panel"></div>');
    $(pnl).append('<input onclick="cltxt()" type="button" class="close-map-btn">');
    $(pnl).append('<input onclick="'+$functonOk+'" type="button" class="ok-map-btn">');
    $(pnl).append('<textarea id="edt">'+$txt+'</textarea>');

    $(pnl).appendTo($(ov));
    $(ov).appendTo($('body'));

    setTimeout(function() {
        $('#edt').focus();
        $('#edt').select();
    }, 100);
}

function cltxt() {
    $('.txt-panel-overlay').remove();
}

function getPosition(e) {
    var posx = 0;
    var posy = 0;

    if (!e) var e = window.event;

    if (e.pageX || e.pageY) {
        posx = e.pageX;
        posy = e.pageY;
    }
    else if (e.clientX || e.clientY) {
        posx = e.clientX + document.body.scrollLeft
            + document.documentElement.scrollLeft;
        posy = e.clientY + document.body.scrollTop
            + document.documentElement.scrollTop;
    }

    return {
        x: posx,
        y: posy
    }
}

function remooveItm(obj) {
    $(obj).remove();
}

function email_valid(email) {
    let reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if(reg.test(email) === false) {
        return false;
    } else {
        return true;
    }
}

function rnd(min, max) {
    // получить случайное число от (min-0.5) до (max+0.5)
    let rand = min - 0.5 + Math.random() * (max - min + 1);
    return Math.round(rand);
}

function say($text, $type=1, $autoHide=false) {
    if($('#placer').length <= 0) {
        $('body').append('<div id="placer" style="transition: 1s;" class="messages-list"></div>');
    }
    let bg = '';
    let col = '';
    let header = '';
    let m1 = '';
    let btn = '';
    switch($type) {
        case 1:
            bg = 'rgba(163,255,150,0.93)';
            col = '#000000';
            header = '<h3>Сообщение</h3>';
            m1 = 'm-1';
            $autoHide = true;
            break;
        case 2:
            bg = '#fffeb3';
            col = '#000000';
            header = '<h3>Внимание</h3>';
            m1 = 'm-2';
            // btn = '<button class="mess-btn">OK</button>';
            break;
        case 3:
            bg = '#ffa893';
            col = '#ffc500';
            header = '<h3>Ошибка</h3>';
            m1 = 'm-3';
            // btn = '<button class="mess-btn">OK</button>';
            break;
        default:
            bg = 'rgba(195,255,185,0.93)';
            col = '#000000';
            header = '<h3>Сообщение</h3>';
            m1 = 'm-4';
            // btn = '<button class="mess-btn">OK</button>';
            break;
    }
    col = '#000000';
    let itm = $('<div class="mess-itm sys-message '+m1+' " style="background-color: '+bg+'; color: '+col+';">'+header+$text+btn+'</div>');
    if($autoHide) {
        setTimeout(function() {
            $(itm).addClass('deltime');
            $(itm).css('transition', '0.8s').css('overflow', 'hidden');
            $(itm).css('opacity', '0');
            $(itm).css('max-height', 0);
            $(itm).css('padding', '0 0 0 20px');
            $(itm).css('margin', '2px 0').css('transform', 'translateX(600px)');
            $(itm).css('border', 'none');
            setTimeout(function() {
                $(itm).css('overflow', 'hidden');
                $(itm).css('transition', '0.3s');
                $(itm).css('line-height', '0');
                $(itm).css('margin', '0');
                $(itm).css('padding', '0');
                $(itm).css('height', '0');
                setTimeout(function() {
                    $(itm).remove();
                }, 300);
            }, 1100);
        },9000);
    }

    $(itm).on('click', function() {
        $(itm).addClass('deltime');
        $(itm).css('transition', '0.8s').css('overflow', 'hidden');
        $(itm).css('opacity', '0');
        $(itm).css('max-height', '0');
        $(itm).css('margin', '2px 0').css('transform', 'translateX(600px)');
        $(itm).css('padding', '0');
        $(itm).css('border', 'none');
        setTimeout(function() {
            $(itm).css('overflow', 'hidden');
            $(itm).css('transition', '0.3s');
            $(itm).css('padding', '0');
            $(itm).css('margin', '0');
            $(itm).css('max-height', '0');
            setTimeout(function() {
                $(itm).remove();
            }, 300);
        }, 1100);
    });

    $('#placer').append(itm);
}

function loadTemplateIn(containerID, template_name, params= {}, call_back=function() {}) {
    SENDER('load_template', {template: template_name, params: params}, function(mess) {
        mess = JSON.parse(mess);
        if(mess.status === 'ok') {
            $('#'+containerID).html(mess.body);
            call_back(mess);
        } else {
            error_executing(mess, 3);
        }
    });
}

function get_template(template_name, datas={}) {
    return fetchData(template_name, datas);
}

function fetchData(template_name, datas) {
    return new Promise((resolve, reject) => {
        SENDER('load_template', {template: template_name, params: datas}, function(mess) {
            mess = JSON.parse(mess);
            if(mess.status === 'ok') {
                resolve(mess.body);
            } else {
                reject(mess);
            }
        });
    });
}

function sendAJAX($addr, $array, $successF, $errorF) {
    $.ajax({
        url: $addr,
        type: "POST",
        cache: false,
        timeout: 15000,
        data: $array,
        success: function (msg) {
            $successF(msg);
        },
        error: function (x, t, m) {
            $errorF(m);
        }
    });
}

function AJAX($addr, $array, $fReturn) {
    $.ajax({
        url: $addr,
        type: "POST",
        cache: false,
        timeout: 15000,
        data: $array,
        success: function (msg) {
            $fReturn(msg);
        }
    });
}

function SENDER(com, $array, $fReturn, $error=function(m) { console.log(m); }) {
    $array['com'] = com;
    $.ajax({
        url: '/',
        type: "POST",
        cache: false,
        timeout: 15000,
        data: $array,
        success: function (msg) {
            $fReturn(msg);
        },
        error: function (x, t, m) {
            $error(m);
        }
    });
}

function BACK(back_control_name, com, $array, $fReturn, $error=function(m) { console.log(m); }) {
    $array['com'] = com;
    $array['back'] = back_control_name;
    $.ajax({
        url: '/',
        type: "POST",
        cache: false,
        timeout: 15000,
        data: $array,
        success: function (msg) {
            $fReturn(msg);
        },
        error: function (x, t, m) {
            $error(m);
        }
    });
}

function SENDER_APP(com, $array, $fReturn, $error=function(m) { console.log(m); }) {
    $array['com'] = com;
    $array['app'] = buffer_app;
    $.ajax({
        url: '/',
        type: "POST",
        cache: false,
        timeout: 15000,
        data: $array,
        success: function (msg) {
            $fReturn(msg);
        },
        error: function (x, t, m) {
            $error(m);
        }
    });
}

function template(template, params={}, call_back) {
    AJAX('/static/exe5', {template: template, params: params}, function(mess) {
        call_back(mess);
    });
}

// // function fullScreen($bool) {
// //     if ($bool == true) {
// //         if (document.documentElement.requestFullscreen) {
// //             document.documentElement.requestFullscreen();
// //         } else if (document.documentElement.msRequestFullscreen) {
// //             document.documentElement.msRequestFullscreen();
// //         } else if (document.documentElement.mozRequestFullScreen) {
// //             document.documentElement.mozRequestFullScreen();
// //         } else if (document.documentElement.webkitRequestFullscreen) {
// //             document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
// //         }
// //     } else {
// //         if (document.exitFullscreen) {
// //             document.exitFullscreen();
// //         } else if (document.msExitFullscreen) {
// //             document.msExitFullscreen();
// //         } else if (document.mozCancelFullScreen) {
// //             document.mozCancelFullScreen();
// //         } else if (document.webkitExitFullscreen) {
// //             document.webkitExitFullscreen();
// //         }
// //     }
// // }
//
function copyToClipboard($text) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($text).select();
    document.execCommand("copy");
    $temp.remove();
}
function clipboard_get(call_back) {
    if (navigator.clipboard && navigator.clipboard.readText) {
        navigator.clipboard.readText().then(function(text) {
            call_back(text);
        });
    } else if (window.clipboardData && window.clipboardData.getData) {
        call_back(window.clipboardData.getData('Text')) ;
    }
}

function sendCom($addr, $com, $arrayObj, $reloadPage=true) {
    $arrayObj['com'] = $com;
    $.ajax({
        url: $addr,
        type: "POST",
        cache: false,
        timeout: 15000,
        data: $arrayObj,
        success: function (msg) {
            if($reloadPage === true) {
                location.reload();
            } else {
                return msg;
            }
        },
        error: function (x, t, m) {
            console.log('Ошибка связи с сервером');
        }
    });
}

function locals($key) {
    if(localStorage.getItem($key) !== null) {
        return localStorage.getItem($key);
    } else {
        return '';
    }
}

function intToMonth($num, pad=false) {
    let $arr = {};
    if(pad === false) {
        $arr = {1:'Январь', 2:'Февраль', 3:'Март', 4:'Апрель', 5:'Май', 6:'Июнь', 7:'Июль', 8:'Август', 9:'Сентябрь', 10:'Октябрь', 11:'Ноябрь', 12:'Декабрь'};
    } else {
        $arr = {1:'Января', 2:'Февраля', 3:'Марта', 4:'Апреля', 5:'Мая', 6:'Июня', 7:'Июля', 8:'Августа', 9:'Сентября', 10:'Октября', 11:'Ноября', 12:'Декабря'};
    }
    return $arr[parseInt($num)];
}

function monthToInt($month) {
    let $arr = {'январь':1, 'февраль':2, 'март':3, 'апрель':4, 'май':5, 'июнь':6, 'июль':7, 'август':8, 'сентябрь':9, 'октябрь':10, 'ноябрь':11, 'декабрь':12};
    return $arr[$month.toLowerCase()];
}

function mooveTo($tagName, $pos, href_page='') {
    if($pos == undefined) {
        $pos = 100;
    }
    var elementClick = $($tagName);
    var destination = $(elementClick).offset().top-$pos;
    $('html').animate({ scrollTop: destination }, 1100);
    return false;
}

function isset(r) {
    return typeof r !== 'undefined';
}

function translit(str, down_shirt=false) {
    let minus = '-';
    if(down_shirt) {
        minus = '_';
    }
    let ru = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
        'е': 'e', 'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i',
        'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
        'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'c', 'ч': 'ch', 'ш': 'sh',
        'щ': 'shch', 'ы': 'y', 'э': 'e', 'ю': 'u', 'я': 'ya',
        ' ':minus
    }, n_str = [];
    str = str.replace(/[ъь]+/g, '').replace(/й/g, 'i');
    for ( let i = 0; i < str.length; ++i ) {
        n_str.push(
            ru[ str[i] ]
            || ru[ str[i].toLowerCase() ] == undefined && str[i]
            || ru[ str[i].toLowerCase() ].replace(/^(.)/, function ( match ) { return match.toUpperCase() })
        );
    }
    return n_str.join('').toLowerCase();
}

function getParamFromString($allString, $param) {
    let arr = $allString.split('|');
    let $answer = '';
    $.each(arr, function($k, $v) {
        if($v.substr(0, $param.length)+'=' == $param+'=') {
            $answer = $v.substr($param.length+1, $v.length);
        }
    });
    return $answer;
}

function addParamToString($allString, $param, $argum) {
    let arr = $allString.split('|');
    let yes = false;
    $.each(arr, function($k, $v) {
        if($v.substr(0, $param.length)+'=' == $param+'=') {
            arr[$k] = $param+'='+$argum;
            yes = true;
        }
    });
    if(yes == false) {
        arr.push($param+'='+$argum);
    }
    let answer = arr.join('|');
    return answer;
}

function removeParamFromString($allString, $param) {
    let arr = $allString.split('|');
    $.each(arr, function($k, $v) {
        if($v !== undefined && $v.substr(0, $param.length)+'=' == $param+'=') {
            arr.splice($k, 1);
        }
    });
    let answer = arr.join('|');
    return answer;
}

function getImgParam($nameImg, $promise) {
    sendAJAX('/static/exec/', {getImgParam: $nameImg}, $promise, function(err) {
        say('Ошибка связи с сервером',3);
    });
}

function sliceTo($tag, $offset=0) {
    $('html, body').animate({scrollTop: 0},500);
}

function mess_info($pos=undefined, calBackMethodTrue=undefined, $qestText="Подтвердите ваши действия?", $btnTextOk="Подтверждаю") {
    if($pos === undefined) {
        $pos = cursorPos;
    }
    let nameBtn = Math.random().toString().slice(2);
    nameBtn = 'btn_'+nameBtn;
    let pnl = $('<div style="display: flex; flex-direction: column; transition: 0.2s; z-index: 20; background-color: #bbc8ff; transform: translate(-50%, -50%) scale(0); min-width: 150px; min-height: 80px; left: '+$pos.x+'px; top: '+$pos.y+'px; position: fixed; border-radius: 10px; box-shadow: 5px 5px 10px 5px rgba(0,0,0,0.57)"></div>');
    $(pnl).append('<h4 style="font-weight: 100; color: #000; justify-self: flex-start; border-top-left-radius: 10px; border-top-right-radius: 10px; margin: 0; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$qestText+'<button onclick="event.stopPropagation(); closeMessage(this)" class="close-map-btn-circ" style="width: 12px; height: 12px; margin-top: 4px; margin-right: 4px; "></button></h4>');
    let btn = $('<button id="'+nameBtn+'" class="presser" style="margin: 15px auto; width: fit-content; padding: 5px 10px; border: none; box-shadow: 1px 1px 4px 0 rgba(0,0,0,0.34); outline: none; border-radius: 7px; cursor: pointer;">'+$btnTextOk+'</button>');
    $(pnl).append(btn);
    $('#body').append(pnl);

    $(document).on('click', '#'+nameBtn, function(e) {
        e.stopPropagation();
        setTimeout(calBackMethodTrue, 0);
        closeMessage($(this).parent().children('h4').find('button'));
    });

    setTimeout(function() {
        $(pnl).css('transform', 'translate(-50%, -50%) scale(1)');
    }, 100);
    setTimeout(function() {
        $(pnl).css('transform', 'translate(-50%, -50%) scale(0)');
        setTimeout(function() {
            $(pnl).remove();
        }, 200);
    }, 20000);
}

function closeMessage(obj) {
    let obj2 = $(obj).parent().parent();
    $(obj2).css('transform', 'translate(-50%, -50%) scale(0)');
    setTimeout(function() {
        $(obj2).remove();
    }, 200);
    return false;
}

function zero_plus(int_value, count_digits, before_or_after=true) {
    while(int_value.toString().length < count_digits) {
        if(before_or_after) {
            int_value = '0'+int_value;
        } else {
            int_value += '0';
        }
    }
    return int_value;
}

function valid_email(email) {
    let reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if(reg.test(email) == false) {
        return false;
    } else {
        return true;
    }
}

function printed(class_name) {
    let divToPrint=document.getElementsByClassName(class_name)[0];
    let newWin= window.open("");
    newWin.document.write(divToPrint.outerHTML);
    newWin.print();
    newWin.close();
}

hashCode = function(s){
    return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);
}

function error_executing(mess, type_error=3) {
    console.log(mess);
    if(typeof mess === 'string') {
        mess = JSON.parse(mess);
    }
    say(mess.body+'<br>'+mess.errors.join('<br>'), type_error);
}
function set_local_url(curLoc){
    try {
        history.pushState(null, null, curLoc);
        return;
    } catch(e) {}
    location.hash = '#' + curLoc;
}
function load_JSscript_once(script_path) {
    let allScripts = document.scripts;
    let not_load = false;
    for (let i = 0; i < allScripts.length; i++) {
        if(allScripts[i].src !== '' && allScripts[i].src.indexOf(script_path) !== -1) {
            not_load = true;
        }
    }
    if(!not_load) {
        let scr = $('<script style="display: none" src="' + domain_name + script_path + '"></script>');
        $('body').append(scr);
    }
}
function dataURItoBlob(dataURI) {
    let byteString = atob(dataURI.split(',')[1]);
    let mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
    let ab = new ArrayBuffer(byteString.length);
    let ia = new Uint8Array(ab);

    for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ab], {type: mimeString});
}
function add_loader(obj, width=60, height=60) {
    $(obj).html('<img width="'+width+'" height="'+height+'" src="/DOWNLOAD/20230609-201051_id-2-217564.gif">');
}
function mess_executer(mess, call_back=function() {}) {
    mess = JSON.parse(mess);
    if(mess.status !== 'ok') {
        error_executing(mess);
    } else {
        call_back(mess);
    }
}

// Функция для вычисления MD5 хеша
function md5(input) {
    const md5Hasher = crypto.subtle.digest("MD5", new TextEncoder().encode(input));
    return md5Hasher.then(hashBuffer => {
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray.map(byte => byte.toString(16).padStart(2, '0')).join('');
        return hashHex;
    });
}
// Функция для вычисления MD5 хеша
async function md6(input) {
    const msgUint8 = new TextEncoder().encode(input);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgUint8);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    return hashHex.slice(0, 32);
}
function show_popap(obj) {
    $('#body-s').append(obj);
    $(obj).addClass('former');
    $(obj).removeClass('invisible');
    $(obj).addClass('transit');
    setTimeout(function() {
        $(obj).addClass('open');
        setTimeout(function() {
            $('main').addClass('blur');
        }, 100);
    }, 10);
}
function hide_popap(obj) {
    $(obj).removeClass('open');
    setTimeout(function() {
        $('main').removeClass('blur');
        $(obj).addClass('invisible');
        $(obj).removeClass('transit');
    }, 300);
}

function SENDER_AND_WAIT_RESULT(com, params={}, obj_for_shore=null, wait_text='', call_back_ok=function(mess){}, call_back_neg=function(mess){}, seconds_to_wait=15) {
    params['quest'] = com;
    let div = null;
    if(obj_for_shore !== null) {
        div = $('<div class="loader-gray"><h1>'+wait_text+'</h1><img width="60" height="60" src="./IMG/SYS/loader.gif"><div class="bar"></div></div>');
        $(obj_for_shore).addClass('relative');
        $(obj_for_shore).append(div);
    }
    SENDER('send_tele_access', params, function(mess){
        mess = JSON.parse(mess);
        if(mess.status === 'ok') {
            console.dir(mess);
            $(div).find('.bar').css('transition', seconds_to_wait+'s');
            setTimeout(function() {
                $(div).find('.bar').css('width', '0');
            }, 1);
            let prom = setInterval(function() {
                SENDER('scan_answer_result', {hesh: mess.params}, function (messer) {
                    console.log(messer);
                    if (messer === 'ok') {
                        say('Операция подтверждена.');
                        clearInterval(prom);
                        clearTimeout(auto_abort);
                        $('.loader-gray').remove();
                        call_back_ok(messer);
                    }
                    if (messer === 'neg') {
                        call_back_neg(messer);
                        $('.loader-gray').remove();
                        say('К сожалению Ваш запрос отклонён...', 2);
                        clearInterval(prom);
                        clearTimeout(auto_abort);
                    }
                });
            }, 3000);
            let auto_abort = setTimeout(function () {
                clearInterval(prom);
                clearTimeout(auto_abort);
                call_back_neg(mess);
                $('.loader-gray').remove();
                say('К сожалению Ваш запрос отклонён...', 2);
            }, seconds_to_wait * 1000);
        }
    }, function(mess){
        call_back_neg(mess);
    });
}
function rubber(obj) {
    $(obj).addClass('rubber');
    setTimeout(function() {
        $(obj).addClass('show');
        setTimeout(function() {
            $(obj).removeClass('rubber');
            $(obj).removeClass('show');
            $(obj).focus();
        }, 510);
    }, 5);
}


function EventRBS() {
    this.subscribers = [];
}
EventRBS.prototype.subscribe = function(fn) {
    this.subscribers.push(fn);
};
EventRBS.prototype.action = function(data) {
    for(let i = 0; i < this.subscribers.length; i++) {
        this.subscribers[i](data);
    }
};

function price_format(num) {
    let parts = num.toFixed(2).toString().split(".");
    let integerPart = parts[0];
    let decimalPart = parts[1];

    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, " ");

    return integerPart + "." + decimalPart;
}

function isEmpty(obj) {
    return Object.keys(obj).length === 0;
}

function from_brackets(txt) {
    const regex = /\((.*?)\)/;
    const match = regex.exec(txt);
    return match[1];
}
function from_sqare_brackets(txt) {
    const regex = /\[(.*?)\]/;
    const match = regex.exec(txt);
    return match[1];
}
function get_all_scripts() {
    let scripts = document.getElementsByTagName('script');
    let arr = [];
    for (let i = 0; i < scripts.length; i++) {
        let nm = scripts[i].src;
        if(nm !== '') {
            nm = nm.split('/');
            nm = nm[nm.length - 1];
            let twice = nm.split('?');
            if (twice.length > 1) {
                if (twice[0] !== '') {
                    arr.push(twice[0]);
                }
            } else {
                arr.push(nm);
            }
        }
    }
    return arr;
}
function isset_script(name_without_js) {
    name_without_js += '.js';
    for(let i of get_all_scripts()) {
        if(i === name_without_js) {
            return true;
        }
    }
    return false;
}
function include_js_script(name_without_js, call_back=function() {}) {
    let scripter = document.createElement('script');
    scripter.src = domain+'TEMPLATES/JS/'+name_without_js+'.js?'+rnd(1000000, 9999999);
    $('body').append(scripter);
    scripter.onload = function() {
        call_back();
    };
}
$(document).on('click', '.place-popap', function(e) {
    e.stopPropagation();
});
$(document).on('click', '.overlay-gray-2', function(e) {
    close_popup($(this).attr('data-overlay'))
});
function open_popup(popup_name, datas={}, call_back=function(mess_params) {}) {
    let div = setOverlayJust2();
    let place = $('<div class="place-popap"><div class="loader-cont flex column gap-10"><img width="100" height="100" src="./DOWNLOAD/20230609-201051_id-2-217564.gif"><div>Ожидайте. Документ структурируется...</div></div><button onclick="close_popup(\''+popup_name+'\')" class="closer popap-closer-btn"></button></div>');
    SENDER('get_popup', {name: popup_name, datas: datas}, function(mess) {
        mess_executer(mess, function(mess) {
            $(place).append(mess.params);
            $('.loader-cont').remove();
            call_back();
        });
    });
    $(div).append(place);
    $(div).attr('data-overlay', popup_name);
    setTimeout(function() {
        place.addClass('showed');
    }, 100);
}
function close_popup(popup_name) {
    let overl = $('.overlay-gray-2[data-overlay="'+popup_name+'"]');
    overl.find('.place-popap').removeClass('showed');
    $('body').removeClass('blocker');
    setTimeout(function() {
        overl.removeClass('opened');
        setTimeout(function() {
            overl.remove();
        }, 600);
    }, 150);
}
function set_param_in_address_row(param, argum) {
    let url = new URL(location.href);
    if(url.searchParams.has(param)) {
        url.searchParams.set(param, argum);
    } else {
        url.searchParams.append(param, argum);
    }
    history.pushState(null, '', url.href);
}
function get_param_from_address_row(param) {
    let url = new URL(location.href);
    return url.searchParams.get(param);
}
function del_param_from_address_row(param) {
    let url = new URL(location.href);
    url.searchParams.delete(param)
    history.pushState(null, '', url.href);
}
function isset_param_in_address_row(param) {
    let url = new URL(location.href);
    return url.searchParams.has(param);
}
function decode_phone(str_number) {
    return str_number.replace(/[^0-9+\-()]/g, '');
}
function get_extention(file_name) {
    let exp = file_name.split('.');
    exp = exp[exp.length - 1];
    exp = exp.toLowerCase();
    switch(exp) {
        case 'jpg':
        case 'jpeg':
        case 'webp':
        case 'png':
        case 'gif':
            return 'image';
        case 'mp3':
        case 'ogg':
        case 'wav':
        case 'ape':
        case 'flac':
            return 'audio';
        case 'mp4':
        case 'webm':
        case 'wmv':
        case 'avi':
            return 'video';
        case 'svg':
            return 'svg';
        case 'txt':
            return 'txt';
        case 'doc':
        case 'docx':
            return 'word';
        case 'xlsx':
        case 'xlsm':
            return 'tabs';
        case 'pdf':
            return 'pdf';
        default:
            return 'file';
    }
}