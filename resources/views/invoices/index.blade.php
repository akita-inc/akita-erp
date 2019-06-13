@extends('Layouts.app')
@section('title',trans("invoices.title"))
@section('title_header',trans("invoices.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}"/>
    <style>
        .input-cd+div{
            width: 300px!important;
        }
        .autosuggest__results-container{
            font-size: 14px;
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
    <div id="ctrInvoiceListVl">
        {{ Breadcrumbs::render('invoices') }}
        @include('Layouts.alert')
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header" style="background-color: #F4B084">
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-9 col-sm-12 row text-left">
                        <div class="col-md-1 padding-row-5 col-list-search-f text-center">
                            {{trans("invoices.list.search.mst_business_office_id")}}
                        </div>
                        <div class="col-md-3 padding-row-5 grid-form-search">
                            <select class="custom-select form-control" name="mst_business_office_id"  id="mst_business_office_id"  v-model="fileSearch.mst_business_office_id">
                                <option value="">{{trans("invoices.list.search.mst_business_office_id_default")}}</option>
                                @foreach($businessOffices as $office)
                                    <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 padding-row-5 col-list-search-f text-center">
                            {{trans("invoices.list.search.billing_year")}}
                        </div>
                        <div class="col-md-2 padding-row-5 row lh-38">
                            <select class="custom-select form-control" name="billing_year"  id="billing_year"  v-model="fileSearch.billing_year">
                                @foreach($listYear as $year)
                                    <option value="{{$year}}"> {{$year}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search row lh-38">
                            <div class="col-md-1 col-sm-1 no-padding text-center">年</div>
                            <div class="col-md-4 no-padding">
                                <select class="custom-select form-control" name="billing_month"  id="billing_month"  v-model="fileSearch.billing_month">
                                    @foreach($listMonth as $month)
                                        <option value="{{$month}}"> {{$month}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 no-padding row">
                                <div class="col-md-1 no-padding"></div>
                                <div class="col-md-4 text-left no-padding lh-38">月</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 row"></div>
                </div>
                <div class="break-row-form" style="height: 25px;"></div>
                <div class="row">
                    <div class="col-md-9 col-sm-12 row">
                        <div class="col-md-1 padding-row-5 col-list-search-f text-center">
                            {{trans("invoices.list.search.customer")}}
                        </div>
                        <div class="col-md-2 padding-row-5 grid-form-search text-left">
                            <label class="grid-form-search-label" for="input_mst_customers_cd">
                                {{trans("invoices.list.search.code")}}
                            </label>
                            <vue-autosuggest
                                    :suggestions="filteredCustomerCd"
                                    :input-props="inputPropsCd"
                                    :on-selected="onSelectedCd"
                                    :render-suggestion="renderSuggestion"
                                    :get-suggestion-value="getSuggestionValueCd"
                                    ref="customer_cd"
                                    @blur="getListBundleDt"
                            >
                            </vue-autosuggest>
                        </div>
                        <div class="col-md-3 padding-row-5 grid-form-search text-left">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("invoices.list.search.name")}}
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
                        <div class="col-md-1 col-sm-12 text-center pl-1 lh-38">
                            {{trans("invoices.list.search.closed_date")}}
                        </div>
                        <div class="col-md-2  col-sm-12 no-padding row lh-38">
                            <div class="col-md-10  col-sm-10 no-padding">
                                <select  v-bind:class="errors.closed_date != undefined ? 'custom-select form-control is-invalid':'custom-select form-control' " name="closed_date"  id="closed_date"  v-model="fileSearch.closed_date" :disabled ='fileSearch.special_closing_date==1' v-cloak>
                                    <option v-for="item in list_bundle_dt" :value="item.bundle_dt" v-cloak>
                                        @{{ item.bundle_dt }}
                                    </option>

                                </select>
                            </div>
                            <div class="col-md-2 col-sm-2 no-padding">日</div>
                        </div>
                        <div class="col-md-3  row no-padding">
                            <div class="col-md-7 col-sm-7 no-padding lh-38 text-right">
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" value="1" v-model="fileSearch.special_closing_date" class="form-check-input">{{trans("invoices.list.search.special_closing_date")}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3  col-sm-3 no-padding">
                                <input id="input_closed_date" v-bind:class="errors.closed_date_input != undefined ? 'form-control is-invalid':'form-control' "  name="closed_date_input" v-model="fileSearch.closed_date_input" :disabled ='fileSearch.special_closing_date!=1' v-cloak>
                            </div>
                            <div class="col-md-1 col-sm-2 no-padding lh-38"><span class="pl-1">日</span></div>
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
                <div class="row">
                    <div class="col-md-7 text-right" v-if="errors.closed_date != undefined" v-cloak>
                        <span v-cloak v-if="errors.closed_date != undefined" class="message-error" v-html="errors.closed_date.join('<br />')"></span>
                    </div>
                    <div class="col-md-9 text-right" v-if="errors.closed_date_input != undefined" v-cloak>
                        <span v-cloak v-if="errors.closed_date_input != undefined" class="message-error text-right w-100" v-html="errors.closed_date_input.join('<br />')"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3 sub-header" style="background-color: #FFD966" v-if="items.length > 0" v-cloak>
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row justify-content-center">
                    <div class="col-md-1 padding-row-5 col-list-search-f">
                        {{trans("invoices.list.search.date_of_issue")}}
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
                                {{trans('invoices.list.search.button.issue')}}
                            </button>
                        </div>
                        <div class="col-md-4 padding-row-5">
                            <button class="btn btn-primary w-100" data-toggle="modal" data-target="#confirmCSVModal" :disabled="disableBtn">
                                {{trans('invoices.list.search.button.csv')}}
                            </button>
                        </div>
                        <div class="col-md-4 padding-row-5">
                            <button class="btn btn-primary w-100" data-toggle="modal" data-target="#confirmAmazonCSVModal" :disabled="disableBtn">
                                {{trans('invoices.list.search.button.amazonCSV')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-table" v-if="items.length > 0" v-cloak>
            <table class="table table-striped table-bordered search-content">
                <thead>
                <tr>
                    <th class="wd-60"></th>
                    @foreach($fieldShowTable as $key => $field)
                        <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("invoices.list.table.".$key)}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr  v-cloak v-for="item in items">
                    <td class="no-padding wd-60 text-center">
                        <button type="button" class="btn  btn-secondary" v-on:click="openModal(item)">
                            <span> {{trans("invoices.list.search.button.export")}} </span>

                        </button>
                    </td>
                    @foreach($fieldShowTable as $key => $field)
                        <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                            @switch($key)
                                @case('tax_included_amount')
                                <span v-if="item['total_fee']==null || item['consumption_tax']==null">￥0</span>
                                <span v-else>{!!"￥@{{Number( parseFloat(item['total_fee']) + parseFloat(item['consumption_tax']) ).toLocaleString() }}" !!}</span>

                                @break
                                @case('total_fee')
                                @case('consumption_tax')
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
                            {{trans("invoices.list.search.no_data")}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include("invoices.modal",[
            'fieldShowTable'=>$fieldShowTable,
            'fieldShowTableDetails'=>$fieldShowTableDetails,
         ])
        @include('Layouts.modal',[
        'id'=> 'confirmPDFModal',
        'title'=> '',
        'content'=> trans('messages.MSG10022'),
        'attr_input' => "@click='createPDF()'",
        'btn_ok_title' => trans('common.button.yes'),
        'btn_cancel_title' => trans('common.button.no'),
        ])
        @include('Layouts.modal',[
        'id'=> 'confirmCSVModal',
        'title'=> '',
        'content'=> trans('messages.MSG10022'),
        'attr_input' => "@click='createCSV()'",
        'btn_ok_title' => trans('common.button.yes'),
        'btn_cancel_title' => trans('common.button.no'),
        ])
        @include('Layouts.modal',[
        'id'=> 'confirmAmazonCSVModal',
        'title'=> '',
        'content'=> trans('messages.MSG10022'),
        'attr_input' => "@click='createAmazonCSV()'",
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
    <script type="text/javascript" src="{{ mix('/assets/js/controller/invoice-list.js') }}" charset="utf-8"></script>
@endsection
