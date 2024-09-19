function search_context_in_shop(obj) {
    let txt = $(obj).closest('.find-field').find('input').val();
    if(txt.length < 2) {
        txt = '-';
    }
    let arr = txt.split(' ');
    BACK('finder', 'find_in_shop', {words_arr: arr, shop_id: shop_id}, function(mess) {
        mess_executer(mess, function(mess) {
            console.dir(mess);
            location.reload();
        });
    })
}