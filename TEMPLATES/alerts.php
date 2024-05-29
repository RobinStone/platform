<?php
if(Access::scanLevel() > 0) {
    $P = PROFIL::init(Access::userID());
    $arr = $P->get_all_alerts();
    if ($arr === '') {
        $arr = [];
    }
} else {
    $arr = [];
}
$count = 0;
foreach($arr as $k=>$v) {
    foreach($v as $kk=>$vv) {
        ++$count;
    }
}

if($count > 0) {
?>

<style>
    .alerts-tbl {
        border-collapse: collapse;
        position: relative;
        margin-bottom: 20px;
        z-index: 1;
        width: 100%;
    }
    .alerts-tbl:before {
        content: '';
        position: absolute;
        left: -5px;
        top: -5px;
        width: calc(100% + 10px);
        height: calc(100% + 10px);
        background-color: rgba(255, 255, 0, 0.16);
        z-index: -1;
        border-radius: 5px;
    }
    .label-img {
        display: inline-block;
        width: 20px;
        height: 20px;
        object-fit: contain;
    }
    .btn-alert {
        background: none;
        border: none;
        display: inline-block;
        width: 20px;
        height: 20px;
        padding: 0;
        margin: 0;
    }
    .btn-alert img {
        display: inline-block;
        width: 16px;
        height: 16px;
        object-fit: contain;
    }
    .info-row {
        cursor: pointer;
        font-size: 14px;
    }
    .info-row td:nth-child(2) {
        width: 100%;
    }
    .info-row:hover {
        background-color: rgba(13, 210, 172, 0.66);
    }
    .info-row td:nth-child(2) {
        padding: 3px 15px;
    }
</style>

<table class="alerts-tbl">
<?php
foreach($arr as $k=>$v) {
    foreach($v as $kk=>$vv) {
        if(is_array($vv)) {
            $txt = $vv['text'];
            if(isset($vv['link'])) {
                $txt = "<a href='".$vv['link']."'>".$txt."</a>";
            }
        } else {
            $txt = $vv;
        }
        switch($k) {
            case 'message':
                echo '<tr class="info-row info-msg" data-key="'.$kk.'" data-type="'.$k.'"><td><img src="/IMG/img100x100/20240420-205205_id-2-531246.png" class="label-img"></td><td>'.$txt.'</td><td><button onclick="clear_alert(this, \''.$kk.'\')" class="btn-alert action-btn"><img src="/DOWNLOAD/ec578cdcc885e82e09a1347fafc079bc.svg"></button></td></tr>';
                break;
            case 'attantion':
                echo '<tr class="info-row info-attantion" data-key="'.$kk.'" data-type="'.$k.'"><td><img src="/IMG/img100x100/20240420-115255_id-2-488961.png" class="label-img"></td><td>'.$txt.'</td><td><button onclick="$(this).closest(\'tr\').remove()" class="btn-alert action-btn"><img src="/DOWNLOAD/ec578cdcc885e82e09a1347fafc079bc.svg"></button></td></tr>';
                break;
            case 'warning':
            case 'danger':
            case 'error':
                echo '<tr class="info-row info-warning" data-key="'.$kk.'" data-type="'.$k.'"><td><img src="/IMG/img100x100/20240420-205906_id-2-265057.png" class="label-img"></td><td>'.$txt.'</td><td></td></tr>';
                break;
        }
    }
}
echo '</table>';
?>

<script>
    function clear_alert(obj, id_or_key) {
        SENDER('clear_alert', {id_or_key: id_or_key}, function(mess) {
            mess_executer(mess, function(mess) {
                $(obj).closest('tr').remove();
            })
        });
    }
</script>

    <?php } ?>