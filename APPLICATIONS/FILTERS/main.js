filters = $('#filters');
filters_items = {};
prefabs = {};               // переменная для хранения шаблонов

/**
 * В переменную data вложен объект со следующими полями
 * data = {
 *     lst - список кнопок с их обработчиками
 *     obj - главная панель (html) в котором хранятся все свойства
 * }
 *
 */
const menu_panel_json = new EventRBS();

types_list = {
    '-':'-',
    'Поле ввода':'input',
    'Текстовый блок':'text',
    'Переключатель (ДА/НЕТ)': 'bool',
    'Список': 'list',
};

$(document).on('dblclick', '.preff', function(e) {
    let name = $(this).find('b').text();
    let id = $(this).attr('data-id');
    open_editor_filter(id, name)
});

$(document).on('dragover', '.insert-container', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).addClass('drag-over');
});
$(document).on('dragleave', '.insert-container', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).removeClass('drag-over');
});
$(document).on('drop', '.insert-container', function(e) {
    let obj = $(this);
    e.preventDefault();
    e.stopPropagation();
    obj.removeClass('drag-over');
    obj.empty();
    // say('dropped-'+buffer_preff);
    let new_panel = buffer_preff_obj.get(0).cloneNode(true);
    if($(new_panel).attr('data-type') === 'list') {
        $(new_panel).find('button').attr('onclick', '');
        $(new_panel).find('button').on('click', function() {
            scan_access_add_lists(obj);
            $(this).closest(".preff").remove();
        }).addClass('micro-closer');
        obj.append(new_panel);
    } else {
        say('Данное поле может принять только прессеты типа "Список 🧾"', 2);
    }
    scan_access_add_lists(obj);
});

function scan_access_add_lists(obj) {
    setTimeout(()=>{
        let pnls = [];
        $(obj).closest('div.flex').find('.insert-container').each(function(e,t) {
            let pnl = $(t).find('.preff');
            if(pnl.length > 0) {
                pnls.push(pnl.attr('data-id'));
            }
        });
        if(pnls.length === 2) {
            $(obj).closest('.content').find('.micro-btn').removeClass('disabled');
        } else {
            $(obj).closest('.content').find('.micro-btn').addClass('disabled');
        }
    }, 100);
}

$(document).on('dblclick', '.off', function(e) {
    let obj = $(this);
    info_qest(undefined, function() {
        obj.removeClass('off');
        obj.find('.param-names input:nth-child(2)').removeAttr('readonly');
    }, function() {}, 'Защита данного поля будет снята<br>' +
        'и вы получите возможность изменить существующие поля данного параметра.<br>' +
        'Если этот параметр где то уже был задействован,<br>то товары с ним могут оказаться недоступными.<br><br>Вы уверены что хотите разблокировать эти поля ?', 'Да', 'Отменить');
});

$(document).on('dragover', '.filter-editor .content.pre', function(e) {
    e.preventDefault();
    $(this).addClass('drag-over');
});
$(document).on('dragleave', '.filter-editor .content.pre', function(e) {
    e.preventDefault();
    $(this).removeClass('drag-over');
});
$(document).on('drop', '.filter-editor .content.pre', function(e) {
    e.preventDefault();
    $('.drag-over').removeClass('drag-over');
    buffer_obj_filter = this;
    get_filter_from_db_and_insert_in(buffer_preff);
});

let buffer_preff = -1;
let buffer_preff_obj = null;

$(document).on('dragstart', '.my-presets .preff', function(e) {
    let obj = $(this);
    buffer_preff_obj = obj;
    buffer_preff = obj.attr('data-id');
    dropped_element_action = function(poss) {
        let name = obj.find('b').text();
        let id = obj.attr('data-id');
        open_editor_filter(id, name, poss)
        console.log('Координаты мыши при сбросе:', cursorPos);
    }
});

