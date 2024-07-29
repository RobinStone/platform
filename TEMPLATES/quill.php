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

<style>
    .ql-size-10 { font-size: 10px; }
    .ql-size-12 { font-size: 12px; }
    .ql-size-14 { font-size: 14px; }
    .ql-size-16 { font-size: 16px; }
    .ql-size-32 { font-size: 32px; }
</style>

<div class="editor-wrapper" style="<?=$vis?>">
    <?=$closer?>
    <div id="field"><?=$txt?></div>
    <?=$header?>
</div>

<script>
    if(typeof tiny_active === 'undefined') {
        tiny_active = new EventRBS();
    }

    text_area = $('#field');

    function change_text_area() {
        changer();
        $('tr[data-field="tiny"]').attr('data-changed', true).attr('data-real', escape(text_area.find('.ql-editor').html()));
        console.log('Текстовое поле изменено');
    }


    // window.addEventListener('load', function() {
        tiny_active.action();
        const quill = new Quill('#field', {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'], // Форматирование текста
                    [{'list': 'ordered'}, {'list': 'bullet'}], // Списки
                    [{'color': []}, {'background': []}], // Цвет текста и фона
                    [{'font': []}, {'size': ['small', 'normal', 'large', 'huge']}],
                    [{'align': []}], // Выравнивание
                    ['clean'] // Очистка форматирования
                ],
            },
            theme: 'snow'
        });

    quill.on('text-change', function(delta, oldDelta, source) {
        change_text_area();
    });

    // });
</script>
