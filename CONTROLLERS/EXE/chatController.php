<?php
AUTH::token_verification_and_auth();

if(Access::scanLevel() < 1) {
    header('Location: /?auth=true');
    exit;
}