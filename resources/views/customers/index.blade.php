@extends('Layouts.app')
@section('title',trans("customers.title"))
@section('title_header',trans("customers.title"))
@section('content')
    <div class="row row-xs" id="ctrCustomersListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one text-right">
                <button class="btn btn-yellow" onclick="window.location.href= '{{route('customers.create')}}'">
                    {{trans('common.button.add')}}
                </button>
            </div>
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-4 col-sm-12 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f">
                            {{trans("customers.list.search.customer")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_customers_cd">
                                {{trans("customers.list.search.code")}}
                            </label>
                            <input id="input_mst_customers_cd" class="form-control" name="mst_customers_cd" v-model="fileSearch.mst_customers_cd">
                        </div>
                        <div class="col-md-5 padding-row-5">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("customers.list.search.name")}}
                            </label>
                            <input id="input_mst_customers_name" class="form-control"  name="customer_nm" v-model="fileSearch.customer_nm">
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 row">
                        @include('Component.search.search-reference-date',['field_radio'=>'fileSearch.status','field_date'=>'fileSearch.reference_date'])
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-5 lh-38 padding-row-5">
                            <button class="btn btn-black w-100" v-on:click="clearCondition()">
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-7 lh-38 text-right no-padding">
                            <button class="btn btn-primary w-100" v-on:click="getItems(1)">
                                {{trans('common.button.search')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-table">
            <table class="table table-striped table-bordered table-green">
                <thead>
                <tr>
                    @foreach($fieldShowTable as $key => $field)
                        <th class="{{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("customers.list.table.".$key)}}</th>
                    @endforeach
                    <th class="wd-60"></th>
                </tr>
                </thead>
                <tbody>
                    <tr  v-cloak v-for="item in items">
                        @foreach($fieldShowTable as $key => $field)
                            <td class="{{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @if ($key == 'customer_nm')
                                    <span class="xsmall">{!! "@{{ item['customer_nm_kana'] }}" !!}</span><br v-if="item['customer_nm_kana']">
                                @endif
                                @if( $key == 'mst_customers_cd' )
                                    <a class="cd-link" href="">{!! "@{{ item['$key'] }}" !!}</a>
                                @else
                                    <span>{!! "@{{ item['$key'] }}" !!}</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="no-padding">
                            <button v-if="item['adhibition_end_dt'] === item['max_adhibition_end_dt']" type="button" class="btn btn-delete w-100" v-on:click="deleteSupplier(item['id'])">削除</button>
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
@endsection
@section("scripts")
    <script>
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
        var date_now ='<?php echo date('Y/m/d'); ?>';
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/customers-list.js') }}" charset="utf-8"></script>
@endsection
