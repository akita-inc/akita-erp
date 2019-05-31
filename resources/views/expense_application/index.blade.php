@extends('Layouts.app')
@section('title',trans("expense_application.list.title"))
@section('title_header',trans("expense_application.list.title"))
@section('content')
    @include('Layouts.alert')
@endsection
@section("scripts")
    <script>
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
        var date_now ='<?php echo date('Y/m/d'); ?>';
    </script>
{{--    <script type="text/javascript" src="{{ mix('/assets/js/controller/take-vacation-list.js') }}" charset="utf-8"></script>--}}
@endsection