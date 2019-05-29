<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class Helper
{
    public static function setActive($path, $active = 'active')
    {
        return Request::is("$path/*") || Request::is("$path") ? $active : '';
    }
}
