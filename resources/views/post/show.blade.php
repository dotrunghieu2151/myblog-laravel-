@extends('layout.main')
@section('title',"Blog show")
@section('content')   
    <div class="well">
        <h3>{{$post->title}}</h3>
        <img style="width:100%;" src="/storage/cover_images/{{$post->cover_image}}">
        <br><br>
        <small>Written at {{$post->created_at->format('d-m-Y')}} - {{$post->created_at->diffForHumans()}}</small>
        <small>Written by {{$post->user->name}}</small>
        <h4>{{$post->body}}</h4>        
    </div>
    @auth
        @if (Auth::user()->id === $post->user->id )
            <a href="/posts/{{$post->url_title}}/edit" class="btn btn-success">Edit</a>
            <form action="/posts/{{$post->id}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="DELETE">
                <input type="submit" value="delete" name="submit" class="btn btn-danger">
            </form>
        @endif
    @endauth
@endsection