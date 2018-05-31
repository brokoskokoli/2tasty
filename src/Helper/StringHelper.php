<?php

namespace App\Helper;


class StringHelper
{

    public static function splitString(string $string, $delimiters = ['<br />', '<br>', '<p>', '</p>'])
    {
        $splitDelimiterString = implode('|', $delimiters);
        $parts = preg_split( "/ ($splitDelimiterString) /", $string );
        dump($parts);die;
    }
}
