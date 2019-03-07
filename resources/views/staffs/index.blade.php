@extends('Layouts.app')
@section('title',trans("staffs.title"))
@section('title_header',trans("staffs.title"))
@section('content')
    <div class="row row-xs" id="ctrStaffsListVl">
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
                        <div class="col-md-2 padding-row-5 col-list-search-f">
                            {{trans("customers.list.search.customer")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_customers_cd">
                                {{trans("customers.list.search.code")}}
                            </label>
                            <select class="form-control dropdown-list" name="mst_customers_cd"  id="input_mst_customers_cd"  v-model="fileSearch.mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                        <div class="col-md-6 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_customers_name">
                                {{trans("customers.list.search.name")}}
                            </label>
                            <select class="form-control dropdown-list" name="mst_customers_cd"  id="input_mst_customers_cd"  v-model="fileSearch.mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-4 col-sm-12 lh-38">
                            所属会社
                        </div>
                        <div class="col-md-8  col-sm-12 input-group ">
                            <select class="form-control dropdown-list" name="mst_customers_cd"  id="input_mst_customers_cd"  v-model="fileSearch.mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-4 padding-row-5 lh-38">
                            所属会社
                        </div>
                        <div class="col-md-8 padding-row-0 input-group ">
                            <select class="form-control dropdown-list" name="mst_customers_cd"  id="input_mst_customers_cd"  v-model="fileSearch.mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="break-row-form"></div>
                <div class="row">
                    <div class="col-md-4 col-sm-12 row">
                        <div class="col-md-2 padding-row-5 col-list-search-f">
                            {{trans("customers.list.search.customer")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <select class="form-control dropdown-list" name="mst_customers_cd"  id="input_mst_customers_cd"  v-model="fileSearch.mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                        <div class="col-md-2 padding-row-5 col-list-search-f">
                            {{trans("customers.list.search.customer")}}
                        </div>
                        <div class="col-md-4 padding-row-5 grid-form-search">
                            <select class="form-control dropdown-list" name="mst_customers_cd"  id="input_mst_customers_cd"  v-model="fileSearch.mst_customers_cd">
                                <option> </option>
                                <option> Example</option>
                                <option> Example</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 row">
                        <div class="col-md-6 col-sm-12 lh-38">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="status" value="0" v-model="fileSearch.status">{{trans("staffs.list.search.radio-all")}}
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="status" value="1" v-model="fileSearch.status" >{{trans("staffs.list.search.radio-reference-date")}}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 input-group datepicker">
                            <input  class="form-control input-calendar" name="reference_date" id="reference_date" v-model="fileSearch.reference_date">
                            <div class="input-group-append">
                                <span class="input-group-text fa fa-calendar"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-5 lh-38 padding-row-5">
                            <button class="btn btn-black w-100" >
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-7 lh-38 text-right no-padding">
                            <button class="btn btn-primary w-100" v-on:click="getItems(pagination.current_page)">
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
