@extends('Layouts.app')
@section('title','得意先　新規追加')
@section('title_header','得意先　新規追加')
@section('style')
    <style>
        .textarea-payment{
            resize: none;
            border-radius: unset;
            height:86px!important;
            width:100%!important;
        }
        .btn-invoice-close{
            background-color: #c6ef9f;
            color: #000000;
            border-radius: unset;
            border: #c6ef9f;
        }
    </style>
@endsection
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
                            <input type="text" class="form-control w-50" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <div class="col-md-6 col-sm-12 row grid-col">
                            <label class="col-md-4 col-sm-4 required" for="input_mst_customers_cd">適用開始日</label>
                            <div class="col-md-8 col-sm-8 wrap-control">
                                <input type="text" class="form-control" id="input_mst_customers_cd">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 row grid-col">
                            <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">適用開始日</label>
                            <div class="col-md-8 col-sm-8 wrap-control">
                                <input type="text" class="form-control" id="input_mst_customers_cd">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 required" for="input_mst_customers_cd">得意先名略称</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 " for="input_mst_customers_cd">得意先略称カナ名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 " for="input_mst_customers_cd">得意先正式名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4 " for="input_mst_customers_cd">得意先正式カナ名</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">担当者名(姓）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">担当者名カナ（姓）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">担当者名(名）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">担当者名カナ（名）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">郵便番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <div class="col-md-4 col-sm-4 wrap-control">
                            <button class="btn btn-black w-75 h-100">〒 → 住所</button>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">都道府県</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select  class="form-control w-39" id="input_mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">市区町村</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-69" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">町名番地</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">建物等</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-69" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">電話番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">FAX番号</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <label class="col-md-2 col-sm-2" for="input_mst_customers_cd">WEBサイトアドレス</label>
                        <div class="col-md-10 col-sm-10 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">得意先カテゴリ小区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select  class="form-control w-100" id="input_mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">取得営業所</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-69" id="input_mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">請求に関する説明</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea type="text" class="form-control textarea-payment" id="input_mst_customers_cd">
                            </textarea>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">請求締日</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-25" id="input_mst_customers_cd">
                        </div>
                        <div class="break-row-form"></div>
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">入金予定月</label>
                        <div class="col-md-3 col-sm-3 wrap-control">
                            <select class="form-control w-75" id="input_mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">入金予定日</label>
                        <div class="col-md-1 col-sm-1 wrap-control">
                            <input type="text" class="form-control" id="input_mst_customers_cd">
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">入金予定方法</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="input_mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                        <div class="break-row-form"></div>
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">取引開始日</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-69" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">入金予定補足説明</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <textarea type="text" class="form-control textarea-payment" id="input_mst_customers_cd">
                            </textarea>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">消費税計算単位区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select  class="form-control w-50" id="input_mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">消費税端数処理区分</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <select class="form-control w-50" id="input_mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>
                    <div class="break-row-form"></div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">値引率（％）</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" id="input_mst_customers_cd">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 row grid-col">
                        <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">請求書の発行有無</label>
                        <div class="col-md-8 col-sm-8 wrap-control">
                            <input type="text" class="form-control w-50" id="input_mst_customers_cd" placeholder="□あり">
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-form">
                <div class="row">
                    <div class="col-md-12 col-sm-12 row grid-col">
                        <button type="text" class="form-control btn-invoice-close w-100" id="input_mst_customers_cd">
                            ▼請求書発行先
                        </button>
                    </div>
                    <div class="col-md-11 col-sm-11 row">
                        <div class="grid-form margin-form-child">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 row grid-col">
                                    <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">郵便番号</label>
                                    <div class="col-md-8 col-sm-8 wrap-control">
                                        <input type="text" class="form-control w-25" id="input_mst_customers_cd">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 row grid-col">
                                    <div class="col-md-4 col-sm-4 wrap-control">
                                        <button class="btn btn-black w-75 h-100">〒 → 住所</button>
                                    </div>
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12 row grid-col">
                                    <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">都道府県</label>
                                    <div class="col-md-8 col-sm-8 wrap-control">
                                        <select  class="form-control w-39" id="input_mst_customers_cd">
                                            <option> </option>
                                            <option> Example</option>
                                            <option> Example</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 row grid-col">
                                    <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">市区町村</label>
                                    <div class="col-md-8 col-sm-8 wrap-control">
                                        <input type="text" class="form-control w-69" id="input_mst_customers_cd">
                                    </div>
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12 row grid-col">
                                    <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">町名番地</label>
                                    <div class="col-md-8 col-sm-8 wrap-control">
                                        <input type="text" class="form-control" id="input_mst_customers_cd">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 row grid-col">
                                    <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">建物等</label>
                                    <div class="col-md-8 col-sm-8 wrap-control">
                                        <input type="text" class="form-control w-69" id="input_mst_customers_cd">
                                    </div>
                                </div>
                                <div class="break-row-form"></div>
                                <div class="col-md-6 col-sm-12 row grid-col">
                                    <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">電話番号</label>
                                    <div class="col-md-8 col-sm-8 wrap-control">
                                        <input type="text" class="form-control w-50" id="input_mst_customers_cd">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 row grid-col">
                                    <label class="col-md-4 col-sm-4" for="input_mst_customers_cd">FAX番号</label>
                                    <div class="col-md-8 col-sm-8 wrap-control">
                                        <input type="text" class="form-control w-50" id="input_mst_customers_cd">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 col-sm-1 row">
                        <div class="btn-group-plus-minus">
                            <button class="btn btn-black">
                                    -
                            </button>
                             <button class="btn btn-danger">
                                    +
                             </button>
                        </div>
                    </div>
            </div>
        </form>
    </div>
@endsection
@section("scripts")
    <script type="text/javascript" src="{{ mix('/assets/js/controller/customers.js') }}" charset="utf-8"></script>
@endsection
