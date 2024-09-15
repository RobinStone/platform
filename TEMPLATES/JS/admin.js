stay_visible = new EventRBS();
stay_hidden = new EventRBS();

let functions = [];

function edit_profil() {
    let win = create_window(transform_pos('right', {x:-150, y:-100}), 'Редактор профиля:');
    open_card(win, 'users', user_id, {}, function() {
        $(win).closest('.window').find('h4 button:nth-child(2)').remove();
        $(win).closest('.window').find('h4').prepend('<div class="logo-table"><img src="/IMG/SYS/infocard.svg"></div>');
    });
}

function edit_row(table_name, id_row, title='Редактирование записи') {
    let win = create_window(transform_pos('center'), title);
    open_card(win, table_name, id_row, {}, function() {
        $(win).closest('.window').find('h4 button:nth-child(2)').remove();
        $(win).closest('.window').find('h4').prepend('<div class="logo-table"><img src="/IMG/SYS/infocard.svg"></div>');
    });
}

function chat_activator() {

}

function sms_agregator() {
    BACK('admin', 'sms_agregator', {}, function(mess) {
        let money = parseFloat(mess);
        say('Осталось: '+money+' Р');
    });
}

function show_hidden_chat() {
    chat_activator();
}

function hu_in_chat(count=20) {
    let com = '>add_in_stack 0~~'+parseInt(count)+'~~get_all_connected~~2~~data';
    $('#message-m').val(com);
    $('.chat-sender-btn').click();
}

function access_manager() {
    open_table('access_manager');
}

function on_off(table_name) {
    SENDER('toggle_edit_table_status', {table: table_name}, function(mess) {
        mess_executer(mess, function(mess) {
            refrash_table_name(table_name);
        });
    });
}

///////////////////////////////////////////////////////////////////////
///                          ADMIN FUNCTIONS                         //
///////////////////////////////////////////////////////////////////////

function append_permission(obj) {
    let card = {
        id: parseInt($(obj).find('td[data-name="id"]').text()),
        action: $(obj).find('td[data-name="name"]').text(),
        target: $(obj).find('td[data-name="target"]').text().split('|'),
        key: $(obj).find('td[data-name="key"]').text(),
        limit: parseInt($(obj).find('td[data-name="secs"]').text()),
    };
    console.dir(card);
    let win = create_window(undefined, 'УСТАНОВКА ПАРАМЕТРА');
    get_template('manager-card', card).then(function(dt) {
        win.html(dt);
    }).catch(function(er) {
        console.error('Ошибка промиса...');
    });
}
function select_city(obj) {
    let id = parseInt($(obj).find('td[data-name="id"]').text());
    let table_name = $(obj).closest('.table-db').attr('data-title');
    let table = $(obj).closest('.table-db').attr('data-name');
    $('.finder-form').remove();
    let lst = {
        'Изменить город': function() {
            let win = create_window(undefined, 'Поиск в таблице "'+table_name+'"', undefined, 'finder-form');
            $(win).append('<input list="finder-'+table+'" onchange="set_city(this, \''+table+'\', '+id+')" oninput="find_in_table(this, \''+table_name+'\', '+id+', \''+table+'\')" type="text">');
            $(win).append('<datalist id="finder-'+table+'"></datalist>');
        },
        'Цена за подключение города': function() {
            info_inputString(undefined, function() {
                let price = parseInt(bufferText);
                set_table_cell(table, 'price', id, price, function() {
                    refrash_table_name('price_cities');
                });
            }, 'Укажите цену за подключения города:', '100', 'Применить');
        },
    };
    info_variants(undefined, lst);
}
function find_in_table(obj, table_name, id_row, table) {
    let txt = $(obj).val();
    SENDER('find_in_table', {table: 'cities', field_name: 'name', txt: txt}, function(mess) {
        mess_executer(mess, function(mess) {
            $('#finder-'+table).html(mess.body);
        });
    });
}
function set_city(obj, table, id) {
    let id_set = $('#finder-'+table+' option[value="'+$(obj).val()+'"]').attr('data-id');
    $('.finder-form').remove();
    set_table_cell(table, 'city_id', id, id_set, function() {
        refrash_table_name('price_cities');
    });
}