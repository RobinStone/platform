$(document).on('click', '.count-lines-1.action-btn', function(e) {
    $('.sell-li').removeClass('sell-li');
    $(this).closest('li').addClass('sell-li');
    swipe_panel();
});

function swipe_panel() {
    const scrollContainer = document.querySelector('.profil');
    scrollContainer.scrollTo({
        left: 6000,
        behavior: 'smooth'
    });
}