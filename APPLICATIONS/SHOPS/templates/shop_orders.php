<?php
$rows = SQL_ROWS_FIELD(q("
SELECT orders.*, shops.owner as shop_owner FROM orders
LEFT JOIN shops ON
orders.shop_id = shops.id 
WHERE shops.owner=".Access::userID()." 
ORDER BY id DESC LIMIT 40
"), 'id');
foreach($rows as $k=>$v) {
    $rows[$k]['order_code'] = unserialize($v['order_code']);
}
?>
<style>
    .my-orders-list {
        padding: 5px 0;
    }
    .my-orders-list {
        max-width: 600px;
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
        background: linear-gradient(92.05deg, #c2c2c27d, rgba(98, 98, 98, 0.48) 138.42%);
    }
    .order-item.not-recived {
        opacity: 1;
        background: linear-gradient(92.05deg, rgba(255, 145, 0, 0.49) 2.32%, rgba(115, 62, 0, 0.48) 138.42%);
        border-top: 3px solid #ccffcc;
        border-right: 3px solid #ccffcc;
        border-bottom: 3px solid #12561229;
        border-left: 3px solid #12561229;
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
        text-shadow: 0 1px 0 #fff;
        white-space: nowrap;
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
    }

</style>
<section class="my-orders" style="max-width: unset">
    <h2 class="h2">Заказы у меня</h2>
    <ul class="my-orders-list">
        <?php foreach($rows as $k=>$v) { ?>
        <li class="order-item <?php if($v['status'] !== 'получен') { echo 'not-recived'; } ?>">
            <div class="flex between width100perc" style="margin-bottom: 3px">
                <span style="white-space: nowrap">Заказ: <b><?=$k?></b></span>
                <div class="data-time flex between width100perc" style="font-size: 14px">
                    <button onclick="begin_chat_with(<?=$v['id_user']?>)" class="gray-btn">Чат с покупателем</button>
                    <div class="flex gap-10 data-time">
                        <i>Дата/Время: </i>
                        <b><?=date('d.m.Y H:i', strtotime($v['datatime']))?></b>
                    </div>
                </div>
            </div>
            <div class="flex between width100perc" style="align-items: end;">
                <ul class="items">
                    <?php foreach($v['order_code'] as $vv) { ?>
                    <li><a target="_blank" href="/<?=$vv['link']?>" class="flex gap-10 align-center">
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
                <div class="flex column" style="max-width: min-content; align-items: end">
                    <span class="type-pay"><?=$v['type_pay']?></span>
                    <?php
                    switch($v['status']) {
                        case 'создан':
                        case 'оплачен':
                            echo '<span class="pay-status" style="color: green;">Ожидает получения</span>';
                            break;
                        case 'получен':
                            echo '<span class="pay-status" style="color: #d5d5d5; background-color: #3b3b3b">Сделка закрыта</span>';
                            break;
                        default:
                            echo '<span class="pay-status" style="color: red;">'.$v['status'].'</span>';
                            break;
                    }
                    ?>
                    <div class="total-summ"><?=VALUES::price_format($v['total_summ'], 2)?> P</div>
                </div>
            </div>
        </li>
        <?php } ?>
    </ul>
</section>