<?php
$type = $type ?? 'продукт';
(bool)$only_edit = $only_edit ?? false;
$data_star = 0.0;
$row = [];
switch($type) {
    case 'ответ':
        if($row = SQL_ONE_ROW(q("SELECT * FROM reviews_prod WHERE id=".(int)$id." AND owner_product=".Access::userID()))) {
            $text = $row['html_ans'];
            $data_star = -1;
        }
        break;
    case 'продукт':
        if($row = SQL_ONE_ROW(q("SELECT * FROM reviews_prod WHERE id=".(int)$id." AND author_id=".Access::userID()))) {
            $text = $row['html'];
            $data_star = round((float)$row['stars'], 2);
            $id = (int)$row['product_id'];
        }
        break;
    case 'корзина':
        $table = 'orders';
        $query = " AND product_id=".(int)$id;
        if($only_edit) {
            $table = 'reviews_prod';
            $query = "";
        }
        if($row = SQL_ONE_ROW(q("SELECT * FROM ".$table." WHERE id=".(int)$id))) {
            if($only_edit) {
                $id = (int)$row['product_id'];
            }
            if($req = SQL_ONE_ROW(q("
                     SELECT * FROM reviews_prod WHERE 
                     shop_id=".(int)$row['shop_id']." AND
                     author_id=".Access::userID()." AND
                     type_review='корзина' ".$query." 
                     LIMIT 1
                     "))) {
                $text = $req['html'];
                $data_star = round((float)$req['stars'], 2);
            } else {

            }
        }
        break;

}
$text = $text ?? '';
?>

<style>
    .place-popap {
        box-sizing: border-box;
        max-width: calc(100vw - 10px);
        width: 1200px;
    }
    .form {
        width: 100%;
        max-width: calc(100vw - 10px);
    }
    .form h1 {
        margin-top: unset;
        margin-bottom: 15px;
    }
    .tox {
        z-index: 99999999!important;
    }
</style>

<div class="form">
    <h1>Оставляем отзыв "<?=$type?>"</h1>
    <div class="flex column gap-10">
        <textarea id="request"><?=$text?></textarea>
    </div>
    <header class="header-tiny flex gap-5"><button onclick="save_request()" class="tiny-btn action-btn">Сохранить</button>
        <div class='stars flex' data-star="<?=$data_star?>"><div class='star-shore'></div><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'><img width='50' height='50' src='/DOWNLOAD/20230902-204747_id-2-695580.png'></div>
    </header>
</div>

<script>
    setTimeout(()=> {
        update_stars_draw($('#container-3 .stars'));
    }, 500)


    tinymce.remove('#request');

    tinymce.init({
        selector: '#request',
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
                console.log('Текстовое поле изменено');
            });
        }
    });

    function save_request() {
        let cont = tinymce.get('request').getContent();
        console.log(cont);
        buffer_app = 'SHOPS';
        SENDER_APP('set_review_for_product', {
            shop_id: <?=(int)$row['shop_id']?>,
            product_id: <?=$id?>,   // тут можно передать как id продукта, так и id отзыва
            html: cont,
            stars: $('#container-3 .stars').attr('data-star'),
            type_review: '<?=$type?>'
        }, function(mess) {
            mess_executer(mess, function(mess) {
                close_popup('request');
                say('Ваш отзыв принят.');
                let url = new URL(location.href);
                if(url.pathname === '/reviews') {
                    location.reload();
                }
            });
        });
    }


</script>