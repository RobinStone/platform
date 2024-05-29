<?php
$banners = SQL_ROWS_FIELD(q("
SELECT * FROM `banner` WHERE
`owner`=".Access::userID()."
ORDER BY `id` DESC
"), 'id');

foreach($banners as $k=>$v) {
    $css = $v['css'] ?? [];
    if($css !== []) {
        $banners[$k]['css'] = unserialize($css);
    }
}
?>
<section class="my-orders" style="max-width: unset">
    <h2 class="h2">Заказ баннера</h2>
    <div class="text" style="margin-bottom: 20px">
        Размеры для изображения баннера должны быть следующими:<br>
        ширина - от <b>1200</b> до <b>800</b> пикселей<br>
        высота - <b>230</b> пикселей<br>
        <i style="font-size: 14px; margin-top: 10px; display: inline-block">
            Будьте внимательны! Информативная часть баннера не должна<br>превышать ширину в 800 пикселей.
        </i>
    </div>
    <div onclick="mobile_back_row()" class="mobire-back-row flex align-center gap-10">
        <span class="svg-wrapper"><?=RBS::SVG('left_long_row')?></span>
        <span>Назад</span>
    </div>
    <div class="banners-rates flex column gap-5">
        <details open>
            <summary style="position: relative">
                <h2>Мои баннеры (<?=count($banners)?>)</h2>
                <div style="margin-left: auto" class="flex gap-5">
                    <button onclick="create_banner()" class="btn btn-just">Создать</button>
                </div>
            </summary>
            <div class="banner-list">
        <?php
        $dt = date('Y-m-d H:i:s');
        foreach($banners as $v) {
            $not_action = "";
            if($v['action_to'] < $dt) {
                $not_action = " not-action ";
            }
            $to_arr = explode('-', explode(' ', $v['action_to'])[0]);
            $to = $to_arr[2]." ".VALUES::intToMonth($to_arr[1], true).", ".$to_arr[0];
            ?>
            <div data-id="<?=$v['id']?>" class="banner-item <?=$not_action?>">
                <div class="flex gap-10 banner-header">
                    <span class="action-to">до <?=$to?></span>
                </div>
                <div class="banner-img" style="background: url('/DOWNLOAD/<?=$v['img']?>')">
                    <div class="banner-content">
                        <h3 class="text-shadow-white"><?=$v['title']?></h3>
                        <div class="descr text-shadow-white count-lines-2"><?=$v['descr']?></div>
                    </div>
                </div>
                <div class="menu flex flex-wrap gap-5">
                    <?php if($v['show_main'] == 1) {
                        echo '<button onclick="hidden_from_main('.$v['id'].','.$summ_show_at_main.')" class="btn btn-just">Скрыть с главной</button>';
                     } else
                        echo '<button class="btn btn-just" onclick="show_at_main('.$summ_show_at_main.', '.$v['id'].')">Показать на главной</button>';
                         ?>
<!--                    <button onclick="change_content(this, 'title')" class="btn btn-just">Название</button>-->
<!--                    <button onclick="change_content(this, 'descr')" class="btn btn-just">Описание</button>-->
                    <button onclick="change_content(this, 'link')" class="btn btn-just">Ссылка</button>
<!--                    <button onclick="set_attr(this)" class="btn btn-just">Надписи</button>-->
                    <button onclick="new_img_for_banner(this)" class="btn btn-just">Изображение</button>
                    <button onclick="new_time(this, <?=$v['id']?>)" class="btn btn-just">Разместить</button>
                    <button onclick="del_banner(<?=$v['id']?>)" class="btn btn-just flex gap-10 align-center"><span class="svg-wrapper" style="width: 20px; height: 20px"><?=RBS::SVG('closer.svg')?></span><span>Удалить</span></button>
                </div>
            </div>
        <?php } ?>
            </div>
        </details>
    </div>
</section>

<template id="banner-editor">
    <div class="banner-editor">
        <div class="coord-btns">
            <button onclick="dest(1)"><span></span><span></span><span></span></button>
            <button onclick="dest(2)"><span></span><span></span><span></span></button>
            <button onclick="dest(3)"><span></span><span></span><span></span></button>
            <button onclick="dest(4)"><span></span><span></span><span></span></button>
            <button onclick="dest(5)"><span></span><span></span><span></span></button>
            <button onclick="dest(6)"><span></span><span></span><span></span></button>
            <button onclick="dest(7)"><span></span><span></span><span></span></button>
            <button onclick="dest(8)"><span></span><span></span><span></span></button>
            <button onclick="dest(9)"><span></span><span></span><span></span></button>
        </div>
        <input style="display: inline-block; margin: 10px 0 5px" oninput="change_padding(this, 'h')" type="range" value="0" min="0" max="10" step="0.05">
        <input style="position: absolute; left: 90px; top: 55px; display: inline-block; margin: 10px 0 5px; transform: rotate(90deg)" oninput="change_padding(this, 'v')" type="range" value="0" min="0" max="10" step="0.05">
        <input style="position: absolute; left: 181px; top: 11px; display: inline-block;" oninput="change_color_(this)" type="color">
        <label style="position: absolute; left: 181px; top: 55px; display: inline-block; cursor: pointer">
            <input onchange="shadow_onoff(this)" style="display: inline-block; width: 20px; height: 20px" type="checkbox">
            <span style="display: inline-block; font-size: 12px; line-height: 6px">Контурная<br></br>тень</span>
        </label>
        <button class="btn" style="min-width: 100px; margin-top: 15px" onclick="save_baner_attr(this)">Сохранить</button>
    </div>
</template>

<script>
    banners = <?=json_encode($banners, JSON_UNESCAPED_UNICODE)?>;
    memory = {};

    for(let i in banners) {
        let css = banners[i]['css'];
        if(css !== null) {
            let ban = $('.banner-item[data-id="' + i + '"]').find('.banner-img');
            ban.css('justify-content', css['justify']);
            ban.css('align-items', css['align']);
            ban.css('padding', css['padding']);
            ban.find('.banner-content').css('text-align', css['text_align']);
            ban.find('.banner-content h3').css('color', css['color']);
            ban.find('.banner-content .descr').css('color', css['color']);
            ban.css('filter', css['filter']);
        }
    }

    setTimeout(function() {
        if(isset_script('general_files') === false) {
            include_js_script('general_files');
        }
    }, 50);

    function new_img_for_banner(obj) {
        let target = $(obj).closest('.banner-item').find('.banner-img');
        let id = $(obj).closest('.banner-item').attr('data-id');
        general_executer = 'loader_banner';
        place_to_indicators_container_tag = target;    //  тег для поиска контейнера с индикаторами (работает, если агружаем по кнопке)
        params_general = {
            id_banner: id,
        };
        set_accept_types_loading(['img']);
        final_loading_ok = function() {
            location.reload();
            say('Новый баннер - загружен')
        };
        $('#general-input-file').click();
    }

    function create_banner() {
        if($('.banner-item').length < 5) {
            buffer_app = 'BANNER';
            SENDER_APP('create_banner', {}, function(mess) {
                mess_executer(mess, function(mess) {
                    location.reload();
                })
            });
        } else {
            say('Максимально-допустимое колличество баннеров = 5', 2);
        }
    }

    function change_content(obj, types) {
        let last = '';
        let types_name = '';
        switch(types) {
            case 'title':
                last = $(obj).closest('.banner-item').find('.banner-img h3').text();
                types_name = 'Изменение названия баннера ';
                break;
            case 'descr':
                last = $(obj).closest('.banner-item').find('.banner-img .descr').text();
                types_name = 'Изменение описания баннера';
                break;
            case 'link':
                last = $(obj).closest('.banner-item').find('.banner-img .btn').attr('data-src') || '/';
                types_name = 'Изменение ссылки для баннера';
                break;
        }
        let id = $(obj).closest('.banner-item').attr('data-id');
        info_inputString(transform_pos('center'), function() {
            buffer_app = 'BANNER';
            SENDER_APP('change_content_banner', {id: id, content: bufferText, types: types}, function(mess) {
                mess_executer(mess, function(mess) {
                    switch(types) {
                        case 'title':
                            $(obj).closest('.banner-item').find('.banner-img h3').text(bufferText);
                            break;
                        case 'descr':
                            $(obj).closest('.banner-item').find('.banner-img .descr').text(bufferText);
                            break;
                        case 'link':
                            location.reload();
                            break;
                    }
                })
            });
        }, types_name+'<br><i style="font-size: 11px">(если нужно убрать просто оставьте поле пустым)</i>', last, 'Изменить');
    }

    buffer_banner = null;
    buffer_banner_id = -1;
    function set_attr(obj) {
        buffer_banner = $(obj).closest('.banner-item').find('.banner-img');
        let win = create_window(transform_pos('center'), 'Редактор баннера', function() {
            setTimeout(function() {
                $(win).closest('.window').find('h4').prepend('<div class="logo-table"><img src="/DOWNLOAD/20231203-160853_id-2-519884.svg"></div>');
                $(win).closest('.window').css('width', '300px').css('height', '200px');
                $(win).closest('.window').addClass('baner-editor-win');
                $(win).closest('.window').find('h4').css('padding-left', '50px');
                $(win).closest('.window').find('h4 .minimaze-btn').remove();
                $(win).css('padding', '10px');
                $(win).append(document.getElementById('banner-editor').content.cloneNode(true));
            }, 10);
        });
    }

    function dest(i) {
        let cont = buffer_banner.get(0);
        let into = cont.querySelector('.banner-content');
        switch(i) {
            case 1:
                cont.style.justifyContent = 'left';
                cont.style.alignItems = 'start';
                into.style.textAlign = 'left';
                break;
            case 2:
                cont.style.justifyContent = 'center';
                cont.style.alignItems = 'start';
                into.style.textAlign = 'center';
                break;
            case 3:
                cont.style.justifyContent = 'right';
                cont.style.alignItems = 'start';
                into.style.textAlign = 'right';
                break;
            case 4:
                cont.style.justifyContent = 'left';
                cont.style.alignItems = 'center';
                into.style.textAlign = 'left';
                break;
            case 5:
                cont.style.justifyContent = 'center';
                cont.style.alignItems = 'center';
                into.style.textAlign = 'center';
                break;
            case 6:
                cont.style.justifyContent = 'right';
                cont.style.alignItems = 'center';
                into.style.textAlign = 'right';
                break;
            case 7:
                cont.style.justifyContent = 'left';
                cont.style.alignItems = 'end';
                into.style.textAlign = 'left';
                break;
            case 8:
                cont.style.justifyContent = 'center';
                cont.style.alignItems = 'end';
                into.style.textAlign = 'center';
                break;
            case 9:
                cont.style.justifyContent = 'right';
                cont.style.alignItems = 'end';
                into.style.textAlign = 'right';
                break;
        }
    }

    function change_padding(obj, types) {
        let v = $(obj).val();
        if(types === 'h') {
            $(buffer_banner).css('padding-left', v+'%').css('padding-right', v+'%');
        } else {
            $(buffer_banner).css('padding-top', v+'%').css('padding-bottom', v+'%');
        }
    }

    function change_color_(obj) {
        let v = $(obj).val();
        $(buffer_banner).find('h3').css('color', v);
        $(buffer_banner).find('.descr').css('color', v);
    }

    function shadow_onoff(obj) {
        let cont = $(buffer_banner);
        if($(obj).prop('checked')) {
            buffer_banner.css('filter', 'drop-shadow(0 0 2px rgba(0, 0, 0, 0.5))');
        } else {
            buffer_banner.css('filter', '');
        }
    }

    function save_baner_attr(obj) {
        let arr = {
            justify: buffer_banner.css('justify-content'),
            align: buffer_banner.css('align-items'),
            padding: buffer_banner.css('padding'),
            text_align: buffer_banner.find('.banner-content').css('text-align'),
            color: buffer_banner.find('.banner-content h3').css('color'),
            filter: buffer_banner.css('filter'),
        };


        console.dir(arr);
        buffer_app = 'BANNER';
        SENDER_APP('set_banner_attr', {id: buffer_banner.closest('.banner-item').attr('data-id'), attr: arr}, function(mess) {
            mess_executer(mess, function(mess) {
                location.reload();
            })
        });
    }

    function hidden_from_main(id_banner, summ) {
        info_qest(transform_pos('center'), function() {
            buffer_app = 'BANNER';
            SENDER_APP('banner_hidden_from_main_page', {id: id_banner}, function(mess) {
                mess_executer(mess, function(mess) {
                    location.reload();
                });
            });
        }, function() {

        }, 'Размещение баннера на первой странице будет прекращено,<br>для повторного размещения будет необходимо снова купить размещение.<br>Продолжить?', 'Да - прекратить показ', 'Нет - пускай остаётся');
    }

    function show_at_main(summ, id_banner) {
        if(cash-summ < 0) {
            info_qest(transform_pos('center'), function() {
                let add_summ = summ-cash;
                if(add_summ < min_add_summ) {
                    add_summ = min_add_summ;
                }
                location.href = '/profil?title=bank&summ='+add_summ;
            }, function() {

            }, 'Для размещения на основной странице,<br>необходимо '+summ+' Р,<br>на Вашем счету '+cash+' Р<br>Вы хотите пополнить балланс ?', 'Да - пополнить на '+(summ-cash)+' Р', 'Нет - не буду');
        } else {
            info_qest(transform_pos('center'), function() {
                buffer_app = 'BANNER';
                SENDER_APP('banner_show_at_main_page', {id: id_banner}, function(mess) {
                    mess_executer(mess, function(mess) {
                        location.reload();
                    });
                });
            }, function() {

            }, 'Размещение на первой странице.<br>С Вашего счёта будет списано '+summ+' Р<br>Продолжить?', 'Да', 'Нет');
        }
    }

    function new_time(obj, id_banner) {
        open_popup('set_area_visibility_for_banner', {id_banner: id_banner});
        buffer_banner_id = id_banner;
    }

    // ВНИМАНИЕ !!!
    function sel_selected(type_sel, id) {  // этот метод выбирает не по переданному type_sel а по memory['sel']
        memory['user_sel'][type_sel] = id;
        switch(memory['sel']) {
            case 'maincat':
                append_banner_for(id, -1, -1, 'main_cat');
                break;
            case 'undercat':
                if(type_sel === 'maincat') {  // указываем предыдущий
                    memory['user_sel']['main_cat'] = id;
                    close_popup('sel_place_from_cats');
                    memory['sel_now'] = 'undercat';
                    setTimeout(function () {
                        open_popup('sel_place_from_cats', {sel: 'undercat', memory: memory});
                    }, 400);
                } else {
                    append_banner_for(memory['user_sel']['main_cat'], id, -1, 'under_cat');
                }
                break;
            case 'actionlist':
                if(type_sel === 'maincat') {  // указываем предыдущий
                    memory['user_sel']['main_cat'] = id;
                    close_popup('sel_place_from_cats');
                    memory['sel_now'] = 'undercat';
                    setTimeout(function () {
                        open_popup('sel_place_from_cats', {sel: 'undercat', memory: memory});
                    }, 400);
                } else if(type_sel === 'undercat') {
                    memory['user_sel']['under_cat'] = id;
                    close_popup('sel_place_from_cats');
                    setTimeout(function () {
                        memory['sel_now'] = 'actionlist';
                        open_popup('sel_place_from_cats', {sel: 'actionlist', memory: memory});
                    }, 400);
                } else {
                    append_banner_for(memory['user_sel']['main_cat'], memory['user_sel']['under_cat'], id, 'action_list');
                }
                break;
        }
    }

    function append_banner_for(main_cat, under_cat=-1, action_list=-1, payed_for='main_cat|under_cat|action_list') {
        buffer_app='BANNER';
        SENDER_APP('append_banner_for', {
            main_cat: main_cat,
            under_cat: under_cat,
            action_list: action_list,
            id_banner: buffer_banner_id,
            payed_for: payed_for,
        }, function(mess) {
            mess_executer(mess, function(mess) {
                console.dir(mess);
                close_popup('sel_place_from_cats');
                setTimeout(function() {
                    location.reload();
                }, 500);
            });
        });
    }
</script>