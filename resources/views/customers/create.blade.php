@extends('Layouts.app')
@section('title',trans("customers.create.title"))
@section('title_header',trans("customers.create.title"))
@section('content')
    <div class="wrapper-container" id="ctrCustomersVl">
        <div class="sub-header">
            <div class="sub-header-line-one">
                <button class="btn btn-black">{{ trans("common.button.back") }}</button>
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
                        @include('Component.form.input',['class'=>'wd-300','filed'=>'mst_customers_cd','required'=>true])
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
            </div>
            <!--Block 2-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'customer_nm','required'=>true])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'customer_nm_kana'])
                    </div>

                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'customer_nm_formal'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'customer_nm_kana_formal'])
                    </div>
                </div>
            </div>
            <!--Block 3-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'person_in_charge_last_nm'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'person_in_charge_last_nm_kana'])
                    </div>

                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'person_in_charge_first_nm'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'person_in_charge_first_nm_kana'])
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
                        <button type="button" class="btn btn-black">〒 → 住所</button>
                    </div>

                    <div class="break-row-form"></div>

                    <!--prefectures_cd address1-->

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-300','filed'=>'prefectures_cd','array'=>[""=>"select",'text','text2']])
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
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'phone_number'])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'fax_number'])
                    </div>

                    <div class="break-row-form"></div>
                    <!--hp_url-->
                    <div class="col-md-12 col-sm-12 pd-r-0">
                        @include('Component.form.input',['filed'=>'hp_url'])
                    </div>

                    <div class="break-row-form"></div>

                    <!--customer_category_id prime_business_office_id-->
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['filed'=>'customer_category_id','array'=>[""=>"select",'text','text2']])
                    </div>

                    <div class="col-md-5 col-sm-12 pd-l-20">
                        @include('Component.form.select',['filed'=>'prime_business_office_id','array'=>[""=>"select",'text','text2']])
                    </div>

                </div>
            </div>
            <!--Block 5-->
        </form>
    </div>
@endsection
@section("scripts")
    <script type="text/javascript" src="{{ mix('/assets/js/controller/customers.js') }}" charset="utf-8"></script>
@endsection
