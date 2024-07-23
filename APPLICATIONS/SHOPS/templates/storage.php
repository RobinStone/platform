<?php

?>
<div class="back-fone" style="min-height: 100%">
    <h2 class="h2">Мои файлы</h2>
    <?php TOTALCOMANDER::draw(); ?>
</div>

<script>
    if(typeof total_c === 'undefined') {
        total_c = new TOTALCOMMANDER();
    }
    total_c.update_total('/', '/');

</script>