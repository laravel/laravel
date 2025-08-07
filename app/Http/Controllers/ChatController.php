<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use Illuminate\Http\Request;
use OpenAI;

class ChatController extends Controller
{
    protected array $bannedWords = ['badword1','badword2'];

    protected function filterBadWords(string $text): string
    {
        foreach ($this->bannedWords as $w) {
            $text = preg_replace('/'.preg_quote($w, '/').'/i', str_repeat('*', strlen($w)), $text);
        }
        return $text;
    }

    public function send(Request $request, Agent $agent)
    {
        $user = $request->user();
        $requiredMin = $agent->requiredMinCredits();
        if ($requiredMin !== null && $user->credits < $requiredMin) {
            return response()->json(['error' => 'This agent requires at least '.$requiredMin.' credits.'], 403);
        }
        if ($user->credits <= 0) {
            return response()->json(['error' => 'Insufficient credits'], 402);
        }

        $data = $request->validate([
            'message' => 'required|string|max:4000',
            'thread_id' => 'nullable|integer',
        ]);

        $message = $this->filterBadWords($data['message']);

        $thread = $data['thread_id'] ? ChatThread::findOrFail($data['thread_id']) : ChatThread::create([
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'title' => null,
            'is_public' => false,
        ]);

        ChatMessage::create([
            'chat_thread_id' => $thread->id,
            'role' => 'user',
            'content' => $message,
        ]);

        $messages = $thread->messages()->orderBy('id')->get()->map(fn($m) => ['role' => $m->role, 'content' => $m->content])->toArray();
        if ($agent->prompt) array_unshift($messages, ['role' => 'system', 'content' => $agent->prompt]);

        $client = OpenAI::client(config('services.openai.api_key'));
        $params = [
            'model' => $agent->model,
            'messages' => $messages,
            'temperature' => $agent->temperature ?? 1.0,
        ];
        if ($agent->max_tokens) $params['max_tokens'] = $agent->max_tokens;
        if ($agent->top_p) $params['top_p'] = $agent->top_p;
        if ($agent->frequency_penalty) $params['frequency_penalty'] = $agent->frequency_penalty;
        if ($agent->presence_penalty) $params['presence_penalty'] = $agent->presence_penalty;

        $resp = $client->chat()->create($params);

        $answer = $resp->choices[0]->message->content ?? '';
        $promptTokens = $resp->usage->promptTokens ?? 0;
        $completionTokens = $resp->usage->completionTokens ?? 0;

        ChatMessage::create([
            'chat_thread_id' => $thread->id,
            'role' => 'assistant',
            'content' => $answer,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
        ]);

        $deduct = ($promptTokens + $completionTokens) > 0 ? ($promptTokens + $completionTokens) : 1;
        $user->decrement('credits', $deduct);

        return response()->json([
            'thread_id' => $thread->id,
            'message' => $answer,
            'deducted' => $deduct,
            'remaining_credits' => $user->fresh()->credits,
        ]);
    }

    public function guest(Request $request, Agent $agent)
    {
        $data = $request->validate([
            'message' => 'required|string|max:4000',
            'thread_key' => 'nullable|string',
        ]);

        $trialUsed = (int) $request->session()->get('guest_trial_count', 0);
        if ($trialUsed >= 3) {
            return response()->json(['error' => 'Trial limit reached. Please log in.'], 429);
        }

        $message = $this->filterBadWords($data['message']);

        $messages = [];
        if ($agent->prompt) $messages[] = ['role' => 'system', 'content' => $agent->prompt];
        $messages[] = ['role' => 'user', 'content' => $message];

        $client = OpenAI::client(config('services.openai.api_key'));
        $resp = $client->chat()->create([
            'model' => $agent->model,
            'messages' => $messages,
            'temperature' => $agent->temperature ?? 1.0,
            'max_tokens' => $agent->max_tokens ?? 256,
        ]);

        $answer = $resp->choices[0]->message->content ?? '';

        $request->session()->put('guest_trial_count', $trialUsed + 1);

        return response()->json([
            'message' => $answer,
            'remaining_trial' => max(0, 3 - ($trialUsed + 1)),
        ]);
    }
}