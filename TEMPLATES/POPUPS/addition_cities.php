<?phpINCLUDE_CLASS('SHOPS', 'SHOP');if(!SHOP::is_my_shop($id_shop)) {    echo '<h1 style="max-width: 700px; padding-right: 40px">Запрещено редактирование параметров чужой торговой площадки!..</h1>';} else {    ob_start();        $citys = SQL_ROWS_FIELD(q("SELECT id, name, region, shirota, dolgota FROM cities ORDER BY name ASC"), 'id');        $addition_cities = ADD::get_additional_props('shops', $id_shop, 'city');        foreach($addition_cities as $k=>$v) {            $addition_cities[$k]['CITY'] = $citys[$v['val']]['name'];        }?><style>    .form {        max-width: 600px;    }    .form .sell {        background-color: rgba(0, 227, 81, 0.55);    }    .form #llist li {        padding: 1px 3px;        cursor: pointer;    }    .form #llist li:hover {        outline: 1px solid #1fbcff;    }    .form #llist li:active {        transform: scale(0.95);    }    .form input {        margin: 0;        flex-grow: 2;        padding: 5px;        border: 1px solid #4A55684D;        outline: none;        border-radius: 5px;    }    .addited-list {        margin-top: 10px;        padding: 10px;        background-color: rgba(15, 150, 64, 0.15);        border-radius: 10px;    }    .addited-list li {        position: relative;        padding: 3px 38px 3px 7px;        min-height: 34px;        border-radius: 5px;        box-shadow: 3px 5px 6px rgba(0, 128, 0, 0.2);    }    @media screen and (max-width: 950px) {        .form {            max-width: calc(100% - 10px);        }    }</style><div class="form flex gap-5 column">    <h1 style="padding-right: 60px">Дополнительные города</h1>    <p>За подключение дополнительного города с вашего счёта будет списана сумма, в соответствии с прайсом *</p>    <ul class="flex center gap-5 flex-wrap addited-list">        <?php foreach($addition_cities as $k=>$v) {            echo '<li data-id-city="'.$v['val'].'" class="flex gap-5 align-center"><span>'.$v["CITY"].'</span><button onclick="unset_adittional(this, '.$k.')" class="closer"></button></li>';        } ?>    </ul>    <h3>Какой город добавить:</h3>    <input oninput="change_list(this)" list="list" type="text">    <ul id="llist"></ul></div><script>    setTimeout(function() {        $('input[list]').focus();        $(document).on('click', '.popap-closer-btn', function(e) {            location.reload();        })    }, 20);    arr__ = <?=json_encode($citys, JSON_UNESCAPED_UNICODE);?>;    llist__ = $('#llist');    arr__ = Object.values(arr__);    $(document).on('click', '#llist li', function(e) {        let obj = this;        $('input[list]').val($(obj).text());        add_additional($(obj).attr('data-id'), $(obj).text());    });    function add_additional(id, txt) {        if($('.addited-list li[data-id-city="'+id+'"]').length === 0) {            buffer_app = 'SHOPS';            SENDER_APP('add_additional', {id_shop: <?=$id_shop?>, city_id: id}, function(mess) {                mess_executer(mess, function(mess) {                    let last_id = parseInt(mess.body);                    $('.addited-list').append('<li data-id-city="' + id + '" class="flex gap-5 align-center"><span>' + txt + '</span><button onclick="unset_adittional(this, '+last_id+')" class="closer"></button></li>');                });            });        }    }    function unset_adittional(obj, id_addit) {        let name = $(obj).closest('li').find('span').text();        info_qest(undefined, function() {            buffer_app = 'SHOPS';            SENDER_APP('del_from_addit', {shop_id: <?=$id_shop?>, id_addition: id_addit}, function(mess) {                mess_executer(mess, function(mess) {                    $(obj).closest('li').remove();                });            });        }, function() {        }, 'Подтвердите отключение торговой площадки от города <b class="bold-font">"'+name+'"</b>', 'ОТКЛЮЧИТЬ', 'отмена');    }    if(typeof action_123 === 'undefined') {        action_123 = true;        $(document).on('keydown', 'input[list]', function (e) {            switch (e.keyCode) {                case 40:                    count__ = $('#llist li').length;                    index__ = $('#llist li.sell').index();                    ++index__;                    if (index__ >= count__) {                        index__ = 0;                    }                    $('.sell').removeClass('sell');                    $('#llist li:nth-child(' + (index__ + 1) + ')').addClass('sell');                    e.preventDefault();                    break;                case 38:                    count__ = $('#llist li').length;                    index__ = $('#llist li.sell').index();                    --index__;                    if (index__ < 0) {                        index__ = count__ - 1;                    }                    $('.sell').removeClass('sell');                    $('#llist li:nth-child(' + (index__ + 1) + ')').addClass('sell');                    e.preventDefault();                    break;                case 13:                    $('input[list]').val($('li.sell').text());                    add_additional($('li.sell').attr('data-id'), $('li.sell').text());                    e.preventDefault();                    break;            }        })        function change_list(obj) {            llist__.empty();            let max = 15;            let i = 0;            let cur = true;            arr__.forEach(function (item) {                if (item.name.toLowerCase().includes(obj.value.toLowerCase())) {                    if (++i > max) {                        return true;                    }                    if (cur) {                        llist__.append('<li data-id="' + item.id + '" class="sell">' + item.name + '</li>');                        cur = false;                    } else {                        llist__.append('<li data-id="' + item.id + '">' + item.name + '</li>');                    }                }            });        }    }</script><?phpecho ob_get_clean();} ?>