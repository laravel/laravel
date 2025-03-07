<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100">

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white h-screen shadow-md p-6 fixed top-0 left-0">
            <h1 class="text-xl font-bold mb-4">Tidsregistrering</h1>
            <nav>
                <button wire:click="$set('currentPage', 'dashboard')" class="block p-2 hover:bg-gray-200 rounded">📊 Dashboard</button>
                <button wire:click="$set('currentPage', 'timeEntries')" class="block p-2 hover:bg-gray-200 rounded">⏳ Mine Timer</button>
                <button wire:click="$set('currentPage', 'projects')" class="block p-2 hover:bg-gray-200 rounded">📁 Projekter</button>
                <button wire:click="$set('currentPage', 'customers')" class="block p-2 hover:bg-gray-200 rounded">👤 Kunder</button>
                <button wire:click="$set('currentPage', 'profile')" class="block p-2 hover:bg-gray-200 rounded">⚙️ Profil</button>
                <a href="/logout" class="block p-2 text-red-500 hover:bg-gray-200 rounded">🚪 Log Ud</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1 p-6">
            @livewire('dashboard-controller')
        </main>
    </div>

    @livewireScripts
 <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<p style="color: red; font-weight: bold;">Dette er layoutet "dashboard.blade.php"</p>

</body>
</html>

