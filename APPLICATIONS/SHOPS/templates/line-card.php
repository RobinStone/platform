<?php
if(count($arr) > 0) {
    foreach($arr as $k=>$v) {
        $props = [];
        $img = '';
        $price = '';
        foreach($v['VALS'] as $itm) {
            $props[$itm['name']] = $itm['VALUE'];
            if($img === '' && $itm['field_type'] === 'file') {
                $img = './IMG/img100x100/'.$itm['VALUE'];
            }
            if($price === '' && $itm['name'] === 'Стоимость') {
                $price = $itm['VALUE'];
            }
        }
        ?>
        <div data-shop="<?=$shop_id?>" data-product-id="<?=$k?>" class="line-card flex gap-5">
            <img width="100" height="100" src="<?=$img?>">
            <div style="margin-left: 10px">
                <h2><a href="/product/<?=$v['TRANS']?>?s=<?=$shop_id?>&prod=<?=$k?>" target="_blank"><?=$v['NAME']?></a></h2>
                <div class="price"><?=$price?> $</div>
            </div>
        </div>
<?php }

//wtf($arr, 1);

}





