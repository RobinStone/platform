console.log('ADMIN_PANEL - is ACTIVE');
let buffer_type = '';
let buffer_obj = null;
let buffer_id = -1;
let buffer_type_loading = '';

$(document).ready(function() {
    if($('#uploader-loader').length > 0) {
        document.querySelector('#uploader-loader').onchange = function (event) {
            const files = event.target.files;
            for (let file of files) {
                if (file.size <= 11 * 1024 * 1024) {
                    console.dir(file);
                    uploader(file, buffer_obj, buffer_type, buffer_id);
                } else {
                    say("Разрешено загружать файлы до 11 Мб", 2);
                }
            }
        };
    }
});


$(document).on('mouseenter', '.editor-place', function(e) {
    e.stopPropagation();
    e.preventDefault();
});
$(document).on('click', '.editor-place', function(e) {
    let obj = this;
    let id = $(obj).attr('data-id');
    let types = $(obj).attr('data-type');
    let txt = $(obj).parent().find('a span').text();
    if(txt === '') {
        txt = $(obj).parent().find('.name-cat').text();
    }
    e.stopPropagation();
    e.preventDefault();
    let lst = {
        'Изменить название': function() {
            info_inputString(undefined, function() {
                SENDER('change_cataloger_item_name', {type: $(obj).attr('data-type'), id: id, new_name: bufferText}, function(mess) {
                    mess_executer(mess, function(mess) {
                        location.reload();
                    });
                });
            }, 'Введите новое значение', txt, 'Изменить');
        },
        'Загрузить новое изображение': function() {
            buffer_obj = obj;
            buffer_type = types;
            buffer_id = id;
            $('#uploader-loader').click();
        },
        'Загрузить БАННЕР': function() {
            buffer_obj = obj;
            buffer_type = types;
            buffer_id = id;
            buffer_type_loading = 'banner';
            console.log('buffer_type='+buffer_type);
            console.log('buffer_id='+buffer_id);
        },
    };
    info_variants(undefined, lst);
});

$(document).on('dragover', '.editor-place', function (e) {
    $(this).addClass('load-in');
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('dragleave', '.editor-place', function (e) {
    $(this).removeClass('load-in');
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('drop', '.editor-place', function (e) {
    buffer_obj = $(this).closest('.swiper-slide');
    buffer_type = $(this).attr('data-type');
    buffer_id = $(this).attr('data-id');

    $('.load-in').removeClass('load-in');
    let o = this;
    let dt = null;
    if (typeof e.originalEvent === 'object') {
        dt = e.originalEvent.dataTransfer;
    } else {
        dt = e.clipboardData;
    }
    e.stopPropagation();
    e.preventDefault();
    if (!dt && !dt.files) {
        return false;
    }
    // Получить список загружаемых файлов
    let files = dt.files;
    for (let file of files) {
        uploader(file, buffer_obj, buffer_type, buffer_id);
    }
    console.dir(files);
});

function uploader(file, obj, type, id) {
    console.log('Файл ------------');
    if(file.type !== 'image/png' && file.type !== 'image/webp' && file.type !== 'image/jpeg') {
        say('Для данной загрузки необходимы только файлы (.png или .webp)', 2);
        return false;
    }
    console.dir(file);
    let ind = $('<div class="ind"><span>'+file.name+'</span><div class="ind-ind"></div></div>')
    $(obj).append(ind);

    const formData = new FormData();
    formData.append("userfile", file);
    formData.append("com", 'files');
    formData.append("table", 'file');
    formData.append("column", 'none');
    formData.append("change_cataloger_image", type);
    formData.append("cataloger_id", id);
    formData.append("id", -1);

    const xhr = new XMLHttpRequest();
    xhr.upload.onprogress = function(event) {
        let percent = 100 - Math.round(event.total - event.loaded) / event.total * 100;
        percent = Math.round(percent);
        $(ind).find('.ind-ind').css('width', percent+'%');
        // console.log('Загрузка данных: '+percent+' %');
    };
    xhr.onload = xhr.onerror = function() {
        if (this.status === 200) {
            let mess = JSON.parse(xhr.response);
            console.dir(mess);
            if(mess.status === 'ok') {
                $(ind).remove();
                if($(buffer_obj).find('a img').length > 0) {
                    $(buffer_obj).find('a img').remove();
                }
                $(buffer_obj).find('a').append('<img src="/IMG/img300x300/'+mess.sys_name+'">');
                say('Изображение загружено, если ничего не изменилось - обновите страницу.');
            }
        } else {
            say('Не удалось отправить файл');
        }
    };
    xhr.open("POST", domain, true);
    xhr.responseType = 'text';
    xhr.send(formData);
}

