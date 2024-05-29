let buffer_folder_name = 'GLOBAL';

$(document).on('dragover', '#loader-zone', function (e) {
    $(this).addClass('load-in');
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('dragstart', '.buffer-file-dragger', function (e) {

});
$(document).on('dragleave', '#loader-zone', function (e) {
    $(this).removeClass('load-in');
    e.preventDefault();
    e.stopPropagation();
});
$(document).on('drop', '#loader-zone', function (e) {
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
    let folder_name = '-';
    if($('.folder-sel').length > 0) {
        let txt = $('.folder-sel td').text();
        if(txt !== 'GLOBAL') {
            folder_name = txt;
        }
    }
    // Получить список загружаемых файлов
    let files = dt.files;
    for (let file of files) {
        upload_files_in_folder(file, folder_name);
    }
    console.dir(files);
});

$(document).on('dblclick', '#table-files tr', function(e) {
    let obj = this;
    $('#player').attr('src', '/DOWNLOAD/'+$(obj).attr('data-src'));
    setTimeout(function() {
        document.getElementById('player').play();
    }, 200);
});

function update_folders() {
    $('#table-folder').empty();
    let tbl = $('<table></table>');
    for(let i in tracks) {
        let tr = $('<tr onclick="$(\'.folder-sel\').removeClass(\'folder-sel\'); $(this).addClass(\'folder-sel\')"><td onclick="update_files(\''+i+'\')">'+i+'</td></tr>');
        $(tbl).append(tr);
    }
    $('#table-folder').append(tbl);
    setTimeout(function() {
        if($('.folder-sel').length > 0) {
            update_files(buffer_folder_name);
        } else {
            $('#table-folder td:contains("'+buffer_folder_name+'")').closest('tr').click();
            update_files(buffer_folder_name);
        }
    }, 300);
    $('#table-folder').append('<div id="loader-zone"><div id="indicators"></div><span>Перетащите сюда один<br>или несколько файлов,<br>или кликните по этой надписи.</span></div>');
}

function update_files(folder_name) {
    buffer_folder_name = folder_name;
    let row = document.getElementById('menu-files-row').content.cloneNode(true);
    $('#table-files').empty();
    $('#table-files').append(row);
    let tbl = $('<table></table>');
    if(typeof tracks[folder_name] === 'undefined') {
        console.log('Не найдена папка - ['+folder_name+']', 2);
        return false;
    }
    let obj = tracks[folder_name];
    for(let i in obj) {
        let tr = $('<tr data-id="'+obj[i]['id']+'" data-src="'+obj[i]['sys_name']+'"><td class="inpt-check"><input type="checkbox"></td><td>'+obj[i]['user_name']+'</td></tr>');
        $(tbl).append(tr);
    }
    $('#table-files').append(tbl);
}

function update_all_tracks(call_back=function() {}) {
    SENDER_APP('get_all_tracks', {}, function(mess) {
        mess_executer(mess, function(mess) {
            tracks = mess.params;
            call_back();
        });
    });
}

$(document).on('click', '#table-files td.inpt-check', function(e) {
    let obj = this;
    let i = -1;
    if (e.shiftKey) {
        if($('#table-files td.inpt-check input:checked').length >= 2) {
            $('#table-files tr').each(function(e,t) {
                if($(t).find('td.inpt-check input').prop('checked')) {
                    if(i === -1) {
                        i=1;
                    } else {
                        i=-1;
                    }
                } else {
                    if(i === 1) {
                        $(t).find('td.inpt-check input').prop('checked', true);
                    } else {
                        $(t).find('td.inpt-check input').prop('checked', false);
                    }
                }
            });
        }
    }
});

function sell_all() {
    if($('.inpt-check input:checked').length > 0) {
        $('.inpt-check input').each(function(e,t) {
            $(t).prop('checked', false);
        });
    } else {
        $('.inpt-check input').each(function(e,t) {
            $(t).prop('checked', true);
        });
    }
}
function invertation() {
    $('.inpt-check input').each(function(e,t) {
        if($(t).prop('checked')) {
            $(t).prop('checked', false);
        } else {
            $(t).prop('checked', true);
        }
    });
}

function change_folder() {
    let lst = {};
    $('#table-folder td').each(function(e,t) {
        lst[$(t).text()]=function() {
            buffer_folder_name = $(t).text();
            move_files_in_folder(buffer_folder_name);
        };
        lst['Создать новую папку'] = function() {
            info_inputString(undefined, function() {
                buffer_folder_name = bufferText;
                move_files_in_folder(buffer_folder_name);
            }, 'Введите имя новой папки:', '', 'Создать');
        };
    });
    info_variants(transform_pos('center'), lst, 'Укажите папку перемещения или создайте новую:');
}

function move_files_in_folder(folder_name) {
    buffer_app = 'MUSIC';
    let list_ids = [];
    $('#table-files tr').each(function(e,t) {
        if($(t).find('td.inpt-check input').prop('checked')) {
            list_ids.push(parseInt($(t).attr('data-id')));
        }
    });
    SENDER_APP('move_files_in_folder', {folder_name: folder_name, arr: list_ids}, function(mess) {
        mess_executer(mess, function(mess) {
            console.dir(mess);
            update_all_tracks(function() {
                update_folders();
                update_files(folder_name);
            });
        });
    });
}

function del_complite() {
    buffer_app = 'MUSIC';
    let list_ids = [];
    $('#table-files tr').each(function(e,t) {
        if($(t).find('td.inpt-check input').prop('checked')) {
            list_ids.push(parseInt($(t).attr('data-id')));
        }
    });
    SENDER_APP('del_complite', {arr: list_ids}, function(mess) {
        mess_executer(mess, function(mess) {
            console.dir(mess);
            update_all_tracks(function() {
                update_folders();
                update_files(buffer_folder_name);
            });
        });
        $('.text-input-wrapper input').val('');
    });
}

function update_complite() {
    update_all_tracks(function() {
        update_folders();
        update_files(buffer_folder_name);
    });
}

function finder_track(obj) {
    let txt = $(obj).val();
    if(txt.length >= 2) {
        $('.folder-sel').removeClass('folder-sel');
        buffer_folder_name = '';
        $('#table-folder table').addClass('disabled');
        let tbl = $('#table-files table');
        SENDER_APP('find', {txt: txt}, function(mess) {
            mess_executer(mess, function(mess) {
                // console.dir(mess);
                if($('#table-files table').length === 0) {
                    $('#table-files').append('<table></table>');
                }
                if(typeof mess.params !== 'undefined') {
                    $(tbl).empty();
                    for(let i in mess.params) {
                        let obj = mess.params[i];
                        let tr = $('<tr data-id="'+obj['id']+'" data-src="'+obj['sys_name']+'"><td class="inpt-check"><input type="checkbox"></td><td>'+obj['user_name']+'</td></tr>');
                        $(tbl).append(tr);
                    }
                }
            });
        });
    } else {
        $('#table-folder table').removeClass('disabled');
        $('#table-files table').empty();
    }
}