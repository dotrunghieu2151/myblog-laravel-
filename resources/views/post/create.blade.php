@extends('layout.main')
@section('title','My Blog - Posts')
@section('content')
    <h1>Create Post</h1>
    <form action='/posts' method='POST' enctype="multipart/form-data">
        <label>title</label>
        <input type='text' name='title' value="{{ old('title') }}"> 
        <label>Body</label>
        <input type='textarea' name='body' value='{{ old('body') }}'>
        @csrf
        <div class="form-group">
            <input type="file" name="cover_image">
        </div>
        <input type='submit' name='submit'>
    </form>
@endsection