<?php$target = (int)$_GET['for'];$my_subscr_count = (int)SQL_ROWS(q("SELECT COUNT(*) FROM `subscriptions` WHERE `subscr_id`=".$target." "))[0]['COUNT(*)'];$P = PROFIL::create($target, PROFIL_TYPE::id);INCLUDE_CLASS('shops', 'reviews_prod');$reviews = REVIEWS_PROD::get_all_reviews_at_ouner_product_id($target);