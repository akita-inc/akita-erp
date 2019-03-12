@extends('Layouts.app')
@section('title',trans("vehicles.title"))
@section('title_header',trans("vehicles.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}">
@endsection
@section('content')
    <div class="row row-xs" id="ctrVehiclesListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one text-right">
                <button class="btn btn-yellow" onclick="window.location.href= '{{route('vehicles.create')}}'">
                    {{trans('common.button.add')}}
                </button>
            </div>
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-12 col-sm-12 no-padding form-inline mb-3">
                        <div class="col-md-5 col-sm-12 row">
                            <div class="col-md-6 col-sm-12 row">
                                <label class="col-md-4 no-padding justify-content-start">
                                    {{trans("vehicles.list.search.vehicle_cd")}}
                                </label>
                                <div class="col-md-8 col-sm-12 row">
                                    <input type="text" class="form-control w-100" v-model="fieldSearch.vehicles_cd">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 row">
                                <label class="col-md-4">
                                    {{trans("vehicles.list.search.door_number")}}
                                </label>
                                <div class="col-md-8 col-sm-12 row">
                                    <input type="text"  class="form-control w-100" v-model="fieldSearch.door_number">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-12 row">
                            <div class="col-md-4 col-sm-12 row">
                                <label class="col-md-5">
                                    {{trans("vehicles.list.search.vehicles_kb")}}
                                </label>
                                <div class="col-md-7 col-sm-12 row">
                                    <select class="form-control w-100" v-model="fieldSearch.vehicles_kb">
                                        <option value="">{{trans('common.select_option')}}</option>
                                        @foreach($vehicle_kbs as $kb)
                                            <option value="{{$kb['date_id']}}"> {{$kb['date_nm']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-12 row">
                                <label class="col-md-5">
                                    {{trans("vehicles.list.search.registration_numbers")}}
                                </label>
                                <div class="col-md-7 col-sm-12 row">
                                    <input type="text" class="form-control w-100" v-model="fieldSearch.registration_numbers">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-12 row form-inline">
                            <label class="col-md-2 no-padding justify-content-start">{{trans("vehicles.list.search.mst_business_office")}}</label>
                            <div class="col-md-10 row">
                                <select class="form-control w-100" v-model="fieldSearch.mst_business_office_id">
                                    <option value="">{{trans('common.select_option')}}</option>
                                    @foreach($business_offices as $office)
                                        <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 row">
                        @include('Component.search.search-reference-date',['field_radio'=>'fieldSearch.radio_reference_date','field_date'=>'fieldSearch.reference_date'])
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-5 lh-38 padding-row-5">
                            <button class="btn btn-black w-100" type="button" v-on:click="clearCondition()" >
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-7 lh-38 text-right no-padding">
                            <button class="btn w-100 btn-primary" type="button" v-on:click="getItems(1)">
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
                    <th>{{trans("vehicles.list.search.vehicle_cd")}}</th>
                    <th>{{trans("vehicles.list.search.door_number")}}</th>
                    <th>{{trans("vehicles.list.search.vehicles_kb")}}</th>
                    <th>{{trans("vehicles.list.search.registration_numbers")}}</th>
                    <th>{{trans("vehicles.list.search.mst_business_office")}}</th>
                    <th>{{trans("vehicles.list.table.vehicle_size")}}</th>
                    <th>{{trans("vehicles.list.table.vehicle_purpose")}}</th>
                    <th class="wd-120">{{trans("vehicles.list.table.adhibition_start_dt")}}</th>
                    <th class="wd-120">{{trans("vehicles.list.table.adhibition_end_dt")}}</th>
                    <th class="wd-120">{{trans("vehicles.list.table.modified_at")}}</th>
                    <th class="wd-60"></th>
                </tr>
                </thead>
                <tbody>
                    <tr v-cloak v-for="item in items">
                        <td><div class="cd-link" v-on:click="checkIsExist(item.id)">{!! "@{{ item['vehicles_cd'] }}" !!}</div></td>
                        <td>{!! "@{{ item['door_number'] }}" !!}</td>
                        <td>{!! "@{{ item['vehicles_kb_nm'] }}" !!}</td>
                        <td>{!! "@{{ item['registration_numbers'] }}" !!}</td>
                        <td>{!! "@{{ item['business_office_nm'] }}" !!}</td>
                        <td>{!! "@{{ item['vehicle_size_kb_nm'] }}" !!}</td>
                        <td>{!! "@{{ item['vehicle_purpose_nm'] }}" !!}</td>
                        <td>{!! "@{{ item['adhibition_start_dt'] }}" !!}</td>
                        <td>{!! "@{{ item['adhibition_end_dt'] }}" !!}</td>
                        <td>{!! "@{{ item['modified_at'] }}" !!}</td>
                        <td class="no-padding">
                            <button v-if="item['adhibition_end_dt'] === item['max_adhibition_end_dt']" type="button" class="btn btn-delete w-100" v-on:click="deleteVehicle(item['id'])">削除</button>
                        </td>
                    </tr>
                    <tr v-cloak v-if="message !== ''">
                        <td colspan="8">@{{message}} </td>
                    </tr>

                </tbody>
            </table>
            <div v-cloak class="mg-t-10">
                @include("Layouts.pagination")
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script>
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
        var date_now ='<?php echo date('Y/m/d'); ?>';
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/vehicles-list.js') }}" charset="utf-8"></script>
@endsection
