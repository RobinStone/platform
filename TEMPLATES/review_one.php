<?phpif(!file_exists('./IMG/img100x100/'.$arr['user']['avatar'])) {    $arr['user']['avatar'] = './DOWNLOAD/user.svg';}//wtf($arr, 1);?><li class="review-one flex column">    <div class="flex gap-20">        <div class="circle-wrapper img-wrapper">            <img width="80" height="80" src="./IMG/img100x100/<?=$arr['user']['avatar']?>">        </div>        <div>            <div class="user-name"><?=$arr['user']['login']?></div>            <div class="data-review"><?=$arr['dt']?></div>            <div class="stars-micro flex">                <?php                $stars = (float)$arr['stars'] * 100 / 5;                echo '<div class="stars-shore" style="width: '.$stars.'%"></div>';                for($i=1;$i<=5;++$i) {                    echo '<img width="24" height="24" src="./DOWNLOAD/20230902-204747_id-2-695580.png">';                }                ?>            </div>            <?php if(isset($arr['product'])) {                if($arr['product']['action_list'] === 'СПИСОК') { $arr['product']['action_list'] = 'list'; }                $link = $arr['product']['main_cat']."/".$arr['product']['under_cat']."/".$arr['product']['action_list']."?s=".(int)$arr['shop_id']."&prod=".$arr['product_id']                ?>                <div class="review-product-name"><b>Отзыв о товаре:</b><a href="<?=$link?>"><?=$arr['product']['name']?></a></div>            <?php } ?>        </div>    </div>    <div>        <div class="comment"><?=$arr['html']?></div>        <?php        if($arr['html_ans'] !== null && mb_strlen($arr['html_ans']) >= 2) {            echo "<div class='width100perc flex'><div class='ans-mess'>".$arr['html_ans']."</div></div>";        }        ?>    </div>    <?php    if(Access::userID() === (int)$arr['author_id']) { ?>        <button onclick="open_popup('request', {id: <?=$arr['id']?>, type: '<?=$arr['type_review']?>', only_edit: true})" class="edit-review btn action-btn">Мой отзыв</button>    <?php }    if(Access::userID() === (int)$arr['owner_product']) { ?>    <button onclick="open_popup('request', {id: <?=$arr['id']?>, type: 'ответ', only_edit: true})" class="edit-review btn action-btn">Ответить</button>    <?php } ?></li>