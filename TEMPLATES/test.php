<button onclick="work()">SEND</button>

<script>
    function work() {
        get_citys('красноярск', function(results) {
            console.dir(results);
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