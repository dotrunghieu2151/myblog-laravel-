@extends('layout.main')
@section('title','My Blog - Posts')
@section('content')
    <h1>Edit Post</h1>
    <form action='/posts/{{$post->id}}' method='POST' enctype="multipart/form-data">
        <label>title</label>
        <input type='text' name='title' value="{{old('title') ?? $post->title}}">
        <label>Body</label>
        <input type='textarea' name='body' value="{{old('body') ?? $post->body}}">
        <div class="col-md-6">
            <select name="gender">
                @foreach ($post->genderOptions() as $key => $option)
                <option value="{{$key}}" {{$post->gender == $key ? 'selected' : ''}}>
                    {{$option}}
                </option>
                @endforeach
            </select>
        </div>
        @csrf
        @method('PUT')
        <input type="file" name="cover_image">
        <input type='submit' name='submit'>
    </form>
@endsection