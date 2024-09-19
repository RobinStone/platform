<?php
$dt = VALUES::plus_days(-3);
$auto_recived_orders_ids = [];

$rows = SQL_ROWS_FIELD(q("
SELECT orders.*, shops.owner as shop_owner FROM orders
LEFT JOIN shops ON
orders.shop_id = shops.id 
WHERE id_user=".Access::userID()." 
ORDER BY id DESC LIMIT 40
"), 'id');

foreach($rows as $k=>$v) {
    $rows[$k]['order_code'] = unserialize($v['order_code']);
    if($v['closed'] == 0 && $v['datatime'] < $dt) {
        $auto_recived_orders_ids[] = (int)$v['id'];
    }
}

//wtf($rows, 1);

if(count($auto_recived_orders_ids) > 0) {
    INCLUDE_CLASS('shops', 'shop');
    INCLUDE_CLASS('shops', 'pay');
    if(SHOP::closed_payed_orders($auto_recived_orders_ids)) {
        $reload = true;
    } else {
        Message::addError('Не удалось провести авто-подтверждение заказов!..');
    }
}
?>
<style>
    .my-orders-list {
        padding: 5px 0;
    }
    .my-orders-list {
        max-width: 600px;
    }
    .align-center {
        align-self: center!important;
    }
    .order-item {
        position: relative;
        margin: 3px 0;
        padding: 3px 5px 3px 3px;
        border-top: 3px solid rgba(255, 255, 255, 0.29);
        border-right: 3px solid rgba(255, 255, 255, 0.29);
        border-bottom: 3px solid rgba(255, 255, 255, 0.29);
        border-left: 3px solid rgba(255, 255, 255, 0.29);
        border-radius: 10px;
        opacity: 1;
        background: linear-gradient(244deg, #cccccc70, rgba(208, 208, 208, 0.28));
    }
    .order-item.not-recived {
        opacity: 1;
        background: linear-gradient(244deg, #cccccc70, rgba(138, 225, 138, 0.44));
        border-top: 3px solid #ccffcc;
        border-right: 3px solid #ccffcc;
        border-bottom: 3px solid #12561229;
        border-left: 3px solid #12561229;
    }
    a b,
    .total-summ {
        white-space: nowrap;
    }
    .order-item:hover {
        opacity: 1;
    }
    .items {
        font-size: 12px;
        padding: 5px;
        border-radius: 5px;
        background-color: rgba(248, 248, 248, 0.53);
    }
    .items a {
        color: #000;
        text-decoration: none;
    }
    .items a:hover {
        text-decoration: underline;
        color: blue;
    }
    .total-summ {
        font-weight: 800;
        color: #000000;
        padding-top: 6px;
        text-align: right;
    }
    .pay-status {
        background-color: #ffffbb;
        padding: 1px 4px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        text-align: right;
        display: inline-block;
        max-width: min-content;
    }
    .type-pay {
        font-family: 'Open Sans', sans-serif;
        color: blue;
        font-size: 12px;
        font-style: italic;
        text-align: right;
        padding: 1px 4px;
    }
    .received-btn {
        opacity: 0;
        text-align: center;
        font-size: 13px;
        transition: 0.2s;
        position: absolute;
        right: 0;
        height: 100%;
        top: 0;
        max-width: min-content;
        background: linear-gradient(92.05deg, #ff9100 2.32%, #733e00 138.42%);
        border-radius: 4px;
        border: none;
        color: #fff;
        cursor: pointer;
        text-decoration: none;
        transform: translateX(calc(100% + 5px));
    }
    .order-item:hover .received-btn {
        opacity: 1;
    }
    .received-btn:hover {
        transition: 0.0001s;
        transform: translateX(calc(100% + 5px)) scale(1.05);
    }
    .received-btn:active {
        transition: 0.0001s;
        transform: translateX(calc(100% + 5px)) scale(0.98);
    }
    .data-time i {
        font-style: normal;
    }
    .gray-btn {
        margin-left: 10px;
        font-size: 13px;
    }

    @media screen and (max-width: 950px) {
        .profil .column-right {
            border: none;
        }
        .received-btn {
            opacity: 1;
            transform: translateY(100%);
            max-width: unset;
            top: unset;
            bottom: -4px;
            height: unset;
        }
        .received-btn:hover,
        .received-btn:active {
            transform: translateY(100%);
        }
        .order-item {
            opacity: 1!important;
        }
        .not-recived {
            margin-bottom: 30px;
        }
        .name-of-product {
            max-width: 30vw;
            flex-grow: 3;
        }
        .data-time i {
            display: none;
        }
        .title-flex {
            flex-direction: column;
        }
        .title-flex.gap-15 {
            gap: 5px;
        }
        .title-flex h2.h2 {
            margin: 5px auto!important;
        }
        .title-flex span {
            text-align: center!important;
            font-size: 12px;
            padding-bottom: 10px;
        }
    }

</style>
<section class="my-orders" style="max-width: unset">
    <div class="flex between gap-15 title-flex" style="max-width: 600px">
        <h2 class="h2" style="white-space: nowrap">Мои покупки</h2>
        <span style="display: inline-block; text-align: right; text-shadow: 0 1px 1px red">
            Если в течение 3-ёх дней вы не подтвердите получение заказа, подтверждение произойдёт автоматически!
        </span>
    </div>
    <ul class="my-orders-list">
        <?php foreach($rows as $k=>$v) { ?>
        <li class="order-item <?php if($v['status'] !== 'получен') { echo 'not-recived'; } ?>">
            <div class="flex between width100perc" style="margin-bottom: 3px;">
                <span style="white-space: nowrap">Заказ: <b><?=$k?></b></span>
                <div class="data-time flex between width100perc" style="font-size: 14px">
                    <button onclick="begin_chat_with(<?=$v['shop_owner']?>)" class="gray-btn">Чат с продавцом</button>
                    <button onclick="open_popup('request', {id: '<?=$k?>', type: 'корзина'}, ()=>{})" class="gray-btn request-btn flex gap-10" title="Оставить отзыв продавцу">
                        <img style="display: inline-block; width: 12px; height: 12px; transform: translateY(2px);" src="/DOWNLOAD/a07dc10669c4422f602702f620760797.svg">
                        <span>Отзыв</span>
                    </button>
                    <div class="flex gap-10">
                        <i>Дата/Время: </i>
                        <b><?=date('d.m.Y H:i', strtotime($v['datatime']))?></b>
                    </div>
                </div>
            </div>
            <div class="flex between width100perc" style="align-items: end;">
                <ul class="items">
                    <?php foreach($v['order_code']['products'] as $vv) { ?>
                    <li><a target="_blank" href="/<?=$vv['link']?>" class="flex gap-5 align-center">
                            <img width="20" height="20" src="/IMG/img100x100/<?=$vv['img']?>">
                            <span class="count-lines-1 name-of-product"><?=$vv['name']?></span>
                            <span><?=$vv['count']?> шт.</span>
                            <?php
                            if((int)$vv['discount'] > 0) {
                                echo '<b title="товар со скидкой" style="background-color: rgba(0,255,90,0.32)">' .$vv['price_with_discount'].' Р</b>';
                            } else {
                                echo $vv['price_with_discount'].' Р';
                            }
                            ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
                <div style="margin-bottom: 5px">
<!--                 тут копируем код из shop_orders.php - для отображения кнопки-->
                </div>
                <div class="flex column" style="align-items: end">
                    <span class="type-pay"><?=$v['type_pay']?></span>
                    <?php
                    switch($v['status']) {
                        case 'создан':
                        case 'оплачен':
                            echo '<span class="pay-status" style="color: green;">'.$v['status'].'</span>';
                            break;
                        case 'получен':
                            echo '<span class="pay-status" style="color: #d5d5d5; background-color: #3b3b3b">' .$v['status'].'</span>';
                            break;
                        default:
                            echo '<span class="pay-status" style="color: red;">'.$v['status'].'</span>';
                            break;
                    }
                    ?>
                    <div class="total-summ"><?=VALUES::price_format($v['total_summ'], 2)?> P</div>
                </div>
                <?php
                if($v['status'] !== 'получен') {
                    echo '<button onclick="recived_order('.$k.')" class="received-btn">Подтвердить получение</button>';
                }
                ?>
            </div>
        </li>
        <?php } ?>
    </ul>
</section>

<script>
    function recived_order(id_order) {
        info_qest(transform_pos('center'), function() {
            buffer_app = 'SHOPS';
            SENDER_APP('closed_order', {id: id_order}, function(mess) {
                mess_executer(mess, function(mess) {
                    open_tab('purchases');
                });
            });
        }, function() {

        }, 'Подтвердите, что товар вам доставлен (или получен) Вами.<br>' +
            'Вы удостоверились, что товар отвечает всем заявленным требованиям.<br><br>' +
            '<b style="background-color: yellow">После того как вы подтвердите получение товара, оплата за него будет переведена продавцу.</b><br><br>' +
            'Подверждаете получение товара ?', 'ДА - товар у меня', 'НЕТ - товар пока не у меня');
    }

    <?php if(isset($reload)) { ?>
    setTimeout(function() {
        open_tab('purchases');
    }, 200);
    <?php } ?>
</script>
