<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Clientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="mb-4">Olá <strong>{{ Auth::user()->name }}</strong></p>

                    @can('level')
                        <p class="mb-4 p-6">
                            <a href="{{ route('cliente.index') }}" class="bg-blue-500 text-white rounded p-2">Lista de Clientes</a>
                        </p>
                    @endcan

                    <p class="mb-4 p-6">
                            <a href="{{ route('meus.clientes', Auth::user()->id) }}" class="bg-blue-500 text-white rounded p-2">Meus Clientes</a>
                    </p>

                    @if(session('msg')) 
                        <p class="bg-blue-500 p-2 rounded text-center text-white mb-4">{{ session('msg') }}</p>
                    @endif
                    
                    <form action="{{ route('cliente.store') }}" method="post">
                        @csrf

                        <fieldset class="border-2 rounded p-6">
                            <legend>Preencha todos os campos</legend>

                            <input type="hidden" name="user_id" value="{{ auth::user()->id }}">

                            <div class="bg-gray-100 text-black p-4 rounded overflow-hidden mb-3">
                                <label for="nome">Nome:</label>
                                <input type="text" name="nome" id="nome" class="w-full rounded required autofocus">
                            </div>
                            
                            <div class="bg-gray-100 text-black p-4 rounded overflow-hidden mb-3">
                                <label for="email" class="text-black">E-mail:</label>
                                <input type="email" name="email" id="email" class="w-full rounded required">
                            </div>
                            
                            <div class="bg-gray-100 text-black p-4 rounded overflow-hidden mb-3">
                                <label for="telefone" class="text-black">Telefone:</label>
                                <input type="tel" name="telefone" id="telefone" class="w-full rounded required">
                            </div>
                            
                            <div class="bg-gray-100 text-black p-4 rounded overflow-hidden mb-3">
                                <label for="empresa" class="text-black">Empresa:</label>
                                <input type="text" name="empresa" id="empresa" class="w-full rounded required">
                            </div>
                            
                            <div class="bg-gray-100 text-black p-4 rounded overflow-hidden mb-3">
                                <label for="tel_comercial" class="text-black">Telefone comercial:</label>
                                <input type="tel" name="tel_comercial" id="tel_comercial" class="w-full rounded required">
                            </div>
                            
                            <div class="bg-gray-100 p-4 rounded overflow-hidden">
                                <button type="submit" value="cadastrar" class="bg-blue-500 text-white rounded p-2 text-white">cadastrar</button>
                                <button type="reset" value="limpar" class="bg-red-500 text-white rounded p-2 text-white">Limpar</button>
                            </div>

                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
