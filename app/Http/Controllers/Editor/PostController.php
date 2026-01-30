<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of editor's posts.
     */
    public function index(Request $request)
    {
        $query = Post::where('user_id', auth()->id());

        // Filter by search
        if ($search = $request->get('search')) {
            $query->where('titulo', 'like', "%{$search}%");
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $posts = $query->orderBy('updated_at', 'desc')->paginate(15);

        return view('editor.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('editor.posts.create');
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'nullable|string|max:500',
            'conteudo' => 'required|string',
            'categoria' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        // Auto-generate slug
        $validated['slug'] = Str::slug($validated['titulo']);
        
        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Post::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        // Handle tags
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'rascunho'; // Always save as draft
        $validated['is_featured'] = $request->boolean('is_featured');

        $post = Post::create($validated);

        ActivityLog::log('create', "Criou rascunho: {$post->titulo}", $post);

        return redirect()
            ->route('editor.posts.edit', $post)
            ->with('success', 'Rascunho salvo com sucesso! Revise e publique quando estiver pronto.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $this->authorizePost($post);

        return view('editor.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $this->authorizePost($post);

        return view('editor.posts.edit', compact('post'));
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorizePost($post);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'nullable|string|max:500',
            'conteudo' => 'required|string',
            'categoria' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $oldValues = $post->toArray();

        // Handle tags
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        } else {
            $validated['tags'] = null;
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        $post->update($validated);

        ActivityLog::log('update', "Atualizou artigo: {$post->titulo}", $post, $oldValues, $post->toArray());

        return redirect()
            ->route('editor.posts.edit', $post)
            ->with('success', 'Alterações salvas com sucesso!');
    }

    /**
     * Publish the specified post.
     */
    public function publish(Post $post)
    {
        $this->authorizePost($post);

        if ($post->status === 'publicado') {
            return redirect()->back()->with('error', 'Este artigo já está publicado.');
        }

        $post->update([
            'status' => 'publicado',
            'published_at' => now(),
        ]);

        ActivityLog::log('publish', "Publicou artigo: {$post->titulo}", $post);

        return redirect()
            ->route('editor.posts.index')
            ->with('success', 'Artigo publicado com sucesso!');
    }

    /**
     * Unpublish (return to draft) the specified post.
     */
    public function unpublish(Post $post)
    {
        $this->authorizePost($post);

        $post->update(['status' => 'rascunho']);

        ActivityLog::log('unpublish', "Despublicou artigo: {$post->titulo}", $post);

        return redirect()->back()->with('success', 'Artigo movido para rascunhos.');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        $this->authorizePost($post);

        $titulo = $post->titulo;
        $post->delete();

        ActivityLog::log('delete', "Removeu artigo: {$titulo}");

        return redirect()
            ->route('editor.posts.index')
            ->with('success', 'Artigo removido com sucesso!');
    }

    /**
     * Check if the current user owns the post.
     */
    protected function authorizePost(Post $post): void
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para acessar este artigo.');
        }
    }
}
