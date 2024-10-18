function set_map(id_container, longitude, latitude, label_img, hint='', baloon='') {
    setTimeout(function () {
        ymaps.ready(init);

        function init() {
            myMap = new ymaps.Map(id_container, {
                center: [longitude, latitude],
                zoom: 17,  // от 0 (весь мир) до 19.
                controls: [
                    'rulerControl', // Линейка
                ]
            });
            setTimeout(function() {
                add_placemark(longitude, latitude, label_img, hint='', baloon='');
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
            myMap.geoObjects.add(myPlacemark);
        }

    }, 10);
}