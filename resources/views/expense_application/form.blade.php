@extends('Layouts.app')
@section('title',trans("expense_application.create.title_".$mode))
@section('title_header',trans("expense_application.create.title_".$mode))
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
    @php $prefix='expense_application.create.field.' @endphp
    <div class="wrapper-container" id="ctrExpenseApplicationVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                    <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                </div>

                <input type="hidden" id="hd_expense_application_edit" value="{!! !empty($mWFBusinessEntertain) ? 1:0 !!}">
                <input type="hidden" id="mode" value="{!! $mode !!}">
                @if(!empty($mWFBusinessEntertain))
                    @foreach($mWFBusinessEntertain as $key=>$value)
                        <input type="hidden" id="hd_{!! $key !!}" value="{{$value }}">
                    @endforeach
                    <div class="d-flex ml-auto">
                        @if($role==1 && ($mode=='register' || $mode=='edit'))
                            <button class="btn btn-danger text-white" v-on:click="deleteVacation('{{$mWFBusinessEntertain['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
                        @endif
                    </div>
                @endif
            </div>
            @if($role==1)
                <div class="sub-header-line-two">
                    <div class="grid-form border-0">
                        <div class="row">
                            @if($mode=='register' || $mode=='edit')
                                <div class="col-md-5 col-sm-12 row grid-col h-100"></div>
                                <div class="col-md-7 col-sm-12 row grid-col h-100">
                                    <button data-toggle="modal" data-target="#{{$mode}}Modal" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
                                    <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                        {{ trans("common.button.clear") }}
                                    </button>
                                </div>
                            @endif
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
                                <div class="col-md-7 col-sm-12">
                                    @include('Component.form.input',['class' =>'pl-0','filed'=>'staff_nm','attr_input' => "disabled"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-4 col-sm-12">
                                    @include('Component.form.input',['filed'=>'applicant_office_nm','attr_input' => "disabled",'required'=>true])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-4 col-sm-12">
                                    @include('Component.form.date-picker',['filed'=>'date','required'=>true, 'attr_input' => ""])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.input',['filed'=>'cost','required'=>true,'attr_input' => 'maxlength=8 @focus="removeCommaByID(\'cost\')" @blur="addCommaByID(\'cost\')" onkeypress="return isNumberKey(event)"'])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.input',['filed'=>'client_company_name','required'=>true,'attr_input' => "maxlength=200"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-3 col-sm-12">
                                    @include('Component.form.input',['filed'=>'client_members_count','required'=>true, 'attr_input' => "maxlength=4"])
                                </div>
                                <div class="no-padding wd-32 lh-38 text-center">名</div>
                                <div class="col-md-3 col-sm-12">
                                    @include('Component.form.input',['class' => 'pl-0','filed'=>'client_members', 'attr_input' => "maxlength=200"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-3 col-sm-12">
                                    @include('Component.form.input',['filed'=>'own_members_count','required'=>true, 'attr_input' => "maxlength=4"])
                                </div>
                                <div class="no-padding wd-32 lh-38 text-center">名</div>
                                <div class="col-md-3 col-sm-12">
                                    @include('Component.form.input',['class' => 'pl-0','filed'=>'own_members', 'attr_input' => "maxlength=200"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.input',['filed'=>'place','required'=>true,'attr_input' => "maxlength=200"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.textarea',['filed'=>'conditions','required'=>true,'attr_input' => "maxlength=400 class='h-50'"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12">
                                    @include('Component.form.textarea',['filed'=>'purpose','required'=>true,'attr_input' => "maxlength=400 class='h-50'"])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-4 col-sm-12">
                                    @include('Component.form.radio',['class'=>'w-100','filed'=>'deposit_flg','required'=>true,'array' => $listDepositClassification,'role' => $mode=='register' || $mode=='edit' ? 1 :2,'attr_input'=>"@change='handleDepositFlag'" ])
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    @include('Component.form.input',['filed'=>'deposit_amount','attr_input' => ':disabled="deposit_flg" maxlength=8 @focus="removeCommaByID(\'deposit_amount\')" @blur="addCommaByID(\'deposit_amount\')" onkeypress="return isNumberKey(event)"'])
                                </div>
                            </div>
                        </div>
                @if($mode=='reference' || $mode=='approval')
                    </fieldset>
                @endif
                @include('Component.workflow.search-email-address',['label' => trans("expense_application.create.field.additional_notice")])
                @include('Component.workflow.list-approval-status',['listWApprovalStatus' => $listWApprovalStatus])
            </form>
            <div class="sub-header mt-3">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                    </div>
                    @if(!empty($mWFBusinessEntertain))
                        <div class="d-flex ml-auto">
                            @if($role==1 && $mode=='edit')
                                <button class="btn btn-danger text-white" v-on:click="deleteExpenseApplication('{{$mWFBusinessEntertain['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
                            @endif
                        </div>
                    @endif
                </div>
                @if($role==1)
                    <div class="sub-header-line-two">
                        <div class="grid-form border-0">
                            <div class="row">
                                @if($mode=='register' || $mode=='edit')
                                    <div class="col-md-5 col-sm-12 row grid-col h-100"></div>
                                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                                        <button data-toggle="modal" data-target="#{{$mode}}Modal" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
                                        <button class="btn btn-light m-auto" type="button" @click="resetForm" >
                                            {{ trans("common.button.clear") }}
                                        </button>
                                    </div>
                                @else

                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
        @include("Component.workflow.modalSearch")
        @if($role==1)
            @include('Layouts.modal',['id'=> $mode.'Modal',
                                      'title'=> trans("expense_application.modal.".$mode.".title"),
                                      'content'=> trans("expense_application.modal.".$mode.".content"),
                                      'attr_input' => "@click='submit(".($mode=='approval' ? 1 : '').")'",
                                      'btn_cancel_title'=>'いいえ',
                                      'btn_ok_title'=>'はい'])
        @endif
        @if($role==1 && $mode=='approval')
            @include('Layouts.modal',['id'=> 'vacation_rejectModal','title'=> trans("expense_application.modal.reject.title"),'content'=> trans("expense_application.modal.reject.content"),'attr_input' => "@click='submit(0)'"])
        @endif
    </div>
@endsection
@section("scripts")
    <script>
        var listRoute = "{{route('expense_application.list')}}";
        var defaultApprovalKb = "{{array_keys($listDepositClassification)[0]}}";
        var messages = [];
        messages["MSG10028"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG10028'); ?>";
        var staff_cd = "<?php echo \Illuminate\Support\Facades\Auth::user()->staff_cd ?>";
        var staff_nm = "<?php echo \Illuminate\Support\Facades\Auth::user()->last_nm. \Illuminate\Support\Facades\Auth::user()->first_nm?>";
        var mst_business_office_id = "{{$businessOfficeID}}";
        var business_ofice_nm = "{{ $businessOfficeNm}}";
        var currentDate = "{{ $currentDate}}";
        var listWfAdditionalNotice = "{{ $listWfAdditionalNotice}}";
        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31
                && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/expense-application.js') }}" charset="utf-8"></script>
@endsection