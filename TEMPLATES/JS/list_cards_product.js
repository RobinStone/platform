$(document).on('click', '.products-list li button.like', function(e) {    e.stopPropagation();    e.preventDefault();});$(document).on('click', '.products-list li button.basket', function(e) {    e.stopPropagation();    e.preventDefault();});$(document).on('click', '.products-list li', function(e) {    location.href = $(this).find('a').attr('href');});function favorite_check(obj, id_shop, id_product) {    buffer_app = 'SHOPS';    SENDER_APP('add_rem_favorite', {id_shop: id_shop, id_product: id_product}, function(mess) {        mess_executer(mess, function(mess) {            if(mess.params === true) {                $(obj).find('span.svg-wrapper').addClass('hart-red');                on_hart(obj);            } else {                $(obj).find('span.svg-wrapper').removeClass('hart-red');                off_hart(obj);            }        });    });}function on_hart(obj) {    let hart = $('<img class="hart inline-block" width="30" height="30" src="/DOWNLOAD/20230824-171803_id-2-114735.svg">');    $(obj).append(hart);    setTimeout(function() {        $(hart).addClass('min-hart');        setTimeout(function() {            $(hart).remove();        }, 500);    }, 20);}function off_hart(obj) {    let hart = $('<img class="hart-2 inline-block" width="30" height="30" src="/DOWNLOAD/20230824-171803_id-2-114735.svg">');    $(obj).append(hart);    setTimeout(function() {        $(hart).addClass('max-hart');        setTimeout(function() {            $(hart).remove();        }, 500);    }, 20);}function in_basket(code_product, obj) {    SENDER('in_basket', {code: code_product}, function(mess) {        mess_executer(mess, function(mess) {            console.dir(mess);            if(mess.params === '+') {                basket_set('+');                $(obj).html('<img width="20" height="20" src="/DOWNLOAD/20231007-181635_id-2-704258.svg">');            }            if(mess.params === '-') {                basket_set('-');                $(obj).html('<img width="20" height="20" src="/DOWNLOAD/20230609-193649_id-2-923913.svg">');            }        });    });}let access_for_next_cards = true;/** * Показывает следующую порцию карточек с учётом уже показанных * @param count_cards * @param params * @param isset_ids_arr */function show_next_pagination_cards(count_cards, params={}, isset_ids_arr=[]) {    if(access_for_next_cards) {        access_for_next_cards = false;        let arr = $('li[data-indexer]').map(function () {            return parseInt($(this).attr('data-indexer'));        }).toArray();        console.dir(arr);        if(isset_ids_arr.length === 0) {            isset_ids_arr = arr;        }        params['count'] = count_cards;        params['isset'] = isset_ids_arr;        if(typeof city_index !== 'undefined') {            params['city_index'] = city_index;        } else {            params['city_index'] = -1;        }        buffer_app = 'SHOPS';        SENDER_APP('get_next_cards', params, function(mess) {            mess_executer(mess, function(mess) {                $('.products-list ul').append(mess.params.list);                if(mess.params.count > 0) {                    access_for_next_cards = true;                }            })        });    }}