$(document).on('click', '.complaint li', function(e) {
    $('.complaint button.disabled').removeClass('disabled');
});

function buy_form(shop_id, product_id, order_params) {
    console.log('SHOP_ID=' + shop_id + ' PRODUCT_ID=' + product_id);
    $('#order-map').empty();
    $('.back-fone').addClass('active');
    setTimeout(function () {
        $('.back-fone').addClass('showed');
        $('.buy-form').addClass('showed');
    }, 10);

    let coords = get_coords_of_order(order_params);
    console.dir(coords);

    map_coder_order_init(coords.longitude,
        coords.latitude,
        order_params['SHOP']['logo']);

    $('div[data-type="self"]').click();

    const input = document.querySelector("#phoner");
    window.intlTelInput(input, {
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
        initialCountry: code,
        preferredCountries: ["ru", "ua", "by"],
    });
}

function closed_form() {
    $('.back-fone').removeClass('showed');
    $('.buy-form').removeClass('showed');
    setTimeout(function () {
        $('.back-fone').removeClass('active');
    }, 500);
}

function map_coder_order_init(longitude, latitude, label_img, address = '', hint = '', baloon = '') {
    ymaps.ready(init_order);
    say(label_img);
    function init_order() {
        let myMap_order = new ymaps.Map("order-map", {
            center: [longitude, latitude],
            zoom: 17,  // от 0 (весь мир) до 19.
            controls: [
                'rulerControl', // Линейка
            ]
        });

        let MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
            '<div style="position: relative; width: 70px; height: 70px; transform: translate(-26px, -37px)">' +
            '<div style="position: absolute; background: url(/DOWNLOAD/20230729-164009_id-2-778703.svg) no-repeat center; width: 70px; height: 70px; background-size: 100%"></div>' +
            '<div style="position: absolute; left: 15px; top: 2px; background-color: #ffffff; width: 40px; height: 40px; border-radius: 50%; "></div>' +
            '<div style="position: absolute; left: 15px; top: 2px; background: url(https://rumbra.ru/IMG/img100x100/' + label_img + ') no-repeat center; border-radius: 50%; width: 40px; height: 40px; background-size: 80%"></div>' +
            '</div>', {});

        // say('[' + address + ']');
        if (address === '' || address === '-') {
            let myPlacemark = new ymaps.Placemark([longitude, latitude], {
                hintContent: hint,
                balloonContent: baloon,
            }, {
                iconContentLayout: MyIconContentLayout,
                iconImageSize: [70, 70], // размер всей иконки с учетом обоих изображений
                iconImageOffset: [0, 25],
            });
            // После того как метка была создана, добавляем её на карту.
            myMap_order.geoObjects.add(myPlacemark);
        } else {
            myMap_order.geocode(address, {
                results: 1
            }).then(function (res) {
                let firstGeoObject = res.geoObjects.get(0);
                let coords = firstGeoObject.geometry.getCoordinates();
                console.dir(coords);

                myMap_order.setCenter(coords, 15);

                let myPlacemark = new ymaps.Placemark(coords, {
                    hintContent: hint,
                    balloonContent: baloon,
                }, {
                    iconContentLayout: MyIconContentLayout,
                    iconImageSize: [70, 70], // размер всей иконки с учетом обоих изображений
                    iconImageOffset: [0, 25],
                });
                // После того как метка была создана, добавляем её на карту.
                myMap_order.geoObjects.add(myPlacemark);
            });
        }
    }
}

function get_coords_of_order(order_arr) {
    let ans = {
        longitude:0,
        latitude:0,
    }
    for(let i in order_arr['VALS']) {
        if(order_arr['VALS'][i]['name'] === 'Широта') { ans.longitude = parseFloat(order_arr['VALS'][i]['VALUE']); }
        if(order_arr['VALS'][i]['name'] === 'Долгота') { ans.latitude = parseFloat(order_arr['VALS'][i]['VALUE']); }
    }
    return ans;
}

function show_title(obj) {
    let type = $(obj).closest('.time-delivery').attr('data-type');
    let txt = $(obj).attr('title');
    $(obj).closest('label').find('input').click();
    $('#cont').text(txt);
    switch(type) {
        case 'self':
        case 'service':
            $('#data-address').css('display', 'none');
            $('#data-ofice').css('display', 'none');
            break;
        case 'courier':
            $('#data-address').css('display', 'table-row');
            $('#data-ofice').css('display', 'table-row');
            break;
    }
}

function send_compare(obj) {
    let estimate = $('.complaint input:checked + span').text();
    if(estimate === 'Другое') {
        estimate = $('.text-comp').val();
    }
    SENDER('send_bad_request', {txt: estimate, code_prod: order_params.ID}, function(mess) {
        mess_executer(mess, function(mess) {
            say('Ваша жалоба отправлена администраторам ресурса');
            closed_complaint_form();
            setTimeout(function() {
                location.reload();
            }, 4000);
        });
    });
}
