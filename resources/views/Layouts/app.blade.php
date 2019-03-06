<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Twitter -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>システムタイトル | @yield('title')</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- fontawesome core CSS -->
    <link href="{{ asset('fontawesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

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
    <h1>@yield('title_header')</h1>
    <!--Ringt menu -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarRightContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarRightContent">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="#">プロフィール</a>
            </li>
            <li class="list-group-item">
                <a href="{{route('logout')}}">ログアウト</a>
            </li>
        </ul>
    </div>
    <!--Left menu -->
    <button class="navbar-toggler-left"
            type="button" data-toggle="collapse" data-target="#sidebar"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation">
        Menu
    </button>
    <div class="collapse navbar-collapse" id="sidebar">
        <a href="#" class="list-group-item" data-parent="#menu1">Top</a>
        <a href="#menu1sub1" class="list-group-item" data-toggle="collapse" aria-expanded="false">販売</a>
        <div class="collapse" id="menu1sub1">
            <a href="#" class="list-group-item" data-parent="#menu1sub1">社員</a>
            <a href="#" class="list-group-item" data-parent="#menu1sub1">仕入先</a>
            <a href="#" class="list-group-item" data-parent="#menu1sub1">得意先</a>
            <a href="#" class="list-group-item" data-parent="#menu1sub1">車両</a>
        </div>
        <a href="#menu2sub1" class="list-group-item" data-toggle="collapse" aria-expanded="false">購買</a>
        <div class="collapse" id="menu2sub1">
            <a href="#" class="list-group-item" data-parent="#menu2sub1">社員</a>
            <a href="#" class="list-group-item" data-parent="#menu2sub1">仕入先</a>
            <a href="#" class="list-group-item" data-parent="#menu2sub1">得意先</a>
            <a href="#" class="list-group-item" data-parent="#menu2sub1">車両</a>
        </div>
        <a href="#menu3sub1" class="list-group-item" data-toggle="collapse" aria-expanded="true">マスタ</a>
        <div class="collapse show" id="menu3sub1">
            <a href="#" class="list-group-item" data-parent="#menu3sub1">社員</a>
            <a href="#" class="list-group-item" data-parent="#menu3sub1">仕入先</a>
            <a href="#" class="list-group-item" data-parent="#menu3sub1">得意先</a>
            <a href="#" class="list-group-item" data-parent="#menu3sub1">車両</a>
        </div>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous">
</script>

<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('js/app.js')}}"></script>
<script type="text/javascript">
    $('.datepicker input').datepicker({
        format: "yyyy/mm/dd"
    });
</script>
<script type="text/javascript" src="{{ mix('/assets/js/app-vl.js') }}"></script>
<script type="text/javascript" src="{{ mix('/assets/js/service/service.js') }}" charset="utf-8"></script>
<script type="text/javascript" src="{{ mix('/assets/js/directive/directive.js') }}" charset="utf-8"></script>
@yield('scripts')
</body>
</html>
