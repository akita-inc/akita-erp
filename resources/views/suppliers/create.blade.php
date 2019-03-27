@extends('Layouts.app')
@section('title',$mSupplier->id ? '仕入先　修正画面' : '仕入先　新規追加')
@section('title_header',$mSupplier->id ? '仕入先　修正画面' : '仕入先　新規追加')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    <div class="row row-xs" id="ctrSupplierrsVl">
        <form class="form-inline w-100" role="form" method="post" id="form1" action="{{$mSupplier->id ? route('suppliers.edit.post',['id' => $mSupplier->id, 'mode'=>'edit']): ''}}">
            @csrf
            <div class="sub-header">
                <div class="sub-header-line-one d-flex">
                    <div class="d-flex">
                        <button class="btn btn-black" type="button" onclick="window.history.back();">{{ trans("common.button.back") }}</button>
                    </div>
                    @if($mSupplier->id && $role==1)
                    <div class="d-flex ml-auto">
                        <button class="btn btn-danger text-white" v-on:click='deleteSupplier("{{$mSupplier->id}}")' type="button">{{ trans("common.button.delete") }}</button>
                    </div>
                    @endif
                </div>
                @if($mSupplier->id && $role==1)
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
                @elseif($role==1)
                    <div class="sub-header-line-two">
                        <button class="btn btn-primary btn-submit" type="submit">{{ trans("common.button.register") }}</button>
                    </div>
                @endif
            </div>
            @if($role!=1)
                <div class="alert alert-danger w-100 mt-2">
                    {{\Illuminate\Support\Facades\Lang::get('messages.MSG10006')}}
                </div>
            @endif
            @if($role==1 || ($role==2 && $mSupplier->id))
            @if($role==2)
            <fieldset disabled="disabled">
            @endif
            <div class="text-danger w-100">*　は必須入力の項目です。</div>
            <div class="w-100">
                @include('Layouts.alert')
                <input type="hidden" id="adhibition_start_dt" value="{{ old("adhibition_start_dt",$mSupplier->adhibition_start_dt ? : '') }}">
            </div>
            @if($mSupplier->id)
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5 required" for="mst_suppliers_cd">仕入先コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('mst_suppliers_cd')? 'is-invalid': ''}}" name="mst_suppliers_cd" id="mst_suppliers_cd" readonly maxlength="5" value="{{ old('mst_suppliers_cd',!is_null($mSupplier->mst_suppliers_cd) ? $mSupplier->mst_suppliers_cd :'') }}">
                        </div>
                        <span class="note">
                            ※編集中データをもとに、新しい適用期間のデータを作成したい場合は、適用開始日（新規用）を入力し、新規登録（履歴残し）ボタンを押してください。
                        </span>
                        @if ($errors->has('mst_suppliers_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('mst_suppliers_cd') }}</strong>
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
                                             @if($role!=1) :disabled="true" @endif
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
                                                 @if($role!=1) :disabled="true" @endif
                                    >
                                    </date-picker>
                                    <input type="hidden" id="adhibition_end_dt" value="{{ old('adhibition_end_dt',$mSupplier->adhibition_end_dt ? :'') }}">
                                @else
                                    <input type="text" readonly class="form-control" id="adhibition_end_dt" name="adhibition_end_dt" value="{{ str_replace('-', '/',  $mSupplier->adhibition_end_dt ?? config('params.adhibition_end_dt_default'))}}">
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
                                             @if($role!=1) :disabled="true" @endif
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
                            <label class="col-md-5 col-sm-5 required" for="mst_suppliers_cd">仕入先コード</label>
                            <div class="col-md-7 col-sm-7 wrap-control">
                                <input type="text" class="form-control w-50 {{$errors->has('mst_suppliers_cd')? 'is-invalid': ''}}" name="mst_suppliers_cd" id="mst_suppliers_cd" maxlength="5" value="{{ old('mst_suppliers_cd') }}">
                            </div>
                            @if ($errors->has('mst_suppliers_cd'))
                                <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('mst_suppliers_cd') }}</strong>
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
                                                 @if($role!=1) :disabled="true" @endif
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
                        <label class="col-md-5 col-sm-5 required" for="supplier_nm">仕入先名</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('supplier_nm')? 'is-invalid': ''}}" id="supplier_nm" name="supplier_nm" value="{{ old('supplier_nm',!is_null($mSupplier->supplier_nm) ? $mSupplier->supplier_nm :'') }}" maxlength="200" >
                        </div>
                        @if ($errors->has('supplier_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('supplier_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="supplier_nm_kana">仕入先カナ名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('supplier_nm_kana')? 'is-invalid': ''}}" id="supplier_nm_kana" name="supplier_nm_kana" value="{{ old('supplier_nm_kana',!is_null($mSupplier->supplier_nm_kana) ? $mSupplier->supplier_nm_kana :'') }}" maxlength="200">
                        </div>
                        @if ($errors->has('supplier_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('supplier_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="supplier_nm_formal">仕入先正式名</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('supplier_nm_formal')? 'is-invalid': ''}}" id="supplier_nm_formal" name="supplier_nm_formal" value="{{ old('supplier_nm_formal',!is_null($mSupplier->supplier_nm_formal) ? $mSupplier->supplier_nm_formal :'') }}" maxlength="200">
                        </div>
                        @if ($errors->has('supplier_nm_formal'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('supplier_nm_formal') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="supplier_nm_kana_formal">仕入先正式カナ名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('supplier_nm_kana_formal')? 'is-invalid': ''}}" id="supplier_nm_kana_formal" name="supplier_nm_kana_formal" value="{{ old('supplier_nm_kana_formal',!is_null($mSupplier->supplier_nm_kana_formal) ? $mSupplier->supplier_nm_kana_formal :'') }}" maxlength="200">
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="dealing_person_in_charge_last_nm">取引担当者名(姓）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('dealing_person_in_charge_last_nm')? 'is-invalid': ''}}" id="dealing_person_in_charge_last_nm" name="dealing_person_in_charge_last_nm"  value="{{ old('dealing_person_in_charge_last_nm',!is_null($mSupplier->dealing_person_in_charge_last_nm) ? $mSupplier->dealing_person_in_charge_last_nm :'') }}" maxlength="25">
                        </div>
                        @if ($errors->has('dealing_person_in_charge_last_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('dealing_person_in_charge_last_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="dealing_person_in_charge_last_nm_kana">取引担当者名カナ（姓）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('dealing_person_in_charge_last_nm_kana')? 'is-invalid': ''}}" id="dealing_person_in_charge_last_nm_kana" name="dealing_person_in_charge_last_nm_kana" value="{{ old('dealing_person_in_charge_last_nm_kana',!is_null($mSupplier->dealing_person_in_charge_last_nm_kana) ? $mSupplier->dealing_person_in_charge_last_nm_kana :'') }}" maxlength="50">
                        </div>
                        @if ($errors->has('dealing_person_in_charge_last_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('dealing_person_in_charge_last_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="dealing_person_in_charge_first_nm">取引担当者名(名）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('dealing_person_in_charge_first_nm')? 'is-invalid': ''}}" id="dealing_person_in_charge_first_nm" name="dealing_person_in_charge_first_nm"  value="{{ old('dealing_person_in_charge_first_nm', !is_null($mSupplier->dealing_person_in_charge_first_nm) ? $mSupplier->dealing_person_in_charge_first_nm : '')}}" maxlength="25">
                        </div>
                        @if ($errors->has('dealing_person_in_charge_first_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('dealing_person_in_charge_first_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="dealing_person_in_charge_first_nm_kana">取引担当者名カナ（名）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('dealing_person_in_charge_first_nm_kana')? 'is-invalid': ''}}" id="dealing_person_in_charge_first_nm_kana" name="dealing_person_in_charge_first_nm_kana" value="{{ old('dealing_person_in_charge_first_nm_kana', !is_null($mSupplier->dealing_person_in_charge_first_nm_kana) ? $mSupplier->dealing_person_in_charge_first_nm_kana : '')}}" maxlength="50">
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="accounting_person_in_charge_last_nm">経理担当者名(姓）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('accounting_person_in_charge_last_nm')? 'is-invalid': ''}}" id="accounting_person_in_charge_last_nm" name="accounting_person_in_charge_last_nm" value="{{ old('accounting_person_in_charge_last_nm', !is_null($mSupplier->accounting_person_in_charge_last_nm) ? $mSupplier->accounting_person_in_charge_last_nm : '')}}" maxlength="25">
                        </div>
                        @if ($errors->has('accounting_person_in_charge_last_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('accounting_person_in_charge_last_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="accounting_person_in_charge_last_nm_kana">経理担当者名カナ（姓）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('accounting_person_in_charge_last_nm_kana')? 'is-invalid': ''}}" id="accounting_person_in_charge_last_nm_kana" name="accounting_person_in_charge_last_nm_kana" value="{{ old('accounting_person_in_charge_last_nm_kana', !is_null($mSupplier->accounting_person_in_charge_last_nm_kana) ? $mSupplier->accounting_person_in_charge_last_nm_kana : '')}}" maxlength="50">
                        </div>
                        @if ($errors->has('accounting_person_in_charge_last_nm_kana'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('accounting_person_in_charge_last_nm_kana') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="accounting_person_in_charge_first_nm">経理担当者名(名）</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('accounting_person_in_charge_first_nm')? 'is-invalid': ''}}" id="accounting_person_in_charge_first_nm" name="accounting_person_in_charge_first_nm" value="{{ old('accounting_person_in_charge_first_nm', !is_null($mSupplier->accounting_person_in_charge_first_nm) ? $mSupplier->accounting_person_in_charge_first_nm : '')}}" maxlength="25">
                        </div>
                        @if ($errors->has('accounting_person_in_charge_first_nm'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('accounting_person_in_charge_first_nm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="accounting_person_in_charge_first_nm_kana">経理担当者名カナ（名）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('accounting_person_in_charge_first_nm_kana')? 'is-invalid': ''}}" id="accounting_person_in_charge_first_nm_kana" name="accounting_person_in_charge_first_nm_kana" value="{{ old('accounting_person_in_charge_first_nm_kana',!is_null($mSupplier->accounting_person_in_charge_first_nm_kana) ? $mSupplier->accounting_person_in_charge_first_nm_kana : '')}}" maxlength="50">
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
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="zip_cd">郵便番号</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('zip_cd')? 'is-invalid': ''}}" id="zip_cd" name="zip_cd" value="{{ old('zip_cd', !is_null($mSupplier->zip_cd) ? $mSupplier->zip_cd : '')}}" maxlength="7">
                        </div>
                        @if ($errors->has('zip_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('zip_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <button class="btn btn-black" v-on:click="getAddrFromZipCode" type="button">〒 → 住所</button>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="prefectures_cd">都道府県</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="prefectures_cd" name="prefectures_cd">
                                @foreach($listPrefecture as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('prefectures_cd', $key, $mSupplier->prefectures_cd)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="address1">市区町村</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('address1')? 'is-invalid': ''}}" id="address1" name="address1" value="{{ old('address1', !is_null($mSupplier->address1) ? $mSupplier->address1 : '')}}" maxlength="20">
                        </div>
                        @if ($errors->has('address1'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('address1') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="address2">町名番地</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control {{$errors->has('address2')? 'is-invalid': ''}}" id="address2" name="address2" value="{{ old('address2', !is_null($mSupplier->address2) ? $mSupplier->address2 : '')}}" maxlength="20">
                        </div>
                        @if ($errors->has('address2'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('address2') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="address3">建物等</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-75 {{$errors->has('address3')? 'is-invalid': ''}}" id="address3" name="address3" value="{{ old('address3', !is_null($mSupplier->address3) ? $mSupplier->address3 : '')}}" maxlength="50">
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
                            <input type="text" class="form-control w-75 {{$errors->has('phone_number')? 'is-invalid': ''}}" id="phone_number" name="phone_number" value="{{ old('phone_number', !is_null($mSupplier->phone_number) ? $mSupplier->phone_number : '')}}" maxlength="20">
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
                            <input type="text" class="form-control w-50 {{$errors->has('fax_number')? 'is-invalid': ''}}" id="fax_number" name="fax_number" value="{{ old('fax_number', !is_null($mSupplier->fax_number) ? $mSupplier->fax_number: '')}}" maxlength="20">
                        </div>
                        @if ($errors->has('fax_number'))
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('fax_number') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col h-100">
                        <label class="col-md-2 col-sm-4" for="hp_url">WEBサイトアドレス</label>
                        <div class="col-md-10 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('hp_url')? 'is-invalid': ''}}" id="hp_url" name="hp_url" value="{{ old('hp_url', !is_null($mSupplier->hp_url) ? $mSupplier->hp_url: '')}}" maxlength="2500">
                        </div>
                        @if ($errors->has('hp_url'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('hp_url') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="bundle_dt">締日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('bundle_dt')? 'is-invalid': ''}}" id="bundle_dt" name="bundle_dt" value="{{ old('bundle_dt', !is_null($mSupplier->bundle_dt) ? $mSupplier->bundle_dt: '')}}" maxlength="2">
                        </div>
                        @if ($errors->has('bundle_dt'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('bundle_dt') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="payday">支払日</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payday')? 'is-invalid': ''}}" id="payday" name="payday" value="{{ old('payday', !is_null($mSupplier->payday) ? $mSupplier->payday :'')}}" maxlength="2">
                        </div>
                        @if ($errors->has('payday'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payday') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="payment_month_id">支払予定月</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="payment_month_id" name="payment_month_id">
                                @foreach($listPaymentMonth as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('payment_month_id', $key, $mSupplier->payment_month_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="payment_day">支払予定日</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payment_day')? 'is-invalid': ''}}" id="payment_day" name="payment_day" value="{{ old('payment_day', !is_null($mSupplier->payment_day) ? $mSupplier->payment_day: '')}}" maxlength="2">
                        </div>
                        @if ($errors->has('payment_day'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_day') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="payment_method_id">支払予定方法</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="payment_method_id" name="payment_method_id">
                                @foreach($listPaymentMethod as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('payment_method_id', $key, $mSupplier->payment_method_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="break-row-form"></div>

                        <label class="col-md-5 col-sm-5" for="business_start_dt">取引開始日</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <date-picker format="YYYY/MM/DD"
                                         placeholder=""
                                         v-model="business_start_dt" v-cloak=""
                                         :lang="lang"
                                         :input-class="'form-control w-100'"
                                         :value-type="'format'"
                                         :input-name="'business_start_dt'"
                                         @if($role!=1) :disabled="true" @endif
                            >
                            </date-picker>
                            <input type="hidden" id="business_start_dt" value="{{ old('business_start_dt', $mSupplier->business_start_dt ? : '')}}" >
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="explanations_bill">支払いに関する説明</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea class="form-control w-100 {{$errors->has('explanations_bill')? 'is-invalid': ''}}" rows="3" name="explanations_bill" id="explanations_bill" maxlength="100">{{ old('explanations_bill', !is_null($mSupplier->explanations_bill) ? $mSupplier->explanations_bill :'' )}}</textarea>
                        </div>
                        @if ($errors->has('explanations_bill'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('explanations_bill') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="consumption_tax_calc_unit_id">消費税計算単位区分</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50" id="consumption_tax_calc_unit_id" name="consumption_tax_calc_unit_id">
                                @foreach($listConsumptionTaxCalcUnit as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('consumption_tax_calc_unit_id', $key, $mSupplier->consumption_tax_calc_unit_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="rounding_method_id">消費税端数処理区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="rounding_method_id" name="rounding_method_id">
                                @foreach($listRoundingMethod as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('rounding_method_id', $key, $mSupplier->rounding_method_id)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form mb-5">
                <div class="row">
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="payment_bank_cd">支払銀行コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payment_bank_cd')? 'is-invalid': ''}}" id="payment_bank_cd" name="payment_bank_cd" maxlength="4" value="{{ old('payment_bank_cd', !is_null($mSupplier->payment_bank_cd) ? $mSupplier->payment_bank_cd: '')}}">
                        </div>
                        @if ($errors->has('payment_bank_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_bank_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="payment_bank_name">支払銀行名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('payment_bank_name')? 'is-invalid': ''}}" id="payment_bank_name" name="payment_bank_name" maxlength="30" value="{{ old('payment_bank_name', !is_null($mSupplier->payment_bank_name) ? $mSupplier->payment_bank_name: '')}}">
                        </div>
                        @if ($errors->has('payment_bank_name'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_bank_name') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="payment_branch_cd">支払銀行支店コード</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payment_branch_cd')? 'is-invalid': ''}}" id="payment_branch_cd" name="payment_branch_cd" maxlength="4" value="{{ old('payment_branch_cd', !is_null($mSupplier->payment_branch_cd) ? $mSupplier->payment_branch_cd: '')}}">
                        </div>
                        @if ($errors->has('payment_branch_cd'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_branch_cd') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="payment_branch_name">支払銀行支店名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control {{$errors->has('payment_branch_name')? 'is-invalid': ''}}" id="payment_branch_name" name="payment_branch_name" maxlength="30" value="{{ old('payment_branch_name', !is_null($mSupplier->payment_branch_name) ? $mSupplier->payment_branch_name: '')}}">
                        </div>
                        @if ($errors->has('payment_branch_name'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_branch_name') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-5 col-sm-12 row grid-col h-100">
                        <label class="col-md-5 col-sm-5" for="payment_account_type">支払口座種別</label>
                        <div class="col-md-7 col-sm-7 wrap-control">
                            <select class="form-control w-50 " id="payment_account_type" name="payment_account_type">
                                @foreach($listPaymentAccountType as $key => $value)
                                    <option value="{{$key}}" {{Common::selectIfValue('payment_account_type', $key, $mSupplier->payment_account_type)}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="payment_account_number">支払口座番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50 {{$errors->has('payment_account_number')? 'is-invalid': ''}}" id="payment_account_number" name="payment_account_number" maxlength="10" value="{{ old('payment_account_number',!is_null($mSupplier->payment_account_number) ? $mSupplier->payment_account_number: '')}}">
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
                            <input type="text" class="form-control {{$errors->has('payment_account_holder')? 'is-invalid': ''}}" id="payment_account_holder" name="payment_account_holder" maxlength="30" value="{{ old('payment_account_holder',!is_null($mSupplier->payment_account_holder) ? $mSupplier->payment_account_holder: '')}}">
                        </div>
                        @if ($errors->has('payment_account_holder'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('payment_account_holder') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-7 col-sm-12 row grid-col h-100">
                        <label class="col-md-4 col-sm-4" for="notes">備考</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea class="form-control w-100 {{$errors->has('notes')? 'is-invalid': ''}}" rows="3" id="notes" name="notes" maxlength="50">{{ old('notes', !is_null($mSupplier->notes) ? $mSupplier->notes : '')}}</textarea>
                        </div>
                        @if ($errors->has('notes'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('notes') }}</strong>
                            </span>
                        @endif
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
    <script>
        var listRoute = "{{route('suppliers.list')}}";
        var deleteRoute = "{{route('suppliers.delete.post',['id' => $mSupplier->id])}}";
        var role = "{{$role}}";
        var messages = [];
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG07001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG07001'); ?>";
        messages["MSG07002"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG07002'); ?>";
        function registerHistoryLeft() {
            $('#form1').attr('action','{{route('suppliers.edit.post',['id' => $mSupplier->id, 'mode'=>'registerHistoryLeft'])}}');
            $('#form1').submit();
        }
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/suppliers.js') }}" charset="utf-8"></script>
@endsection