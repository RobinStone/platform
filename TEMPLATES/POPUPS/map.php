<?php
$COORDS = GEONAMER::get_local_position(SITE::$my_place[0]);
$city = SITE::$my_place[0] ?? '';

include_JS_once('/TEMPLATES/JS/class_cdek.js');
?>
<style>
    .place-popap.showed {
        width: 900px;
        height: 600px;
        max-width: calc(100% - 20px);
        max-height: calc(100dvh - 50px);
    }

    .place-popap.showed #map {
        top: 90px;
        min-height: calc(100% - 90px);
    }

    @media screen and (max-width: 950px) {

    }

</style>

<section class="message">

</section>
    <h2 class="h2">Укажите пункт выдачи СДЭК, в котором Вам будет удобно забрать посылку ?</h2>
    <div id="map"></div>
<script>
    myMap = null;

    points = [];
    CDEK = new class_cdek();

    CDEK.get_points_of_city_name('<?=SITE::$my_place[0]?>', function(mess) {
        points = mess.params;
        console.dir(points);

        setTimeout(function(){
            map_coder_init(<?=$COORDS[0]?>, <?=$COORDS[1]?>, '20240930-123402_id-2-197100.png')
        }, 1000);
    });

    function map_coder_init(longitude, latitude, label_img, address='', hint='', baloon='') {
        if ($('#map').children().length === 0) {

            // get_cdek_points()

            setTimeout(function () {
                ymaps.ready(init);

                function init() {
                    myMap = new ymaps.Map("map", {
                        center: [longitude, latitude],
                        zoom: 13,  // от 0 (весь мир) до 19.
                        controls: [
                            'rulerControl', // Линейка
                        ]
                    });

                    // myMap.events.add('click', function (e) {
                    //     // Получаем координаты клика
                    //     const coords = e.get('coords');
                    //     const latitude = coords[0]; // Широта
                    //     const longitude = coords[1]; // Долгота
                    //
                    //     add_placemark(latitude, longitude, label_img);
                    // });
                    setTimeout(function() {
                        console.dir(points);
                        for(let i in points) {
                            let loc = points[i]['location'];
                            console.dir(loc);
                            add_placemark(loc.latitude, loc.longitude, label_img, "Забрать на :<br><b>"+loc.address+"</b>", points[i].code);
                        }
                    }, 100);
                }

                function add_placemark(lat, lon, label_img, hint='', baloon='') {
                    let MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                        '<div style="position: relative; width: 70px; height: 70px; transform: translate(-26px, -37px)">' +
                        '<div style="position: absolute; background: url(/DOWNLOAD/20230729-164009_id-2-778703.svg) no-repeat center; width: 70px; height: 70px; background-size: 100%"></div>' +
                        '<div style="position: absolute; left: 15px; top: 2px; background-color: #ffffff; width: 40px; height: 40px; border-radius: 50%; "></div>' +
                        '<div style="position: absolute; left: 15px; top: 2px; background: url(https://rumbra.ru/IMG/img100x100/' + label_img + ') no-repeat center; border-radius: 50%; width: 40px; height: 40px; background-size: 80%"></div>' +
                        '</div>', {});

                    let myPlacemark = new ymaps.Placemark([lat, lon], {
                        hintContent: hint,
                        balloonContent: baloon,
                    }, {
                        iconContentLayout: MyIconContentLayout,
                        iconImageSize: [70, 70], // размер всей иконки с учетом обоих изображений
                        iconImageOffset: [0, 25],
                        preset: 'islands#emptyIcon', // Скрывает стандартный указатель
                    });

                    myPlacemark.events.add('mouseenter', function (e) {
                        myPlacemark.options.set('iconImageSize', [80, 80]); // Increase size
                        myPlacemark.options.set('iconImageOffset', [0, 20]); // Adjust offset if needed
                    });

                    myPlacemark.events.add('mouseleave', function (e) {
                        myPlacemark.options.set('iconImageSize', [70, 70]); // Reset size
                        myPlacemark.options.set('iconImageOffset', [0, 25]); // Reset offset
                    });

                    myPlacemark.events.add('click', function (e) {
                        info_qest(undefined, function() {
                            // say(baloon, 2);
                            BACK('cdek', 'set_end_point_cdek', {point: baloon}, function(mess) {
                                mess_executer(mess, function(mess) {
                                    close_popup('map');
                                    setTimeout(function() {
                                        let url = new URL(location.href);
                                        url.searchParams.set('delivery', 'places');
                                        location.href = url.toString();
                                    }, 1000);
                                });
                            });
                        }, function() {
                            location.reload();
                        }, 'Подтвердите, что вы хотите забрать посылку по адресу<br><b>'+hint+'</b> ?');
                        console.log(hint);
                    });

                    myMap.geoObjects.add(myPlacemark);
                }

            }, 10);
        }
    }
</script>

