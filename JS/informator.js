cursorPos = {    x:0,    y:0};$(document).on('keydown', function(e) {    if(e.keyCode == 27) {        $('.info-panel .close-map-btn').click();    }});$(document).on('keydown', '.close-map-btn', function(e) {    e.preventDefault();    e.stopPropagation();    closeMessage(this);});$(document).on('input', '.input-phone', function(e) {    let obj = this;    let val = $(obj).val();    let pattern = /^[0-9()+-\s]*$/;    let isValid = pattern.test(val);    if (!isValid) {        $(obj).addClass('invalid');    } else {        $(obj).removeClass('invalid');    }});$(document).ready(function(){    // Координаты курсора относительно всего документа    $(document).mousemove(function(event){        cursorPos = {            x:event.pageX,            y:event.pageY-$(this).scrollTop()        };    });});$(document).on('click', '.minimaze-btn', function(e) {    let obj = $(this).closest('.window');    let img = $(obj).find('h4 img').attr('src');    $(obj).addClass('minimaze-mode');    let table_name = $(obj).find('.table-db').attr('data-name');    let table_title = $(obj).find('.table-db').attr('data-title');    let btn = $('<button title="'+table_title+' ('+table_name+')" data-min-table="'+table_name+'" data-min-title="'+table_title+'" class="minimaze-btn-ico"><img src="'+img+'"></button>');    $('.ico-panel').append(btn);    setTimeout(function() {        $(btn).addClass('show-btn');        let tbtn_ = $(btn).get(0);        tbtn_.addEventListener("click", function(e){            e.preventDefault();            $(obj).removeClass('minimaze-mode');            $(tbtn_).remove();            setTimeout(function() {                mem.save();            }, 200);        }, false);    }, 100);    e.preventDefault();    e.stopPropagation()});function info_qest($pos=undefined, calBackMethodOk=null, calBackMethodNeg=null, $qestText="Подтвердите ваши действия?", $btnTextOk="Да", $btnTextNeg="Нет") {    if($pos === undefined) {        $pos = cursorPos;    }    let nameBtn = Math.random().toString().slice(2);    nameBtn = 'btn_'+nameBtn;    let pnl = $('<div class="info-qest info-panel" style="display: flex; flex-direction: column; transition: 0.2s; z-index: 99999; background-color: #bbc8ff; transform: translate(-50%, -50%) scale(0); min-width: 150px; min-height: 80px; left: '+$pos.x+'px; top: '+$pos.y+'px; position: fixed; border-radius: 10px; box-shadow: 5px 5px 10px 5px rgba(0,0,0,0.57)"></div>');    $(pnl).append('<h4 style="font-weight: 100; color: #000; justify-self: flex-start; border-top-left-radius: 10px; border-top-right-radius: 10px; margin: 0; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$qestText+'<button onclick="closeMessage(this)" class="close-map-btn" style="width: 12px; height: 12px; margin-top: 4px; margin-right: 4px; "></button></h4>');    let btn = $('<button id="'+nameBtn+'" class="presser" style="margin: 15px auto; width: fit-content; padding: 5px 10px; border: none; box-shadow: 1px 1px 4px 0 rgba(0,0,0,0.34); outline: none; border-radius: 7px; cursor: pointer;">'+$btnTextOk+'</button>');    $(document).on('click', '#'+nameBtn, calBackMethodOk);    let btnPnl = $('<div style="display: flex; width: 100%; justify-content: center; align-items: center; margin: 10px;"></div>');    $(btnPnl).append(btn);    if(calBackMethodNeg !== null) {        let btn2 = $('<button id="'+nameBtn+'2" class="presser" style="margin: 15px auto; width: fit-content; padding: 5px 10px; border: none; box-shadow: 1px 1px 4px 0 rgba(0,0,0,0.34); outline: none; border-radius: 7px; cursor: pointer;">'+$btnTextNeg+'</button>');        $(document).on('click', '#'+nameBtn+'2', calBackMethodNeg);        $(btnPnl).append(btn2);    }    $(pnl).append(btnPnl);    $('body').append(pnl);    dragElement(pnl);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(1)');    }, 100);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(0)');        setTimeout(function() {            $(pnl).remove();        }, 200);    }, 100000);    return pnl;}function info_info($pos=undefined, $html_text="bla-bla", $header="Сообщение", $btnTextOk="Ok", img='') {    if($pos === undefined) {        $pos = cursorPos;    }    let image = '';    if(img !== '') {        image = '<img width="150" height="150" src="/IMG/img300x300/'+img+'" style="float:left; display: inline-block; margin: 0 10px 0 0">';    }    let nameBtn = Math.random().toString().slice(2);    nameBtn = 'btn_'+nameBtn;    let pnl = $('<div class="info-info info-panel" style="display: flex; flex-direction: column; transition: 0.2s; z-index: 99999; background-color: #bbc8ff; transform: translate(-50%, -50%) scale(0); min-width: 150px; min-height: 80px; left: '+$pos.x+'px; top: '+$pos.y+'px; position: fixed; border-radius: 10px; box-shadow: 5px 5px 10px 5px rgba(0,0,0,0.57)"></div>');    $(pnl).append('<h4 style="font-weight: 100; color: #000; justify-self: flex-start; border-top-left-radius: 10px; border-top-right-radius: 10px; margin: 0; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$header+'<button onclick="closeMessage(this)" class="close-map-btn" style="width: 12px; height: 12px; margin-top: 1px; margin-right: 4px; "></button></h4>');    pnl.append('<div class="info-content">'+image+$html_text+'</div>');    $('body').append(pnl);    dragElement(pnl);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(1)');    }, 100);    return pnl;}function info_sellect($pos=undefined, id_list_of_datalist, calBackMethod=undefined, $qestText="Введите значение:", $defaultValue='') {    if($pos === undefined) {        $pos = cursorPos;    }    let nameBtn = Math.random().toString().slice(2);    nameBtn = 'btn_'+nameBtn;    let pnl = $('<div class="info-sellect info-panel"  style="display: flex; flex-direction: column; transition: 0.2s; z-index: 20; background-color: #bbc8ff; transform: translate(-50%, -50%) scale(0); min-width: 150px; min-height: 80px; left: '+$pos.x+'px; top: '+$pos.y+'px; position: fixed; border-radius: 10px; box-shadow: 5px 5px 10px 5px rgba(0,0,0,0.57)"></div>');    $(pnl).append('<h4 style="font-weight: 100; color: #000; justify-self: flex-start; border-top-left-radius: 10px; border-top-right-radius: 10px; margin: 0; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$qestText+'<button onclick="closeMessage(this)" class="close-map-btn" style="width: 12px; height: 12px; margin-top: 4px; margin-right: 4px; "></button></h4>');    $(pnl).append('<input onchange="return sellectChanged(\'#inpt_'+nameBtn+'\', this)" id="inpt_'+nameBtn+'" list="'+id_list_of_datalist+'" type="text" value="'+$defaultValue+'" style="margin: 5px 5px 0; border-radius: 10px; font-weight: 100; color: #000; justify-self: flex-start; padding: 5px 0 5px 10px; background-color: #ffffff;">');    $(document).on('change', '#inpt_'+nameBtn, calBackMethod);    $('body').append(pnl);    dragElement(pnl);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(1)');        $('#inpt_'+nameBtn).select();    }, 100);}function info_inputString($pos=undefined, calBackMethod=undefined, $qestText="Введите значение:", $defaultValue='', $btnTextOk="Ок") {    if($pos === undefined) {        $pos = cursorPos;    }    let nameBtn = Math.random().toString().slice(2);    nameBtn = 'btn_'+nameBtn;    let pnl = $('<div class="info-inputString info-panel"  style="display: flex; flex-direction: column; transition: 0.2s; z-index: 20; background-color: #bbc8ff; transform: translate(-50%, -50%) scale(0); min-width: 150px; min-height: 80px; left: '+$pos.x+'px; top: '+$pos.y+'px; position: fixed; border-radius: 10px; box-shadow: 5px 5px 10px 5px rgba(0,0,0,0.57)"></div>');    $(pnl).append('<h4 style="font-weight: 100; color: #000; justify-self: flex-start; border-top-left-radius: 10px; border-top-right-radius: 10px; margin: 0; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$qestText+'<button onclick="closeMessage(this)" class="close-map-btn" style="width: 12px; height: 12px; margin-top: 4px; margin-right: 4px; "></button></h4>');    $(pnl).append('<input id="inpt_'+nameBtn+'" type="text" value="'+$defaultValue+'" style="margin: 5px 5px 0; border-radius: 10px; font-weight: 100; color: #000; justify-self: flex-start; padding: 5px 30px 5px 10px; background-color: #ffffff;">');    let btn = $('<button onclick="return sellectTextFromInput(\'#inpt_'+nameBtn+'\')" id="'+nameBtn+'" class="presser" style="align-self: center; margin: 15px auto!important; width: fit-content; padding: 5px 10px; border: none; box-shadow: 1px 1px 4px 0 rgba(0,0,0,0.34); outline: none; border-radius: 7px; cursor: pointer;">'+$btnTextOk+'</button>');    $(document).on('click', '#'+nameBtn, calBackMethod);    $(document).on('keydown', '#inpt_'+nameBtn, function(e) {        if(e.keyCode === 13) {            $('#'+nameBtn).click();        }    });    $(pnl).append(btn);    $('body').append(pnl);    dragElement(pnl);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(1)');        $('#inpt_'+nameBtn).select();    }, 100);}function info_inputText($pos=undefined, calBackMethod=undefined, $qestText="Введите значение:", $defaultValue='', $btnTextOk="Ок") {    if($pos === undefined) {        $pos = cursorPos;    }    let nameBtn = Math.random().toString().slice(2);    nameBtn = 'btn_'+nameBtn;    let pnl = $('<div class="info-panel" style="display: flex; flex-direction: column; transition: 0.2s; z-index: 20; background-color: #bbc8ff; transform: translate(-50%, -50%) scale(0); min-width: 150px; min-height: 80px; left: '+$pos.x+'px; top: '+$pos.y+'px; position: fixed; border-radius: 10px; box-shadow: 5px 5px 10px 5px rgba(0,0,0,0.57)"></div>');    $(pnl).append('<h4 style="cursor:move; font-weight: 100; color: #000; justify-self: flex-start; border-top-left-radius: 10px; border-top-right-radius: 10px; margin: 0; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$qestText+'<button onclick="closeMessage(this)" class="close-map-btn" style="width: 12px; height: 12px; margin-top: 4px; margin-right: 4px; "></button></h4>');    $(pnl).append('<textarea id="inpt_'+nameBtn+'" type="text" style="max-width: 100%; width: 300px; height: 170px;  margin: 5px 5px 0; border-radius: 10px; font-weight: 100; color: #000; justify-self: flex-start; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$defaultValue+'</textarea>');    let btn = $('<button onclick="return sellectTextFromInput(\'#inpt_'+nameBtn+'\')" id="'+nameBtn+'" class="presser" style="margin: 15px auto; width: fit-content; padding: 5px 10px; border: none; box-shadow: 1px 1px 4px 0 rgba(0,0,0,0.34); outline: none; border-radius: 7px; cursor: pointer;">'+$btnTextOk+'</button>');    $(document).on('click', '#'+nameBtn, calBackMethod);    $(pnl).append(btn);    dragElement(pnl);    $('body').append(pnl);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(1)');        $('#inpt_'+nameBtn).select();    }, 100);    return pnl;}function sellectTextFromInput(obj) {    bufferText = $(obj).val();    return true;}function sellectChanged(obj) {    bufferText = $(obj).val();    let obj3 = $(obj).parent();    $(obj3).css('transform', 'translate(-50%, -50%) scale(0)');    setTimeout(function() {        $(obj3).remove();    }, 200);    return true;}$(document).on('mouseenter', '.button-timer-activator', function(e) {    $(this).addClass('closer-if-leave');});$(document).on('mouseleave', '.closer-if-leave', function(e) {    $(this).find('h4 button').click();});function all_menu_panels_close() {    $('.menu-info-panel').each(function(e,t) {        $(t).find('h4 button').click();    })}function info_variants($pos=undefined, listOfActions={}, $qestText="Выберите из предложенных вариантов:", add_class="", is_menu=false) {    if($pos === undefined) {        if(is_menu) {            $pos = {                x: cursorPos.x-100,                y: cursorPos.y            };        } else {            $pos = cursorPos;        }    }    let pnl = $('<div class="info-variants info-panel '+add_class+'"  style="max-height: 500px; max-width: 100vw; width: 300px; overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column; transition: 0.2s; z-index: 20; background-color: #bbc8ff; transform: translate(-50%, -50%) scale(0); min-width: 150px; min-height: 80px; left: '+$pos.x+'px; top: '+$pos.y+'px; position: fixed; padding: 0 5px 5px 5px; border-radius: 10px; box-shadow: 5px 5px 10px 5px rgba(0,0,0,0.57)"></div>');    if(is_menu) {        pnl.addClass('button-timer-activator not-autoclose');        $('.closer-if-leave').removeClass('closer-if-leave');        pnl.addClass('closer-if-leave');    }    $(pnl).append('<h4 style="font-weight: 100; color: #000; justify-self: flex-start; border-top-left-radius: 10px; border-top-right-radius: 10px; margin: 0 -6px 5px -6px; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$qestText+'<button onclick="closeMessage(this)" class="close-map-btn" style="width: 12px; height: 12px; margin-top: 4px; margin-right: 4px; "></button></h4>');    let lst = $('<div style="display: flex; flex-direction: column; max-height: 70vh; overflow-y: auto" class="scroll-list"></div>');        for (let key in listOfActions) {            let nameBtn = 'btn_' + Math.random().toString().slice(2);            let btn = $('<button id="' + nameBtn + '" class="presser" style="align-self: center; margin: 2px auto; width: fit-content; padding: 7px 10px; border: none; box-shadow: 1px 1px 4px 0 rgba(0,0,0,0.34); outline: none; border-radius: 7px; cursor: pointer;">' + key + '</button>');            if(is_menu) {                $(document).on('click', '#' + nameBtn, function() {                    listOfActions[key].click();                    clearTimeout(timer_lock2);                });                $(document).on('mouseenter', '#' + nameBtn, function(e) {                    timer_lock2 = setTimeout(function() {                        listOfActions[key].hover();                    }, 800);                }).on('mouseleave', '#' + nameBtn, function() {                    clearTimeout(timer_lock2);                }).on('mousemove', '#' + nameBtn, function() {                    clearTimeout(timer_lock2)                    timer_lock2 = setTimeout(function() {                        listOfActions[key].hover();                    }, 400);                });;            } else {                $(document).on('click', '#' + nameBtn, listOfActions[key]);            }            $(lst).append(btn);        }    $(pnl).append(lst);    $('body').append(pnl);    dragElement(pnl);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(1)');    }, 100);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(0)');        setTimeout(function() {            $(pnl).remove();        }, 200);    }, 100000);    return pnl;}function info_list_editor($pos=undefined, llist={}, on_ok=function(){}, $qestText="Отредактируйте список:", edited=true, add_class="") {    if($pos === undefined) {        $pos = cursorPos;    }    let pnl = $('<div class="info-llists info-panel '+add_class+'"  style="max-height: 500px; max-width: 100vw; width: 300px; overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column; transition: 0.2s; z-index: 20; background-color: #bbc8ff; transform: translate(-50%, -50%) scale(0); min-width: 150px; min-height: 80px; left: '+$pos.x+'px; top: '+$pos.y+'px; position: fixed; padding: 0 5px 5px 5px; border-radius: 10px; box-shadow: 5px 5px 10px 5px rgba(0,0,0,0.57)"></div>');    $(pnl).append('<h4 style="font-weight: 100; color: #000; justify-self: flex-start; border-top-left-radius: 10px; border-top-right-radius: 10px; margin: 0 -6px 5px -6px; padding: 5px 30px 5px 10px; background-color: #ffffff;">'+$qestText+'<button onclick="closeMessage(this)" class="close-map-btn" style="width: 12px; height: 12px; margin-top: 4px; margin-right: 4px; "></button></h4>');    let lst = $('<div style="display: flex; flex-direction: column; max-height: 70vh; overflow-y: auto" class="scroll-list"></div>');    let tbl = $('<table></table>');    for(let i in llist) {            let del_btn = '';            if(edited) {                del_btn = '<td><button class="btn-gray" style="border-radius: 50%; font-weight: 800; color: red" onclick="$(this).closest(\'tr\').remove()">✕</button></td>';            }            let tr = $('<tr><td><input value="'+llist[i]+'"></td>'+del_btn+'</tr>');            tbl.append(tr);        }    $(lst).append(tbl);    $(pnl).append(lst);    let pnl_btns = $('<div style="display: flex; gap: 15px; padding: 4px 7px; justify-content: right"></div>');    let save_button = $('<button class="action-btn">Сохранить</button>');    let add_button1 = '';    let add_button2 = '';    if(edited) {        add_button1 = $('<button class="action-btn">✚нач</button>');        add_button1.on('click', function() {            tbl.prepend('<tr><td><input value=""></td><td><button class="btn-gray" style="border-radius: 50%; font-weight: 800; color: red" onclick="$(this).closest(\'tr\').remove()">✕</button></td></tr>');        });        add_button2 = $('<button class="action-btn">✚кон</button>');        add_button2.on('click', function() {            tbl.append('<tr><td><input value=""></td><td><button class="btn-gray" style="border-radius: 50%; font-weight: 800; color: red" onclick="$(this).closest(\'tr\').remove()">✕</button></td></tr>');        });    }    save_button.on('click', function() {        let ans = [];        tbl.find('tr').each(function(e,t) {            ans.push($(t).find('input').val());        });        on_ok(ans); // Вызываем переданную функцию        pnl.find('h4 button').click();    });    if(edited) {        pnl_btns.append(add_button1);        pnl_btns.append(add_button2);    }    pnl_btns.append(save_button);    $(pnl).append(pnl_btns);    $('body').append(pnl);    dragElement(pnl);    setTimeout(function() {        $(pnl).css('transform', 'translate(-50%, -50%) scale(1)');    }, 100);    return pnl;}$(document).on('click', '.presser', function() {    if($(this).closest('.not-autoclose').length > 0) {    } else {        let obj3 = $(this).parent();        if (!$(obj3).hasClass('info-panel')) {            obj3 = $(this).parent().parent();        }        $(obj3).css('transform', 'translate(-50%, -50%) scale(0)');        setTimeout(function () {            $(obj3).remove();        }, 200);    }});function closeMessage(obj) {    let obj2 = $(obj).parent().parent();    $(obj2).css('transform', 'translate(-50%, -50%) scale(0)');    setTimeout(function() {        $(obj2).remove();    }, 200);}function create_window($pos=undefined, title='Новое окно', after_create=function(self_form){}, add_class='') {    if($pos === undefined) {        $pos = cursorPos;    }    let div = $('<div class="window '+add_class+'" style="left: '+$pos.x+'px; top: '+$pos.y+'px"><h4><b>'+title+'</b><button class="minimaze-btn up-row-window-btn" style="right: 27px; top: 11px" onclick="minimize_window($(this).parent().parent())">▁</button><button onclick="close_window($(this).parent().parent())" class="close-window-btn"></button></h4><div style="position: relative" class="content"></div><span class="resizer"></span></div>');    if($('#body').length > 0) {        $('#body').append(div);    } else if($('#body-s').length > 0) {        $('#body-s').append(div);    } else {        say('Не найдены родительские элементы платформы.', 3);    }    dragElement(div);    set_focus(div);after_create(div)    return $(div).find('.content');}bw = null;function set_focus(obj) {    $('.table-focus').removeClass('table-focus');    if($(obj).hasClass('window')) {        $(obj).addClass('table-focus');    } else {        obj = $(obj).closest('.window');        $(obj).addClass('table-focus');    }}$(document).on('mousedown', '.resizer', function(e) {    let obj = this;    set_focus(obj);    let rect = obj.parentNode.getBoundingClientRect();    $(obj).parent().addClass('unselect');    bw = {        obj: $(obj).parent(),        wrp: $(obj).parent().find('.table-wrapper'),        w: rect.width,        h: rect.height,        x: cursorPos.x,        y: cursorPos.y,        pnl_x: rect.left+(rect.width/2),        pnl_y: rect.top+(rect.height/2),    }    $(bw.obj).addClass('no-transition');});$(document).on('mouseup', '#body, #body-s', function(e) {    if(bw !== null) {        $(bw.obj).removeClass('no-transition');        $(bw.obj).removeClass('unselect');        bw = null;        mem.save();    }});$(document).on('mousemove', '#body, #body-s', function(e) {    if(bw !== null) {        let hh = bw.h+((cursorPos.y-bw.y));        let ww = bw.w+((cursorPos.x-bw.x));        let new_x = bw.pnl_x+((cursorPos.x-bw.x)/2);        let new_y = bw.pnl_y+((cursorPos.y-bw.y)/2);        if(ww < 200) {            ww = 200;        }        $(bw.obj).css('width', ww+'px').css('height', hh+'px').css('left', new_x+'px').css('top', new_y+'px');        if(hh >= 115) {            $(bw.wrp).css('max-height', (hh - 50) + 'px');        }    }});function close_window(obj) {    $(obj).addClass('slow-minimizer');    setTimeout(function() {        $(obj).remove();        mem.save();    }, 500);}function sys(info) {    let win = create_window(undefined, 'Системное окно');    $(win).html('<pre>'+info+'</pre>');}// ============================================================================================// DRAGABLE INFO_PANELS// ============================================================================================interp = {    x:0,    y:0};transObj = null;function border_corector(obj) {    // let win_height = window.screen.availHeight;    let win_height = screen.height;    setTimeout(function() {        try {            let rect = 0;            if($('#body-s').length > 0 ) {                rect = document.querySelector('#body-s').getBoundingClientRect();            }            if($('#body').length > 0 ) {                rect = document.querySelector('#body').getBoundingClientRect();            }            let win = $(obj).get(0).getBoundingClientRect();            let r = {                x: win.left,                y: win.top,                width: win.width,                height: win.height,            };            if (r.y < 10) {                $(obj).offset({top: 10, left: r.x});                r.y = 10;            }            if (r.x + r.width > rect.width - 10) {                $(obj).offset({top: r.y, left: rect.width - 10 - r.width});                r.x = rect.width - 10 - r.width;            }            if (r.y + r.height > win_height - 10) {                console.log('MAX-HEIGHT '+(r.y + r.height)+' > '+ (win_height - 10));                $(obj).css('left', (r.x+r.width/2)+'px').css('top', (win_height - r.height-130+r.height/2)+'px');            }            if (r.x < 10) {                $(obj).css('left', (10 + (r.width/2))+'px').css('top', (r.y+(r.height/2))+'px');                r.x = 10;            }        } catch (e) {        }    }, 500);}function dragElement(obj) {    let m_obj = $(obj).find('h4, .drag-field');    let closer = $(obj).find('h4 .close-window-btn');    if(closer === undefined) {        closer = $(obj).find('h4 .close-map-btn');    }    if(closer !== undefined) {        $(closer).mousedown(function (e) {            e.stopPropagation();        });    }    if(m_obj !== undefined) {        $(m_obj).mousedown(function(e) {            set_focus(m_obj);            $(m_obj).addClass('drag-on');            e.preventDefault();            e.stopPropagation();            interp = {                x:e.offsetX-($(obj).width()/2),                y:e.offsetY-($(obj).height()/2)            };            transObj = obj;            $(obj).css('transition', 'none');            $(obj).css('z-index', '999999');        });        $(document).mousemove(function(event){            if(transObj !== null) {                let rect = $(transObj).get(0).getBoundingClientRect();                let new_x = cursorPos.x-interp.x;                let new_y = cursorPos.y-interp.y;                if(new_x-(rect.width/2) > 0 && new_x+(rect.width/2) < window.innerWidth) {                    $(transObj).css('left', (new_x)+'px');                }                if(new_y-(rect.height/2) > 0 && new_y+(rect.height/2) < window.innerHeight) {                    $(transObj).css('top', (new_y) + 'px');                }            }        });        $(document).mouseup(function(e) {            $(obj).find('h4').removeClass('drag-on');            $(transObj).css('transition', '0.2s');            if(!$(transObj).hasClass('chat-m-wrapper')) {                $(obj).css('z-index', '99999');            }            try{                if(transObj !== null) {                    mem.save();                }            } catch (e) {            }            transObj = null;        });    }    border_corector(obj);}function transform_pos(pos=undefined, offset={x:0, y:0}) {    let ans = pos;    let x = window.innerWidth/2;    let y = window.innerHeight/2;    try {        ans = ans.split('-');    } catch (e) {        ans = 'undefined'    }    if(typeof ans === 'object' && ans.length === 2) {        switch(ans[0]) {            case 'left': x=5; break;            case 'right': x=window.innerWidth-5; break;        }        switch(ans[1]) {            case 'top': y=5; break;            case 'bottom': y=window.innerHeight-5; break;        }    } else {        switch(ans[0]) {            case 'top': y=5; break;            case 'right': x = window.innerWidth-5; break;            case 'bottom': y = window.innerHeight-5; break;            case 'left': x=5; break;            case 'undefined':                x = cursorPos.x;                y = cursorPos.y;                break;        }    }    ans = {        x: x+offset.x,        y: y+offset.y    };    return ans;}function minimize_window(obj) {        // ищи в $(document).on('clic}function update_alerts() {    loadTemplateIn('alerts', 'alerts', {}, function(mess) {    })}console.log('informator.js - is loaded');