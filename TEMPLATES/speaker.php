<?php
include_CSS('profil');
$only_chat = false;
if(isset($_GET['only_chat'])) {
    $only_chat = true;
}
?>
<section class="wrapper profil">
    <div class="columns column-left">
        <div class="menu-left-profil flex column gap-10">

        </div>
        <?php
        foreach($users_in_room as $k=>$v) {
            echo '<button onclick="open_direct_chat('.$k.')">'.$v['login'].'</button>';
        }
        ?>
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

<script>
    setTimeout(function() {
        $(' #body-s .quick_access').remove();
    }, 100);

    function open_direct_chat(id) {
        begin_chat_with(id);
    }
</script>