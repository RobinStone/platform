<section class="wrapper">
    <input id="finder-s">
    <button onclick="work()">SEND</button>
    <div class="line"></div>
    <input type="number" id="finder-p">
    <button onclick="get_points()">GET POINTS</button>
</section>


<script>
    function work() {
        get_citys($('#finder-s').val(), function(results) {
            console.dir(results);
        });
    }

    function get_points() {
        BACK('cdek', 'get_points', {cdek_city_id: $('#finder-p').val()}, function(mess) {
            mess_executer(mess, function(mess) {
                console.dir(mess.params);
            });
        });
    }

    function get_citys(city, callback) {
        let res = [];
        if (city) {
            const url = 'https://api.cdek.ru/city/getListByTerm/jsonp.php?q='+city+'&callback=?';
            $.getJSON(url, function (data) {
                if (data && data.geonames && data.geonames.length > 0) {
                    data.geonames.forEach(function (city) {
                        res.push(city);
                    });
                    callback(res);
                } else {
                    // say('Города не найдены.');
                }
            }).fail(function () {
                // say('Ошибка при запросе.', 3);
            });
        } else {
            // say('Пожалуйста, введите город.');
        }
    }
</script>