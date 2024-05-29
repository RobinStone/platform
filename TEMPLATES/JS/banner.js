$(document).ready(function () {
    let swiper_banner = new Swiper('#banner', {
        direction: 'horizontal',
        loop: true,
        slidesPerView: 1,
        slidesPerGroup: 1,
        navigation: {
            nextEl: '.swiper-button-next-banner',
            prevEl: '.swiper-button-prev-banner',
        },
        autoplay: {
            delay: 6000, // Интервал в 3 секунды
        },
    });
});