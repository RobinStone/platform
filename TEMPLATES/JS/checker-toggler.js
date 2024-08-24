// данный скрипт опрашивает лементы с классом checker-toggler и блокирует или разрешает показ нижнего элемента

$(document).ready(function() {
    rescan_bool_rows();
});

$(document).on('click', '.checker-toggler', function(e) {
    rescan_bool_rows();
});

function rescan_bool_rows() {
    let togglers = $('.checker-toggler');

    togglers.each(function(e,t) {
        let stat = parseInt($(t).attr('data-real'));
        if(stat === 1) { stat = 0; } else { stat = 1; }
        let next_all = $(t).nextAll('tr');
        console.dir(next_all);
        if(!next_all.eq(0).hasClass('clear-row')) {
            if(stat === 1) {
                next_all.eq(0).addClass('disabled-row');
                next_all.eq(1).removeClass('disabled-row');
            }
        }
        if(!next_all.eq(1).hasClass('clear-row')) {
            if(stat === 0) {
                next_all.eq(1).addClass('disabled-row');
                next_all.eq(0).removeClass('disabled-row');
            }
        }
    });
}