<?php
    $path = $path ?? './DOWNLOAD/20240817-113912_id-2-524291.svg';
    $dir = SITE::$USER_LOCAL_DIR;

    $content = file_get_contents($dir.$path);
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
    .total-text .total-content {
        max-width: calc(100vw -20px);
        width: 1200px;
    }
</style>

<div class="flex center gap-10 column total-text" data-path="<?=$dir.$path?>">
    <div class="total-content" contenteditable="true" style="outline: none">
        <?php echo $content; ?>
    </div>
    <div class="flex gap-5">
        <button onclick="save_text_content(this)" class="btn-just">Сохранить</button>
    </div>
</div>

<script>
    function save_text_content(obj) {
        let file_name = $(obj).closest('.total-text').attr('data-path');
        let txt = $(obj).closest('.total-text').find('.total-content').html();

        total_c.save(file_name, txt, function() {
            say('Сохранено');
        });
    }
</script>