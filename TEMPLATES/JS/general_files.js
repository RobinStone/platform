general_executer = 'loader-in-file';                    //  название файла обработчика по адресу /CONTROLLERS/UPLOADER_GENERAL_EXECUTERS
place_to_indicators_container_tag = '.gallery-list';    //  тег для поиска контейнера с индикаторами (работает, если агружаем по кнопке)
params_general = {                                      //  иначе - игнорирует и находит его сам в том месте куда делался drop
    name_key: 'value1',
};                                                      //  параметры в виде ключ значение для передачи

access_upload_files_extension = 'all';                  //  ПЕРЕМЕННАЯ ДЛЯ ХРАНЕНИЯ РАЗРЕШЁННЫХ, ДЛЯ ЗАГРУЗКИ, ФАЙЛОВ

final_loading_error = function() {
    // say('Что-то явно пошло не так!...', 3);
};

final_loading_ok = function() {
    try {
        refrash_table_name('file');
    } catch (e) {
        console.log('На этой странице обработчик не работает');
    }
    $('button[onclick="update_gallery(this)"]').click();
};

set_accept_types_loading('all');


///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
// ДЛЯ ЗАГРУЗКИ ПО КЛИКУ
//
//     $('#general-input-file').click();  --- ВОТ ТАК

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
function set_accept_types_loading(types=['img', 'image', 'audio', 'video', 'docx', 'pdf', 'all']) {
    access_upload_files_extension = types;
    let arr = [];
    for(let i in types) {
        switch(types[i]) {
            case 'img':
            case 'image':
                arr.push('image/*');
                break;
            case 'audio': arr.push('audio/*');  break;
            case 'video': arr.push('video/*');  break;
            case 'docx': arr.push('.docx');  break;
            case 'pdf': arr.push('.pdf');  break;
            case 'all': arr = [];  break;
        }
    }
    if(arr.length > 0) {
        $('#general-input-file').attr('accept', arr.join(', '));
    }
}


// $(document).ready(function() {
    console.log('loaded general-fields');
    if(typeof input === 'undefined') {
        input = $('<input id="general-input-file" type="file" multiple="multiple" style="display: none;">');
    }

    $(document).on('change', '#general-input-file', function() {
        let files = Array.from(this.files);
        let place_indicators = $(place_to_indicators_container_tag);
        files.forEach(function(file) {
            upload_start(file, place_indicators);
        });
    });

    $(document).on('dragover', '.upload-zone', function(e) {
        $(this).addClass('load-in');
        e.preventDefault();
        e.stopPropagation();
    });
    $(document).on('dragleave', '.upload-zone', function(e) {
        $(this).removeClass('load-in');
        e.preventDefault();
        e.stopPropagation();
    });
    $(document).on('drop', '.upload-zone', function (e) {
        insert_file(e, this);
        $('.load-in').removeClass('load-in');
        e.preventDefault();
        e.stopPropagation();
    });

    function insert_file(e, container_If_Isset=null) {
        let dt = null;
        if(typeof e.originalEvent === 'object') {
            dt = e.originalEvent.dataTransfer;
        } else {
            dt = e.clipboardData;
        }
        if(!dt && !dt.files) { return false ; }
        let files = dt.files;
        for (let i = 0; i < files.length; i++) {
            if(access_upload_files_extension === 'all' || files[i]['type'].split('/')[0] === access_upload_files_extension) {
                if (files[i].size <= 1073741824) {
                    // console.log('############');
                    // console.dir(files[i]);
                    let nm = files[i].name.split('.')[files[i].name.split('.').length - 1];
                    upload_start(files[i], container_If_Isset);
                } else {
                    say('Максимальный размер загружаемого файла = 1гб', 2);
                }
            } else {
                say('Допущено к загрузке только изображения', 2);
            }
        }
    }

    function upload_start(file, upload_container=null) {
        let bar = $('<div class="bar-indicator" style="width: 0; height: 10px; background-color: green;"></div>');
        if($('.bar-container').length > 0) {
            $('.bar-container').append(bar);
        } else {
            if(upload_container !== null) {
                $(upload_container).append('<div class="bar-container" style="' +
                    'min-height: 10px; width: 200px; background-color: #c8fff6; padding: 5px; ' +
                    'position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); ' +
                    'box-shadow: 5px 5px 10px rgba(0,0,0,0.35)"></div>');
                $(upload_container).find('.bar-container').append(bar);
            } else {
                console.error('Не указан контейнер для индикатора загрузки.');
            }
        }
        let ajax = new XMLHttpRequest();
        ajax.upload.onprogress = function(event) {
            // console.log('Загр- ' + event.loaded + ' из ' + event.total);
            // console.log('loaded = '+event.loaded);
            let percent = 100 - Math.round(event.total - event.loaded) / event.total * 100;
            percent = Math.round(percent);
            // console.log('Загрузка данных: '+percent+' %');
            $(bar).css('width', percent+'%');
        };

        ajax.onload = ajax.onerror = function() {
            if (this.status === 200) {
                console.log('All ok');
            } else {
                say('Не удалось отправить файл');
            }
            let mess = JSON.parse(ajax.response);
            console.dir(mess);
            if(mess.status !== 'ok') {
                say(mess.text,2);
                $(bar).remove();
                if($('.bar-indicator').length === 0) {
                    if(upload_container !== null) {
                        $(upload_container).find('.bar-container').remove()
                    }
                }
                final_loading_error();
            } else {
                $(bar).remove();
            }
            if(mess.status === 'ok' || mess.status === 'clear') {
                if($('.bar-indicator').length === 0) {
                    console.info('Загрузка завершена');
                    if(upload_container !== null) {
                        $(upload_container).find('.bar-container').remove()
                    }
                    final_loading_ok(mess);
                } else {
                    say('Файл загружен.<br>Ожидаем загрузку всех файлов...');
                }
            }
        };
        let formData = new FormData();
        formData.append("userfile", file);
        formData.append("com", 'files');
        formData.append("executer", general_executer);
        for(let ii in params_general) {
            formData.append(ii, params_general[ii]);
        }
        ajax.open("POST", domain, true);
        ajax.responseType = 'text';
        ajax.send(formData);
    }

    $('body').append(input);
// });