$(document).on('contextmenu', '.filter-editor .content.pre', function(e) {
    e.preventDefault();
    e.stopPropagation();

    buffer_obj_filter = $(this);

    let lst = {
        '+ новый < КЛЮЧ > - < ЗНАЧЕНИЕ >': function () {
            let cont = buffer_obj_filter;
            if (!$(cont).hasClass('inner-element')) {
                $(cont).append('<div class="inner-class inner-element"><div style="font-weight: 600; display: inline-block;">NEW_KEY</div> : <input type="text" value="-"><br></div>');
            } else {
                $(cont).parent().append('<div class="inner-class inner-element"><div style="font-weight: 600; display: inline-block;">NEW_KEY</div> : <input type="text" value="-"><br></div>');
            }
        },
        '+ новый КЛАСС': function () {
            let cont = buffer_obj_filter;
            $(cont).append('<div class="inner-class main-class"><div class="title-log" style="font-weight: 600; display: inline-block;">NEW_OBJ</div><br><div class="inner-class inner-element"><div style="font-weight: 600; display: inline-block;">NEW_KEY</div> : <input type="text" value="NEW VALUE"><br></div></div>');
        },
        '📦 Добавить из пресетов': function() {
            let win = create_window(undefined, 'Пресеты. Для поиска, начните вводить');
            let pnl = $('<div style="padding: 5px"></div>');
            $(pnl).append('<fieldset class="fieldset"><legend>Поиск</legend><input style="width: 300px" onchange="selected_preset(this)" oninput="update_preset_list(this)" list="preset-list"></fieldset>');
            $(win).append('<datalist id="preset-list"></datalist>');
            $(win).append(pnl);
            setTimeout(function() {
                $(win).find('input').focus();
                // get_last_filters(pnl);
            }, 300);
        },
        '🧹 Очистить редактор': function() {
            info_qest(undefined, function() {
                $(buffer_obj_filter).empty();
            }, function() {

            }, '🧹 Окно редактора будет очищено. Продолжить ?', 'ДА', 'НЕТ');
        },
    }

    menu_panel_json.action({lst: lst, obj: buffer_obj_filter});

    if(buffer_fragment !== '') {
        lst['➔⊙ Вставить в главный класс'] = function() {
            let new_fr = $('<div class="inner-class main-class"></div>');
            let datas = JSON.parse(localStorage.getItem('buffer_fragment_data'));
            let ind = '';
            for(let i in datas) {
                if(i === 'filterItem') {
                    ind = 'filter-item';
                } else {
                    ind = i;
                }
                new_fr.attr('data-'+ind, datas[i]);
            }
            $(new_fr).append(buffer_fragment)
            $(buffer_obj_filter).closest('.pre').append(new_fr);
        };
    }

    let pnl = null;

    navigator.clipboard.readText()
        .then(text => {
            try {
                JSON.parse(text);
                const btn = $('<button class="presser">+ JSON из буфера в главный класс</button>');
                $(pnl).find('.scroll-list').append(btn);
                btn.on('click', function() {
                    create_form(JSON.parse(text), buffer_obj_filter.get(0));
                });
            } catch (e) {

            }
        })
        .catch(err => {
            console.error('Ошибка при чтении текста: ', err);
        });


    pnl = info_variants(undefined, lst);
});

function get_last_filters(container_obj) {
    let cont = $('<div class="container"></div>');
    $(container_obj).append(cont);
    update_my_presets(cont);
}

function open_my_presets(obj) {
    setTimeout(()=> {
        if($(obj).closest('details').hasAttr('open')) {
            if($(obj).find('.container').length === 0) {
                get_last_filters(obj);
            } else {
                update_my_presets($(obj).find('.container'));
            }
        }
    }, 50);
}

function update_my_presets(obj) {
    BACK('filters_app', 'get_my_presets', {}, function(mess) {
        mess_executer(mess, function(mess) {
            obj = $(obj);
            obj.empty();
            let arr = mess.params;
            console.dir(arr);
            if(typeof arr['flows'] !== 'undefined') {
                let all = arr['_all'];
                let flo = arr['flows']
                for (let i in flo) {
                    let ind = flo[i];
                    create_preset_panel(obj, all[ind]['field'], flo[i], all[ind]['field_name']);
                }
            }
        });
    });
}

function create_preset_panel(parrent, field, id, field_name) {
    let data_type = '';
    let type = '';
    switch(field) {
        case 'bool': type=' Переключатель 🗜'; data_type='toggler'; break
        case 'input': type='Поле ввода ✏️ '; data_type='input'; break
        case 'text': type='Текст 📒'; data_type='text'; break
        case 'list': type='Список 🧾'; data_type='list'; break
    }
    $(parrent).append('<div data-type="'+data_type+'" draggable="true" data-id="'+id+'" class="preff flex column not-selected"><b>'+field_name+'</b><i style="width: 100%; text-align: right; display: inline-block; padding: 5px 0 0">'+type+'</i><button class="micro-closer" onclick="del_param(this, '+id+')">✕</button></div>');
}

function open_my_filters(obj) {
    obj = $(obj).closest('details');
    if(obj.find('.container').length === 0) {
        let cont = $('<div class="container my-filters-last"></div>');
        obj.append(cont);
    } else {
        cont = obj.find('.container');
    }
    $('details .container.my-filters-last').empty();
    setTimeout(()=> {
        if($(obj).closest('details').hasAttr('open')) {
            update_my_filters_last();  //edit_filter(id, 'list');
        }
    }, 50);
}

function add_last_filter(name, type, id) {
    BACK('filters_app', 'add_last_filter', {name: name, type: type, id: id}, function(mess) {
        mess_executer(mess, function(mess) {
            update_my_filters_last();
        });
    });
}

function update_my_filters_last() {
    let cont = $('details .container.my-filters-last');
    if(typeof cont !== 'undefined') {
        cont.empty();
        BACK('filters_app', 'get_last_filters', {}, function(mess) {
            mess_executer(mess, function(mess) {
                console.dir(mess);
                if(typeof mess.params !== 'undefined') {
                    let all = mess.params;
                    for(let i in all) {
                        cont.append('<button onclick="edit_filter('+all[i].id+', \''+all[i].type+'\', \''+all[i].name+'\')" class="btn-gray btn-gray-text not-border micro-btn padding-btn">'+all[i].name+'</button>');
                    }
                }
            });
        });
    }
}


