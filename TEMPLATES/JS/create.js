buffer_app = 'SHOPS';window.onload = function() {    place_to_indicators_container_tag = '.loader-img-form';    final_loading_ok = function (mess) {        try {            append_image_on_panel(mess.sys_name);        } catch (e) {            console.log('На этой странице обработчик не работает');        }    };    set_accept_types_loading('image');}setInterval(function() {    let count = $('.list-prev-img img').length;    $('#count-imgs').text('Загружено '+count+' из 5');    if(count >= 5) {        $('.loader-img-form').addClass('disabled');    } else {        $('.loader-img-form').removeClass('disabled');    }}, 1000);setTimeout(function() {    $('.product-creator table tr[data-field="input-object-undercat"]').addClass('disabled');    $('.product-creator table tr[data-field="input-object-actionlist"]').addClass('disabled');}, 10)$(document).on('input', '.product-creator tr td:nth-child(2) *', function(e) {    $(this).closest('tr').attr('edited-old', 'true');});function append_image_on_panel(image_sys_name) {    let btn = $('<button onclick="del_img(this)" data-name="'+image_sys_name+'" class="img-btn action-btn" title="Удалить изображение"></button>');    let image = $('<img src="/IMG/img300x300/'+image_sys_name+'">')    $(btn).append(image);    $('.list-prev-img').append(btn);    $('.product-creator tr[data-type="image"]').closest('tr').attr('data-changed', true);    changer();}function load_new_img() {    let imgs = this;    let count_imgs = $('.list-prev-img button').length;    if(count_imgs === 5) {        say('Разрешено добавлять только 5 изображений.', 2);        return false    }    final_loading_ok = function(mess) {        try {            append_image_on_panel(mess.sys_name);        } catch (e) {            console.log('На этой странице обработчик не работает');        }    };    set_accept_types_loading('image');    $('#general-input-file').click();}function change_shop(obj) {    buffer_app = 'SHOPS';    let id = $(obj).find('option:selected').attr('data-id');    SENDER_APP('set_add_shop', {id: id}, function(mess) {        mess = JSON.parse(mess);        if(mess.status === 'ok') {            location.reload();        } else {            error_executing(mess);        }    });}function buffer_method_start() {    location.href = '/profil?title=my_orders&filter=not_show';}$(document).on('click', '.cat-btn', function(e) {    let obj = $(this);    switch(obj.attr('data-type')) {        case 'main':            BACK('category_finder', 'get_under_all', {main_cat_id: obj.attr('data-main')}, function (mess) {                mess_executer(mess, function (mess) {                    update_cat_btns(mess);                });            });            break;        case 'under':            BACK('category_finder', 'get_list_all', {                under_cat_id: obj.attr('data-under'),                main_cat_id: obj.attr('data-main')            }, function (mess) {                mess_executer(mess, function (mess) {                    if(mess.body.length > 0) {                        update_cat_btns(mess);                    } else {                        address_create(obj);                    }                });            });            break;        case 'list':            address_create(obj);            break;    }});let cat_buttons_cont = $('.cat-buttons');let finder_cat_buttons_cont = $('.finder-cat-buttons');function analised_catagorys(obj) {    let txt = $(obj).val();    if(txt.length >= 2) {        cat_buttons_cont.addClass('invisible');        finder_cat_buttons_cont.removeClass('invisible');        BACK('category_finder', 'scan', {txt: txt}, function (mess) {            mess_executer(mess, function (mess) {                update_cat_btns(mess);            });        });    } else {        cat_buttons_cont.removeClass('invisible');        finder_cat_buttons_cont.addClass('invisible');    }}function update_cat_btns(mess) {    if(cat_buttons_cont.length === 0) {        cat_buttons_cont = $('.cat-buttons');        finder_cat_buttons_cont = $('.finder-cat-buttons');    }    console.dir(mess.body);    cat_buttons_cont.addClass('invisible');    finder_cat_buttons_cont.removeClass('invisible');    finder_cat_buttons_cont.empty();    for(let i in mess.body) {        let btn = mess.body[i];        let btn_body = $('<button data-type="'+btn['type']+'" class="cat-btn special-btn btn-just">'+btn['name']+'</button>');        if(btn['type'] === 'list') {            btn_body.append('<div><span class="btn-under-text">'+btn['under_name']+'</span></div>');            btn_body.attr('data-under', btn['under_id']);            btn_body.attr('data-main', btn['main_id']);            btn_body.attr('data-list', btn['id']);        }        if(btn['type'] === 'under') {            btn_body.append('<div><span class="btn-under-text">'+btn['main_name']+'</span></div>');            btn_body.attr('data-main', btn['main_id']);            btn_body.attr('data-under', btn['id']);        }        if(btn['type'] === 'main') {            btn_body.attr('data-main', btn['id']);        }        finder_cat_buttons_cont.append(btn_body);    }}function address_create(obj) {    const currentUrl = window.location.href;    const newUrl = currentUrl.split('?')[0];    window.history.replaceState(null, '', newUrl);    if(typeof $(obj).attr('data-main') !== 'undefined') {        url_loc.searchParams.set('main', $(obj).attr('data-main'));    }    if(typeof $(obj).attr('data-under') !== 'undefined') {        url_loc.searchParams.set('under', $(obj).attr('data-under'));    }    if(typeof $(obj).attr('data-list') !== 'undefined') {        url_loc.searchParams.set('list', $(obj).attr('data-list'));    }    window.history.replaceState(null, '', url_loc.toString());    location.reload();}