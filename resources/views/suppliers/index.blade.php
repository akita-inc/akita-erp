@extends('Layouts.app')
@section('title',trans("suppliers.title"))
@section('title_header',trans("suppliers.title"))
@section('style')
    <link rel="stylesheet" href="{{ asset('css/search-list.css') }}">
@endsection
@section('content')
    @php $accessible_kb = \Illuminate\Support\Facades\Session::get('suppliers_accessible_kb'); @endphp
    @if ($accessible_kb == 9)
        <div class="alert alert-danger w-100 mt-2">
            {{\Illuminate\Support\Facades\Lang::get('messages.MSG10006')}}
        </div>
    @else
    <div id="ctrSuppliersListVl">
        {{ Breadcrumbs::render('suppliers_list') }}
        @include('Layouts.alert')
        <pulse-loader :loading="loading"></pulse-loader>
        <div class="sub-header">
            @if ($accessible_kb == 1)
            <div class="sub-header-line-one text-right">
                <button class="btn btn-yellow" onclick="window.location.href= '{{route('suppliers.create')}}'">
                    {{trans('common.button.add')}}
                </button>
            </div>
            @endif
            <div class="sub-header-line-two p-t-30 frm-search-list">
                <div class="row">
                    <div class="col-md-9 col-sm-12 row">
                        <div class="col-md-1 padding-row-5 col-list-search-f">
                            {{trans("suppliers.list.search.customer")}}
                        </div>
                        <div class="col-md-2 padding-row-5 grid-form-search">
                            <label class="grid-form-search-label" for="input_mst_suppliers_cd">
                                {{trans("suppliers.list.search.code")}}
                            </label>
                            <input type="text" v-model="fieldSearch.mst_suppliers_cd" name="supplier_cd" id="supplier_cd" class="form-control" placeholder="{{trans("suppliers.list.search.code")}}">
                        </div>
                        <div class="col-md-3 padding-row-5">
                            <label class="grid-form-search-label" for="input_mst_suppliers_name">
                                {{trans("suppliers.list.search.name")}}
                            </label>
                            <input type="text" v-model="fieldSearch.supplier_nm" name="supplier_nm" id="supplier_nm" class="form-control" placeholder="{{trans("suppliers.list.search.name")}}">
                        </div>
                        <div class="col-md-3 padding-row-5">
                            <label class="grid-form-search-label" for="input_mst_suppliers_name_kana_formal">
                                {{trans("suppliers.list.search.name_kana")}}
                            </label>
                            <input type="text" v-model="fieldSearch.supplier_nm_kana_formal" name="supplier_nm_kana_formal" id="supplier_nm_kana_formal" class="form-control" placeholder="{{trans("suppliers.list.search.name_kana")}}">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 row">
                        <div class="col-md-5 lh-38 padding-row-5">
                            <button class="btn btn-black w-100" type="button" v-on:click="clearCondition()" >
                                {{trans('common.button.condition-clear')}}
                            </button>
                        </div>
                        <div class="col-md-7 lh-38 text-right no-padding">
                            <button class="btn w-100 btn-primary" type="button" v-on:click="getItems(1)">
                                {{trans('common.button.search')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-table Scroll">
            <table class="table table-striped table-bordered search-content mb-3">
                <thead>
                <tr>
                    <th v-on:click="sortList($event, 'mst_suppliers_cd')" id="th_mst_suppliers_cd" class="wd-100">{{trans('suppliers.list.table.mst_suppliers_cd')}}</th>
                    <th v-on:click="sortList($event, 'supplier_nm_kana')" id="th_supplier_nm">{{trans('suppliers.list.table.supplier_nm')}}</th>
                    <th v-on:click="sortList($event, 'street_address')" id="th_street_address">{{trans('suppliers.list.table.street_address')}}</th>
                    <th v-on:click="sortList($event, 'explanations_bill')" id="th_explanations_bill">{{trans('suppliers.list.table.explanations_bill')}}</th>
                    <th v-on:click="sortList($event, 'modified_at')" id="th_modified_at" class="wd-120">{{trans('suppliers.list.table.modified_at')}}</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-cloak v-for="item in items.data">
                        <td>
                            <div class="cd-link" v-on:click="checkIsExist(item.id)">{!! "@{{ item['mst_suppliers_cd'] }}" !!}</div>
                        </td>
                        <td>
                            <span class="xsmall">{!! "@{{ item['supplier_nm_kana'] }}" !!}</span>
                            <br v-if="item['supplier_nm_kana']">
                            <span>{!! "@{{ item['supplier_nm'] }}" !!}</span>
                        </td>
                        <td>{!! "@{{ item['street_address'] }}" !!}</td>
                        <td class="td-nl2br">{!! "@{{ item['explanations_bill'] }}" !!}</td>
                        <td class="text-center">{!! "@{{ item['modified_at'] }}" !!}</td>
                    </tr>
                    <tr v-cloak v-if="message !== ''">
                        <td colspan="8">@{{message}} </td>
                    </tr>

                </tbody>
            </table>

            @include("Layouts.pagination")
        </div>
    </div>
    @endif
@stop
@section('scripts')
    <script>
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
        var date_now ='<?php echo date('Y/m/d'); ?>';
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/suppliers-list.js') }}" charset="utf-8"></script>
@endsection
