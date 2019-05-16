@extends('Layouts.app')
@section('title',trans("work_flow.create.title_step_1"))
@section('title_header',trans("work_flow.create.title_step_1"))
@section('css')

@endsection
@section('content')
    @php $prefix='work_flow.create.field.' @endphp
    <div class="wrapper-container" id="ctrWorkFlowVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                    <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                </div>

                <input type="hidden" id="hd_work_flow_edit" value="{!! !empty($mWfType) ? 1:0 !!}">
                {{--<input type="hidden" id="mode" value="{!! $mode !!}">--}}
                @if(!empty($mWfType))
                    @foreach($mWfType as $key=>$value)
                        <input type="hidden" id="hd_{!! $key !!}" value="{{$value }}">
                    @endforeach
                @endif
            </div>

            <div class="sub-header-line-two">
                    <div class="grid-form border-0">
                        <div class="row">
                            <div class="col-md-5 col-sm-12 row grid-col h-100"></div>
                            <div class="col-md-7 col-sm-12 row grid-col h-100">
                                    <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
                                    <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                        {{ trans("work_flow.create.button.clear") }}
                                    </button>

                            </div>
                        </div>
                    </div>
                </div>

        </div>


        <form class="form-inline" role="form">
            <div class="grid-form">
                <div class="row">
                    @if($mode=='edit' && \Illuminate\Support\Facades\Session::get('sysadmin_flg'))
                        <div class="col-md-4 col-sm-12">
                            @include('Component.form.select',['filed'=>'status','array'=>$listStatus,'required'=>true])
                        </div>
                        <div class="break-row-form"></div>
                    @endif
                    <div class="col-md-4 col-sm-12">
                        @include('Component.form.select',['filed'=>'regist_office_id','array'=>$listBusinessOffices,'required'=>true,'attr_input' => "disabled"])
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12">
                        @include('Component.form.radio',['class'=>'w-100','filed'=>'vehicle_kb','array' => $listVehicleClassification,'required'=>true,'role' => $mode=='register' || $mode=='edit' ? 1 :2 ])
                    </div>
                    <div class="break-row-form"></div>
                    @if($mode=='register' || $mode=='edit')
                        <div class="col-md-5 col-sm-12">
                            <div class="wrap-control-group">
                                <label for="search_vehicle">ナンバー検索</label>
                                <input v-model="registration_numbers"
                                       type="tel"
                                       class="form-control w-50"
                                       id="search_vehicle"
                                       maxlength="4"
                                >
                                <span>※ナンバー4桁を入力してください。</span>
                            </div>

                        </div>
                        <div class="col-md-7 col-sm-12">
                            <button class="btn btn-outline-secondary" type="button" @click="searchVehicle">{{ trans("common.button.search") }}</button>
                        </div>
                        <div class="break-row-form"></div>
                    @endif
                    <div class="col-md-12 col-sm-12">
                        @include('Component.form.input',['filed'=>'registration_numbers','attr_input' => "maxlength='50'"])
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12">
                        @include('Component.form.input',['filed'=>'vehicle_size','required'=>true,'attr_input' => "maxlength='50'"])
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12">
                        @include('Component.form.input',['filed'=>'vehicle_body_shape','required'=>true,'attr_input' => "maxlength='50'"])
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-10">
                        @include('Component.form.input',['filed'=>'max_load_capacity','required'=>true,'attr_input' => "maxlength='5' @blur='addComma(\"max_load_capacity\")' @focus='removeComma(\"max_load_capacity\")'", 'type' => 'tel'])
                    </div>
                    <div class="col-md-7 col-sm-2 pl-0 d-flex align-items-center">kg</div>
                    <div class="break-row-form"></div>
                    @if(empty($mEmptyInfo))
                        <div class="col-md-8 col-sm-12">
                            @include('Component.form.multiple_checkbox',['filed'=>'equipment','array' => $listEquipment ,'required'=>true])
                        </div>
                    @else
                        <div class="col-md-12 col-sm-12">
                            @include('Component.form.textarea',['filed'=>'equipment','attr_input' => "maxlength='200' rows='6' class='h-100'" ,'required'=>true, 'label_class' => 'h-100'])
                        </div>
                    @endif
                </div>
            </div>
        </form>
        <div class="sub-header mt-3">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                    <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                </div>

                <input type="hidden" id="hd_work_flow_edit" value="{!! !empty($mWfType) ? 1:0 !!}">
                {{--<input type="hidden" id="mode" value="{!! $mode !!}">--}}
            </div>

            <div class="sub-header-line-two">
                <div class="grid-form border-0">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 row grid-col h-100"></div>
                        <div class="col-md-7 col-sm-12 row grid-col h-100">
                            <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
                            <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                {{ trans("work_flow.create.button.clear") }}
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
@section("scripts")
    <script>
        var listRoute = "{{route('work_flow.list')}}";
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";


    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/work-flow.js') }}" charset="utf-8"></script>
@endsection
