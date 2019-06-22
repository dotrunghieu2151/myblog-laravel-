<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \App\Http\Requests\PostsRequest;
use App\Post;
use \App\Exports\PostsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\Helper;

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
        $posts = Post::with('user')->orderBy('created_at','desc')->paginate(5);
        return view('post.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $post = new Post;
        return view('post.create',compact('post'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostsRequest $request)
    {    
        // handle file upload
        if ($request->hasFile('cover_image')) {
            $fileNameToStore = Helper::handleImage($request);
        } else {
            $fileNameToStore = "noimage.jpg";
        }
        $url_title =  Post::createSlug($request->title);
        Post::create([
            'title' => $request->title,
            'url_title' => $url_title,
            'body' => $request->body,
            'userId' => auth()->user()->id,
            'cover_image' => $fileNameToStore,
            'gender' => $request->gender
        ]);
        return redirect('/posts')->with('success','Post created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($url_title)
    {
        $post = Post::where("url_title","=",$url_title)->first();
        if ($post === null) {
            return redirect('/posts')->withError("We cannot find this post");
        } elseif ($post->gender !== "2" && auth()->user() === null) {
            return redirect()->back()->withError("This post is gender specify. You need to login");
        } elseif ($post->gender !== "2" && 
                  auth()->user()->gender !== $post->gender &&
                  auth()->user()->id !== $post->userId) {
            return redirect()->back()->withError("This is {$post->genderOptions()[$post->gender]} only.");
        } 
        return view('post.show',compact('post'));   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($url_title)
    {
        $post = Post::where("url_title","=",$url_title)->first();
        if ($post === null) {
            return redirect('/posts')->withError("We cannot find this post");
        }
        // check for correct user and post
        if (auth()->user()->id !== $post->user->id) {
            return redirect('/posts')->withError("Unauthorized post");
        }
        return view("post.edit",compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostsRequest $request, $id)
    {
        $post = Post::find($id);
        if (auth()->user()->id !== $post->user->id) {
            return redirect('/posts')->withError("Unauthorized post");
        }
        // handle image
        if ($request->hasFile('cover_image')) {
             $post->cover_image = Helper::handleImage($request);     
        }
        $url_title = Post::createSlug($request->title, $id);
        $post->title = $request->input('title');
        $post->url_title = $url_title;
        $post->body = $request->input('body');
        $post->gender = $request->gender;
        $post->save();
        return redirect("/posts")->with('success','Post updated');
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
    public function export()
    {
        if (auth()->user() === null)
        {
            return redirect('/posts')->withError("you need to login to use the feature");
        }
        return Excel::download(new PostsExport, "posts-".auth()->user()->name.".xlsx");     
    }
}
