<?php
$txt = $text ?? 'Пусто';
?>
<style>
    .circle {
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
    .circle.blue {
        background-color: #002480;
        left: 65%;
        top: 10%;
    }
    .phone-changer {
        font-size: 20px;
    }
    .phone-changer input {
        color: #000000!important;
        font-weight: 600;
    }

    @media screen and (max-width: 950px) {
        .phone-changer {
            max-width: 100vw;
            max-height: calc(100vh - 70px);
            overflow-y: auto;
        }
    }

</style>

<section class="message">
    <h2 style="padding-right: 30px; margin-bottom: 0.5em" class="h2">Сообщение</h2>
    <div class="circle"></div>
    <div class="circle blue"></div>
    <div style="font-weight: 600; ">
        <?php echo $txt; ?>
    </div>
</section>

<script>

</script>

