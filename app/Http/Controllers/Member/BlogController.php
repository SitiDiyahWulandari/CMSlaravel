<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        // $search = $request->search;
    
        // return view('member.blogs.index', [
        //     'Post' => Post::where('user_id', $user->id)
        //         ->when($search, function ($query) use ($search) {
        //             $query->where('title', 'like', "%{$search}%")
        //                 ->orWhere('content', 'like', "%{$search}%");
        //         })
        //         ->orderBy('id', 'desc')
        //         ->paginate(3)
        //         ->withQueryString()
        // ]);
        $key = $request->search;
        if ($key != null) {
            $posts = Post::where('title', 'like', "%$key%")->get();
        } else {
            $posts = Post::all();
        }
        return view('member.blogs.index',[
            'posts' => $posts, 
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('member.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'image|mimes:jpeg,jpg,png|max:10240'
        ], [
            'title.required' => 'Judul wajib diisi',
            'content.required' => 'Konten wajib diisi',
            'thumbnail.image' => 'Hanya gambar yang diperbolehkan',
            'thumbnail.mimes' => 'Ekstensi yang diperbolehkan hanya JPEG, JPG, dan PNG',
            'thumbnail.max' => 'Ukuran maksimum untuk thumbnail adalah 10MB',
        ]);

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $image_name = time() . "_" . $image->getClientOriginalName();
            $destination_path = public_path(getenv('CUSTOM_TUMBNAIL_LOCATION'));
            $image->move($destination_path, $image_name);
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'status' => $request->status,
            'thumbnail' => isset($image_name) ? $image_name : null,
            'slug' => $this->generateSlug($request->title),
            'user_id'=> Auth::user()->id
        ];

        Post::create($data);
        return redirect()->route('member.blogs.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // Implement this method if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('member.blogs.edit', ['data' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'image|mimes:jpeg,jpg,png|max:10240'
        ], [
            'title.required' => 'Judul wajib diisi',
            'content.required' => 'Konten wajib diisi',
            'thumbnail.image' => 'Hanya gambar yang diperbolehkan',
            'thumbnail.mimes' => 'Ekstensi yang diperbolehkan hanya JPEG, JPG, dan PNG',
            'thumbnail.max' => 'Ukuran maksimum untuk thumbnail adalah 10MB',
        ]);

        if ($request->hasFile('thumbnail')) {
            if (isset($post->thumbnail) && file_exists(public_path(getenv('CUSTOM_TUMBNAIL_LOCATION')) . "/" . $post->thumbnail)) {
                unlink(public_path(getenv('CUSTOM_TUMBNAIL_LOCATION')) . "/" . $post->thumbnail);
            }

            $image = $request->file('thumbnail');
            $image_name = time() . "_" . $image->getClientOriginalName();
            $destination_path = public_path(getenv('CUSTOM_TUMBNAIL_LOCATION'));
            $image->move($destination_path, $image_name);
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'status' => $request->status,
            'thumbnail' => isset($image_name) ? $image_name : $post->thumbnail,
            'slug' => $this->generateSlug($request->title, $post->id)
        ];

        Post::where('id', $post->id)->update($data);
        return redirect()->route('member.blogs.index')->with('success', 'Data berhasil di-update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (isset($post->thumbnail) && file_exists(public_path(getenv('CUSTOM_TUMBNAIL_LOCATION')) . "/" . $post->thumbnail)) {
            unlink(public_path(getenv('CUSTOM_TUMBNAIL_LOCATION')) . "/" . $post->thumbnail);
        }
        Post::where('id', $post->id)->delete();
       return redirect()->route('member.blogs.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Generate a unique slug for the post.
     */
    private function generateSlug($title, $id = null){
        $slug = Str::slug($title);
        $count = Post::where('slug', $slug)->when($id, function ($query, $id) {
            return $query->where('id', '!=', $id);
        })->count();

        if($count > 0){
            $slug = $slug."-".($count + 1);
        }
        return $slug;
    }
}
