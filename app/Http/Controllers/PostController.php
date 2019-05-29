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
        $this->paginPage = $posts->currentPage();
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
        $title = Post::where("title","=",$request->input("title"))
                                ->first();
        if ($title !== null) {
            return redirect("/posts/create")->withError("This title has been used")
                                            ->withInput();
        }
        $post = new Post;
        $post->title = $request->input("title");
        $post->url_title = str_slug($request->input("title"),'-');
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
    public function show($url_title)
    {
        $post = Post::where("url_title","=",$url_title)->first();
        if ($post === null) {
            return redirect('/posts')->withError("We cannot find this post");
        }
        return view('post.show',["post"=>$post]);
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
        $post = Post::find($id);
        // handle image
         if ($request->hasFile('cover_image')) {
            $fileNameWithEXT = $request->file("cover_image")->getClientOriginalName();
            // get just the filename
            $fileName = pathinfo($fileNameWithEXT, PATHINFO_FILENAME);
            // get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // filename to store
            $fileNameToStore = $fileName.'_'.time().".".$extension;
            // upload to storage
            $path = $request->file("cover_image")->storeAs('public/cover_images', $fileNameToStore);
            // asign to database
            $post->cover_image = $fileNameToStore;          
         } else {
            $post->cover_image = "noimage.jpg";
         }
        if ($request->input('title') !== $post->title &&
            Post::where("title","=",$request->input('title'))->first() !== null) {
            return redirect("/posts/$post->url_title/edit")->withError("This title has been taken");
        }
        $post->title = $request->input('title');
        $post->url_title = str_slug($request->input('title'),'-');
        $post->body = $request->input('body');
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
}
