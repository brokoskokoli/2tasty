<?php

namespace App\Helper;


class FileHelper
{

    public static function getTempFileName()
    {
        return sys_get_temp_dir() . '/' . 'tempfile' . uniqid().'.tmp';
    }
}
