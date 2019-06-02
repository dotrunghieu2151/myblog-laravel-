<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as FormRequest;

class Helper
{
    public static function setActive($path, $active = 'active')
    {
        return Request::is("$path/*") || Request::is("$path") ? $active : '';
    }
    public static function handleImage(FormRequest $request)
    {
        $fileNameWithEXT = $request->file("cover_image")->getClientOriginalName();
        // get just the filename
        $fileName = pathinfo($fileNameWithEXT, PATHINFO_FILENAME);
        // get just extension
        $extension = $request->file("cover_image")->getClientOriginalExtension();
        // filename to store
        $fileNameToStore = $fileName.'_'.time().".".$extension;
        // upload
        $path = $request->file("cover_image")->storeAs('public/cover_images', $fileNameToStore);
        return $fileNameToStore;
    }
    public static function escape_like(string $value, string $char = '\\')
    {
        return str_replace(
            [$char, '%', '_'],
            [$char.$char, $char.'%', $char.'_'],
            $value
        );
    }
}
