@extends('layouts.app')
@section('title','仕入先　新規追加')
@section('title_header','仕入先　新規追加')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    <div class="row row-xs" id="ctrSupplierrsVl">
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

            <div class="text-danger">*　は必須入力の項目です。</div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="mst_suppliers_cd">仕入先コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('mst_suppliers_cd')? 'is-invalid': ''}}" name="mst_suppliers_cd" id="mst_suppliers_cd" maxlength="5" value="{{ $mSupplier->mst_suppliers_cd ?? old('mst_suppliers_cd') }}">
                        </div>
                        @if ($errors->has('mst_suppliers_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('mst_suppliers_cd') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row h-100">
                        <div class="col row grid-col">
                            <label class="col-6 required" for="adhibition_start_dt">適用開始日</label>
                            <div class="col-6 wrap-control">
                                <div class="input-group date" data-provide="datepicker">
                                    <input type="text" class="form-control {{$errors->has('adhibition_start_dt')? 'is-invalid': ''}}" name="adhibition_start_dt" id="adhibition_start_dt" value="{{ $mSupplier->adhibition_start_dt ?? old('adhibition_start_dt') }}">
                                    <div class="input-group-addon">
                                        <span class="fa fa-calendar input-group-text" aria-hidden="true "></span>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('adhibition_start_dt'))
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('adhibition_start_dt') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col row grid-col h-100">
                            <label class="col-6" for="adhibition_end_dt">適用開始日</label>
                            <div class="col-6 wrap-control">
                                <input type="text" disabled class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ config('params.adhibition_end_dt_default') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="supplier_nm">仕入先名</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" id="supplier_nm" name="supplier_nm" v-on:keyup="convertKana($event, 'supplier_nm_kana')" value="{{ $mSupplier->supplier_nm ?? old('supplier_nm') }}" maxlength="200">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="supplier_nm_kana">仕入先カナ名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('supplier_nm_kana')? 'is-invalid': ''}}" id="supplier_nm_kana" name="supplier_nm_kana" value="{{ $mSupplier->supplier_nm_kana ?? old('supplier_nm_kana') }}" maxlength="200">
                        </div>
                        @if ($errors->has('supplier_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('supplier_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="supplier_nm_formal">仕入先正式名</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" id="supplier_nm_formal" name="supplier_nm_formal" v-on:keyup="convertKana($event, 'supplier_nm_kana_formal')" value="{{ $mSupplier->supplier_nm_kana_formal ?? old('supplier_nm_kana_formal') }}" maxlength="200">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="supplier_nm_kana_formal">仕入先正式カナ名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('supplier_nm_kana_formal')? 'is-invalid': ''}}" id="supplier_nm_kana_formal" name="supplier_nm_kana_formal" value="{{ $mSupplier->supplier_nm_kana_formal ?? old('supplier_nm_kana_formal') }}" maxlength="200">
                        </div>
                        @if ($errors->has('supplier_nm_kana_formal'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('supplier_nm_kana_formal') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="dealing_person_in_charge_last_nm">取引担当者名(姓）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" id="dealing_person_in_charge_last_nm" name="dealing_person_in_charge_last_nm" v-on:keyup="convertKana($event, 'dealing_person_in_charge_last_nm_kana')" value="{{ $mSupplier->dealing_person_in_charge_last_nm ?? old('dealing_person_in_charge_last_nm') }}" maxlength="25">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="dealing_person_in_charge_last_nm_kana">取引担当者名カナ（姓）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('dealing_person_in_charge_last_nm_kana')? 'is-invalid': ''}}" id="dealing_person_in_charge_last_nm_kana" name="dealing_person_in_charge_last_nm_kana" value="{{ $mSupplier->dealing_person_in_charge_last_nm_kana ?? old('dealing_person_in_charge_last_nm_kana') }}" maxlength="50">
                        </div>
                        @if ($errors->has('dealing_person_in_charge_last_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('dealing_person_in_charge_last_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="dealing_person_in_charge_first_nm">取引担当者名(名）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" id="dealing_person_in_charge_first_nm" name="dealing_person_in_charge_first_nm" v-on:keyup="convertKana($event, 'dealing_person_in_charge_first_nm_kana')" value="{{ $mSupplier->dealing_person_in_charge_first_nm ?? old('dealing_person_in_charge_first_nm') }}" maxlength="25">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="dealing_person_in_charge_first_nm_kana">取引担当者名カナ（名）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('dealing_person_in_charge_first_nm_kana')? 'is-invalid': ''}}" id="dealing_person_in_charge_first_nm_kana" name="dealing_person_in_charge_first_nm_kana" value="{{ $mSupplier->dealing_person_in_charge_first_nm_kana ?? old('dealing_person_in_charge_first_nm_kana') }}" maxlength="50">
                        </div>
                        @if ($errors->has('dealing_person_in_charge_first_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('dealing_person_in_charge_first_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="accounting_person_in_charge_last_nm">経理担当者名(姓）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" id="accounting_person_in_charge_last_nm" name="accounting_person_in_charge_last_nm" v-on:keyup="convertKana($event, 'accounting_person_in_charge_last_nm_kana')" value="{{ $mSupplier->accounting_person_in_charge_last_nm ?? old('accounting_person_in_charge_last_nm') }}" maxlength="25">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="accounting_person_in_charge_last_nm_kana">経理担当者名カナ（姓）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('accounting_person_in_charge_last_nm_kana')? 'is-invalid': ''}}" id="accounting_person_in_charge_last_nm_kana" name="accounting_person_in_charge_last_nm_kana" value="{{ $mSupplier->accounting_person_in_charge_last_nm_kana ?? old('accounting_person_in_charge_last_nm_kana') }}" maxlength="50">
                        </div>
                        @if ($errors->has('accounting_person_in_charge_last_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('accounting_person_in_charge_last_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="accounting_person_in_charge_last_nm">経理担当者名(名）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" id="accounting_person_in_charge_first_nm" name="accounting_person_in_charge_first_nm" v-on:keyup="convertKana($event, 'accounting_person_in_charge_first_nm_kana')" value="{{ $mSupplier->accounting_person_in_charge_first_nm ?? old('accounting_person_in_charge_first_nm') }}" maxlength="25">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="accounting_person_in_charge_first_nm_kana">経理担当者名カナ（名）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('accounting_person_in_charge_first_nm_kana')? 'is-invalid': ''}}" id="accounting_person_in_charge_first_nm_kana" name="accounting_person_in_charge_first_nm_kana" value="{{ $mSupplier->accounting_person_in_charge_first_nm_kana ?? old('accounting_person_in_charge_first_nm_kana') }}" maxlength="50">
                        </div>
                        @if ($errors->has('accounting_person_in_charge_first_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('accounting_person_in_charge_first_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="zip_cd">郵便番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('zip_cd')? 'is-invalid': ''}}" id="zip_cd" name="zip_cd" value="{{ $mSupplier->zip_cd ?? old('zip_cd') }}" maxlength="7">
                        </div>
                        @if ($errors->has('zip_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('zip_cd') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <button class="btn btn-black">〒 → 住所</button>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="prefectures_cd">都道府県</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="prefectures_cd" name="prefectures_cd">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="address1">市区町村</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75" id="address1" name="address1" value="{{ $mSupplier->address1 ?? old('address1') }}" maxlength="20">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="address2">町名番地</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" id="address2" name="address2" value="{{ $mSupplier->address2 ?? old('address2') }}" maxlength="20">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="address3">建物等</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75" id="address3" name="address3" value="{{ $mSupplier->address3 ?? old('address3') }}" maxlength="50">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="phone_number">電話番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('phone_number')? 'is-invalid': ''}}" id="phone_number" name="phone_number" value="{{ $mSupplier->phone_number ?? old('phone_number') }}" maxlength="20">
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
                            <input type="text" class="form-control w-50 {{$errors->has('fax_number')? 'is-invalid': ''}}" id="fax_number" name="fax_number" value="{{ $mSupplier->fax_number ?? old('fax_number') }}" maxlength="20">
                        </div>
                        @if ($errors->has('fax_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('fax_number') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <label class="col-md-2 col-sm-4" for="hp_url">WEBサイトアドレス</label>
                        <div class="col-md-10 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="hp_url" name="hp_url" value="{{ $mSupplier->hp_url ?? old('hp_url') }}" maxlength="2500">
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="bundle_dt">締日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('bundle_dt')? 'is-invalid': ''}}" id="bundle_dt" name="bundle_dt" value="{{ $mSupplier->bundle_dt ?? old('bundle_dt') }}" maxlength="2">
                        </div>
                        @if ($errors->has('bundle_dt'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('bundle_dt') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payday">支払日</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('payday')? 'is-invalid': ''}}" id="payday" name="payday" value="{{ $mSupplier->payday ?? old('payday') }}" maxlength="2">
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
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payment_day">支払予定日</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25 {{$errors->has('payment_day')? 'is-invalid': ''}}" id="payment_day" name="payment_day" value="{{ $mSupplier->payment_day ?? old('payment_day') }}" maxlength="2">
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
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>

                        <div class="break-row-form"></div>

                        <label class="col-md-5 col-sm-5" for="business_start_dt">取引開始日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <div class="input-group date w-50" data-provide="datepicker">
                                <input type="text" class="form-control" id="business_start_dt" name="business_start_dt" value="{{ $mSupplier->business_start_dt ?? old('business_start_dt') }}">
                                <div class="input-group-addon">
                                    <span class="fa fa-calendar input-group-text" aria-hidden="true "></span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="explanations_bill">支払いに関する説明</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea class="form-control w-100" rows="3" name="explanations_bill" id="explanations_bill" maxlength="100">{{ $mSupplier->explanations_bill ?? old('explanations_bill') }}</textarea>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="consumption_tax_calc_unit_id">消費税計算単位区分</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="consumption_tax_calc_unit_id" name="consumption_tax_calc_unit_id">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="rounding_method_id">消費税端数処理区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="rounding_method_id" name="rounding_method_id">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
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
                            <input type="text" class="form-control w-25" id="payment_bank_cd" name="payment_bank_cd" maxlength="4">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payment_bank_name">支払銀行名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="payment_bank_name" name="payment_bank_name" maxlength="30">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_branch_cd">支払銀行支店コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-25" id="payment_branch_cd" name="payment_branch_cd" maxlength="4">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payment_branch_name">支払銀行支店名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="payment_branch_name" name="payment_branch_name" maxlength="30">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col">
                        <label class="col-md-5 col-sm-5" for="payment_account_type">支払口座種別</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-25" id="payment_account_type" name="payment_account_type">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="payment_account_number">支払口座番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" id="payment_account_number" name="payment_account_number" maxlength="10">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="payment_account_holder">支払口座名義</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control" id="payment_account_holder" name="payment_account_holder" maxlength="30">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">備考</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea class="form-control w-100" rows="3" id="notes" name="notes" maxlength="50"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section("scripts")
    <script type="text/javascript" src="{{ mix('/assets/js/controller/suppliers.js') }}" charset="utf-8"></script>
@endsection