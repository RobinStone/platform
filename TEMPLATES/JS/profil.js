let url = new URL(location.href);

window.addEventListener('load', function() {
    console.log('Before window included...');
    if(url.searchParams.get('title') !== null) {
        setTimeout(function() {
            open_tab(url.searchParams.get('title'));
        }, 100);
    }
});

$(document).on('click', '.wrapper-input input', function(e) {
    if($(this).val() !== '') {
        $(this).val('');
    }
});
$(document).on('click', '.option label', function(e) {
    let obj = $(this);
    if(obj.find('input[type="checkbox"]').length > 0) {
        let ch = !obj.find('input[type="checkbox"]').is(':checked');
        obj.find('input[type="checkbox"]').attr('checked', ch);
        PROFIL.set('only_my_city', ch);
    }
    e.stopPropagation();
    e.preventDefault();
});

$(document).on('dblclick', '.one-order', function(e) {
    let obj = this;
    info_qest(undefined, function(e) {
        SENDER_APP('del_item', {shop: $(obj).attr('data-id-shop'), item: $(obj).attr('data-id')}, function(mess) {
            mess_executer(mess,function(mess) {
                location.reload();
            });
        })
    }, function(e) {

    }, 'Подтвердите безвозвратное удаление продукта?');
});

$(document).on('change', '.check-ordr input', function(e) {
    let stat = $(this).prop('checked');
    let obj = this;
    if(stat) {
        $(obj).parent().append('<img class="ok-check" width="40" height="40" src="/IMG/SYS/ok_ckeck.gif?'+rnd(10000,99999)+'">')
    } else {
        $(obj).parent().find('.ok-check').remove();
    }
    setTimeout(scan_count_check, 10);
});

$(document).on('click', '.products-container', function(e) {
    let obj = this;
    if(!$(this).prop('open') === true) {
        let list_id = $(obj).find('.objects-list').attr('data-list-id');
        add_loader($(obj).find('.objects-list'));
        SENDER_APP('get_products_list', {lst: list_id, render_type: 'line-card'}, function(mess) {
            mess = JSON.parse(mess);
            if(mess.status !== 'ok') {
                error_executing(mess);
            } else {
                $(obj).find('.objects-list').html(mess.params);
            }
        });
    }
});

function scan_count_check() {
    let count = $('.ok-check').length;
    $('.publish-btn').each(function(e,t) {
        if(count > 1) {
            $(t).text('Опубликовать ('+count+')');
        } else {
            $(t).text('Опубликовать');
        }
    });
    $('.higger-btn').each(function(e,t) {
        if(count > 1) {
            $(t).text('Продвинуть ('+count+')');
        } else {
            $(t).text('Продвижение');
        }
    });
    $('.archive-btn').each(function(e,t) {
        if(count > 1) {
            $(t).text('Архивировать ('+count+')');
        } else {
            $(t).text('Архивировать');
        }
    });
}

function open_tab(name, params={}, call_back=function() {}) {
    url.searchParams.set('title', name);
    let new_url = 'https://'+url.hostname+url.pathname+'?'+url.searchParams;
    history.pushState(null, null, new_url);
    switch(name) {
        case 'rate':
        case 'bank':
        case 'account':
        case 'my_shops':
        case 'subscribes':
        case 'subscribes_mans':
        case 'messenger':
        case 'storage':
        case 'banners':
        case 'purchases': // покупки
        case 'shop_orders': // мои продажи (контроль)
        case 'settings':
        case 'favorite':
            add_loader($('.column-right'));
            buffer_app = 'SHOPS';
            SENDER_APP('get_template', {template: name, params: params}, function(mess) {
                mess_executer(mess, function(mess) {
                    $('.column-right').html(mess.params.page);
                    console.dir(mess.params.meta);
                    document.title = mess.params.meta.title;
                    document.querySelector('meta[name="description"]').setAttribute("content", mess.params.meta.description);
                    document.querySelector('meta[name="keywords"]').setAttribute("content", mess.params.meta.keywords);
                    if($mobile) {
                        swipe_panel();
                    }
                    call_back();
                });
            });
            break;
        case 'my_orders':
            $('.column-right').html('<div class="up-panel"></div><div class="catalog"></div>');
            console.log('>>>>>'+$('.column-right').length);
            add_loader($('.column-right .up-panel'));
            // add_loader($('.column-right .catalog'));
            buffer_app = 'SHOPS';
            SENDER_APP('get_my_orders', {}, function(mess) {
                mess = JSON.parse(mess);
                if(mess.status !== 'ok') {
                    error_executing(mess);
                } else {
                    document.title = mess.params.meta.title;
                    document.querySelector('meta[name="description"]').setAttribute("content", mess.params.meta.description);
                    document.querySelector('meta[name="keywords"]').setAttribute("content", mess.params.meta.keywords);
                    $('.up-panel').html(mess.params.page);
                    console.dir(mess.params.meta);
                    if(url.searchParams.get('filter') !== null) {
                        setTimeout(function() {
                            $('button[data-filter="'+url.searchParams.get('filter')+'"]').click();
                        }, 10);
                    } else {
                        $('.filter-btns-list button:first-child').click();
                    }
                    if($mobile) {
                        swipe_panel();
                    }
                    call_back();
                }
            });
            break;
    }
}

function check_filter(obj) {
    let type = $(obj).attr('data-filter');
    $('.active-filter').removeClass('active-filter');
    $(obj).addClass('active-filter');
    $('.filter-btns-list button').addClass('btn-white');
    $(obj).removeClass('btn-white');

    update_products_list();

    url.searchParams.set('filter', type);
    let new_url = 'https://'+url.hostname+url.pathname+'?'+url.searchParams;
    history.pushState(null, null, new_url);
}

