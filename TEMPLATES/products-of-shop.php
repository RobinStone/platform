<style>
    .all-products tr {
        pointer-events: auto!important;
    }
    .table-wrapper-prod {
        background-color: #fff;
        max-height: 300px;
        overflow-y: auto;
    }
    .on_, .off_ {
        display: inline-block;
        padding: 1px 6px!important;
        border-radius: 5px;
        font-weight: 800;
        font-size: 14px;
        width: 100%;
    }
    .on_ {
        background-color: lime;
    }
    .off_ {
        background-color: red;
        color: yellow;
    }
    .table-wrapper-prod td:first-child {
        text-align: center;
    }
    .table-wrapper-prod input[type=checkbox] {
        display: inline-block;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>
<?php
INCLUDE_CLASS('SHOPS', 'SHOP');
$rows = SHOP::get_list_products_of_shop($id_shop);
//$ids = [];
//foreach($rows as $k=>$v) {
//    $ids[] = $v['prod_id'];
//}
//$rows = SHOP::get_products_list_at_id($id_shop, $ids, true);
//wtf($rows, 1);
echo '<div class="table-wrapper-prod"><table class="all-products">';
echo '<tr><td><input onchange="change_all(this)" type="checkbox"></td><td><input oninput="finder_prods(this)" placeholder="найти..." type="text"></td><td></td></tr>';
foreach($rows as $k=>$v) {
    $stat = '<button onclick="toggle_item(this)" class="btn-checker on_ action-btn">ON</button>';
    if($v['active'] == 0) {
        $stat = '<button onclick="toggle_item(this)" class="btn-checker off_ action-btn">OFF</button>';
    }
    echo '<tr data-id="'.$id_shop.'_'.$v['prod_id'].'" data-id_prod="'.$v['prod_id'].'" data-text="'.$v['name'].'">
            <td>
                <input value="'.$v['prod_id'].'" type="checkbox">
            </td>
            <td>
                <a target="_blank" href="/product?s='.$id_shop.'&prod='.$v['prod_id'].'">'.$v['name'].'</a>
            </td>
            <td>'.$stat.'</td>
        </tr>';
}
echo '</table></div>'
?>
<div class="flex gap-5 flex-wrap" style="padding: 10px">
    <button onclick="del_checked(this, <?=$id_shop?>)" class="action-btn btn-gray btn-gray-text not-border micro-btn">Удалить отмеченные</button>
    <button onclick="toggle_checked(this, <?=$id_shop?>, false)" class="action-btn btn-gray btn-gray-text not-border micro-btn">Заблокировать отмеченные</button>
    <button onclick="toggle_checked(this, <?=$id_shop?>, true)" class="action-btn btn-gray btn-gray-text not-border micro-btn">Разблокировать отмеченные</button>
</div>
