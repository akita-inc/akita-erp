<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Twitter -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>システムタイトル @yield('title')</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
    @yield('style')
    @yield('css')
</head>
<body>
<!-- header -->
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="/">システムタイトル</a>
    <!--Ringt menu -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarRightContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarRightContent">
        <ul class="list-group">
            <li class="list-group-item">プロフィール</li>
            <li class="list-group-item">ログアウト</li>
        </ul>
    </div>
    <!--Left menu -->
    <button class="navbar-toggler-left"
            type="button" data-toggle="collapse" data-target="#navbarLeftContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation">
        Menu
    </button>
    <div class="collapse navbar-collapse" id="navbarLeftContent">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="#">ＴＯＰ</a>
            </li>
            <li class="list-group-item">
                <a href="#">販売</a>
            </li>
            <li class="list-group-item">
                <a href="#">マスタ</a>
            </li>
        </ul>
    </div>
</nav>


@yield('content')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous">
</script>

<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/app.js')}}"></script>
@yield('scripts')

</body>
</html>
