<?php

namespace App\Helper;


class StringHelper
{

    public static function splitString(string $string, $delimiters = ['<br />', '<br>', '<p>', '</p>'])
    {
        foreach ($delimiters as &$delimiter) {
            $delimiter = preg_quote($delimiter, '/');
        }
        $splitDelimiterString = implode('|', $delimiters);
        $regexSplit = "/($splitDelimiterString)/";
        $parts = preg_split( $regexSplit, $string );
        $result = [];
        foreach ($parts as $part) {
            $preparedPart = trim($part);
            if ($preparedPart != '') {
                $result[] = $preparedPart;
            }
        }

        return $result;
    }
}
