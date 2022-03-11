<?php


namespace App\Utils;




class CreateRandomPassword
{


    public function createRandomPassword(): string
    {
        $character = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@$!%*?&0123456789";
        $shuffle = str_shuffle($character);
        $randomPassword = substr($shuffle,0,12);

        return $randomPassword;
    }
}