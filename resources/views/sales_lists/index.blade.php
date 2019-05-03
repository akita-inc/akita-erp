@extends('Layouts.app')
@section('title',trans("sales_lists.list.title"))
@section('title_header',trans("sales_lists.list.title"))
@section('content')
    @include('Layouts.alert')
    <div class="row row-xs" id="ctrSalesListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header bg-sales-lists">
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-2  padding-row-5 col-list-search-f text-left">
                            {{trans("sales_lists.list.search.mst_business_office_id")}}
                        </div>
                        <div class="col-md-4  padding-row-5 grid-form-search">
                            <select class="form-control dropdown-list" name="mst_business_office_id"  id="mst_business_office_id"  v-model="fileSearch.mst_business_office_id">
                                <option value="">{{trans('sales_lists.list.search.business_default_value')}}</option>
                                @foreach($businessOffices as $office)
                                    <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2  padding-row-5 col-list-search-f text-left">
                            {{trans("sales_lists.list.search.period_time")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <date-picker :lang='lang' id="from_date" :format="format_date" value-type="format" v-model="fileSearch.from_date"></date-picker>
                        </div>
                    </div>
                    <div  class="col-md-5 col-sm-12 row">
                        <div class="col-md-1 col-list-search-f text-left">
                            ～
                        </div>
                        <div class="col-md-5 grid-form-search padding-row-5">
                            <date-picker :lang='lang' id="to_date" :format="format_date" value-type="format" v-model="fileSearch.to_date"></date-picker>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="break-row-form"></div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-2  padding-row-5 col-list-search-f text-left">
                            {{trans("sales_lists.list.search.customer")}}
                        </div>
                        <div class="col-md-4  padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_customers_cd">
                                {{trans("sales_lists.list.search.code")}}
                            </label>
                            <vue-autosuggest
                                    :suggestions="filteredOptions"
                                    :limit="10"
                                    :input-props="inputProps"
                                    :on-selected="onSelected"
                            >
                            </vue-autosuggest>
                        </div>
                        <div class="col-md-6 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("sales_lists.list.search.customer_nm")}}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row">
                        <div class="col-md-4 grid-form-search d-inline-flex no-padding">
                            <label class="col-list-search-f w-50"> {{trans("sales_lists.list.search.invoicing_flag")}}</label>
                            <select class="form-control dropdown-list" name="invoicing_flag"  id="invoicing_flag"  v-model="fileSearch.invoicing_flag">
                                <option value="">{{trans("sales_lists.list.search.invoicing_flag_default_value")}}</option>
                                @foreach(config('params.invoicing_flag') as $key=>$invoice)
                                    <option value="{{$key}}"> {{$invoice}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 lh-38 text-right padding-row-5">
                            <button class="btn btn-black  w-75" v-on:click="clearCondition()">
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-2 lh-38 text-left padding-row-5">
                            <button class="btn btn-primary w-100" v-on:click="getItems(1)">
                                {{trans('common.button.search')}}
                            </button>
                        </div>
                        <div class="col-md-2 lh-38 text-left padding-row-5">
                            <button class="btn btn-primary w-100">
                                {{trans('common.button.export_excel')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-table">
            <table class="table table-striped table-bordered table-blue table-green">
                <thead>
                <tr>
                    @foreach($fieldShowTable as $key => $field)
                        <th class="{{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("sales_lists.list.table.".$key)}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr  v-cloak v-for="item in items">
                    @foreach($fieldShowTable as $key => $field)
                        <td class="{{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                            @switch($key)
                                @case('total_fee')
                                <p v-if="item['{{$key}}']">{!!"￥@{{ item['$key'] }}" !!}</p>
                                <p v-else>---</p>
                                @break
                                @case('consumption_tax')
                                <p v-if="item['{{$key}}']">{!!"￥@{{ item['$key'] }}" !!}</p>
                                <p v-else>---</p>
                                @break
                                @case('tax_included_amount')
                                <p v-if="item['{{$key}}']">{!!"￥@{{ item['$key'] }}" !!}</p>
                                <p v-else>---</p>
                                @break
                                @default
                                <p v-if="item['{{$key}}']">{!! "@{{ item['$key'] }}" !!}</p>
                                <p v-else>---</p>
                                @break
                            @endswitch
                        </td>
                    @endforeach
                </tr>
                <tr v-cloak v-if="message !== ''">
                    <td colspan="10">@{{message}} </td>
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
    <script type="text/javascript" src="{{ mix('/assets/js/controller/sales-lists.js') }}" charset="utf-8"></script>
@endsection