@extends('Layouts.app')
@section('title',trans("accounts_payable_data_output.title"))
@section('title_header',trans("accounts_payable_data_output.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}"/>
@endsection
@section('content')
    @include('Layouts.alert')
    <div class="row row-xs" id="ctrAccountsPayableDataOutputVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header" style="background-color: #F4B084">
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-12 col-sm-12 row text-left">
                        <div class="col-md-6  col-sm-12 row">
                            <div class="col-md-2 padding-row-5 col-list-search-f text-center">
                                {{trans("accounts_payable_data_output.list.search.billing_month")}}
                            </div>
                            <div class="col-md-2 no-padding">
                                <select class="form-control dropdown-list" name="billing_year"  id="billing_year"  v-model="fileSearch.billing_year" @change="handleEndDate">
                                    @foreach($listYear as $year)
                                        <option value="{{$year}}"> {{$year}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 padding-row-5 text-left col-list-search-f">年</div>
                            <div class="col-md-2 no-padding">
                                <select class="form-control dropdown-list" name="billing_month"  id="billing_month"  v-model="fileSearch.billing_month" @change="handleEndDate">
                                    @foreach($listMonth as $month)
                                        <option value="{{$month}}"> {{$month}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 padding-row-5 text-left col-list-search-f">月</div>
                            <div class="col-md-1 no-padding text-center col-list-search-f">
                                {{trans("accounts_payable_data_output.list.search.closed_date")}}
                            </div>
                            <div class="col-md-2 padding-row-5 col-list-search-f">
                                <select  v-bind:class="errors.closed_date != undefined ? 'form-control dropdown-list is-invalid':'form-control dropdown-list' " name="closed_date"  id="closed_date"  v-model="fileSearch.closed_date" v-cloak @change="handleEndDate">
                                    @foreach($listBundleDt as $key => $value)
                                    <option value="{{ $value['bundle_dt']  }}" v-cloak>
                                        {{ $value['bundle_dt']  }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        <div class="col-md-1 col-sm-1 no-padding text-left lh-38">日</div>
                        </div>
                        <div class="col-md-6  col-sm-12 row">
                            <div class="col-md-2 padding-row-5 col-list-search-f">
                                {{trans("accounts_payable_data_output.list.search.billing_date")}}
                            </div>
                            <div class="col-md-4 padding-row-5 row lh-38">
                                <input type="text" disabled v-model="fileSearch.start_date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-1 no-padding text-center lh-38">～</div>
                            <div class="col-md-4 padding-row-5 grid-form-search row lh-38">
                                <input type="text" disabled v-model="fileSearch.end_date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="break-row-form" style="height: 25px;"></div>
                <div class="row">
                    <div class="col-md-12 justify-content-center">
                        <button class="btn btn-primary  btn-submit" v-on:click="createCSV">
                            {{trans('accounts_payable_data_output.list.search.button.output')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="sub-header mt-3 ml-5 mr-5" v-bind:class="outputSuccess?'bg-color-green':'bg-color-pink'" v-cloak v-if="flagSearch">
            <div class="sub-header-line-two">
                <div class="grid-form border-0">
                    <div class="row">
                        <div class="col-sm-12" v-if="outputSuccess">
                            {{trans("accounts_payable_data_output.list.search.output_success")}}
                        </div>
                        <div class="col-sm-12" v-else>
                            {{trans("accounts_payable_data_output.list.search.no_data")}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section("scripts")
    <script>
        var defaultBundleDt = "{{$listBundleDt ? array_values($listBundleDt)[0]['bundle_dt'] :''}}";
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/accounts-payable-data-output.js') }}" charset="utf-8"></script>
@endsection
