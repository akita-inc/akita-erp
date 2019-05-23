<div class="modal" tabindex="-1" role="dialog" id="searchStaffModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center ">
                <form class="form-inline w-100" role="form" id="searchStaffFrm">
                    <div class="row">
                        <div class="col-md-3 col-sm-12 text-right">
                            {{ trans('take_vacation.create.search.name') }}
                        </div>
                        <div class="col-md-5 col-sm-12">
                            <input v-model="search.name"
                                   type="text"
                                   class="form-control w-100"
                                   id="name">
                        </div>
                        <div class="break-row-form"></div>
                        <div class="col-md-3 col-sm-12 text-right">
                            {{ trans('take_vacation.create.search.mst_business_office_id') }}
                        </div>
                        <div class="col-md-5 col-sm-12">
                            <select class="form-control w-100"
                                    id="name"
                                    v-model="search.mst_business_office_id">
                                    @foreach($listBusinessOffices as $key => $value)
                                        <option value="{!! $key !!}">{!! $value !!}</option>
                                    @endforeach

                            </select>
                        </div>
                        <div class="col-md-3 col-sm-12" >
                            <button class="btn btn-outline-secondary" type="button" @click="searchStaff">{{ trans("common.button.search") }}</button>
                        </div>
                    </div>
                </form>
                <table class="table table-striped table-bordered search-content table-green mt-3" v-if="listStaffs.length>0">
                    <thead>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <th v-on:click="sortList($event, '{{$field["sortBy"]}}')" id="th_{{$key}}" class="cursor-pointer align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("take_vacation.create.modal.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr  v-cloak v-for="item in listStaffs">
                        @foreach($fieldShowTable as $key => $field)
                            <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @if($key=='staff_nm')
                                    <a href="javaScript:void(0);" @click="setEmailAddress(item)">{!!"@{{ item['$key'] }}" !!}</a>
                                @else
                                {!!"@{{ item['$key'] }}" !!}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
                <div class="w-100 mt-3" v-cloak v-if="message !== ''">@{{message}}</div>
            </div>
            <div class="modal-footer justify-content-center">

            </div>
        </div>
    </div>
</div>