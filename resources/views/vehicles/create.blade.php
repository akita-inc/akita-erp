@extends('Layouts.app')
@section('title',!empty($mVehicle) ? '車両　修正画面' : '車両　新規追加')
@section('title_header',!empty($mVehicle) ? '車両　修正画面' : '車両　新規追加')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    @php $prefix='vehicles.create.field.' @endphp
    <div class="row row-xs" id="ctrVehiclesVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <form class="form-inline w-100" role="form" method="post" id="form1" >
            @csrf
            <div class="sub-header">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black" type="button" @click="backHistory">{{ trans("common.button.back") }}</button>
                    </div>
                    <input type="hidden" id="hd_adhibition_end_dt_default" value="{!! config('params.adhibition_end_dt_default') !!}">
                    <input type="hidden" id="hd_vehicle_edit" value="{!! !empty($mVehicle) ? 1:0 !!}">
                    @if(!empty($mVehicle))
                        @foreach($mVehicle as $key=>$value)
                            <input type="hidden" id="hd_{!! $key !!}" value="{!! $value !!}">
                        @endforeach
                        <div class="d-flex ml-auto">
                            @if($role==1)
                                <button class="btn btn-danger text-white" v-on:click='deleteVehicle("{{$mVehicle['id']}}")' type="button">{{ trans("common.button.delete") }}</button>
                            @endif
                        </div>
                    @endif
                </div>
                @if(!empty($mVehicle) && $role==1)
                    <div class="grid-form border-0">
                        <div class="row">
                            <div class="col-md-5 col-sm-12 row grid-col h-100"></div>
                            <div class="col-md-7 col-sm-12 row grid-col h-100">
                                <button class="btn btn-primary btn-submit" type="button" @click="submit('edit')">{{ trans("common.button.edit") }}</button>
                                @if($flagLasted)
                                    <button class="btn btn-primary btn-submit m-auto" type="button" @click="submit('registerHistoryLeft')" >{{ trans("common.button.register_history_left") }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($role==1)
                    <div class="sub-header-line-two">
                        <button class="btn btn-primary btn-submit" type="button" @click="submit(null)">{{ trans("common.button.register") }}</button>
                    </div>
                @endif
            </div>
            @if($role==9 || ($role==2 && empty($mVehicle)))
                <div class="alert alert-danger w-100 mt-2">
                    {{\Illuminate\Support\Facades\Lang::get('messages.MSG10006')}}
                </div>
            @endif
            @if($role==1 || ($role==2 && !empty($mVehicle)))
                @if($role==2)
                    <fieldset disabled="disabled">
                @endif
            <div class="text-danger w-100">*　は必須入力の項目です。</div>
            <div class="w-100 alert-area">
                @include('Layouts.alert')
            </div>
            @if(!empty($mVehicle))
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="vehicles_cd">車両コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.vehicles_cd != undefined ? 'is-invalid':'' " name="vehicles_cd" id="vehicles_cd" readonly maxlength="10" v-model="field.vehicles_cd">
                        </div>
                        <span class="note">
                            ※編集中データをもとに、新しい適用期間のデータを作成したい場合は、適用開始日（新規用）を入力し、新規登録（履歴残し）ボタンを押してください。
                        </span>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicles_cd!= undefined" v-html="errors.vehicles_cd[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row  h-100">
                        <div class="col row grid-col h-100">
                            <label class="col-7 required" for="adhibition_start_dt_old">適用開始日（更新用）</label>
                            <div class="col-5 wrap-control">
                                <date-picker format="YYYY/MM/DD"
                                             placeholder=""
                                             v-model="field.adhibition_start_dt" v-cloak=""
                                             :lang="lang"
                                             :input-class="errors.adhibition_start_dt != undefined ? 'form-control w-100 is-invalid':'form-control w-100' "
                                             :value-type="'format'"
                                             :input-name="'adhibition_start_dt'"
                                             v-on:change="getListStaff"
                                             @if($role!=1) :disabled="true" @endif
                                >
                                </date-picker>
                            </div>
                            <span class="message-error w-100 grid-col" role="alert" v-cloak v-if="errors.adhibition_start_dt!= undefined" v-html="errors.adhibition_start_dt[0]"></span>
                        </div>
                        <div class="col row grid-col h-100">
                            <label class="col-7" for="adhibition_end_dt">適用終了日（更新用）</label>
                            <div class="col-5 wrap-control">
                                @if($flagLasted)
                                    <date-picker format="YYYY/MM/DD"
                                                 placeholder=""
                                                 v-model="field.adhibition_end_dt" v-cloak=""
                                                 :lang="lang"
                                                 :input-class="errors.adhibition_end_dt != undefined ? 'form-control w-100 is-invalid':'form-control w-100' "
                                                 :value-type="'format'"
                                                 :input-name="'adhibition_end_dt'"
                                                 @if($role!=1) :disabled="true" @endif
                                    >
                                    </date-picker>
                                    <input type="hidden" id="adhibition_end_dt" >
                                @else
                                    <input type="text" readonly class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ str_replace('-', '/',  $mVehicle['adhibition_end_dt'] ?? config('params.adhibition_end_dt_default'))}}" >
                                @endif
                            </div>
                            <span class="message-error w-100" role="alert" v-cloak v-if="errors.adhibition_end_dt!= undefined" v-html="errors.adhibition_end_dt[0]"></span>
                        </div>
                        @if($flagLasted)
                            <div class="break-row-form"></div>
                            <div class="col row grid-col h-100">
                                <label class="col-7 required" for="adhibition_start_dt">適用開始日（新規用）</label>
                                <div class="col-5 wrap-control">
                                    <date-picker format="YYYY/MM/DD"
                                                 placeholder=""
                                                 v-model="field.adhibition_start_dt_new" v-cloak=""
                                                 :lang="lang"
                                                 :input-class="errors.adhibition_start_dt_new != undefined ? 'form-control w-100 is-invalid':'form-control w-100' "
                                                 :value-type="'format'"
                                                 :input-name="'adhibition_start_dt_new'"
                                                 @if($role!=1) :disabled="true" @endif
                                    >
                                    </date-picker>
                                    <input type="hidden" id="adhibition_start_dt_new" value="{{ old('adhibition_start_dt_new') }}" >
                                </div>
                            </div>
                            <div class="col row grid-col h-100">
                                <label class="col-7" for="adhibition_end_dt_new">適用終了日（新規用）</label>
                                <div class="col-5 wrap-control">
                                    <input type="text" readonly class="form-control" id="adhibition_end_dt_new" name="adhibition_end_dt_new" v-model="field.adhibition_end_dt_new">
                                </div>
                            </div>
                        @endif
                        <span class="message-error w-100 grid-col" role="alert" v-cloak v-if="errors.adhibition_start_dt_new!= undefined" v-html="errors.adhibition_start_dt_new[0]"></span>
                    </div>
                </div>
            </div>
            @else
                <div class="grid-form">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 row grid-col h-100">
                            <label class="col-md-5 col-sm-5 required" for="vehicles_cd">車両コード</label>
                            <div class="col-md-7 col-sm-7 wrap-control">
                                <input type="text" class="form-control w-50" v-bind:class="errors.vehicles_cd != undefined ? 'is-invalid':'' " name="vehicles_cd" id="vehicles_cd" maxlength="10" v-model="field.vehicles_cd">
                            </div>
                            <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicles_cd!= undefined" v-html="errors.vehicles_cd[0]"></span>
                            </span>
                        </div>
                        <div class="col-md-7 col-sm-12 row  h-100">
                            <div class="col row grid-col h-100">
                                <label class="col-7 required" for="adhibition_start_dt">適用開始日</label>
                                <div class="col-5 wrap-control">
                                    <date-picker format="YYYY/MM/DD"
                                                 placeholder=""
                                                 v-model="field.adhibition_start_dt" v-cloak=""
                                                 :lang="lang"
                                                 :input-class="errors.adhibition_start_dt!= undefined ? 'form-control w-100 is-invalid':'form-control w-100' "
                                                 :value-type="'format'"
                                                 :input-name="'adhibition_start_dt'"
                                                 v-on:change="getListStaff"
                                                 @if($role!=1) :disabled="true" @endif
                                    >
                                    </date-picker>
                                </div>
                            </div>
                            <div class="col row grid-col h-100">
                                <label class="col-7" for="adhibition_end_dt">適用終了日</label>
                                <div class="col-5 wrap-control">
                                    <input type="text" readonly class="form-control" id="adhibition_end_dt" name="" v-model="field.adhibition_end_dt">
                                </div>
                            </div>
                            <span class="message-error w-100 grid-col" role="alert" v-cloak v-if="errors.adhibition_start_dt!= undefined" v-html="errors.adhibition_start_dt[0]"></span>
                        </div>
                    </div>
                </div>
            @endif
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="door_number">ドア番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.door_number != undefined ? 'is-invalid':'' " id="door_number" name="door_number" v-model="field.door_number" maxlength="10">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.door_number!= undefined" v-html="errors.door_number[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicles_kb">車両区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicles_kb" name="vehicles_kb" v-model="field.vehicles_kb">
                                @foreach($listVehicleKb as $key => $value)
                                <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicles_kb!= undefined" v-html="errors.vehicles_kb[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="registration_numbers">自動車登録番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.registration_numbers != undefined ? 'is-invalid':'' " id="registration_numbers" name="registration_numbers" v-model="field.registration_numbers" maxlength="50" >
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.registration_numbers!= undefined" v-html="errors.registration_numbers[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4 required" for="mst_business_office_id">営業所</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="mst_business_office_id" name="mst_business_office_id" v-model="field.mst_business_office_id">
                                @foreach($listBusinessOffices as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.mst_business_office_id!= undefined" v-html="errors.mst_business_office_id[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_size_kb">小中大区分</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="vehicle_size_kb" name="vehicle_size_kb" v-model="field.vehicle_size_kb">
                                @foreach($listVehicleSize as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_size_kb!= undefined" v-html="errors.vehicle_size_kb[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_purpose_id">用途</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicle_purpose_id" name="vehicle_purpose_id" v-model="field.vehicle_purpose_id">
                                @foreach($listVehiclePurpose as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_purpose_id!= undefined" v-html="errors.vehicle_purpose_id[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="land_transport_office_cd">陸運支局</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-100" id="land_transport_office_cd" name="land_transport_office_cd" v-model="field.land_transport_office_cd">
                                @foreach($listLandTranportOfficeCd as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.land_transport_office_cd!= undefined" v-html="errors.land_transport_office_cd[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5 label-input-file" v-bind:class="field.vehicle_inspection_sticker_pdf ? 'pr-0' : ''" for="vehicle_inspection_sticker_pdf">車検証PDF<span v-if="field.vehicle_inspection_sticker_pdf">（１ファイル）</span></label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="vehicle_inspection_sticker_pdf" name="vehicle_inspection_sticker_pdf" class="input-file" v-on:change="onFileChange($event,'vehicle_inspection_sticker_pdf')" accept="application/pdf" ref="vehicle_inspection_sticker_pdf">
                                <span id="vehicle_inspection_sticker_pdf_file_name" class="w-100 form-control {{$role==2 ? 'disabled' : ''}}" v-cloak>@{{ field.vehicle_inspection_sticker_pdf }}</span>

                            </div>
                            <div class="d-flex">
                                <label for="vehicle_inspection_sticker_pdf" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                @if(!empty($mVehicle["vehicle_inspection_sticker_pdf"]))
                                    <modal-viewer-file
                                        :header="'{{$mVehicle["vehicle_inspection_sticker_pdf"]}}'"
                                        :path="'{{\App\Helpers\Common::getPathVehicles($mVehicle,"vehicle_inspection_sticker_pdf")}}'"
                                    >
                                        プレビュー
                                    </modal-viewer-file>
                                @endif
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" @click="deleteFileUpload($event,'vehicle_inspection_sticker_pdf')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_inspection_sticker_pdf!= undefined" v-html="errors.vehicle_inspection_sticker_pdf[0]"></span>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="registration_dt">自動車登録年月日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="field.registration_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'registration_dt'"
                                         @if($role!=1) :disabled="true" @endif
                            >
                            </date-picker>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="first_year_registration_dt">初年度登録年月</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25" v-bind:class="errors.first_year_registration_dt != undefined ? 'is-invalid':'' " id="first_year_registration_dt" name="first_year_registration_dt" v-model="field.first_year_registration_dt" maxlength="6">
                            ※例：201903など
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.first_year_registration_dt!= undefined" v-html="errors.first_year_registration_dt[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_classification_id">自動車種別</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="vehicle_classification_id" name="vehicle_classification_id" v-model="field.vehicle_classification_id">
                                @foreach($listVehicleClassification as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_classification_id!= undefined" v-html="errors.vehicle_classification_id[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="private_commercial_id">自家用・事業用の別</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="private_commercial_id" name="private_commercial_id" v-model="field.private_commercial_id">
                                @foreach($listPrivateCommercial as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.private_commercial_id!= undefined" v-html="errors.private_commercial_id[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="car_body_shape_id">車体の形状</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="car_body_shape_id" name="car_body_shape_id" v-model="field.car_body_shape_id">
                                @foreach($listCarBodyShape as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.car_body_shape_id!= undefined" v-html="errors.car_body_shape_id[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_id">車名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicle_id" name="vehicle_id" v-model="field.vehicle_id">
                                @foreach($listVehicle as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_id!= undefined" v-html="errors.vehicle_id[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="seating_capacity">定員</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.seating_capacity != undefined ? 'is-invalid':'' " id="seating_capacity" name="seating_capacity" v-model="field.seating_capacity" maxlength="2">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.seating_capacity!= undefined" v-html="errors.seating_capacity[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="max_loading_capacity">最大積載量（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.max_loading_capacity != undefined ? 'is-invalid':'' " id="max_loading_capacity" name="max_loading_capacity" v-model="field.max_loading_capacity" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.max_loading_capacity!= undefined" v-html="errors.max_loading_capacity[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_body_weights">車両重量（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.vehicle_body_weights != undefined ? 'is-invalid':'' " id="vehicle_body_weights" name="vehicle_body_weights" v-model="field.vehicle_body_weights" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_body_weights!= undefined" v-html="errors.vehicle_body_weights[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_total_weights">車両総重量（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.vehicle_total_weights != undefined ? 'is-invalid':'' " id="vehicle_total_weights" name="vehicle_total_weights" v-model="field.vehicle_total_weights" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_total_weights!= undefined" v-html="errors.vehicle_total_weights[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="frame_numbers">車台番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.frame_numbers != undefined ? 'is-invalid':'' " id="frame_numbers" name="frame_numbers" v-model="field.frame_numbers" maxlength="10">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.frame_numbers!= undefined" v-html="errors.frame_numbers[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_lengths">長さ（cm）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.vehicle_lengths != undefined ? 'is-invalid':'' " id="vehicle_lengths" name="vehicle_lengths" v-model="field.vehicle_lengths" maxlength="4">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_lengths!= undefined" v-html="errors.vehicle_lengths[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_widths">幅（cm）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.vehicle_widths != undefined ? 'is-invalid':'' " id="vehicle_widths" name="vehicle_widths" v-model="field.vehicle_widths" maxlength="3">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_widths!= undefined" v-html="errors.vehicle_widths[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_heights">高さ（cm）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.vehicle_heights != undefined ? 'is-invalid':'' " id="vehicle_heights" name="vehicle_heights" v-model="field.vehicle_heights" maxlength="3">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_heights!= undefined" v-html="errors.vehicle_heights[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="axle_loads_ff">前前軸重（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.axle_loads_ff != undefined ? 'is-invalid':'' " id="axle_loads_ff" name="axle_loads_ff" v-model="field.axle_loads_ff" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.axle_loads_ff!= undefined" v-html="errors.axle_loads_ff[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="axle_loads_fr">前後軸重（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.axle_loads_fr != undefined ? 'is-invalid':'' " id="axle_loads_fr" name="axle_loads_fr" v-model="field.axle_loads_fr" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.axle_loads_fr!= undefined" v-html="errors.axle_loads_fr[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="axle_loads_rf">後前軸重（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.axle_loads_rf != undefined ? 'is-invalid':'' " id="axle_loads_rf" name="axle_loads_rf" v-model="field.axle_loads_rf" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.axle_loads_rf!= undefined" v-html="errors.axle_loads_rf[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="axle_loads_rr">後後軸重（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" id="axle_loads_rr" v-bind:class="errors.axle_loads_rr != undefined ? 'is-invalid':'' " name="axle_loads_rr" v-model="field.axle_loads_rr" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.axle_loads_rr!= undefined" v-html="errors.axle_loads_rr[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_types">型式</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.vehicle_types != undefined ? 'is-invalid':'' " id="vehicle_types" name="vehicle_types" v-model="field.vehicle_types" maxlength="50">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_types!= undefined" v-html="errors.vehicle_types[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="engine_typese">原動機の型式</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.engine_typese != undefined ? 'is-invalid':'' " id="engine_typese" name="engine_typese" v-model="field.engine_typese" maxlength="50">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.engine_typese!= undefined" v-html="errors.engine_typese[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="total_displacements">総排気量（L）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.total_displacements != undefined ? 'is-invalid':'' " id="total_displacements" name="total_displacements" v-model="field.total_displacements" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.total_displacements!= undefined" v-html="errors.total_displacements[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="rated_outputs">定格出力（KW）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.rated_outputs != undefined ? 'is-invalid':'' " id="rated_outputs" name="rated_outputs" v-model="field.rated_outputs" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.rated_outputs!= undefined" v-html="errors.rated_outputs[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="kinds_of_fuel_id">燃料の種類</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-75" id="kinds_of_fuel_id" name="kinds_of_fuel_id" v-model="field.kinds_of_fuel_id">
                                @foreach($listKindOfFuel as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.kinds_of_fuel_id!= undefined" v-html="errors.kinds_of_fuel_id[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="type_designation_numbers">型式指定番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.type_designation_numbers != undefined ? 'is-invalid':'' " id="type_designation_numbers" name="type_designation_numbers" v-model="field.type_designation_numbers" maxlength="50">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.type_designation_numbers!= undefined" v-html="errors.type_designation_numbers[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="id_segment_numbers">識別区分番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.id_segment_numbers != undefined ? 'is-invalid':'' " id="id_segment_numbers" name="id_segment_numbers" v-model="field.id_segment_numbers" maxlength="50">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.id_segment_numbers!= undefined" v-html="errors.id_segment_numbers[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col label-input-file">
                        <label class="col-md-4 col-sm-4">無線装置</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="wireless_installation_fg" value="1" name="wireless_installation_fg" v-model="field.wireless_installation_fg">
                                <label class="d-block custom-control-label" for="wireless_installation_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">◆所有者</div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="owner_nm">氏名または名称</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100" v-bind:class="errors. owner_nm!= undefined ? 'is-invalid':'' " id="owner_nm" name="owner_nm" v-model="field.owner_nm" maxlength="50">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.owner_nm!= undefined" v-html="errors.owner_nm[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5" for="owner_address">住所</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100" v-bind:class="errors.owner_address != undefined ? 'is-invalid':'' " id="owner_address" name="owner_address" v-model="field.owner_address" maxlength="200">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.owner_address!= undefined" v-html="errors.owner_address[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">◆使用者</div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="user_nm">氏名または名称</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100" v-bind:class="errors.user_nm != undefined ? 'is-invalid':'' " id="user_nm" name="user_nm" v-model="field.user_nm" maxlength="50">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.user_nm!= undefined" v-html="errors.user_nm[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <button class="btn btn-black" @click="copyText" type="button">所有者の内容コピー</button>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5" for="user_address">住所</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100" v-bind:class="errors.user_address != undefined ? 'is-invalid':'' " id="user_address" name="user_address" v-model="field.user_address" maxlength="200">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.user_address!= undefined" v-html="errors.user_address[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5" for="user_base_locations">本拠の位置</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100" v-bind:class="errors.user_base_locations != undefined ? 'is-invalid':'' " id="user_base_locations" name="user_base_locations" v-model="field.user_base_locations" maxlength="200">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.user_base_locations!= undefined" v-html="errors.user_base_locations[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="expiry_dt">有効期間の満了する日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="field.expiry_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'expiry_dt'"
                                         @if($role!=1) :disabled="true" @endif
                            >
                            </date-picker>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.expiry_dt!= undefined" v-html="errors.expiry_dt[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5" for="car_inspections_notes">車検証備考</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <textarea class="form-control w-100" v-bind:class="errors.car_inspections_notes != undefined ? 'is-invalid':'' " rows="3" id="car_inspections_notes" name="car_inspections_notes" maxlength="50" v-model="field.car_inspections_notes" v-cloak>@{{ field.car_inspections_notes }}</textarea>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.car_inspections_notes!= undefined" v-html="errors.car_inspections_notes[0]"></span>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="digital_tachograph_numbers">デジタコ車載器No.</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-75" v-bind:class="errors.digital_tachograph_numbers != undefined ? 'is-invalid':'' " id="digital_tachograph_numbers" name="digital_tachograph_numbers" v-model="field.digital_tachograph_numbers" maxlength="10">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.digital_tachograph_numbers!= undefined" v-html="errors.digital_tachograph_numbers[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="etc_numbers">ETC車載器No.</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75" v-bind:class="errors.etc_numbers != undefined ? 'is-invalid':'' " id="etc_numbers" name="etc_numbers" v-model="field.etc_numbers" maxlength="19">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.etc_numbers!= undefined" v-html="errors.etc_numbers[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="drive_recorder_numbers">ドラレコNo.</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-75" v-bind:class="errors.drive_recorder_numbers != undefined ? 'is-invalid':'' " id="drive_recorder_numbers" name="drive_recorder_numbers" v-model="field.drive_recorder_numbers" maxlength="10">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.drive_recorder_numbers!= undefined" v-html="errors.drive_recorder_numbers[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col label-input-file">
                        <label class="col-md-4 col-sm-4">ベッドの有無</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="bed_fg" value="1" name="bed_fg" v-model="field.bed_fg">
                                <label class="d-block custom-control-label" for="bed_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5">冷蔵冷凍機の有無</label>
                        <div class="col-md-7 col-sm-7 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="refrigerator_fg" name="refrigerator_fg" value="1" v-model="field.refrigerator_fg">
                                <label class="d-block custom-control-label" for="refrigerator_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="drive_system_id">駆動</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control" id="drive_system_id" name="drive_system_id" v-model="field.drive_system_id">
                                @foreach($listDriveSystem as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="transmissions_id">ミッション</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control" id="transmissions_id" name="transmissions_id" v-model="field.transmissions_id">
                                @foreach($listTransmissions as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="transmissions_notes">ミッション備考</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.transmissions_notes != undefined ? 'is-invalid':'' " id="transmissions_notes" name="transmissions_notes" v-model="field.transmissions_notes" maxlength="50">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.transmissions_notes!= undefined" v-html="errors.transmissions_notes[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="suspensions_cd">サスペンション</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control" id="suspensions_cd" name="suspensions_cd" v-model="field.suspensions_cd">
                                @foreach($listSuspensionsCd as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="tank_capacity_1">燃料タンクの容量１</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.tank_capacity_1 != undefined ? 'is-invalid':'' " id="tank_capacity_1" name="tank_capacity_1" v-model="field.tank_capacity_1" maxlength="3">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.tank_capacity_1!= undefined" v-html="errors.tank_capacity_1[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="tank_capacity_2">燃料タンクの容量２</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.tank_capacity_2 != undefined ? 'is-invalid':'' " id="tank_capacity_2" name="tank_capacity_2" v-model="field.tank_capacity_2" maxlength="3">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.tank_capacity_2!= undefined" v-html="errors.tank_capacity_2[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">◆積込可能内寸</div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="loading_inside_dimension_capacity_length">長さ（cm）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.loading_inside_dimension_capacity_length != undefined ? 'is-invalid':'' " id="loading_inside_dimension_capacity_length" name="loading_inside_dimension_capacity_length" v-model="field.loading_inside_dimension_capacity_length" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.loading_inside_dimension_capacity_length!= undefined" v-html="errors.loading_inside_dimension_capacity_length[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="loading_inside_dimension_capacity_width">幅（cm）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.loading_inside_dimension_capacity_width != undefined ? 'is-invalid':'' " id="loading_inside_dimension_capacity_width" name="loading_inside_dimension_capacity_width" v-model="field.loading_inside_dimension_capacity_width" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.loading_inside_dimension_capacity_width!= undefined" v-html="errors.loading_inside_dimension_capacity_width[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="loading_inside_dimension_capacity_height">高さ（cm）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.loading_inside_dimension_capacity_height != undefined ? 'is-invalid':'' " id="loading_inside_dimension_capacity_height" name="loading_inside_dimension_capacity_height" v-model="field.loading_inside_dimension_capacity_height" maxlength="5">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.loading_inside_dimension_capacity_height!= undefined" v-html="errors.loading_inside_dimension_capacity_height[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5">融雪</label>
                        <div class="col-md-7 col-sm-7 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="snowmelt_fg" name="snowmelt_fg" value="1" v-model="field.snowmelt_fg">
                                <label class="d-block custom-control-label" for="snowmelt_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4">観音扉</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="double_door_fg" name="double_door_fg" value="1" v-model="field.double_door_fg">
                                <label class="d-block custom-control-label" for="double_door_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5">床・鉄板</label>
                        <div class="col-md-7 col-sm-7 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="floor_iron_plate_fg" name="floor_iron_plate_fg" value="1" v-model="field.floor_iron_plate_fg">
                                <label class="d-block custom-control-label" for="floor_iron_plate_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4">床・佐川仕様埋込式</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="floor_sagawa_embedded_fg" name="floor_sagawa_embedded_fg" value="1" v-model="field.floor_sagawa_embedded_fg">
                                <label class="d-block custom-control-label" for="floor_sagawa_embedded_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5">床・ローラー</label>
                        <div class="col-md-7 col-sm-7 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="floor_roller_fg" name="floor_roller_fg" value="1" v-model="field.floor_roller_fg">
                                <label class="d-block custom-control-label" for="floor_roller_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4">床・ジョルダー及びコンベアー</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="floor_joloda_conveyor_fg" name="floor_joloda_conveyor_fg" value="1" v-model="field.floor_joloda_conveyor_fg">
                                <label class="d-block custom-control-label" for="floor_joloda_conveyor_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="power_gate_cd">パワーゲート</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="power_gate_cd" name="power_gate_cd" v-model="field.power_gate_cd">
                                @foreach($listPowerGate as $key => $value)
                                <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_delivery_dt">納車日</label>
                        <div class="col-md-3 col-sm-8 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="field.vehicle_delivery_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'vehicle_delivery_dt'"
                                         @if($role!=1) :disabled="true" @endif
                            >
                            </date-picker>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2" for="specification_notes">仕様に関する備考</label>
                        <div class="col-md-10 col-sm-10 wrap-control">
                            <textarea class="form-control w-100" v-bind:class="errors.specification_notes != undefined ? 'is-invalid':'' " rows="3" id="specification_notes" name="specification_notes" maxlength="200" v-cloak v-model="field.specification_notes">@{{ field.specification_notes}}</textarea>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.specification_notes!= undefined" v-html="errors.specification_notes[0]"></span>
                    </div>
                </div>
            </div>
            <div class="grid-form mb-5">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="mst_staff_cd">車輌管理責任者</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-100" id="mst_staff_cd" name="mst_staff_cd" v-cloak="" v-model="field.mst_staff_cd">
                                <option v-for="option in listStaffs" :value="option.value" v-cloak="">@{{option.text}}</option>
                            </select>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.mst_staff_cd!= undefined" v-html="errors.mst_staff_cd[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="personal_insurance_prices">対人保険料（円）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.personal_insurance_prices != undefined ? 'is-invalid':'' " id="personal_insurance_prices" name="personal_insurance_prices" v-model="field.personal_insurance_prices" maxlength="11">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.personal_insurance_prices!= undefined" v-html="errors.personal_insurance_prices[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="property_damage_insurance_prices">対物保険料（円）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25" v-bind:class="errors.property_damage_insurance_prices != undefined ? 'is-invalid':'' " id="property_damage_insurance_prices" name="property_damage_insurance_prices" v-model="field.property_damage_insurance_prices" maxlength="11">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.property_damage_insurance_prices!= undefined" v-html="errors.property_damage_insurance_prices[0]"></span>
                    </div>

                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_insurance_prices">車両保険料（円）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.vehicle_insurance_prices != undefined ? 'is-invalid':'' " id="vehicle_insurance_prices" name="vehicle_insurance_prices" v-model="field.vehicle_insurance_prices" maxlength="11">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.vehicle_insurance_prices!= undefined" v-html="errors.vehicle_insurance_prices[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2 label-input-file" for="picture_fronts">写真　前</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="picture_fronts" name="" class="input-file" v-on:change="onFileChange($event,'picture_fronts')" accept=".png, .jpg, .jpeg" ref="picture_fronts">
                                <span id="picture_fronts_file_name" class="w-100 form-control {{$role==2 ? 'disabled' : ''}}" v-cloak="">@{{ field.picture_fronts }}</span>

                            </div>
                            <div class="d-flex">
                                <label for="picture_fronts" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                @if(!empty($mVehicle["picture_fronts"]))
                                    <modal-viewer-file
                                        :header="'{{$mVehicle["picture_fronts"]}}'"
                                        :path="'{{\App\Helpers\Common::getPathVehicles($mVehicle,"picture_fronts")}}'"
                                    >
                                        プレビュー
                                    </modal-viewer-file>
                                @endif
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" @click="deleteFileUpload($event,'picture_fronts')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.picture_fronts!= undefined" v-html="errors.picture_fronts[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2 label-input-file" for="picture_rights">写真　側面右</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="picture_rights" name="picture_rights" class="input-file" v-on:change="onFileChange($event,'picture_rights')" accept=".png, .jpg, .jpeg" ref="picture_rights">
                                <span id="picture_rights_file_name" class="w-100 form-control {{$role==2 ? 'disabled' : ''}}" v-cloak>@{{ field.picture_rights }}</span>

                            </div>
                            <div class="d-flex">
                                <label for="picture_rights" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                @if(!empty($mVehicle["picture_rights"]))
                                    <modal-viewer-file
                                        :header="'{{$mVehicle["picture_rights"]}}'"
                                        :path="'{{\App\Helpers\Common::getPathVehicles($mVehicle,"picture_rights")}}'"
                                    >
                                        プレビュー
                                    </modal-viewer-file>
                                @endif
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" @click="deleteFileUpload($event,'picture_rights')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.picture_rights!= undefined" v-html="errors.picture_rights[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2 label-input-file" for="picture_lefts">写真　側面左</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="picture_lefts" name="picture_lefts" class="input-file" v-on:change="onFileChange($event,'picture_lefts')" accept=".png, .jpg, .jpeg" ref="picture_lefts">
                                <span id="picture_lefts_file_name" class="w-100 form-control {{$role==2 ? 'disabled' : ''}}" v-cloak>@{{ field.picture_lefts }}</span>

                            </div>
                            <div class="d-flex">
                                <label for="picture_lefts" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                @if(!empty($mVehicle["picture_lefts"]))
                                    <modal-viewer-file
                                        :header="'{{$mVehicle["picture_lefts"]}}'"
                                        :path="'{{\App\Helpers\Common::getPathVehicles($mVehicle,"picture_lefts")}}'"
                                    >
                                        プレビュー
                                    </modal-viewer-file>
                                @endif
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" @click="deleteFileUpload($event,'picture_lefts')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.picture_lefts!= undefined" v-html="errors.picture_lefts[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2 label-input-file" for="picture_rears">写真　後</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="picture_rears" name="picture_rears" class="input-file" v-on:change="onFileChange($event,'picture_rears')" accept=".png, .jpg, .jpeg" ref="picture_rears">
                                <span id="picture_rears_file_name" class="w-100 form-control {{$role==2 ? 'disabled' : ''}}" v-cloak>@{{ field.picture_rears }}</span>

                            </div>
                            <div class="d-flex">
                                <label for="picture_rears" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                @if(!empty($mVehicle["picture_rears"]))
                                    <modal-viewer-file
                                        :header="'{{$mVehicle["picture_rears"]}}'"
                                        :path="'{{\App\Helpers\Common::getPathVehicles($mVehicle,"picture_rears")}}'"
                                    >
                                        プレビュー
                                    </modal-viewer-file>
                                @endif
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" @click="deleteFileUpload($event,'picture_rears')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.picture_rears!= undefined" v-html="errors.picture_rears[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="acquisition_amounts">取得金額（円）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.acquisition_amounts != undefined ? 'is-invalid':'' " id="acquisition_amounts" name="acquisition_amounts" maxlength="11" v-model="field.acquisition_amounts">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.acquisition_amounts!= undefined" v-html="errors.acquisition_amounts[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="acquisition_amortization">償却回数（回）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25" v-bind:class="errors.acquisition_amortization != undefined ? 'is-invalid':'' " id="acquisition_amortization" name="acquisition_amortization" maxlength="3" v-model="field.acquisition_amortization">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.acquisition_amortization!= undefined" v-html="errors.acquisition_amortization[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="durable_years">耐用年数（年）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.durable_years != undefined ? 'is-invalid':'' " id="durable_years" name="durable_years" maxlength="3" v-model="field.durable_years">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.durable_years!= undefined" v-html="errors.durable_years[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="tire_sizes">タイヤサイズ</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.tire_sizes != undefined ? 'is-invalid':'' " id="tire_sizes" name="tire_sizes" maxlength="10" v-model="field.tire_sizes">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.tire_sizes!= undefined" v-html="errors.tire_sizes[0]"></span>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="battery_sizes">バッテリーサイズ</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50" v-bind:class="errors.battery_sizes != undefined ? 'is-invalid':'' " id="battery_sizes" name="battery_sizes" maxlength="10" v-model="field.battery_sizes">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.battery_sizes!= undefined" v-html="errors.battery_sizes[0]"></span>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="dispose_dt">売却または廃車日</label>
                        <div class="col-md-3 col-sm-8 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="field.dispose_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'dispose_dt'"
                                         @if($role!=1) :disabled="true" @endif
                            >
                            </date-picker>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2" for="notes">備考</label>
                        <div class="col-md-10 col-sm-10 wrap-control">
                            <input type="text" class="form-control" v-bind:class="errors.notes != undefined ? 'is-invalid':'' " id="notes" name="notes" v-model="field.notes" maxlength="100">
                        </div>
                        <span class="message-error w-100" role="alert" v-cloak v-if="errors.notes!= undefined" v-html="errors.notes[0]"></span>
                    </div>
                </div>
            </div>
            @endif
              @if($role==2)
               </fieldset>
             @endif
        </form>
    </div>
@endsection
@section("scripts")
    <script type="text/javascript">
        var listRoute = "{{route('vehicles.list')}}";
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
    </script>
    <script type="text/javascript" src="{{ url('/js/PDFObject/pdfobject.min.js') }}" charset="utf-8"></script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/vehicles.js') }}" charset="utf-8"></script>
@endsection
