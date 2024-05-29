<?php
include_JS_once('/APPLICATIONS/SHOPS/JS/details-shop.js');
$img = '/DOWNLOAD/20230508-154105_id-2-312764.svg';
if(file_exists('./IMG/img100x100/'.$arr['logo'])) {
    $img = './IMG/img100x100/'.$arr['logo'];
}
$params = $params ?? [];
$open = false;
foreach($params as $v) {
    if(in_array($arr['id'], $v)) {
        $open = true;
    }
}
?>
<link rel="stylesheet" href="/APPLICATIONS/SHOPS/CSS/details-shop.css?<?= filemtime('./APPLICATIONS/SHOPS/CSS/details-shop.css') ?>">
<details class="shops-profil" data-id="<?=$arr['id']?>">
    <summary class="flex gap-10 flex-wrap">
        <div class="img-wrapper paddinger-5px">
            <img width="60" height="60" src="<?=$img?>">
        </div>
        <div class="paddinger-5px info-addr">
            <h3 style="position: relative" class="flex ">
                <div class="row"><?=$arr['name']?></div>
            </h3>
            <?php if($arr['title'] !== 'Бесплатный') { ?>
                <div title="Теперь доступен для изменения ваш домен" class="row link"><button onclick="window.open('/<?=$arr["domain"]?>', '_blank')" class="action-btn">Перейти</button><a target="_blank" href="/<?=$arr['domain']?>"><?=Core::$DOMAIN.$arr['domain']?></a></div>
            <?php } ?>
            <div class="flex gap-5">
                <div class="row"><?=$arr['city']?></div>
                <div class="row"><?=$arr['address']?></div>
            </div>
        </div>
        <div class="tarif-wrapper paddinger-5px">
            <div class="min-font-size row"><?=$arr['title']?></div>
            <div class="min-font-size"><?=date('d.m.Y', strtotime($arr['created']))?></div>
        </div>
    </summary>
    <div class="shop-editor-form">
        <div class="big-text"></div>
    </div>
    <div class="content-shop"></div>
</details>