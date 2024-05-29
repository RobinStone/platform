class memory_pull {    constructor() {        this.mem = {};    }    save() {        let i = 0;        let ob = this;        let mem = {};        setTimeout(function() {            $('.table-db').each(function(e,t) {                if($(t).closest('.child-table').length === 0) {                    let params = ob.get_param_table(t);                    if (params.table_name !== 'COMPILED' && params.minimaze === false) {                        mem[i] = params;                        ++i;                    }                }            });            ob.auto_save(mem);            let minimize_btns = [];            $('.ico-panel .minimaze-btn-ico').each(function(e, t) {                minimize_btns.push({                    name: $(t).attr('data-min-table'),                    title: $(t).attr('data-min-title'),                    src: $(t).find('img').attr('src'),                });            });            localStorage.setItem('minimize_btns', JSON.stringify(minimize_btns));        }, 500);        let apps = {};        $('.app-wrapper').each(function(e,t) {            if($(t).css('display') !== 'none') {                let rect = $(t).get(0).getBoundingClientRect();                apps[$(t).find('.content').attr('data-name-app')] = {                    x: rect.x + (rect.width / 2),                    y: rect.y + (rect.height / 2),                    w: rect.width,                    h: rect.height                }            }        });        localStorage.setItem('apps', JSON.stringify(apps));    }    get_param_table(obj_table) {        let ans = {};        if($(obj_table).closest('.window').length > 0) {            let win = $(obj_table).closest('.window').get(0);            let rect = win.getBoundingClientRect();            ans = {                type: 'dynamic',                left: rect.x+(rect.width/2),                top: rect.y+(rect.height/2),                width: rect.width,                height: rect.height,                paginator: $(win).find('.paginator').attr('data-paginator-item'),                table_name: $(obj_table).attr('data-name'),                table_title: $(obj_table).attr('data-title'),            };            if($(win).hasClass('minimaze-mode')) {                ans.minimaze = true            } else {                ans.minimaze = false;            }        } else {            ans = {                type: 'static',                table_name: $(obj_table).attr('data-name'),                paginator: $(obj_table).closest('.table-wrapper').find('.paginator').attr('data-paginator-item'),            };        }        return ans;    }    auto_save(arr) {        localStorage.setItem('memory_pull', JSON.stringify(arr));        // say('saved');    }    load() {        let arr = JSON.parse(localStorage.getItem('memory_pull'));        for(let i in arr) {            switch(arr[i].type) {                case 'dynamic':                    let win = create_window(undefined, arr[i].table_name);                    let dir = $('<div class="table-wrapper"></div>');                    $(win).html(dir);                    load_table_in(arr[i].table_name, dir, function(e) {                        $(dir).closest('.content').removeClass('content');                        $(dir).closest('.window').css('left', arr[i].left+'px').css('top', arr[i].top+'px').css('width', 2+arr[i].width+'px').css('height', 3+arr[i].height+'px');                        $(dir).closest('.window').find('.paginator').attr('data-paginator-item', arr[i].paginator);                        $(dir).closest('.window').find('.table-wrapper').css('max-height', (arr[i].height-55)+'px');                        let p = parseInt(arr[i].paginator);                        if(p > 1) {                            $(dir).closest('.window').find('.refresh-table').click();                        }                    });                    break;                case 'static':                    $('section.table .table-wrapper .paginator').attr('data-paginator-item', arr[i].paginator);                    $('section.table .table-wrapper .refresh-table').click();                    break;            }        }        let minimize_btns = localStorage.getItem('minimize_btns');        if(minimize_btns !== null) {            let arr = JSON.parse(minimize_btns);            // console.log('BUTTONS');            // console.dir(arr);            for(let i in arr) {                let btn = $('<button title="'+arr[i].title+' ('+arr[i].name+')" data-min-table="'+arr[i].name+'" data-min-title="'+arr[i].title+'" class="minimaze-btn-ico"><img src="'+arr[i].src+'"></button>');                $('.ico-panel').append(btn);                setTimeout(function() {                    $(btn).addClass('show-btn');                    let tbtn_ = $(btn).get(0);                    tbtn_.addEventListener("click", function(e){                        e.preventDefault();                        let win = create_window({x: window.innerWidth/2, y: window.innerHeight/2}, arr[i].name);                        let dir = $('<div class="table-wrapper"></div>');                        load_table_in(arr[i].name, dir, function(e) {                            $(win).html(dir);                            $(dir).closest('.content').removeClass('content');                            border_corector($(win).closest('.window'));                            let ico = $(win).find('.table-db').attr('data-ico');                            if(ico.toString() !== '') {                                if($(win).closest('.window').length > 0) {                                    $(win).closest('.window').find('h4').prepend('<div class="logo-table"><img src="'+ico+'"></div>');                                    let txt = $(win).find('.table-db').attr('data-title')+' ('+$(win).find('.table-db').attr('data-name')+')';                                    $(win).closest('.window').find('h4 b').text(txt);                                    $(win).closest('.window').find('h4').css('padding-left', '44px');                                }                            }                        });                        $(tbtn_).remove();                        setTimeout(function() {                            mem.save();                        }, 200);                    }, false);                }, 100);            }        }        let apps = localStorage.getItem('apps');        if(apps !== null) {            apps = JSON.parse(apps);            for(let i in apps) {                $('button[data-name-btn="'+i+'"]').click();                setTimeout(function() {                    let win = $('.content[data-name-app="'+i+'"]').closest('.app-wrapper');                    $(win).css('left', apps[i].x+'px').css('top', apps[i].y+'px').css('width', apps[i].w+'px').css('height', apps[i].h+'px');                }, 100);            }        }    }}