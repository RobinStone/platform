class class_cdek {
    get_citys(city, callback) {
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

    get_points_of_city_name(city_name, callback) {
        this.get_citys(city_name, (mess) => {
            if(typeof mess !== 'undefined' && mess.length > 0) {
                let point = mess[0];
                this.get_points(point.id, callback);
            } else {
                say('По локлизации "'+city_name+'" пунктов выдачи CDEK - не найдено...', 2);
            }
        });
    }

    get_points(cdek_city_id, callback) {
        BACK('cdek', 'get_points', {cdek_city_id: cdek_city_id}, function (mess) {
            mess_executer(mess, (mess) => {
                callback(mess);
            });
        });
    }
}