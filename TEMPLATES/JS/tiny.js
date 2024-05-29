active_stars_scan = false;
$(document).on('mouseenter', '.stars', function(e) {
    active_stars_scan = true;
});
$(document).on('mouseleave', '.stars', function(e) {
    let st = this;
    active_stars_scan = true;
    let stars = parseFloat($(st).attr('data-star'));
    let percents = (parseFloat(stars)*100/5)+'%';
    $(st).find('.star-shore').css('width', percents);
});
$(document).on('mousemove', '.stars', function(e) {
    let st = this;
    console.log('32434234234234');
    if(active_stars_scan) {
        let x = e.pageX - $(st).offset().left;
        $(st).find('.star-shore').css('width', (x * 100 / 250) + '%');
    }
});
function update_stars_draw(obj) {
    let st = this;
    let stars = parseFloat($(obj).attr('data-star'));
    let percents = (stars*100/5)+'%';
    $(obj).find('.star-shore').css('width', percents);
}
$(document).on('touchmove', '.stars', function(e) {
    let st = this;
    let x = e.originalEvent.touches[0].pageX;
    let off = $(st).offset().left;
    let p = (x - off) * 100 / 250;
    if(p > 100) { p = 100; }
    if(p < 0) { p = 0; }
    $(st).find('.star-shore').css('width', p + '%');
    let stars = (parseFloat($(st).find('.star-shore').width())) * 5 / 250;
    $(st).attr('data-star', stars);
});
$(document).on('click', '.stars', function(e) {
    let st = this;
    active_stars_scan = false;
    let stars = (parseFloat($(st).find('.star-shore').width())) * 5 / 250;
    $(st).attr('data-star', stars);
});

function show_editor(html_text, stars=0.00) {
    $('.editor-wrapper').css('display', 'block');
    setTimeout(function() {
        let iframe = document.getElementById('field_ifr');
        let iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        let style = document.createElement('style');
        style.innerHTML = 'body p { margin:0!important; }';
        iframeDoc.head.appendChild(style);

        tinymce.get('field').setContent(html_text);
        $('.editor-wrapper').addClass('editor-visible');

        let percents = '0';
        if(parseFloat(stars) > 0) {
            percents = (parseFloat(stars)*100/5)+'%';
        }

        $('.star-shore').css('width', percents);
        $('.stars').attr('data-star', stars);
        tinymce.get('field').focus();
    }, 10);
}
function hide_editor() {
    $('.editor-wrapper').removeClass('editor-visible');
    setTimeout(function() {
        $('.editor-wrapper').css('display', 'none');
    }, 500);
}
function save_content() {
    let cont = tinymce.get('field').getContent();
    console.log(cont);
    bufferText = cont;
    save_tiny(cont);
}