function del_param(obj, id, folder='flows') {
    BACK('filters_app', 'del_param', {id: id, folder: folder}, function(mess) {
        mess_executer(mess, function(mess) {
            $(obj).closest('div').remove();
        });
    });
}

function find_or_create(obj) {
    if(typeof filters === 'undefined') {
        filters = $('#filters');
    }
    obj = $(obj);
    filters.empty();
    let txt = obj.val();
    if(txt.length > 0) {
        obj.val(txt.charAt(0).toUpperCase() + txt.slice(1));

        BACK('filters_app', 'get_filters', {txt: txt}, function(mess) {
            mess_executer(mess, function(mess) {
                filters = $('.filters-data');
                filters_items = mess.params;
                for(let i in mess.params) {
                    filters.append('<option data-id="'+mess.params[i].id+'" value="'+i+'"></option>');
                }
            });
        });

    }
}
function selected_input(obj) {
    let txt = $(obj).val();
    let filter_item = filters_items[txt];
    let id = -1;

    if(typeof filter_item !== 'undefined') {
        let alias = filter_item['alias'];
        id = filter_item['id'];
        let name = filter_item['field_name'];
        setTimeout(function () {
            filters.empty();
        }, 500);
    }

    open_editor_filter(id, txt);
}

function open_editor_filter(id, txt, coords = {}) {
    let poss = undefined;
    if (Object.keys(coords).length !== 0) {
        poss = coords;
    }

    let win = create_window(poss, "Редактор элемента фильтра: <i style='display: inline-block; margin-left: 4px; background-color: rgba(255,255,0,0.46); padding: 3px 6px; border-radius: 40px'>«"+txt+"»</i>", function() {  //«»
        setTimeout(function() {
            let main = $(win).closest('.window');
            $(win).closest('.window').find('h4 button:nth-child(2)').remove();
            $(win).closest('.window').find('h4').prepend('<div class="logo-table"><img src="./DOWNLOAD/ec68beb7c94d11e1fb3a5fb87772aee1.svg"></div>');
            $(win).attr('data-filter-param-id', id);

            let cont = document.getElementById('editor-filter').content.cloneNode(true);
            win.append(cont);

            if(id !== -1) {
                BACK('filters_app', 'get_filter_param', {id: id}, function (mess) {
                    mess_executer(mess, function(mess) {
                        main.find('h4').addClass('flex align-center no-wrap');
                        main.find('h4 b').css('transform', 'translateY(0px)');
                        main.find('h4 button').before('<button onclick="in_presets('+id+')" title="Создаст закладку этого параметра в списке -Мои пресеты-" style="margin-left: auto; transform: translateX(18px)" class="btn-gray align-center btn-gray-text not-border micro-btn btn-img-with-text"><img src="./DOWNLOAD/20230505-205115_id-2-938272.svg" width="20" height="20"><span>В пресеты</span></button>')
                        main.find('.param-names').addClass('off');
                        main.find('.params-type').addClass('disabled');
                        main.find('.fieldset.min-size:not(.params-type)').addClass('off');
                        console.dir(mess.params);
                        let arr = mess.params;
                        win.find('input[data-name="param_name"]').val(arr.field_name);
                        win.find('input[data-name="param_name_alias"]').val(arr.alias);
                        win.find('select[data-name="type-input"]').addClass('disabled');
                        switch(arr.field) {
                            case 'list':
                                win.find('select[data-name="type_element"]').val('Список');
                                open_json(id, arr.default, main);
                                break;
                            case 'text':
                                win.find('select[data-name="type_element"]').val('Текстовый блок');
                                open_value_text(id, arr.default, main);
                                break;
                            case 'bool':
                                win.find('select[data-name="type_element"]').val('Переключатель (ДА/НЕТ)');
                                open_json(id, arr.default, main);
                                break;
                            case 'input':
                                win.find('select[data-name="type_element"]').val('Поле ввода');
                                win.find('select[data-name="type-input"]').removeClass('disabled');
                                if(arr.type === 'string') {
                                    html_set_value(win, 'toggle', 'type-input', 1);
                                }
                                if(arr.type === 'int') {
                                    html_set_value(win, 'toggle', 'type-input', 0);
                                }
                                open_value(id, arr.default, main);
                                break;
                        }
                        change_right_panel(win, arr.field);
                        change_type_filter_item(win.find('select[data-name="type_element"]'));
                    });
                });
            } else {
                BACK('filters_app', 'get_prefabs', {}, function (mess) {
                    mess_executer(mess, function(mess) {
                        prefabs = mess.params;
                        console.dir(prefabs);
                    });
                });
                $(win).find('.param-names input').addClass('red-border');
            }
        }, 100);
    }, 'filter-item-win');
}

