<?php
class PP {
    public static function _(): ?PROFIL
    {
        return PROFIL::init(Access::userID());
    }
}