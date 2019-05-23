@extends('Layouts.app')
@section('title',trans("empty_info.create.title_".$mode))
@section('title_header',trans("empty_info.create.title_".$mode))
@section('css')
    @if($role==1 && ($mode=='register' || $mode=='edit'))
        <style>
            .form-control[readonly]{
                background-color: white;
            }
        </style>
    @endif
@endsection
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
                <input type="hidden" id="mode" value="{!! $mode !!}">
                @if(!empty($mEmptyInfo))
                    @foreach($mEmptyInfo as $key=>$value)
                        <input type="hidden" id="hd_{!! $key !!}" value="{{$value }}">
                    @endforeach
                    <div class="d-flex ml-auto">
                        @if($role==1 && ($mode=='register' || $mode=='edit'))
                            <button class="btn btn-danger text-white" v-on:click="deleteInfo('{{$mEmptyInfo['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
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
                                @if($mode=='register' || $mode=='edit')
                                <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.".$mode) }}</button>
                                <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                    {{ trans("common.button.reset") }}
                                </button>
                                @else
                                    @if(($mode=='reservation' && $mEmptyInfo['status']==1) || $mode=='reservation_approval')
                                    <button data-toggle="modal" data-target="#{{$mode}}Modal" class="btn btn-primary btn-submit">{{ trans("common.button.".$mode) }}</button>
                                    @endif
                                    @if($mode=='reservation_approval')
                                    <button data-toggle="modal" data-target="#reservation_rejectModal" class="btn btn-danger btn-submit m-auto">{{ trans("common.button.reservation_reject") }}</button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if($role==2)
            <div class="alert alert-danger w-100 mt-2">
                {{\Illuminate\Support\Facades\Lang::get('messages.MSG10006')}}
            </div>
        @endif
        @if($role==1)
            <form class="form-inline" role="form">
                @if($mode=='reservation' || $mode=='reservation_approval')
                <fieldset disabled="disabled">
                @endif
                    <div class="text-danger">
                        {{ trans("common.description-form.indicates_required_items") }}
                    </div>
                    @if($role==1 && $mode=='reservation_approval')
                        <div class="grid-form">
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    @include('Component.form.select',['filed'=>'application_office_id','array'=>$listBusinessOffices])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.input',['filed'=>'reservation_person','attr_input' => "maxlength='50'"])
                                </div>
                            </div>
                        </div>
                    @endif
                    <!--Block 1-->
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
                    <div class="grid-form">
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                @include('Component.form.date-picker',['filed'=>'start_date','required'=>true,'role' => $mode=='register' || $mode=='edit' ? 1 :2, 'attr_input' => ":editable='false'"])

                            </div>
                            <div class="col-md-8 col-sm-12">
                                @include('Component.form.time-picker',['class' => 'pl-0 wd-150','filed'=>'start_time','role' => $mode=='register' || $mode=='edit' ? 1 :2, 'attr_input' => ":editable='false'"])
                            </div>
                            <div class="break-row-form"></div>
                            <div class="col-md-4 col-sm-12">
                                @include('Component.form.select',['filed'=>'start_pref_cd','array'=>$listPrefecture,'required'=>true])
                            </div>
                            <div class="col-md-8 col-sm-12">
                                @include('Component.form.input',['class' =>'pl-0','filed'=>'start_address','attr_input' => "maxlength='200'"])
                            </div>
                            <div class="break-row-form"></div>
                            <div class="col-md-12 col-sm-12">
                                @include('Component.form.input',['filed'=>'asking_price','required'=>true, 'attr_input' => "maxlength='8' @blur='addComma(\"asking_price\")' @focus='removeComma(\"asking_price\")'", 'type' => 'tel'])
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
                                @include('Component.form.input',['class' =>'pl-0','filed'=>'arrive_address','attr_input' => "maxlength='50'"])
                            </div>
                            <div class="break-row-form"></div>
                            <div class="col-md-4 col-sm-12">
                                @include('Component.form.date-picker',['filed'=>'arrive_date','required'=>true,'role' => $mode=='register' || $mode=='edit' ? 1 :2, 'attr_input' => ":editable='false'"])
                            </div>
                        </div>

                    </div>
                @if($mode=='reservation' || $mode=='reservation_approval')
                </fieldset>
                @endif
            </form>
            <div class="sub-header mt-3">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                    </div>
                    @if(!empty($mEmptyInfo))
                        <div class="d-flex ml-auto">
                            @if($role==1 && $mode=='edit' && $mEmptyInfo['regist_office_id']== \Illuminate\Support\Facades\Auth::user()->mst_business_office_id)
                                <button class="btn btn-danger text-white" v-on:click="deleteInfo('{{$mEmptyInfo['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
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
                                    @if($mode=='register' || $mode=='edit')
                                        <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.".$mode) }}</button>
                                        <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                            {{ trans("common.button.reset") }}
                                        </button>
                                    @else
                                        @if(($mode=='reservation' && $mEmptyInfo['status']==1) || $mode=='reservation_approval')
                                        <button data-toggle="modal" data-target="#{{$mode}}Modal" class="btn btn-primary btn-submit">{{ trans("common.button.".$mode) }}</button>
                                        @endif
                                        @if($mode=='reservation_approval')
                                        <button data-toggle="modal" data-target="#reservation_rejectModal" class="btn btn-danger btn-submit m-auto">{{ trans("common.button.reservation_reject") }}</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
        @if($role==1 && $mode=='reservation' || $mode=='reservation_approval')
            @include('Layouts.modal',['id'=> $mode.'Modal','title'=> trans("empty_info.modal.".$mode.".title"),'content'=> trans("empty_info.modal.".$mode.".content"),'attr_input' => "@click='submit(".($mode=='reservation' ? 2 : 8).")'"])
        @endif
        @if($role==1 && $mode=='reservation_approval')
            @include('Layouts.modal',['id'=> 'reservation_rejectModal','title'=> trans("empty_info.modal.reservation_reject.title"),'content'=> trans("empty_info.modal.reservation_reject.content"),'attr_input' => "@click='submit(1)'"])
        @endif
    </div>
@endsection
@section("scripts")
    <script>
        var listRoute = "{{route('empty_info.list')}}";
        var user_login_mst_business_office_id = "{{ \Illuminate\Support\Facades\Auth::user()->mst_business_office_id }}";
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG07001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG07001'); ?>";
        messages["MSG10009"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG10009'); ?>";
        messages["MSG10010"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG10010'); ?>";
        messages["MSG10012"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG10012'); ?>";

        $(function() {
            var tabindex = 1;
            $('input[type="radio"],input[type="text"],input[type="tel"], select, textarea, button').each(function() {
                if (this.type != "hidden") {
                    var $input = $(this);
                    $input.attr("tabindex", tabindex);
                    tabindex++;
                }
                if(this.type == "button"){
                    if($(this).text()=='戻る'){
                        let index = parseInt($(this).attr("tabindex"));
                        $(this).attr("tabindex", index+1);
                    }
                    if($(this).text()=='削除'){
                        let index = parseInt($(this).attr("tabindex"));
                        $(this).attr("tabindex", index+3);
                    }
                }
                if(this.type == "submit"){
                    if($(this).text()=='登録'){
                        var index1 = parseInt($(this).attr("tabindex"));
                        $(this).attr("tabindex", index1-1);
                    }
                    if($(this).text()=='更新'){
                        var index1 = parseInt($(this).attr("tabindex"));
                        $(this).attr("tabindex", index1-2);
                    }
                }
            });
        });
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/empty-info.js') }}" charset="utf-8"></script>
@endsection
