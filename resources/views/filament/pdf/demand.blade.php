<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        body {}

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        h2 {
            text-align: center;
        }

        button {
            margin-top: 15px;
            padding: 10px;
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .header {
            text-align: center;
            margin-bottom: 20px
        }

        .title {
            font-size: 24px;
            font-weight: bold
        }

        .date {
            color: #666;
            margin-bottom: 30px
        }

        .content {
            padding: 20px 0
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="date"> Generated on: {{ now()->format('d-m-Y H:i') }}</div>
    </div>

    <div class="content">


        <div class="container" id="content">
            <h2>Solicitação de Demanda</h2>
            <p><strong>Unidade/Setor:</strong> Infraestrutura</p>
            <p><strong>Responsável:</strong> João da Silva</p>
            <p><strong>Justificativa:</strong> Necessidade de reparo em via pública devido a buracos.</p>
            <p><strong>Data do Evento:</strong> 10/04/2025</p>
            <p>
                <strong>Especificação:</strong>
                <br /> {!! $record->description !!}
            </p>
        </div>
    </div>

    <div class="footer">

    </div>
</body>

</html>