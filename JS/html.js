$(document).on('click', '.html-toggler', function(e) {
    let obj = $(this);
    let values = {
        yes: obj.attr('data-value-yes'),
        no: obj.attr('data-value-no'),
    };
    if(obj.attr('data-status') === '1') {
        obj.attr('data-status', '0');
        obj.attr('data-value', values.no);
    } else {
        obj.attr('data-status', '1');
        obj.attr('data-value', values.yes);
    }
});

/**
 * Позволяет установить значение в HTML элемент
 *
 * @param obj - любой "родитель" этого контрола
 * @param type - тип контрола (toogle)
 * @param name - имя контрола (даётся при создании в PHP)
 * @param value - значение контрола (зависит от типа)
 */
function html_set_value(obj, type, name, value) {
    obj = $(obj).find('*[data-name="'+name+'"]');
    if(typeof obj !== 'undefined') {
        switch(type) {
            case 'toggle':
                let values = {
                    yes: obj.attr('data-value-yes'),
                    no: obj.attr('data-value-no'),
                };
                if(value === 1) {
                    obj.attr('data-status', '1');
                    obj.attr('data-value', values.yes);
                }
                if(value === 0) {
                    obj.attr('data-status', '0');
                    obj.attr('data-value', values.no);
                }
                break;
        }
    }
}

