<section class="reviews-all wrapper flex gap-20">    <div class="columns column-left">        <?=render('user-micro-card', ['login'=>$P->get_field('login'), 'self'=>true, 'my_subscr_count'=>$my_subscr_count])?>        <div class="menu-left-profil flex column gap-10">        </div>    </div>    <div class="columns column-right">        <?php        foreach($reviews as $v) {            echo render('review_one', ['arr'=>$v]);        }        ?>    </div></section><?php echo render('tiny', ['stars'=>true]); ?>