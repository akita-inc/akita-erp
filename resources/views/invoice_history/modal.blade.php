<div class="modal" tabindex="-1" role="dialog" id="detailsModal">
    <div class="modal-dialog modal-lg" role="document" style="max-width:1250px;">
        <div class="modal-content">
            <div class="modal-header modal_header_custom">
                <h5 class="w-100 modal-title text-center">{{trans("invoice_history.modal.title")}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <table class="table table-striped table-bordered search-content w-90">
                    <thead>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("invoice_history.list.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @switch($key)
                                    @case('tax_included_amount')
                                    @case('total_fee')
                                    @case('consumption_tax')
                                    @case('payment_amount')
                                    @case('payment_remaining')
                                    <span>{!!"￥@{{ modal.invoice['$key'] }}" !!}</span>
                                    @break
                                    @default
                                    <span v-if="modal.invoice['{{$key}}']">{!! "@{{ modal.invoice['$key'] }}" !!}</span>
                                    <span v-else>---</span>
                                    @break
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>

                <div class="w-100 text-left">{{trans("invoice_history.modal.sub_title")}}</div>

                <table class="table table-striped table-bordered search-content">
                    <thead>
                    <tr>
                        @foreach($fieldShowTableDetails as $key => $field)
                            <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("invoice_history.modal.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-cloak v-for="item in modal.sale_info">
                        @foreach($fieldShowTableDetails as $key => $field)
                            <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @switch($key)
                                    @case('consumption_tax')
                                    @case('total_fee')
                                    @case('tax_included_amount')
                                    <span>{!!"￥@{{ item['$key'] }}" !!}</span>
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
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger"  {!! isset($attr_input) ? $attr_input:"" !!} v-on:click="openModalDelete(modal.invoice.invoice_number)" v-if="modal.sale_info.length > 0 && modal.sale_info[0].count_payment_histories == 0">{{trans("invoice_history.list.search.button.delete")}}</button>
                <button type="button" class="btn btn-secondary"  {!! isset($attr_input) ? $attr_input:"" !!} data-dismiss="modal">{{trans("invoice_history.list.search.button.close")}}</button>
            </div>
        </div>
    </div>
</div>