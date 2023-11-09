<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                <p class="mb-4">Olá <strong>{{ Auth::user()->name }}</strong></p>
                <p class="mb-4">
                    Exibindo detalhes do Cliente {{ $cliente->nome }}
                </p>
                <p class="mb-4 p-6">
                    <a href="{{ route('meus.clientes', Auth::user()->id) }}" class="bg-blue-500 text-white rounded p-2">Meus Clientes</a>
                    <a href="{{ route('cliente.edit', $cliente->id) }}" class="bg-purple-500 text-white rounded p-2">Editar Clientes</a>
                    <a href="{{ route('confirma.delet', $cliente->id) }}" class="bg-red-500 text-white rounded p-2">Deletar Clientes</a>
                </p>
                </div>

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p><strong>Nome: </strong>{{ $cliente->nome }}</p>
                    <p><strong>E-mail: </strong>{{ $cliente->email }}</p>
                    <p><strong>Telefone: </strong>{{ $cliente->telefone }}</p>
                    <p><strong>Empresa: </strong>{{ $cliente->empresa }}</p>
                    <p><strong>Tel.Comercial: </strong>{{ $cliente->tel_comercial }}</p>
                </div> 
            </div>
        </div>
    </div>
</x-app-layout>
