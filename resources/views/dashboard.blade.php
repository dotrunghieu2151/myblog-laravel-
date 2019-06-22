@extends('layout.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <li><a href="/posts/create" class="btn btn-primary">Create post</a></li>
                    <h3>Your posted blogs</h3>
                    @if (count($posts) > 0 )
                    <a class="btn btn-success" href="{{route('excel.download')}}">Export to excel</a>
                    <table class='table table-striped'>
                        <tr>
                            <th>Title</th>
                            <th></th>
                            <th></th>
                        </tr>
                        @foreach ($posts as $post)
                            <tr>
                                <td>{{$post->title}}</td>
                                <td><a href="/posts/{{$post->url_title}}/edit">Edit</a></td>
                                <td>
                                     <form action="/posts/{{$post->id}}" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="submit" value="delete" name="submit" class="btn btn-danger">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    @else 
                    <p>You have no posts</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
