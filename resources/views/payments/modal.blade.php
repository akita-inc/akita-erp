<div class="modal" tabindex="-1" role="dialog" id="detailsModal">
    <div class="modal-dialog modal-lg" role="document" style="max-width:1250px;">
        <div class="modal-content">
            <div class="modal-header modal_header_custom">
                <h5 class="w-100 modal-title text-center">{{trans("payments.modal.title")}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <table class="table table-striped table-bordered search-content-no-pointer w-90">
                    <thead>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("payments.list.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($fieldShowTable as $key => $field)
                            <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @switch($key)
                                    @case('billing_amount')
                                    @case('consumption_tax')
                                    <span>￥{!! "@{{Number(modal.payment['$key']).toLocaleString()}}" !!}</span>
                                    @break
                                    @case('total_amount')
                                        <span>
                                            ￥{!! "@{{Number(parseInt(modal.payment.billing_amount) + parseInt(modal.payment.consumption_tax)).toLocaleString()}}" !!}
                                        </span>
                                    @break
                                    @default
                                    <span>{!! "@{{modal.payment['$key']}}" !!}</span>
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>

                <div class="w-100 text-left">{{trans("payments.modal.sub_title")}}</div>

                <table class="table table-striped table-bordered search-content-no-pointer">
                    <thead>
                    <tr>
                        @foreach($fieldShowTableDetails as $key => $field)
                            <th id="th_{{$key}}" class="align-top {{ isset($field["classTH"])?$field["classTH"]:"" }}">{{trans("payments.modal.table.".$key)}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-cloak v-for="item in modal.detail_info">
                        @foreach($fieldShowTableDetails as $key => $field)
                            <td class="text-center {{ isset($field["classTD"])?$field["classTD"]:"" }}" v-cloak>
                                @switch($key)
                                    @case('consumption_tax')
                                    @case('total_fee')
                                    @case('tax_included_amount')
                                    <p>{!!"￥@{{ Number(item['$key']).toLocaleString() }}" !!}</p>
                                    @break
                                    @default
                                    <p>{!! "@{{ item['$key'] }}" !!}</p>
                                    @break
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: none!important;">
                <button type="button" class="btn btn-secondary"  {!! isset($attr_input) ? $attr_input:"" !!} data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>