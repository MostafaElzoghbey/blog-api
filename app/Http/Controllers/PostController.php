<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments'])
            ->latest()
            ->paginate(10);

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        $post->load(['user', 'comments']);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        $post->load(['user', 'comments.user']);
        return response()->json($post);
    }

    public function update(Request $request, Post $post)
    {
        // Check if user owns the post
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        $post->load(['user', 'comments']);

        return response()->json($post);
    }

    public function destroy(Request $request, Post $post)
    {
        // Check if user owns the post
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}
