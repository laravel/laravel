<x-guest-layout>
    <div class="max-w-3xl mx-auto py-10">
        <div class="flex items-center space-x-3 mb-4">
            @if($agent->avatar_url)
                <img src="{{ $agent->avatar_url }}" class="w-10 h-10 rounded-full" alt="avatar"/>
            @endif
            <div>
                <h1 class="text-2xl font-semibold">{{ $agent->name }}</h1>
                <p class="text-sm text-gray-500">Model: {{ $agent->model }}</p>
            </div>
        </div>
        <div id="chat" class="border rounded p-4 min-h-64 space-y-3">
            <div class="text-gray-500">{{ $agent->welcome_message }}</div>
        </div>
        <div class="mt-4 flex space-x-2">
            <input id="message" class="flex-1 border rounded p-2" placeholder="Type your message"/>
            <button id="send" class="px-4 py-2 bg-indigo-600 text-white rounded">Send</button>
        </div>
        <div class="mt-2 text-sm text-gray-500">@auth You're logged in, full credits apply. @else You have 3 trial messages before login. @endauth</div>
    </div>
    @vite(['resources/js/app.js'])
    <script>
        const chat = document.getElementById('chat');
        const input = document.getElementById('message');
        const btn = document.getElementById('send');
        let threadId = null;
        btn.addEventListener('click', async () => {
            const text = input.value.trim();
            if (!text) return;
            input.value = '';
            const userDiv = document.createElement('div');
            userDiv.className = 'text-right';
            userDiv.innerHTML = '<div class="inline-block bg-indigo-50 dark:bg-gray-800 rounded p-2">'+
              document.createTextNode(text).textContent +'</div>';
            chat.appendChild(userDiv);
            const replyDiv = document.createElement('div');
            replyDiv.innerHTML = '<div class="inline-block bg-gray-50 dark:bg-gray-800 rounded p-2">...</div>';
            chat.appendChild(replyDiv);
            try {
                const url = @json(auth()->check() ? url('/api/chat/'.$agent->slug.'/send') : url('/api/chat/'.$agent->slug.'/guest'));
                const payload = @json(auth()->check()) ? {message: text, thread_id: threadId} : {message: text};
                const res = await axios.post(url + (threadId && @json(auth()->check()) ? ('?thread_id='+threadId) : ''), payload);
                if (res.data.thread_id) threadId = res.data.thread_id;
                const suffix = res.data.remaining_trial !== undefined ? ` (Trials left: ${res.data.remaining_trial})` : '';
                replyDiv.innerHTML = '<div class="inline-block bg-gray-50 dark:bg-gray-800 rounded p-2">'+
                  (res.data.message || '(empty)') + suffix +'</div>';
            } catch (e) {
                replyDiv.innerHTML = '<div class="inline-block bg-red-50 text-red-700 rounded p-2">'+ (e.response?.data?.error || 'Error') +'</div>';
            }
        });
        input.addEventListener('keydown', (e) => { if (e.key === 'Enter') btn.click(); });
    </script>
</x-guest-layout>