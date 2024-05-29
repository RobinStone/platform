$(document).on('click', '.smile-btns button', function(e) {
    let obj = this;
    $('#message-m').val($('#message-m').val()+$(obj).text());
});

setTimeout(function() {
    dragElement($('.chat-m-wrapper'));
    console.log('drag=MODE');
}, 200);

$(document).on('dblclick', '.drag-field', function(e) {
    dragElement($('.chat-m-wrapper'));
    say('drag=MODE');
})

mess_come.subscribe(function(mess) {
    if(status_chat === 'hidden' || status_chat === 'off') {
        say('Пришло сообщение в чат...');
    }
});

open_closed_chat.subscribe(function(obj) {
    console.log(obj);
    setTimeout(function() {
        border_corector(obj);
    }, 100);
});