function in_presets(id) {
    BACK('filters_app', 'add_param_in_presets', {id: id}, function(mess) {
        mess_executer(mess, function (mess) {
            say('добавлено');
            update_my_presets($('.my-presets .container'));
        });
    });
}
function open_json(id, json_default, main) {
    json_pnls[id+'_rs_default'] = {
        table: 'filters',
        id: id,
        column: 'default',
        json: json_default,
    }
    main.find('.json').addClass('off');
    create_form(json_default, main.find('.json .left-panel').get(0), '', 0, function() {
        main.find('.json .left-panel').find('.inner-class').addClass('opened');
        main.find('.json .right-panel').append('<div class="type-marker">JSON</div>');
    });
}
function open_value(id, value_default, main) {
    main.find('.json').addClass('off');
    main.find('.json .right-panel').append('<div class="type-marker">STR</div>');
    main.find('.json .left-panel').append('<div style="padding: 6px 8px; min-height: 86px"><input class="" value="'+value_default+'"></div>');
}
function open_value_text(id, value_default, main) {
    main.find('.json').addClass('off');
    main.find('.json .right-panel').append('<div class="type-marker">TXT</div>');
    main.find('.json .left-panel').append('<div style="padding: 6px 8px; min-height: 86px"><textarea placeholder="значение по умолчанию" class="filter-text">'+value_default+'</textarea></div>');
}
function sel_mask() {
    BACK('filters_app', 'get_main', {}, function(mess) {
        mess_executer(mess, function(mess) {
            let lst = {};
            for(let i in mess.params) {
                let _class = "";
                if(typeof mess.params[i].json !== 'undefined') {
                    _class = " isset ";
                }
                let key = '<div class="flex gap-15 '+_class+'"><img width="20" height="20" src="./DOWNLOAD/'+mess.params[i].logo+'"><span>'+i+'</span></div>';
                lst[key] = {
                    click: function() { main_cat_quest(mess.params[i].id, i, _class); },
                    hover: function() { set_sel_under_cat(mess.params[i].id, i, true) } ,
                }
            }
            info_variants(transform_pos('center'), lst, 'Укажите категорию:', 'menu-info-panel', true);
        });
    });
}
function main_cat_quest(id, name, _class) {
    all_menu_panels_close();

    let lst = {};
    if(_class === '') {
        lst['Создать фильтр для категории <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            edit_filter(id, 'main', name);
        };
    } else {
        lst['Редактировать фильтр у категории <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            edit_filter(id, 'main', name);
        };
        lst['Удалить фильтр у категории <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            info_qest(transform_pos('center'), function() {
                delete_filter(id, 'main');
            }, function() {

            }, 'Подтвердите безвозвратное удаление фильтра у <b>категории <i style="background-color: yellow">"'+name+'"</i></b> ?', 'Да - УДАЛИТЬ', 'Нет - не удалять');
        };
    }
    info_variants(undefined, lst, 'Работа <b>с категорией <i style="background-color: yellow">"'+name+'"</i></b>');
}
function set_sel_under_cat(id_main_cat, name_main_cat, is_menu=false) {
    BACK('filters_app', 'get_under', {id_main_cat: id_main_cat}, function(mess) {
        mess_executer(mess, function(mess) {
            let lst = {};
            for(let i in mess.params) {
                let _class = "";
                if(typeof mess.params[i].json !== 'undefined') {
                    _class = " isset ";
                }
                let key = '<div class="flex gap-15 '+_class+'"><img width="20" height="20" src="./DOWNLOAD/'+mess.params[i].logo+'"><span>'+i+'</span></div>';
                lst[key] = {
                    'click': function() { under_cat_quest(mess.params[i].id, i, _class); },
                    'hover': function() { set_sel_action_list(mess.params[i].id, i, true); }
                }
            }
            info_variants(undefined, lst, 'Укажите под-категорию, <b>категории <i style="background-color: yellow">"'+name_main_cat+'"</i>"</b>', 'menu-info-panel', true);
        });
    });
}
function under_cat_quest(id, name, _class) {
    all_menu_panels_close();

    let lst = {};
    if(_class === '') {
        lst['Создать фильтр для под-категории <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            edit_filter(id, 'under', name);
        };
    } else {
        lst['Редактировать фильтр у под-категории <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            edit_filter(id, 'under', name);
        };
        lst['Удалить фильтр у под-категории <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            info_qest(transform_pos('center'), function() {
                delete_filter(id, 'under');
            }, function() {

            }, 'Подтвердите безвозвратное удаление фильтра у <b>под-категории <i style="background-color: yellow">"'+name+'"</i></b> ?', 'Да - УДАЛИТЬ', 'Нет - не удалять');
        };
    }
    info_variants(undefined, lst, 'Работа <b>с под-категорией <i style="background-color: yellow">"'+name+'"</i></b>');
}
function set_sel_action_list(id_under_cat, name_under_cat, is_menu=false) {
    BACK('filters_app', 'get_action_list', {id_under_cat: id_under_cat}, function(mess) {
        mess_executer(mess, function(mess) {
            let lst = {};
            for(let i in mess.params) {
                let _class = "";
                if(typeof mess.params[i].json !== 'undefined') {
                    _class = " isset ";
                }
                let key = '<div class="flex gap-15 '+_class+'"><img width="20" height="20" src="./DOWNLOAD/'+mess.params[i].logo+'"><span>'+i+'</span></div>';
                lst[key] = {
                    'click': function() { action_list_quest(mess.params[i].id, i, _class); },
                    'hover': function() {},
                }
            }
            info_variants(undefined, lst, 'Активный лист, <b>под-категории <i style="background-color: yellow">"'+name_under_cat+'"</i>"</b>', 'menu-info-panel', true);
        });
    });
}
function action_list_quest(id, name, _class) {
    all_menu_panels_close();

    let lst = {};
    if(_class === '') {
        lst['Создать фильтр для листа <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            edit_filter(id, 'list', name);
        };
    } else {
        lst['Редактировать фильтр у листа <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            edit_filter(id, 'list', name);
        };
        lst['Удалить фильтр у листа <b style="font-weight: 800">"'+name+'"</b>'] = function() {
            info_qest(transform_pos('center'), function() {
                delete_filter(id, 'list');
            }, function() {

            }, 'Подтвердите безвозвратное удаление фильтра у <b>активного листа <i style="background-color: yellow">"'+name+'"</i></b> ?', 'Да - УДАЛИТЬ', 'Нет - не удалять');
        };
    }
    info_variants(undefined, lst, 'Работа <b>с активным листом <i style="background-color: yellow">"'+name+'"</i></b>');
}
function delete_filter(id, type="main|under|list") {
    BACK('filters_app', 'delete_filter', {id: id, type: type}, function (mess) {
        mess_executer(mess, function (mess) {
            say('Удаление (успешно)');
        });
    });
}
function edit_filter(id, type='main|under|list', name='') {
    BACK('filters_app', 'get_filter', {id: id, type: type}, function (mess) {
        if(name !== '') {
            add_last_filter(name, type, id);
        }
        mess_executer(mess, function(mess) {
            let arr = mess.params;
            let table_s = 'nnejwo_network_robin';

            switch(type) {
                case 'main': table_s='shops_categorys'; break;
                case 'under': table_s='shops_undercats'; break;
                case 'list': table_s='shops_lists'; break;
            }

            if (Object.prototype.toString.call(arr) !== '[object Object]') {
                if (arr === null) {
                    arr = '{"NEW_OBJ":{"NEW_KEY":"NEW VALUE"}}';
                }
                arr = JSON.parse(arr);
                console.dir(arr);
                edit_json_filter(arr, table_s, id);
            } else {
                edit_json_filter(arr, table_s, id);
            }
        })
    });
}
function edit_json_filter(json, table_name, id) {
    let cont = create_window(transform_pos('center'), 'Редактор фильтров', function() {
        setTimeout(function() {
            let key = id+'_rs_json';
            json_pnls[key] = {
                table: table_name,
                id: id,
                column: 'json',
                json: json,
            }

            $('.json-td img').remove();
            let win_up_panel = $('<div class="win-up-panel"></div>');
            $(cont).closest('.window').find('.minimaze-btn').remove();
            $(cont).closest('.window').addClass('json-win');
            $(cont).closest('.window').find('h4').addClass('flex between align-center gap-5');
            $(win_up_panel).append('<button title="Скопировать в буффер обмена данный JSON" onclick="get_json(\''+key+'\')" class="save-btn btn-gray btn-gray-text not-border micro-btn">В буффер обмена</button>');
            $(win_up_panel).append('<button title="Сохранить изменения" onclick="save_json(this, \''+key+'\')" class="save-btn btn-gray btn-gray-text not-border micro-btn">СОХРАНИТЬ</button>');

            $(cont).closest('.window').css('background', 'white');
            $(cont).addClass('pre');
            console.dir(json);
            if(json !== null) {
                create_form(json, $(cont).get(0));
            }
            $(cont).closest('.window').append(win_up_panel);

            /**
             * Тут я "вешаю" на кнопки меню (в редакторе фильтров) дополнительные функции
             */
            menu_json.subscribe(function(data) {
                const type = $(data.obj).find('> .inner-element[data-row-name="type"] input').val();
                console.log(data.obj);
                switch(type) {
                    case 'int':
                        const range = get_field_from_json_obj(data.obj, 'range');
                        if(range !== false) {
                            if(range === '1') {
                                data.lst['<b style="color: blue;">⏏ ⤋ Отключить преобразование числа в диапазон</b>'] = function() {
                                    add_json_param_in_editor_panel(data.obj, 'range', '0');
                                }
                                data.lst['<b style="color: blue;">⏏ ✕ Удалить преобразование числа в диапазон</b>'] = function() {
                                    delete_json_param_in_editor_panel(data.obj, 'range');
                                }
                            } else {
                                data.lst['<b style="color: blue;">⏏ ⤊ Включить преобразование числа в диапазон</b>'] = function() {
                                    add_json_param_in_editor_panel(data.obj, 'range', '1');
                                }
                            }
                        } else {
                            data.lst['<b style="color: blue;">⏏ Преобразовать в поиске число в диапазон</b>'] = function() {
                                const llist = ['0', '100'];
                                info_list_editor(undefined, llist, function(ans){
                                    add_json_param_in_editor_panel(data.obj, 'range', '1', false);
                                    add_json_param_in_editor_panel(data.obj, 'min', ans[0], false);
                                    add_json_param_in_editor_panel(data.obj, 'max', ans[1]);
                                }, 'Отредактируйте MIN и MAX значения:', false);
                            }
                        }
                        break;
                    case 'bool':
                        const isset_items = get_field_from_json_obj(data.obj, 'ITEMS');
                        if(isset_items === false) {
                            data.lst['<b style="color: #d000ff;">📃 📃 Добавить в переключатель 2 списка</b>'] = function() {
                            let slots = create_window(undefined, 'Настройка переключателя ⟪<b style="background-color: yellow">'+data.txt+'</b>⟫', function() {
                                setTimeout(()=>{
                                    slots.closest('.window').css('background-color', '#ffffff').css('background-image', 'none');
                                    slots.append('<div class="flex between gap-10" style="padding: 10px; width: 100%"><div ondblclick="insert_in_container(this)" class="insert-container"></div><div ondblclick="insert_in_container(this)" class="insert-container"></div></div>');

                                    let btn = $('<button style="margin-left: auto" class="disabled save-btn btn-gray btn-gray-text not-border micro-btn">Применить</button>');
                                    btn.on('click', function() {
                                        apply_lists_in_toggler(this, data.obj);
                                    });

                                    let div = $('<div class="flex" style="width: 100%; padding: 0 20px 5px"></div>');
                                    div.append(btn);

                                    slots.append(div);
                                }, 10);
                            }, 'no-minimazed');

                            }
                        } else {
                            data.lst['<b style="color: #d000ff;">✕ ✕ Удалить списки из переключателя</b>'] = function() {
                                info_qest(undefined, function() {
                                    delete_json_param_in_editor_panel(data.obj, 'checker', false);
                                    delete_json_param_in_editor_panel(data.obj, 'ITEMS');
                                }, function() {

                                }, 'Подтвердите удаление списков из этого переключателя <b class="yellow">'+data.txt+'</b>. Удалять ?');
                            }
                        }
                        break;
                }
            });

        }, 100);
    }, 'filter-editor');
}

