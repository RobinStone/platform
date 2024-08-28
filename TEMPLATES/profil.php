<section class="wrapper profil">
    <div class="columns column-left">
        <?=render('user-micro-card', ['login'=>Access::userName(), 'self'=>true, 'my_subscr_count'=>$my_subscr_count])?>
        <div class="menu-left-profil flex column gap-10">
            <?php if(Access::scanLevel() >= 6) { ?>
                <a title="Панель для супер-админов" href="/admin" target="_blank" style="margin-left: 5px; margin-bottom: 5px; text-decoration: none;" class="action-btn flex align-center gap-10 not-border">
                    <span class="svg-wrapper" style="display: inline-block; width: 30px; height: 30px"><?=RBS::SVG('admin')?></span>
                    <span>Супер-админ панель</span>
                </a>
            <?php } ?>
            <button title="Настройка персональных данных аккаунта (необходимо для правильного отображения ваших товаров)" onclick="open_tab('account')" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('account')?></span>
                <span>Управление аккуантом</span>
            </button>
            <button title="Полноразмерный удобный чат для общения с клиентами" onclick="location.href='/chat'" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('20230530-211804_id-2-746301.svg')?></span>
                <span>Чат</span>
            </button>
            <button title="Выбор платформы, фактически ваш тариф." onclick="open_tab('rate')" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('tarif')?></span>
                <span>Тариф</span>
            </button>
            <?php if($shops_count >= 1) { ?>
            <button title="Настройка внешнего вида вашего магазина, сайта, торговой площадки." onclick="open_tab('my_shops')" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('shop-ico')?></span>
                <span>Мои площадки (<?=$shops_count?>)</span>
            </button>
                <button title="Заказ баннера" onclick="open_tab('banners')" class="action-btn flex align-center gap-10 not-border">
                    <span class="svg-wrapper" style="width: 30px; height: 30px"><?=RBS::SVG('banner_green.svg')?></span>
                    <span>Заказ баннера (<?=$banners_count?>)</span>
                </button>
            <?php } ?>
            <button title="Список ваших объявлений (редактирование, продвижение и т.д.)" onclick="open_tab('my_orders')" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('my_orders')?></span>
                <span>Мои объявления (<?=$all_count_my_lots?>)</span>
            </button>
            <button title="Управление текущим счётом (пополнение, просмотр статистики)" onclick="open_tab('bank')" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('money_box')?></span>
                <span>Кошелёк</span>
            </button>
            <button title="Ваши загруженные файлы (таблицы, изображения, документы)" onclick="open_tab('storage')" class="action-btn flex align-center gap-10 not-border">
                <span style="width: 28px; height: 28px" class="svg-wrapper green-svg"><?=RBS::SVG('files')?></span>
                <span>Мои файлы</span>
            </button>
            <button title="Корзина. Сюда помещаются товары которые вы добавили в корзину. Из корзины удобно делать покупки." onclick="location.href='/basket'" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('bag_box')?></span>
                <span>Корзина (<?=$_SESSION['count_in_basket'] ?? 0;?>)</span>
            </button>
            <button title="Товары которые были куплены у меня." onclick="open_tab('shop_orders')" class="action-btn flex align-center gap-10 not-border">
                <span style="display: inline-block; width: 25px; height: 25px" class="svg-wrapper"><?=RBS::SVG('memorypad.svg')?></span>
                <span>Заказы (<?=$shop_orders_count?>)</span>
            </button>
            <button title="Товары, которые были приобретены мной." onclick="open_tab('purchases')" class="action-btn flex align-center gap-10 not-border">
                <span style="display: inline-block; width: 25px; height: 25px" class="svg-wrapper"><?=RBS::SVG('notepad.svg')?></span>
                <span>Покупки (<?=$purchases_count?>)</span>
            </button>
            <?php if(isMobile()) { ?>
            <button title="Все мои чаты." onclick="location.href='/messenger'" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('mail')?></span>
                <span>Мои сообщения (-)</span>
            </button>
            <?php } ?>
            <button title="Объявления, помеченые мной, как понравившиеся." onclick="open_tab('favorite')" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('hart')?></span>
                <span>Избранное (<?=$favorite_count?>)</span>
            </button>
            <button onclick="support_chat()" class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('tech_help')?></span>
                <span>Обратиться в поддержку</span>
            </button>
            <button class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper"><?=RBS::SVG('docs')?></span>
                <span>Добавить документы</span>
            </button>
            <button class="action-btn flex align-center gap-10 not-border">
                <span class="svg-wrapper green-svg" style="width: 28px; height: 28px"><?=RBS::SVG('API')?></span>
                <span>API</span>
            </button>
            <?php if(isMobile()) { ?>
                <button onclick="location.href='/auth/exit'" class="action-btn flex align-center gap-10 not-border">
                    <span class="svg-wrapper green-svg" style="width: 28px; height: 28px"><?=RBS::SVG('20230508-120328_id-2-455995.svg')?></span>
                    <span>Выход</span>
                </button>
            <?php } ?>

            <div class="line-green"></div>
            <div>Поделится профилем</div>
            <div class="flex align-center gap-15">
                <a class="svg-wrapper"><?=RBS::SVG('facebook')?></a>
                <a class="svg-wrapper"><?=RBS::SVG('tele')?></a>
                <a class="svg-wrapper"><?=RBS::SVG('insta')?></a>
            </div>
        </div>
    </div>

    <div class="columns column-right">
        <?php echo "<div id='alerts'>" . render('alerts') . "</div>"?>
        <div>
            <h2 class="h2">Общие настройки</h2>
            <div class="options">
                <fieldset class="option">
                    <legend>Только в моём городе</legend>
                    <label class="flex gap-10">
                        <input type="checkbox" <?php if(PP::_()->get('only_my_city', 'false') == 'true') { echo 'checked="checked"'; } ?>>
                        <span>Показывать только те объявления, которые относятся к моему городу</span>
                    </label>
                </fieldset>
            </div>
        </div>
    </div>
</section>

<button onclick="open_window()">TEST</button>

<!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params-->

<?php echo render('robot', [
    'header_controller'=>true,  // настройка прячет phone-book в header
    'room'=>'',
    'type_room'=>'personal',
    'params'=>[]
]); ?>


<script>
    cash = <?=PROFIL::init(Access::userID())->get('cash', 0)?>;

    function open_window() {
        let winn = window.open('https://kokonk.com/player', 'Музыкальный Плеер', 'width=400,height=600,top=100,left=100');

        setInterval(function() {
            winn.focus();
        }, 100);
    }
</script>