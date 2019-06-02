<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Str;
use App\Helpers\Helper;

class Post extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo('App\User','userId');
    }
    public function genderOptions()
    {
         return [
            "0" => "For female readers",
            "1" => "For male readers",
            "2" => "For everyone"
        ];
    }
     public static function createSlug($title, int $exceptId = 0)
     {
         $url_title = Str::slug($title, '-');
         $sanitized = Helper::escape_like($url_title);
         $existed_slugs = self::select('url_title')
                            ->where('url_title','like',"$sanitized%")
                            ->where('id','<>',$exceptId)
                            ->get();
         if (!$existed_slugs->contains('url_title',$url_title)) {
             return $url_title;
         }
         for ($i = 1; $i <= $existed_slugs->count(); $i++) {
             $new_url_title = "$url_title-$i";
             if (!$existed_slugs->contains('url_title',$new_url_title)) {
                 return $new_url_title;
             }
         }
     }
}
