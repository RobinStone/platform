<?php
if(isset($paginator) && !empty($paginator) && $paginator['paginator_items'] > 1) {
    echo '<div class="paginator" data-paginator-item="'.(int)$paginator['paginator_num'].'">';
    $all = $paginator['paginator_items'];
    $flow = $paginator['paginator_num'];

    if($flow > 5) {
        $begin = $flow-4;
        echo '<button>1</button>';
        echo '...';
    } else {
        $begin = 1;
    }
    for($i=$begin;$i<$flow;++$i) {
        echo '<button>' . $i . '</button>';
    }

    echo '<button class="now">'.$flow.'</button>';

    if($flow+4 < $all) {
        $end = $flow+4;
    } else {
        $end = $all;
    }
    for($i=$flow+1;$i<=$end;++$i) {
        echo '<button>' . $i . '</button>';
    }
    if($end !== $all) {
        echo '...';
        echo '<button>' . $all . '</button>';
    }
    echo '<select class="all-pags" onchange="">';
    for ($i = 1; $i <= $all; ++$i) {
        if ($i === $flow) {
            echo '<option selected>' . $i . '</option>';
        } else {
            echo '<option>' . $i . '</option>';
        }
    }
    echo '</select>';
    echo '</div>';
}