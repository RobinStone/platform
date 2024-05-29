<?php
$txt = $text ?? '';
$vis = "display: none; ";
$st = "";
if(isset($stars) && $stars === true) {
    $st = "<div class='stars flex'><div class='star-shore'></div><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'></div>";
}
if(isset($visible) && $visible === true) {
    $vis = "display: block; ";
}
$header = '<header class="header-tiny flex gap-5"><button onclick="save_content()" class="tiny-btn action-btn">Сохранить</button>'.$st.'</header>';
if(isset($hidden_save) && $hidden_save === true) {
    $header = "";
}
$closer = '<button class="close-map-btn" onclick="hide_editor()"></button>';
if(isset($not_closed) && $not_closed === true) {
    $closer = '';
}
if(isset($width)) { ?>
    <style>
        td .editor-wrapper {
            width: calc(<?=$width?> - 20px);
            max-width: 745px;
            position: relative;
            opacity: 1;
            left: 0;
            bottom: 0;
        }
    </style>
<?php } ?>

<div class="editor-wrapper" style="<?=$vis?>">
    <?=$closer?>
    <textarea id="field"><?=$txt?></textarea>
    <?=$header?>
</div>

<script>
    tinymce.init({
    selector: '#field',
    menubar: false,
    plugins: 'emoticons image link lists searchreplace table',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align lineheight | tinycomments | checklist numlist bullist indent outdent | removeformat | table | forecolor backcolor',
    statusbar: false,
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
    { value: 'First.Name', title: 'First Name' },
    { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        setup: function (editor) {
            editor.on('keyup input', function () {
                changer();
                console.log('Текстовое поле изменено');
            });
        }
    });
</script>

<!--ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss-->