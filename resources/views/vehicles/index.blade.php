@extends('Layouts.app')
@section('title',trans("vehicles.title"))
@section('title_header',trans("vehicles.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}">
@endsection
@section('content')
    @php $accessible_kb = \Illuminate\Support\Facades\Session::get('vehicles_accessible_kb'); @endphp
    @if ($accessible_kb == 9)
        <div class="alert alert-danger w-100 mt-2">
            {{\Illuminate\Support\Facades\Lang::get('messages.MSG10006')}}
        </div>
    @else
    <div id="ctrVehiclesListVl">
        {{ Breadcrumbs::render('vehicles_list') }}
        @include('Layouts.alert')
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            @if ($accessible_kb == 1)
            <div class="sub-header-line-one text-right">
                <button class="btn btn-yellow" onclick="window.location.href= '{{route('vehicles.create')}}'">
                    {{trans('common.button.add')}}
                </button>
            </div>
            @endif
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-12 col-sm-12 no-padding form-inline mb-3">
                        <div class="col-md-5 col-sm-12 row">
                            <div class="col-md-6 col-sm-12 row">
                                <label class="col-md-4 no-padding justify-content-start">
                                    {{trans("vehicles.list.search.vehicle_cd")}}
                                </label>
                                <div class="col-md-8 col-sm-12 row">
                                    <input type="text" class="form-control w-100" v-model="fieldSearch.vehicles_cd" placeholder="{{trans("vehicles.list.search.vehicle_cd")}}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 row">
                                <label class="col-md-4">
                                    {{trans("vehicles.list.search.door_number")}}
                                </label>
                                <div class="col-md-8 col-sm-12 row">
                                    <input type="text"  class="form-control w-100" v-model="fieldSearch.door_number" placeholder="{{trans("vehicles.list.search.door_number")}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-12 row">
                            <div class="col-md-4 col-sm-12 row">
                                <label class="col-md-5">
                                    {{trans("vehicles.list.search.vehicles_kb")}}
                                </label>
                                <div class="col-md-7 col-sm-12 row">
                                    <select class="custom-select form-control w-100" v-model="fieldSearch.vehicles_kb">
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
                                    <input type="text" class="form-control w-100" v-model="fieldSearch.registration_numbers" placeholder="{{trans("vehicles.list.search.registration_numbers")}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-12 row form-inline">
                            <label class="col-md-2 no-padding justify-content-start">{{trans("vehicles.list.search.mst_business_office")}}</label>
                            <div class="col-md-10 row">
                                <select class="custom-select form-control w-100" v-model="fieldSearch.mst_business_office_id">
                                    <option value="">{{trans('common.select_option')}}</option>
                                    @foreach($business_offices as $office)
                                        <option value="{{$office['id']}}"> {{$office['business_office_nm']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 row">
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
                    <th v-on:click="sortList($event, 'vehicles_cd')" id="th_vehicles_cd">{{trans("vehicles.list.search.vehicle_cd")}}</th>
                    <th v-on:click="sortList($event, 'door_number')" id="th_door_number">{{trans("vehicles.list.search.door_number")}}</th>
                    <th v-on:click="sortList($event, 'mst_general_purposes.date_nm')" id="th_vehicles_kb">{{trans("vehicles.list.search.vehicles_kb")}}</th>
                    <th v-on:click="sortList($event, 'registration_numbers')" id="th_registration_numbers">{{trans("vehicles.list.search.registration_numbers")}}</th>
                    <th v-on:click="sortList($event, 'business_office_nm')" id="th_business_office_nm">{{trans("vehicles.list.search.mst_business_office")}}</th>
                    <th v-on:click="sortList($event, 'size.date_nm')" id="th_vehicle_size">{{trans("vehicles.list.table.vehicle_size")}}</th>
                    <th v-on:click="sortList($event, 'purpose.date_nm')" id="th_vehicle_purpose">{{trans("vehicles.list.table.vehicle_purpose")}}</th>
                    <th v-on:click="sortList($event, 'modified_at')" id="th_modified_at" class="wd-120">{{trans("vehicles.list.table.modified_at")}}</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-cloak v-for="item in items.data">
                        <td>
                            <div class="cd-link" v-on:click="checkIsExist(item.id)">{!! "@{{ item['vehicles_cd'] }}" !!}</div>
                        </td>
                        <td>{!! "@{{ item['door_number'] }}" !!}</td>
                        <td>{!! "@{{ item['vehicles_kb_nm'] }}" !!}</td>
                        <td>{!! "@{{ item['registration_numbers'] }}" !!}</td>
                        <td>{!! "@{{ item['business_office_nm'] }}" !!}</td>
                        <td>{!! "@{{ item['vehicle_size_kb_nm'] }}" !!}</td>
                        <td>{!! "@{{ item['vehicle_purpose_nm'] }}" !!}</td>
                        <td class="text-center">{!! "@{{ item['modified_at'] }}" !!}</td>
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
    @endif
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
