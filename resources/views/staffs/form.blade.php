@extends('Layouts.app')
@section('title',trans("staffs.create.title".(!empty($staff) ? "_edit":"")))
@section('title_header',trans("staffs.create.title".(!empty($staff) ? "_edit":"")))
@section('content')
    @include('Layouts.alert')
    @php $prefix='staffs.create.field.' @endphp
    <div class="wrapper-container" id="ctrStaffsVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                </div>
                <input type="hidden" id="hd_adhibition_end_dt_default" value="{!! config('params.adhibition_end_dt_default') !!}">
                <input type="hidden" id="hd_staff_edit" value="{!! !empty($staff) ? 1:0 !!}">
                <input type="hidden" id="roles_staff_screen" value="@php echo implode(",",$rolesStaffScreen) @endphp">
                @if(!empty($staff))
                    @foreach($staff as $key=>$value)
                        @if($key == 'adhibition_start_dt'
                            || $key == 'adhibition_end_dt'
                            || $key == 'business_start_dt'
                        )
                            @php($value = date("Y/m/d",strtotime($value)))
                        @endif
                        <input type="hidden" id="hd_{!! $key !!}" value="{!! $value !!}">
                    @endforeach
                    <div class="d-flex ml-auto">
                        @if($role==1 && $staff['staff_cd']!=Auth::user()->staff_cd)
                        <button class="btn btn-danger text-white" v-on:click="deleteStaff('{{$staff['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
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
                            <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.".(!empty($staff) ? "edit":"register")) }}</button>
                            @if($flagRegisterHistory)
                                <button class="btn btn-primary btn-submit m-auto" type="button" @click="clone()" >
                                    {{ trans("common.button.register_history_left") }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @if($role==9 || ($role==2 && empty($staff)))
            <div class="alert alert-danger w-100 mt-2">
                {{\Illuminate\Support\Facades\Lang::get('messages.MSG10006')}}
            </div>
        @endif
        @if($role==1 || ($role==2 && !empty($staff)))
        <form class="form-inline" role="form" enctype="multipart/form-data" >
            @if($role==2)
                    <fieldset disabled="disabled" class="w-100">
            @endif
            @if(in_array(1,$rolesStaffScreen))
            <div class="text-danger">
                {{ trans("common.description-form.indicates_required_items") }}
            </div>
            <!--Block 1-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-300','filed'=>'staff_cd','required'=>true,'attr_input' => "maxlength='5'".(!empty($staff) ? 'readonly=""':'')])
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <div class="col-md-6 col-sm-12 no-padding">
                            @include('Component.form.date-picker',['filed'=>'adhibition_start_dt'.(!empty($staff) ? '_edit':''),'required'=>true,'role' => $role])
                        </div>
                        <div class="col-md-6 col-sm-12 pd-l-20">
                            @if($flagRegisterHistory)
                                @include('Component.form.date-picker',['filed'=>'adhibition_end_dt'.(!empty($staff) ? '_edit':''),'required'=>true ,'role' => $role])
                            @else
                                @include('Component.form.input',['filed'=>'adhibition_end_dt'.(!empty($staff) ? '_edit':''),'attr_input' => 'readonly="" value="'.config('params.adhibition_end_dt_default').'"' ])
                            @endif
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'password','class'=>'w-100','required'=>true,'attr_input'=>"maxlength=50 type='password' class='w-100' autocomplete='new-password' autofill='off'"])
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        @if($role!=2 && !$flagRegisterHistory)
                        <div class="col-md-6 col-sm-12 no-padding">
                                @include('Component.form.input',['filed'=>'confirm_password','class'=>'w-100','required'=>true,'attr_input'=>"maxlength=50 type='password' class='w-100' autocomplete='new-password' autofill='off'"])
                        </div>
                        @endif
                        @if($flagRegisterHistory)
                            <div class="col-md-6 col-sm-12 no-padding">
                                @include('Component.form.date-picker',['filed'=>'adhibition_start_dt_history','required'=>true,'role' => $role])
                            </div>
                            <div class="col-md-6 col-sm-12 pd-l-20">
                                @include('Component.form.input',['filed'=>'adhibition_end_dt_history','attr_input' => 'readonly="" value="'.config('params.adhibition_end_dt_default').'"' ])
                            </div>
                        @endif
                    </div>
                </div>
                @if($role!=2 && (!empty($flagRegisterHistory)|| $flagRegisterHistory))
                 <div class="break-row-form"></div>
                 <div class="row">
                    <div class="col-md-5 col-sm-12">
                          @include('Component.form.input',['filed'=>'confirm_password','class'=>'w-100','attr_input'=>"maxlength=50 type='password' class='w-100' autocomplete='new-password' autofill='off'"])
                    </div>
                </div>
                @endif
            </div>
            <!--Block 2-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-300','filed'=>'employment_pattern_id','array'=>@$listEmployPattern])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.select',['class'=>'w-75','filed'=>'position_id','array'=>@$listPosition])

                    </div>

                </div>
            </div>
            <!--Block 3-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',[
                             'filed'=>'last_nm',
                             'attr_input' => 'maxlength=25 v-on:input="convertKana($event, \'last_nm_kana\')"'
                         ])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'last_nm_kana','attr_input'=>'maxlength=50'])
                    </div>

                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',[
                             'filed'=>'first_nm',
                             'attr_input' => 'maxlength=25 v-on:input="convertKana($event, \'first_nm_kana\')"'
                         ])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'first_nm_kana','attr_input'=>'maxlength=50'])
                    </div>
                </div>
            </div>
            <!--Block 4-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'zip_cd','attr_input' => "maxlength=7"])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        <button type="button" class="btn btn-black" v-on:click="getAddrFromZipCode()">〒 → 住所</button>
                    </div>

                    <div class="break-row-form"></div>

                    <!--prefectures_cd address1-->

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-300','filed'=>'prefectures_cd','array'=>@$listPrefecture])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'address1','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="break-row-form"></div>

                    <!--address2 address3-->

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'address2','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'address3','attr_input' => "maxlength='50'"])
                    </div>

                    <div class="break-row-form"></div>
                    <!--phone_number fax_number-->

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'landline_phone_number','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'cellular_phone_number','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="break-row-form"></div>

                    <!--customer_category_id prime_business_office_id-->
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'corp_cellular_phone_number','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="col-md-5 col-sm-12 pd-l-20">
                        @include('Component.form.textarea',['filed'=>'notes','attr_input' => "maxlength='50'"])
                    </div>
                </div>
            </div>
            <!--Block 5-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-300','filed'=>'sex_id','array'=>@$listSex])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.date-picker',['class'=>'wd-350','filed'=>'birthday','role' => $role])
                    </div>

                </div>
            </div>
            <!--Block 6-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.date-picker',['class'=>'wd-350','filed'=>'enter_date','role' => $role])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.date-picker',['class'=>'wd-350','filed'=>'retire_date','role' => $role])
                    </div>

                </div>
            </div>
            <!--Block 7-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-300','filed'=>'insurer_number','attr_input' => "maxlength='3'"])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'basic_pension_number','attr_input' => "maxlength='11'"])
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'person_insured_number','attr_input' => "maxlength='11'"])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'health_insurance_class','attr_input'=>"maxlength='10'"])
                    </div>

                    <div class="break-row-form"></div>
                    <div class="break-row-form"></div>
                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'welfare_annuity_class','attr_input'=>"maxlength='10'"])
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <div class="col-md-6 col-sm-12 no-padding">
                            <label class="grid-form-label pl-150">コード</label>
                            <div class="wrap-control-group ">
                                <label for="relocation_municipal_office_cd">
                                    {{ trans($prefix.'relocation_municipal_office_cd') }}
                                </label>
                                <vue-autosuggest
                                    :suggestions="filteredOptions"
                                    :limit="10"
                                    :input-props="inputProps"
                                    :on-selected="onSelected"
                                >
                                </vue-autosuggest>
                            </div>
                        </div>
                        <div v-if="errors.relocation_municipal_office_cd != undefined" class="w-100">
                            <span v-cloak v-if="errors.relocation_municipal_office_cd != undefined" class="message-error" v-html="errors.relocation_municipal_office_cd.join('<br />')"></span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!--Block 11-->
                 @if(in_array(5,$rolesStaffScreen))
                        <div class="grid-form ">
                            <p class="header-collapse" >
                                <a data-toggle="collapse" href="#b_mst_staff_dependents" role="button" aria-expanded="false" aria-controls="collapseExample">
                                    {{trans($prefix."mst_staff_dependents_title")}}
                                </a>
                            </p>
                            <div class="collapse" id="b_mst_staff_dependents">
                                <div class="wrapper-collapse">
                                    <div class="grid-form items-collapse" v-for="(items,index) in field.mst_staff_dependents">
                                        <div v-cloak class="row">
                                            <div class="col-md-5 col-sm-12">
                                                @include('Component.form.select-vue',[
                                                    'filed'=>'dept_dependent_kb',
                                                     'class'=>'wd-350',
                                                    'filedId'=>"'mst_staff_dependents_dependent_kb'+index",
                                                    'filedMode'=>"items.dept_dependent_kb",
                                                    'array'=>$listDependentKBs,
                                                    'filedErrors'=>"mst_staff_dependents"

                                                ])
                                            </div>
                                            <div class="col-md-7 col-sm-12 pd-l-20">
                                            </div>
                                            <div class="break-row-form"></div>

                                            <div class="col-md-5 col-sm-12">
                                                @include('Component.form.input-vue',[
                                                    'filed'=>'dept_last_nm',
                                                    'filedId'=>"'mst_staff_dependents_last_nm'+index",
                                                    'filedMode'=>"items.dept_last_nm",
                                                    'attr_input'=>'maxlength="25" v-on:input="convertKanaBlock($event, \'dept_last_nm\', \'dept_last_nm_kana\')"',
                                                    'filedErrors'=>"mst_staff_dependents",

                                                ])
                                            </div>
                                            <div class="col-md-7 col-sm-12 pd-l-20">
                                                @include('Component.form.input-vue',[
                                                    'filed'=>'dept_last_nm_kana',
                                                    'filedId'=>"'mst_staff_dependents_last_nm_kana'+index",
                                                    'filedMode'=>"items.dept_last_nm_kana",
                                                    'attr_input'=>"maxlength='50'",
                                                    'filedErrors'=>"mst_staff_dependents"
                                                ])
                                            </div>

                                            <div class="break-row-form"></div>

                                            <div class="col-md-5 col-sm-12">
                                                @include('Component.form.input-vue',[
                                                    'filed'=>'dept_first_nm',
                                                    'filedId'=>"'mst_staff_dependents_first_nm'+index",
                                                    'filedMode'=>"items.dept_first_nm",
                                                    'attr_input'=>'maxlength="25" v-on:input="convertKanaBlock($event, \'dept_first_nm\',\'dept_first_nm_kana\')"',
                                                    'filedErrors'=>"mst_staff_dependents"
                                                ])
                                            </div>
                                            <div class="col-md-7 col-sm-12 pd-l-20">
                                                @include('Component.form.input-vue',[
                                                    'filed'=>'dept_first_nm_kana',
                                                    'filedId'=>"'mst_staff_dependents_first_nm_kana'+index",
                                                    'filedMode'=>"items.dept_first_nm_kana",
                                                     'attr_input'=>"maxlength=50",
                                                    'filedErrors'=>"mst_staff_dependents"
                                                ])
                                            </div>

                                            <div class="break-row-form"></div>

                                            <div class="col-md-5 col-sm-12">
                                                @include('Component.form.date-picker-vue',[
                                                    'filed'=>'dept_birthday',
                                                    'class'=>'wd-350',
                                                    'filedId'=>"'mst_staff_dependents_birthday'+index",
                                                    'filedMode'=>"items.dept_birthday",
                                                    'filedErrors'=>"mst_staff_dependents",
                                                    'role' => $role
                                                ])
                                            </div>
                                            <div class="col-md-7 col-sm-12 pd-l-20">
                                                @include('Component.form.select-vue',[
                                                    'filed'=>'dept_sex_id',
                                                    'class'=>'wd-300',
                                                    'filedId'=>"'mst_staff_dependents_sex_id'+index",
                                                    'filedMode'=>"items.dept_sex_id",
                                                    'array'=>$listSex,
                                                    'filedErrors'=>"mst_staff_dependents"
                                                ])
                                            </div>
                                            <div class="break-row-form"></div>

                                            <div class="col-md-5 col-sm-12">
                                                @include('Component.form.input-vue',[
                                                    'filed'=>'dept_social_security_number',
                                                    'class'=>'wd-350',
                                                    'attr_input' => "maxlength='10'",
                                                    'filedId'=>"'mst_staff_dependents_social_security_number'+index",
                                                    'filedMode'=>"items.dept_social_security_number",
                                                    'filedErrors'=>"mst_staff_dependents"
                                                ])
                                            </div>
                                            <!--address2 address3-->

                                        </div>
                                        <button @click="removeRows('mst_staff_dependents',index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                                    </div>
                                    <button @click="addRows('mst_staff_dependents')" type="button" class="btn btn-primary btn-rows-add">+</button>
                                </div>
                            </div>
                        </div>
                    @endif
            <!--Block 8-->
            @if(in_array(2,$rolesStaffScreen))
            <div class="grid-form">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_educational_background" role="button" aria-expanded="false" aria-controls="collapseExample">
                         {{trans($prefix."educational_background_title")}}
                    </a>
                </p>
                <div class="collapse" id="b_mst_educational_background">
                    <div class="wrapper-collapse pr-0">
                        <div class="grid-form items-collapse">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.input',['class'=>'w-75','filed'=>'educational_background','attr_input' => "maxlength='50'"])
                                </div>

                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',['filed'=>'educational_background_dt','class'=>'wd-350','role' => $role])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!--Block 9-->
            @if(in_array(3,$rolesStaffScreen))
            <div class="grid-form">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_staff_job_experiences" role="button" aria-expanded="false" aria-controls="collapseExample">
                         {{trans($prefix."mst_staff_job_experiences_title")}}
                    </a>
                </p>
                <div class="collapse" id="b_mst_staff_job_experiences">
                    <div class="wrapper-collapse">
                        <div class="grid-form items-collapse" v-for="(items,index) in field.mst_staff_job_experiences">
                            <div v-cloak class="row">
                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'job_duties',
                                        'filedId'=>"'mst_staff_job_experiences_job_duties'+index",
                                        'filedMode'=>"items.job_duties",
                                        'filedErrors'=>"mst_staff_job_experiences",
                                        'attr_input' => "maxlength='50'"
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <!--address2 address3-->

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker-vue',[
                                        'filed'=>'staff_tenure_start_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_job_experiences_staff_tenure_start_dt'+index",
                                        'filedMode'=>"items.staff_tenure_start_dt",
                                        'filedErrors'=>"mst_staff_job_experiences",
                                        'role' => $role
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker-vue',[
                                        'filed'=>'staff_tenure_end_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_job_experiences_staff_tenure_start_dt'+index",
                                        'filedMode'=>"items.staff_tenure_end_dt",
                                        'filedErrors'=>"mst_staff_job_experiences",
                                        'role' => $role
                                    ])
                                </div>

                            </div>
                            <button @click="removeRows('mst_staff_job_experiences',index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows('mst_staff_job_experiences')" type="button" class="btn btn-primary btn-rows-add">+</button>
                    </div>
                </div>
            </div>
            @endif
            <!--Block 10-->
            @if(in_array(4,$rolesStaffScreen))
            <div class="grid-form">
                <p class="header-collapse">
                    <a data-toggle="collapse" href="#b_mst_staff_qualifications" role="button" aria-expanded="false" aria-controls="collapseExample">
                        {{trans($prefix."mst_staff_qualifications_title")}}
                    </a>
                </p>
                <div class="collapse" id="b_mst_staff_qualifications">
                    <div class="wrapper-collapse">
                        <div class="grid-form items-collapse" v-for="(items,index) in field.mst_staff_qualifications">
                            <div v-cloak class="row">
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select-vue',[
                                        'filed'=>'qualification_kind_id',
                                        'filedId'=>"'mst_staff_qualification_kind_id'+index",
                                        'filedMode'=>"items.qualification_kind_id",
                                        'filedErrors'=>"mst_staff_qualifications",
                                        'attr_input' => "maxlength='5'",
                                        'array'=>@$listQualificationKind

                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker-vue',[
                                        'filed'=>'acquisition_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_qualifications_acquisition_dt'+index",
                                        'filedMode'=>"items.acquisition_dt",
                                        'filedErrors'=>"mst_staff_qualifications",
                                        'role' => $role
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <!--address2 address3-->

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker-vue',[
                                        'filed'=>'period_validity_start_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_qualifications_period_validity_start_dt'+index",
                                        'filedMode'=>"items.period_validity_start_dt",
                                         'filedErrors'=>"mst_staff_qualifications",
                                         'role' => $role

                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker-vue',[
                                        'filed'=>'period_validity_end_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_qualifications_period_validity_end_dt'+index",
                                        'filedMode'=>"items.period_validity_end_dt",
                                        'filedErrors'=>"mst_staff_qualifications",
                                        'role' => $role
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.textarea-vue',[
                                        'filed'=>'qualifications_notes',
                                        'filedId'=>"'mst_staff_qualifications_notes'+index",
                                        'filedMode'=>"items.qualifications_notes",
                                        'filedErrors'=>"mst_staff_qualifications"
                                        ,'attr_input' => "maxlength='100'"
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'amounts',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_qualifications_amounts'+index",
                                        'filedMode'=>"items.amounts",
                                        'filedErrors'=>"mst_staff_qualifications"
                                        ,'attr_input' => "maxlength='10'"
                                    ])
                                    <div class="break-row-form"></div>
                                        @include('Component.form.date-picker-vue',[
                                            'filed'=>'payday',
                                            'class'=>'wd-350',
                                            'filedId'=>"'mst_staff_qualifications_payday'+index",
                                            'filedMode'=>"items.payday",
                                            'filedErrors'=>"mst_staff_qualifications",
                                            'role' => $role
                                         ])
                                </div>

                            </div>
                            <button @click="removeRows('mst_staff_qualifications',index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows('mst_staff_qualifications')" type="button" class="btn btn-primary btn-rows-add">+</button>
                    </div>
                </div>
            </div>
            @endif
            <!--Block 12-->
            @if(in_array(6,$rolesStaffScreen))
            <div class="grid-form ">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_staff_driver_license" role="button" aria-expanded="false" aria-controls="collapseExample">
                        運転免許証
                    </a>
                </p>
                <div class="collapse" id="b_mst_staff_driver_license">
                    <div class="wrapper-collapse pr-0">
                        <div class="grid-form items-collapse">
                            <div class="row">
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'drivers_license_number',
                                        'filedId'=>"mst_staff_driver_license_number",
                                        'filedMode'=>"items.drivers_license_number",
                                        'attr_input' => "maxlength='12'",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                       'filed'=>'drivers_license_color_id',
                                        'class'=>'wd-350',
                                       'filedId'=>"mst_staff_driver_license_color_id",
                                       'filedMode'=>"items.drivers_license_color_id",
                                       'array'=>$listDriversLicenseColors,
                                   ])
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'drivers_license_issued_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"mst_staff_driver_license_issued_dt",
                                        'filedMode'=>"items.drivers_license_issued_dt",
                                        'role' => $role
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'drivers_license_period_validity',
                                         'class'=>'wd-350',
                                        'filedId'=>"mst_staff_drivers_license_period_validity",
                                        'filedMode'=>"items.drivers_license_period_validity",
                                        'role' => $role
                                    ])
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.file',[
                                        'filed'=>'drivers_license_picture',
                                        'attr_input'=>'v-on:change="onFileChange" accept=".png, .jpg, .jpeg"',
                                        'attr_delete_path'=>'@click="deleteFileUpload"',
                                        'filedId'=>"mst_staff_drivers_license_picture",
                                        'filedMode'=>"items.drivers_license_picture",
                                        'ref'=>"'drivers_license_picture'",
                                        'role' => $role,
                                        "item" => $staff,
                                        "pathPreview" => \App\Helpers\Common::getPathStaff($staff,"drivers_license_picture")
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_1',
                                         'filedId'=>"mst_staff_drivers_license_divisions_1",
                                         'filedMode'=>"items.drivers_license_divisions_1",
                                         'array'=>$listDriversLicenseDivisions
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_2',
                                         'filedId'=>"mst_staff_drivers_license_divisions_2",
                                         'filedMode'=>"items.drivers_license_divisions_2",
                                         'array'=>$listDriversLicenseDivisions
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_3',
                                         'filedId'=>"mst_staff_drivers_license_divisions_3",
                                         'filedMode'=>"items.drivers_license_divisions_3",
                                         'array'=>$listDriversLicenseDivisions
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_4',
                                         'filedId'=>"mst_staff_drivers_license_divisions_4",
                                         'filedMode'=>"items.drivers_license_divisions_4",
                                         'array'=>$listDriversLicenseDivisions
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_5',
                                         'filedId'=>"mst_staff_drivers_license_divisions_5",
                                         'filedMode'=>"items.drivers_license_divisions_5",
                                         'array'=>$listDriversLicenseDivisions
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_6',
                                         'filedId'=>"mst_staff_drivers_license_divisions_6",
                                         'filedMode'=>"items.drivers_license_divisions_6",
                                         'array'=>$listDriversLicenseDivisions
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_7',
                                         'filedId'=>"mst_staff_drivers_license_divisions_7",
                                         'filedMode'=>"items.drivers_license_divisions_7",
                                         'array'=>$listDriversLicenseDivisions
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_8',
                                         'filedId'=>"mst_staff_drivers_license_divisions_8",
                                         'filedMode'=>"items.drivers_license_divisions_8",
                                         'array'=>$listDriversLicenseDivisions
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_9',
                                         'filedId'=>"mst_staff_drivers_license_divisions_9",
                                         'filedMode'=>"items.drivers_license_divisions_9",
                                         'array'=>$listDriversLicenseDivisions
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_10',
                                         'filedId'=>"mst_staff_drivers_license_divisions_10",
                                         'filedMode'=>"items.drivers_license_divisions_10",
                                         'array'=>$listDriversLicenseDivisions
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_11',
                                         'filedId'=>"mst_staff_drivers_license_divisions_11",
                                         'filedMode'=>"items.drivers_license_divisions_11",
                                         'array'=>$listDriversLicenseDivisions
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_12',
                                         'filedId'=>"mst_staff_drivers_license_divisions_12",
                                         'filedMode'=>"items.drivers_license_divisions_12",
                                         'array'=>$listDriversLicenseDivisions
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_13',
                                         'filedId'=>"mst_staff_drivers_license_divisions_13",
                                         'filedMode'=>"items.drivers_license_divisions_13",
                                         'array'=>$listDriversLicenseDivisions
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_14',
                                         'filedId'=>"mst_staff_drivers_license_divisions_14",
                                         'filedMode'=>"items.drivers_license_divisions_14",
                                         'array'=>$listDriversLicenseDivisions
                                    ])
                                </div>
                                <!--address2 address3-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!--Block 13-->
            @if(in_array(7,$rolesStaffScreen))
            <div class="grid-form">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_others" role="button" aria-expanded="false" aria-controls="collapseExample">
                        その他
                    </a>
                </p>
                <div class="collapse" id="b_mst_others">
                    <div class="wrapper-collapse pr-0">
                        <div class="grid-form items-collapse">
                            <div class="row">
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                        'filed'=>'belong_company_id',
                                        'filedId'=>"mst_others_belong_company_id",
                                        'filedMode'=>"items.belong_company_id",
                                        'array'=>$listBelongCompanies,
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                        'filed'=>'occupation_id',
                                        'class'=>'w-75',
                                        'filedId'=>"mst_others_occupation_id",
                                        'filedMode'=>"items.occupation_id",
                                        'array'=>$listOccupation,
                                    ])
                                </div>
                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                        'filed'=>'mst_business_office_id',
                                        'filedId'=>"mst_others_mst_business_office_id",
                                        'filedMode'=>"items.mst_business_office_id",
                                        'array'=>$mBusinessOffices
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                        'filed'=>'depertment_id',
                                        'class'=>'w-75',
                                        'filedId'=>"mst_others_depertment_id",
                                        'filedMode'=>"items.depertment_id",
                                        'array'=>$listDepartments
                                    ])
                                </div>
                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'retire_reasons',
                                        'filedId'=>"mst_others_retire_reasons",
                                        'filedMode'=>"items.retire_reasons",
                                         'attr_input' => "maxlength='50'",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                       'filed'=>'retire_dt',
                                        'class'=>'wd-350',
                                       'filedId'=>"mst_others_retire_dt",
                                       'filedMode'=>"items.retire_dt",
                                       'role' => $role
                                   ])
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'death_reasons',
                                        'filedId'=>"mst_others_death_reasons",
                                        'filedMode'=>"items.death_reasons",
                                        'attr_input' => "maxlength='50'",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'death_dt',
                                         'class'=>'wd-350',
                                        'filedId'=>"mst_others_death_dt",
                                        'filedMode'=>"items.death_dt",
                                        'role' => $role
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'driver_election_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"mst_others_driver_election_dt",
                                        'filedMode'=>"items.driver_election_dt",
                                        'role' => $role
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                        'filed'=>'medical_checkup_interval_id',
                                         'class'=>'wd-350',
                                        'filedId'=>"mst_others_medical_checkup_interval_id",
                                        'filedMode'=>"items.medical_checkup_interval_id",
                                        'array'=>$listMedicalCheckupInterval
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'employment_insurance_numbers',
                                        'filedId'=>"mst_others_employment_insurance_numbers",
                                        'filedMode'=>"items.employment_insurance_numbers",
                                         'attr_input' => "maxlength='20'",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input',[
                                        'filed'=>'health_insurance_numbers',
                                        'class'=>'w-75',
                                        'filedId'=>"mst_others_health_insurance_numbers",
                                        'filedMode'=>"items.health_insurance_numbers",
                                        'attr_input' => "maxlength='20'",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'employees_pension_insurance_numbers',
                                        'filedId'=>"mst_others_employees_pension_insurance_numbers",
                                        'filedMode'=>"items.employees_pension_insurance_numbers",
                                        'attr_input' => "maxlength='10'",
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.checkbox',[
                                        'filed'=>'workmens_compensation_insurance_fg',
                                        'filedId'=>"mst_others_workmens_compensation_insurance_fg",
                                        'filedMode'=>"items.workmens_compensation_insurance_fg",
                                        'checkboxLabel'=>'あり'
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!--Block 14-->
            @if(in_array(8,$rolesStaffScreen))
            <div class="grid-form ">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_access_authority" role="button" aria-expanded="false" aria-controls="collapseExample">
                        アクセス権限
                    </a>
                </p>
                <div class="collapse" id="b_mst_access_authority">
                    <div class="wrapper-collapse pr-0">
                        <div class="grid-form items-collapse">
                            <div class="row">
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                        'filed'=>'mst_role_id',
                                        'filedId'=>"mst_access_authority_mst_role_id",
                                        'filedMode'=>"items.mst_role_id",
                                        'array'=>@$listRoles,
                                        'attr_input' => 'v-on:change="loadRoleConfig"'
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="wrap-control-group textarea">
                                        <label class="h-100" for="screen_category_id1">
                                            {{ trans(@$prefix.'screen_category_id.1') }}
                                        </label>
                                        <div class="col-md-12 col-sm-12">
                                            ■ 対象の情報
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            @if(@$listStaffScreens)
                                            @foreach($listStaffScreens as $item)
                                                <input type="checkbox" class="form-control" id="info_target_{{$item->id}}" value="{{$item->id}}" v-model="field.mst_staff_auths[1].staffScreen">
                                                <span for="info_target_{{$item->id}}">{{$item->screen_nm}}</span>
                                            @endforeach
                                            @endif
                                            <br/>
                                             <span v-cloak v-if="errors.staffScreen != undefined" class="message-error" v-html="errors.staffScreen.join('<br />')"></span>
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            ■ アクセス許可区分
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            @if(@$listAccessiblePermission)
                                            @foreach($listAccessiblePermission as $key => $value)
                                                <input type="radio" class="form-control" id="staff_access_permission_role_{{$key}}" v-model="field.mst_staff_auths[1].accessible_kb" value="{{$key}}">
                                                <span for="staff_access_permission_role_{{$key}}">{{ $value}}</span>
                                            @endforeach
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    <div class="wrap-control-group textarea">
                                        <label class="h-100" for="screen_category_id2">
                                            {{ trans(@$prefix.'screen_category_id.2') }}
                                        </label>
                                        <div class="col-md-12 col-sm-12">
                                            ■ アクセス許可区分
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            @foreach($listAccessiblePermission as $key => $value)
                                                <input type="radio" class="form-control" id="supplier_access_permission_role_{{$key}}" v-model="field.mst_staff_auths[2].accessible_kb" value="{{$key}}">
                                                <span for="supplier_access_permission_role_{{$key}}">{{ $value}}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    <div class="wrap-control-group textarea">
                                        <label class="h-100" for="screen_category_id3">
                                            {{ trans(@$prefix.'screen_category_id.3') }}
                                        </label>
                                        <div class="col-md-12 col-sm-12">
                                            ■ アクセス許可区分
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            @foreach($listAccessiblePermission as $key => $value)
                                                <input type="radio" class="form-control" id="customer_access_permission_role_{{$key}}" v-model="field.mst_staff_auths[3].accessible_kb" value="{{$key}}">
                                                <span for="customer_access_permission_role_{{$key}}">{{ $value}}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    <div class="wrap-control-group textarea">
                                        <label class="h-100" for="screen_category_id4">
                                            {{ trans(@$prefix.'screen_category_id.4') }}
                                        </label>
                                        <div class="col-md-12 col-sm-12">
                                            ■ アクセス許可区分
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            @foreach($listAccessiblePermission as $key => $value)
                                                <input type="radio" class="form-control" id="vehicle_access_permission_role_{{$key}}" v-model="field.mst_staff_auths[4].accessible_kb" value="{{$key}}">
                                                <span for="vehicle_access_permission_role_{{$key}}">{{ $value}}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!--End-->
            @if($role==2)
            </fieldset>
            @endif
        </form>
        <div class="sub-header mt-3">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                    </div>
                    @if(!empty($staff))
                        <div class="d-flex ml-auto">
                            @if($role==1 && $staff['staff_cd']!=Auth::user()->staff_cd)
                                <button class="btn btn-danger text-white" v-on:click="deleteStaff('{{$staff['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
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
                                    <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.".(!empty($staff) ? "edit":"register")) }}</button>
                                    @if($flagRegisterHistory)
                                        <button class="btn btn-primary btn-submit m-auto" type="button" @click="clone()" >
                                            {{ trans("common.button.register_history_left") }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
@section("scripts")
    <script>
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG06005"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06005'); ?>";
        messages["MSG07002"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG07002'); ?>";
        messages["MSG07001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG07001'); ?>";
        var listRoute = "{{route('staffs.list')}}";
        var auth_staff_id="{{Auth::user()->id}}";
        var info_basic_screen="{{in_array(1,$rolesStaffScreen)}}";
        var dependents_screen="{{in_array(5,$rolesStaffScreen)}}";
        var role = "{{$role}}";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/staffs.js') }}" charset="utf-8"></script>
@endsection
