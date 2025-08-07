<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI; // using openai-php/client facade helper

class ChatController extends Controller
{
    public function send(Request $request, Agent $agent)
    {
        $data = $request->validate([
            'message' => 'required|string|max:4000',
            'thread_id' => 'nullable|integer',
        ]);

        $thread = $data['thread_id'] ? ChatThread::findOrFail($data['thread_id']) : ChatThread::create([
            'user_id' => $request->user()->id,
            'agent_id' => $agent->id,
            'title' => null,
            'is_public' => false,
        ]);

        ChatMessage::create([
            'chat_thread_id' => $thread->id,
            'role' => 'user',
            'content' => $data['message'],
        ]);

        $messages = $thread->messages()->orderBy('id')->get()->map(function ($m) {
            return ['role' => $m->role, 'content' => $m->content];
        })->toArray();

        if ($agent->prompt) {
            array_unshift($messages, ['role' => 'system', 'content' => $agent->prompt]);
        }

        $client = OpenAI::client(config('services.openai.api_key'));
        $resp = $client->chat()->create([
            'model' => $agent->model,
            'messages' => $messages,
            'temperature' => $agent->temperature ?? 1.0,
        ]);

        $answer = $resp->choices[0]->message->content ?? '';

        ChatMessage::create([
            'chat_thread_id' => $thread->id,
            'role' => 'assistant',
            'content' => $answer,
        ]);

        return response()->json([
            'thread_id' => $thread->id,
            'message' => $answer,
        ]);
    }
}