<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Deletar Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                <p class="mb-4">Olá <strong>{{ Auth::user()->name }}</strong></p>
                <p class="mb-4">
                    Gostaria de deletar realmente o Cliente <strong>{{ $id->nome }}</strong> ?<br>
                    Não será possível desfazer!!!
                </p>
                <p>
                    <form action="{{ route('cliente.destroy', $id->id) }}" method="post">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="text-red-500 mr-3"><i class="fa-solid fa-trash"></i>Sim</button>
                        <a href="{{ route('cliente.show', $id->id) }}" class="text-green-500"><i class="fa-solid fa-xmark"></i>Não</a>
                    </form>
                </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
