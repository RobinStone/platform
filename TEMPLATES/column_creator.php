<div class="form-column-creator flex column center">
    <button onclick="$(this).parent().parent().remove(); del_column_local($('#insert-th'));" class="close-map-btn"></button>
    <h2>Создание колонки <b id="name-column"></b></h2>
    <div class="flex gap-10">
        <fieldset>
            <legend>Оглавление колонки</legend>
            <input oninput="change_trans(this)" type="text" name="column-title">
        </fieldset>
        <fieldset>
            <legend>Column name translitteration</legend>
            <input type="text" name="column-name">
        </fieldset>
    </div>
    <div class="flex gap-10" style="align-items: flex-start">
        <fieldset>
            <legend>Тип информации</legend>
            <select id="types" onchange="sel_type(this)">
                <option disabled selected>выберете нужный тип</option>
                <option data-type="tinyint" value="tinyint">Да / Нет (true | false)</option>
                <option data-type="int" value="int">Целое число (-2, -1, 0, 1, 2)</option>
                <option data-type="double" value="double">Дробное число (-2.15, -1.1, 0, 1.33, 2.55)</option>
                <option data-type="varchar" value="varchar">Строка (до 255 символов)</option>
                <option data-type="text" value="text">Текст (многострочный)</option>
                <option data-type="datetime" value="datetime">Дата / Время (<?=date('Y-m-d H:i:s')?>)</option>
                <option data-type="date" value="date">Дата (<?=date('Y-m-d')?>)</option>
                <option data-type="time" value="time">Время (<?=date('H:i:s')?>)</option>
                <option data-type="varchar" value="select">Список (другая таблица)</option>
                <option data-type="enum" value="enum">Перечисление (ENUM)</option>
                <option data-type="varchar" value="file">Изображение / ресурс (file)</option>
            </select>
        </fieldset>
        <fieldset>
            <legend>Опции</legend>
            <div>
                <fieldset id="default-varchar" class="not-visible">
                    <legend><label class="flex gap-5" style="cursor: pointer"><input oninput="$(this).parent().parent().parent().find('.fill-content').toggleClass('disabled')" style="pointer-events: none" type="checkbox"><span>По умолчанию (строка)</span></label></legend>
                    <div class="fill-content disabled">
                        <input type="text" value="-" style="display: inline-block; min-width: 255px">
                    </div>
                </fieldset>
                <fieldset id="default-int" class="not-visible">
                    <legend>По умолчанию (целое число)</legend>
                    <div class="fill-content">
                        <input type="number" value="0">
                    </div>
                </fieldset>
                <fieldset id="default-datetime" class="not-visible">
                    <legend><label class="flex gap-5" style="cursor: pointer"><input oninput="$(this).parent().parent().parent().find('.fill-content').toggleClass('disabled')" style="pointer-events: none" type="checkbox"><span>По умолчанию (Y-m-d H:i:s)</span></label></legend>
                    <div class="fill-content disabled">
                        <input class="dt" type="datetime-local" value="<?=date('Y-m-d H:i:s')?>">
                    </div>
                </fieldset>
                <fieldset id="default-date" class="not-visible">
                    <legend><label class="flex gap-5" style="cursor: pointer"><input oninput="$(this).parent().parent().parent().find('.fill-content').toggleClass('disabled')" style="pointer-events: none" type="checkbox"><span>По умолчанию (Y-m-d)</span></label></legend>
                    <div class="fill-content disabled">
                        <input class="dt" type="date" value="<?=date('Y-m-d')?>">
                    </div>
                </fieldset>
                <fieldset id="default-time" class="not-visible">
                    <legend><label class="flex gap-5" style="cursor: pointer"><input oninput="$(this).parent().parent().parent().find('.fill-content').toggleClass('disabled')" style="pointer-events: none" type="checkbox"><span>По умолчанию (H:i:s)</span></label></legend>
                    <div class="fill-content disabled">
                        <input class="dt" type="time" value="<?=date('H:i:s')?>">
                    </div>
                </fieldset>
                <fieldset id="default-double" class="not-visible">
                    <legend>По умолчанию (дробное число)</legend>
                    <div class="fill-content">
                        <input type="number" step="0.01" value="0.00">
                    </div>
                </fieldset>
                <fieldset id="default-text" class="not-visible">
                    <legend><label class="flex gap-5" style="cursor: pointer"><input oninput="$(this).parent().parent().parent().find('.fill-content').toggleClass('disabled')" style="pointer-events: none" type="checkbox"><span>По умолчанию (многострочный)</span></label></legend>
                    <div class="fill-content disabled">
                        <textarea>-</textarea>
                        <div class="flex gap-10 type-text" style="padding: 5px 10px; width: 100%">
                            <label class="flex gap-5 center action-btn" style="flex-grow: 2">
                                <input checked type="radio" name="type-text" value="text">
                                <span>Обычный текст</span>
                            </label>
                            <label class="flex gap-5 center action-btn" style="flex-grow: 2">
                                <input type="radio" name="type-text" value="params">
                                <span>Список</span>
                            </label>
                            <label class="flex gap-5 center action-btn" style="flex-grow: 2">
                                <input type="radio" name="type-text" value="json">
                                <span>JSON массив</span>
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset id="default-enum" class="not-visible">
                    <legend class="flex gap-10"><span>По умолчанию (ENUM)</span><button onclick="create_new_enum(this)">✚</button></legend>
                    <div id="enum" class="fill-content flex column"></div>
                </fieldset>
                <fieldset id="default-select" class="not-visible">
                    <legend>По умолчанию (многострочный)</legend>
                    <div class="fill-content">
                        <div class="flex gap-10 between">
                            <span>Привязка к таблице:</span>
                            <select onchange="sel_alter_table_key()" id="alter-table"></select>
                        </div>
                        <div class="flex gap-10 between">
                            <span>Активный ключ (отображаемое поле):</span>
                            <select onchange="load_data_list_values(this)" id="alter-table-key"></select>
                        </div>
                        <div class="flex gap-10 between">
                            <span>Значение по умолчанию (при создании новой записи):</span>
                            <select id="alter-table-value-id"></select>
                        </div>
                    </div>
                </fieldset>
                <fieldset id="default-bool" class="not-visible">
                    <legend>По умолчанию (Да / Нет)</legend>
                    <div class="fill-content">
                        <label>
                            <input type="radio" name="radio-bool" value="1">
                            <span>(true)</span>
                        </label>
                        <label>
                            <input checked type="radio" name="radio-bool" value="0">
                            <span>(false)</span>
                        </label>
                    </div>
                    <div>
                        <label>
                            <div style="font-size: 12px; margin-top: 10px; margin-bottom: 10px">В полях ниже укажите 2 противоположных названия.<br>(напр. Вкл/Выкл, Мужск./Женск, Чёрный/Белый)</div>
                            <input placeholder="true" type="text" name="bool-true" value="Да">
                            <span style="padding-left: 3px">- true</span>
                        </label><br>
                        <label>
                            <input placeholder="false" type="text" name="bool-false" value="Нет">
                            <span style="padding-left: 3px">- false</span>
                        </label>
                    </div>
                </fieldset>
                <fieldset id="default-file" class="not-visible">
                    <legend class="flex gap-10"><span>Изображение / ресурс (file)</span></legend>
                    <div class="fill-content">
                        <h3>Доступные для загрузки расширения:</h3>
                        <div class="flex gap-5 wrap extension-list" style="max-width: 350px">
                            <div class="flex gap-10 input-gap">
                                <label><input type="checkbox" name="extension" value="webp"><b>WEBP</b> (изобр.)</label>
                                <label><input type="checkbox" name="extension" value="jpeg,jpg"><b>JPEG, JPG</b> (изобр.)</label>
                                <label><input type="checkbox" name="extension" value="png"><b>PNG</b> (изобр.)</label>
                                <label><input type="checkbox" name="extension" value="gif"><b>GIF</b> (изобр.)</label>
                            </div>
                            <div class="flex gap-10 input-gap">
                                <label><input type="checkbox" name="extension" value="mp3"><b>MP3</b> (аудио)</label>
                                <label><input type="checkbox" name="extension" value="ogg"><b>OGG</b> (аудио)</label>
                                <label><input type="checkbox" name="extension" value="wav"><b>WAV</b> (аудио)</label>
                                <label><input type="checkbox" name="extension" value="ape"><b>APE</b> (аудио)</label>
                                <label><input type="checkbox" name="extension" value="flac"><b>FLAC</b> (аудио)</label>
                            </div>
                            <div class="flex gap-5 input-gap">
                                <label><input type="checkbox" name="extension" value="mp4"><b>MP4</b> (видео)</label>
                                <label><input type="checkbox" name="extension" value="wmv"><b>WMV</b> (видео)</label>
                                <label><input type="checkbox" name="extension" value="avi"><b>AVI</b> (видео)</label>
                                <label><input type="checkbox" name="extension" value="webm"><b>WEBM</b> (видео)</label>
                            </div>
                            <div class="flex gap-5 input-gap">
                                <label><input type="checkbox" name="extension" value="mp4"><b>TXT</b> (текст)</label>
                                <label><input type="checkbox" name="extension" value="wmv"><b>EXE</b> (исполн.)</label>
                                <label><input type="checkbox" name="extension" value="avi"><b>PDF</b> (докум.)</label>
                                <label title="Данные любых типов."><input type="checkbox" name="extension" value="*"><b>*</b> (любые)</label>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </fieldset>
    </div>
    <button id="btn-creator" onclick="create_new_column_form()" style="margin-top: 1em; align-self: flex-end" class="btn-just">СОЗДАТЬ</button>