function insert_in_container(obj) {
    let win = create_window(undefined, 'Пресеты. Для поиска, начните вводить');
    let pnl = $('<div style="padding: 5px"></div>');
    let fs = $('<fieldset class="fieldset"><legend>Поиск</legend></fieldset>');
    let inpt = $('<input style="width: 300px" oninput="update_preset_list(this, [\'list\'])" list="preset-list">');
    $(inpt).on('change', function() {
        insert_in_container_set(obj, this);
    });
    fs.append(inpt);
    pnl.append(fs);

    $(win).append('<datalist id="preset-list"></datalist>');
    $(win).append(pnl);
    setTimeout(function() {
        $(win).find('input').focus();
        // get_last_filters(pnl);
    }, 300);
}

function insert_in_container_set(container, obj) {
    let opt = $('option[data-value="'+$(obj).val()+'"]').attr('data-id');
    $(obj).closest('.window').remove();
    BACK('filters_app', 'get_filter_name', {id: opt}, function (mess) {
        mess_executer(mess, function(mess) {
            create_preset_panel(container, 'list', opt, mess.params);
            $(container).find('.preff').removeAttr('draggable');
            scan_access_add_lists(container);
        });
    });
}

function apply_lists_in_toggler(obj, parrent_obj) {
    let pnls = [];
    $(obj).closest('div.content').find('.insert-container').each(function(e,t) {
        let pnl = $(t).find('.preff');
        if(pnl.length > 0) {
            pnls.push(pnl.attr('data-id'));
        }
    });

    if(pnls.length === 2 && pnls[0] !== pnls[1]) {
        BACK('filters_app', 'get_filter_params', {ids: pnls}, function (mess) {
            mess_executer(mess, function(mess) {
                add_obj_json_param_in_editor_panel(parrent_obj, {ITEMS:mess.params});
                add_json_param_in_editor_panel(parrent_obj, 'checker', 1, true);
                close_window($(obj).closest('.window'));
            });
        });
    } else {
        say('Оба слота должны быть заполнены и прессеты не должны быть одинаковыми...', 2);
    }
}

