<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $agent->name }} - Embed</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style> body { background: transparent } </style>
</head>
<body>
<div class="p-3">
    <div id="chat" class="border rounded p-3 space-y-3">
        <div class="text-gray-500">{{ $agent->welcome_message }}</div>
    </div>
    <div class="mt-3 flex space-x-2">
        <input id="message" class="flex-1 border rounded p-2" placeholder="Type your message"/>
        <button id="send" class="px-4 py-2 bg-indigo-600 text-white rounded">Send</button>
    </div>
</div>
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
            const res = await axios.post('/api/chat/{{ $agent->slug }}'+ (threadId ? ('?thread_id='+threadId) : ''), {
                message: text,
                thread_id: threadId,
            });
            threadId = res.data.thread_id;
            replyDiv.innerHTML = '<div class="inline-block bg-gray-50 dark:bg-gray-800 rounded p-2">'+
              (res.data.message || '(empty)') +'</div>';
        } catch (e) {
            replyDiv.innerHTML = '<div class="inline-block bg-red-50 text-red-700 rounded p-2">'+ (e.response?.data?.error || 'Error') +'</div>';
        }
    });
    input.addEventListener('keydown', (e) => { if (e.key === 'Enter') btn.click(); });
</script>
</body>
</html>