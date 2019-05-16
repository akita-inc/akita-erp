@extends('Layouts.app')
@section('title',trans("work_flow.list.title"))
@section('title_header',trans("work_flow.list.title"))
@section('content')
    @include('Layouts.alert')
        <div class="row row-xs" id="ctrWorkFlowListVl">
            <pulse-loader :loading="loading"></pulse-loader>
            <div class="sub-header">
                <div class="sub-header-line-one text-right">
                     <button class="btn btn-yellow" onclick="window.location.href= '{{route('work_flow.create')}}'">
                         {{trans('work_flow.list.search.btn_new_register')}}
                     </button>
                </div>
                <div class="sub-header-line-two p-t-30 frm-search-list">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 row">
                            <div class="col-md-2 col-list-search-f">
                                {{trans("work_flow.list.search.name")}}
                            </div>
                            <div class="col-md-4 grid-form-search">
                                <input type="text" v-model="fileSearch.name" name="name" id="name" class="form-control">
                            </div>
                            <div class="col-md-2 lh-38 padding-row-5">
                                <button class="btn btn-light m-auto w-100" type="button" v-on:click="clearCondition()" >
                                    {{trans('work_flow.list.search.btn_clear')}}
                                </button>
                            </div>
                            <div class="col-md-2 lh-38 text-right no-padding">
                                <button class="btn w-100 btn-primary" type="button" id="searchItems" v-on:click="getItems(1)">
                                    {{trans('common.button.search')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
            <div class="wrapper-table table-green w-50" v-cloak v-if="flagSearch">
                <table class="table table-striped table-bordered search-content">
                    <thead>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <th  v-on:click="sortList($event, '{{$field["sortBy"]}}')" id="th_{{$key}}" class="cursor-pointer {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("work_flow.list.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr  v-cloak v-for="item in items">
                        @foreach($fieldShowTable as $key => $field)
                            @if($key=='id')
                            <td>
                                <div class="cd-link text-center" v-on:click="checkIsExist(item.id)">{!! "@{{ item['$key'] }}" !!}</div>
                            </td>
                            @else
                            <td class="{{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                <p v-if="item['{{$key}}']">{!! "@{{item['$key']}}" !!}</p>
                                <p v-else>---</p>
                            </td>
                            @endif
                        @endforeach
                    </tr>
                    <tr v-cloak v-if="message !== ''">
                        <td colspan="10">@{{message}} </td>
                    </tr>
                    </tbody>
                </table>
                <div v-cloak class="mg-t-10">
                        @include("Layouts.pagination")
                </div>
            </div>
            <div class="col-md-3"></div>

        </div>
@endsection
@section("scripts")
    <script>
        var messages = [];
        messages["MSG05001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG05001'); ?>";
        messages["MSG06001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG06001'); ?>";
        messages["MSG02001"] = "<?php echo \Illuminate\Support\Facades\Lang::get('messages.MSG02001'); ?>";
        var date_now ='<?php echo date('Y/m/d'); ?>';
    </script>
    <script type="text/javascript" src="{{ mix('/assets/js/controller/work-flow-list.js') }}" charset="utf-8"></script>
@endsection