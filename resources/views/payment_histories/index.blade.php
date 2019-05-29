@extends('Layouts.app')
@section('title',trans("payment_histories.title"))
@section('title_header',trans("payment_histories.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}"/>
    <style>
        .input-cd+div{
            width: 300px!important;
        }
        .autosuggest__results-container{
            font-size: 14px;
            text-align: left;
        }
        .search-content thead {
            cursor: default !important;
        }
        #detailsModal .modal-footer{
            border-top: none !important;
        }
        .form-control[readonly]{
            background-color: white;
        }
    </style>
@endsection
@section('content')
    @include('Layouts.alert')
    <div class="row row-xs" id="ctrPaymentHistoryListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header" style="background-color: #F4B084">
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-6 col-sm-12 row">
                        <div class="col-md-2 padding-row-5 col-list-search-f text-center">
                            {{trans("payment_histories.list.search.customer")}}
                        </div>
                        <div class="col-md-3 padding-row-5 grid-form-search text-left">
                            <label class="grid-form-search-label" for="input_mst_customers_cd">
                                {{trans("payment_histories.list.search.code")}}
                            </label>
                            <vue-autosuggest
                                    :suggestions="filteredCustomerCd"
                                    :input-props="inputPropsCd"
                                    :on-selected="onSelectedCd"
                                    :render-suggestion="renderSuggestion"
                                    :get-suggestion-value="getSuggestionValueCd"
                                    ref="mst_customers_cd"
                            >
                            </vue-autosuggest>
                        </div>
                        <div class="col-md-5 padding-row-5 grid-form-search text-left">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("invoices.list.search.name")}}
                            </label>
                            <vue-autosuggest
                                    :suggestions="filteredCustomerNm"
                                    :input-props="inputPropsName"
                                    :on-selected="onSelectedName"
                                    :render-suggestion="renderSuggestion"
                                    :get-suggestion-value="getSuggestionValueName"
                                    ref="mst_customers_nm"
                            >
                            </vue-autosuggest>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row">
                        <div class="col-md-5 padding-row-5 grid-form-search d-inline-flex">
                            <div class="wd-60 col-list-search-f text-center">
                                {{trans("sales_lists.list.search.period_time")}}
                            </div>
                            <div class="w-100">
                                <date-picker
                                        :lang='lang'
                                        id="from_date"
                                        :format="format_date"
                                        value-type="format"
                                        v-model="fileSearch.from_date"
                                        :input-class="errors.from_date != undefined ? 'form-control w-100 is-invalid':'form-control w-100' "
                                ></date-picker>
                                <span v-cloak v-if="errors.from_date != undefined" class="message-error">@{{errors.from_date}}</span>
                            </div>
                        </div>
                        <div class="col-md-5 padding-row-5 grid-form-search d-inline-flex">
                            <div class="wd-60 col-list-search-f text-left">
                                ～
                            </div>
                            <div class="w-100">
                                <date-picker
                                        :lang='lang'
                                        id="to_date"
                                        :format="format_date"
                                        value-type="format"
                                        v-model="fileSearch.to_date"
                                        :input-class="errors.to_date != undefined ? 'form-control w-100 is-invalid':'form-control w-100' "
                                ></date-picker>
                                <span v-cloak v-if="errors.to_date != undefined" class="message-error">@{{errors.to_date}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="break-row-form" style="height: 15px;"></div>
                <div class="row">
                    <div class="col-md-7 col-sm-12 row text-left">
                    </div>
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-3 col-sm-12 no-padding row lh-38">
                            <button class="btn btn-black w-100" v-on:click="clearCondition()">
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-3  row no-padding">
                            <button class="btn btn-primary w-100" v-on:click="getItems(1)">
                                {{trans('common.button.search')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-table" v-if="items.length>0" v-cloak>
            <table class="table table-striped table-bordered search-content">
                <thead>
                <tr>
                    <th class="wd-60"></th>
                    @foreach($fieldShowTable as $key => $field)
                        <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("payment_histories.list.table.".$key)}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr  v-cloak v-for="item in items">
                    <td class="no-padding wd-60 text-center">
                        <button type="button" class="btn  btn-secondary" v-on:click="openModal(item)">
                            <span> {{trans("payment_histories.list.search.button.detail")}} </span>
                        </button>
                    </td>
                    @foreach($fieldShowTable as $key => $field)
                        <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                            @switch($key)
                                @case('actual_dw')
                                @case('fee')
                                @case('discount')
                                @case('total_dw_amount')
                                <span>{!! "￥@{{ Number(item['$key']).toLocaleString()}}" !!}</span>
                                @break
                                @default
                                <span v-if="item['{{$key}}']">{!! "@{{ item['$key'] }}" !!}</span>
                                <span v-else>---</span>
                                @break
                            @endswitch
                        </td>
                    @endforeach
                </tr>
                <tr v-cloak v-if="message !== ''">
                    <td colspan="14">@{{message}} </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="sub-header bg-color-pink mt-3 ml-5 mr-5" v-cloak v-if="items.length==0 && flagSearch">
            <div class="sub-header-line-two">
                <div class="grid-form border-0">
                    <div class="row">
                        <div class="col-sm-12">
                            {{trans("payment_histories.list.search.no_data")}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sub-header bg-color-green mt-3 ml-5 mr-5" v-cloak v-if="items.length==0 && flagSearch">
              <div class="sub-header-line-two">
                    <div class="grid-form border-0">
                        <div class="row">
                            <div class="col-sm-12">
                                {{trans("payment_histories.list.search.deleted")}}
                            </div>
                        </div>
                    </div>
              </div>
        </div>
        @include("payment_histories.modal",[
            'fieldShowTable'=>$fieldShowTable,
            'fieldShowTableDetails'=>$fieldShowTableDetails,
         ])
    </div>
@endsection
@section("scripts")
    <script>
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG06005"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06005'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/payment-histories-list.js') }}" charset="utf-8"></script>
@endsection
