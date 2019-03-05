@extends('Layouts.app')
@section('title','得意先　新規追加')
@section('title_header','得意先　新規追加')
@section('content')
    <div class="row row-xs" id="ctrCustomersVl">
        <div class="sub-header">
            <button class="btn btn-black">戻る</button>
        </div>
    </div>
@endsection
@section("scripts")
    <script type="text/javascript" src="{{ mix('/assets/js/controller/customers.js') }}" charset="utf-8"></script>
@endsection