</div>

<script>
    buffer_arr = {};

    function change_trans(obj) {
        $('input[name="column-name"]').val(translit($(obj).val(), true));
    }

    function sel_type(obj) {
        $('fieldset fieldset').addClass('not-visible');
        let type = $(obj).find('option:selected').val();
        switch(type) {
            case 'tinyint':
                $('#default-bool').removeClass('not-visible');
                break;
            case 'datetime':
                $('#default-datetime').removeClass('not-visible');
                break;
            case 'date':
                $('#default-date').removeClass('not-visible');
                break;
            case 'time':
                $('#default-time').removeClass('not-visible');
                break;
            case 'varchar':
                $('#default-varchar').removeClass('not-visible');
                break;
            case 'int':
                $('#default-int').removeClass('not-visible');
                break;
            case 'double':
                $('#default-double').removeClass('not-visible');
                break;
            case 'text':
                $('#default-text').removeClass('not-visible');
                break;
            case 'enum':
                $('#default-enum').removeClass('not-visible');
                break;
            case 'file':
                $('#default-file').removeClass('not-visible');
                break;
            case 'select':
                $('#default-select').removeClass('not-visible');
                if($('#alter-table option').length === 0) {
                    SENDER('get_all_tables', {}, function (mess) {
                        mess = JSON.parse(mess);
                        if (mess.status === 'ok') {
                            buffer_arr = mess.params;
                            console.dir(buffer_arr);
                            $('#alter-table').append($('<option>-</option>'));
                            for (let i in buffer_arr) {
                                $('#alter-table').append($('<option>' + i + '</option>'));
                            }
                        } else {
                            error_executing(mess);
                        }
                    });
                }
                break;
        }
    }

    function load_data_list_values(obj, call_back=function() {}) {
        SENDER('load_data_list_values', {table: $('#alter-table').val(), column: $('#alter-table-key').val()}, function(mess) {
            mess = JSON.parse(mess);
            console.dir(mess);
            if(mess.status === 'ok') {
                let name_field = $('#alter-table-key').val();
                $('#alter-table-value-id').empty();
                let p = mess.params;
                $('#alter-table-value-id').append('<option value="-1" selected>-</option>');
                for(let i in p) {
                    $('#alter-table-value-id').append('<option value="'+p[i].id+'">'+p[i][name_field]+'</option>');
                }
                call_back();
            } else {
                error_executing(mess);
            }
        });
    }

    function create_new_enum(obj) {
        $(obj).parent().parent().find('#enum').append($('<label><input type="text"><button onclick="$(this).parent().remove()">✖</button></label>'));
    }

    function sel_alter_table_key(call_back=function(){}) {
        let types = $('#alter-table').val();
        $('#alter-table-key').empty();
        for (let i in buffer_arr[types]) {
            $('#alter-table-key').append($('<option>' + buffer_arr[types][i] + '</option>'));
        }
        call_back();
    }

    function create_new_column_form(edit=false) {
        let arr = {};
        if($('input[name="column-title"]').val().length < 2) {
            say('Оглавление колонки не может быть < 2 символов', 2);
            $('input[name="column-title"]').focus();
            return false;
        }
        arr.title = $('input[name="column-title"]').val();

        if($('input[name="column-name"]').val().length < 2) {
            say('Транслитерация колонки не может быть < 2 символов', 2);
            $('input[name="column-name"]').focus();
            return false;
        }
        arr.name = $('input[name="column-name"]').val();
        arr.old_name = $('input[name="column-name"]').attr('data-old');

        let types = $('#types option:selected').attr('data-type');
        let types_column = $('#types option:selected').val();
        if(types === undefined) {
            say('Тип информации должен быть определён', 2);
            $('#types').focus();
            return false;
        }
        arr.types = $('#types option:selected').attr('data-type');
        arr.types_column = $('#types option:selected').val();

        switch(types_column) {
            case 'tinyint':
                arr.default = {
                    value: parseInt($('#default-bool input[type="radio"]:checked').val()),
                    true: $('input[name="bool-true"]').val(),
                    false: $('input[name="bool-false"]').val(),
                };
                break;
            case 'int':
                arr.default = parseInt($('#default-int input[type="number"]').val());
                break;
            case 'double':
                arr.default = parseFloat($('#default-double input[type="number"]').val()).toFixed(2);
                break;
            case 'varchar':
                arr.default = $('#default-varchar input[type="text"]').val();
                break;
            case 'text':
                arr.default = {
                    value: $('#default-text textarea').val(),
                    type: $('.type-text input:checked').val(),
                }
                break;
            case 'datetime':
            case 'date':
            case 'time':
                if($('#default-'+types_column+' input[type="checkbox"]').prop('checked')) {
                    arr.default = $('#default-' + types_column + ' input.dt').val();
                } else {
                    arr.default = '0000-00-00 00:00:00';
                }
                break;
            case 'select':
                arr.default = {
                    table:  $('#alter-table').val(),
                    field:  $('#alter-table-key').val(),
                    value: $('#alter-table-value-id').val()
                };
                break;
            case 'enum':
                arr.default = {
                    list: [],
                    value: ''
                };
                $('#enum label').each(function(e,t) {
                    let txt = $(t).find('input').val();
                    if(txt === '') { txt = '---'; }
                    arr.default.list.push(txt);
                });
                break;
            case 'file':
                arr.default = {
                    value: '-',
                    list: [],
                };
                $('.extension-list input[type="checkbox"]:checked').each(function(e,t) {
                    arr.default.list.push($(t).val());
                });
                // console.dir(arr);
                break;
            default:
                arr.default = null;
                break;
        }
        try {
            arr.ord = parseInt($('#insert-th').eq().prevObject[0].cellIndex);
        } catch (e) {
            arr.ord = parseInt($('th.sel-column').eq().prevObject[0].cellIndex);
            arr.edited = true;
        }
        arr.table = $(buffer_table).parent().attr('data-name');
        console.dir(arr);
        $('#insert-th').attr('data-column', arr.name);

        setTimeout(function() {
            let lst = {};
            let i = 0;
            $('.table-db[data-name="'+arr.table+'"] tr:first-child th').each(function(e,t) {
                lst[$(t).attr('data-column')] = i++;
            });
            SENDER('set_orders_columns', {table: arr.table, orders: lst}, function(mess) {
                mess = JSON.parse(mess);
                if(mess.status === 'ok') {
                    SENDER('create_new_column', arr, function (mess) {
                        mess = JSON.parse(mess);
                        if(mess.status === 'ok') {
                            load_table_in(arr.table, $(buffer_table).closest('.table-wrapper'));
                        } else {
                            error_executing(mess);
                        }
                        delOvelay();
                    });
                } else {
                    error_executing(mess, 3);
                }
            });
        }, 200);

    }
</script>