function del_all_json_filter(obj_container) {
    info_qest(undefined, function(){
        obj_container = $(obj_container).closest('.window').find('.pre');
        $(obj_container).empty();
        $(obj_container).append('<div class="inner-class main-class"></div>');
    }, function() {}, 'Подтвердите очистку всего редактора ?', 'Да - очистить', 'Нет - пусть всё остаётся');
}
function enter_name(obj, is_id_field=false) {
    obj = $(obj);
    if(is_id_field) {
        if(obj.val().length === 0) {
            obj.addClass('red-border');
        } else {
            obj.removeClass('red-border');
        }
    } else {
        let save_all_btn = obj.closest('.window').find('.save-all-btn');
        let data_filter_id = obj.closest('div[data-filter-param-id]').attr('data-filter-param-id');

        let title_obj = obj.closest('.window').find('h4 i');
        let translit_obj = obj.closest('.fieldset').find('input:last-child');
        let txt = obj.val();
        if (data_filter_id === '-1') {
            translit_obj.val(translit(txt, true));
        }
        if (txt.length > 0) {
            obj.val(txt.charAt(0).toUpperCase() + txt.slice(1));
            obj.removeClass('red-border');
        } else {
            obj.addClass('red-border');
        }

        if (translit_obj.val().length > 0) {
            translit_obj.removeClass('red-border');
        } else {
            translit_obj.addClass('red-border');
        }
        title_obj.text("«" + txt + "»");
    }
    timer_name_alias_exist_scaner(obj, obj.closest('.param-names').find('input:first-child').val(), obj.closest('.param-names').find('input:last-child').val())
}
timer_scaner_sender = null;

