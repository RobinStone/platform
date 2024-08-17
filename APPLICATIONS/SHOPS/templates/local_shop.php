<?php
$id = (int)$post['id'];
$shop = SUBD::get_executed_rows('shops', 'id', $id)[$id];
$shop_vals = SUBD::getLineDB('shops', 'id', $id);
$cities = SQL_ROWS_FIELD(q("SELECT `id`, `name`, `id_country` FROM `city`"), 'id');

$created = VALUES::dateToFormat($shop['created']);
$active_to = VALUES::dateToFormat($shop['active_to']);

$alert = "";
$end = "тся";
if($shop_vals['active_to'] < date('Y-m-d H:i:s')) {
    $alert = " background-color: red; color: yellow; ";
    $end = "лся";
}

$addit = ADD::get_additional_props('shops', $shop_vals['id'], 'city');

$addition_cities = [];
foreach($addit as $k=>$v) {
    $addition_cities[] = "<div class='add-cont flex align-center'><span>".$cities[$v['val']]['name']."</span><button class='action-btn' onclick='del_addit(this, ".$k.")'>❌</button></div>";
}
?>

<section class="local-shop">
    <table>
        <tr data-field="logo" data-value="<?=$shop_vals['logo']?>">
            <td>Логотип</td>
            <td class="cont-ind"></td>
            <td><button onclick="edit_shop(this, 'img', '')" class="edit-btn"></button></td>
        </tr>
        <tr data-field="name" data-value="<?=$shop_vals['name']?>">
            <td>Имя площадки</td>
            <td><?=$shop['name']?></td>
            <td><button onclick="edit_shop(this, 'string', '')" class="edit-btn"></button></td>
        </tr>
        <?php if($shop['title'] !== 'Бесплатный') { ?>
        <tr data-field="domain" data-value="<?=$shop_vals['domain']?>">
            <td>Домен (адрес)</td>
            <td><?=Core::$DOMAIN?><?=$shop['domain']?></td>
            <td><button onclick="edit_shop(this, 'string', '')" class="edit-btn"></button></td>
        </tr>
        <?php } ?>
        <tr data-field="active" data-value="<?=$shop_vals['active']?>">
            <td>Статус</td>
            <td><?=$shop['active']?></td>
            <td><button onclick="edit_shop(this, 'bool', '')" class="edit-btn"></button></td>
        </tr>
        <tr data-field="owner" data-value="<?=$shop_vals['owner']?>">
            <td>Владелец</td>
            <td><?=$shop['owner']?></td>
            <td><button onclick="edit_shop(this, 'delegate', '')" class="edit-btn"></button></td>
        </tr>
        <tr data-field="city" data-value="<?=$shop_vals['city']?>">
            <td>Город (насел.пункт)</td>
            <td><?=$shop['city']?></td>
            <td><button onclick="edit_shop(this, 'select', '')" class="edit-btn"></button></td>
        </tr>
        <?php if($shop['title'] !== 'Бесплатный') { ?>
            <tr data-field="additional-points" data-value="-">
                <td>Доп.города</td>
                <td><div class="add-cities flex gap-5 flex-wrap"><?=implode(' ', $addition_cities)?></div></td>
                <td><button onclick="open_popup('addition_cities', {id_shop: <?=$shop_vals['id']?>})" class="edit-btn"></button></td>
            </tr>
        <?php } ?>
        <tr data-field="address" data-value="<?=$shop_vals['address']?>">
            <td>Адрес</td>
            <td><?=$shop['address']?></td>
            <td><button onclick="edit_shop(this, 'text', '')" class="edit-btn"></button></td>
        </tr>
        <?php if($shop['title'] !== 'Бесплатный') { ?>
            <tr data-field="worktime" data-value="<?=$shop_vals['worktime']?>">
                <td>Время работы</td>
                <td><?=WORKTIME::format($shop_vals['worktime'])?></td>
                <td><button onclick="edit_shop(this, 'worktime', '')" class="edit-btn"></button></td>
            </tr>
        <?php } ?>
        <tr data-field="all_time_work" data-value="<?=$shop_vals['all_time_work']?>">
            <td>Круглосуточное обслуживание</td>
            <td><?=$shop['all_time_work']?></td>
            <td><button onclick="edit_shop(this, 'bool', '')" class="edit-btn"></button></td>
        </tr>
        <?php if($shop_vals['title'] !== 'Бесплатный') { ?>
        <tr data-field="active_to" data-value="<?=$shop_vals['active_to']?>" style="<?=$alert?>">
            <td>Тариф закончи<?=$end?></td>
            <td><?=$active_to?></td>
            <td><button onclick="edit_shop(this, 'plus', '')" class="edit-btn"></button></td>
        </tr>
        <?php } ?>
        <tr data-field="title" data-value="<?=$shop_vals['title']?>">
            <td>Тариф</td>
            <td><?=$shop['title']?></td>
            <td><button onclick="edit_shop(this, 'tarif', '')" class="edit-btn"></button></td>
        </tr>
        <tr data-field="created" data-value="<?=$shop_vals['created']?>">
            <td>Когда был создан</td>
            <td><?=$created?></td>
            <td></td>
        </tr>
        <tr data-field="id" data-value="<?=$shop_vals['id']?>">
            <td>ID площадки</td>
            <td><?=$shop['id']?></td>
            <td></td>
        </tr>
        <tr data-field="descr" data-value="<?=$shop_vals['descr']?>">
            <td>Описание площадки</td>
            <td class="count-lines-3"><?=$shop['descr']?></td>
            <td><button onclick="edit_shop(this, 'text', '')" class="edit-btn"></button></td>
        </tr>
    </table>
</section>