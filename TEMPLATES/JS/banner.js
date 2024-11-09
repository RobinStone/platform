$(document).ready(function () {
    let size_between = 15;
    if(is_mobile()) {
        size_between = 5;
    }


    let swiper_banner = new Swiper('#banner', {
        direction: 'horizontal',
        loop: true,
        slidesPerView: 1.15, // Показываем 1.5 слайда (центрируем один, и видим часть соседних)
        centeredSlides: true, // Центрируем активный слайд
        spaceBetween: size_between, // Устанавливаем расстояние между слайдами
        navigation: {
            nextEl: '.swiper-button-next-banner',
            prevEl: '.swiper-button-prev-banner',
        },
        autoplay: {
            delay: 6000, // Интервал в 6 секунд
            disableOnInteraction: true, // Автопроигрывание не останавливается при взаимодействии
        },
    });
});