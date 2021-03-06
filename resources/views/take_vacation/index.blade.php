@extends('Layouts.app')
@section('title',trans("take_vacation.list.title"))
@section('title_header',trans("take_vacation.list.title"))
@section('content')
    @include('Layouts.alert')
        <div class="row row-xs" id="ctrTakeVacationListVl">
            <pulse-loader :loading="loading"></pulse-loader>
            <div class="sub-header">
                <div class="sub-header-line-one text-right">
                     <button class="btn btn-yellow" onclick="window.location.href= '{{route('take_vacation.create')}}'">
                         {{trans('take_vacation.list.search.btn_new_register')}}
                     </button>
                </div>
                <div class="sub-header-line-two p-t-30 frm-search-list">
                    <div class="row">
                        <div class="col-md-7 col-sm-12 row">
                            <div class="col-md-6 row">
                                <div class="col-md-4 col-list-search-f">
                                    {{trans("take_vacation.list.search.applicant_id")}}
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="fileSearch.vacation_id" name="vacation_id" id="vacation_id" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4 col-list-search-f">
                                    {{trans("take_vacation.list.search.applicant")}}
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="fileSearch.applicant_nm" name="applicant_nm" id="applicant_nm" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12 row">
                            <div class="col-md-7 row">
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" value="1" v-model="fileSearch.show_approved" class="form-check-input">{{trans("take_vacation.list.search.show_approved")}}
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" value="1" v-model="fileSearch.show_deleted" class="form-check-input">{{trans("take_vacation.list.search.show_deleted")}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5"></div>
                        </div>
                    </div>
                    <div class="break-row-form" style="height: 25px;"></div>
                    <div class="row">
                        <div class="col-md-7 col-sm-12 row">
                            <div class="col-md-6 row">
                                <div class="col-md-4 col-list-search-f">
                                    {{trans("take_vacation.list.search.sales_office")}}
                                </div>
                                <div class="col-md-8 input-group">
                                    <select class="form-control dropdown-list" name="sales_office"  id="sales_office"  v-model="fileSearch.sales_office">
                                        <option value="">{{trans('common.kara_select_option')}}</option>
                                        @foreach($businessOffices as $office)
                                            <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4 col-list-search-f">
                                    {{trans("take_vacation.list.search.vacation_class")}}
                                </div>
                                <div class="col-md-8 input-group">
                                    <select class="form-control dropdown-list" name="vacation_class"  id="vacation_class"  v-model="fileSearch.vacation_class">
                                        <option value="">{{trans('common.all')}}</option>
                                        @foreach($vacationClasses as $class)
                                            <option value="{{$class['date_id']}}"> {{$class['date_nm']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12 row">
                            <div class="col-md-6 row">
                                <div class="col-md-5 row">
                                    <button class="btn w-100 btn-light mr-2" type="button" id="clearConditions" v-on:click="clearCondition()">
                                        {{trans('common.button.clear')}}
                                    </button>
                                </div>
                                <div class="col-md-5 row">
                                    <button class="btn w-100 btn-primary" type="button" id="searchItems" v-on:click="getItems(1)">
                                        {{trans('common.button.search')}}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
            <div class="wrapper-table table-green" v-cloak v-if="flagSearch">
                <table class="table table-striped table-bordered search-content">
                    <thead>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <th  v-on:click="sortList($event, '{{$field["sortBy"]}}')" id="th_{{$key}}" class="cursor-pointer {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("take_vacation.list.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr  v-cloak v-for="item in items" v-bind:style="{ backgroundColor: setBgColor(item.delete_at) }">
                        @foreach($fieldShowTable as $key => $field)
                            @if($key=='id')
                            <td>
                                <div class="cd-link text-center" v-on:click="checkIsExist(item.id)">{!! "@{{ item['$key'] }}" !!}</div>
                            </td>
                            @else
                            <td class="{{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                <span v-if="item['{{$key}}']">{!! "@{{item['$key']}}" !!}</span>
                                <span v-else>---</span>
                            </td>
                            @endif
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
            <div class="col-md-3"></div>

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
    <script type="text/javascript" src="{{ mix('/assets/js/controller/take-vacation-list.js') }}" charset="utf-8"></script>
@endsection