function timer_name_alias_exist_scaner(obj, name, alias) {
    clearTimeout(timer_scaner_sender);
    timer_scaner_sender = setTimeout(()=>{
        BACK('filters_app', 'check_valid_exist_name_and_alias', {name: name, alias: alias}, function(mess) {
            mess_executer(mess, function(mess) {
                if(mess.body === 'isset') {
                    if(name === mess.params.field_name) {
                        $(obj).closest('.param-names').find('input:first-child').addClass('red-border');
                        say('Такое имя, уже зарегистрировано', 2);
                    }
                    if(alias === mess.params.alias) {
                        $(obj).closest('.param-names').find('input:last-child').addClass('red-border');
                        say('Такой ID, уже зарегистрирован', 2);
                    }
                }
            });
        });
        clearTimeout(timer_scaner_sender);
    }, 1000);
}

function validate_param_form(obj) {
    let valid_params = true;
    obj = $(obj);
    if(!obj.hasClass('filter-item-win')) {
        obj = obj.closest('.filter-item-win');
    }
    if(obj.find('.param-names input:first-child').val().length === 0) {
        say('Не укзан параметр "Название параметра"', 2);
        valid_params = false;
    }
    if(obj.find('.param-names input:last-child').val().length === 0) {
        say('Не укзан параметр "ID параметра"', 2);
        valid_params = false;
    }

    if(obj.find('select[data-name="type_element"]').val() === '-') {
        say('Не укзан параметр "Тип элемента параметра"', 2);
        valid_params = false;
    }
    return valid_params;
}
function change_type_filter_item(obj) {
    let type = $(obj).val();
    if(type === 'Поле ввода') {
        $(obj).closest('fieldset').find('.html-toggler').removeClass('disabled');
    } else {
        $(obj).closest('fieldset').find('.html-toggler').addClass('disabled');
    }

    let id = $(obj).closest('div[data-filter-param-id]').attr('data-filter-param-id');

    if(id === '-1') {
        let type_field = types_list[type];
        let arr = prefabs[type_field];
        // console.dir(prefabs);

        $(obj).closest('.filter-item-win').find('.json .right-panel .type-marker').remove();
        let panel = $(obj).closest('.editor-item-filter').find('.json .left-panel');
        panel.empty();

        switch(type_field) {
            case 'input':
                $(obj).closest('.filter-item-win').find('.json .right-panel').append('<div class="type-marker">STR</div>');
                panel.append('<div style="padding: 6px 8px; min-height: 86px"><input class="" placeholder="значение по умолчанию" value=""></div>');
                break;
            case 'text':
                $(obj).closest('.filter-item-win').find('.json .right-panel').append('<div class="type-marker">TXT</div>');
                panel.append('<div style="padding: 6px 8px; min-height: 86px"><textarea placeholder="значение по умолчанию" class="filter-text"></textarea></div>');
                break;
            case 'list':
            case 'bool':
                $(obj).closest('.filter-item-win').find('.json .right-panel').append('<div class="type-marker">JSON</div>');
                create_form(arr.json, panel.get(0), '', 0, function() {
                    $(panel).find('.inner-class').each(function(e,t) {
                        $(t).addClass('opened');
                    })
                });
                break;
            default:
                say('Тип - не распознан...', 2);
                break;
        }
        change_right_panel(obj, type_field);
    } else {
        // say('Изменить тип у уже существующего параметра - нельзя..', 2);
    }
}
function show_types() {
    let txt = "<ul><li><b>Поле ввода</b> - строковая или числовая переменная (однострочное поле) до 255 символов.</li>" +
        "<li><b>Текстовый блок</b> - многострочное поле, подходит для небольших текстов.</li>" +
        "<li><b>Переключатель (ДА/НЕТ)</b> - поле, которое имеет только два состояния.</li>" +
        "<li><b>Список</b> - блок со многими вариантами выбора</li></ul>";
    info_info(undefined, txt, 'Типы элементов параметра', 'ok', '20241027-195937_id-2-788004.png');
}
function update_json_panel_from_params(obj_parent_window) {
    let obj = $(obj_parent_window).closest('.window').find('.right-panel');
    let rows = get_json_from_panel(obj, {});
    console.dir(rows);
}
function json_to_panel(json, panel_obj, open_all=false) {
    $(panel_obj).empty();
    create_form(json, panel_obj.get(0), '', 0, function() {
        if(open_all) {
            $(panel_obj).find('.inner-class').each(function (e, t) {
                $(t).addClass('opened');
            })
        }
    });
}
/**
 * Изменяет конролы в левой панели
 * @param main_obj
 * @param type - (input text bool list)
 */
