<style>
    #pl {
        position: relative;
        z-index: 999;
    }
    #pl #jp_container_1 {
        position: fixed;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        transform: none;
    }
    #pl .jp-volume-bar {
        left: 10px;
        width: calc(100% - 20px);
    }
</style>

<div id="pl">
    <?php echo render('player'); ?>
</div>

<script>
    $('#jp_container_1 .close-map-btn').click();

    $('header.wrapper').remove();
    $('.seo-panel').remove();

    window.onload = function() {
        $('.footer-wrapper').remove();
        $('.admin-panel').remove();
    }

</script>
