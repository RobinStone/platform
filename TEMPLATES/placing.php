<div class="wrapper main-header">    <H1>Оформление заказа</H1></div><section class="wrapper profil placing">    <div class="columns column-left">        <table class="prods-list backer">            <?php            $complite_total = 0;            echo '<tr><th></th><th>Название</th><th>Цена</th><th>Колич.</th><th>Стоимость</th></tr>';            foreach($items as $v) {                $price = (float)$v['SHOP']['PRICE'];                $discount = (int)$v['SHOP']['DISCOUNT'];                $total = (int)$v['COUNT'] * $price;                $complite_total += $total;                ?>                <tr class="product-item">                    <td><img width="50" height="50" src="./IMG/img100x100/<?=$v['SHOP']['IMG']?>"></td>                    <td><?=$v['SHOP']['PRODUCT_NAME']?></td>                    <td style="white-space: nowrap"><?=VALUES::price_format($price)?> P</td>                    <td><?=$v['COUNT']?></td>                    <td class="price-one" data-summ="<?=$total?>" style="white-space: nowrap"><?=VALUES::price_format($total)?> P</td>                </tr>            <?php } ?>        </table>        <div class="backer">            <p>1. Населённый пункт</p>            <label class="flex gap-10 down-row-pt">                <div class="svg-wrapper" style="width: 25px; height: 25px"><?=RBS::SVG('location.svg')?></div>                <input id="city-delivery" list="citys" type="text" value="<?=$place?>">                <button class="list-btn"><?=RBS::SVG('down_row_pt.svg')?></button>            </label>        </div>        <div class="backer">            <p>2. Способ получения</p>            <ul class="flex width100" style="align-items: normal">                <li class="delivery-item">                    <label class="flex center paddinger-1">                        <input style="display: none" value="self" type="radio" checked="checked" name="delivery">                        <span>Самовывоз</span>                    </label>                </li>                <li class="delivery-item">                    <label class="flex center paddinger-1">                        <input style="display: none" value="courier" type="radio" name="delivery">                        <span>Пункты выдачи</span>                    </label>                </li>                <li class="delivery-item">                    <label class="flex center paddinger-1">                        <input oninput="delivery_settings()" style="display: none" value="courier" type="radio" name="delivery">                        <span>Курьером продавца</span>                    </label>                </li>            </ul>        </div>        <div class="backer">            <p>3. Оплата (на счету <?=$cash?> Р)</p>            <label class="flex gap-10 down-row-pt">                <div class="svg-wrapper" style="width: 25px; height: 25px"><?=RBS::SVG('money_box.svg')?></div>                <select id="type-pay" class="not-row" type="text">                    <?php                    foreach($types_pay as $v) {                        echo '<option value="'.$v.'">'.$v.'</option>';                    }                    ?>                </select>                <button class="list-btn"><?=RBS::SVG('down_row_pt.svg')?></button>            </label>        </div>        <div class="backer">            <p>4. Комментарии к заказу</p>            <textarea></textarea>        </div>    </div>    <div class="columns column-right">        <table class="width100perc">            <tr>                <td><span>К оплате</span></td>                <td class="width100perc"><div class="down-pointers"></div></td>                <td class="price"><?=VALUES::price_format($complite_total)?> P</td>            </tr>            <tr>                <td><span>Доставка</span></td>                <td class="width100perc"><div class="down-pointers"></div></td>                <td class="price">0 P</td>            </tr>            <tr>                <td><span>Итого</span></td>                <td class="width100perc"><div class="down-pointers"></div></td>                <td id="fool-complite-total" data-comlite-total="<?=$complite_total?>" class="price"><?=VALUES::price_format($complite_total)?> P</td>            </tr>        </table>        <div class="gray-line"></div>        <label class="flex gap-20 align-center promo">            <span>Промокод</span>            <input type="text">        </label>        <button onclick="buy()" class="btn width100perc" style="margin-top: 2em; min-height: 40px">Оформить заказ</button>        <p class="bold-font center text-center" style="font-size: 12px; text-align: center; max-width: 520px; margin: 1em auto">Нажимая кнопку «Оформить заказ» вы соглашаетесь на покупку товара на условиях,            указанных в объявлении продавца        </p>    </div></section><!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params--><?php echo render('robot', [    'header_controller'=>true,  // настройка прячет phone-book в header    'room'=>'',    'type_room'=>'personal',    'params'=>[]]); ?><datalist id="citys">    <?php    foreach(SUBD::getAllDB('cities') as $v) {        echo '<option value="'.$v['name'].'"></option>';    }    ?></datalist><script>    let basket = <?=json_encode($items, JSON_UNESCAPED_UNICODE)?>;    let shop_id =<?=(int)$_GET['shop']?>;    let cash = <?=round($cash, 2)?>;</script>