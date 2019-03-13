@extends('Layouts.app')
@section('title',trans("staffs.create.title"))
@section('title_header',trans("staffs.create.title"))
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    @php $table='staffs' @endphp
    <div class="wrapper-container" id="ctrStaffsVl">
        <div class="sub-header">
            <div class="sub-header-line-one">
                <button class="btn btn-black" type="button" onclick="window.history.back();">{{ trans("common.button.back") }}</button>
            </div>
            <div class="sub-header-line-two">
                <button class="btn btn-primary btn-submit">{{ trans("common.button.register") }}</button>
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
                            @include('Component.form.input',['filed'=>'adhibition_start_dt','attr_input' => 'readonly="" value="2999/12/31"' ])
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'password','required'=>true])
                    </div>
                </div>
            </div>
            <!--Block 2-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-300','filed'=>'employment_pattern_id','array'=>$listEmploymentPattern])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.select',['class'=>'w-75','filed'=>'position_id','array'=>[]])
                    </div>

                </div>
            </div>
            <!--Block 3-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'last_nm'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'first_nm'])
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
                        <button type="button" class="btn btn-black" v-on:click="getAddrFromZipCode">〒 → 住所</button>
                    </div>

                    <div class="break-row-form"></div>

                    <!--prefectures_cd address1-->

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-300','filed'=>'prefectures_cd','array'=>[]])
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
                        @include('Component.form.select',['class'=>'wd-300','filed'=>'sex_id','array'=>[]])
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
                            <label class="grid-form-label pl-5">コード</label>
                            @include('Component.form.input',['class'=>'wd-300','filed'=>'relocation_municipal_office_cd'])
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
                                        @include('Component.form.input',['filed'=>'educational_background_dt'])
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
                            <button @click="removeRows(index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows" type="button" class="btn btn-primary btn-rows-add">+</button>
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
                            <button @click="removeRows(index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows" type="button" class="btn btn-primary btn-rows-add">+</button>
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
                            <button @click="removeRows(index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows" type="button" class="btn btn-primary btn-rows-add">+</button>
                    </div>
                </div>
            </div>
            <!--Block 12-->
            <div class="grid-form">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_staff" role="button" aria-expanded="false" aria-controls="collapseExample">
                        運転免許証
                    </a>
                </p>
                <div class="collapse" id="b_mst_staff">
                    <div class="wrapper-collapse">
                        <div class="grid-form items-collapse" v-for="(items,index) in field.mst_staff">
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
                            <button @click="removeRows(index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows" type="button" class="btn btn-primary btn-rows-add">+</button>
                    </div>
                </div>
            </div>

            <!--Block last-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-350','filed'=>'deposit_bank_cd','array'=>[]])
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-350','filed'=>'mst_account_titles_id','array'=>[]])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20 row grid-col">
                        <div class="col-md-6 col-sm-12 no-padding">
                            @include('Component.form.select',['class'=>'wd-350','filed'=>'mst_account_titles_id_2','array'=>[]])
                        </div>

                        <div class="col-md-6 col-sm-12 pd-l-20">
                            @include('Component.form.select',['class'=>'wd-350','filed'=>'mst_account_titles_id_3','array'=>[]])
                        </div>
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.textarea',['filed'=>'notes'])
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section("scripts")
    <script>
        var listRoute = "{{route('staffs.list')}}";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/staffs.js') }}" charset="utf-8"></script>
@endsection