<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de CLientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">Olá <strong>{{ Auth::user()->name }}</strong></p>
                </div>
                
                <div class="p-6 text-gray-900">

                <div class="bg-gray-100 rounded p-2 mb-4">
                    {{ $clientes->links() }}
                </div>
                  <table class="table-auto w-full">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="p-2">Nome</th>
                            <th class="p-2">E-mail</th>
                            <th class="p-2">Telefone</th>
                            <th class="p-2">Usuário</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($clientes as $cliente) 
                            <tr class="hover:bg-gray-100">
                                <td class="p-2">{{ $cliente->nome }}</td>
                                <td class="p-2">{{ $cliente->email }}</td>
                                <td class="p-2">{{ $cliente->telefone }}</td>
                                <td class="p-2">{{ $cliente->telefone }}</td>
                                <td class="p-2">{{ $cliente->user->nome }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
