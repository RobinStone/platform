<?php$P = new PROFIL(Access::userID());$avatar = $P->get_field('avatar');if(file_exists('./IMG/img100x100/'.$avatar)) {    $avatar = './IMG/img100x100/'.$avatar;} else {    $avatar = './IMG/SYS/user.svg';}