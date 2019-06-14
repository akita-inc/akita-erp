<div class="modal" tabindex="-1" role="dialog" id="detailsModal">
    <div class="modal-dialog modal-lg" role="document" style="max-width:1250px;">
        <div class="modal-content">
            <div class="modal-header modal_header_custom">
                <h5 class="w-100 modal-title text-center">{{trans("payment_histories.modal.title")}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <table class="table table-striped table-bordered search-content w-90">
                    <thead>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("payment_histories.list.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @switch($key)
                                    @case('actual_dw')
                                    @case('fee')
                                    @case('discount')
                                    @case('total_dw_amount')
                                    <span v-if="modal.payment_histories['actual_dw']==null || modal.payment_histories['discount']==null || modal.payment_histories['fee']==null || modal.payment_histories['total_dw_amount']==null">￥0</span>
                                    <span v-else>{!!"￥@{{ Number(modal.payment_histories['$key']).toLocaleString() }}" !!}</span>
                                    @break
                                    @default
                                    <span v-if="modal.payment_histories['{{$key}}']">{!! "@{{ modal.payment_histories['$key'] }}" !!}</span>
                                    <span v-else>---</span>
                                    @break
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>

                <div class="w-100 text-left"></div>
                <div class="wrapper">
                    <div class="scroll-horizontal">
                    <table class="table table-striped table-bordered search-content" v-if="modal.billing_headers.length>0">
                        <thead>
                            <tr>
                                @foreach($fieldShowTableDetails as $key => $field)
                                    <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("payment_histories.modal.table.".$key)}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-cloak v-for="item in modal.billing_headers">
                                @foreach($fieldShowTableDetails as $key => $field)
                                    <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak >
                                        @switch($key)
                                            @case('tax_included_amount')
                                            @case('consumption_tax')
                                            @case('total_fee')
                                            @case('total_fee')
                                            @case('last_payment_amount')
                                            @case('total_dw_amount')
                                            @case('fee')
                                            @case('discount')
                                            @case('deposit_balance')
                                            <span>{!! "￥@{{ Number(item['$key']).toLocaleString()}}" !!}</span>
                                            @break
                                            @default
                                            <span v-if="item['{{$key}}']">{!! "@{{ item['$key'] }}" !!}</span>
                                            <span v-else>---</span>
                                            @break
                                        @endswitch
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-delete"  {!! isset($attr_input) ? $attr_input:"" !!} data-dismiss="modal" v-on:click="openModalDelete(modal.payment_histories['dw_number'])">{{trans("payment_histories.list.search.button.delete")}}</button>
                <button type="button" class="btn btn-secondary"  data-dismiss="modal">{{trans("payment_histories.list.search.button.close")}}</button>
            </div>
        </div>
    </div>
</div>