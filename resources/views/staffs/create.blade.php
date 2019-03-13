@extends('Layouts.app')
@section('title',trans("staffs.create.title"))
@section('title_header',trans("staffs.create.title"))
@section('content')
    @php $prefix='staffs.create.field.' @endphp
    <div class="wrapper-container" id="ctrStaffsVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one">
                <button class="btn btn-black" type="button" onclick="window.history.back();">{{ trans("common.button.back") }}</button>
            </div>
            <div class="sub-header-line-two">
                <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
            </div>
        </div>

        <form class="form-inline" role="form">
            <div class="text-danger">
                {{ trans("common.description-form.indicates_required_items") }}
            </div>
            <!--Block 1-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-300','filed'=>'staff_cd','required'=>true])
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <div class="col-md-6 col-sm-12 no-padding">
                            @include('Component.form.date-picker',['filed'=>'adhibition_start_dt','required'=>true])
                        </div>
                        <div class="col-md-6 col-sm-12 pd-l-20">
                            @include('Component.form.input',['filed'=>'adhibition_end_dt','attr_input' => 'readonly="" value="2999/12/31"' ])
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'password','class'=>'w-100','required'=>true,'attr_input'=>"type='password' class='w-100'"])
                    </div>
                </div>
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
                             'required'=>true,
                             'attr_input' => 'v-on:input="convertKana($event, \'last_nm_kana\')" v-on:blur="onBlur"'
                         ])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',[
                              'filed'=>'first_nm',
                              'required'=>true,
                              'attr_input' => 'v-on:input="convertKana($event, \'first_nm_kana\')" v-on:blur="onBlur"'
                          ])
                    </div>

                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'last_nm_kana'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'first_nm_kana'])
                    </div>
                </div>
            </div>
            <!--Block 4-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-250','filed'=>'zip_cd'])
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
                        @include('Component.form.input',['filed'=>'address1'])
                    </div>

                    <div class="break-row-form"></div>

                    <!--address2 address3-->

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'address2'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'address3'])
                    </div>

                    <div class="break-row-form"></div>
                    <!--phone_number fax_number-->

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'landline_phone_number'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'cellular_phone_number'])
                    </div>

                    <div class="break-row-form"></div>

                    <!--customer_category_id prime_business_office_id-->
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'corp_cellular_phone_number'])
                    </div>

                    <div class="col-md-5 col-sm-12 pd-l-20">
                        @include('Component.form.textarea',['filed'=>'notes'])
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
                        @include('Component.form.date-picker',['class'=>'wd-350','filed'=>'birthday'])
                    </div>

                </div>
            </div>
            <!--Block 6-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.date-picker',['class'=>'wd-350','filed'=>'enter_date'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.date-picker',['class'=>'wd-350','filed'=>'retire_date'])
                    </div>

                </div>
            </div>
            <!--Block 7-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-300','filed'=>'insurer_number'])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'basic_pension_number'])
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'person_insured_number'])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'health_insurance_class'])
                    </div>

                    <div class="break-row-form"></div>
                    <div class="break-row-form"></div>
                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'welfare_annuity_class'])
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <div class="col-md-6 col-sm-12 no-padding">
                            <label class="grid-form-label pl-150">コード</label>
                            @include('Component.form.select',['class'=>'wd-300','filed'=>'relocation_municipal_office_cd'])
                        </div>
                        <div class="col-md-6 col-sm-12 pd-l-20">
                            <label class="grid-form-label ">名称</label>
                            <input v-model="field.relocation_municipal_office_cd" name="relocation_municipal_office_cd" type="text" class="form-control" id="relocation_municipal_office_cd">
                        </div>
                    </div>
                </div>
            </div>
            <!--Block 8-->
            <div class="grid-form">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_bill_issue_destinations" role="button" aria-expanded="false" aria-controls="collapseExample">
                        学歴
                    </a>
                </p>
                <div class="collapse" id="b_mst_bill_issue_destinations">
                    <div class="wrapper-collapse pr-0">
                        <div class="grid-form items-collapse">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.input',['class'=>'w-75','filed'=>'educational_background'])
                                </div>

                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',['filed'=>'educational_background_dt','class'=>'wd-350'])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Block 9-->
            <div class="grid-form">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_staff_job_experiences" role="button" aria-expanded="false" aria-controls="collapseExample">
                        前職経歴
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
                                    ])
                                </div>
                                <div class="break-row-form"></div>


                                <!--address2 address3-->

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'staff_tenure_start_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_bill_issue_destinations_staff_tenure_start_dt'+index",
                                        'filedMode'=>"items.staff_tenure_start_dt",
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'staff_tenure_end_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_bill_issue_destinations_staff_tenure_start_dt'+index",
                                        'filedMode'=>"items.staff_tenure_end_dt",
                                    ])
                                </div>

                            </div>
                            <button @click="removeRows('mst_staff_job_experiences',index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows('mst_staff_job_experiences')" type="button" class="btn btn-primary btn-rows-add">+</button>
                    </div>
                </div>
            </div>
            <!--Block 10-->
            <div class="grid-form">
                <p class="header-collapse">
                    <a data-toggle="collapse" href="#b_mst_staff_qualifications" role="button" aria-expanded="false" aria-controls="collapseExample">
                        資格
                    </a>
                </p>
                <div class="collapse" id="b_mst_staff_qualifications">
                    <div class="wrapper-collapse">
                        <div class="grid-form items-collapse" v-for="(items,index) in field.mst_staff_qualifications">
                            <div v-cloak class="row">
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'qualification_kind_id',
                                        'filedId'=>"'mst_staff_qualifications_qualification_kind_id'+index",
                                        'filedMode'=>"items.qualification_kind_id",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'acquisition_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_qualifications_acquisition_dt'+index",
                                        'filedMode'=>"items.acquisition_dt",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <!--address2 address3-->

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'period_validity_start_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_qualifications_period_validity_start_dt'+index",
                                        'filedMode'=>"items.period_validity_start_dt",
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'period_validity_end_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_qualifications_period_validity_end_dt'+index",
                                        'filedMode'=>"items.period_validity_end_dt",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.textarea',[
                                        'filed'=>'qualification_notes',
                                        'filedId'=>"'mst_staff_qualifications_qualification_notes'+index",
                                        'filedMode'=>"items.qualification_notes",
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'amounts',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_qualifications_amounts'+index",
                                        'filedMode'=>"items.amounts",
                                    ])
                                    <div class="break-row-form"></div>
                                    <div v-if="index!=0">
                                        @include('Component.form.input-vue',[
                                            'filed'=>'payday',
                                            'class'=>'wd-350',
                                            'filedId'=>"'mst_staff_qualifications_payday'+index",
                                            'filedMode'=>"items.payday",
                                         ])
                                    </div>
                                </div>

                            </div>
                            <button @click="removeRows('mst_staff_qualifications',index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows('mst_staff_qualifications')" type="button" class="btn btn-primary btn-rows-add">+</button>
                    </div>
                </div>
            </div>
            <!--Block 11-->
            <div class="grid-form">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_staff_dependents" role="button" aria-expanded="false" aria-controls="collapseExample">
                        扶養者
                    </a>
                </p>
                <div class="collapse" id="b_mst_staff_dependents">
                    <div class="wrapper-collapse">
                        <div class="grid-form items-collapse" v-for="(items,index) in field.mst_staff_dependents">
                            <div v-cloak class="row">
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'dependent_kb',
                                         'class'=>'wd-300',
                                        'filedId'=>"'mst_staff_dependents_dependent_kb'+index",
                                        'filedMode'=>"items.dependent_kb",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'mst_staff_dependents.last_nm',
                                        'filedId'=>"'mst_staff_dependents_last_nm'+index",
                                        'filedMode'=>"items.last_nm",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'mst_staff_dependents.last_nm_kana',
                                        'filedId'=>"'mst_staff_dependents_last_nm_kana'+index",
                                        'filedMode'=>"items.last_nm_kana",
                                    ])
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'mst_staff_dependents.first_nm',
                                        'filedId'=>"'mst_staff_dependents_first_nm'+index",
                                        'filedMode'=>"items.first_nm",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'mst_staff_dependents.first_nm_kana',
                                        'filedId'=>"'mst_staff_dependents_first_nm_kana'+index",
                                        'filedMode'=>"items.first_nm_kana",
                                    ])
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'mst_staff_dependents.birthday',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_dependents_birthday'+index",
                                        'filedMode'=>"items.birthday",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select-vue',[
                                        'filed'=>'mst_staff_dependents.sex_id',
                                        'class'=>'wd-300',
                                        'filedId'=>"'mst_staff_dependents_sex_id'+index",
                                        'filedMode'=>"items.sex_id",
                                    ])
                                </div>
                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'mst_staff_dependents.social_security_number',
                                        'class'=>'wd-350',
                                        'filedId'=>"'mst_staff_dependents_social_security_number'+index",
                                        'filedMode'=>"items.social_security_number",
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
            <!--Block 12-->
            <div class="grid-form">
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
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                       'filed'=>'drivers_license_color_id',
                                        'class'=>'wd-350',
                                       'filedId'=>"mst_staff_driver_license_color_id",
                                       'filedMode'=>"items.drivers_license_color_id",
                                   ])
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'drivers_license_issued_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"mst_staff_driver_license_issued_dt",
                                        'filedMode'=>"items.drivers_license_issued_dt",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'drivers_license_period_validity',
                                         'class'=>'wd-350',
                                        'filedId'=>"mst_staff_drivers_license_period_validity",
                                        'filedMode'=>"items.drivers_license_period_validity",
                                    ])
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-12 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'drivers_license_picture',
                                        'filedId'=>"mst_staff_drivers_license_picture",
                                        'filedMode'=>"items.drivers_license_picture",
                                    ])
                                    <div class="wrap-control-group">
                                        <button type="file" class="btn btn-secondary float-left">{{trans($prefix.'btn_browse_license_picture')}}</button>
                                        <button type="file" class="btn btn-dark float-right">{{trans($prefix.'btn_delete_file_license_picture')}}</button>
                                    </div>
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_1',
                                         'filedId'=>"mst_staff_drivers_license_divisions_1",
                                         'filedMode'=>"items.drivers_license_divisions_1",
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_2',
                                         'filedId'=>"mst_staff_drivers_license_divisions_2",
                                         'filedMode'=>"items.drivers_license_divisions_2",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_3',
                                         'filedId'=>"mst_staff_drivers_license_divisions_3",
                                         'filedMode'=>"items.drivers_license_divisions_3",
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_4',
                                         'filedId'=>"mst_staff_drivers_license_divisions_4",
                                         'filedMode'=>"items.drivers_license_divisions_4",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_5',
                                         'filedId'=>"mst_staff_drivers_license_divisions_5",
                                         'filedMode'=>"items.drivers_license_divisions_5",
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_6',
                                         'filedId'=>"mst_staff_drivers_license_divisions_6",
                                         'filedMode'=>"items.drivers_license_divisions_6",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_7',
                                         'filedId'=>"mst_staff_drivers_license_divisions_7",
                                         'filedMode'=>"items.drivers_license_divisions_7",
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_8',
                                         'filedId'=>"mst_staff_drivers_license_divisions_8",
                                         'filedMode'=>"items.drivers_license_divisions_8",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_9',
                                         'filedId'=>"mst_staff_drivers_license_divisions_9",
                                         'filedMode'=>"items.drivers_license_divisions_9",
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_10',
                                         'filedId'=>"mst_staff_drivers_license_divisions_10",
                                         'filedMode'=>"items.drivers_license_divisions_10",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_11',
                                         'filedId'=>"mst_staff_drivers_license_divisions_11",
                                         'filedMode'=>"items.drivers_license_divisions_11",
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_12',
                                         'filedId'=>"mst_staff_drivers_license_divisions_12",
                                         'filedMode'=>"items.drivers_license_divisions_12",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_13',
                                         'filedId'=>"mst_staff_drivers_license_divisions_13",
                                         'filedMode'=>"items.drivers_license_divisions_13",
                                     ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                         'filed'=>'drivers_license_divisions_14',
                                         'filedId'=>"mst_staff_drivers_license_divisions_14",
                                         'filedMode'=>"items.drivers_license_divisions_14",
                                    ])
                                </div>
                                <!--address2 address3-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Block 13-->
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
                                    @include('Component.form.input',[
                                        'filed'=>'retire_reasons',
                                        'filedId'=>"mst_others_retire_reasons",
                                        'filedMode'=>"items.retire_reasons",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                       'filed'=>'retire_dt',
                                        'class'=>'wd-350',
                                       'filedId'=>"mst_others_retire_dt",
                                       'filedMode'=>"items.retire_dt",
                                   ])
                                </div>

                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'death_reasons',
                                        'filedId'=>"mst_others_death_reasons",
                                        'filedMode'=>"items.death_reasons",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'death_dt',
                                         'class'=>'wd-350',
                                        'filedId'=>"mst_others_death_dt",
                                        'filedMode'=>"items.death_dt",
                                    ])
                                </div>
                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                        'filed'=>'belong_company_id',
                                        'filedId'=>"mst_others_belong_company_id",
                                        'filedMode'=>"items.belong_company_id",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                        'filed'=>'occupation_id',
                                        'class'=>'w-75',
                                        'filedId'=>"mst_others_occupation_id",
                                        'filedMode'=>"items.occupation_id",
                                    ])
                                </div>
                                <div class="break-row-form"></div>

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select',[
                                        'filed'=>'mst_business_office_id',
                                        'filedId'=>"mst_others_mst_business_office_id",
                                        'filedMode'=>"items.mst_business_office_id",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                        'filed'=>'depertment_id',
                                        'class'=>'w-75',
                                        'filedId'=>"mst_others_depertment_id",
                                        'filedMode'=>"items.depertment_id",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.date-picker',[
                                        'filed'=>'driver_election_dt',
                                        'class'=>'wd-350',
                                        'filedId'=>"mst_others_driver_election_dt",
                                        'filedMode'=>"items.driver_election_dt",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.select',[
                                        'filed'=>'medical_checkup_interval_id',
                                         'class'=>'wd-350',
                                        'filedId'=>"mst_others_medical_checkup_interval_id",
                                        'filedMode'=>"items.medical_checkup_interval_id",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'employment_insurance_numbers',
                                        'filedId'=>"mst_others_employment_insurance_numbers",
                                        'filedMode'=>"items.employment_insurance_numbers",
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input',[
                                        'filed'=>'health_insurance_numbers',
                                        'class'=>'w-75',
                                        'filedId'=>"mst_others_health_insurance_numbers",
                                        'filedMode'=>"items.health_insurance_numbers",
                                    ])
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input',[
                                        'filed'=>'employees_pension_insurance_numbers',
                                        'filedId'=>"mst_others_employees_pension_insurance_numbers",
                                        'filedMode'=>"items.employees_pension_insurance_numbers",
                                    ])
                                </div>

                                <div class="break-row-form"></div>
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.checkbox',[
                                        'filed'=>'admin_fg',
                                        'filedId'=>"mst_others_admin_fg",
                                        'filedMode'=>"items.admin_fg",
                                        'checkboxLabel'=>'管理者である'
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Block 14-->
            <div class="grid-form">
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
                                            <input type="checkbox" class="form-control" id="info_target_1">
                                            <span for="info_target.1">{{ trans(@$prefix."info_target.1") }}</span>
                                            <input type="checkbox" class="form-control" id="info_target_2">
                                            <span for="info_target.2">{{ trans(@$prefix."info_target.2") }}</span>
                                            <input type="checkbox" class="form-control" id="info_target_3">
                                            <span for="info_target.3">{{ trans(@$prefix."info_target.3") }}</span>
                                            <input type="checkbox" class="form-control" id="info_target_4">
                                            <span for="info_target.4">{{ trans(@$prefix."info_target.4") }}</span>
                                            <input type="checkbox" class="form-control" id="info_target_5">
                                            <span for="info_target.5">{{ trans(@$prefix."info_target.5") }}</span>
                                            <input type="checkbox" class="form-control" id="info_target_6">
                                            <span for="info_target.6">{{ trans(@$prefix."info_target.6") }}</span>
                                            <input type="checkbox" class="form-control" id="info_target_7">
                                            <span for="info_target.7">{{ trans(@$prefix."info_target.7") }}</span>
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            ■ アクセス許可区分
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_1">
                                            <span for="access_permission_role.1">{{ trans(@$prefix."access_permission_role.1") }}</span>
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_2">
                                            <span for="access_permission_role.2">{{ trans(@$prefix."access_permission_role.2") }}</span>
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_3">
                                            <span for="access_permission_role.3">{{ trans(@$prefix."access_permission_role.3") }}</span>
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
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_1">
                                            <span for="access_permission_role.1">{{ trans(@$prefix."access_permission_role.1") }}</span>
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_2">
                                            <span for="access_permission_role.2">{{ trans(@$prefix."access_permission_role.2") }}</span>
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_3">
                                            <span for="access_permission_role.3">{{ trans(@$prefix."access_permission_role.3") }}</span>
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
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_1">
                                            <span for="access_permission_role.1">{{ trans(@$prefix."access_permission_role.1") }}</span>
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_2">
                                            <span for="access_permission_role.2">{{ trans(@$prefix."access_permission_role.2") }}</span>
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_3">
                                            <span for="access_permission_role.3">{{ trans(@$prefix."access_permission_role.3") }}</span>
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
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_1">
                                            <span for="access_permission_role.1">{{ trans(@$prefix."access_permission_role.1") }}</span>
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_2">
                                            <span for="access_permission_role.2">{{ trans(@$prefix."access_permission_role.2") }}</span>
                                            <input type="radio" name="access_permission_role" class="form-control" id="access_permission_role_3">
                                            <span for="access_permission_role.3">{{ trans(@$prefix."access_permission_role.3") }}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End-->

        </form>
    </div>
@endsection
@section("scripts")
    <script>
        var listRoute = "{{route('staffs.list')}}";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/staffs.js') }}" charset="utf-8"></script>
@endsection