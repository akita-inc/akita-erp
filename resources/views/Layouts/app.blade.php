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

    <!-- 上書き CSS -->
    <link href="{{ asset('css/OverWrite-4.css') }}" rel="stylesheet">

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <style>
        [v-cloak] {
            display: none !important;
        }
    </style>
    @yield('style')
    @yield('css')
</head>
<body>

<!-- 追加 左のnavここから-->
<div id="navBarMain">
<nav class="nav-left navbar-light h-100">

    <div class="Category-Box d-flex justify-content-between">
        <div class="Logo">AKITA</div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

    <div class="Category-Box"><a class="navbar-brand" href="/">システムタイトル</a></div>
    <div class="collapse navbar-collapse/" id="navbarSupportedContent">

        <div class="Category-Box">
            <h2><a href="#menu0sub1" data-toggle="collapse" aria-expanded="false" class="Arrow">
                    <span>{{$businessOfficeNm}}</span>
                    <div class="ml-1-7 mt-2">{{Auth::user()->last_nm.Auth::user()->first_nm}}</div>
                </a></h2>
            <div class="collapse" id="menu0sub1">
                <ul>
                    <li><a href="{{route('getManualFile',['filename' => config('params.manual_file_name')])}}" target="_blank" data-parent="menu0sub1">マニュアル</a></li>
                    <li><a href="{{route('logout')}}" data-parent="menu0sub1">ログアウト</a></li>
{{--                    <li><a href="#" data-parent="menu0sub1">パスワード変更</a></li>--}}
                    <li>
                        <a v-on:click="openModal" href="#"  data-parent="menu0sub1">パスワード変更</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="Category-Box">
            <h2><a href="javaScript:" class="Nav-Icon-Home">ホーム</a></h2>
        </div>
        @if(Auth::user()->sysadmin_flg==1 || config("params.MENU_DISP_FLG")=='True')
        <div class="Category-Box">
            <h2><a href="#menu1sub1" data-toggle="collapse" aria-expanded="false" class="Arrow">販売</a></h2>
            <div class="collapse" id="menu1sub1">
                <ul>
                    <li><a href="{{route('sales_lists.list')}}" data-parent="#menu1sub1" class="active">売上一覧</a></li>
                    <li><a href="{{route('invoices.list')}}" data-parent="#menu1sub1">請求書発行</a></li>
                    <li><a href="{{route('invoice_history.list')}}" data-parent="#menu1sub1">請求書発行履歴</a></li>
                    <li><a href="{{route('payment_processing.list')}}" data-parent="#menu1sub1">入金処理</a></li>
                    <li><a href="{{route('payment_histories.list')}}" data-parent="#menu1sub1">入金履歴</a></li>
                </ul>
            </div>
        </div>

        <div class="Category-Box">
            <h2><a href="#menu2sub1" data-toggle="collapse" aria-expanded="false" class="Arrow">購買</a></h2>
            <div class="collapse" id="menu2sub1">
                <ul>
                    <li><a href="{{route('purchases_lists.list')}}" data-parent="#menu2sub1">仕入一覧</a></li>
                    <li><a href="{{route('accounts_payable_data_output.list')}}" data-parent="#menu2sub1">買掛データ出力</a></li>
                </ul>
            </div>
        </div>

        @if (\Illuminate\Support\Facades\Session::get('staffs_accessible_kb') != 9
                || \Illuminate\Support\Facades\Session::get('suppliers_accessible_kb') != 9
                || \Illuminate\Support\Facades\Session::get('customers_accessible_kb') != 9
                || \Illuminate\Support\Facades\Session::get('vehicles_accessible_kb') != 9)
        <div class="Category-Box">
            <h2><a href="#menu3sub1" data-toggle="collapse" aria-expanded="false" class="Arrow">マスタ</a></h2>
            <div class="collapse" id="menu3sub1">
                <ul>
                    @if (\Illuminate\Support\Facades\Session::get('staffs_accessible_kb') != 9)
                    <li><a href="{{route('staffs.list')}}" data-parent="#menu3sub1">社員</a></li>
                    @endif
                    @if (\Illuminate\Support\Facades\Session::get('suppliers_accessible_kb') != 9)
                    <li><a href="{{route('suppliers.list')}}" data-parent="#menu3sub1">仕入先</a></li>
                    @endif
                    @if (\Illuminate\Support\Facades\Session::get('customers_accessible_kb') != 9)
                    <li><a href="{{route('customers.list')}}" data-parent="#menu3sub1">得意先</a></li>
                    @endif
                    @if (\Illuminate\Support\Facades\Session::get('vehicles_accessible_kb') != 9)
                    <li><a href="{{route('vehicles.list')}}" data-parent="#menu3sub1">車両</a></li>
                    @endif
                </ul>
            </div>
        </div>
        @endif
        <div class="Category-Box">
            <h2><a href="{{route('empty_info.list')}}" class="Nav-Icon-Hakokara">ハコカラ</a></h2>
        </div>

        <div class="Category-Box">
            <h2><a href="#menu4sub1" data-toggle="collapse" aria-expanded="false" class="Arrow">ワークフロー</a></h2>
            <div class="collapse" id="menu4sub1">
                <ul>
                    <li><a href="{{route('take_vacation.list')}}" data-parent="#menu4sub1">休暇取得申請</a></li>
                    <li><a href="{{route('expense_application.list')}}" data-parent="#menu4sub1">交際費申請</a></li>
                    <li><a href="{{route('expense_entertainment.list')}}" data-parent="#menu4sub1">交際費精算</a></li>
                @if(Auth::user()->sysadmin_flg==1)
                    <li><a href="{{route('work_flow.list')}}" data-parent="#menu4sub1">基本設定</a></li>
                    @endif
                </ul>
            </div>
        </div>
        @endif
    </div>
</nav>
<!-- 追加 左のnavここまで-->
    @include('Layouts.modalChangePassword',[
                   'id'=> 'changePasswordModal',
           ])
</div>
<div class="container">
    <div class="alert alert-success mt-3" id="msg-success-password">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <span>{{trans('messages.MSG04002')}}</span>
    </div>
    @yield('content')
</div>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous">
</script>
<script>
    lang_date_picker = {
        'days': ["日", "月", "火", "水", "木", "金", "土"],
        'months': ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        'pickers': ['次の7日', '次の30日', '過去7日間', '過去30日間'],
        'placeholder': {
            'date': '日付を選択',
            'dateRange': '期間を選択'
        }
    };
    format_date_picker = "YYYY/MM/DD";
</script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('js/bootstrap-datepicker.ja.min.js')}}"></script>
<script src="{{asset('js/main.js')}}"></script>
<script type="text/javascript" src="{{ mix('/assets/js/app-vl.js') }}"></script>
<script type="text/javascript" src="{{ mix('/assets/js/service/service.js') }}" charset="utf-8"></script>
<script type="text/javascript" src="{{ mix('/assets/js/directive/directive.js') }}" charset="utf-8"></script>
<script type="text/javascript" src="{{ mix('/assets/js/controller/change-password.js') }}" charset="utf-8"></script>
@yield('scripts')
<script>
    axios.interceptors.response.use((response) => {
        return response;
    }, function (error) {
        // Do something with response error
        if (error.response.status === 401) {
            window.location = "{{route("logoutError")}}";
        }
        return Promise.reject(error.response);
    });
</script>
</body>
</html>
