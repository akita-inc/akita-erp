@extends('Layouts.app')
@section('title',trans("empty_info.list.title"))
@section('title_header',trans("empty_info.list.title"))
@section('content')
    @include('Layouts.alert')
    <div class="row row-xs" id="ctrEmptyInfoListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one text-right">
                <button class="btn btn-yellow" onclick="window.location.href= '{{route('empty_info.create')}}'">
                    {{trans('common.btn_kara.add')}}
                </button>
            </div>
            <div class="sub-header-line-two frm-search-list">
                <div class="row">
                    <div class="col-md-7 col-sm-12 row">
                        <div class="col-md-6 col-sm-12 lh-38 d-inline-flex">
                            <div class="wd-120 text-left no-padding">
                                {{trans("empty_info.list.search.regist_office_id")}}
                            </div>
                            <div class="wd-350 pd-l-20">
                                <select class="form-control dropdown-list" name="regist_office_id"  id="regist_office_id"  v-model="fileSearch.regist_office_id">
                                    <option value="">{{trans('common.kara_select_option')}}</option>
                                    @foreach($businessOffices as $office)
                                        <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 lh-38 text-left">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" v-model="fileSearch.status" class="form-check-input">{{trans("empty_info.list.search.status")}}
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" v-model="fileSearch.arrive_date"  class="form-check-input" >{{trans("empty_info.list.search.arrive_date")}}
                                </label>
                            </div>
                        </div>

                        <div class="break-row-form"></div>

                        <div class="col-md-6 lh-38 d-inline-flex">
                            <div class="wd-120 text-left no-padding">
                                {{trans("empty_info.list.search.vehicle_size")}}
                            </div>
                            <div class="wd-350 pd-l-20">
                                <input id="input_vehicle_size" class="form-control" name="vehicle_size" v-model="fileSearch.vehicle_size">
                            </div>
                        </div>
                        <div class="col-md-6 lh-38 d-inline-flex">
                            <div class="wd-120 text-left no-padding">
                                {{trans("empty_info.list.search.vehicle_body_shape")}}
                            </div>
                            <div class="wd-350 pd-l-20">
                                <input id="input_vehicle_body_shape" class="form-control" name="vehicle_body_shape" v-model="fileSearch.vehicle_body_shape">
                            </div>
                        </div>

                        <div class="break-row-form"></div>

                        <div class="col-md-6 lh-38 d-inline-flex">
                            <div class="wd-120 text-left no-padding">
                                {{trans("empty_info.list.search.asking_baggage")}}
                            </div>
                            <div class="wd-350 pd-l-20">
                                <select class="form-control dropdown-list" name="asking_baggage"  id="asking_baggage"  v-model="fileSearch.asking_baggage">
                                    <option value="">{{trans('common.kara_select_option')}}</option>
                                    @foreach($askingBaggages as $baggage)
                                        <option value="{{$baggage['date_id']}}"> {{$baggage['date_nm']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 lh-38 d-inline-flex">
                            <div class="wd-120 text-left no-padding">
                                {{trans("empty_info.list.search.equipment")}}
                            </div>
                            <div class="wd-350 pd-l-20">
                                <input id="input_equipment" class="form-control" name="equipment" v-model="fileSearch.equipment">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-7 col-sm-12 row">
                        <div class="col-md-12 lh-38 d-inline-flex">
                            <div class="wd-120 text-left no-padding">
                                {{trans("empty_info.list.search.start_pref_cd")}}
                            </div>
                            <div class="wd-250 pd-l-10">
                                <select class="form-control dropdown-list" name="start_pref_cd"  id="start_pref_cd"  v-model="fileSearch.start_pref_cd">
                                    <option value="">{{trans("empty_info.list.search.pref")}}</option>
                                    @foreach($startPrefCds as $prefCd)
                                        <option value="{{$prefCd['date_id']}}"> {{$prefCd['date_nm']}}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="wd-520 pl-lg-2">
                                <input id="input_start_address" class="form-control" name="start_address" v-model="fileSearch.start_address">
                             </div>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div  class="row">
                    <div class="col-md-7 col-sm-12 row">
                        <div class="col-md-12 lh-38 d-inline-flex">
                            <div class="wd-120 text-left no-padding">
                                {{trans("empty_info.list.search.arrive_pref_cd")}}
                            </div>
                            <div class="wd-250 pd-l-10">
                                <select class="form-control dropdown-list" name="arrive_pref_cd"  id="arrive_pref_cd"  v-model="fileSearch.arrive_pref_cd">
                                    <option value="">{{trans("empty_info.list.search.pref")}}</option>
                                    @foreach($startPrefCds as $prefCd)
                                        <option value="{{$prefCd['date_id']}}"> {{$prefCd['date_nm']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="wd-520 pl-lg-2">
                                <input id="input_arrive_address" class="form-control" name="arrive_address" v-model="fileSearch.arrive_address">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 lh-38 text-right no-padding">
                        <button class="btn btn-black w-100" v-on:click="clearCondition()">
                            {{trans('common.button.condition-clear')}}
                        </button>
                    </div>
                    <div class="mr-3"></div>
                    <div class="col-md-1 lh-38 text-right no-padding">
                        <button class="btn btn-primary m-auto w-100" v-on:click="getItems(1)">
                            {{trans('common.button.search')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-table">
            <table class="table table-striped table-bordered search-content table-green">
                <thead>
                <tr>
                    <th class="wd-60"></th>
                    @foreach($fieldShowTable as $key => $field)
                        <th v-on:click="sortList($event, '{{$field["sortBy"]}}')" id="th_{{$key}}" class="cursor-pointer align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("empty_info.list.table.".$key)}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr  v-cloak v-for="item in items" v-bind:style="{ backgroundColor: setBgColor(item.status) }">
                    <td class="no-padding wd-100 text-center">
                        <button type="button" class="btn  btn-handle-empty-info  w-75" v-on:click="handleLinkEmptyInfo(item.id,item.status,item.regist_office_id)">
                            <span v-if="item.status==8 || item.status==9"> {{trans("empty_info.list.search.button.inquiry")}} </span>
                            <span v-else-if="auth_offfice_id!=item.regist_office_id">{{trans("empty_info.list.search.button.select")}}</span>
                            <span v-else-if="auth_offfice_id==item.regist_office_id && item.status==1">{{trans("empty_info.list.search.button.edit")}}</span>
                            <span v-else-if="auth_offfice_id==item.regist_office_id && item.status==2">{{trans("empty_info.list.search.button.approve")}}</span>
                        </button>
                    </td>
                     @foreach($fieldShowTable as $key => $field)
                        <td class="text-left {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                            @switch($key)
                                @case('max_load_capacity')
                                    <p v-if="item['{{$key}}']">{!!"@{{ item['$key'] }}kg" !!}</p>
                                    <p v-else>---</p>
                                    @break
                                @case('asking_price')
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
                    <td colspan="14">@{{message}} </td>
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
        var auth_offfice_id="{{Auth::user()->mst_business_office_id}}";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/empty-info-list.js') }}" charset="utf-8"></script>
@endsection