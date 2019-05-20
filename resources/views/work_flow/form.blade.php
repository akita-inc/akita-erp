@extends('Layouts.app')
@section('title',trans("work_flow.create.title_step_1"))
@section('title_header',trans("work_flow.create.title_step_1"))
@section('css')
    <style>
        .form-control{
            background-clip: initial !important;
        }

    </style>
@endsection
@section('content')
    @php $prefix='work_flow.create.field.' @endphp
    <div class="wrapper-container" id="ctrWorkFlowVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                    <button class="btn btn-black" type="button" @click="backHistory" v-cloak v-if="screenStep==1">{{ trans("common.button.back") }}</button>
                    <button class="btn btn-black" type="button" @click="previousStep" v-cloak v-else>{{ trans("common.button.back") }}</button>
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
                                <button v-if="screenStep==3 && work_flow_edit==0" v-cloak @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
                                <button v-if="screenStep==3&& work_flow_edit==1" v-cloak @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.edit") }}</button>
                                <button v-if="screenStep!=3" v-cloak class="btn btn-primary btn-submit" type="button" v-on:click="nextStep">{{ trans("work_flow.create.button.next") }}</button>
                                <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                    {{ trans("work_flow.create.button.clear") }}
                                </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form class="form-inline" role="form" autocomplete="off">
            <div class="text-danger">
                {{ trans("common.description-form.indicates_required_items") }}
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        @include('Component.form.input',['filed'=>'name','required'=>true,'attr_input' => "maxlength='100' :disabled='screenStep != 1'"])
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12">
                        @include('Component.form.input',['filed'=>'steps','required'=>true,'attr_input' => "maxlength='2' :disabled='screenStep != 1'"])
                    </div>
                    <div class="col-md-6 col-sm-12 no-padding lh-38">段階</div>

                </div>
            </div>
            <div class="grid-form" v-if="screenStep==2" v-cloak>
                <div class="row" v-for="(items,index) in field.mst_wf_require_approval_base">
                    <div class="col-md-6 col-sm-12">
                        @include('Component.form.select-vue',[
                            'filed'=>'approval_levels',
                            'array'=>$listWfLevel,
                            'filedId'=> "'mst_wf_require_approval_base_approval_levels_'+index" ,
                            'filedMode'=>"items.approval_levels",
                            'index' => "index+1"
                        ])
                    </div>
                    <div class="col-md-6 col-sm-12">
                        @include('Component.form.select-vue',[
                            'filed'=>'approval_kb',
                            'array'=>$listWfApprovalIndicator,
                            'filedId'=> "'mst_wf_require_approval_base_approval_kb_'+index" ,
                            'filedMode'=>"items.approval_kb",
                        ])
                    </div>
                    <div class="break-row-form"></div>
                </div>
            </div>
            <div class="grid-form" v-if="screenStep==3" v-cloak v-for="(items,section) in field.mst_wf_require_approval">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="wrap-control-group ">
                            <label for="steps">
                                申請者所属区分
                            </label>
                            <span class="pl-3 lh-38">@{{items.applicant_section_nm}}</span>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row" v-for="(item,index) in items.list">
                    <div class="col-md-6 col-sm-12">
                        @include('Component.form.select-vue',[
                            'filed'=>'approval_levels',
                            'array'=>$listWfLevel,
                            'filedId'=> "'mst_wf_require_approval_levels_'+section+'_'+index" ,
                            'filedMode'=>"item.approval_levels",
                            'index' => "index+1"
                        ])
                    </div>
                    <div class="col-md-6 col-sm-12">
                        @include('Component.form.select-vue',[
                            'filed'=>'approval_kb',
                            'array'=>$listWfApprovalIndicator,
                            'filedId'=> "'mst_wf_require_approval_kb_'+section+'_'+index" ,
                            'filedMode'=>"item.approval_kb",
                        ])
                    </div>
                    <div class="break-row-form"></div>
                </div>
            </div>
        </form>
        <div class="sub-header mt-3">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                    <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                </div>
            </div>

            <div class="sub-header-line-two">
                <div class="grid-form border-0">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 row grid-col h-100"></div>
                        <div class="col-md-7 col-sm-12 row grid-col h-100">
                            <button v-if="screenStep==3 && work_flow_edit==0" v-cloak @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
                            <button v-if="screenStep==3&& work_flow_edit==1" v-cloak @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.edit") }}</button>
                            <button v-if="screenStep!=3" v-cloak class="btn btn-primary btn-submit" type="button" v-on:click="nextStep">{{ trans("work_flow.create.button.next") }}</button>
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
        var defaultLevel = "{{array_keys($listWfLevel)[0]}}";
        var defaultKb = "{{array_keys($listWfApprovalIndicator)[0]}}";
        var listRoute = "{{route('work_flow.list')}}";
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";


    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/work-flow.js') }}" charset="utf-8"></script>
@endsection
