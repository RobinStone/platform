let product_page = true;

function show_hidden_chat(show=false) {
    // if($('.btn-chat').is(':visible')) {
    if(status_chat === 'hidden' || status_chat === 'off') {
        status_chat = 'visible';
        $('.btn-chat').css('display', 'none');
        $('.chat-m-wrapper').addClass('slowly');
        setTimeout(function() {
            $('.chat-m-wrapper').removeClass('hidden-in-right');
            $('.status.action-btn').click();
            setTimeout(function() {
                $('.chat-m-wrapper').removeClass('slowly');
            }, 500);
        }, 10);
    } else {
        status_chat = 'hidden';
        $('.btn-chat').css('display', 'flex');
        $('.chat-m-wrapper').addClass('hidden-in-right');
    }
}
function open_product_chat(shop_id) {
    switch(status_chat) {
        case 'off':
            transmision($('.chat-m-wrapper div.status'), 'product', false, shop_id);
            break;
        case 'hidden':
            show_hidden_chat();
            break;
        case 'visible':

            break;
    }
    setTimeout(function() {
        dragElement($('.chat-m-wrapper'));
    }, 300);
}

function subscribes(id) {
    buffer_app='SHOPS';
    SENDER_APP('subscribe_saler', {id: id}, function(mess) {
        mess_executer(mess, function(mess) {
            if(mess.body === 'subscr') {
                $('.user-micro-card .rewiews-block button').text('Отписаться');
                say('Подписка оформлена');
            } else {
                $('.user-micro-card .rewiews-block button').text('Подписаться');
                say('Подписка отменена<br>Вы не будете более получать уведомления от этого продавца.');
            }
        });
    });
}

function show_number(obj, phone_number) {
    $(obj).text(phone_number);
}

function add_rem_basket(obj, code_product) {
    in_basket(code_product, $(obj).find('span:first-child'));
}

function complaint() {
    $('.back-fone').addClass('active');
    setTimeout(function () {
        $('.back-fone').addClass('showed');
        $('.complaint').addClass('showed');
    }, 10);
}
function closed_complaint_form() {
    $('.back-fone').removeClass('showed');
    $('.complaint').removeClass('showed');
    setTimeout(function () {
        $('.back-fone').removeClass('active');
    }, 500);
}

$(document).ready(function(e) {
    if(!$mobile) {
        $('#place').append($('.user-params.b-2'));
    } else {
        $('.img-table.b-2').after($('.right-side'));
        $('.img-table.b-2').after($('h1'));
        $('.map-controlers').after($('#map-place-mobile'));
        $('.profil-btn-action').before($('.likes-carts'));
    }
});
