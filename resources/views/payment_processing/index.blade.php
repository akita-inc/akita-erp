@extends('Layouts.app')
@section('title',trans("payment_processing.title"))
@section('title_header',trans("payment_processing.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}"/>
    <style>
        .form-control[readonly]{
            background-color: white;
        }
    </style>
@endsection
@section('content')
    @include('Layouts.alert')
    <div class="row row-xs" id="ctrPaymentProcessingVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header" style="background-color: #F4B084">
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-9 col-sm-12 row">
                        <div class="col-md-1 padding-row-5 col-list-search-f text-center">
                            {{trans("payment_processing.list.search.customer")}}
                        </div>
                        <div class="col-md-3 padding-row-5 grid-form-search text-left">
                            <label class="grid-form-search-label" for="input_mst_customers_cd">
                                {{trans("payment_processing.list.search.code")}}
                            </label>
                            <vue-autosuggest
                                    :suggestions="filteredCustomerCd"
                                    :input-props="inputPropsCd"
                                    :on-selected="onSelectedCd"
                                    :render-suggestion="renderSuggestion"
                                    :get-suggestion-value="getSuggestionValueCd"
                                    ref="customer_cd"
                            >
                            </vue-autosuggest>
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search text-left">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("payment_processing.list.search.name")}}
                            </label>
                            <vue-autosuggest
                                    :suggestions="filteredCustomerNm"
                                    :input-props="inputPropsNm"
                                    :on-selected="onSelectedNm"
                                    :render-suggestion="renderSuggestion"
                                    :get-suggestion-value="getSuggestionValueNm"
                                    ref="customer_nm"
                            >
                            </vue-autosuggest>
                        </div>
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
        <div class="mt-3 sub-header" style="background-color: #FFD966" v-if="items.length > 0" v-cloak>
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row justify-content-center">
                    <div class="col-md-1 padding-row-5 col-list-search-f">
                        {{trans("payment_processing.list.search.date_of_issue")}}
                    </div>
                    <div class="col-md-3 padding-row-5 grid-form-search">
                        <div class="col-md-7 no-padding">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="date_of_issue" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'date_of_issue'"
                                         :editable='false'
                            >
                            </date-picker>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row justify-content-center">
                    <div class="col-md-2 padding-row-5 col-list-search-f "></div>
                    <div class="col-md-4 padding-row-5 grid-form-search row">
                        <div class="col-md-4 padding-row-5">
                            <button class="btn btn-primary w-100" data-toggle="modal" data-target="#confirmPDFModal" :disabled="disableBtn">
                                {{trans('payment_processing.list.search.button.issue')}}
                            </button>
                        </div>
                        <div class="col-md-4 padding-row-5">
                            <button class="btn btn-primary w-100" data-toggle="modal" data-target="#confirmCSVModal" :disabled="disableBtn">
                                {{trans('payment_processing.list.search.button.csv')}}
                            </button>
                        </div>
                        <div class="col-md-4 padding-row-5">
                            <button class="btn btn-primary w-100" data-toggle="modal" data-target="#confirmAmazonCSVModal" :disabled="disableBtn">
                                {{trans('payment_processing.list.search.button.amazonCSV')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="wrapper-table" v-if="items.length > 0" v-cloak>--}}
            {{--<table class="table table-striped table-bordered search-content">--}}
                {{--<thead>--}}
                {{--<tr>--}}
                    {{--<th class="wd-60"></th>--}}
                    {{--@foreach($fieldShowTable as $key => $field)--}}
                        {{--<th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("payment_processing.list.table.".$key)}}</th>--}}
                    {{--@endforeach--}}
                {{--</tr>--}}
                {{--</thead>--}}
                {{--<tbody>--}}
                {{--<tr  v-cloak v-for="item in items">--}}
                    {{--<td class="no-padding wd-60 text-center">--}}
                        {{--<button type="button" class="btn  btn-secondary" v-on:click="openModal(item)">--}}
                            {{--<span> {{trans("payment_processing.list.search.button.export")}} </span>--}}

                        {{--</button>--}}
                    {{--</td>--}}
                    {{--@foreach($fieldShowTable as $key => $field)--}}
                        {{--<td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>--}}
                            {{--@switch($key)--}}
                                {{--@case('tax_included_amount')--}}
                                {{--<span v-if="item['total_fee']==null || item['consumption_tax']==null">￥0</span>--}}
                                {{--<span v-else>{!!"￥@{{Number( parseFloat(item['total_fee']) + parseFloat(item['consumption_tax']) ).toLocaleString() }}" !!}</span>--}}

                                {{--@break--}}
                                {{--@case('total_fee')--}}
                                {{--@case('consumption_tax')--}}
                                {{--<span>{!! "￥@{{ Number(item['$key']).toLocaleString()}}" !!}</span>--}}
                                {{--@break--}}
                                {{--@default--}}
                                {{--<span v-if="item['{{$key}}']">{!! "@{{ item['$key'] }}" !!}</span>--}}
                                {{--<span v-else>---</span>--}}
                                {{--@break--}}
                            {{--@endswitch--}}
                        {{--</td>--}}
                    {{--@endforeach--}}
                {{--</tr>--}}
                {{--<tr v-cloak v-if="message !== ''">--}}
                    {{--<td colspan="14">@{{message}} </td>--}}
                {{--</tr>--}}
                {{--</tbody>--}}
            {{--</table>--}}
        {{--</div>--}}
        {{--<div class="sub-header bg-color-pink mt-3 ml-5 mr-5" v-cloak v-if="items.length==0 && flagSearch">--}}
            {{--<div class="sub-header-line-two">--}}
                {{--<div class="grid-form border-0">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-sm-12">--}}
                            {{--{{trans("payment_processing.list.search.no_data")}}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        @include('Layouts.modal',[
        'id'=> 'confirmPDFModal',
        'title'=> '',
        'content'=> trans('messages.MSG10022'),
        'attr_input' => "@click='createPDF()'",
        'btn_ok_title' => trans('common.button.yes'),
        'btn_cancel_title' => trans('common.button.no'),
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
    <script type="text/javascript" src="{{ mix('/assets/js/controller/payment-processing.js') }}" charset="utf-8"></script>
@endsection
