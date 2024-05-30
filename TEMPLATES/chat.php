<?php
include_CSS('profil');
?>
<div class="wrapper">
    <?php echo "<div id='alerts'>" . render('alerts') . "</div>"?>
</div>
<section class="wrapper profil">
    <div class="columns column-left">
        <div class="menu-left-profil flex column gap-10">

        </div>
    </div>
    <div class="columns column-right">


    </div>
</section>

<!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params-->

<?php echo render('robot', [
    'page'=>true,  // настройка показывает чат на странице инлайновым методом
    'room'=>'',
    'type_room'=>'personal',
    'params'=>[]
]); ?>
