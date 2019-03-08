@extends('Layouts.app')
@section('title',trans("suppliers.title"))
@section('title_header',trans("suppliers.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}">
@endsection
@section('content')
    @include('Layouts.alert')
    <div class="row row-xs" id="ctrSuppliersListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one text-right">
                <button class="btn btn-yellow" onclick="window.location.href= '{{route('suppliers.create')}}'">
                    {{trans('common.button.add')}}
                </button>
            </div>
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-4 col-sm-12 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f">
                            {{trans("suppliers.list.search.customer")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_suppliers_cd">
                                {{trans("suppliers.list.search.code")}}
                            </label>
                            <input type="text" v-model="fieldSearch.mst_suppliers_cd" name="supplier_cd" id="supplier_cd" class="form-control">
                        </div>
                        <div class="col-md-5 padding-row-5">
                            <label class="grid-form-search-label" for="input_mst_suppliers_name">
                                {{trans("suppliers.list.search.name")}}
                            </label>
                            <input type="text" v-model="fieldSearch.supplier_nm" name="supplier_nm" id="supplier_nm" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 row">
                        @include('Component.search.search-reference-date',['field_radio'=>'fieldSearch.radio_reference_date','field_date'=>'fieldSearch.reference_date'])
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-5 lh-38 padding-row-5">
                            <button class="btn btn-black w-100" type="button" v-on:click="clearCondition()" >
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-7 lh-38 text-right no-padding">
                            <button class="btn w-100 btn-primary" type="button" v-on:click="getItems(1)">
                                {{trans('common.button.search')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                        <td><a class="cd-link" :href="'{{route('suppliers.edit','')}}'+'/'+item.id">{!! "@{{ item['mst_suppliers_cd'] }}" !!}</a></td>
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
                            <button v-if="item['adhibition_end_dt'] === item['max_adhibition_end_dt']" type="button" class="btn btn-delete" v-on:click="deleteSupplier(item['id'])">削除</button>
                        </td>
                    </tr>
                    <tr v-cloak v-if="message !== ''">
                        <td colspan="8">@{{message}} </td>
                    </tr>

                </tbody>
            </table>
            <div v-cloak class="mg-t-10">
                @include("Layouts.pagination")
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script>
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
        var date_now ='<?php echo date('Y/m/d'); ?>';
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/suppliers-list.js') }}" charset="utf-8"></script>
@endsection
