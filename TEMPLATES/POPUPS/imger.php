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
    <img class="imger" src="/DOWNLOAD/<?=$img?>">
</div>

<script>

</script>
