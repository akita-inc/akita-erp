@extends('Layouts.app')
@section('title',trans("customers.create.title".(!empty($customer) ? "_edit":"")))
@section('title_header',trans("customers.create.title".(!empty($customer) ? "_edit":"")))
@section('content')
@endsection
@section("scripts")
    <script type="text/javascript" src="{{ mix('/assets/js/controller/empty-info.js') }}" charset="utf-8"></script>
@endsection