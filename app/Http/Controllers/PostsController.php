<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; //bringing the storage library to be able to delete images
use App\Post; //this is to get it from the model Post
use DB;// If i want to use SQL instead of Eloquent

class PostsController extends Controller
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   //we added this constructor to make sure that we won't be able to reach create posts unless we are logged in but we will put an exception
        $this->middleware('auth',['except'=>['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   //If I want to use SQL
        //$posts= DB::select('SELECT * FROM posts');

        //Here it will get all the posts
        //$posts = Post::all();
        //We made it descanding because we want it most recent post first
        //$posts = Post::orderBy('title','desc')->get();

        //here instead of get we made paginate which will view only posts in the same pgae and to view others i press page 2
        $posts = Post::orderBy('created_at','desc')->paginate(10);
        return view('posts.index')->with('posts',$posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=> 'required',
            'body'=> 'required',
            'cover_image' => 'image|nullable|max:1999'
        ]);
        //Handle file upload
        //this to check if he actually pressed the button to choose file
        if ($request->hasFile('cover_image')) {
            //Get file name with the extension 
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            //Get just file name
            //pathinfo to exract the name without extension it is php function
            $filename = pathinfo($filenameWithExt,PATHINFO_FILENAME);
            //Get just ext
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //Filename to store
            //so like that each file will have a unique name and overwriting won't happen
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            //Upload Image
            //Here folder cover_images will be created in storage public but this is not accessible from the web so  you won't be able to upload
            //a pic so we need to move it to public instead of storage public by typing a command in terminal- php artisan storage:link
            $path = $request->file('cover_image')->storeAs('public/cover_images',$fileNameToStore);
        }else {
            $fileNameToStore = 'noimage.jpg';
        }
        //Create post
        $post = new Post;
        $post->title= $request->input('title');
        $post->body= $request->input('body');
        $post->user_id=auth()->user()->id;
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
    {   //it find the post and shows it
        $post = Post::find($id);
        return view('posts.show')->with('post',$post);
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

        //Check for correct user
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error','Unauthorized Page');
        }

        return view('posts.edit')->with('post',$post);
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
        $this->validate($request,[
            'title'=> 'required',
            'body'=> 'required'
        ]);
        
        //Handle file upload
        //this to check if he actually pressed the button to choose file
        if ($request->hasFile('cover_image')) {
            //Get file name with the extension 
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            //Get just file name
            //pathinfo to exract the name without extension it is php function
            $filename = pathinfo($filenameWithExt,PATHINFO_FILENAME);
            //Get just ext
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //Filename to store
            //so like that each file will have a unique name and overwriting won't happen
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            //Upload Image
            //Here folder cover_images will be created in storage public but this is not accessible from the web so  you won't be able to upload
            //a pic so we need to move it to public instead of storage public by typing a command in terminal- php artisan storage:link
            $path = $request->file('cover_image')->storeAs('public/cover_images',$fileNameToStore);
        }
        //we removed the else because if he didn't update the image we don't want it to be replace with  no image

        //Update post
        $post = Post::find($id);
        $post->title= $request->input('title');
        $post->body= $request->input('body');
        if ($request->hasFile('cover_image')) {
            if ($post->cover_image != 'noimage.jpg') {
                //when we update we want to delete the old image
                Storage::delete('public/cover_images/'.$post->cover_image);
            }
            $post->cover_image = $fileNameToStore;
        }
       
        $post->save();

        return redirect('/posts')->with('success','Post Updated');
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
        
        //Check for correct user
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error','Unauthorized Page');
        }
        //Here we want to delete the images excepet the noimage
        if ($post->cover_image != 'noimage.jpg') {
            Storage::delete('public/cover_images/'.$post->cover_image);
        }
        
       
        $post->delete();
        return redirect('/posts')->with('success','Post Removed');

    }
}
