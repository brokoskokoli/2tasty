<?php

namespace App\Helper;

class SecurityHelper
{
    public static function generatePassword($length = 8)
    {
        $keySpace = 'abcdefghklmnpqrstuvwxyzABCDEFGHKLMNPQRSTUVWXYZ23456789';
        $max = mb_strlen($keySpace, '8bit') - 1;

        for ($i = 0, $str = ''; $i < $length; ++$i) {
            $str .= $keySpace[random_int(0, $max)];
        }

        return $str;
    }

    public static function generatePasswordResetToken()
    {
        return sha1(uniqid());
    }

}
