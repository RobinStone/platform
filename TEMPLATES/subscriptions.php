<?php
$links = [
    [
        'img' => '20231117-122214_id-2-396957.png',
        'text' => 'Telegram',
        'link' => 'https://t.me/share/url?url='.trim(Core::$DOMAIN, '/').$viber,
    ],
    [
        'img' => '20231117-091239_id-2-698542.png',
        'text' => 'ВКонтакте',
        'link' => 'https://vk.com/share.php?url='.trim(Core::$DOMAIN, '/').$viber,
    ],
    [
        'img' => '20231117-100637_id-2-567350.png',
        'text' => 'WhatsApp',
        'link' => 'https://wa.me/?text='.trim(Core::$DOMAIN, '/').$viber,
    ],
    [
        'img' => '20231117-100637_id-2-153618.png',
        'text' => 'Viber',
        'link' => 'viber://forward?text='.trim(Core::$DOMAIN, '/').$viber,
    ],
    [
        'img' => '20231117-100637_id-2-670734.png',
        'text' => 'Facebook',
        'link' => 'https://www.facebook.com/sharer/sharer.php?u='.trim(Core::$DOMAIN, '/').$viber,
    ],
];
?>
<div class="flex between align-center gap-10 links b-2">
    <?php foreach ($links as $k => $v) { ?>
        <a href="<?= $v['link'] ?>" class="flex column gap-5" title="<?= $v['text'] ?>">
            <img width="50" height="50" src="/DOWNLOAD/<?= $v['img'] ?>">
        </a>
    <?php } ?>
    <div class="links-text" style="font-size: 14px; line-height: 14px; margin-left: 10px; color: green">Поделиться ссылкой<br>на этот товар</div>
</div>
