@extends('Layouts.app')
@section('title',trans("customers.create.title".(!empty($customer) ? "_edit":"")))
@section('title_header',trans("customers.create.title".(!empty($customer) ? "_edit":"")))
@section('content')
    @php $prefix='customers.create.field.' @endphp
    <div class="wrapper-container" id="ctrCustomersVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one d-flex">
                <div class="d-flex">
                    <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                </div>

                <input type="hidden" id="hd_customer_edit" value="{!! !empty($customer) ? 1:0 !!}">
                @if(!empty($customer))
                    @foreach($customer as $key=>$value)
                        <input type="hidden" id="hd_{!! $key !!}" value="{!! $value !!}">
                    @endforeach
                    <div class="d-flex ml-auto">
                        @if($role==1)
                        <button class="btn btn-danger text-white" v-on:click="deleteCustomer('{{$customer['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
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
                            <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.".(!empty($customer) ? "edit":"register")) }}</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @if($role==9 || ($role==2 && empty($customer)))
            <div class="alert alert-danger w-100 mt-2">
                {{\Illuminate\Support\Facades\Lang::get('messages.MSG10006')}}
            </div>
        @endif
        @if($role==1 || ($role==2 && !empty($customer)))
        <form class="form-inline" role="form">
            @if($role==2)
                <fieldset disabled="disabled">
            @endif
            <div class="text-danger">
                {{ trans("common.description-form.indicates_required_items") }}
            </div>
            <!--Block 1-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                            @include('Component.form.input',['class'=>'wd-300','filed'=>'mst_customers_cd','required'=>true,'attr_input' => "maxlength='5'".(!empty($customer) ? 'readonly=""':'')])
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col"></div>
                </div>
            </div>
            <!--Block 2-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',[
                                'filed'=>'customer_nm',
                                'required'=>true,
                                'attr_input' => 'v-on:input="convertKana($event, \'customer_nm_kana\')"  maxlength="200"'
                            ])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'customer_nm_kana','attr_input' => "maxlength='200'" ])
                    </div>

                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',[
                                'filed'=>'customer_nm_formal',
                                'attr_input' => 'v-on:input="convertKana($event, \'customer_nm_kana_formal\')"  maxlength="200"'
                            ])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'customer_nm_kana_formal','attr_input' => "maxlength='200'"])
                    </div>
                </div>
            </div>
            <!--Block 3-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',[
                               'filed'=>'person_in_charge_last_nm',
                               'attr_input' => 'v-on:input="convertKana($event, \'person_in_charge_last_nm_kana\')"  maxlength="25"'
                           ])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'person_in_charge_last_nm_kana','attr_input' => "maxlength='50'"])
                    </div>

                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',[
                               'filed'=>'person_in_charge_first_nm',
                               'attr_input' => 'v-on:input="convertKana($event, \'person_in_charge_first_nm_kana\')"  maxlength="25"'
                           ])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'person_in_charge_first_nm_kana','attr_input' => "maxlength='50'"])
                    </div>
                </div>
            </div>
            <!--Block 4-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-300','filed'=>'zip_cd','attr_input' => "maxlength='7'"])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        <button type="button" class="btn btn-black" v-on:click="getAddrFromZipCode">〒 → 住所</button>
                    </div>

                    <div class="break-row-form"></div>


                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-300','filed'=>'prefectures_cd','array'=>$listPrefecture])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'address1','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="break-row-form"></div>


                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['filed'=>'address2','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['filed'=>'address3','attr_input' => "maxlength='50'"])
                    </div>

                    <div class="break-row-form"></div>
                    <!--phone_number fax_number-->

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'phone_number','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'fax_number','attr_input' => "maxlength='20'"])
                    </div>

                    <div class="break-row-form"></div>
                    <!--hp_url-->
                    <div class="col-md-12 col-sm-12 pd-r-0">
                        @include('Component.form.input',['filed'=>'hp_url','attr_input' => "maxlength='2500'"])
                    </div>

                    <div class="break-row-form"></div>

                    <!--customer_category_id prime_business_office_id-->
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['filed'=>'customer_category_id','array'=>$customer_categories])
                    </div>

                    <div class="col-md-5 col-sm-12 pd-l-20">
                        @include('Component.form.select',['filed'=>'prime_business_office_id','array'=>$business_offices])
                    </div>
                </div>
            </div>
            <!--Block 5-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.textarea',['filed'=>'explanations_bill','attr_input' => "maxlength='100'"])
                    </div>

                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.input',['class'=>'wd-250','filed'=>'bundle_dt','attr_input' => "maxlength='2'"])
                        <div class="break-row-form"></div>
                        <div class="col-md-12 col-sm-12 row grid-col no-padding">
                            <div class="col-md-6 col-sm-12 no-padding">
                                @include('Component.form.select',['class'=>'wd-350','filed'=>'deposit_month_id','array'=>$listDepositMonths])
                            </div>
                            <div class="col-md-6 col-sm-12 pd-l-20">
                                @include('Component.form.input',['class'=>'wd-250','filed'=>'deposit_day','attr_input' => "maxlength='2'"])
                            </div>
                        </div>
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-350','filed'=>'deposit_method_id','array'=>$listDepositMethods])
                        <div class="break-row-form"></div>
                        @include('Component.form.date-picker',['class'=>'wd-350','filed'=>'business_start_dt','role' => $role])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.textarea',['filed'=>'deposit_method_notes','attr_input' => "maxlength='200'"])
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-350','filed'=>'consumption_tax_calc_unit_id','array'=>$listConsumptionTaxCalcUnit])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.select',['class'=>'wd-350','filed'=>'rounding_method_id','array'=>$listRoundingMethod])
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'discount_rate','attr_input' => "maxlength='3'"])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20">
                        @include('Component.form.checkbox',['class'=>'wd-350','filed'=>'except_g_drive_bill_fg','checkboxLabel'=>'あり','attr_input' => "value='1'"])
                    </div>

                </div>
            </div>
            <!--Block 6-->
            <div class="grid-form">
                <p class="header-collapse" >
                    <a data-toggle="collapse" href="#b_mst_bill_issue_destinations" role="button" aria-expanded="false" aria-controls="collapseExample">
                        請求書発行先
                    </a>
                </p>
                <div class="collapse" id="b_mst_bill_issue_destinations">
                    <div class="wrapper-collapse">
                        <div class="grid-form items-collapse" v-for="(items,index) in field.mst_bill_issue_destinations">
                            <div v-cloak class="row">
                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'class'=>'wd-350',
                                        'filed'=>'zip_cd',
                                        'filedId'=>"'mst_bill_issue_destinations_zip_cd'+index",
                                        'filedMode'=>"items.zip_cd",
                                        'filedErrors'=>"mst_bill_issue_destinations",
                                        'attr_input' => "maxlength='7'"
                                    ])
                                </div>
                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    <button type="button" class="btn btn-black" v-on:click="getAddrFromZipCodeCollapse(index)">〒 → 住所</button>
                                </div>

                                <div class="break-row-form"></div>

                                <!--prefectures_cd address1-->

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.select-vue',[
                                        'class'=>'wd-300',
                                        'filed'=>'prefectures_cd',
                                        'filedId'=>"'mst_bill_issue_destinations_prefectures_cd'+index",
                                        'filedMode'=>"items.prefectures_cd",
                                        'array'=>$listPrefecture,
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'address1',
                                        'filedId'=>"'mst_bill_issue_destinations_address1'+index",
                                        'filedMode'=>"items.address1",
                                        'filedErrors'=>"mst_bill_issue_destinations",
                                        'attr_input' => "maxlength='20'"
                                    ])
                                </div>

                                <div class="break-row-form"></div>

                                <!--address2 address3-->

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'address2',
                                        'filedId'=>"'mst_bill_issue_destinations_address2'+index",
                                        'filedMode'=>"items.address2",
                                        'filedErrors'=>"mst_bill_issue_destinations",
                                        'attr_input' => "maxlength='20'"
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input-vue',[
                                        'filed'=>'address3',
                                        'filedId'=>"'mst_bill_issue_destinations_address3'+index",
                                        'filedMode'=>"items.address3",
                                        'filedErrors'=>"mst_bill_issue_destinations",
                                        'attr_input' => "maxlength='50'"
                                    ])
                                </div>

                                <div class="break-row-form"></div>
                                <!--phone_number fax_number-->

                                <div class="col-md-5 col-sm-12">
                                    @include('Component.form.input-vue',[
                                        'class'=>'wd-350',
                                        'filed'=>'phone_number',
                                        'filedId'=>"'mst_bill_issue_destinations_phone_number'+index",
                                        'filedMode'=>"items.phone_number",
                                        'filedErrors'=>"mst_bill_issue_destinations",
                                        'attr_input' => "maxlength='20'"
                                    ])
                                </div>

                                <div class="col-md-7 col-sm-12 pd-l-20">
                                    @include('Component.form.input-vue',[
                                        'class'=>'wd-350',
                                        'filed'=>'fax_number',
                                        'filedId'=>"'mst_bill_issue_destinations_fax_number'+index",
                                        'filedMode'=>"items.fax_number",
                                        'filedErrors'=>"mst_bill_issue_destinations",
                                        'attr_input' => "maxlength='20'"
                                    ])
                                </div>
                            </div>
                            <button @click="removeRows(index)" type="button" class="btn btn-danger btn-rows-remove">-</button>
                        </div>
                        <button @click="addRows" type="button" class="btn btn-primary btn-rows-add">+</button>
                    </div>
                </div>
            </div>
            <!--Block 7-->
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        @include('Component.form.input',['class'=>'wd-350','filed'=>'deposit_bank_cd','attr_input' => "maxlength='4'"])
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.select',['class'=>'wd-350','filed'=>'mst_account_titles_id','array'=>$listAccountTitles])
                    </div>
                    <div class="col-md-7 col-sm-12 pd-l-20 row grid-col">
                        <div class="col-md-6 col-sm-12 no-padding">
                            @include('Component.form.select',['class'=>'wd-350','filed'=>'mst_account_titles_id_2','array'=>$listAccountTitles])
                        </div>

                        <div class="col-md-6 col-sm-12 pd-l-20">
                            @include('Component.form.select',['class'=>'wd-350','filed'=>'mst_account_titles_id_3','array'=>$listAccountTitles])
                        </div>
                    </div>

                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12">
                        @include('Component.form.textarea',['filed'=>'notes','attr_input' => "maxlength='50'"])
                    </div>
                </div>
            </div>
        @if($role==2)
        </fieldset>
        @endif
        </form>
        <div class="sub-header mt-3">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                    </div>

                    <input type="hidden" id="hd_customer_edit" value="{!! !empty($customer) ? 1:0 !!}">
                    @if(!empty($customer))
                        @foreach($customer as $key=>$value)
                            <input type="hidden" id="hd_{!! $key !!}" value="{!! $value !!}">
                        @endforeach
                        <div class="d-flex ml-auto">
                            @if($role==1)
                                <button class="btn btn-danger text-white" v-on:click="deleteCustomer('{{$customer['id']}}')" type="button">{{ trans("common.button.delete") }}</button>
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
                                    <button @click="submit" class="btn btn-primary btn-submit">{{ trans("common.button.".(!empty($customer) ? "edit":"register")) }}</button>
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
        var listRoute = "{{route('customers.list')}}";
        var role = "{{$role}}";
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG07001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG07001'); ?>";
        messages["MSG07002"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG07002'); ?>";
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/customers.js') }}" charset="utf-8"></script>
@endsection
