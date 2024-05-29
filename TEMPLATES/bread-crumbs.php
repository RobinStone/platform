<?php
$crumbs = [];
$link = [];
foreach($items as $k=>$v) {
    if($k !== 'СПИСОК') {
        $link[] = $v;
        $crumbs[] = '<a href="/'.implode('/', $link).'">'.$k.'</a>';
    }
    }
?>

<section class="bread-crumbs flex align-center">
    <h2 class="visually-hidden">Bread - crumbs</h2>
    <?=implode('<span style="display: inline-block; padding: 0 3px;">-</span>', $crumbs)?>
</section>
