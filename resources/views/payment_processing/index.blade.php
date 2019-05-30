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
                            <input type="text" v-model="fileSearch.customer_cd" name="customer_cd" maxlength="5" class="form-control" @change="handleChangeCustomerCd">
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search text-left">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("payment_processing.list.search.name")}}
                            </label>
                            <select class="form-control" v-model="fileSearch.customer_nm" name="customer_nm"  v-cloak @change="handleChangeCustomerNm">
                                <option v-for="option in listCustomer" v-bind:value="option.mst_customers_cd">@{{option.customer_nm}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-5 lh-38 padding-row-5">
                            <button class="btn btn-black w-100" v-on:click="clearCondition">
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-7 lh-38 text-right no-padding">
                            <button class="btn btn-primary w-100" v-on:click="getItems">
                                {{trans('common.button.search')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3 sub-header" style="background-color: #FFD966"  v-cloak>
            <div class="sub-header-line-one frm-search-list">
                <div class="row">
                    <div class="col-md-4 row">
                        <div class="col-md-3 no-padding col-list-search-f">
                            {{trans("payment_processing.list.field.dw_day")}}
                        </div>
                        <div class="col-md-4 no-padding grid-form-search">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="field.dw_day" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'dw_day'"
                                         :editable='false'
                            >
                            </date-picker>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-4 row">
                        <div class="col-md-3 no-padding col-list-search-f">
                            {{trans("payment_processing.list.field.invoice_balance_total")}}
                        </div>
                        <div class="col-md-4 no-padding grid-form-search">
                            <input type="text" v-model="field.invoice_balance_total" name="invoice_balance_total" maxlength="11" disabled class="form-control">
                        </div>
                        <div class="col-md-2 no-padding col-list-search-f text-center">
                            {{trans("payment_processing.list.field.dw_classification")}}
                        </div>
                        <div class="col-md-3 no-padding col-list-search-f">
                            <select class="form-control" v-model="field.dw_classification" name="dw_classification">
                                @foreach($listDepositMethod as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f text-left">
                            {{trans("payment_processing.list.field.payment_amount")}}
                        </div>
                        <div class="col-md-9 no-padding grid-form-search">
                            <input type="text" v-model="field.payment_amount" name="payment_amount" maxlength="11" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f text-left">
                            {{trans("payment_processing.list.field.fee")}}
                        </div>
                        <div class="col-md-4 no-padding grid-form-search">
                            <input type="text" v-model="field.fee" name="fee" maxlength="11" class="form-control">
                        </div>
                        <div class="col-md-2 no-padding col-list-search-f text-center">
                            {{trans("payment_processing.list.field.discount")}}
                        </div>
                        <div class="col-md-3 padding-row-5 grid-form-search">
                            <input type="text" v-model="field.discount" name="discount" maxlength="11" disabled class="form-control">
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-4 row">
                        <div class="col-md-3 no-padding col-list-search-f">
                            {{trans("payment_processing.list.field.note")}}
                        </div>
                        <div class="col-md-9 no-padding grid-form-search">
                            <input type="text" v-model="field.note" name="note" maxlength="200" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f text-left">
                            {{trans("payment_processing.list.field.total_payment_amount")}}
                        </div>
                        <div class="col-md-9 no-padding grid-form-search">
                            <input type="text" v-model="field.total_payment_amount" name="total_payment_amount" maxlength="11" disabled class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f text-left">
                            {{trans("payment_processing.list.field.item_payment_total")}}
                        </div>
                        <div class="col-md-4 no-padding grid-form-search">
                            <input type="text" v-model="field.item_payment_total" name="item_payment_total" maxlength="11" class="form-control" disabled>
                        </div>
                        <div class="col-md-2 no-padding col-list-search-f text-center"></div>
                        <div class="col-md-3 padding-row-5 grid-form-search">
                            <button class="btn btn-primary w-100" data-toggle="modal" data-target="#confirmPDFModal" :disabled="disableBtn">
                                {{trans('common.button.register')}}
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
        var currentDate = "{{$currentDate}}";
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG06005"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06005'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/payment-processing.js') }}" charset="utf-8"></script>
@endsection
