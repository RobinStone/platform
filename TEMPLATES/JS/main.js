setTimeout(function() {    let url = new URL(location.href);    if(url.searchParams.get('auth') === 'true') {        console.log('ok');        auth();    }}, 50);function show_hidden_chat() {    if($('.btn-chat').is(':visible')) {        status_chat = 'visible';        $('.btn-chat').css('display', 'none');        $('.chat-m-wrapper').addClass('slowly');        setTimeout(function() {            $('.chat-m-wrapper').removeClass('hidden-in-right');            $('.status.action-btn').click();            setTimeout(function() {                $('.chat-m-wrapper').removeClass('slowly');            }, 500);        }, 10);        $('#support-btn').css('display', 'none');        $('header.drag-field > div span:nth-child(2)').text('Ожидаем ответа');        if($mobile) {            $(document).on('focus', '#message-m', function(e) {                $('.chat-m-wrapper').addClass('vh50');                scroll_to_bottom($('.chat-m-content'));            });            $(document).on('click', '.chat-m-content', function(e) {                $('.chat-m-wrapper').removeClass('vh50');            });        }    } else {        status_chat = 'hidden';        $('.btn-chat').css('display', 'flex');        $('.chat-m-wrapper').addClass('hidden-in-right');    }}window.addEventListener('scroll', function() {    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {        // Пользователь прокрутил до конца        console.log('Прокручено до конца!..');        show_next_pagination_cards(8);    }});// let initialPoint;// let finalPoint;// document.addEventListener('touchstart', function (event) {//     // event.preventDefault();//     // event.stopPropagation();//     initialPoint = event.changedTouches[0];// }, false);// document.addEventListener('touchend', function (event) {//     // event.preventDefault();//     // event.stopPropagation();//     finalPoint = event.changedTouches[0];//     let xAbs = Math.abs(initialPoint.pageX - finalPoint.pageX);//     let yAbs = Math.abs(initialPoint.pageY - finalPoint.pageY);//     if (xAbs > 120) {//         if (xAbs > yAbs) {//             if (finalPoint.pageX < initialPoint.pageX) {//                 swipeLeft();//             }//         }//     }// }, false);// function swipeLeft() {//     say('inleft');// }// function swipeRight() {//// }