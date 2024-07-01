<?php if(!empty($shops)) { ?>
<div class="flex center gradient" style="padding: 10px 20px; margin-bottom: 2em">
    <section class="shops">
        <?php f_('h2-shop', './TEMPLATES/shops.php'); ?>
        <h2 class="h2 visually-hidden">Магазины</h2>
        <?php f_end('h2-shop'); ?>
        <p id="txt-2" class="micro-text">Добро пожаловать в мир проверенных продавцов и их надежных торговых площадок!
            У нас вы найдете широкий выбор качественных товаров и услуг от лучших продавцов.
            Наслаждайтесь комфортом онлайн-шопинга и уверенностью в каждой сделке.
            Заходите прямо сейчас, чтобы открыть для себя удивительный мир возможностей!</p>
        <div class="flex center gap-15 flex-wrap">
            <?php foreach($shops as $v) { ?>
                <a target="_blank" href="/<?=$v['domain']?>" class="flex gap-10">
                    <div class="round-shore"></div>
                    <img width="70" height="70" src="/IMG/img100x100/<?=$v['logo']?>">
                    <div class="flex column gap-5">
                        <div class="bold-font count-lines-1"><?=$v['name']?></div>
                        <div class="micro-text count-lines-2"><?=$v['descr']?></div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </section>
</div>
<?php } ?>