<?php

namespace App\URLParser;


class URLParser
{
    /** @var array|URLParserBase[]  */
    protected static $parser = [];

    public static function readParser()
    {
        if (empty(self::$parser)) {
            foreach (scandir(__DIR__) as $file)
            {
                $filename = realpath(__DIR__.'/'.$file);
                if (is_file($filename)) {
                    require_once($filename);

                    // get the file name of the current file without the extension
                    // which is essentially the class name
                    $class = __NAMESPACE__.'\\'.basename($file, '.php');

                    if (class_exists($class))
                    {
                        if (method_exists($class, 'canHandleUrl')) {
                            self::$parser[] = new $class;
                        }
                    }
                }
            }
        }
    }

    public static function getParser($url) : ?URLParserBase
    {
        self::readParser();
        foreach (self::$parser as $parser) {
            if ($parser->canHandleUrl($url)) {
                return $parser;
            }
        }

        return null;
    }
}