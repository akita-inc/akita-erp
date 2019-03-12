@extends('Layouts.app')
@section('title',trans("staffs.title"))
@section('title_header',trans("staffs.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}"/>
@endsection
@section('content')
    <div class="row row-xs" id="ctrStaffsListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one text-right">
                <button class="btn btn-yellow">
                    {{trans('common.button.add')}}
                </button>
            </div>
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-2 padding-row-5 col-list-search-f text-left">
                            {{trans("staffs.list.search.staff")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_customers_cd">
                                {{trans("staffs.list.search.code")}}
                            </label>
                            <input id="input_staffs_cd" class="form-control" name="staffs_cd" v-model="fileSearch.staff_cd">
                        </div>
                        <div class="col-md-6 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("staffs.list.search.staff_nm")}}
                            </label>
                            <input id="input_mst_staffs_name" class="form-control" name="staff_nm" v-model="fileSearch.staff_nm">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 row">
                        <div class="col-md-4 col-sm-12 text-left">
                            {{trans("staffs.list.search.belong_company_id")}}
                        </div>
                        <div class="col-md-8  col-sm-12 input-group">
                            <select class="form-control dropdown-list" name="belong_company_id"  id="belong_company_id"  v-model="fileSearch.belong_company_id">
                                    <option value="">===選択===</option>
                                @foreach($belongCompanies as $company)
                                    <option value="{{$company['date_id']}}"> {{$company['date_nm']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-4 padding-row-5 lh-38 text-left">
                            {{trans("staffs.list.search.mst_business_office_id")}}
                        </div>
                        <div class="col-md-8 lh-38 text-right no-padding">

                            <select class="form-control dropdown-list" name="mst_business_office_id"  id="mst_business_office_id"  v-model="fileSearch.mst_business_office_id">
                                <option value="">==選択==</option>
                                @foreach($businessOffices as $office)
                                    <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-5 col-sm-12 row text-left">
                        <div class="col-md-2 padding-row-5 col-list-search-f">
                            {{trans("staffs.list.search.employment_pattern_id")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <select class="form-control dropdown-list" name="employment_pattern_id"  id="employment_pattern_id"  v-model="fileSearch.employment_pattern_id">
                                <option value="">==選択==</option>
                                @foreach($employmentPatterns as $pattern)
                                    <option value="{{$pattern['date_id']}}"> {{$pattern['date_nm']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 padding-row-5 col-list-search-f">
                            {{trans("staffs.list.search.position_id")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <select class="form-control dropdown-list" name="position_id"  id="position_id"  v-model="fileSearch.position_id">
                                <option value="">==選択==</option>
                                @foreach($positions as $position)
                                    <option value="{{$position['date_id']}}"> {{$position['date_nm']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 row">
                        <div class="col-md-6 col-sm-12 lh-38 text-left">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="status" value="0" v-model="fileSearch.status">{{trans("staffs.list.search.radio-all")}}
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="status" value="1" v-model="fileSearch.status" v-on:click="setDefault()" >{{trans("staffs.list.search.radio-reference-date")}}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <date-picker :lang='lang' id="reference_date" :format="format_date" value-type="format" v-model="fileSearch.reference_date"></date-picker>
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
        <div class="wrapper-table">
            <table class="table table-striped table-bordered search-content">
                <thead>
                <tr>
                    @foreach($fieldShowTable as $key => $field)
                        <th class="{{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("staffs.list.table.".$key)}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr  v-cloak v-for="item in items" v-demo="{item:item}">
                    @foreach($fieldShowTable as $key => $field)
                        <td class="{{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                            @switch($key)
                                @case('staff_cd')
                                    <a href="#">{!! "@{{ item['$key'] }}" !!}</a>
                                    @break
                                @case('staff_nm')
                                    <span class="xsmall">{!! "@{{ item['staff_nm_kana'] }}" !!}</span><br v-if="item['staff_nm_kana']">
                                    <span>{!! "@{{ item['staff_nm'] }}" !!}</span>
                                    @break
                                @case('delete')
                                    <button class="btn btn-delete" v-on:click="deleteStaffs(item.id)" v-if="item['staff_cd']!='admin' && item['staff_cd']!=auth_staff_cd">削除</button>
                                    @break
                                @default
                                    <span>{!! "@{{ item['$key'] }}" !!}</span>
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
        var auth_staff_cd="<?php echo @Auth::user()->staff_cd; ?>";
        var date_now ='<?php echo date('Y/m/d'); ?>';
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/staffs-list.js') }}" charset="utf-8"></script>
@endsection
