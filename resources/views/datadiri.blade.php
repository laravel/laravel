<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Data Diri</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('/bradley-pelish-NPivaRqGFWw-unsplash.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .card {
            background: linear-gradient(145deg, rgba(28, 30, 36, 0.85), rgba(15, 17, 21, 0.75));
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.6);
            width: 380px;
        }

        h2 {
            margin-top: 0;
            color: #f3f4f6;
            letter-spacing: 0.5px;
        }

        hr {
            border: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.15);
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
            color: #d1d5db;
            font-size: 15px;
        }

        p strong {
            color: #9ca3af;
        }

        ul {
            padding-left: 20px;
            color: #e5e7eb;
            margin-top: 5px;
        }

        li {
            margin: 4px 0;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>Data Diri</h2>
        <hr>
        <p><strong>Nama:</strong> {{ $data['nama'] }}</p>
        <p><strong>Profesi:</strong> {{ $data['profesi'] }}</p>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>

        <p><strong>Keahlian:</strong></p>
        <ul>
            @foreach($data['keahlian'] as $skill)
            <li>{{ $skill }}</li>
            @endforeach
        </ul>
    </div>

</body>

</html>