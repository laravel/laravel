<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
    <script>
        function showAlert(event) {
            event.preventDefault(); 
            const nome = document.getElementById('nome').value;
            const descricao = document.getElementById('descricao').value;
            const preco = document.getElementById('preco').value;
            const quantidade = document.getElementById('quantidade').value;
            const categoria = document.getElementById('categoria').value;
            const imagem = document.getElementById('imagem').value;

            if (nome && descricao && preco && quantidade && categoria && imagem) {
                alert('Produto cadastrado com sucesso!');
                document.querySelector('form').submit(); 
            } else {
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Cadastrar Produto</h1>
        <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data" onsubmit="showAlert(event)">
            @csrf 
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="{{ old('nome') }}" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="4" required>{{ old('descricao') }}</textarea>

            <label for="preco">Preço:</label>
            <input type="number" name="preco" id="preco" value="{{ old('preco') }}" min="0" step="0.01" required>

            <label for="quantidade">Quantidade:</label>
            <input type="number" name="quantidade" id="quantidade" value="{{ old('quantidade') }}" min="0" required>

            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria" required>
                <option value="">Selecione uma categoria</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria['id'] }}">{{ $categoria['name'] }}</option>
                @endforeach
            </select>

            <label for="imagem">Imagem:</label>
            <input type="file" id="imagem" name="imagem" required>

            <button type="submit">Cadastrar</button>
        </form>
    </div>
</body>
</html>
