@extends('Layouts.app')
@section('title',$mVehicle->id ? '車両　修正画面' : '車両　新規追加')
@section('title_header',$mVehicle->id ? '車両　修正画面' : '車両　新規追加')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    <div class="row row-xs" id="ctrSupplierrsVl">
        <form class="form-inline" role="form" method="post" id="form1" action="{{$mVehicle->id ? route('vehicles.edit.post',['id' => $mVehicle->id, 'mode'=>'edit']): ''}}">
            @csrf
            <div class="sub-header">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black">{{ trans("common.button.back") }}</button>
                    </div>
                    @if($mVehicle->id)
                    <div class="d-flex ml-auto">
                        <button class="btn btn-danger text-white" onclick="detele()">{{ trans("common.button.delete") }}</button>
                    </div>
                    @endif
                </div>
                @if($mVehicle->id)
                    <div class="grid-form border-0">
                        <div class="row">
                            <div class="col-md-5 col-sm-12 row grid-col"></div>
                            <div class="col-md-7 col-sm-12 row grid-col">
                                <button class="btn btn-primary btn-submit" type="submit" disabled>{{ trans("common.button.edit") }}</button>
                                @if($flagLasted)
                                <button class="btn btn-primary btn-submit m-auto" type="button" onclick="registerHistoryLeft()" >{{ trans("common.button.register_history_left") }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="sub-header-line-two">
                        <button class="btn btn-primary btn-submit" type="submit">{{ trans("common.button.register") }}</button>
                    </div>
                @endif
            </div>

            <div class="text-danger w-100">*　は必須入力の項目です。</div>
            <div class="w-100">
                @include('Layouts.alert')
            </div>
            @if($mVehicle->id)
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="vehicles_cd">車両コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicles_cd')? 'is-invalid': ''}}" name="vehicles_cd" id="vehicles_cd" readonly maxlength="5" value="{{ $mVehicle->vehicles_cd ?? old('vehicles_cd') }}">
                        </div>
                        <span class="note">
                            ※編集中データをもとに、新しい適用期間のデータを作成したい場合は、適用開始日（新規用）を入力し、新規登録（履歴残し）ボタンを押してください。
                        </span>
                        @if ($errors->has('vehicles_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('vehicles_cd') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row  h-100">
                        <div class="col row grid-col h-100">
                            <label class="col-7 required" for="adhibition_start_dt_old">適用開始日（更新用）</label>
                            <div class="col-5 wrap-control">
                                <input type="text" readonly class="form-control" id="adhibition_start_dt_old" name="adhibition_start_dt_old" value="{{str_replace('-', '/', $mVehicle->adhibition_start_dt) }}">
                            </div>
                        </div>
                        <div class="col row grid-col h-100">
                            <label class="col-7" for="adhibition_end_dt">適用終了日（更新用）</label>
                            <div class="col-5 wrap-control">
                                <input type="text" readonly class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ str_replace('-', '/', $mVehicle->adhibition_end_dt ?? config('params.adhibition_end_dt_default') )}}">
                            </div>
                        </div>
                        <div class="break-row-form"></div>
                        <div class="col row grid-col h-100">
                            <label class="col-7 required" for="adhibition_start_dt">適用開始日（新規用）</label>
                            <div class="col-5 wrap-control">
                                <date-picker format="YYYY/MM/DD"
                                             placeholder=""
                                             v-model="adhibition_start_dt" v-cloak=""
                                             :lang="lang"
                                             :input-class="{{ $errors->has('adhibition_start_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                             v-on:change="onChangeDatepicker1"
                                             :value-type="'format'"
                                >
                                </date-picker>
                                <input type="hidden" class="form-control {{$errors->has('adhibition_start_dt')? 'is-invalid': ''}}" name="adhibition_start_dt" id="adhibition_start_dt" value="{{ old('adhibition_start_dt') }}" >
                            </div>
                        </div>
                        <div class="col row grid-col h-100">
                            <label class="col-7" for="adhibition_end_dt">適用終了日（新規用）</label>
                            <div class="col-5 wrap-control">
                                <input type="text" readonly class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ str_replace('-', '/', config('params.adhibition_end_dt_default') )}}">
                            </div>
                        </div>
                        @if ($errors->has('adhibition_start_dt'))
                            <span class="invalid-feedback d-block grid-col" role="alert">
                                    <strong>{{ $errors->first('adhibition_start_dt') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            @else
                <div class="grid-form">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 row grid-col h-100">
                            <label class="col-md-5 col-sm-5 required" for="vehicles_cd">車両コード</label>
                            <div class="col-md-7 col-sm-7 wrap-control">
                                <input type="text" class="form-control w-50 {{$errors->has('vehicles_cd')? 'is-invalid': ''}}" name="vehicles_cd" id="vehicles_cd" maxlength="5" value="{{ $mVehicle->vehicles_cd ?? old('vehicles_cd') }}">
                            </div>
                            @if ($errors->has('vehicles_cd'))
                                <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicles_cd') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-md-7 col-sm-12 row  h-100">
                            <div class="col row grid-col h-100">
                                <label class="col-7 required" for="adhibition_start_dt">適用開始日</label>
                                <div class="col-5 wrap-control">
                                    <date-picker format="YYYY/MM/DD"
                                                 placeholder=""
                                                 v-model="adhibition_start_dt" v-cloak=""
                                                 :lang="lang"
                                                 :input-class="{{ $errors->has('adhibition_start_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                                 v-on:change="onChangeDatepicker1"
                                                 :value-type="'format'"
                                    >
                                    </date-picker>
                                    <input type="hidden" class="form-control {{$errors->has('adhibition_start_dt')? 'is-invalid': ''}}" name="adhibition_start_dt" id="adhibition_start_dt" value="{{ $mVehicle->adhibition_start_dt ?? old('adhibition_start_dt') }}" >
                                </div>
                            </div>
                            <div class="col row grid-col h-100">
                                <label class="col-7" for="adhibition_end_dt">適用終了日</label>
                                <div class="col-5 wrap-control">
                                    <input type="text" readonly class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ $mVehicle->adhibition_end_dt ?? config('params.adhibition_end_dt_default') }}">
                                </div>
                            </div>
                            @if ($errors->has('adhibition_start_dt'))
                                <span class="invalid-feedback d-block grid-col" role="alert">
                                    <strong>{{ $errors->first('adhibition_start_dt') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="door_number">ドア番</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('door_number')? 'is-invalid': ''}}" id="door_number" name="door_number">
                        </div>
                        @if ($errors->has('door_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('door_number') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="vehicles_kb">車両区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicles_kb" name="vehicles_kb">
                                @foreach($listVehicleKb as $key => $value)
                                <option value="{{$key}}" {{$key==$mVehicle->vehicles_kb || $key==old('vehicles_kb') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('vehicles_kb'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('vehicles_kb') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="registration_numbers">自動車登録番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('registration_numbers')? 'is-invalid': ''}}" id="registration_numbers" name="registration_numbers" value="{{ $mVehicle->registration_numbers ?? old('registration_numbers') }}" maxlength="200" >
                        </div>
                        @if ($errors->has('registration_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('registration_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="mst_business_office_id">営業所</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="mst_business_office_id" name="mst_business_office_id">
                                @foreach($listBusinessOffices as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->mst_business_office_id || $key==old('mst_business_office_id') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('mst_business_office_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('mst_business_office_id') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="vehicle_size_kb">小中大区分</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="vehicle_size_kb" name="vehicle_size_kb">
                                @foreach($listVehicleSize as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->vehicle_size_kb || $key==old('vehicle_size_kb') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('vehicle_size_kb'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_size_kb') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="vehicle_purpose_id">用途</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicle_purpose_id" name="vehicle_purpose_id">
                                @foreach($listVehiclePurpose as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->vehicle_purpose_id || $key==old('vehicle_purpose_id') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('vehicle_purpose_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('vehicle_purpose_id') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="land_transport_office_cd">陸運支局</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-100" id="land_transport_office_cd" name="land_transport_office_cd">
                                @foreach($listLandTranportOfficeCd as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->land_transport_office_cd || $key==old('land_transport_office_cd') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('land_transport_office_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('land_transport_office_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <label class="col-md-2 col-sm-5" for="vehicle_inspection_sticker_pdf">車検証PDF</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="file" class="form-control {{$errors->has('vehicle_inspection_sticker_pdf')? 'is-invalid': ''}}" id="vehicle_inspection_sticker_pdf" name="vehicle_inspection_sticker_pdf" value="{{ $mVehicle->vehicle_inspection_sticker_pdf ?? old('vehicle_inspection_sticker_pdf') }}" maxlength="2500">
                        </div>
                        @if ($errors->has('vehicle_inspection_sticker_pdf'))
                            <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('vehicle_inspection_sticker_pdf') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="registration_dt">自動車登録年月日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="registration_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         v-on:change='onChangeDatepicker2'
                                         :value-type="'format'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control {{$errors->has('registration_dt')? 'is-invalid': ''}}" name="registration_dt" id="registration_dt" value="{{ $mVehicle->registration_dt ?? old('registration_dt') }}" >
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="first_year_registration_dt">初年度登録年月</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25{{$errors->has('first_year_registration_dt')? 'is-invalid': ''}}" id="first_year_registration_dt" name="first_year_registration_dt" value="{{ $mVehicle->first_year_registration_dt ?? old('first_year_registration_dt') }}" maxlength="50">
                            ※例：201903など
                        </div>
                        @if ($errors->has('first_year_registration_dt'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('first_year_registration_dt') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="vehicle_classification_id">自動車種別</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="vehicle_classification_id" name="vehicle_classification_id">
                                @foreach($listVehicleClassification as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->vehicle_classification_id || $key==old('vehicle_classification_id') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('vehicle_classification_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_classification_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="private_commercial_id">自家用・事業用の別</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="private_commercial_id" name="private_commercial_id">
                                @foreach($listVehicleClassification as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->private_commercial_id || $key==old('private_commercial_id') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('private_commercial_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('private_commercial_id') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="car_body_shape_id">車体の形状</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="car_body_shape_id" name="car_body_shape_id">
                                @foreach($listCarBodyShape as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->car_body_shape_id || $key==old('car_body_shape_id') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('car_body_shape_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('car_body_shape_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="vehicle_id">車名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicle_id" name="vehicle_id">
                                @foreach($listVehicle as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->vehicle_id || $key==old('vehicle_id') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('vehicle_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="seating_capacity">定員</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('seating_capacity')? 'is-invalid': ''}}" id="seating_capacity" name="seating_capacity" value="{{ $mVehicle->seating_capacity ?? old('seating_capacity') }}" maxlength="2">
                        </div>
                        @if ($errors->has('seating_capacity'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('seating_capacity') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="max_loading_capacity">最大積載量（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('max_loading_capacity')? 'is-invalid': ''}}" id="max_loading_capacity" name="max_loading_capacity" value="{{ $mVehicle->max_loading_capacity ?? old('max_loading_capacity') }}" maxlength="2">
                        </div>
                        @if ($errors->has('max_loading_capacity'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('max_loading_capacity') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="vehicle_body_weights">車両重量（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_body_weights')? 'is-invalid': ''}}" id="vehicle_body_weights" name="vehicle_body_weights" value="{{ $mVehicle->vehicle_body_weights ?? old('vehicle_body_weights') }}" maxlength="2">
                        </div>
                        @if ($errors->has('vehicle_body_weights'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_body_weights') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="vehicle_total_weights">車両総重量（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_total_weights')? 'is-invalid': ''}}" id="vehicle_total_weights" name="vehicle_total_weights" value="{{ $mVehicle->vehicle_total_weights ?? old('vehicle_total_weights') }}" maxlength="2">
                        </div>
                        @if ($errors->has('vehicle_total_weights'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_total_weights') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="frame_numbers">車台番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('frame_numbers')? 'is-invalid': ''}}" id="frame_numbers" name="frame_numbers" value="{{ $mVehicle->frame_numbers ?? old('frame_numbers') }}" maxlength="2">
                        </div>
                        @if ($errors->has('frame_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('frame_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="vehicle_lengths">長さ（cm）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_lengths')? 'is-invalid': ''}}" id="vehicle_lengths" name="vehicle_lengths" value="{{ $mVehicle->vehicle_lengths ?? old('vehicle_lengths') }}" maxlength="2">
                        </div>
                        @if ($errors->has('vehicle_lengths'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_lengths') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="vehicle_widths">幅（cm）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_widths')? 'is-invalid': ''}}" id="vehicle_widths" name="vehicle_widths" value="{{ $mVehicle->vehicle_widths ?? old('vehicle_widths') }}" maxlength="2">
                        </div>
                        @if ($errors->has('vehicle_widths'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_widths') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="vehicle_heights">高さ（cm）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_heights')? 'is-invalid': ''}}" id="vehicle_heights" name="vehicle_heights" value="{{ $mVehicle->vehicle_heights ?? old('vehicle_heights') }}" maxlength="2">
                        </div>
                        @if ($errors->has('vehicle_heights'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_heights') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="axle_loads_ff">前前軸重（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('axle_loads_ff')? 'is-invalid': ''}}" id="axle_loads_ff" name="axle_loads_ff" value="{{ $mVehicle->axle_loads_ff ?? old('axle_loads_ff') }}" maxlength="2">
                        </div>
                        @if ($errors->has('axle_loads_ff'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('axle_loads_ff') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="axle_loads_fr">前後軸重（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('axle_loads_fr')? 'is-invalid': ''}}" id="axle_loads_fr" name="axle_loads_fr" value="{{ $mVehicle->axle_loads_fr ?? old('axle_loads_fr') }}" maxlength="2">
                        </div>
                        @if ($errors->has('axle_loads_fr'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('axle_loads_fr') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="axle_loads_rf">後前軸重（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('axle_loads_rf')? 'is-invalid': ''}}" id="axle_loads_rf" name="axle_loads_rf" value="{{ $mVehicle->axle_loads_rf ?? old('axle_loads_rf') }}" maxlength="2">
                        </div>
                        @if ($errors->has('axle_loads_rf'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('axle_loads_rf') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="axle_loads_rr">後後軸重（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('axle_loads_rr')? 'is-invalid': ''}}" id="axle_loads_rr" name="axle_loads_rr" value="{{ $mVehicle->axle_loads_rr ?? old('axle_loads_rr') }}" maxlength="2">
                        </div>
                        @if ($errors->has('axle_loads_rr'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('axle_loads_rr') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="vehicle_types">型式</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_types')? 'is-invalid': ''}}" id="vehicle_types" name="vehicle_types" value="{{ $mVehicle->vehicle_types ?? old('vehicle_types') }}" maxlength="2">
                        </div>
                        @if ($errors->has('vehicle_types'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_types') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="engine_typese">原動機の型式</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('engine_typese')? 'is-invalid': ''}}" id="engine_typese" name="engine_typese" value="{{ $mVehicle->engine_typese ?? old('engine_typese') }}" maxlength="2">
                        </div>
                        @if ($errors->has('engine_typese'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('engine_typese') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="total_displacements">総排気量（L）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('total_displacements')? 'is-invalid': ''}}" id="total_displacements" name="total_displacements" value="{{ $mVehicle->total_displacements ?? old('total_displacements') }}" maxlength="2">
                        </div>
                        @if ($errors->has('total_displacements'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('total_displacements') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="rated_outputs">定格出力（KW）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('rated_outputs')? 'is-invalid': ''}}" id="rated_outputs" name="rated_outputs" value="{{ $mVehicle->rated_outputs ?? old('rated_outputs') }}" maxlength="2">
                        </div>
                        @if ($errors->has('rated_outputs'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('rated_outputs') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="kinds_of_fuel_id">燃料の種類</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-75" id="kinds_of_fuel_id" name="kinds_of_fuel_id">
                                @foreach($listKindOfFuel as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->kinds_of_fuel_id || $key==old('kinds_of_fuel_id') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('kinds_of_fuel_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('kinds_of_fuel_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="type_designation_numbers">型式指定番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('type_designation_numbers')? 'is-invalid': ''}}" id="type_designation_numbers" name="type_designation_numbers" value="{{ $mVehicle->type_designation_numbers ?? old('type_designation_numbers') }}" maxlength="2">
                        </div>
                        @if ($errors->has('type_designation_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('type_designation_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="id_segment_numbers">識別区分番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('id_segment_numbers')? 'is-invalid': ''}}" id="id_segment_numbers" name="id_segment_numbers" value="{{ $mVehicle->id_segment_numbers ?? old('id_segment_numbers') }}" maxlength="2">
                        </div>
                        @if ($errors->has('id_segment_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('id_segment_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">◆所有者</div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="owner_nm">氏名または名称</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('owner_nm')? 'is-invalid': ''}}" id="owner_nm" name="owner_nm" value="{{ $mVehicle->owner_nm ?? old('owner_nm') }}" maxlength="2">
                        </div>
                        @if ($errors->has('owner_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('owner_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <label class="col-md-2 col-sm-5" for="owner_address">住所</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('owner_address')? 'is-invalid': ''}}" id="owner_address" name="owner_address" value="{{ $mVehicle->owner_address ?? old('owner_address') }}" maxlength="2">
                        </div>
                        @if ($errors->has('owner_address'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('owner_address') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">◆使用者</div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="user_nm">氏名または名称</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('user_nm')? 'is-invalid': ''}}" id="user_nm" name="user_nm" value="{{ $mVehicle->user_nm ?? old('user_nm') }}" maxlength="2">
                        </div>
                        @if ($errors->has('user_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('user_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <button class="btn btn-black" v-on:click="getAddrFromZipCode" type="button">所有者の内容コピー</button>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <label class="col-md-2 col-sm-5" for="user_address">住所</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('user_address')? 'is-invalid': ''}}" id="user_address" name="user_address" value="{{ $mVehicle->user_address ?? old('user_address') }}" maxlength="2">
                        </div>
                        @if ($errors->has('user_address'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('user_address') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <label class="col-md-2 col-sm-5" for="user_base_locations">本拠の位置</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('user_base_locations')? 'is-invalid': ''}}" id="user_base_locations" name="user_base_locations" value="{{ $mVehicle->user_base_locations ?? old('user_base_locations') }}" maxlength="2">
                        </div>
                        @if ($errors->has('user_base_locations'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('user_base_locations') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="expiry_dt">有効期間の満了する日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="expiry_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         v-on:change='onChangeDatepicker2'
                                         :value-type="'format'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control {{$errors->has('expiry_dt')? 'is-invalid': ''}}" name="expiry_dt" id="expiry_dt" value="{{ $mVehicle->expiry_dt ?? old('expiry_dt') }}" >
                        </div>
                        @if ($errors->has('expiry_dt'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('expiry_dt') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <label class="col-md-2 col-sm-5" for="car_inspections_notes">車検証備考</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <textarea class="form-control w-100 {{$errors->has('car_inspections_notes')? 'is-invalid': ''}}" rows="3" id="car_inspections_notes" name="car_inspections_notes" maxlength="50">{{ $mVehicle->car_inspections_notes ?? old('car_inspections_notes') }}</textarea>
                        </div>
                        @if ($errors->has('car_inspections_notes'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('car_inspections_notes') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="zip_cd">デジタコ車載器No.</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control" id="vehicles_kb" name="vehicles_kb">
                                @foreach($listPrivateCommercial as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->vehicles_kb || $key==old('vehicles_kb') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('zip_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('zip_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="address1">ETC車載器No.</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('address1')? 'is-invalid': ''}}" id="address1" name="address1" value="{{ $mVehicle->address1 ?? old('address1') }}" maxlength="20">
                        </div>
                        @if ($errors->has('address1'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('address1') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="zip_cd">ドラレコNo.</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control" id="vehicles_kb" name="vehicles_kb">
                                @foreach($listPrivateCommercial as $key => $value)
                                    <option value="{{$key}}" {{$key==$mVehicle->vehicles_kb || $key==old('vehicles_kb') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('zip_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('zip_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="address1">ベッドの有無</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <div class="custom-control custom-checkbox d-block">
                                <input type="checkbox" class="d-block custom-control-input" id="customCheck1">
                                <label class="d-block custom-control-label" for="customCheck1">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="address2">町名番地</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('address2')? 'is-invalid': ''}}" id="address2" name="address2" value="{{ $mVehicle->address2 ?? old('address2') }}" maxlength="20">
                        </div>
                        @if ($errors->has('address2'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('address2') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="address3">建物等</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('address3')? 'is-invalid': ''}}" id="address3" name="address3" value="{{ $mVehicle->address3 ?? old('address3') }}" maxlength="50">
                        </div>
                        @if ($errors->has('address3'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('address3') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="phone_number">電話番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('phone_number')? 'is-invalid': ''}}" id="phone_number" name="phone_number" value="{{ $mVehicle->phone_number ?? old('phone_number') }}" maxlength="20">
                        </div>
                        @if ($errors->has('phone_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('phone_number') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="fax_number">FAX番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('fax_number')? 'is-invalid': ''}}" id="fax_number" name="fax_number" value="{{ $mVehicle->fax_number ?? old('fax_number') }}" maxlength="20">
                        </div>
                        @if ($errors->has('fax_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('fax_number') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <label class="col-md-2 col-sm-4" for="vehicle_inspection_sticker_pdf">WEBサイトアドレス</label>
                        <div class="col-md-10 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('vehicle_inspection_sticker_pdf')? 'is-invalid': ''}}" id="vehicle_inspection_sticker_pdf" name="vehicle_inspection_sticker_pdf" value="{{ $mVehicle->vehicle_inspection_sticker_pdf ?? old('vehicle_inspection_sticker_pdf') }}" maxlength="2500">
                        </div>
                        @if ($errors->has('vehicle_inspection_sticker_pdf'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_inspection_sticker_pdf') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="registration_dt">締日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('registration_dt')? 'is-invalid': ''}}" id="registration_dt" name="registration_dt" value="{{ $mVehicle->registration_dt ?? old('registration_dt') }}" maxlength="2">
                        </div>
                        @if ($errors->has('registration_dt'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('registration_dt') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payday">支払日</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payday')? 'is-invalid': ''}}" id="payday" name="payday" value="{{ $mVehicle->payday ?? old('payday') }}" maxlength="2">
                        </div>
                        @if ($errors->has('payday'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payday') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_month_id">支払予定月</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="payment_month_id" name="payment_month_id">
                                {{--@foreach($listPaymentMonth as $key => $value)--}}
                                    {{--<option value="{{$key}}" {{$key==$mVehicle->payment_month_id || $key==old('payment_month_id') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payment_day">支払予定日</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payment_day')? 'is-invalid': ''}}" id="payment_day" name="payment_day" value="{{ $mVehicle->payment_day ?? old('payment_day') }}" maxlength="2">
                        </div>
                        @if ($errors->has('payment_day'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_day') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_method_id">支払予定方法</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="payment_method_id" name="payment_method_id">
                                {{--@foreach($listPaymentMethod as $key => $value)--}}
                                    {{--<option value="{{$key}}" {{$key==$mVehicle->payment_method_id || $key==old('payment_method_id') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>

                        <div class="break-row-form"></div>

                        <label class="col-md-5 col-sm-5" for="registration_dt">取引開始日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="registration_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         v-on:change="onChangeDatepicker2"
                                         :value-type="'format'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control {{$errors->has('registration_dt')? 'is-invalid': ''}}" name="registration_dt" id="registration_dt" value="{{ $mVehicle->registration_dt ?? old('registration_dt') }}" >
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="explanations_bill">支払いに関する説明</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea class="form-control w-100 {{$errors->has('explanations_bill')? 'is-invalid': ''}}" rows="3" name="explanations_bill" id="explanations_bill" maxlength="100">{{ $mVehicle->explanations_bill ?? old('explanations_bill') }}</textarea>
                        </div>
                        @if ($errors->has('explanations_bill'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('explanations_bill') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="consumption_tax_calc_unit_id">消費税計算単位区分</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="consumption_tax_calc_unit_id" name="consumption_tax_calc_unit_id">
                                {{--@foreach($listConsumptionTaxCalcUnit as $key => $value)--}}
                                    {{--<option value="{{$key}}" {{ ($key==$mVehicle->consumption_tax_calc_unit_id && !is_null($mVehicle->consumption_tax_calc_unit_id))  || ($key==old('consumption_tax_calc_unit_id') && !is_null(old('consumption_tax_calc_unit_id')) ) ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="rounding_method_id">消費税端数処理区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="rounding_method_id" name="rounding_method_id">
                                {{--@foreach($listRoundingMethod as $key => $value)--}}
                                    {{--<option value="{{$key}}" {{$key==$mVehicle->rounding_method_id || $key==old('rounding_method_id') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_bank_cd">支払銀行コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payment_bank_cd')? 'is-invalid': ''}}" id="payment_bank_cd" name="payment_bank_cd" maxlength="4" value="{{ $mVehicle->payment_bank_cd ?? old('payment_bank_cd') }}">
                        </div>
                        @if ($errors->has('payment_bank_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_bank_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payment_bank_name">支払銀行名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('payment_bank_name')? 'is-invalid': ''}}" id="payment_bank_name" name="payment_bank_name" maxlength="30" value="{{ $mVehicle->payment_bank_name ?? old('payment_bank_name') }}">
                        </div>
                        @if ($errors->has('payment_bank_name'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_bank_name') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_branch_cd">支払銀行支店コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payment_branch_cd')? 'is-invalid': ''}}" id="payment_branch_cd" name="payment_branch_cd" maxlength="4" value="{{ $mVehicle->payment_branch_cd ?? old('payment_branch_cd') }}">
                        </div>
                        @if ($errors->has('payment_branch_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_branch_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payment_branch_name">支払銀行支店名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('payment_branch_name')? 'is-invalid': ''}}" id="payment_branch_name" name="payment_branch_name" maxlength="30" value="{{ $mVehicle->payment_branch_name ?? old('payment_branch_name') }}">
                        </div>
                        @if ($errors->has('payment_branch_name'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_branch_name') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_account_type">支払口座種別</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50 " id="payment_account_type" name="payment_account_type">
                                {{--@foreach($listPaymentAccountType as $key => $value)--}}
                                    {{--<option value="{{$key}}" {{$key==$mVehicle->payment_account_type || $key==old('payment_account_type') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payment_account_number">支払口座番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payment_account_number')? 'is-invalid': ''}}" id="payment_account_number" name="payment_account_number" maxlength="10" value="{{ $mVehicle->payment_account_number ?? old('payment_account_number') }}">
                        </div>
                        @if ($errors->has('payment_account_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_account_number') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="payment_account_holder">支払口座名義</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('payment_account_holder')? 'is-invalid': ''}}" id="payment_account_holder" name="payment_account_holder" maxlength="30" value="{{ $mVehicle->payment_account_holder ?? old('payment_account_holder') }}">
                        </div>
                        @if ($errors->has('payment_account_holder'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_account_holder') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="notes">備考</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea class="form-control w-100 {{$errors->has('notes')? 'is-invalid': ''}}" rows="3" id="notes" name="notes" maxlength="50">{{ $mVehicle->notes ?? old('notes') }}</textarea>
                        </div>
                        @if ($errors->has('notes'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('notes') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section("scripts")
    <script>

        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        function registerHistoryLeft() {
            $('#form1').attr('action','{{route('vehicles.edit.post',['id' => $mVehicle->id, 'mode'=>'registerHistoryLeft'])}}');
            $('#form1').submit();
        }
        function detele() {
            if(confirm(messages['MSG06001'])){
                $('#form1').attr('action','{{route('vehicles.delete.post',['id' => $mVehicle->id])}}');
                $('#form1').submit();
            }
        }
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/vehicles.js') }}" charset="utf-8"></script>
@endsection