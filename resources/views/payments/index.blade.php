@extends('Layouts.app')
@section('title',trans("payments.title"))
@section('title_header',trans("payments.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}"/>
    <style>
        .input-cd+div{
            width: 300px!important;
        }
        .autosuggest__results-container{
            font-size: 14px;
        }
    </style>
@endsection
@section('content')
    @include('Layouts.alert')
    <div class="row row-xs" id="ctrPaymentsListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header" style="background-color: #C6E0B4">
            <div class="p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row text-left">
                        <div class="col-md-2 padding-row-5 col-list-search-f">
                            {{trans("payments.list.search.sales-office")}}
                        </div>
                        <div class="col-md-3 padding-row-5 grid-form-search">
                            <select class="form-control dropdown-list" name="mst_business_office_id"  id="mst_business_office_id"  v-model="fileSearch.mst_business_office_id">
                                <option value="">{{trans('payments.list.search.all-office')}}</option>
                                @foreach($businessOffices as $office)
                                    <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 padding-row-5 col-list-search-f">
                            {{trans("payments.list.search.billing-date")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <select class="form-control dropdown-list" name="billing_year"  id="billing_year"  v-model="fileSearch.billing_year">
                                @foreach($lstYear as $year)
                                    @if($initYear == $year)
                                        <option value="{{$year}}" selected> {{$year}}</option>
                                    @else
                                        <option value="{{$year}}"> {{$year}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 text-left no-padding lh-38">
                            {{trans('payments.list.search.year')}}
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 row">
                        <div class="col-md-2 padding-row-5 grid-form-search">
                            <select class="form-control dropdown-list" name="billing_month"  id="billing_month"  v-model="fileSearch.billing_month">
                                @foreach($lstMonth as $month)
                                    @if($initMonth == $month)
                                        <option value="{{$month}}" selected> {{$month}}</option>
                                    @else
                                        <option value="{{$month}}"> {{$month}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 text-left no-padding lh-38">
                            {{trans('payments.list.search.month')}}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 row">

                    </div>
                </div>
                <div class="break-row-form" style="height: 25px;"></div>
                <div class="row">
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-2 padding-row-5 col-list-search-f text-left">
                            {{trans("payments.list.search.supplier")}}
                        </div>
                        <div class="col-md-3 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_suppliers_cd">
                                {{trans("payments.list.search.code")}}
                            </label>
                            <vue-autosuggest
                                    :suggestions="filteredSupplierCd"
                                    :limit="10"
                                    :input-props="inputPropsCd"
                                    :on-selected="onSelectedCd"
                                    :render-suggestion="renderSuggestion"
                                    :get-suggestion-value="getSuggestionValueCd"
                                    ref="supplier_cd"
                                    @blur="getListBundleDt"
                            >
                            </vue-autosuggest>
                        </div>
                        <div class="col-md-6 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_suppliers_nm">
                                {{trans("payments.list.search.name")}}
                            </label>
                            <vue-autosuggest
                                    :suggestions="filteredSupplierNm"
                                    :limit="10"
                                    :input-props="inputPropsNm"
                                    :on-selected="onSelectedNm"
                                    :render-suggestion="renderSuggestion"
                                    :get-suggestion-value="getSuggestionValueNm"
                                    ref="supplier_nm"
                            >
                            </vue-autosuggest>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 row lh-38">
                        <div class="col-md-3 col-sm-12 text-left pl-1">
                            {{trans("payments.list.search.closing-date")}}
                        </div>
                        <div class="col-md-3 col-sm-12 no-padding row">
                            <div class="col-md-9 col-sm-9 no-padding">
                                <select  v-bind:class="errors.closed_date != undefined ? 'form-control dropdown-list is-invalid':'form-control dropdown-list' " name="closed_date"  id="closed_date"  v-model="fileSearch.closed_date" v-cloak>
                                    <option v-for="item in list_bundle_dt" :value="item.bundle_dt" v-cloak>
                                        @{{ item.bundle_dt }}
                                    </option>

                                </select>
                            </div>
                            <div class="col-md-3 col-sm-3 no-padding">
                                {{trans('payments.list.search.day')}}
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 no-padding"></div>
                        <span v-cloak v-if="errors.closed_date != undefined" class="message-error" v-html="errors.closed_date.join('<br />')"></span>
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
        <div class="mt-3 sub-header d-flex align-items-center justify-content-center" style="background-color: #FFD966" v-if="items.length > 0" v-cloak>
            <button class="btn btn-primary" data-toggle="modal" data-target="#confirmExecution">
                {{trans('payments.list.search.button.execution')}}
            </button>
        </div>
        <div class="wrapper-table">
            <table class="table table-striped table-bordered search-content">
                <thead>
                    <tr v-if="items.length > 0" v-cloak>
                        <th class="wd-60"></th>
                        @foreach($fieldShowTable as $key => $field)
                            <th v-on:click="sortList($event, '{{$field["sortBy"]}}')" id="th_{{$key}}" class="{{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("payments.list.table.".$key)}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr v-cloak v-for="item in items">
                        <td class="no-padding wd-60 text-center">
                            <button class="btn btn-secondary" type="button" v-on:click="openModal(item)">{{trans("payments.list.table.button.detail")}}</button>
                        </td>
                        @foreach($fieldShowTable as $key => $field)
                            <td class="{{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @switch($key)
                                    @case('billing_amount')
                                    @case('consumption_tax')
                                    <span>{!! "￥@{{item['$key']}}" !!}</span>
                                    @break
                                    @case('total_amount')
                                        <span>
                                            {!! "￥@{{parseInt(item.billing_amount) + parseInt(item.consumption_tax)}}" !!}
                                        </span>
                                    @break
                                    @default
                                        <span>{!! "@{{item['$key']}}" !!}</span>
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                    <tr v-cloak v-if="message !== ''">
                        <td colspan="14">@{{message}} </td>
                    </tr>
                    <tr v-cloak v-if="errors.execution != undefined">
                        <td colspan="14">@{{errors.execution}} </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @include("payments.modal",[
        'fieldShowTable'=>$fieldShowTable,
         'fieldShowTableDetails'=>$fieldShowTableDetails
         ])
        @include('Layouts.modal',[
        'id'=> 'confirmExecution',
        'title'=> '',
        'content'=> trans('messages.MSG10024'),
        'attr_input' => "@click='execution()'",
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
        messages["MSG10023"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG10023'); ?>";
        messages["MSG10024"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG10023'); ?>";
        var currentMonth = new Date().getMonth();
        var currentYear = new Date().getFullYear();
        if(currentMonth == 0){
            currentMonth = 11;
            currentYear--;
        }
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/payments-list.js') }}" charset="utf-8"></script>
@endsection