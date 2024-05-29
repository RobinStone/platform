<!DOCTYPE html>
<html lang="ru">

<head>
<!--    <meta charset="UTF-8">-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="<?=Core::$description?>">
    <meta name="keywords" content="<?=Core::$keywords?>">

    <?php
    if (count(Core::$META) > 0) {
        echo implode("\r\n", Core::$META);
    }
    ?>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico?342342" type="image/x-icon">

    <?php
    if(Core::$CANONICAL !== '') {
        echo '<link rel="canonical" href="'.Core::$CANONICAL.'">';
    }
    ?>

    <link rel="stylesheet" href="/CSS/reset.css">
    <link rel="stylesheet" href="/CSS/style.css?<?= filemtime('./CSS/style.css') ?>">
    <link rel="stylesheet" href="/CSS/rbs-main.css?<?= filemtime('./CSS/rbs-main.css') ?>">
    <link rel="stylesheet" href="/TEMPLATES/CSS/menu.css?<?= filemtime('./TEMPLATES/CSS/menu.css') ?>">

    <script src="/JS/jquery-3.6.0.min.js"></script>
    <script src="/JS/jquery.cookie.js"></script>
    <script src="/JS/sysMessages.js?<?= filemtime('./JS/sysMessages.js') ?>"></script>
    <script src="/JS/informator.js?<?= filemtime('./JS/informator.js') ?>"></script>

    <script async="async" src="https://cdn.tiny.cloud/1/7gjf6kwpz0c9ibyyqwqlogs78kvprk56ar1csufu5bmw17ca/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<!--    <script src="/TEMPLATES/JS/header.js?--><?//= filemtime('./TEMPLATES/JS/header.js') ?><!--"></script>-->

    <?php
    if (count(Core::$CSS) > 0) {
        echo implode("\r\n", Core::$CSS);
    }
    if (count(Core::$LINKS_START_JS) > 0) {
        echo implode("\r\n", Core::$LINKS_START_JS);
    }
    if (count(Core::$JS) > 0) {
        echo implode("\r\n", Core::$JS);
    }
    ?>

<!--linker-CSS-->
<!--linker-JS-->

    <?php f_('meta', './TEMPLATES/header.php'); ?>
<!-- нет кода -->
    <?php f_end('meta'); ?>

    <title><?=Core::$title?></title>
</head>
    <body id="body-s" class="site-pages">
        <main>
            <?php
            if(isMobile()) {
                echo render('header_phone', ['user_img'=>$user_img]);
            } else {
                echo render('header_desctop', [
                        'user_img'=>$user_img,
                        'favorite_count'=>$favorite_count,
                        'count_in_basket'=>$count_in_basket,
                ]);
                if($admin_panel > 0) {
                    echo render('admin_panel', ['params'=>$admin_panel_array]);
                }
            }
            ?>

            <?php
            if(isset($P) && $P->get('SEO') === '1') {
                echo render('seo-panel', ['P'=>$P]);
            }
            ?>


        <script>
            $(document).on('mouseenter', '.menu-item', function(e) {
                $('.sel-item').removeClass('sel-item');
                $(this).addClass('sel-item');
            });

            function show_hide_menu() {
            if($('.menu-wrapper').hasClass('hidden-menu')) {
                    if(!is_mobile()) {
                        setOverlayJust(true);
                    }
                    $('.menu-wrapper').removeClass('hidden-menu');
                        setTimeout(function() {
                            $('.menu-wrapper').addClass('opener');
                        }, 10);
                    } else {
                $('.menu-wrapper').removeClass('opener');
                delOvelay();
                setTimeout(function() {
                    $('.menu-wrapper').addClass('hidden-menu');
                }, 400);
                }
            }

            <?php
            global $JS_VARS;
            foreach($JS_VARS as $k=>$v) {
                echo $k.'='.$v.';';
            }
            ?>
        </script>