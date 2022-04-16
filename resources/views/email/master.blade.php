<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        pre {
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            font-family: inherit;
            font-size: 1em;
            white-space: pre-wrap;
            white-space: -moz-pre-wrap;
            white-space: -pre-wrap;
            white-space: -o-pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <body>
        <div style="font-family: sans-serif;color: #4C5164;font-size: 14px;">
            <div style="line-height: 1.4em;">
                @yield('content')
            </div>
            <?php $lines = settings()->getMany(['footer_line_1', 'footer_line_2', 'footer_line_3']) ?>
            <div style="border-top: 1px solid #eee;margin-top: 25px;padding-top: 10px; text-align: center; color: #4C5164;">
                <small>{{$lines['footer_line_1']}}</small>
                <br>
                <small>{{$lines['footer_line_2']}}</small>
                <br>
                <small>{{$lines['footer_line_3']}}</small>
            </div>
        </div>
    </body>
</body>
</html>
