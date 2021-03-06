<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{asset('favicon.ico')}}">

    <title>ログイン</title>

    <!-- Bootstrap core CSS -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="{{asset('css/ie10-viewport-bug-workaround.css')}}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{asset('css/signin.css')}}" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]>
    <script src="{{asset('js/ie8-responsive-file-warning.js')}}"></script>
    <![endif]-->
    <script src="{{asset('js/ie-emulation-modes-warning.js')}}"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .msg-require{
            color:red;
            padding-top:5px;
        }
    </style>
</head>

<body>

<div class="container">

    <form class="form-signin" role="form" action="{{route('login')}}" method="POST">
        {!! csrf_field() !!}
        <h2 class="form-signin-heading text-center font-weight-bold">ログイン</h2>
        @if($errors->has('errorlogin'))
            <p  class="text-center" style="color:red">
                {{$errors->first('errorlogin')}}
            </p>
        @endif
        <label for="inputLoginId" class="sr-only">ログインID</label>
        <input type="text" class="form-control"  placeholder="ログインID" name="staff_cd" value="{{old('staff_cd')}}">
        <label for="inputPassword" class="sr-only">パスワード</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="パスワード" name="password"  >
        <div class="checkbox">
            <label>
                <input type="checkbox"  name="remember" {{ old('remember') ? 'checked' : '' }}> ログインしたままにする
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" name="submit" type="submit">ログイン</button>
    </form>

</div> <!-- /container -->


<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="{{asset('js/ie10-viewport-bug-workaround.js')}}"></script>
@if(session('message'))
    <script>
        alert("{!! session('message') !!}");
    </script>
@endif
</body>
</html>
