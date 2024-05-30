<?php    $citys = SQL_ROWS_FIELD(q("SELECT id, name, region, shirota, dolgota FROM cities ORDER BY name ASC"), 'name');?><section class="my-shops">    <h2 class="h2">Мои площадки</h2>    <?php echo "<div id='alerts'>" . render('alerts') . "</div>"?>    <div class="catalog">    </div></section><datalist></datalist><script>    $(document).on('click', '.shops-profil', function(e) {        let obj = this;        if(!$(this).prop('open') === true) {            let id = $(obj).attr('data-id');            if($(obj).find('.content-shop').children().length === 0) {                add_loader($(obj).find('.content-shop'));                buffer_app = 'SHOPS';                SENDER_APP('get_local_shop', {id: id}, function (mess) {                    mess = JSON.parse(mess);                    if (mess.status !== 'ok') {                        error_executing(mess);                    } else {                        $(obj).find('.content-shop').html(mess.params);                        save_map_shops();                    }                });            }        } else {            save_map_shops();        }    });    SENDER_APP('get_my_shops', {params: <?=json_encode($params, JSON_UNESCAPED_UNICODE);?>}, function(mess) {        mess = JSON.parse(mess);        if(mess.status !== 'ok') {            error_executing(mess);        } else {            $('.catalog').html(mess.params);            load_map_shops();        }    });    if(!isset_script('general_files')) {        include_js_script('general_files');    }    function save_map_shops() {        let map = [];        $('.shops-profil').each(function(e,t) {            if($(t).hasAttr('open')) {                let key = $(t).attr('data-id');                map.push(key)            }        });        localStorage.setItem('map_shops', JSON.stringify(map));    }    function load_map_shops() {        let map = localStorage.getItem('map_shops');        if(map !== null) {            map = JSON.parse(map);            for(let i in map) {                $('.shops-profil[data-id="'+map[i]+'"]').find('summary').click();            }        }    }    function check_day(obj) {        let from = $(obj).closest('tr').find('td:nth-child(3) input[type="time"]');        let to = $(obj).closest('tr').find('td:nth-child(4) input[type="time"]');        if($(obj).prop('checked')) {            from.parent().removeClass('disabled');            to.parent().removeClass('disabled');            from.val('08:00');            to.val('16:00');        } else {            from.parent().addClass('disabled');            to.parent().addClass('disabled');            from.val('--:--');            to.val('--:--');        }    }    function save_worktime(id_shop) {        let arr = [];        $('.work-time table tr:not(:first-child)').each(function(e,t) {            let day = $(t).find('td:first-child').text();            let from = $(t).find('td:nth-child(3) input').val();            let to = $(t).find('td:nth-child(4) input').val();            if($(t).find('input[type="checkbox"]').prop('checked')) {                arr.push({                    'DAY': day,                    'FROM': from,                    'TO': to,                    'TYPE': '+'                });            } else {                arr.push({                    'DAY': day,                    'FROM': '----',                    'TO': '----',                    'TYPE': '-'                });            }        });        console.dir(arr);        set_new_param_for_shop(id_shop, 'worktime', arr, function() {            close_popup('worktime');        });    }</script>