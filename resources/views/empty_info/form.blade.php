@extends('Layouts.app')
@section('title',trans("empty_info.create.title".(!empty($mEmptyInfo) ? "_edit":"")))
@section('title_header',trans("empty_info.create.title".(!empty($mEmptyInfo) ? "_edit":"")))
@section('content')
    @php $prefix='empty_info.create.field.' @endphp
    <div class="wrapper-container" id="ctrEmptyInfoVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                    <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                </div>

                <input type="hidden" id="hd_empty_info_edit" value="{!! !empty($mEmptyInfo) ? 1:0 !!}">
                @if(!empty($mEmptyInfo))
                    @foreach($mEmptyInfo as $key=>$value)
                        @if($key == 'adhibition_start_dt'
                            || $key == 'adhibition_end_dt'
                            || $key == 'business_start_dt'
                        )
                            @php($value = date("Y/m/d",strtotime($value)))
                        @endif
                        <input type="hidden" id="hd_{!! $key !!}" value="{!! $value !!}">
                    @endforeach
                    <div class="d-flex ml-auto">
                        @if($role==1)
                            <button class="btn btn-danger text-white" v-on:click="delete('{{$mEmptyInfo['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
                        @endif
                    </div>
                @endif
            </div>
            @if($role==1)
                <div class="sub-header-line-two">
                    <div class="grid-form border-0">
                        <div class="row">
                            <div class="col-md-5 col-sm-12 row grid-col h-100"></div>
                            <div class="col-md-7 col-sm-12 row grid-col h-100">
                                <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.".(!empty($mEmptyInfo) ? "edit":"register")) }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if($role==9 || ($role==2 && empty($mEmptyInfo)))
            <div class="alert alert-danger w-100 mt-2">
                {{\Illuminate\Support\Facades\Lang::get('messages.MSG10006')}}
            </div>
        @endif
        @if($role==1 || ($role==2 && !empty($mEmptyInfo)))
            <form class="form-inline" role="form">
                @if($role==2)
                    <fieldset disabled="disabled">
                        @endif
                        <div class="text-danger">
                            {{ trans("common.description-form.indicates_required_items") }}
                        </div>
                        <!--Block 1-->
                        <div class="grid-form">
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    @include('Component.form.select',['filed'=>'regist_office_id','array'=>$listBusinessOffices,'required'=>true])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.radio',['class'=>'w-100','filed'=>'vehicle_kb','array' => $listVehicleClassification,'required'=>true])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="wrap-control-group">
                                        <label for="search_vehicle">ナンバー検索</label>
                                        <input v-model="registration_numbers"
                                               type="text"
                                               class="form-control w-50"
                                               id="search_vehicle"
                                               maxlength="4">
                                    </div>
                                </div>
                                <div class="col-md-8 col-sm-12">
                                        <button class="btn btn-primary" type="button" @click="searchVehicle">{{ trans("common.button.search") }}</button>
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.input',['filed'=>'registration_numbers','attr_input' => "maxlength='5'"])
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.input',['filed'=>'vehicle_size','required'=>true,'attr_input' => "maxlength='5'"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.input',['filed'=>'vehicle_body_shape','required'=>true,'attr_input' => "maxlength='5'"])
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.input',['filed'=>'max_load_capacity','required'=>true,'attr_input' => "maxlength='5'"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-8 col-sm-12">
                                    @include('Component.form.multiple_checkbox',['filed'=>'equipment','array' => $listEquipment ,'required'=>true])
                                </div>
                            </div>
                            <div class="grid-form">
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        @include('Component.form.date-picker',['filed'=>'start_date','required'=>true,'role' => $role])

                                    </div>
                                    <div class="col-md-8 col-sm-12">
                                        @include('Component.form.time-picker',['class' => 'pl-0 wd-150','filed'=>'start_time','role' => $role])
                                    </div>
                                    <div class="break-row-form"></div>
                                    <div class="col-md-4 col-sm-12">
                                        @include('Component.form.select',['filed'=>'start_pref_cd','array'=>$listPrefecture,'required'=>true])
                                    </div>
                                    <div class="col-md-8 col-sm-12">
                                        @include('Component.form.input',['class' =>'pl-0','filed'=>'start_address','attr_input' => "maxlength='5'"])
                                    </div>
                                    <div class="break-row-form"></div>
                                    <div class="col-md-12 col-sm-12">
                                        @include('Component.form.input',['filed'=>'asking_price','required'=>true, 'attr_input' => "maxlength='5'"])
                                    </div>
                                    <div class="break-row-form"></div>
                                    <div class="col-md-12 col-sm-12">
                                        @include('Component.form.select',['filed'=>'asking_baggage','array' => $listPreferredPackage,'required'=>true])
                                    </div>
                                    <div class="break-row-form"></div>
                                    <div class="col-md-4 col-sm-12">
                                        @include('Component.form.select',['filed'=>'arrive_pref_cd','array'=>$listPrefecture,'required'=>true])
                                    </div>
                                    <div class="col-md-8 col-sm-12">
                                        @include('Component.form.input',['class' =>'pl-0','filed'=>'arrive_address','attr_input' => "maxlength='5'"])
                                    </div>
                                    <div class="break-row-form"></div>
                                    <div class="col-md-12 col-sm-12">
                                        @include('Component.form.date-picker',['filed'=>'arrive_date','required'=>true,'role' => $role])
                                    </div>
                                </div>

                            </div>
                        </div>
                        @if($role==2)
                    </fieldset>
                @endif
            </form>
        @endif
    </div>
@endsection
@section("scripts")
    <script>
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG07001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG07001'); ?>";
        messages["MSG10009"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG10009'); ?>";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/empty-info.js') }}" charset="utf-8"></script>
@endsection