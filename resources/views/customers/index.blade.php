@extends('Layouts.app')
@section('title',trans("customers.title"))
@section('title_header',trans("customers.title"))
@section('content')
    <div class="row row-xs" id="ctrCustomersListVl">
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            <div class="sub-header-line-one text-right">
                <button class="btn btn-yellow">
                    {{trans('common.button.add')}}
                </button>
            </div>
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-4 col-sm-12 row">
                        <div class="col-md-3 padding-row-5 col-list-search-f">
                            {{trans("customers.list.search.customer")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_customers_cd">
                                {{trans("customers.list.search.code")}}
                            </label>
                            <input id="input_mst_customers_cd" class="form-control">
                        </div>
                        <div class="col-md-5 padding-row-5">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("customers.list.search.name")}}
                            </label>
                            <input id="input_mst_customers_name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-6 col-sm-12 lh-38">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="supplier_date" value="0">{{trans("customers.list.search.radio-all")}}
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="supplier_date" value="1" checked>{{trans("customers.list.search.radio-reference-date")}}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 input-group datepicker">
                            <input type="text" class="form-control input-calendar" name="reference_date" id="reference_date">
                            <div class="input-group-append">
                                <span class="input-group-text fa fa-calendar"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-5 lh-38 padding-row-5">
                            <button class="btn btn-black w-100">
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-7 lh-38 text-right no-padding">
                            <button class="btn btn-primary w-100">
                                {{trans('common.button.search')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-table">
            <table class="table table-bordered table-green">
                <thead>
                <tr>
                    @foreach($fieldShowTable as $key => $field)
                        <th class="{{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("customers.list.table.".$key)}}</th>
                    @endforeach
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <tr  v-cloak v-for="item in items" v-demo="{item:item}">
                        @foreach($fieldShowTable as $key => $field)
                            <td class="{{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @if( $key == 'mst_customers_cd' )
                                    <a href="">{!! "@{{ item['$key'] }}" !!}</a>
                                @else
                                    {!! "@{{ item['$key'] }}" !!}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
            <div v-cloak class="mg-t-10">
                @include("Layouts.pagination")
            </div>
        </div>
    </div>
@endsection
@section("scripts")
    <script type="text/javascript" src="{{ mix('/assets/js/controller/customers-list.js') }}" charset="utf-8"></script>
@endsection
