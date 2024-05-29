<?
$SHOPS = SHOP::get_all_my_shops(Access::userID());
$free = false;
if(is_array($SHOPS)) {
    foreach ($SHOPS as $v) {
        if ($v['title'] === 'Бесплатный') {
            $free = true;
        }
    }
}
?>
<section class="my-orders">
    <h2 class="h2">Тарифы</h2>
    <div onclick="mobile_back_row()" class="mobire-back-row flex align-center gap-10">
        <span class="svg-wrapper"><?=RBS::SVG('left_long_row')?></span>
        <span>Назад</span>
    </div>
    <ul class="rates-list flex gap-15 between">
        <li class="flex column gap-20 <? if($free) { echo 'disabled'; } ?>">
            <h3>Базовый</h3>
            <ul class="flex column items gap-15">
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Количесво объявлений -<br>2 шт</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Аналитика спроса</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('neg_in_circ')?></span>
                    <span>Наличие статистики</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('neg_in_circ')?></span>
                    <span>Добавление дополнительных контактов</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('neg_in_circ')?></span>
                    <span>Подключение сотрудников</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('neg_in_circ')?></span>
                    <span>Возможность оплаты на странице компании</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('neg_in_circ')?></span>
                    <span>Скрытие конкурентов в ваших объявлениях</span>
                </li>
            </ul>
            <div class="price">Бесплатно</div>
            <button onclick="buy_tarif('free', this)" class="btn">Выбрать</button>
        </li>
        <li class="flex column gap-20">
            <h3>Витрина</h3>
            <ul class="flex column items gap-15">
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Количесво объявлений -<br>50 шт</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Аналитика спроса</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Наличие статистики</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Добавление дополнительных контактов</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('neg_in_circ')?></span>
                    <span>Подключение сотрудников</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('neg_in_circ')?></span>
                    <span>Возможность оплаты на странице компании</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('neg_in_circ')?></span>
                    <span>Скрытие конкурентов в ваших объявлениях</span>
                </li>
            </ul>
            <div class="price"><?=$plane_showcase?> Р</div>
            <button onclick="buy_tarif('showcase', this)" class="btn">Выбрать</button>
        </li>
        <li class="flex column gap-20">
            <h3>Интернет-магазин</h3>
            <ul class="flex column items gap-15">
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Количесво объявлений -<br>неограничено</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Аналитика спроса</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Наличие статистики</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Добавление дополнительных контактов</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Подключение сотрудников</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Возможность оплаты на странице компании</span>
                </li>
                <li class="flex gap-10 align-center">
                    <span class="svg-wrapper"><?=RBS::SVG('ok_in_circ')?></span>
                    <span>Скрытие конкурентов в ваших объявлениях</span>
                </li>
            </ul>
            <div class="price"><?=$plane_shop?> Р</div>
            <button onclick="buy_tarif('shop', this)" class="btn">Выбрать</button>
        </li>
    </ul>
</section>
