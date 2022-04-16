<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{settings()->get('app_title')}}</title>
        <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
    </head>
    <body>
        <div id="root"></div>
    </body>
    <script type="text/javascript">
        window.apex = {
            app_name: "{{settings()->get('app_title')}}",
            user: {
                id: {{auth()->user()->id}},
                name: "{{auth()->user()->name}}",
                is_admin: {{auth()->user()->is_admin}}
            }
        }
    </script>
    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
</html>
