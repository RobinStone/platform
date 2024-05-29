<aside class="seo-panel flex column <?php if(isset($_COOKIE['seo_open']) && $_COOKIE['seo_open'] === 'true') { echo 'opender'; } ?>">
    <button onclick="$('.seo-panel').toggleClass('opender'); save_status_seo_panel()" class="show-hiden action-btn"><?=RBS::SVG('seo')?></button>
    <div class="title-seo-panel"><?=Core::$TYPE_PAGE?></div>
    <fieldset class="<?php if(Core::$TYPE_PAGE === 'shop') { echo 'disabled'; }?>">
        <legend>TITLE</legend>
        <div onclick="select_meta(this, 'title')" class="flex between gap-10 action-btn <? if(Core::$meta_local['title'] === 'local') { echo 'meta-local'; } ?>"><?=Core::$title?></div>
    </fieldset>
    <fieldset class="<?php if(Core::$TYPE_PAGE === 'shop') { echo 'disabled'; }?>">
        <legend>DESCRIPTION</legend>
        <div onclick="select_meta(this, 'description')" class="flex between gap-10 action-btn <? if(Core::$meta_local['description'] === 'local') { echo 'meta-local'; } ?>"><?=Core::$description?></div>
    </fieldset>
    <fieldset class="<?php if(Core::$TYPE_PAGE === 'shop') { echo 'disabled'; }?>">
        <legend>KEYWORDS</legend>
        <div onclick="select_meta(this, 'keywords')" class="flex between gap-10 action-btn <? if(Core::$meta_local['keywords'] === 'local') { echo 'meta-local'; } ?>"><?=Core::$keywords?></div>
    </fieldset>
    <fieldset class="<?php if(Core::$TYPE_PAGE === 'shop') { echo 'disabled'; }?>">
        <legend>H1</legend>
        <div onclick="select_meta(this, 'h1')" class="flex between gap-10 action-btn <? if(Core::$meta_local['h1'] === 'local') { echo 'meta-local'; } ?>"><?=Core::$h1?></div>
    </fieldset>

    <div class="editor-panel flex column gap-10">
        <div contenteditable="false" class="edited-field"></div>
        <div class="info-panel-message">
            <a target="_blank" href="/DOWNLOAD/20231216-101901_id-2-252570.pdf">Читать инструкцию</a>
            <div style="color: #000000">
                Если необходимо избавиться от метки "ЛОКАЛЬЫЙ" <img width="30" height="30" src="/DOWNLOAD/20231216-103350_id-2-810868.png">,
                просто полностью удалите текст в поле формул, оставив его пустым, и нажмите "СОХРАНИТЬ"
            </div>
        </div>
        <div class="control-panel flex gap-5 flex-wrap">
            <a href="/admin" target="_blank" style="margin-right: auto; cursor: pointer; text-decoration: none">ADMIN-PANEL</a>
            <button onclick="set_local_meta(); $(this).closest('.control-panel').find('input').removeClass('disabled')" title="Создание уникального мета-тега для текущей страницы" id="local-meta-creator-btn" style="background-color: #cfedff" class="invisible seo-btns svg-wrapper action-btn"><?=RBS::SVG('pen')?> локальный</button>
            <button onclick="save_meta()" id="save-btn" class="disabled seo-btns svg-wrapper action-btn"><?=RBS::SVG('save')?> сохранить</button>
            <input oninput="change_layer_check(this)" class="for-all disabled" type="checkbox" title="Применить для всего слоя">
        </div>
    </div>
</aside>

