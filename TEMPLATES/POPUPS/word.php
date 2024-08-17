<?php
    $path = $path ?? './DOWNLOAD/20240817-113912_id-2-524291.svg';
    $dir = SITE::$USER_DIR;
?>
<style>
    .place-popap {
        display: inline-block;
        width: calc(100vw - 10px);
        height: calc(90vh - 10px);
        object-fit: contain;
    }
    .place-popap div {
        min-height: 100%;
    }
    iframe.docx {
        height: 90vh;
    }
</style>

<div class="flex center gap-10">
    <iframe class="docx" src="https://view.officeapps.live.com/op/embed.aspx?src=<?=$dir.$path?>" width="100%" height="500px" frameborder="0"></iframe>
</div>

<script>

</script>