<?php

namespace App\Exports;

use App\Post;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Post::where('userId',auth()->user()->id)->get();
    }
    
    public function map($post): array
    {
        return [
            $post->title,
            $post->body,
            $post->created_at,
            $post->genderOptions()[$post->gender],
            $post->user->name
        ];
    }
     public function headings(): array
    {
        return [
            'Post Title',
            'Post body',
            'Post submitted at',
            'Post restriction',
            'Author Name'
        ];
    }
}
