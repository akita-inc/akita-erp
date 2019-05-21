@extends('Layouts.app')
@section('title',trans("take_vacation.create.title".(!empty($vacation) ? "_edit":"")))
@section('title_header',trans("customers.create.title".(!empty($vacation) ? "_edit":"")))
@section('content')
    <div class="wrapper-container" id="ctrTakeVacationVl">

    </div>
@endsection