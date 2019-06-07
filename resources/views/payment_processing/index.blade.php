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
                            <input type="text" v-model="fileSearch.customer_cd" name="customer_cd" id="customer_cd" maxlength="5" v-bind:class="errors.customer_cd != undefined ? 'form-control  number_only is-invalid':'form-control number_only' " @change="handleChangeCustomerCd" v-cloak>

                        </div>
                        <div class="col-md-5 padding-row-5 grid-form-search text-left">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("payment_processing.list.search.name")}}
                            </label>
                            <select v-bind:class="errors.customer_cd != undefined ? 'form-control is-invalid':'form-control'" v-model="fileSearch.customer_nm" name="customer_nm"  v-cloak @change="handleChangeCustomerNm">
                                <option value="">{{trans('common.kara_select_option')}}</option>
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
                <div class="row">
                    <div class="col-md-9 col-sm-12 row">
                        <div class="col-md-1"></div>
                        <div class="col-md-11 text-left padding-row-5">
                            <span v-cloak v-if="errors.customer_cd != undefined" class="message-error" v-html="errors.customer_cd.join('<br />')"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sub-header mt-3 ml-5 mr-5 bg-color-pink" v-cloak v-if="flagSearch && items.length ==0">
            <div class="sub-header-line-two">
                <div class="grid-form border-0">
                    <div class="row">
                        <div class="col-sm-12" v-if="flagSearch && items.length ==0">
                            {{trans("payment_processing.list.search.no_data")}}
                        </div>
                        <div class="col-sm-12" v-if="!registerSuccess ==0">
                            {{trans("payment_processing.list.search.register_error")}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sub-header mt-3 ml-5 mr-5" v-bind:class="registerSuccess ? 'bg-color-green' :'bg-color-pink'" v-cloak v-if="flagRegister">
            <div class="sub-header-line-two">
                <div class="grid-form border-0">
                    <div class="row">
                        <div class="col-sm-12" v-if="registerSuccess">
                            {{trans("payment_processing.list.search.register_success")}}
                        </div>
                        <div class="col-sm-12" v-else>
                            {{trans("payment_processing.list.search.register_error")}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3 sub-header" style="background-color: #FFD966"  v-cloak v-if="flagSearch && items.length >0">
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
                                         :input-class="errorValidate.dw_day != undefined ? 'form-control w-100 is-invalid':'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'dw_day'"
                                         :editable='false'
                            >
                            </date-picker>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 row">
                        <span v-cloak v-if="errorValidate.dw_day != undefined" class="message-error" v-html="errorValidate.dw_day.join('<br />')"></span>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-4 row">
                        <div class="col-md-3 no-padding col-list-search-f">
                            {{trans("payment_processing.list.field.invoice_balance_total")}}
                        </div>
                        <div class="col-md-4 no-padding grid-form-search">
                            <input type="text" v-model="field.invoice_balance_total" name="invoice_balance_total"  disabled  v-bind:class="errorValidate.invoice_balance_total != undefined ? 'form-control is-invalid':'form-control'">
                            <span v-cloak v-if="errorValidate.invoice_balance_total != undefined" class="message-error" v-html="errorValidate.invoice_balance_total.join('<br />')"></span>
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
                    <div class="col-md-3 row">
                        <div class="col-md-4 padding-row-5 col-list-search-f text-left">
                            {{trans("payment_processing.list.field.payment_amount")}}
                        </div>
                        <div class="col-md-8 no-padding grid-form-search">
                            <input type="text" v-model="field.payment_amount" name="payment_amount" id="payment_amount" @change="handlePayment" @focus="removeCommaByID('payment_amount')" @blur="addCommaByID('payment_amount')" v-bind:class="errors.payment_amount!= undefined ? 'form-control number_only is-invalid':'form-control number_only'" onkeypress="return isNumberKey(event)">

                            <span v-cloak v-if="errorValidate.payment_amount != undefined" class="message-error" v-html="errorValidate.payment_amount.join('<br />')"></span>
                        </div>
                    </div>
                    <div class="col-md-5 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f text-left">
                            {{trans("payment_processing.list.field.fee")}}
                        </div>
                        <div class="col-md-4 no-padding grid-form-search">
                            <input type="text" v-model="field.fee" name="fee" maxlength="13"  @change="handleFee" id="fee" v-bind:class="errorValidate.fee != undefined ? 'form-control number_only is-invalid':'form-control number_only'" @focus="removeCommaByID('fee')" @blur="addCommaByID('fee')" onkeypress="return isNumberKey(event)">
                        </div>
                        <div class="col-md-2 no-padding col-list-search-f text-center">
                            {{trans("payment_processing.list.field.total_discount")}}
                        </div>
                        <div class="col-md-3 padding-row-5 grid-form-search">
                            <input type="text" v-model="field.total_discount" name="total_discount" disabled  v-bind:class="errorValidate.total_discount != undefined ? 'form-control number_only is-invalid':'form-control number_only'">
                            <span v-cloak v-if="errorValidate.total_discount != undefined" class="message-error" v-html="errorValidate.total_discount.join('<br />')"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 row"></div>
                    <div class="col-md-3 row">
                    </div>
                    <div class="col-md-5 row">
                        <div class="col-md-7 padding-row-5 col-list-search-f text-left">
                            <span v-cloak v-if="errorValidate.fee != undefined" class="message-error" v-html="errorValidate.fee.join('<br />')"></span>
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
                            <input type="text" v-model="field.note" name="note" maxlength="200" v-bind:class="errorValidate.note != undefined ? 'form-control is-invalid':'form-control'">
                        </div>
                    </div>
                    <div class="col-md-3 row">
                        <div class="col-md-4 padding-row-5 col-list-search-f text-left">
                            {{trans("payment_processing.list.field.total_payment_amount")}}
                        </div>
                        <div class="col-md-8 no-padding grid-form-search">
                            <input type="text" v-model="field.total_payment_amount" name="total_payment_amount" disabled v-bind:class="errorValidate.total_payment_amount != undefined ? 'form-control is-invalid':'form-control'">
                            <span v-cloak v-if="errorValidate.total_payment_amount != undefined" class="message-error" v-html="errorValidate.total_payment_amount.join('<br />')"></span>
                        </div>
                    </div>
                    <div class="col-md-5 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f text-left">
                            {{trans("payment_processing.list.field.item_payment_total")}}
                        </div>
                        <div class="col-md-4 no-padding grid-form-search">
                            <input type="text" v-model="field.item_payment_total" name="item_payment_total" disabled v-bind:class="errorValidate.item_payment_total != undefined ? 'form-control is-invalid':'form-control'">
                            <span v-cloak v-if="errorValidate.item_payment_total != undefined" class="message-error" v-html="errorValidate.item_payment_total.join('<br />')"></span>
                        </div>
                        <div class="col-md-2 no-padding col-list-search-f text-center"></div>
                        <div class="col-md-3 padding-row-5 grid-form-search">
                            <button class="btn btn-primary w-100" @click="submit">
                                {{trans('common.button.register')}}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 row">
                        <span v-cloak v-if="errorValidate.note != undefined" class="message-error" v-html="errorValidate.note.join('<br />')"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-n3" v-cloak v-if="errorStr!=''">
            <div class="sub-header-line-two">
                <div class="grid-form border-0">
                    <div class="row">
                        <div class="col-sm-12 message-error" v-html="errorStr"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-table" v-if="items.length > 0" v-cloak>
            <div class="scroll-horizontal">
            <table class="table table-striped table-bordered search-content">
                <thead>
                <tr>
                    <th class="wd-20">
                        <input type="checkbox" @click="selectAll" v-model="allSelected">
                    </th>
                    @foreach($fieldShowTable as $key => $field)
                        <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("payment_processing.list.table.".$key)}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr  v-cloak v-for="(item,index) in items">
                    <td class="no-padding wd-20 text-center">
                        <input type="checkbox" v-model="listCheckbox" @change="handleChecked($event)" :value="index" :id="index">
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
                                @case('fee')
                                @case('last_payment_amount')
                                @case('payment_remaining')
                                <span>{!! "￥@{{ Number(item['$key']).toLocaleString()}}" !!}</span>
                                @break
                                @case('total_dw_amount')
                                <input type="text" v-model="item.total_dw_amount" :name="'total_dw_amount'+index" maxlength="13" class="wd-140 text-center number_only" :disabled="listCheckbox.indexOf(index) == -1" @change="changeTotalDwAmount(index)" :id="'total_dw_amount'+index" v-bind:class="(errors.total_dw_amount!= undefined && errors.total_dw_amount.indexError.indexOf(index) != -1) || (errorValidate.listInvoice!= undefined &&  errorValidate.listInvoice[0].total_dw_amount!= undefined  && errorValidate.listInvoice[0].total_dw_amount.indexError.indexOf(index) != -1) ? 'form-control is-invalid':'form-control'" @focus="removeCommaByID('total_dw_amount',index)" @blur="addCommaByID('total_dw_amount',index)" onkeypress="return isNumberKey(event)">
                                @break
                                @case('discount')
                                <input type="text" v-model="item.discount" :name="'discount'+index" maxlength="13" class="wd-140 form-control text-center number_only" :disabled="listCheckbox.indexOf(index) == -1" @change="changeDiscount(index)" :id="'discount'+index" v-bind:class="errorValidate.listInvoice!= undefined &&  errorValidate.listInvoice[0].discount!= undefined  && errorValidate.listInvoice[0].discount.indexError.indexOf(index) != -1 ? 'form-control is-invalid':'form-control'" @focus="removeCommaByID('discount',index)" @blur="addCommaByID('discount',index)" onkeypress="return isNumberKey(event)">
                                @break
                                @default
                                <span v-if="item['{{$key}}']">{!! "@{{ item['$key'] }}" !!}</span>
                                <span v-else>---</span>
                                @break
                            @endswitch
                        </td>
                    @endforeach
                </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
@endsection
@section("scripts")
    <script>
        var currentDate = "{{$currentDate}}";
        var defaultDwClassification = "{{array_keys($listDepositMethod)[0]}}";
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG06005"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06005'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31
                && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/payment-processing.js') }}" charset="utf-8"></script>
@endsection
