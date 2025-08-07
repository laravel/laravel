<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $agent->name }} - Embed</title>
    @vite(['resources/css/app.css'])
    <style> body { background: transparent } </style>
</head>
<body>
<div class="p-3">
    <div class="text-sm text-gray-500">Embed placeholder for {{ $agent->name }}</div>
    <div class="border rounded p-3">Chat UI coming next</div>
</div>
</body>
</html>