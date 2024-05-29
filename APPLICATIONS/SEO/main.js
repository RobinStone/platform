$(document).on('click', 'span.inpt', function(e) {
    let lst = {};
    let obj = this;
    for(let i in commands) {
        lst['<label class="flex column meta-item"><span>'+commands[i]['name']+'</span><span class="meta-descr">'+commands[i]['descr']+'</span></label>'] = function() {
            $(obj).text(commands[i]['name']);
            $('#saver-btn').addClass('red-shine');
        };
    }
    info_variants(undefined, lst, 'Укажите переменную для подстановки:', 'meta-panel');
});

$(document).on('change', '#page-temp', function() {
    let val = $(this).val();
    let option = $('#pages option[data-val="'+val+'"]').attr('data-id');
    buffer_meta_id = parseInt(option);
    console.log(option);
    let obj = get_page_at_id(option);
    console.dir(obj);
    if(typeof obj.id !== 'undefined') {
        $('.meta-info').css('color', '#9fffa4');
        $('.meta-selector').removeClass('disabled');
        $('#meta-btn-title').click();
        $('#meta-btn-title').focus();
        $('.meta-info').html(obj.descr);
        if(obj.dynamic == '1') {
            $('#del-meta').removeClass('disabled');
        }
    }
});

const container = document.getElementById('meta-box');

container.addEventListener('dblclick', function(event) {
    const target = event.target;
    if (target.tagName === 'DIV' && target.getAttribute('contenteditable') === 'true') {
        const span = document.createElement('span');
        span.className = 'inpt';
        span.textContent = '[NEW]';
        const selection = window.getSelection();
        const range = selection.getRangeAt(0);
        range.deleteContents();
        range.insertNode(span);
        range.collapse(false);
        selection.removeAllRanges();
        selection.addRange(range);

        formaiting($('#meta-box'));
    }
});

$(document).on('change', '#filer', function() {
    let fileInput = document.getElementById("filer");
    let file = fileInput.files[0];
    let formData = new FormData();
    formData.append("textfile", file);
    formData.append("com", 'send_robots');
    formData.append("file_name", $(fileInput).attr('data-name'));
    formData.append("app", 'SEO');  // тут указываем для route.php какому приложению отправляем
    let xhr = new XMLHttpRequest();
    xhr.open("POST", domain, true);
    xhr.responseType = 'text';
    xhr.send(formData);
});

$(document).on('input', '.meta-box', function(e) {
    $('#saver-btn').addClass('red-shine');
});

function save_robots() {
    buffer_app = 'SEO';
    SENDER_APP('get_robots_txt', {}, function(messer) {
        mess_executer(messer, function(messer) {
            info_inputText(transform_pos('center'), function(mess) {
                SENDER_APP('save_robots_txt', {txt: bufferText}, function(mess) {
                    mess_executer(mess, function(mess) {

                    });
                });
            }, 'Данное поле предназначено для ввода текста в robots.txt', messer.params, 'Сохранить');
        });
    });

}
function save_xml() {
    buffer_app = 'SEO';
    SENDER_APP('get_sitemap_xml', {}, function(messer) {
        mess_executer(messer, function(messer) {
            info_inputText(transform_pos('center'), function(mess) {
                SENDER_APP('save_sitemap_xml', {txt: bufferText}, function(mess) {
                    mess_executer(mess, function(mess) {

                    });
                });
            }, 'Данное поле предназначено для ввода кода в sitemap.xml', messer.params, 'Сохранить');
        });
    });
}

function get_page_at_id(id) {
    for(let i in pages) {
        if(pages[i]['id'] == id) {
            return pages[i];
        }
    }
    return {};
}
function set_page_array_at_id(id, type, text) {
    for(let i in pages) {
        if(pages[i]['id'] == id) {
            pages[i][type] = text;
        }
    }
}

function del_page_array_at_id(id) {
    let i =0;
    for(let i in pages) {
        if(pages[i]['id'] == id) {
            pages.splice(i, 1);
            return true;
        }
        ++i;
    }
}

function get_constructor_write(obj, type) {
    $('.sel-btn').removeClass('sel-btn');
    $(obj).addClass('sel-btn');
    buffer_type_meta = type;
    let page = get_page_at_id(buffer_meta_id);
    $('.meta-box').html(page[type]);
    $('.meta-box').attr('contenteditable', 'true');

    formaiting($('.meta-box'));

    setTimeout(function() {
        $('.meta-box').focus();
    }, 10);
    $('#saver-btn').removeClass('red-shine');
}

function clear_meta() {
    $('#page-temp').val('');
    $('#saver-btn').removeClass('red-shine');
    $('.meta-selector').addClass('disabled');
    $('#del-meta').addClass('disabled');
    $('.meta-box').text('');
    $('.meta-box').attr('contenteditable', 'false');
    $('.meta-info').html('<b>Выберете модель</b> названия страницы<br>для которой необходимо изменить данные мета-тега');
    $('.meta-info').css('color', '#cccccc');
}

function save_meta() {
    $('#saver-btn').removeClass('red-shine');
    let arr = {
        id: buffer_meta_id,
        type: buffer_type_meta,
        text: $('.meta-box').text()
    }
    buffer_app = 'SEO';
    SENDER_APP('save_meta', arr, function(mess) {
        mess_executer(mess, function() {
            set_page_array_at_id(arr.id, arr.type, arr.text);
            say('Успешно сохранено');
        });
    });
}

function del_meta() {
    info_qest(undefined, function() {
        buffer_app = 'SEO';
        SENDER_APP('del_meta', {id_meta: buffer_meta_id}, function(mess) {
            mess_executer(mess, function() {
                clear_meta();
                del_page_array_at_id(buffer_meta_id);
            });
        });
    }, function() {

    }, 'Подтвердите удаление данных модуля ['+$('#page-temp').val()+']<br>Его мета-данные будут взяты из дефолтного состояния.<br>Продолжить удаление?', 'Да - удалить', 'Нет - отмена');
}

function formaiting(container) {
    console.log(container);
    let text = $(container).text();
    let inputs = text.replace(/\[(.*?)\]/g, '[<span contenteditable="false" class="inpt">$1</span>]');
    $(container).html(inputs);
}