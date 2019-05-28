<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except'=>['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('created_at','desc')->paginate(5);
        return view('post.index',['posts'=>$posts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "title" => 'required',
            "body" => 'required',
            "cover_image" =>'image|nullable|max:1999'
        ]);
        // handle file upload
        if ($request->hasFile('cover_image')) {
            $fileNameWithEXT = $request->file("cover_image")->getClientOriginalName();
            // get just the filename
            $fileName = pathinfo($fileNameWithEXT, PATHINFO_FILENAME);
            // get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // filename to store
            $fileNameToStore = $fileName.'_'.time().".".$extension;
            // upload
            $path = $request->file("cover_image")->storeAs('public/cover_images', $fileNameToStore);
        } else {
            $fileNameToStore = "noimage.jpg";
        }
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->userId = auth()->user()->id;
        $post->cover_image = $fileNameToStore;
        $post->save();
        return redirect('/posts')->with('success','Post created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('post.show',["post"=>$post]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        // check for correct user and post
        if (auth()->user()->id !== $post->user->id) {
            return redirect('/posts')->with('error',"Unauthorized post");
        }
        return view("post.edit",["post"=>$post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
          $this->validate($request, [
            "title" => 'required',
            "body" => 'required',
            "cover_image" =>'image|nullable|max:1999'
        ]);
         if ($request->hasFile('cover_image')) {
            $fileNameWithEXT = $request->file("cover_image")->getClientOriginalName();
            // get just the filename
            $fileName = pathinfo($fileNameWithEXT, PATHINFO_FILENAME);
            // get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // filename to store
            $fileNameToStore = $fileName.'_'.time().".".$extension;
            // upload
            $path = $request->file("cover_image")->storeAs('public/cover_images', $fileNameToStore);
        }
        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        if ($request->hasFile('cover_image')) {
            $post->cover_image = $fileNameToStore;
        }
        $post->cover_image = $fileNameToStore;
        $post->save();
        return redirect('/posts')->with('success','Post updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if (auth()->user()->id !== $post->user->id) {
            return redirect('/posts')->with('error',"Unauthorized post");
        }
        if ($post->cover_image != "noimage.jpg") {
            Storage::delete("public/cover_images/$post->cover_image");
        }
        $post->delete();
        return redirect("/posts")->with("success","Post removed");
    }
}
