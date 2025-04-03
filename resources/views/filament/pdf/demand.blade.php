<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.6
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
        {!! $record->description !!}
    </div>

    <div class="footer">
       
    </div>
</body>

</html>