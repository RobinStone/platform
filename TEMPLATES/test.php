<link href="./TEMPLATES/CSS/quill.css" rel="stylesheet">
<script src="./TEMPLATES/JS/quill.js"></script>



<script src="./JS/jquery-3.6.0.min.js"></script>
<script src="./JS/sysMessages.js"></script>
<script src="./TEMPLATES/JS/general_files.js"></script>




<div id="field">Проверка микрофона</div>

<script>
    window.addEventListener('load', function() {
        const quill = new Quill('#field', {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'], // Форматирование текста
                    [{'list': 'ordered'}, {'list': 'bullet'}], // Списки
                    [{'color': []}, {'background': []}], // Цвет текста и фона
                    [{'font': []}, {'size': []}], // Шрифты и размеры
                    [{'align': []}], // Выравнивание
                    ['link', 'image', 'video'], // Вставка
                    ['clean'] // Очистка форматирования
                ],
            },
            theme: 'snow'
        });
    });
</script>