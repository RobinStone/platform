//  этот файл необязателен, но он позволяет запустить приложение скрыто
//  даже если на него не нажали

setTimeout(function() {
    let url = new URL(location.href);
    if(url.searchParams.get('get_local_shop') !== null && url.searchParams.get('get_local_shop') !== 'null') {
        start_hidden_app('SHOPS', function() {  // собственно вот это и запускает скрытый запуск
            activate_work_shop(url.searchParams.get('get_local_shop'));
        })
    }
}, 10);