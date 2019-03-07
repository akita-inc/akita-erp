@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}">
@endsection
@section('content')
    @include('Layouts.alert')
    <div class="container-fluid">
        <div class="content" id="content-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex search-form">
                        <form class="form w-100" name="suppliers-search"
                              action="{{ url()->current() }}" accept-charset="UTF-8" method="post">
                            @csrf
                            <div class="row justify-content-end">
                                <button class="btn btn-addnew" type="button">新規追加</button>
                            </div>
                            <div class="row title-sm">
                                <div class="col-sm-4 no-padding">
                                    <div class="row">
                                        <div class="col-sm-3 no-padding"></div>
                                        <div class="col-sm-9 form-inline no-padding justify-content-between">
                                            <div class="row w-100">
                                                <div class="col-sm-3 no-padding">コード</div>
                                                <div class="col-sm-9">名称</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-inline">
                                <div class="col-sm-4 no-padding">
                                    <div class="row">
                                        <label class="col-sm-3 no-padding">仕入先</label>
                                        <div class="col-sm-9 form-inline no-padding justify-content-between">
                                            <div class="row">
                                                <div class="col-sm-3 no-padding">
                                                    <input type="text" name="supplier_cd" id="supplier_cd" class="form-control w-100"
                                                        value="{{ old('supplier_cd') }}">
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" name="supplier_nm" id="supplier_nm" class="form-control w-100"
                                                           value="{{ old('supplier_nm') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 form-inline">
                                    <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="supplier_date" value="0">すべて
                                    </label>
                                    </div>
                                    <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="supplier_date" value="1" checked>基準日
                                    </label>
                                    </div>
                                    <div class="input-group datepicker">
                                        <input type="text" class="form-control input-calendar" name="reference_date" id="reference_date">
                                        <div class="input-group-append">
                                            <span class="input-group-text fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 no-padding d-flex justify-content-between">
                                    <div class="row w-100">
                                        <div class="col-sm-4 no-padding">
                                            <button class="btn btn-clear" type="button">条件クリア</button>
                                        </div>
                                        <div class="col-sm-8 no-padding">
                                            <button class="btn w-100 btn-search" type="submit">検索</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="table table-bordered search-content">
                        <thead>
                        <tr>
                            <th>仕入先CD</th>
                            <th>仕入先名</th>
                            <th>住所</th>
                            <th>支払いに関する説明</th>
                            <th>適用開始日</th>
                            <th>適用開始日</th>
                            <th>更新日時</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $suppliers as $sup)
                            <tr>
                                <td><a class="supplier-link" href="">{{ $sup->mst_suppliers_cd }}</a></td>
                                <td>{{ $sup->supplier_nm }}</td>
                                <td>{{ $sup->date_nm . $sup->address1 . $sup->address2 . $sup->address3 }}</td>
                                <td>{{ $sup->explanations_bill }}</td>
                                <td>{{ $sup->adhibition_start_dt }}</td>
                                <td>{{ $sup->adhibition_end_dt }}</td>
                                <td>{{ $sup->modified_at }}</td>
                                <td>
                                    <button type="button" class="btn btn-delete">削除</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="search-footer">
                        @include('layouts.pagination', ['paginator' => $suppliers])
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
@section('scripts')
<script type="text/javascript">
    $(function (){
        reference_date = $('#reference_date');
        if (reference_date.val() === '')
            reference_date.val('<?php echo date('Y-m-d'); ?>');
    });

    function clear(){

    }

    function checkSearch(){
       // if ()
    }
</script>
@endsection
