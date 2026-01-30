<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Public listing for website (published only)
     */
    public function publicList(Request $request)
    {
        $query = Post::where('status', 'publicado')
            ->with('user:id,name')
            ->orderBy('published_at', 'desc');

        if ($request->has('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->has('limit')) {
            return response()->json($query->take($request->limit)->get());
        }

        return response()->json($query->paginate(12));
    }

    /**
     * Show single post by slug (public)
     */
    public function showBySlug(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'publicado')
            ->with('user:id,name')
            ->firstOrFail();

        return response()->json($post);
    }

    /**
     * List all posts (admin)
     */
    public function index(Request $request)
    {
        $query = Post::with('user:id,name');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('resumo', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($posts);
    }

    /**
     * List user's own posts (editor)
     */
    public function myPosts(Request $request)
    {
        $query = Post::where('user_id', $request->user()->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($posts);
    }

    /**
     * Store new post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'required|string|max:500',
            'conteudo' => 'required|string',
            'categoria' => 'nullable|string|max:100',
            'imagem_destaque' => 'nullable|image|max:4096',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['titulo']);
        $validated['status'] = 'rascunho';

        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Post::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        if ($request->hasFile('imagem_destaque')) {
            $validated['imagem_destaque'] = $request->file('imagem_destaque')->store('posts', 'public');
        }

        $post = Post::create($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_post',
            'description' => "Criou artigo: {$post->titulo}",
            'model_type' => Post::class,
            'model_id' => $post->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json($post, 201);
    }

    /**
     * Show single post
     */
    public function show(Request $request, Post $post)
    {
        // Check if editor can only view own posts
        if ($request->user()->role === 'editor' && $post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json($post->load('user:id,name'));
    }

    /**
     * Update post
     */
    public function update(Request $request, Post $post)
    {
        // Check ownership for editors
        if ($request->user()->role === 'editor' && $post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'required|string|max:500',
            'conteudo' => 'required|string',
            'categoria' => 'nullable|string|max:100',
            'imagem_destaque' => 'nullable|image|max:4096',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        // Update slug only if title changed
        if ($validated['titulo'] !== $post->titulo) {
            $validated['slug'] = Str::slug($validated['titulo']);
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Post::where('slug', $validated['slug'])->where('id', '!=', $post->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        if ($request->hasFile('imagem_destaque')) {
            if ($post->imagem_destaque) {
                Storage::disk('public')->delete($post->imagem_destaque);
            }
            $validated['imagem_destaque'] = $request->file('imagem_destaque')->store('posts', 'public');
        }

        $post->update($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_post',
            'description' => "Atualizou artigo: {$post->titulo}",
            'model_type' => Post::class,
            'model_id' => $post->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json($post);
    }

    /**
     * Publish post
     */
    public function publish(Request $request, Post $post)
    {
        // Check ownership for editors
        if ($request->user()->role === 'editor' && $post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $post->update([
            'status' => 'publicado',
            'published_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'publish_post',
            'description' => "Publicou artigo: {$post->titulo}",
            'model_type' => Post::class,
            'model_id' => $post->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Artigo publicado com sucesso', 'post' => $post]);
    }

    /**
     * Unpublish post (back to draft)
     */
    public function unpublish(Request $request, Post $post)
    {
        if ($request->user()->role === 'editor' && $post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $post->update([
            'status' => 'rascunho',
            'published_at' => null,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'unpublish_post',
            'description' => "Despublicou artigo: {$post->titulo}",
            'model_type' => Post::class,
            'model_id' => $post->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Artigo movido para rascunho', 'post' => $post]);
    }

    /**
     * Archive post (admin only)
     */
    public function archive(Request $request, Post $post)
    {
        $post->update(['status' => 'arquivado']);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'archive_post',
            'description' => "Arquivou artigo: {$post->titulo}",
            'model_type' => Post::class,
            'model_id' => $post->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Artigo arquivado', 'post' => $post]);
    }

    /**
     * Delete post
     */
    public function destroy(Request $request, Post $post)
    {
        // Check ownership for editors
        if ($request->user()->role === 'editor' && $post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $titulo = $post->titulo;

        if ($post->imagem_destaque) {
            Storage::disk('public')->delete($post->imagem_destaque);
        }

        $post->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_post',
            'description' => "Removeu artigo: {$titulo}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Artigo removido com sucesso']);
    }
}
