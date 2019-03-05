@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}">
@endsection
@section('content')
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
                                                    <input type="text" name="supplier_cd" id="supplier_cd" class="form-control w-100">
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" name="supplier_nm" id="supplier_nm" class="form-control w-100">
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
                                        <input type="text" class="form-control input-calendar">
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
                        @for( $i = 0; $i <=5;$i++)
                            <tr>
                                <td><a class="supplier-link" href="">S0101</a></td>
                                <td>株式会社０００００１</td>
                                <td>愛知県名古屋市中区新栄5-0-0</td>
                                <td></td>
                                <td>2019-04-01</td>
                                <td>2999-12-01</td>
                                <td>2019-04-01</td>
                                <td>
                                    <button type="button" class="btn btn-delete">削除</button>
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>

                    <div class="search-footer">
                        @include('layouts.pagination')
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