<script>
    meta_info_type = 'global';
    page = <?=json_encode($page, JSON_UNESCAPED_UNICODE)?>;
    commands = <?=json_encode($commands, JSON_UNESCAPED_UNICODE)?>;
    meta_local = <?=json_encode($local_meta, JSON_UNESCAPED_UNICODE)?>;
    buffer_meta_id = <?=$page['id'] ?? -1;?>;

    if(buffer_meta_id === -1) {
        say('У данной страницы отсутствует подключение к панели SEO', 3);
    }

    console.dir(page);
    console.dir(commands);
    console.dir(meta_local);


    $(document).on('keydown', '.editor-panel', function(e) {
        console.log(e.keyCode);
    });

    function save_status_seo_panel() {
        setTimeout(function() {
            if($('.opender').length > 0) {
                setCookies('seo_open', 'true');
            } else {
                setCookies('seo_open', 'false');
            }
        }, 10);
    }

    function select_meta(obj, type) {
        $('.edited-field').attr('contenteditable', 'false');
        $('#save-btn').addClass('disabled');
        let pnl = $('.edited-field');
        $('.yellow-marker').removeClass('yellow-marker');
        $(obj).addClass('yellow-marker');
        $('.edited-field').text('');
        if($(obj).hasClass('meta-local')) {
            meta_info_type = 'local';
            $('.control-panel').find('input').removeClass('disabled');
            if(typeof meta_local[type] !== "undefined") {
                buffer_type_meta = type;
                pnl.html(meta_local[type]);
                formaiting(pnl);
                $('.edited-field').attr('contenteditable', 'true');
                $('.edited-field').focus();
            }
        } else {
            meta_info_type = 'global';
            $('.control-panel').find('input').addClass('disabled');
            if(page !== {}) {
                if(typeof page[type] !== 'undefined') {
                    buffer_type_meta = type;
                    pnl.html(page[type]);
                    formaiting(pnl);
                    $('.edited-field').attr('contenteditable', 'true');
                    $('.edited-field').focus();
                }
            }
            if(!isEmpty(meta_local)) {
                $('#local-meta-creator-btn').removeClass('invisible');
            }
        }
    }

    function formaiting(container) {
        console.log(container);
        let text = $(container).text();
        let inputs = text.replace(/\[(.*?)\]/g, '[<span contenteditable="false" class="inpt">$1</span>]');
        $(container).html(inputs);
    }

    $(document).on('click', 'span.inpt', function(e) {
        let lst = {};
        let obj = this;
        for(let i in commands) {
            lst['<label class="flex column meta-item"><span>'+commands[i]['name']+'</span><span class="meta-descr">'+commands[i]['descr']+'</span></label>'] = function() {
                $(obj).text(commands[i]['name']);
                $('#save-btn').removeClass('disabled');
            };
        }
        info_variants(undefined, lst, 'Укажите переменную для подстановки:', 'meta-panel');
    });

    const container = document.querySelector('.edited-field');

    container.addEventListener('dblclick', function(event) {
        const target = event.target;
        // say(target.tagName);
        if (target.tagName === 'DIV' && $(target).closest('.edited-field').attr('contenteditable') === 'true') {
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
            formaiting($('.edited-field'));
        }
    });

    $(document).on('input', '.edited-field', function(e) {
        $('#save-btn').removeClass('disabled');
    });

    function save_meta() {
        let for_all = $('.for-all').prop('checked') || false;

        $('#saver-btn').removeClass('red-shine');
        let arr = {
            id: buffer_meta_id,
            type: buffer_type_meta,
            text: $('.edited-field').text()
        }
        buffer_app = 'SEO';
        if(meta_info_type === 'global') {
            SENDER_APP('save_meta', arr, function (mess) {
                mess_executer(mess, function () {
                    say('Успешно сохранено');
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                });
            });
        }
        if(meta_info_type === 'local') {
            let arr = {
                id: parseInt(meta_local['id']),
                type: buffer_type_meta,
                type_meta: meta_local['type_category'],
                text: $('.edited-field').text(),
                for_all: for_all,
            }
            console.dir(arr);

            SENDER_APP('save_meta_local', arr, function (mess) {
                mess_executer(mess, function () {
                    say('Успешно сохранено');
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                });
            });
        }
    }

    function set_local_meta() {
        $('.yellow-marker').addClass('meta-local');
        $('#local-meta-creator-btn').addClass('invisible');
        $('.edited-field').text('');
        $('.yellow-marker').click();
    }

    function change_layer_check(obj) {
        if($(obj).prop('checked')) {
            $(obj).prop('checked', false);
            console.dir(meta_local);
            buffer_app = 'SEO';
            SENDER_APP('get_layer_items_for', {id: meta_local.id, type: meta_local.type_category}, function(mess) {
                mess_executer(mess, function(mess) {
                    let txt = mess.params.join(', ');
                    info_qest(transform_pos('center'), function() {
                        $(obj).prop('checked', true);
                    }, function() {

                    }, 'К текущему слою, принадлежат следующие компоненты:<br><br><b style="background-color: yellow; display: inline-block; padding: 2px 5px">'+txt+'</b><br><br>Продолжим ?', 'Да', 'Нет');
                });
            });
        }
    }
</script>