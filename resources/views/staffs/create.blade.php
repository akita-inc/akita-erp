@extends('Layouts.app')
@section('title','社員　新規追加')
@section('title_header','社員　新規追加')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    <div class="row row-xs" id="ctrStaffsVl">
        <form class="form-inline" role="form" method="post">
            @csrf
            <div class="sub-header">
                <div class="sub-header-line-one">
                    <button class="btn btn-black">戻る</button>
                </div>
                <div class="sub-header-line-two">
                    <button class="btn btn-primary btn-submit" type="submit">登録</button>
                </div>
            </div>

            <div class="text-danger w-100">*　は必須入力の項目です。</div>
            <div class="w-100">
                @include('Layouts.alert')
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        {{--<label class="col-md-5 col-sm-5 required" for="staff_cd">社員コード</label>--}}
                        {{--<div class="col-md-7 col-sm-7 wrap-control">--}}
                            {{--<input type="text" class="form-control w-50 {{$errors->has('staff_cd')? 'is-invalid': ''}}" name="staff_cd" id="staff_cd" maxlength="5" value="{{ $mStaffs->staff_cd ?? old('staff_cd') }}">--}}
                        {{--</div>--}}
                        {{--@if ($errors->has('staff_cd'))--}}
                            {{--<span class="invalid-feedback d-block" role="alert">--}}
                                    {{--<strong>{{ $errors->first('staff_cd') }}</strong>--}}
                                {{--</span>--}}
                        {{--@endif--}}
                        @include('Component.form.input',['class'=>'wd-300','filed'=>'staff_cd','required'=>true])
                    </div>
                    <div class="col-md-7 col-sm-12 row  h-100">
                        <div class="col row grid-col h-100">
                            <label class="col-6 required" for="adhibition_start_dt">適用開始日</label>
                            <div class="col-6 wrap-control">
                                <date-picker format="YYYY/MM/DD"
                                             placeholder=""
                                             v-model="adhibition_start_dt" v-cloak=""
                                             :lang="lang"
                                             :input-class="{{ $errors->has('adhibition_start_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                             v-on:change="onChangeDatepicker1"
                                             :value-type="'format'"
                                >
                                </date-picker>
                                <input type="hidden" class="form-control {{$errors->has('adhibition_start_dt')? 'is-invalid': ''}}" name="adhibition_start_dt" id="adhibition_start_dt" value="{{ $mStaff->adhibition_start_dt ?? old('adhibition_start_dt') }}" >
                            </div>
                        </div>
                        <div class="col row grid-col h-100">
                            <label class="col-6" for="adhibition_end_dt">適用開始日</label>
                            <div class="col-6 wrap-control">
                                <input type="text" readonly class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ $mStaff->adhibition_end_dt ?? config('params.adhibition_end_dt_default') }}">
                            </div>
                        </div>
                        @if ($errors->has('adhibition_start_dt'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('adhibition_start_dt') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="accounting_person_in_charge_last_nm">パスワード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="password" class="form-control {{$errors->has('password')? 'is-invalid': ''}}" id="password" name="password" v-on:keyup="convertKana($event, 'password')" value="{{ $mStaff->password ?? old('password') }}" maxlength="25">
                        </div>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="employment_pattern_id">雇用形態</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="employment_pattern_id" name="employment_pattern_id">
                                {{--@foreach($listPaymentMethod as $key => $value)--}}
                                {{--<option value="{{$key}}" {{$key==$mSupplier->payment_method_id || $key==old('payment_method_id') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                        @if ($errors->has('employment_pattern_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('employment_pattern_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="position_id">役職</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="position_id" name="position_id">
                                {{--@foreach($listPaymentMethod as $key => $value)--}}
                                {{--<option value="{{$key}}" {{$key==$mSupplier->payment_method_id || $key==old('payment_method_id') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                        @if ($errors->has('position_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('position_id') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="dealing_person_in_charge_last_nm">社員名（姓）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('last_nm')? 'is-invalid': ''}}" id="last_nm" name="last_nm" v-on:keyup="convertKana($event, 'last_nm')" value="{{ $mStaff->last_nm ?? old('last_nm') }}" maxlength="25">
                        </div>
                        @if ($errors->has('last_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('last_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="first_nm">社員カナ名（姓）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('first_nm')? 'is-invalid': ''}}" id="first_nm" name="first_nm" value="{{ $mStaff->first_nm ?? old('first_nm') }}" maxlength="50">
                        </div>
                        @if ($errors->has('first_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('first_nm') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="dealing_person_in_charge_first_nm">社員名（名）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('last_nm_kana')? 'is-invalid': ''}}" id="last_nm_kana" name="last_nm_kana" v-on:keyup="convertKana($event, 'last_nm_kana')" value="{{ $mStaff->last_nm_kana ?? old('last_nm_kana') }}" maxlength="25">
                        </div>
                        @if ($errors->has('last_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('last_nm_kana') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="dealing_person_in_charge_first_nm_kana">社員カナ名（名）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('first_nm_kana')? 'is-invalid': ''}}" id="first_nm_kana" name="first_nm_kana" value="{{ $mStaff->first_nm_kana ?? old('first_nm_kana') }}" maxlength="50">
                        </div>
                        @if ($errors->has('first_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('first_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_bank_cd">郵便番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('zip_cd')? 'is-invalid': ''}}" id="zip_cd" name="zip_cd" maxlength="5" value="{{ $mSupplier->zip_cd ?? old('zip_cd') }}">
                        </div>
                        @if ($errors->has('zip_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('zip_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <button class="btn btn-black" v-on:click="getAddrFromZipCode" type="button">〒 → 住所</button>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="prefectures_cd">都道府県</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="prefectures_cd" name="prefectures_cd">
                                {{--@foreach($listPaymentMethod as $key => $value)--}}
                                {{--<option value="{{$key}}" {{$key==$mSupplier->payment_method_id || $key==old('payment_method_id') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                        @if ($errors->has('prefectures_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('prefectures_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="address1">市区町村</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('address1')? 'is-invalid': ''}}" id="address1" name="address1" maxlength="30" value="{{ $mStaff->address1 ?? old('address1') }}">
                        </div>
                        @if ($errors->has('address1'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('address1') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_account_type">町名番地</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('address2')? 'is-invalid': ''}}" id="address2" name="address2" maxlength="10" value="{{ $mStaff->address2 ?? old('address2') }}">
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
                            <input type="text" class="form-control w-50 {{$errors->has('address3')? 'is-invalid': ''}}" id="address3" name="address3"  value="{{ $mStaff->address3 ?? old('address3') }}">
                        </div>
                        @if ($errors->has('address3'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('address3') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="landline_phone_number">固定電話番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('landline_phone_number')? 'is-invalid': ''}}" id="landline_phone_number" name="landline_phone_number" maxlength="30" value="{{ $mStaff->landline_phone_number ?? old('landline_phone_number') }}">
                        </div>
                        @if ($errors->has('landline_phone_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('landline_phone_number') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="cellular_phone_number">携帯電話番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('cellular_phone_number')? 'is-invalid': ''}}" id="cellular_phone_number" name="cellular_phone_number"  value="{{ $mStaff->cellular_phone_number ?? old('cellular_phone_number') }}">
                        </div>
                        @if ($errors->has('cellular_phone_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('cellular_phone_number') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="corp_cellular_phone_number">支給携帯電話番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('corp_cellular_phone_number')? 'is-invalid': ''}}" id="corp_cellular_phone_number" name="corp_cellular_phone_number" maxlength="30" value="{{ $mStaff->corp_cellular_phone_number ?? old('corp_cellular_phone_number') }}">
                        </div>
                        @if ($errors->has('corp_cellular_phone_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('corp_cellular_phone_number') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="notes">備考</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea class="form-control w-100 {{$errors->has('notes')? 'is-invalid': ''}}" rows="3" id="notes" name="notes" maxlength="50">{{ $mStaff->notes ?? old('notes') }}</textarea>
                        </div>
                        @if ($errors->has('notes'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('notes') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="sex_id">性別</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="sex_id" name="sex_id">
                                {{--@foreach($listPaymentMethod as $key => $value)--}}
                                {{--<option value="{{$key}}" {{$key==$mSupplier->sex_id || $key==old('sex_id') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                        @if ($errors->has('sex_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('sex_id') }}</strong>
                             </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="birthday">生年月日</label>
                        <div class="col-md-2 col-sm-2 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="birthday" v-cloak=""
                                         :lang="lang"
                                         :input-class="{{ $errors->has('birthday')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                         v-on:change="onChangeDatepicker1"
                                         :value-type="'format'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control   {{$errors->has('birthday')? 'is-invalid': ''}}" name="birthday" id="birthday" value="{{ $mStaff->birthday ?? old('birthday')}}" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="enter_date">入社年月日</label>
                        <div class="col-md-4 col-sm-4 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="enter_date" v-cloak=""
                                         :lang="lang"
                                         :input-class="{{ $errors->has('enter_date')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                         v-on:change="onChangeDatepicker1"
                                         :value-type="'format'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control   {{$errors->has('enter_date')? 'is-invalid': ''}}" name="enter_date" id="enter_date" value="{{ $mStaff->enter_date ?? old('enter_date')}}" >
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="retire_date">退社年月日</label>
                        <div class="col-md-2 col-sm-2 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="retire_date" v-cloak=""
                                         :lang="lang"
                                         :input-class="{{ $errors->has('retire_date')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                         v-on:change="onChangeDatepicker1"
                                         :value-type="'format'"
                            >
                            </date-picker>
                            <input type="hidden" class="form-control   {{$errors->has('retire_date')? 'is-invalid': ''}}" name="retire_date" id="retire_date" value="{{ $mStaff->retire_date ?? old('retire_date')}}" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="insurer_number">保険番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('insurer_number')? 'is-invalid': ''}}" id="insurer_number" name="insurer_number" value="{{ $mStaff->insurer_number ?? old('insurer_number') }}" maxlength="3">
                        </div>
                        @if ($errors->has('insurer_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('insurer_number') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="basic_pension_number">基礎年金番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75{{$errors->has('basic_pension_number')? 'is-invalid': ''}}" id="basic_pension_number" name="basic_pension_number" value="{{ $mStaff->basic_pension_number ?? old('basic_pension_number') }}" maxlength="50">
                        </div>
                        @if ($errors->has('basic_pension_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('basic_pension_number') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="person_insured_number">被保険者番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('person_insured_number')? 'is-invalid': ''}}" id="person_insured_number" name="person_insured_number"  value="{{ $mStaff->person_insured_number ?? old('person_insured_number') }}" maxlength="25">
                        </div>
                        @if ($errors->has('person_insured_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('person_insured_number') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="health_insurance_class">健康保険等級</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75{{$errors->has('health_insurance_class')? 'is-invalid': ''}}" id="health_insurance_class" name="health_insurance_class" value="{{ $mStaff->health_insurance_class ?? old('health_insurance_class') }}" maxlength="50">
                        </div>
                        @if ($errors->has('health_insurance_class'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('health_insurance_class') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="break-row-form"></div>
                    <div class="break-row-form"></div>

                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="welfare_annuity_class">厚生年金等級</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('welfare_annuity_class')? 'is-invalid': ''}}" id="welfare_annuity_class" name="welfare_annuity_class"  value="{{ $mStaff->welfare_annuity_class ?? old('welfare_annuity_class') }}">
                        </div>
                        @if ($errors->has('welfare_annuity_class'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('welfare_annuity_class') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="relocation_municipal_office_cd">市町村役場</label>
                        <div class="col-md-2 col-sm-2 wrap-control">
                            <label class="grid-form-label" for="relocation_municipal_office_cd">コード</label>
                            <select class="form-control w-75" id="relocation_municipal_office_cd" name="relocation_municipal_office_cd">
                                {{--@foreach($listPaymentMethod as $key => $value)--}}
                                {{--<option value="{{$key}}" {{$key==$mSupplier->payment_method_id || $key==old('payment_method_id') ? 'selected' : ''}}>{{$value}}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                        @if ($errors->has('relocation_municipal_office_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('relocation_municipal_office_cd') }}</strong>
                            </span>
                        @endif
                        <div class="col-md-6 col-sm-6 h-100 pl-0">
                            <label class="grid-form-label" for="">名称</label>
                            <input type="text"  class="form-control w-100" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ $mStaff->adhibition_end_dt ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-form">
                <div class="row">
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <button class="btn btn-collapse w-100">▼ 学歴</button>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="grid-form form-collapse">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 row grid-col">
                            <label class="col-md-2 col-sm-2" for="educational_background">最終学歴</label>
                            <div class="col-md-10 col-sm-10 wrap-control">
                                <input type="text" class="form-control w-75 {{$errors->has('educational_background')? 'is-invalid': ''}}" id="educational_background" name="educational_background" value="{{ $mStaff->educational_background ?? old('educational_background') }}" maxlength="25">
                            </div>
                            @if ($errors->has('educational_background'))
                                <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('educational_background') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="break-row-form"></div>
                        <div class="col-md-5 col-sm-12 row grid-col">
                            <label class="col-md-5 col-sm-5" for="educational_background_dt">最終学歴日付</label>
                            <div class="col-md-7 col-sm-7 wrap-control">
                                <date-picker format="YYYY/MM/DD"
                                             placeholder=""
                                             v-model="educational_background_dt" v-cloak=""
                                             :lang="lang"
                                             :input-class="{{ $errors->has('educational_background_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                             v-on:change="onChangeDatepicker1"
                                             :value-type="'format'"
                                >
                                </date-picker>
                                <input type="hidden" class="form-control   {{$errors->has('educational_background_dt')? 'is-invalid': ''}}" name="educational_background_dt" id="educational_background_dt" value="{{ $mStaff->educational_background_dt ?? old('educational_background_dt')}}" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <button class="btn btn-collapse w-100">▼ 前職経歴</button>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="">
                <div class="row form-collapse w-95">
                     <div class="col-md-10 col-sm-12 row grid-col">
                            <label class="col-md-2 col-sm-2" for="job_duties">職務内容</label>
                            <div class="col-md-10 col-sm-10 wrap-control">
                                <input type="text" class="form-control w-75 {{$errors->has('job_duties')? 'is-invalid': ''}}" id="job_duties" name="job_duties" value="{{ $mStaff->job_duties ?? old('job_duties') }}">
                            </div>
                            @if ($errors->has('job_duties'))
                                <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('job_duties') }}</strong>
                            </span>
                            @endif
                      </div>
                     <div class="break-row-form"></div>
                     <div class="col-md-5 col-sm-12 row grid-col h-100">
                            <label class="col-md-5 col-sm-5" for="staff_tenure_start_dt">在職期間（開始日）</label>
                            <div class="col-md-4 col-sm-4 wrap-control">
                                <date-picker format="YYYY/MM/DD"
                                             placeholder=""
                                             v-model="staff_tenure_start_dt" v-cloak=""
                                             :lang="lang"
                                             :input-class="{{ $errors->has('staff_tenure_start_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                             v-on:change="onChangeDatepicker1"
                                             :value-type="'format'"
                                >
                                </date-picker>
                                <input type="hidden" class="form-control   {{$errors->has('staff_tenure_start_dt')? 'is-invalid': ''}}" name="staff_tenure_start_dt" id="staff_tenure_start_dt" value="{{ $mStaff->staff_tenure_start_dt ?? old('staff_tenure_start_dt')}}" >
                            </div>
                      </div>
                     <div class="col-md-7 col-sm-12 row grid-col">
                            <label class="col-md-4 col-sm-4" for="staff_tenure_end_dt">在職期間（終了日）</label>
                            <div class="col-md-2 col-sm-2 wrap-control">
                                <date-picker format="YYYY/MM/DD"
                                             placeholder=""
                                             v-model="staff_tenure_end_dt" v-cloak=""
                                             :lang="lang"
                                             :input-class="{{ $errors->has('staff_tenure_end_dt')? "'form-control w-100 is-invalid'": "'form-control w-100'"}}"
                                             v-on:change="onChangeDatepicker1"
                                             :value-type="'format'"
                                >
                                </date-picker>
                                <input type="hidden" class="form-control {{$errors->has('staff_tenure_end_dt')? 'is-invalid': ''}}" name="staff_tenure_end_dt" id="staff_tenure_end_dt" value="{{ $mStaff->staff_tenure_end_dt ?? old('staff_tenure_end_dt')}}" >
                            </div>
                     </div>
                </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section("scripts")
    <script type="text/javascript" src="{{ mix('/assets/js/controller/staffs.js') }}" charset="utf-8"></script>
@endsection