function change_right_panel(main_obj, type) {
    main_obj = $(main_obj).closest('.filter-item-win').find('.right-panel');
    main_obj.find('.tmp').remove();
    switch(type) {
        case 'input':
            main_obj.append(document.getElementById('tmp-'+type).content.cloneNode(true));
            break;
        case 'text':
            main_obj.append(document.getElementById('tmp-'+type).content.cloneNode(true));
            break;
        case 'bool':
            main_obj.append(document.getElementById('tmp-'+type).content.cloneNode(true));
            break;
        case 'list':
            main_obj.append(document.getElementById('tmp-'+type).content.cloneNode(true));
            break;
        default:
            say('Не удалось расспознать тип...', 2);
            break;
    }
}
function change_list_items(obj) {
    let panel = $(obj).closest('.json').find('.left-panel');
    let arr = get_json_from_panel(panel);
    let lst_ans = {};
    info_list_editor(undefined, arr, function(lst) {
        for(let i in lst) {
            lst_ans[i] = lst[i];
        }
        json_to_panel(lst_ans, panel);
    }, 'Редактирование списка', true);
}
function change_bool_list(obj) {
    let panel = $(obj).closest('.json').find('.left-panel');
    let arr = get_json_from_panel(panel);
    let lst_ans = {};
    info_list_editor(undefined, arr.states, function(lst) {
        for(let i in lst) {
            lst_ans[i] = lst[i];
        }
        arr.states = lst_ans;
        json_to_panel(arr, panel, true);
    }, 'Редактирование состояний', false);
}
function change_stat(obj) {
    let panel = $(obj).closest('.json').find('.left-panel');
    let arr = get_json_from_panel(panel);
    setTimeout(()=>{
        let stat = parseInt($(obj).attr('data-status'));
        if(stat === 1) { arr.preset = 1; }
        if(stat === 0) { arr.preset = 0; }
        json_to_panel(arr, panel, true);
    }, 50);
}
function change_default_text(obj) {
    let panel = $(obj).closest('.json').find('.left-panel');
    let txt = panel.find('textarea').val();
    info_inputText(undefined, function() {
        panel.find('textarea').val(bufferText);
    }, 'Введите новый текст');
}
function change_default_string(obj) {
    let panel = $(obj).closest('.json').find('.left-panel');
    let txt = panel.find('input').val();
    info_inputString(undefined, function() {
        panel.find('input').val(bufferText);
    }, 'Введите новое значение');
}

function filter_parameter_update(obj, not_save=false) {
    let arr = {};
    obj = $(obj).closest('.editor-item-filter');
    if(validate_param_form(obj) === false) {
        return;
    }
    let editor = obj.find('.json .left-panel');
    let default__ = get_json_from_panel(editor);
    arr = {
        alias: obj.find('.param-names input:last-child').val(),
        block: 0,
        default: default__,
        field: types_list[obj.find('select[data-name="type_element"]').val()],
        field_name: obj.find('.param-names input:first-child').val(),
        id: obj.closest('.content').attr('data-filter-param-id'),
        order: 120,
        required: obj.find('.html-toggler[data-name="required"]').attr('data-status'),
        visible: obj.find('.html-toggler[data-name="visible"]').attr('data-status'),
    };

    switch(arr.field) {
        case 'bool':
            arr['type'] = 'bool';
            break;
        case 'list':
            arr['type'] = 'string';
            break;
        case 'input':
            let type_input = obj.find('.html-toggler[data-name="type-input"]').attr('data-status');
            if(type_input === '1') {
                arr['type'] = 'string';
            } else if(type_input === '0') {
                arr['type'] = 'int';
            }
            break;
        case 'text':
            arr['type'] = 'text';
            break;
    }
    console.dir(arr);
    if(not_save) {
         return arr;
    } else {
        BACK('filters_app', 'update_filter', {arr: arr}, function (mess) {
            mess_executer(mess, function (mess) {
                console.dir(mess);
                if(parseInt(arr.id) === -1) {
                    if(mess.status === 'ok') {
                        in_presets(mess.params);
                        say('Новый параметр - создан.<br>Параметр добавлен в "Мои прессеты"');
                    }
                } else {
                    say('Успешно');
                }
            });
        });
    }
}
function clear_all_my_pressets() {
    info_qest(undefined, function() {
        BACK('filters_app', 'delete_my_pressets', {folder: 'flows'}, function (mess) {
            mess_executer(mess, function (mess) {
                update_my_presets($('.my-presets .container'));
            });
        });
    }, function() {

    }, 'Действительно очистить панель "Мои пресеты" ?');
}
function clear_all_my_filters() {
    info_qest(undefined, function() {
        BACK('filters_app', 'delete_my_filters', {}, function (mess) {
            mess_executer(mess, function (mess) {
                update_my_filters_last();
            });
        });
    }, function() {

    }, 'Действительно очистить панель "Последние сборки фильтров" ?');
}
