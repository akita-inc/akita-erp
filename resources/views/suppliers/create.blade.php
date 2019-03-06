@extends('layouts.app')
@section('title','仕入先　新規追加')
@section('title_header','仕入先　新規追加')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/supplier/add.css') }}">
@endsection
@section('content')
    <div class="container-fluid pt-5">
        <div class="content" id="content-body">
            <div class="row">
                <div class="col">
                    <div class="search-form ">
                        <div class="row justify-content-start">
                            <button class="btn btn-dark" type="button">戻る</button>
                        </div>
                        <div class="row justify-content-center">
                            <button class="btn btn-search" style="background-color: #0070C0" type="submit">登録</button>
                        </div>
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
        </div>
    </div>
@endsection