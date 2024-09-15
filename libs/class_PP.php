<?php
class PP {
    public static function _(): PROFIL|bool
    {
        if(Access::scanLevel() > 0) {
            return PROFIL::init(Access::userID());
        }
        return false;
    }
}