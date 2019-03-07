@extends('layouts.app')
@section('title',trans("suppliers.title"))
@section('title_header',trans("suppliers.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}">
@endsection
@section('content')
    @include('Layouts.alert')
    <div class="container-fluid">
        <div class="content" id="ctrSuppliersListVl">
            <pulse-loader :loading="loading"></pulse-loader>
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex search-form">
                        <form class="form w-100" name="suppliers-search" accept-charset="UTF-8" method="post">
                            @csrf
                            <div class="row justify-content-end">
                                <button class="btn btn-addnew" type="button" v-on:click="gotoCreate()">新規追加</button>
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
                                        <label class="col-sm-3 no-padding justify-content-start">仕入先</label>
                                        <div class="col-sm-9 form-inline no-padding justify-content-between">
                                            <div class="row">
                                                <div class="col-sm-3 no-padding">
                                                    <input type="text" v-model="fieldSearch.mst_suppliers_cd" name="supplier_cd" id="supplier_cd" class="form-control w-100">
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" v-model="fieldSearch.supplier_nm" name="supplier_nm" id="supplier_nm" class="form-control w-100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 form-inline">
                                    <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" v-model="fieldSearch.radio_reference_date" class="form-check-input" name="supplier_date" value="0" checked>すべて
                                    </label>
                                    </div>
                                    <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" v-model="fieldSearch.radio_reference_date" v-on:click="setDefault()" class="form-check-input" name="supplier_date" value="1">基準日
                                    </label>
                                    </div>
                                    <div class="input-group datepicker">
                                        <input type="text" v-model="fieldSearch.reference_date" class="form-control input-calendar" name="reference_date" id="reference_date"
                                            style="width: 130px;">
                                        <div class="input-group-append">
                                            <span class="input-group-text fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 no-padding d-flex justify-content-between">
                                    <div class="row w-100">
                                        <div class="col-sm-4 no-padding">
                                            <button class="btn btn-clear" type="button" v-on:click="clearCondition()" >条件クリア</button>
                                        </div>
                                        <div class="col-sm-8 no-padding">
                                            <button class="btn w-100 btn-search" type="button" v-on:click="getItems(1)">検索</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="wrapper-table">
                    <table class="table table-striped table-bordered search-content">
                        <thead>
                        <tr>
                            <th width="8%">仕入先CD</th>
                            <th width="20%">仕入先名</th>
                            <th width="20%">住所</th>
                            <th width="18%">支払いに関する説明</th>
                            <th width="9%">適用開始日</th>
                            <th width="9%">適用開始日</th>
                            <th width="9%">更新日時</th>
                            <th width="7%"></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-cloak v-for="item in items">
                                <td><a class="supplier-link" href="">{!! "@{{ item['mst_suppliers_cd'] }}" !!}</a></td>
                                <td>
                                    <span class="xsmall">{!! "@{{ item['supplier_nm_kana'] }}" !!}</span>
                                    <br>
                                    <span>{!! "@{{ item['supplier_nm'] }}" !!}</span>
                                </td>
                                <td>{!! "@{{ item['street_address'] }}" !!}</td>
                                <td>{!! "@{{ item['explanations_bill'] }}" !!}</td>
                                <td>{!! "@{{ item['adhibition_start_dt'] }}" !!}</td>
                                <td>{!! "@{{ item['adhibition_end_dt'] }}" !!}</td>
                                <td>{!! "@{{ item['modified_at'] }}" !!}</td>
                                <td>
                                    <button v-if="item['adhibition_end_dt'].trim() === item['max_adhibition_end_dt'].trim()" type="button" class="btn btn-delete" v-on:click="deleteSupplier(item['id'])">削除</button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <div v-cloak class="mg-t-10">
                        @include("layouts.pagination")
                    </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
@section('scripts')
    <script type="text/javascript" src="{{ mix('/assets/js/controller/suppliers-list.js') }}" charset="utf-8"></script>
@endsection
