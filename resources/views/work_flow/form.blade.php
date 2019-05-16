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
                    <div class="col-md-12 col-sm-12">
                        @include('Component.form.input',['filed'=>'name','required'=>true,'attr_input' => "maxlength='50'"])
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12">
                        @include('Component.form.input',['filed'=>'steps','required'=>true,'attr_input' => "maxlength='50'"])
                    </div>
                    <div class="col-md-6 col-sm-12 no-padding lh-38">段階</div>

                </div>
            </div>
            <div class="grid-form">
                <div class="row" v-for="(items,index) in field.mst_wf_require_approval_base">
                    @include('work_flow.row',[])
                </div>
            </div>
            <div class="grid-form" v-for="(items,index) in field.mst_wf_require_approval">
                <div class="row" v-for="(item,k) in items">
                    @include('work_flow.row',[])
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
