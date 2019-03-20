@extends('Layouts.app')
@section('title',$mVehicle->id ? '車両　修正画面' : '車両　新規追加')
@section('title_header',$mVehicle->id ? '車両　修正画面' : '車両　新規追加')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    <div class="row row-xs" id="ctrVehiclesVl">
        <form class="form-inline" role="form" method="post" id="form1" enctype="multipart/form-data"  data-ajax="false" action="{{$mVehicle->id ? route('vehicles.edit.post',['id' => $mVehicle->id, 'mode'=>'edit']): ''}}">
            @csrf
            <div class="sub-header">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black" type="button" onclick="window.history.back();">{{ trans("common.button.back") }}</button>
                    </div>
                    @if($mVehicle->id)
                    <div class="d-flex ml-auto">
                        <button class="btn btn-danger text-white" v-on:click='deleteVehicle("{{$mVehicle->id}}")' type="button">{{ trans("common.button.delete") }}</button>
                    </div>
                    @endif
                </div>
                @if($mVehicle->id)
                    <div class="grid-form border-0">
                        <div class="row">
                            <div class="col-md-5 col-sm-12 row grid-col h-100"></div>
                            <div class="col-md-7 col-sm-12 row grid-col h-100">
                                <button class="btn btn-primary btn-submit" type="submit">{{ trans("common.button.edit") }}</button>
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
            <div class="w-100 alert-area">
                @include('Layouts.alert')
                <input type="hidden" id="adhibition_start_dt" value="{{  old('adhibition_start_dt', $mVehicle->adhibition_start_dt ? : '') }}">
            </div>
            @if($mVehicle->id)
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="vehicles_cd">車両コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicles_cd')? 'is-invalid': ''}}" name="vehicles_cd" id="vehicles_cd" readonly maxlength="5" value="{{ old('vehicles_cd', !is_null($mVehicle->vehicles_cd) ? $mVehicle->vehicles_cd : '') }}">
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
                                <date-picker format="YYYY/MM/DD"
                                             placeholder=""
                                             v-model="adhibition_start_dt" v-cloak=""
                                             :lang="lang"
                                             :input-class="{{ $errors->has('adhibition_start_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                             :value-type="'format'"
                                             :input-name="'adhibition_start_dt'"
                                >
                                </date-picker>
                            </div>
                            @if ($errors->has('adhibition_start_dt'))
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('adhibition_start_dt') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col row grid-col h-100">
                            <label class="col-7" for="adhibition_end_dt">適用終了日（更新用）</label>
                            <div class="col-5 wrap-control">
                                @if($flagLasted)
                                    <date-picker format="YYYY/MM/DD"
                                                 placeholder=""
                                                 v-model="adhibition_end_dt" v-cloak=""
                                                 :lang="lang"
                                                 :input-class="{{ $errors->has('adhibition_end_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                                 :value-type="'format'"
                                                 :input-name="'adhibition_end_dt'"
                                    >
                                    </date-picker>
                                    <input type="hidden" id="adhibition_end_dt" value="{{ old('adhibition_end_dt', $mVehicle->adhibition_end_dt ? : '') }}">
                                @else
                                    <input type="text" readonly class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ str_replace('-', '/',  $mVehicle->adhibition_end_dt ?? config('params.adhibition_end_dt_default'))}}">
                                @endif
                            </div>
                            @if ($errors->has('adhibition_end_dt'))
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('adhibition_end_dt') }}</strong>
                                </span>
                            @endif
                        </div>
                        @if($flagLasted)
                            <div class="break-row-form"></div>
                            <div class="col row grid-col h-100">
                                <label class="col-7 required" for="adhibition_start_dt">適用開始日（新規用）</label>
                                <div class="col-5 wrap-control">
                                    <date-picker format="YYYY/MM/DD"
                                                 placeholder=""
                                                 v-model="adhibition_start_dt_new" v-cloak=""
                                                 :lang="lang"
                                                 :input-class="{{ $errors->has('adhibition_start_dt_new')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                                 :value-type="'format'"
                                                 :input-name="'adhibition_start_dt_new'"
                                    >
                                    </date-picker>
                                    <input type="hidden" id="adhibition_start_dt_new" value="{{ old('adhibition_start_dt_new') }}" >
                                </div>
                            </div>
                            <div class="col row grid-col h-100">
                                <label class="col-7" for="adhibition_end_dt_new">適用終了日（新規用）</label>
                                <div class="col-5 wrap-control">
                                    <input type="text" readonly class="form-control" id="adhibition_end_dt_new" name="adhibition_end_dt_new" value="{{ str_replace('-', '/', config('params.adhibition_end_dt_default') )}}">
                                </div>
                            </div>
                        @endif
                        @if ($errors->has('adhibition_start_dt_new'))
                            <span class="invalid-feedback d-block grid-col" role="alert">
                                    <strong>{{ $errors->first('adhibition_start_dt_new') }}</strong>
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
                                <input type="text" class="form-control w-50 {{$errors->has('vehicles_cd')? 'is-invalid': ''}}" name="vehicles_cd" id="vehicles_cd" maxlength="5" value="{{ old('vehicles_cd') }}">
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
                                                 :value-type="'format'"
                                                 :input-name="'adhibition_start_dt'"
                                    >
                                    </date-picker>
                                </div>
                            </div>
                            <div class="col row grid-col h-100">
                                <label class="col-7" for="adhibition_end_dt">適用終了日</label>
                                <div class="col-5 wrap-control">
                                    <input type="text" readonly class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ config('params.adhibition_end_dt_default') }}">
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="door_number">ドア番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('door_number')? 'is-invalid': ''}}" id="door_number" name="door_number" value="{{ old('door_number',  !is_null($mVehicle->door_number) ? $mVehicle->door_number : '') }}" maxlength="10">
                        </div>
                        @if ($errors->has('door_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('door_number') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicles_kb">車両区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicles_kb" name="vehicles_kb">
                                @foreach($listVehicleKb as $key => $value)
                                <option value="{{$key}}" {{Common::selectIfValue('vehicles_kb', $key, $mVehicle->vehicles_kb)}}>{{$value}}</option>
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="registration_numbers">自動車登録番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('registration_numbers')? 'is-invalid': ''}}" id="registration_numbers" name="registration_numbers" value="{{ old('registration_numbers', !is_null($mVehicle->registration_numbers) ? $mVehicle->registration_numbers : '') }}" maxlength="50" >
                        </div>
                        @if ($errors->has('registration_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('registration_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4 required" for="mst_business_office_id">営業所</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="mst_business_office_id" name="mst_business_office_id">
                                @foreach($listBusinessOffices as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('mst_business_office_id', $key, $mVehicle->mst_business_office_id)}}>{{$value}}</option>
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_size_kb">小中大区分</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="vehicle_size_kb" name="vehicle_size_kb">
                                @foreach($listVehicleSize as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('vehicle_size_kb', $key, $mVehicle->vehicle_size_kb)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('vehicle_size_kb'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_size_kb') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_purpose_id">用途</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicle_purpose_id" name="vehicle_purpose_id">
                                @foreach($listVehiclePurpose as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('vehicle_purpose_id', $key, $mVehicle->vehicle_purpose_id)}}>{{$value}}</option>
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="land_transport_office_cd">陸運支局</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-100" id="land_transport_office_cd" name="land_transport_office_cd">
                                @foreach($listLandTranportOfficeCd as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('land_transport_office_cd', $key, $mVehicle->land_transport_office_cd)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('land_transport_office_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('land_transport_office_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5 label-input-file {{$mVehicle->vehicle_inspection_sticker_pdf ? 'pr-0': ''}}" for="vehicle_inspection_sticker_pdf">車検証PDF{{$mVehicle->vehicle_inspection_sticker_pdf ? '（１ファイル）': ''}}</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="vehicle_inspection_sticker_pdf" name="vehicle_inspection_sticker_pdf" class="input-file" onchange='uploadFile(this)' value="{{old('vehicle_inspection_sticker_pdf', $mVehicle->vehicle_inspection_sticker_pdf ? : '') }}" accept="application/pdf" >
                                <span id="vehicle_inspection_sticker_pdf_file_name" class="w-100 form-control">{{old('vehicle_inspection_sticker_pdf', $mVehicle->vehicle_inspection_sticker_pdf? :'')}}</span>

                            </div>
                            <div class="d-flex">
                                <label for="vehicle_inspection_sticker_pdf" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" onclick="deleteFileUpload(this,'vehicle_inspection_sticker_pdf')">ファイル削除</button>
                                </div>
                            </div>
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="registration_dt">自動車登録年月日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="registration_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'registration_dt'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control {{$errors->has('registration_dt')? 'is-invalid': ''}}" id="registration_dt" value="{{ old('registration_dt', $mVehicle->registration_dt ? : '') }}" >
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="first_year_registration_dt">初年度登録年月</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('first_year_registration_dt')? 'is-invalid': ''}}" id="first_year_registration_dt" name="first_year_registration_dt" value="{{ old('first_year_registration_dt',!is_null($mVehicle->first_year_registration_dt) ? $mVehicle->first_year_registration_dt : '') }}" maxlength="6">
                            ※例：201903など
                        </div>
                        @if ($errors->has('first_year_registration_dt'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('first_year_registration_dt') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_classification_id">自動車種別</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="vehicle_classification_id" name="vehicle_classification_id">
                                @foreach($listVehicleClassification as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('vehicle_classification_id', $key, $mVehicle->vehicle_classification_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('vehicle_classification_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_classification_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="private_commercial_id">自家用・事業用の別</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="private_commercial_id" name="private_commercial_id">
                                @foreach($listPrivateCommercial as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('private_commercial_id', $key, $mVehicle->private_commercial_id)}}>{{$value}}</option>
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="car_body_shape_id">車体の形状</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="car_body_shape_id" name="car_body_shape_id">
                                @foreach($listCarBodyShape as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('car_body_shape_id', $key, $mVehicle->car_body_shape_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('car_body_shape_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('car_body_shape_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_id">車名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-25" id="vehicle_id" name="vehicle_id">
                                @foreach($listVehicle as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('vehicle_id', $key, $mVehicle->vehicle_id)}}>{{$value}}</option>
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="seating_capacity">定員</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('seating_capacity')? 'is-invalid': ''}}" id="seating_capacity" name="seating_capacity" value="{{ old('seating_capacity', !is_null($mVehicle->seating_capacity) ? $mVehicle->seating_capacity : '') }}" maxlength="2">
                        </div>
                        @if ($errors->has('seating_capacity'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('seating_capacity') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="max_loading_capacity">最大積載量（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('max_loading_capacity')? 'is-invalid': ''}}" id="max_loading_capacity" name="max_loading_capacity" value="{{ old('max_loading_capacity', !is_null($mVehicle->max_loading_capacity) ? $mVehicle->max_loading_capacity : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('max_loading_capacity'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('max_loading_capacity') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_body_weights">車両重量（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_body_weights')? 'is-invalid': ''}}" id="vehicle_body_weights" name="vehicle_body_weights" value="{{ old('vehicle_body_weights', !is_null($mVehicle->vehicle_body_weights) ? $mVehicle->vehicle_body_weights : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('vehicle_body_weights'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_body_weights') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_total_weights">車両総重量（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_total_weights')? 'is-invalid': ''}}" id="vehicle_total_weights" name="vehicle_total_weights" value="{{ old('vehicle_total_weights', !is_null($mVehicle->vehicle_total_weights) ? $mVehicle->vehicle_total_weights : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('vehicle_total_weights'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_total_weights') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="frame_numbers">車台番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('frame_numbers')? 'is-invalid': ''}}" id="frame_numbers" name="frame_numbers" value="{{ old('frame_numbers', !is_null($mVehicle->frame_numbers) ? $mVehicle->frame_numbers : '') }}" maxlength="10">
                        </div>
                        @if ($errors->has('frame_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('frame_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_lengths">長さ（cm）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_lengths')? 'is-invalid': ''}}" id="vehicle_lengths" name="vehicle_lengths" value="{{ old('vehicle_lengths', !is_null($mVehicle->vehicle_lengths) ? $mVehicle->vehicle_lengths : '') }}" maxlength="4">
                        </div>
                        @if ($errors->has('vehicle_lengths'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_lengths') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_widths">幅（cm）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_widths')? 'is-invalid': ''}}" id="vehicle_widths" name="vehicle_widths" value="{{ old('vehicle_widths', !is_null($mVehicle->vehicle_widths) ? $mVehicle->vehicle_widths : '') }}" maxlength="3">
                        </div>
                        @if ($errors->has('vehicle_widths'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_widths') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_heights">高さ（cm）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_heights')? 'is-invalid': ''}}" id="vehicle_heights" name="vehicle_heights" value="{{ old('vehicle_heights', !is_null($mVehicle->vehicle_heights) ? $mVehicle->vehicle_heights : '') }}" maxlength="3">
                        </div>
                        @if ($errors->has('vehicle_heights'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_heights') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="axle_loads_ff">前前軸重（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('axle_loads_ff')? 'is-invalid': ''}}" id="axle_loads_ff" name="axle_loads_ff" value="{{ old('axle_loads_ff', !is_null($mVehicle->axle_loads_ff) ? $mVehicle->axle_loads_ff : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('axle_loads_ff'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('axle_loads_ff') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="axle_loads_fr">前後軸重（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('axle_loads_fr')? 'is-invalid': ''}}" id="axle_loads_fr" name="axle_loads_fr" value="{{ old('axle_loads_fr', !is_null($mVehicle->axle_loads_fr) ? $mVehicle->axle_loads_fr : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('axle_loads_fr'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('axle_loads_fr') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="axle_loads_rf">後前軸重（Kg）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('axle_loads_rf')? 'is-invalid': ''}}" id="axle_loads_rf" name="axle_loads_rf" value="{{ old('axle_loads_rf', !is_null($mVehicle->axle_loads_rf) ? $mVehicle->axle_loads_rf : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('axle_loads_rf'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('axle_loads_rf') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="axle_loads_rr">後後軸重（Kg）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('axle_loads_rr')? 'is-invalid': ''}}" id="axle_loads_rr" name="axle_loads_rr" value="{{ old('axle_loads_rr', !is_null($mVehicle->axle_loads_rr) ? $mVehicle->axle_loads_rr : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('axle_loads_rr'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('axle_loads_rr') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_types">型式</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_types')? 'is-invalid': ''}}" id="vehicle_types" name="vehicle_types" value="{{ old('vehicle_types', !is_null($mVehicle->vehicle_types) ? $mVehicle->vehicle_types : '') }}" maxlength="50">
                        </div>
                        @if ($errors->has('vehicle_types'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_types') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="engine_typese">原動機の型式</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('engine_typese')? 'is-invalid': ''}}" id="engine_typese" name="engine_typese" value="{{  old('engine_typese', !is_null($mVehicle->engine_typese) ? $mVehicle->engine_typese : '') }}" maxlength="50">
                        </div>
                        @if ($errors->has('engine_typese'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('engine_typese') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="total_displacements">総排気量（L）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('total_displacements')? 'is-invalid': ''}}" id="total_displacements" name="total_displacements" value="{{ old('total_displacements', !is_null($mVehicle->total_displacements) ? $mVehicle->total_displacements : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('total_displacements'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('total_displacements') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="rated_outputs">定格出力（KW）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('rated_outputs')? 'is-invalid': ''}}" id="rated_outputs" name="rated_outputs" value="{{ old('rated_outputs', !is_null($mVehicle->rated_outputs) ? $mVehicle->rated_outputs : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('rated_outputs'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('rated_outputs') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="kinds_of_fuel_id">燃料の種類</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-75" id="kinds_of_fuel_id" name="kinds_of_fuel_id">
                                @foreach($listKindOfFuel as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('kinds_of_fuel_id', $key, $mVehicle->kinds_of_fuel_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('kinds_of_fuel_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('kinds_of_fuel_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="type_designation_numbers">型式指定番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('type_designation_numbers')? 'is-invalid': ''}}" id="type_designation_numbers" name="type_designation_numbers" value="{{ old('type_designation_numbers', !is_null($mVehicle->type_designation_numbers) ? $mVehicle->type_designation_numbers : '') }}" maxlength="50">
                        </div>
                        @if ($errors->has('type_designation_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('type_designation_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="id_segment_numbers">識別区分番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('id_segment_numbers')? 'is-invalid': ''}}" id="id_segment_numbers" name="id_segment_numbers" value="{{ old('id_segment_numbers', !is_null($mVehicle->id_segment_numbers) ? $mVehicle->id_segment_numbers : '') }}" maxlength="50">
                        </div>
                        @if ($errors->has('id_segment_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('id_segment_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col label-input-file">
                        <label class="col-md-4 col-sm-4">無線装置</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="wireless_installation_fg" value="1" name="wireless_installation_fg" {{Common::checkIfValue('wireless_installation_fg', '1', $mVehicle->wireless_installation_fg)}}>
                                <label class="d-block custom-control-label" for="wireless_installation_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">◆所有者</div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="owner_nm">氏名または名称</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('owner_nm')? 'is-invalid': ''}}" id="owner_nm" name="owner_nm" value="{{ old('owner_nm', !is_null($mVehicle->owner_nm) ? $mVehicle->owner_nm : '') }}" maxlength="50">
                        </div>
                        @if ($errors->has('owner_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('owner_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5" for="owner_address">住所</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('owner_address')? 'is-invalid': ''}}" id="owner_address" name="owner_address" value="{{  old('owner_address', !is_null($mVehicle->owner_address) ? $mVehicle->owner_address : '') }}" maxlength="200">
                        </div>
                        @if ($errors->has('owner_address'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('owner_address') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">◆使用者</div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="user_nm">氏名または名称</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('user_nm')? 'is-invalid': ''}}" id="user_nm" name="user_nm" value="{{ old('user_nm', !is_null($mVehicle->user_nm) ? $mVehicle->user_nm : '') }}" maxlength="50">
                        </div>
                        @if ($errors->has('user_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('user_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <button class="btn btn-black" onclick="copyText()" type="button">所有者の内容コピー</button>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5" for="user_address">住所</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('user_address')? 'is-invalid': ''}}" id="user_address" name="user_address" value="{{ old('user_address', !is_null($mVehicle->user_address) ? $mVehicle->user_address : '') }}" maxlength="200">
                        </div>
                        @if ($errors->has('user_address'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('user_address') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5" for="user_base_locations">本拠の位置</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-100 {{$errors->has('user_base_locations')? 'is-invalid': ''}}" id="user_base_locations" name="user_base_locations" value="{{ old('user_base_locations', !is_null($mVehicle->user_base_locations) ? $mVehicle->user_base_locations : '') }}" maxlength="200">
                        </div>
                        @if ($errors->has('user_base_locations'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('user_base_locations') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="expiry_dt">有効期間の満了する日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="expiry_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'expiry_dt'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control {{$errors->has('expiry_dt')? 'is-invalid': ''}}" id="expiry_dt" value="{{ old('expiry_dt', $mVehicle->expiry_dt ? : '') }}" >
                        </div>
                        @if ($errors->has('expiry_dt'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('expiry_dt') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-5" for="car_inspections_notes">車検証備考</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <textarea class="form-control w-100 {{$errors->has('car_inspections_notes')? 'is-invalid': ''}}" rows="3" id="car_inspections_notes" name="car_inspections_notes" maxlength="50">{{ old('car_inspections_notes', $mVehicle->car_inspections_notes ? :'' )}}</textarea>
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="digital_tachograph_numbers">デジタコ車載器No.</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('digital_tachograph_numbers')? 'is-invalid': ''}}" id="digital_tachograph_numbers" name="digital_tachograph_numbers" value="{{ old('digital_tachograph_numbers', !is_null($mVehicle->digital_tachograph_numbers) ? $mVehicle->digital_tachograph_numbers : '') }}" maxlength="10">
                        </div>
                        @if ($errors->has('digital_tachograph_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('digital_tachograph_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="etc_numbers">ETC車載器No.</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('etc_numbers')? 'is-invalid': ''}}" id="etc_numbers" name="etc_numbers" value="{{ old('etc_numbers', !is_null($mVehicle->etc_numbers) ? $mVehicle->etc_numbers : '') }}" maxlength="19">
                        </div>
                        @if ($errors->has('etc_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('etc_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="drive_recorder_numbers">ドラレコNo.</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('drive_recorder_numbers')? 'is-invalid': ''}}" id="drive_recorder_numbers" name="drive_recorder_numbers" value="{{ old('drive_recorder_numbers', !is_null($mVehicle->drive_recorder_numbers) ? $mVehicle->drive_recorder_numbers : '') }}" maxlength="10">
                        </div>
                        @if ($errors->has('drive_recorder_numbers'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('drive_recorder_numbers') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col label-input-file">
                        <label class="col-md-4 col-sm-4">ベッドの有無</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="bed_fg" value="1" name="bed_fg" {{Common::checkIfValue('bed_fg', '1', $mVehicle->bed_fg)}}>
                                <label class="d-block custom-control-label" for="bed_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5">冷蔵冷凍機の有無</label>
                        <div class="col-md-7 col-sm-7 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="refrigerator_fg" name="refrigerator_fg" value="1" {{Common::checkIfValue('refrigerator_fg', '1', $mVehicle->refrigerator_fg)}}>
                                <label class="d-block custom-control-label" for="refrigerator_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="drive_system_id">駆動</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control" id="drive_system_id" name="drive_system_id">
                                @foreach($listDriveSystem as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('drive_system_id', $key, $mVehicle->drive_system_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="transmissions_id">ミッション</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control" id="transmissions_id" name="transmissions_id">
                                @foreach($listTransmissions as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('transmissions_id', $key, $mVehicle->transmissions_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="transmissions_notes">ミッション備考</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('transmissions_notes')? 'is-invalid': ''}}" id="transmissions_notes" name="transmissions_notes" value="{{ old('transmissions_notes', !is_null($mVehicle->transmissions_notes) ? $mVehicle->transmissions_notes : '') }}" maxlength="50">
                        </div>
                        @if ($errors->has('transmissions_notes'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('transmissions_notes') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="suspensions_cd">サスペンション</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control" id="suspensions_cd" name="suspensions_cd">
                                @foreach($listSuspensionsCd as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('suspensions_cd', $key, $mVehicle->suspensions_cd)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="tank_capacity_1">燃料タンクの容量１</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('tank_capacity_1')? 'is-invalid': ''}}" id="tank_capacity_1" name="tank_capacity_1" value="{{ old('tank_capacity_1', !is_null($mVehicle->tank_capacity_1) ? $mVehicle->tank_capacity_1 : '') }}" maxlength="3">
                        </div>
                        @if ($errors->has('tank_capacity_1'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('tank_capacity_1') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="tank_capacity_2">燃料タンクの容量２</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('tank_capacity_2')? 'is-invalid': ''}}" id="tank_capacity_2" name="tank_capacity_2" value="{{ old('tank_capacity_2', !is_null($mVehicle->tank_capacity_2) ? $mVehicle->tank_capacity_2 : '') }}" maxlength="3">
                        </div>
                        @if ($errors->has('tank_capacity_2'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('tank_capacity_2') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">◆積込可能内寸</div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="loading_inside_dimension_capacity_length">長さ（cm）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('loading_inside_dimension_capacity_length')? 'is-invalid': ''}}" id="loading_inside_dimension_capacity_length" name="loading_inside_dimension_capacity_length" value="{{ old('loading_inside_dimension_capacity_length', !is_null($mVehicle->loading_inside_dimension_capacity_length) ? $mVehicle->loading_inside_dimension_capacity_length : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('loading_inside_dimension_capacity_length'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('loading_inside_dimension_capacity_length') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="loading_inside_dimension_capacity_width">幅（cm）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('loading_inside_dimension_capacity_width')? 'is-invalid': ''}}" id="loading_inside_dimension_capacity_width" name="loading_inside_dimension_capacity_width" value="{{ old('loading_inside_dimension_capacity_width', !is_null($mVehicle->loading_inside_dimension_capacity_width) ? $mVehicle->loading_inside_dimension_capacity_width : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('loading_inside_dimension_capacity_width'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('loading_inside_dimension_capacity_width') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="loading_inside_dimension_capacity_height">高さ（cm）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('loading_inside_dimension_capacity_height')? 'is-invalid': ''}}" id="loading_inside_dimension_capacity_height" name="loading_inside_dimension_capacity_height" value="{{ old('loading_inside_dimension_capacity_height',!is_null($mVehicle->loading_inside_dimension_capacity_height) ? $mVehicle->loading_inside_dimension_capacity_height : '') }}" maxlength="5">
                        </div>
                        @if ($errors->has('loading_inside_dimension_capacity_height'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('loading_inside_dimension_capacity_height') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5">融雪</label>
                        <div class="col-md-7 col-sm-7 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="snowmelt_fg" name="snowmelt_fg" value="1" {{Common::checkIfValue('snowmelt_fg', '1', $mVehicle->snowmelt_fg)}}>
                                <label class="d-block custom-control-label" for="snowmelt_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4">観音扉</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="double_door_fg" name="double_door_fg" value="1" {{Common::checkIfValue('double_door_fg', '1', $mVehicle->double_door_fg)}}>
                                <label class="d-block custom-control-label" for="double_door_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5">床・鉄板</label>
                        <div class="col-md-7 col-sm-7 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="floor_iron_plate_fg" name="floor_iron_plate_fg" value="1" {{Common::checkIfValue('floor_iron_plate_fg', '1', $mVehicle->floor_iron_plate_fg)}}>
                                <label class="d-block custom-control-label" for="floor_iron_plate_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4">床・佐川仕様埋込式</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="floor_sagawa_embedded_fg" name="floor_sagawa_embedded_fg" value="1" {{Common::checkIfValue('floor_sagawa_embedded_fg', '1', $mVehicle->floor_sagawa_embedded_fg)}}>
                                <label class="d-block custom-control-label" for="floor_sagawa_embedded_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5">床・ローラー</label>
                        <div class="col-md-7 col-sm-7 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="floor_roller_fg" name="floor_roller_fg" value="1" {{Common::checkIfValue('floor_roller_fg', '1', $mVehicle->floor_roller_fg)}}>
                                <label class="d-block custom-control-label" for="floor_roller_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4">床・ジョルダー及びコンベアー</label>
                        <div class="col-md-8 col-sm-8 wrap-control d-flex align-content-center align-self-center pl-3">
                            <div class="custom-control custom-checkbox form-control border-0">
                                <input type="checkbox" class="custom-control-input" id="floor_joloda_conveyor_fg" name="floor_joloda_conveyor_fg" value="1" {{Common::checkIfValue('floor_joloda_conveyor_fg', '1', $mVehicle->floor_joloda_conveyor_fg)}}>
                                <label class="d-block custom-control-label" for="floor_joloda_conveyor_fg">あり</label>
                            </div>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="power_gate_cd">パワーゲート</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="power_gate_cd" name="power_gate_cd">
                                @foreach($listPowerGate as $key => $value)
                                <option value="{{$key}}" {{Common::selectIfValue('power_gate_cd', $key, $mVehicle->power_gate_cd)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="vehicle_delivery_dt">納車日</label>
                        <div class="col-md-3 col-sm-8 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="vehicle_delivery_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'vehicle_delivery_dt'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control {{$errors->has('vehicle_delivery_dt')? 'is-invalid': ''}}" id="vehicle_delivery_dt" value="{{ old('vehicle_delivery_dt',$mVehicle->vehicle_delivery_dt ? : '') }}" >
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2" for="specification_notes">仕様に関する備考</label>
                        <div class="col-md-10 col-sm-10 wrap-control">
                            <textarea class="form-control w-100 {{$errors->has('specification_notes')? 'is-invalid': ''}}" rows="3" id="specification_notes" name="specification_notes" maxlength="200">{{ old('specification_notes',$mVehicle->specification_notes ? $mVehicle->specification_notes : '') }}</textarea>
                        </div>
                        @if ($errors->has('specification_notes'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('specification_notes') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid-form mb-5">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="mst_staffs_id">車輌管理責任者号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-100" id="mst_staffs_id" name="mst_staffs_id">
                                @foreach($listAdminStaffs as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('mst_staffs_id', $key, $mVehicle->mst_staffs_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="personal_insurance_prices">対人保険料（円）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('personal_insurance_prices')? 'is-invalid': ''}}" id="personal_insurance_prices" name="personal_insurance_prices" value="{{ old('personal_insurance_prices', !is_null($mVehicle->personal_insurance_prices) ? $mVehicle->personal_insurance_prices : '') }}" maxlength="11">
                        </div>
                        @if ($errors->has('personal_insurance_prices'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('personal_insurance_prices') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="property_damage_insurance_prices">対物保険料（円）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('property_damage_insurance_prices')? 'is-invalid': ''}}" id="property_damage_insurance_prices" name="property_damage_insurance_prices" value="{{ old('property_damage_insurance_prices', !is_null($mVehicle->property_damage_insurance_prices) ? $mVehicle->property_damage_insurance_prices : '') }}" maxlength="11">
                        </div>
                        @if ($errors->has('property_damage_insurance_prices'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('property_damage_insurance_prices') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="vehicle_insurance_prices">車両保険料（円）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('vehicle_insurance_prices')? 'is-invalid': ''}}" id="vehicle_insurance_prices" name="vehicle_insurance_prices" value="{{ old('vehicle_insurance_prices', !is_null($mVehicle->vehicle_insurance_prices) ? $mVehicle->vehicle_insurance_prices : '') }}" maxlength="11">
                        </div>
                        @if ($errors->has('vehicle_insurance_prices'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('vehicle_insurance_prices') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100"></div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2 label-input-file" for="picture_fronts">写真　前</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="picture_fronts" name="picture_fronts" class="input-file" onchange='uploadFile(this)' value="{{old('picture_fronts', $mVehicle->picture_fronts ? : '') }}" accept=".png, .jpg, .jpeg">
                                <span id="picture_fronts_file_name" class="w-100 form-control">{{old('picture_fronts', $mVehicle->picture_fronts ? :'' )}}</span>

                            </div>
                            <div class="d-flex">
                                <label for="picture_fronts" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" onclick="deleteFileUpload(this,'picture_fronts')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        @if ($errors->has('picture_fronts'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('picture_fronts') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2 label-input-file" for="picture_rights">写真　側面右</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="picture_rights" name="picture_rights" class="input-file" onchange='uploadFile(this)' value="{{old('picture_rights', $mVehicle->picture_rights ? : '') }}" accept=".png, .jpg, .jpeg">
                                <span id="picture_rights_file_name" class="w-100 form-control">{{old('picture_rights', $mVehicle->picture_rights ? :'' )}}</span>

                            </div>
                            <div class="d-flex">
                                <label for="picture_rights" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" onclick="deleteFileUpload(this,'picture_rights')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        @if ($errors->has('picture_rights'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('picture_rights') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2 label-input-file" for="picture_lefts">写真　側面左</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="picture_lefts" name="picture_lefts" class="input-file" onchange='uploadFile(this)' value="{{old('picture_lefts', $mVehicle->picture_lefts ? : '') }}" accept=".png, .jpg, .jpeg">
                                <span id="picture_lefts_file_name" class="w-100 form-control">{{old('picture_lefts', $mVehicle->picture_lefts ? :'' )}}</span>

                            </div>
                            <div class="d-flex">
                                <label for="picture_lefts" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" onclick="deleteFileUpload(this,'picture_lefts')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        @if ($errors->has('picture_lefts'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('picture_lefts') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2 label-input-file" for="picture_rears">写真　後</label>
                        <div class="col-md-10 col-sm-7 wrap-control">
                            <div class="inputfile-box">
                                <input type="file" id="picture_rears" name="picture_rears" class="input-file" onchange='uploadFile(this)' value="{{old('picture_rears', $mVehicle->picture_rears ? : '') }}" accept=".png, .jpg, .jpeg">
                                <span id="picture_rears_file_name" class="w-100 form-control">{{old('picture_rears', $mVehicle->picture_rears ? :'' )}}</span>

                            </div>
                            <div class="d-flex">
                                <label for="picture_rears" class="d-inline">
                                    <span class="btn btn-secondary">ファイル選択</span>
                                </label>
                                <div class="ml-auto">
                                    <button type="button" class="btn btn-dark" onclick="deleteFileUpload(this,'picture_rears')">ファイル削除</button>
                                </div>
                            </div>
                        </div>
                        @if ($errors->has('picture_rears'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('picture_rears') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="acquisition_amounts">取得金額（円）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('acquisition_amounts')? 'is-invalid': ''}}" id="acquisition_amounts" name="acquisition_amounts" maxlength="11" value="{{ old('acquisition_amounts', !is_null($mVehicle->acquisition_amounts) ? $mVehicle->acquisition_amounts  : '') }}">
                        </div>
                        @if ($errors->has('acquisition_amounts'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('acquisition_amounts') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="acquisition_amortization">償却回数（回）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('acquisition_amortization')? 'is-invalid': ''}}" id="acquisition_amortization" name="acquisition_amortization" maxlength="3" value="{{ old('acquisition_amortization', !is_null($mVehicle->acquisition_amortization) ? $mVehicle->acquisition_amortization : '') }}">
                        </div>
                        @if ($errors->has('acquisition_amortization'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('acquisition_amortization') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="durable_years">耐用年数（年）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('durable_years')? 'is-invalid': ''}}" id="durable_years" name="durable_years" maxlength="3" value="{{ old('durable_years', !is_null($mVehicle->durable_years) ? $mVehicle->durable_years : '') }}">
                        </div>
                        @if ($errors->has('durable_years'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('durable_years') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="tire_sizes">タイヤサイズ</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('tire_sizes')? 'is-invalid': ''}}" id="tire_sizes" name="tire_sizes" maxlength="10" value="{{ old('tire_sizes', !is_null($mVehicle->tire_sizes) ? $mVehicle->tire_sizes : '') }}">
                        </div>
                        @if ($errors->has('tire_sizes'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('tire_sizes') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="battery_sizes">バッテリーサイズ</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('battery_sizes')? 'is-invalid': ''}}" id="battery_sizes" name="battery_sizes" maxlength="10" value="{{ old('battery_sizes', !is_null($mVehicle->battery_sizes) ? $mVehicle->battery_sizes : '') }}">
                        </div>
                        @if ($errors->has('battery_sizes'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('battery_sizes') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="dispose_dt">売却または廃車日</label>
                        <div class="col-md-3 col-sm-8 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="dispose_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="{{ $errors->has('dispose_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                         :value-type="'format'"
                                         :input-name="'dispose_dt'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control {{$errors->has('dispose_dt')? 'is-invalid': ''}}" id="dispose_dt" value="{{ old('dispose_dt',$mVehicle->dispose_dt  ? : '') }}" >
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-2" for="notes">備考</label>
                        <div class="col-md-10 col-sm-10 wrap-control">
                            <input type="text" class="form-control {{$errors->has('notes')? 'is-invalid': ''}}" id="notes" name="notes" value="{{ old('notes', !is_null($mVehicle->notes) ? $mVehicle->notes : '') }}" maxlength="100">
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
    <script type="text/javascript">
        var listRoute = "{{route('vehicles.list')}}";
        var deleteRoute = "{{route('vehicles.delete.post',['id' => $mVehicle->id])}}";
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        function registerHistoryLeft() {
            $('#form1').attr('action','{{route('vehicles.edit.post',['id' => $mVehicle->id, 'mode'=>'registerHistoryLeft'])}}');
            $('#form1').submit();
        }
        function uploadFile(target) {
            document.getElementById(target.name+"_file_name").innerHTML = target.files[0].name;
        }
        function deleteFileUpload(e,destination) {
            $('#'+destination+"_file_name").html('');
            $('#'+destination).wrap('<form>').closest('form').get(0).reset();
            $('#'+destination).unwrap();
            if($('div.alert-area :input[value='+destination+']').length <= 0){
                $('div.alert-area').append("<input type='hidden' class='deleteFile' name='deleteFile[]' value='"+ destination+"'>");
            }
        }
        
        function copyText() {
            $('#user_nm').val($('#owner_nm').val());
            $('#user_address').val($('#owner_address').val());
        }
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/vehicles.js') }}" charset="utf-8"></script>
@endsection