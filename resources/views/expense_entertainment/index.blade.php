@extends('Layouts.app')
@section('title',trans("expense_entertainment.list.title"))
@section('title_header',trans("expense_entertainment.list.title"))
@section('content')
        <div id="ctrExpenseEntertainmentListVl">
            {{ Breadcrumbs::render('expense_entertainment_list') }}
            @include('Layouts.alert')
            <pulse-loader :loading="loading"></pulse-loader>
            <div class="sub-header">
                <div class="sub-header-line-one text-right">
                     <button class="btn btn-yellow" onclick="window.location.href= '{{route('expense_entertainment.create')}}'">
                         {{trans('expense_entertainment.list.search.btn_new_register')}}
                     </button>
                </div>
                <div class="sub-header-line-two p-t-30 frm-search-list">
                    <div class="row">
                        <div class="col-md-7 col-sm-12 row">
                            <div class="col-md-6 row">
                                <div class="col-md-4 col-list-search-f">
                                    {{trans("expense_entertainment.list.search.applicant_id")}}
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="fileSearch.applicant_id" name="applicant_id" id="applicant_id" class="form-control" placeholder="{{trans("expense_entertainment.list.search.applicant_id")}}">
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4 col-list-search-f">
                                    {{trans("expense_entertainment.list.search.applicant")}}
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="fileSearch.applicant_nm" name="applicant_nm" id="applicant_nm" class="form-control" placeholder="{{trans("expense_entertainment.list.search.applicant")}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12 row">
                            <div class="col-md-7 row align-items-center">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" value="1" v-model="fileSearch.show_status" class="custom-control-input" id="show_status">
                                    <label class="custom-control-label" for="show_status">{{trans("expense_entertainment.list.search.show_status")}}</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" value="1" v-model="fileSearch.show_deleted" class="custom-control-input" id="show_deleted">
                                    <label class="custom-control-label" for="show_deleted">{{trans("expense_entertainment.list.search.show_deleted")}}</label>
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
                                    {{trans("expense_entertainment.list.search.applicant_office_id")}}
                                </div>
                                <div class="col-md-8 input-group">
                                    <select class="custom-select form-control" name="applicant_office_id"  id="applicant_office_id"  v-model="fileSearch.applicant_office_id">
                                        <option value="">{{trans('common.kara_select_option')}}</option>
                                        @foreach($businessOffices as $office)
                                            <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4 col-list-search-f">
                                    {{trans("expense_entertainment.list.search.client_company_name")}}
                                </div>
                                <div class="col-md-8 input-group">
                                    <input type="text" v-model="fileSearch.client_company_name" name="client_company_name" id="client_company_name" class="form-control" placeholder=" {{trans("expense_entertainment.list.search.client_company_name")}}">
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
                    </form>
                </div>
            </div>
            <div class="col-md-3"></div>
            <div class="wrapper-table table-green" v-cloak v-if="flagSearch">
                <table class="table table-striped table-bordered search-content">
                    <thead>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <th  v-on:click="sortList($event, '{{$field["sortBy"]}}')" id="th_{{$key}}" class="cursor-pointer {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("expense_entertainment.list.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr  v-cloak v-for="item in items.data" v-bind:style="{ backgroundColor: setBgColor(item.delete_at) }">
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
    <script type="text/javascript" src="{{ mix('/assets/js/controller/expense-entertainment-list.js') }}" charset="utf-8"></script>
@endsection