<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(Request $request)
    {
        $query = Post::with('user');

        // Filter by search
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by category
        if ($categoria = $request->get('categoria')) {
            $query->where('categoria', $categoria);
        }

        // Filter by author (for admin view)
        if ($author = $request->get('author')) {
            $query->where('user_id', $author);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);

        $categorias = Post::distinct()->whereNotNull('categoria')->pluck('categoria');

        return view('admin.posts.index', compact('posts', 'categorias'));
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'nullable|string',
            'conteudo' => 'required|string',
            'categoria' => 'nullable|string|max:100',
            'status' => 'required|in:rascunho,publicado,arquivado',
            'is_featured' => 'boolean',
        ]);

        $oldValues = $post->toArray();

        $validated['is_featured'] = $request->boolean('is_featured');

        // If publishing for the first time, set published_at
        if ($validated['status'] === 'publicado' && $post->status !== 'publicado') {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        ActivityLog::log('update', "Atualizou post: {$post->titulo}", $post, $oldValues, $post->toArray());

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post atualizado com sucesso!');
    }

    /**
     * Publish a post.
     */
    public function publish(Post $post)
    {
        $post->publish();

        ActivityLog::log('publish', "Publicou post: {$post->titulo}", $post);

        return redirect()->back()->with('success', 'Post publicado com sucesso!');
    }

    /**
     * Archive a post.
     */
    public function archive(Post $post)
    {
        $post->update(['status' => 'arquivado']);

        ActivityLog::log('archive', "Arquivou post: {$post->titulo}", $post);

        return redirect()->back()->with('success', 'Post arquivado com sucesso!');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        $titulo = $post->titulo;
        $post->delete();

        ActivityLog::log('delete', "Removeu post: {$titulo}");

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post removido com sucesso!');
    }
}
