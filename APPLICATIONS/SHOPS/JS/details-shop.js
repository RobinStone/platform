let access_free = ['active', 'city', 'address', 'all_time_work', 'title'];

$(document).on('click', '.link', function(e) {
    e.stopPropagation();
});

function edit_shop(obj, types, str) {
    let title = $(obj).closest('details').find('tr[data-field="title"]').attr('data-value');
    let id_shop = $(obj).closest('details').attr('data-id');
    let tr = $(obj).closest('tr');
    let field_name = $(tr).find('td:first-child').text();
    let field = tr.attr('data-field');
    let val = tr.attr('data-value');
    if(title === 'Бесплатный' && !access_free.includes(field)) {
        say('Изменение данного поля на тарифе "Бесплатный" - невозможно.<br>Пожалуйста, для получения всех возможностей - измените тариф на "Витрина" или "Интернет-магазин"', 2);
        return false;
    }
    switch(types) {
        case 'string':
            info_inputString(undefined, function() {
                set_new_param_for_shop(id_shop, field, bufferText);
            }, 'Введите новое значение для поля<br><b style="background-color: rgba(255,255,0,0.49)">«'+field_name+'»</b>', val);
            break;
        case 'text':
            info_inputText(undefined, function() {
                set_new_param_for_shop(id_shop, field, bufferText);
            }, 'Введите новое значение', val);
            break;
        case 'bool':
            if(val === '1') {
                val = '0';
            } else {
                val = '1';
            }
            switch(field) {
                case 'active':
                    set_new_param_for_shop(id_shop, field, val);
                    break;
                case 'all_time_work':
                    set_new_param_for_shop(id_shop, field, val);
                    break;
            }
            break;
        case 'select':
            open_popup('cities', {val: val, id_shop: id_shop});
            break;
        case 'worktime':
            open_popup('worktime', {val: val, id_shop: id_shop});
            break;
        case 'plus':
            info_qest(transform_pos('center'), function() {
                set_new_param_for_shop(id_shop, field, add_period_days_plan);
            }, function() {

            }, 'Вы действительно хотите продлить действующий план на '+add_period_days_plan+' дней ?', 'ДА - продлить на '+add_period_days_plan+' дней', 'Нет - не продлевать');
            break;
        case 'tarif':
            open_popup('change_plan', {id_shop: id_shop});
            break;
        case 'img':
            let lst = {
                'Загрузить с устройства': function () {
                    general_executer ='loader-logo-shop';
                    place_to_indicators_container_tag = tr.find('.cont-ind');
                    params_general = {
                        id_shop: $(tr).closest('details').attr('data-id'),
                    };
                    set_accept_types_loading('img');
                    final_loading_ok = function() {
                        location.reload();
                    };
                    $('#general-input-file').click();
                },
                'Ранее загруженные': function() {
                    open_popup('self_imgs_gallery', {id_shop: id_shop});
                }
            };
            info_variants(undefined, lst, 'Варианты изменения лого');
            break;
    }
}

function del_addit(obj, id_addition) {
    let name = $(obj).closest('.add-cont').find('span').text();
    info_qest(undefined, function() {
        buffer_app = 'SHOPS';
        SENDER_APP('del_from_addit', {shop_id: $(obj).closest('.shops-profil').attr('data-id'), id_addition: id_addition}, function(mess) {
            mess_executer(mess, function(mess) {
                $(obj).closest('.add-cont').remove();
            });
        });
    }, function() {

    }, 'Подтвердите отключение торговой площадки от города <b class="bold-font">"'+name+'"</b>', 'ОТКЛЮЧИТЬ', 'отмена');
}

function set_plan(id_shop, plan, price) {
    close_popup('change_plan');
    info_qest(transform_pos('center'), function() {
        set_new_param_for_shop(id_shop, 'title', plan);
    }, function() {

    }, 'ВНИМАНИЕ!<br>Вы пытаетесь изменить план на [<b>'+plan+'</b>]<br>' +
        'Сумма в [<b class="bold-font">'+price+' P</b>] - будет списана с вашего счёта в любом случае, если вы нажмёте <b class="bold-font">ДА</b>, даже если вы ПОНИЖАЕТЕ тариф.<br>' +
        'Обдумайте Ваше решение ещё раз.<br>Вы согласны перевести эту площадку на тариф [<b class="bold-font">'+plan+'</b>] за <b class="bold-font">'+price+' P</b> ?', 'ДА', 'НЕТ');
}

function set_new_param_for_shop(id_shop, param, argum, call_back=function() {}) {
    buffer_app = 'SHOPS';
    SENDER_APP('set_new_param_for_shop', {id: id_shop, param: param, argum: argum}, function(mess) {
        mess_executer(mess, function(mess) {
            let open_map_shop = [];
            if($('.shops-profil').each(function(e,t) {
                if($(t).hasAttr('open')) {
                    open_map_shop.push($(t).attr('data-id'));
                }
            })) {
                open_tab('my_shops', {params: open_map_shop}, function() {
                    load_map_shops();
                });
            }
            if($('#container-3').length > 0) {
                close_popup('self_imgs_gallery');
            }
            call_back();
        });
    });
}