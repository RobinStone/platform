<!DOCTYPE html>
<html lang="<?=Core::$LANG?>">
<head>
<!--    <meta charset="UTF-8">-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="description" content="<?=Core::$description?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@500;700&display=swap" rel="stylesheet">

    <link rel="icon" href="/favicon.ico?49376" type="image/x-icon">

    <link rel="stylesheet" href="/CSS/reset.css">
    <link rel="stylesheet" href="/CSS/style.css?<?= filemtime('./CSS/style.css') ?>">
    <link rel="stylesheet" href="/CSS/rbs-main.css?<?= filemtime('./CSS/rbs-main.css') ?>">

    <script src="/JS/jquery-3.6.0.min.js"></script>
    <script src="/JS/jquery.cookie.js"></script>
    <script src="/JS/sysMessages.js?<?= filemtime('./JS/sysMessages.js') ?>"></script>
    <script src="/JS/informator.js?<?= filemtime('./JS/informator.js') ?>"></script>
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

    <title><?=Core::$title?></title>

</head>
<main>
<body class="admin-pages" id="body">

<script>
    <?php
    global $JS_VARS;
    foreach($JS_VARS as $k=>$v) {
        echo $k.'='.$v.';';
    }
    ?>
</script>