function update_products_list(paginator_num=1) {
    add_loader($('#products-list'));
    let filter = $('.filter-btns-list .active-filter').attr('data-filter');
    let items = [];  // products[filter]
    for(let i=showed_products*paginator_num-showed_products;i<showed_products*paginator_num;++i) {
        items.push(products[filter][i]);
    }
    buffer_app = 'SHOPS';
    console.dir(items);
    SENDER_APP('get_filter_list_id', {arr: items}, function(mess) {
        mess = JSON.parse(mess);
        if(mess.status !== 'ok') {
            error_executing(mess);
        } else {
            $('#products-list').html(mess.params);
            update_paginate(Object.keys(products[filter]).length, paginator_num);
        }
    });
}

function update_paginate(count_items_all=0, select_num) {
    $('.paginator').remove();
    if(count_items_all > showed_products) {
        let count = Math.ceil(count_items_all/showed_products);
        let paginator = $('<div class="paginator"></div>');
        for(let i = 0;i<count;++i) {
            if(i+1 === select_num) {
                $(paginator).append('<button class="btn" onclick="update_products_list('+(i+1)+')">'+(i+1)+'</button>');
            } else {
                $(paginator).append('<button class="btn-white" onclick="update_products_list('+(i+1)+')">'+(i+1)+'</button>');
            }
        }
        $('.column-right').append(paginator);
    }
}

function archive_item(obj) {
    published(obj, 'archive_list');
}

function to_hi_level(obj) {
    published(obj, 'to_hi_level_list');
}

function published(obj, type_action_list='published_list') {
    let arr = [];
    if($(obj).closest('li.one-order').find('.ok-check').length === 0) {
        $(obj).closest('li.one-order').find('.check-ordr').click();
    }

    let itm = null;
    let pr = null;

    $('.ok-check').each(function(e,t) {
        itm = $(t).closest('li.one-order');
        pr = {
            item_id: $(itm).attr('data-id'),
            shop_id: $(itm).attr('data-id-shop'),
            name: $(itm).find('div h2.h2').text()
        }
        arr.push(pr);
    });

    console.dir(arr);

    let txt = '';
    let btn_ok = '';
    switch(type_action_list) {
        case 'published_list':
            txt = 'Количество публикуемых объявлений - '+arr.length+' шт.';
            btn_ok = 'Опубликовать';
            break;
        case 'archive_list':
            txt = 'Количество архивируемых объявлений - '+arr.length+' шт.';
            btn_ok = 'Архивировать';
            break;
        case 'to_hi_level_list':
            txt = 'Количество продвигаемых объявлений - '+arr.length+' шт.';
            btn_ok = 'Продвинуть';
            break;
        default:
            say('Ошибка типа действия', 2);
            return false;
            break;
    }

    info_qest(undefined, function(e) {
        buffer_app = 'SHOPS';
        SENDER_APP(type_action_list, {arr: arr}, function(mess) {
            mess_executer(mess, function() {
                // if(typeof mess.no_pays !== 'undefined') {
                //     let errs = parseInt(mess.no_pays);
                //     if(errs > 0) {
                //         say('Не удалось опубликовать сообщений - '+errs+' шт.<br>По причине отсутствия средств на баллансе.<br>В меню "КОШЕЛЁК" пополните личный счёт...', 2);
                //     }
                // }
                location.reload();
            });
        });
    }, function(e) {

    }, txt, btn_ok, 'Отмена');
}
function buy_tarif(tarif_name, obj) {
    info_qest(undefined, function() {
        buffer_app = 'SHOPS';
        SENDER_APP('buy_tarif', {tarif_name: tarif_name}, function(mess) {
            mess = JSON.parse(mess);
            if(mess.status === 'ok') {
                location.href='/profil?title=my_orders&filter=not_show';
            } else {
                error_executing(mess);
            }
        });
    }, function() {

    }, 'Подтвердите Ваше желание купить тариф ?', 'ДА - я хочу купить', 'Нет - не буду покупать');
}

function swipe_panel() {
    const scrollContainer = document.querySelector('.profil');
    scrollContainer.scrollTo({
        left: 6000,
        behavior: 'smooth'
    });
}
function mobile_back_row() {
    const scrollContainer = document.querySelector('.profil');
    scrollContainer.scrollTo({
        left: 0,
        behavior: 'smooth'
    });
}

function edit_order(obj) {
    obj = $(obj).closest('li.one-order');
    let id_shop = parseInt($(obj).attr('data-id-shop'));
    let id_order = parseInt($(obj).attr('data-id'));

    location.href = '/create?com=edit&s='+id_shop+'&ord='+id_order;
}

function del_banner(id) {
    info_qest(transform_pos('center'), function() {
        buffer_app = 'BANNER';
        SENDER_APP('del_banner', {id: id}, function(mess) {
            mess_executer(mess, function(mess) {
                $('.banner-item[data-id="'+id+'"]').remove();
            });
        })
    }, function() {

    }, 'Подтвердите полное удаления баннера ?', 'Да - удалить', 'Нет - пусть останется');
}

function toggle_action(name_action) {

}

function support_chat() {
    BACK('profil', 'get_support_users', {}, function(mess) {
        mess_executer(mess, function(mess) {
            console.dir(mess.body);
            if(mess.body.length > 0) {
                let r = rnd(0, (mess.body.length-1));
                console.dir(mess.body[r]);
                say('Оператор службы поддержки: '+mess.body[r]['NAME']);
                begin_chat_with(mess.body[r]['ID']);
                BACK('chat_service', 'call', {user_id: mess.body[r]['ID']}, function(mess) {
                    mess_executer(mess, function(mess) {

                    });
                })
            } else {
                say('Все операторы службы поддержки заняты.<<br>Пожалуйста, обратитесь позднее...', 2);
            }
        });
    });
}