<?php
//t($plane_showcase);
//t($plane_shop);
INCLUDE_CLASS('shops', 'shop');
$shop = SHOP::get_shop($id_shop);
$title = $shop['title'];
?>
<style>
    .place-popap {
        overflow: hidden;
        position: relative;
    }
    .plans {
        max-width: calc(100vw - 2em);
        width: 800px;
        position: relative;
    }
    .plans li {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }
    .plans header {
        font-size: 1.5em;
        font-weight: 600;
    }
    .self-text {
        padding: 0 10px 0;
        text-align: center;
    }
    .plans footer {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
        width: 100%;
    }
    .plans footer button.btn {
        min-width: 40%;
    }
    .self-text b {
        color: green;
        font-weight: 800;
    }
    li.circle {
        position: absolute;
        z-index: -1;
        background-color: green;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        filter: blur(149px);
        left: -2%;
        top: 44%;
        pointer-events: none;
    }
    li.circle.blue {
        background-color: #002480;
        left: 65%;
        top: 10%;
    }

    @media screen and (max-width: 950px) {
        .plans {
            box-sizing: border-box;
            max-height: 85vh;
            overflow-y: auto;
            max-width: calc(100vw - 45px);
            justify-content: flex-start;
        }
    }

</style>

<ul class="flex column gap-10 plans center">
    <li class="circle"></li>
    <li class="circle blue"></li>
    <li>
        <header>"БЕСПЛАТНЫЙ"</header>
        <div class="self-text">
            Тариф <b>"Бесплатный"</b> предоставляет возможность размещения до двух бесплатных объявлений.
            Объявления, размещенные с этого тарифа, будут отображаться в общем пространстве поиска
            и не будут иметь персональной доски объявлений с собственным URL-адресом. Вы сможете
            размещать объявления о различных товарах, услугах или событиях, и они будут доступны
            для просмотра всем пользователям платформы.
        </div>
        <footer>
            <button onclick="set_plan(<?=$id_shop?>, 'Бесплатный', 0)" class="btn">ПОНИЗИТЬ ПЛАН</button>
        </footer>
    </li>
    <li>
        <header>"ВИТРИНА"</header>
        <div class="self-text">
            Тариф <b>"Витрина"</b> предоставляет возможность размещения объявлений с собственной площадкой и собственным URL-адресом.
            Вы сможете выбрать логотип, изменить название витрины и создать свой собственный сайт с уникальным оформлением и
            доменом второго уровня, например, https://rumbra.ru/ваш_сайт.
            Этот тариф предоставляет пользователям персонализированную витрину для их объявлений,
            позволяя создать уникальное онлайн-пространство для продуктов, услуг или событий.
        </div>
        <footer>
            <button onclick="set_plan(<?=$id_shop?>, 'Витрина', <?=$plane_showcase?>)" class="btn">ПОВЫСИТЬ ПЛАН + <?=$plane_showcase?> P</button>
        </footer>
    </li>
    <li>
        <header>"ИНТЕРНЕТ-МАГАЗИН"</header>
        <div class="self-text">
            Тариф <b>"Интернет-магазин"</b> предоставляет все преимущества тарифа "Витрина", включая возможность размещения
            объявлений с собственной площадкой и собственным URL-адресом, выбор логотипа, изменение названия витрины и
            создание собственного сайта с уникальным оформлением и доменом второго уровня (например, https://rumbra.ru/ваш_сайт).
            Кроме того, данный тариф предоставляет возможность продажи онлайн товаров и услуг, что делает его идеальным выбором
            для создания полноценного интернет-магазина с уникальным брендингом и персонализированным онлайн-пространством для
            продуктов и услуг.
        </div>
        <footer>
            <button onclick="set_plan(<?=$id_shop?>, 'Интернет-магазин', <?=$plane_shop?>)" class="btn">ПОВЫСИТЬ ПЛАН + <?=$plane_shop?> P</button>
        </footer>
    </li>
</ul>

