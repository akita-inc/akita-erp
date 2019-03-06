@extends('layouts.app')
@section('title','仕入先　新規追加')
@section('title_header','仕入先　新規追加')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    <div class="row row-xs" id="ctrSuppliersVl">
        <div class="sub-header">
            <div class="sub-header-line-one">
                <button class="btn btn-black">戻る</button>
            </div>
            <div class="sub-header-line-two">
                <button class="btn btn-primary btn-submit">登録</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col text-danger">
            *　は必須入力の項目です。
        </div>
    </div>
    <form>
        <div class="row">
            <div class="col ">
                <div class="form-row align-items-center block-item">
                    <div class="col-auto">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">仕入先コード</div>
                            </div>
                            <input type="text" class="form-control form-control-lg" id="inlineFormInputGroup">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">適用開始日</div>
                            </div>
                            <input type="text" class="form-control form-control-lg" id="inlineFormInputGroup">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">適用開始日</div>
                            </div>
                            <input type="text" disabled class="form-control form-control-lg" id="inlineFormInputGroup">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection