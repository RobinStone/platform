<section class="center-wrapper-scroller">

</section>

<?php echo render('admin_up_row', ['apps'=>$apps]); ?>
<?php echo render('admin_down_row'); ?>
<?php echo render('work_templates'); ?>


<?php RBS::js_script_link_append_once('/JS/uploader.js');?>
<?php RBS::js_script_link_append_once('/JS/forms.js');?>
<?php RBS::js_script_link_append_once('/JS/CMD.js');?>

<!-- При рендаре robot ОБЯЗАТЕЛЬНО ПЕРЕДАЁМ room, type_room, params-->

<?php echo render('robot', [
    'admin_page'=>true,
    'room'=>'',
    'type_room'=>'personal',
    'params'=>[]
]); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js" type="text/javascript" charset="utf-8"></script>