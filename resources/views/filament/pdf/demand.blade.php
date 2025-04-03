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
            margin: 0px;
        }

        h3 {
            text-align: center;
            margin: 0px;

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
            padding: 20px 0;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999
        }

        .center {
            text-align: center;
        }

        .logo {
            width: 150px;
            filter: grayscale(100);
        }
    </style>
</head>

<body>
    <div class="header">

        <div class="center">
            <img class="logo" src="./logo-cm-campo-grande.png" />
        </div>
        <h2>CÂMARA MUNICIPAL DE CAMPO GRANDE</h2>
        <h2>ESTADO DE MATO GROSSO DO SUL</h2>
        <h3 class="title">{{ $title }}</h3>

        <p><strong>Unidade/Setor:</strong> {{ $area }}</p>
        <p><strong>Responsável:</strong> {{ $requester }}</p>
        <p><strong>Código da solicitaão:</strong> {{ $demand_code }}</p>
        <p><strong>Justificativa:</strong> Necessidade de reparo em via pública devido a buracos.</p>

        <p class="date"><strong>Data da solicitação:</strong> {{ $date }}</p>
    </div>

    <div class="content">


        <div class="container" id="content">


            <h3>Justificativa da Demanda</h3>
            <table>
                <tr>
                    <td style="height: 100px;"></td>
                </tr>
            </table>
            <h3>Especificação da Demanda</h3>
            <table>
                <tr>
                    <td style="height: 100px;">
                        {!! $record->description !!}
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div class="footer">

    </div>
</body>

</html>