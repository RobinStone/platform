new_alert = new Event();function recalc_shop(obj) {    let count = 0;    let count_all = 0;    let price = 0;    let total_price = 0;    let price_count = 0    let discount = 0;    if($(obj).find('tr').length > 0) {        $(obj).find('tr').each(function (e, t) {            discount = parseInt($(t).attr('data-discount'));            count = parseInt($(t).find('.count-prods').text());            count_all += count;            price = parseFloat($(t).attr('data-price'));            if(discount > 0) {                price -= (price * discount)/100;            }            total_price += price * count;            $(t).find('.price').text(price_format(price * count) + ' P');        });        $(obj).find('footer .price').text(price_format(total_price) + ' P');        $(obj).find('footer .local-line span').text(count_all);    } else {        $(obj).remove();    }    setTimeout(function() {        let count = $('.basket-item').length;        $('#names-count').text(count);        $('#basket-count').text(count);    }, 100);}function operation(op, obj, max=-1) {    if(op === '+') {        obj = $(obj).parent().find('.count-prods');        let i = parseInt($(obj).text());        if(max !== -1) {            if(i+1 <= max) {                ++i;            }        } else {            ++i;        }        change_count_of_item($(obj).closest('.basket-item').attr('data-product-code'), i);        $(obj).text(i);    }    if(op === '-') {        obj = $(obj).parent().find('.count-prods');        let i = parseInt($(obj).text());        --i;        if(i < 1) {            i = 1;        }        change_count_of_item($(obj).closest('.basket-item').attr('data-product-code'), i);        $(obj).text(i);    }    recalc_shop($(obj).closest('li.shop-wrapper'));}function change_count_of_item(code_product, count) {    let arr = {};    arr[code_product] = count;    SENDER('change_count_item_in_basket', {arr: arr}, function(mess) {        mess_executer(mess, function(mess) {            console.dir(mess);        });    });}function remove_all_from_basket() {    info_qest(transform_pos('center'), function() {        let arr = [];        $('.basket-item').each(function(e,t) {            arr.push($(t).attr('data-product-code'));        });        remove_item_server_from_basket(arr, function() {            $('.basket').remove();            setTimeout(function() {                let count = $('.basket-item').length;                $('#names-count').text(count);                $('#basket-count').text(count);            }, 100);        });    }, function() {    }, 'Подвердите полную очистку корзины?', 'ДА - очистить всё', 'Отмена');}function remove_shop_from_basket(obj) {    info_qest(transform_pos('center'), function() {        let arr = [];        $(obj).find('tr.basket-item').each(function(e,t) {            arr.push($(t).attr('data-product-code'));        });        remove_item_server_from_basket(arr, function() {            $(obj).remove();            setTimeout(function() {                let count = $('.basket-item').length;                $('#names-count').text(count);                $('#basket-count').text(count);            }, 100);        });    }, function() {    }, 'Удалить все товары [ <span style="color: #018536; font-weight: 800; font-size: 20px">'+$(obj).find('.user-name').text()+'</span> ] из корзины?', 'ДА - удалить', 'Отмена');}function delete_item_from_basket(obj) {    info_qest(transform_pos('center'), function() {        let shop = $(obj).closest('li.shop-wrapper');        let code_product = $(obj).attr('data-product-code');        $(obj).remove();        setTimeout(function() {            remove_item_server_from_basket([code_product], function() {                recalc_shop(shop);            });        }, 10);    }, function() {    }, 'Удалить [ <span style="color: #018536; font-weight: 800; font-size: 20px">'+$(obj).find('.text-name .name').text()+'</span> ] из корзины?', 'ДА - удалить', 'Отмена');}function remove_item_server_from_basket(arr_of_code_prods, call_back=function(){}) {    let arr = {};    if(arr_of_code_prods.length > 0) {        for(let i in arr_of_code_prods) {            arr[arr_of_code_prods[i]] = 0;        }        SENDER('change_count_item_in_basket', {arr: arr}, function(mess) {            mess_executer(mess, function(mess) {                console.dir(mess);                call_back();            });        });    }}