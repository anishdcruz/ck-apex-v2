<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{settings()->get('app_title')}}</title>
    <link rel="stylesheet" type="text/css" href="{{mix('css/app.css')}}">
    <link rel="shortcut icon" href="/favicon.ico?v2" type="image/x-icon">
    <link rel="icon" href="/favicon.ico?v2" type="image/x-icon">
</head>
<body>
    <div class="main">
        <div class="main-content">
            <div class="login-page">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="login-brand">
                            <img src="{{url('images/apex-logo.png')}}" class="image">
                        </div>
                    </div>
                    <div class="panel-body">
                        <form action="/login" method="POST">
                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" name="email" value="{{old('email')}}" class="form-control">
                                @if($errors->has('email'))
                                    <small class="error-control">{{$errors->first('email')}}</small>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control">
                                @if($errors->has('password'))
                                    <small class="error-control">{{$errors->first('password')}}</small>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="submit" value="LOGIN" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
