@extends('layout.main')
@section('title','My Blog - Posts')
@section('content')
    <h1>Edit Post</h1>
    <form action='/posts/{{$post->id}}' method='POST' enctype="multipart/form-data">
        <label>title</label>
        <input type='text' name='title' value="{{$post->title}}">
        <label>Body</label>
        <input type='textarea' name='body' value="{{$post->body}}">
        {{csrf_field()}}
        <input type="hidden" name="_method" value="PUT">
        <input type="file" name="cover_image">
        <input type='submit' name='submit'>
    </form>
@endsection