<?php

?>
<style>
    .info-panel {
        z-index: 99999999!important;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=Core::$DOMAIN?>TEMPLATES/JS/general_files.js" type="text/javascript" charset="utf-8"></script>

<div id="admin-panel" class="<?php if(isset($params['open-panel']) && $params['open-panel'] == '1') { echo 'opened'; } ?>">
    <?=RBS::SVG('triangle.svg')?>
    <div class="content flex gap-5">
        <button onclick="open_visual_gallery()" class="adm-pnl-btn action-btn" title="Вызов визуальной галереи">ГАЛЕРЕЯ</button>
        <a href="/admin" target="_blank" class="adm-pnl-btn action-btn" style="text-decoration: none" title="Переход в админ-панель">ADMIN-PANEL</a>
        <button onclick="editor_site(this)" class="adm-pnl-btn <?php if(isset($params['editor']) && $params['editor'] == '1') { echo 'online'; } ?> indicate action-btn">РЕДАКТОР</button>
    </div>
</div>
<input class="invisible" id="uploader-loader" type="file">

<script>
    $(document).on('click', '#admin-panel > svg', function(e) {
        open_close();
    });

    function open_close() {
        if($('#admin-panel').hasClass('opened')) {
            $('#admin-panel').removeClass('opened');
            set_param('open-panel', '0');
        } else {
            $('#admin-panel').addClass('opened');
            set_param('open-panel', '1');
        }
    }

    function set_param(param_panel, argum_panel, call_back=function(mess) {}) {
        SENDER('set_param_panel', {param: param_panel, argum: argum_panel}, function(mess) {
            mess_executer(mess, function(mess) {
                call_back(mess);
                console.dir(mess);
            });
        });
    }

    function editor_site(obj) {
        if($(obj).hasClass('online')) {
            set_param('editor', '0', function() {
                $(obj).removeClass('online');
                location.reload();
            });
        } else {
            set_param('editor', '1', function() {
                $(obj).addClass('online');
                location.reload();
            });
        }
    }

    function open_visual_gallery() {
        SENDER('get_visual_gallery_list', {}, function(mess) {
            mess_executer(mess, function(mess) {
                console.dir(mess);
                let win = create_window(transform_pos('center'), 'Визуальная галерея', function() {
                    setTimeout(function() {
                        $(win).closest('.window').find('h4').prepend('<div class="logo-table"><img src="/DOWNLOAD/20231203-160853_id-2-519884.svg"></div>');
                        $(win).closest('.window').css('width', '537px').css('height', '595px');
                        $(win).closest('.window').find('h4').css('padding-left', '50px');
                        $(win).closest('.window').find('h4 .minimaze-btn').remove();
                        $(win).append(mess.params);
                    }, 10);
                });
            });
        });
    }

    function open_code_editor(fragment_name, edit_code='<h1>Hello world !!!</h1>', type_code='mixed') {
        let types = 'ace/mode/html';
        switch(type_code) {
            case 'html':
                types = 'ace/mode/html';
                break;
            case 'js':
                types = 'ace/mode/js';
                break;
            case 'css':
                types = 'ace/mode/css';
                break;
            default:
                types = 'ace/mode/php';
                break;
        }

        editor = ace.edit(fragment_name);
        editor.setTheme("ace/theme/terminal");
        editor.setFontSize(16);
        let session = editor.getSession();
        session.setMode(types);
        editor.setValue(edit_code);
        setInterval(function() {
            editor.resize();
        }, 1000);
    }

    function sel_style() {
        let themes = {
            'monokai': 'ace/theme/monokai',
            'chrome': 'ace/theme/chrome',
            'clouds': 'ace/theme/clouds',
            'crimson_editor': 'ace/theme/crimson_editor',
            'dawn': 'ace/theme/dawn',
            'dreamweaver': 'ace/theme/dreamweaver',
            'eclipse': 'ace/theme/eclipse',
            'github': 'ace/theme/github',
            'iplastic': 'ace/theme/iplastic',
            'cobalt': 'ace/theme/cobalt',
            'dracula': 'ace/theme/dracula',
            'gruvbox': 'ace/theme/gruvbox',
            'kr_theme': 'ace/theme/kr_theme',
            'pastel_on_dark': 'ace/theme/pastel_on_dark',
            'tomorrow_night': 'ace/theme/tomorrow_night',
            'twilight': 'ace/theme/twilight',
        };
        let lst = {};
        for(let i in themes) {
            lst[i] = function() {
                editor.setTheme(themes[i]);
            }
        }
        info_variants(transform_pos('center'), lst);
    }

    function load_code(fragment_name, page_path) {
        console.log('Begin update fragment code...');
        if($('.ace_text-input').length === 0) {
            SENDER('get_edit_fagment', {fragment_name: fragment_name, page_path: page_path}, function (mess) {
                mess_executer(mess, function (mess) {
                    open_code_editor(fragment_name, mess.params.code, mess.params.types);
                    $('#' + fragment_name).css('min-height', '70vh').css('z-index', '9999999');
                    $('#'+fragment_name).append('<div class="left-panel"><button title="Изменяет стиль написания кода. Просто меняет цвет редактора." onclick="sel_style()">Изменить стиль</button><button title="Сохраняет код и применяет этот блок на сайте." onclick="save_code(this)" style="background-color: lime">Сохранить код</button><button title="Ничего не меняет на сайте, просто загружает исходный код в редактор. Если не нажать СОХРАНИТЬ ничего не изменится." onclick="update_old(this)">Загрузить исходный</button><button title="Удаляет последне сохранение этого фрагмента и востанавливает предыдущую версию этого участка на сайте." onclick="back_code(this)">Откатить последнее сохранение<button style="background-color: red" onclick="location.reload()">ВЫХОД</button></div>');
                    setOverlayJust();
                    $('#'+fragment_name).closest('.fragment-line').addClass('fixator-editor');
                })
            });
        } else {
            $('.ace_editor').addClass('slow-not-look');
            setTimeout(function() {
                location.reload();
            }, 500);
        }
    }

    function save_code(obj) {
        let page = $(obj).closest('.fragment-line').attr('data-page');
        let fr_name = $(obj).closest('.fragment-line').attr('data-fr-name');
        let fr_text = editor.getValue();

        info_qest(undefined, function() {
            SENDER('save_edit_fragment', {fragment_name: fr_name, page_path: page, fragment_text: fr_text}, function (mess) {
                mess_executer(mess, function (mess) {
                    location.reload();
                });
            });
        }, function() {

        }, 'Подтвердите сохранение.', 'ДА - сохранить', 'НЕТ - не сохранять');
    }

    function update_old(obj) {
        let page = $(obj).closest('.fragment-line').attr('data-page');
        let fr_name = $(obj).closest('.fragment-line').attr('data-fr-name');

        SENDER('get_default_fragment', {fragment_name: fr_name, page_path: page}, function (mess) {
            mess_executer(mess, function (mess) {
                set_mode_editor(mess.params.types)
                editor.setValue(mess.params.code);
            });
        });
    }

    function set_mode_editor(mode) {
        let types = 'ace/mode/html';
        switch(mode) {
            case 'html':
                types = 'ace/mode/html';
                break;
            case 'js':
                types = 'ace/mode/js';
                break;
            case 'css':
                types = 'ace/mode/css';
                break;
            default:
                types = 'ace/mode/php';
                break;
        }
        let session = editor.getSession();
        session.setMode(types);
    }

    function back_code(obj, steps=1) {
        let page = $(obj).closest('.fragment-line').attr('data-page');
        let fr_name = $(obj).closest('.fragment-line').attr('data-fr-name');

        info_qest(undefined, function() {
            SENDER('back_code_steps', {fragment_name: fr_name, page_path: page, steps: steps}, function (mess) {
                mess_executer(mess, function (mess) {
                    console.dir(mess);
                    set_mode_editor(mess.params.types)
                    editor.setValue(mess.params.code);
                    if((Object.keys(mess.params.errors).length > 0)) {
                        for(let i in mess.params.errors) {
                            say(mess.params.errors[i], 2);
                        }
                    }
                });
            });
        }, function() {

        }, 'Последнее сохранение будет отменено и фрагмент откатится к предыдущему сохранению.', 'ПОДТВЕРДИТЬ', 'Оставить как есть');
    }
</script>