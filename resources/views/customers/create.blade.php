@extends('Layouts.app')
@section('title','得意先　新規追加')
@section('title_header','得意先　新規追加')
@section('content')
    <div class="row row-xs" id="ctrCustomersVl">
        <div class="sub-header">
            <div class="sub-header-line-one">
                <button class="btn btn-black">戻る</button>
            </div>
            <div class="sub-header-line-two">
                <button class="btn btn-primary btn-submit">登録</button>
            </div>
        </div>

        <form class="form-inline" role="form">
            <div class="text-danger">*　は必須入力の項目です。</div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 required" for="input_mst_customers_cd">得意先コード</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="email" class="form-control w-50" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <div class="col-md-6 col-sm-12 row grid-col">
                            <label class="col-md-4 col-sm-4 required" for="input_mst_customers_cd">適用開始日</label>
                            <div class="col-md-8 col-sm-8 wrap-control">
                                <input type="email" class="form-control" id="input_mst_customers_cd">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 row grid-col">
                            <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">適用開始日</label>
                            <div class="col-md-8 col-sm-8 wrap-control">
                                <input type="email" class="form-control" id="input_mst_customers_cd">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 required" for="input_mst_customers_cd">得意先コード</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="email" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 required" for="input_mst_customers_cd">得意先コード</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="email" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 required" for="input_mst_customers_cd">得意先コード</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="email" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 required" for="input_mst_customers_cd">得意先コード</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="email" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section("scripts")
    <script type="text/javascript" src="{{ mix('/assets/js/controller/customers.js') }}" charset="utf-8"></script>
@endsection
