@extends('Layouts.app')
@section('title',trans("take_vacation.create.title_".$mode))
@section('title_header',trans("take_vacation.create.title_".$mode))
@section('css')
    @if($role==1 && ($mode=='register' || $mode=='edit'))
        <style>
            .form-control[readonly]{
                background-color: white;
            }
            #searchStaffModal .modal-header{
                border-bottom: none !important;
            }
            #searchStaffModal .modal-footer{
                border-top: none !important;
            }
        </style>

    @endif
    <style>
        .form-control[disabled]{
            background-color: #D9D9D9;;
        }
    </style>
@endsection
@section('content')
    @php $prefix='take_vacation.create.field.' @endphp
    <div class="wrapper-container" id="ctrTakeVacationVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                    <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                </div>

                <input type="hidden" id="hd_take_vacation_edit" value="{!! !empty($mWPaidVacation) ? 1:0 !!}">
                <input type="hidden" id="mode" value="{!! $mode !!}">
                @if(!empty($mWPaidVacation))
                    @foreach($mWPaidVacation as $key=>$value)
                        <input type="hidden" id="hd_{!! $key !!}" value="{{$value }}">
                    @endforeach
                    <div class="d-flex ml-auto">
                        @if($role==1 && ($mode=='register' || $mode=='edit'))
                            <button class="btn btn-danger text-white" v-on:click="deleteVacation('{{$mWPaidVacation['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
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
                                    <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
                                    <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                        {{ trans("common.button.reset") }}
                                    </button>
                                @else
                                    @if($mode=='approval')
                                        <button data-toggle="modal" data-target="#{{$mode}}Modal" class="btn btn-primary btn-submit">{{ trans("common.button.reservation_approval") }}</button>
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
                @if($mode=='approval' || $mode=='reference')
                    <fieldset disabled="disabled">
                        @endif
                        <div class="text-danger">
                            {{ trans("common.description-form.indicates_required_items") }}
                        </div>
                    <!--Block 1-->
                        <div class="grid-form">
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    @include('Component.form.input',['filed'=>'applicant_id','attr_input' => "disabled",'required'=>true])
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    @include('Component.form.input',['class' =>'pl-0','filed'=>'staff_nm','attr_input' => "disabled"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-4 col-sm-12">
                                    @include('Component.form.input',['filed'=>'applicant_office_nm','attr_input' => "disabled",'required'=>true])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.radio',['class'=>'w-100','filed'=>'approval_kb','array' => $listVacationIndicator,'required'=>true,'role' => $mode=='register' || $mode=='edit' ? 1 :2 ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.radio',['class'=>'w-100','filed'=>'half_day_kb','array' => $listVacationAcquisitionTimeIndicator,'required'=>true,'role' => $mode=='register' || $mode=='edit' ? 1 :2 , 'attr_input' => "@change='handleChangeHalfDay'"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',['filed'=>'start_date','required'=>true, 'attr_input' => ":editable='false' @change='handleSelectDate' :disabled='disabledStartDate'"])

                                </div>
                                <div class="no-padding wd-32 lh-38 text-center">～</div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',['class' => 'pl-0','filed'=>'end_date', 'attr_input' => ":editable='false' @change='handleSelectDate' :disabled='disabledEndDate'"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',['filed'=>'days','attr_input' => "maxlength='11' :disabled='disabledDays'",'required'=>true])
                                </div>
                                <div class="no-padding wd-32 lh-38 text-center">日</div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',['class' =>'pl-0','filed'=>'times','attr_input' => "maxlength='11' :disabled='disabledTimes'"])
                                </div>
                                <div class="no-padding wd-32 lh-38 text-center">時間</div>
                                <div class="break-row-form"></div>
                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.textarea',['filed'=>'reasons','attr_input' => "maxlength='200' rows='6' class='h-100'" ,'required'=>true, 'label_class' => 'h-100'])
                                </div>
                            </div>
                        </div>
                        @if($mode=='reference' || $mode=='approval')
                    </fieldset>
                @endif
                        <div class="grid-form">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="wrap-control-group">
                                        <label for="search_vehicle">{{ trans("take_vacation.create.field.additional_notice") }}</label>
                                        <div class="row" v-for="(item,index) in  field.wf_additional_notice" style="margin-bottom: 10px !important;">
                                            <div class="col-md-1 col-sm-12" >
                                                <button class="btn btn-outline-secondary" type="button" @click="openModal(index)">{{ trans("common.button.search") }}</button>
                                            </div>
                                            <div class="col-md-11 col-sm-12">
                                                <input v-model="item.email_address"
                                                       type="email"
                                                       class="form-control w-100"
                                                       :id="'email_address'+index"
                                                       maxlength="300"
                                                       v-bind:class="errors.wf_additional_notice!= undefined && errors.wf_additional_notice[0][index]!= undefined ? 'form-control is-invalid':'form-control' "
                                                >
                                                <span v-cloak v-if="errors.wf_additional_notice != undefined" class="message-error" v-html="errors.wf_additional_notice[0][index]"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-1 col-sm-12"></div>
                                            <div class="col-md-11 col-sm-12">
                                                <button class="btn btn-outline-secondary" type="button" @click="addRow"> {{ trans("common.button.add") }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!empty($listWApprovalStatus))
                            <div class="grid-form">
                                <div class="row">
                                    @foreach($listWApprovalStatus as $item)
                                        <div class="wd-180 text-right font-weight-bold">{{$item->title}}</div>
                                        <div class="col-md-1 col-sm-12">{{$item->status}}</div>
                                        <div class="col-md-2 col-sm-12">{{$item->approval_date}}</div>
                                        <div class="col-md-7 col-sm-12">{{$item->send_back_reason}}</div>
                                        <div class="break-row-form"></div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

            </form>
            <div class="sub-header mt-3">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                    </div>
                    @if(!empty($mWPaidVacation))
                        <div class="d-flex ml-auto">
                            @if($role==1 && $mode=='edit')
                                <button class="btn btn-danger text-white" v-on:click="deleteVacation('{{$mWPaidVacation['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
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
                                        <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
                                        <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                            {{ trans("common.button.reset") }}
                                        </button>
                                    @else
                                        @if($mode=='approval')
                                            <button data-toggle="modal" data-target="#{{$mode}}Modal" class="btn btn-primary btn-submit">{{ trans("common.button.reservation_approval") }}</button>
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
        @include("take_vacation.modalSearch")
        @if($role==1)
            @include('Layouts.modal',['id'=> $mode.'Modal','title'=> trans("take_vacation.modal.".$mode.".title"),'content'=> trans("take_vacation.modal.".$mode.".content"),'attr_input' => "@click='submit(".($mode=='reservation' ? 2 : 8).")'"])
        @endif
    </div>
@endsection
@section("scripts")
    <script>
        var listRoute = "{{route('take_vacation.list')}}";
        var defaultApprovalKb = "{{array_keys($listVacationIndicator)[0]}}";
        var defaultHalfDayKb = "{{array_keys($listVacationAcquisitionTimeIndicator)[0]}}";
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        var staff_cd = "<?php echo \Illuminate\Support\Facades\Auth::user()->staff_cd ?>";
        var staff_nm = "<?php echo \Illuminate\Support\Facades\Auth::user()->last_nm. \Illuminate\Support\Facades\Auth::user()->first_nm?>";
        var staff_nm = "<?php echo \Illuminate\Support\Facades\Auth::user()->last_nm. \Illuminate\Support\Facades\Auth::user()->first_nm?>";
        var mst_business_office_id = "<?php echo \Illuminate\Support\Facades\Auth::user()->mst_business_office_id?>";
        var business_ofice_nm = "{{ $businessOfficeNm}}";
        var currentDate = "{{ $currentDate}}";
        var listWfAdditionalNotice = "{{ $listWfAdditionalNotice}}";

    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/take-vacation.js') }}" charset="utf-8"></script>
@endsection