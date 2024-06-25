<section class="wrapper back column">
    <?=render('user-micro-card', ['login'=>$P->get_field('login'), 'self'=>true])?>
</section>

<section class="wrapper back">
    <?php
    include_CSS('shops');
    include_once './TEMPLATES/shops.php'
    ?>
</section>

<!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params-->


<?php echo render('robot', [
    'header_controller'=>true,  // настройка прячет phone-book в header
    'room'=>'',
    'type_room'=>'personal',
    'params'=>[]
]); ?>

