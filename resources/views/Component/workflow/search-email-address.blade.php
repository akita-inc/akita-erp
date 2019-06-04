@if($mode=='reference')
    <fieldset disabled="disabled" class="w-100">
        @endif
        <div class="grid-form">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="wrap-control-group">
                        <label for="search_vehicle">{{ $label }}</label>
                        <div class="row" v-cloak v-for="(item,index) in  field.wf_additional_notice" style="margin-bottom: 10px !important;">
                            <div class="col-md-1 col-sm-12" >
                                <button class="btn btn-outline-secondary" type="button" @click="openModal(index)" :disabled="field.mode =='approval' && typeof listWfAdditionalNoticeDB[index] !='undefined'">{{ trans("common.button.search") }}</button>
                            </div>
                            <div class="col-md-11 col-sm-12">
                                <input v-model="item.email_address"
                                       type="email"
                                       class="form-control w-100"
                                       :id="'email_address'+index"
                                       maxlength="300"
                                       v-bind:class="errors.wf_additional_notice!= undefined && errors.wf_additional_notice[0][index]!= undefined ? 'form-control is-invalid':'form-control' "
                                       :disabled="field.mode =='approval' && typeof listWfAdditionalNoticeDB[index] !='undefined'"
                                >
                                <span v-cloak v-if="errors.wf_additional_notice != undefined" class="message-error" v-html="errors.wf_additional_notice[0][index]"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 col-sm-12"></div>
                            <div class="col-md-11 col-sm-12">
                                <button class="btn btn-outline-secondary" type="button" @click="addRow" v-cloak v-if="field.mode!= 'reference'"> {{ trans("common.button.add_row") }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($mode=='reference')
    </fieldset>
@endif