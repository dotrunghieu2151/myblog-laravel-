@extends('layout.main')
@section('title','My Blog - Posts')
@section('content')
    <h1>Blog Post</h1>
    @if (count($posts) > 0)
        @foreach ($posts as $post)
            <div class="well">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <img style="width:100%;" src="/storage/cover_images/{{$post->cover_image}}">
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <h3><a href="/posts/{{$post->url_title}}">{{$post->title}}</a></h3>
                        <small>Written at {{$post->created_at->format('d-m-Y')}} - {{$post->created_at->diffForHumans()}}</small>
                        <small>Written by {{$post->user->name}}</small>
                        <p><strong>{{$post->genderOptions()[$post->gender]}}</strong></p>
                    </div>
                </div>                
            </div>
        @endforeach
        {{$posts->links()}};
    @else
        <p>No posts found</p>
    @endif
@endsection