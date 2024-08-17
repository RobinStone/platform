<?php
    $img = $img ?? 'none.svg';
?>
<style>
    .imger {
        display: inline-block;
        width: calc(100vw - 10px);
        height: calc(90vh - 10px);
        object-fit: contain;
    }
</style>

<div class="flex center gap-10">
    <?php if(!isset($path)) { ?>
    <img class="imger" src="/DOWNLOAD/<?=$img?>">
    <?php } else { ?>
        <img class="imger" src="<?=$path?>">
    <?php } ?>
</div>

<script>

</script>
