@extends('layouts.app')
@section('title','仕入先　新規追加')
@section('title_header','仕入先　新規追加')
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
                <div class="col-lg-12 text-danger">
                    *　は必須入力の項目です。
                </div>
                <div class="col-lg-12 border border-dark">
                    *　は必須入力の項目です。
                </div>
            </div>

        </div>
    </div>
@endsection