<div class="modal" tabindex="-1" role="dialog" id="changePasswordModal" >
    <div class="modal-dialog modal-lg" role="document" style="max-width:700px;">
        <div class="modal-content">
            <div class="modal-header modal_header_custom">
                <h5 class="w-100 modal-title text-center">{{trans("common.modal.change_password.title")}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <div class="w-75">
                        <div class="text-danger">
                            {{trans('common.modal.change_password.warning')}}
                        </div>
                        <div v-bind:class="errors.password != undefined ? 'error-form':''">
                                <input {!! isset($attr_input) ? $attr_input:"" !!}
                                       v-model="field.password"
                                       type="password"
                                       name="password"
                                       minlength="6"
                                       v-bind:class="errors.password != undefined ? 'form-control is-invalid':'form-control' "
                                       class="form-control"
                                       id="password">
                        </div>
                    <span v-cloak v-if="errors.password != undefined" class="message-error" v-html="errors.password.join('<br />')" id="password-error"></span>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id="footer-modal">
                <button type="button" class="btn btn-primary w-25"  v-on:click="changePassword">{{trans("common.modal.change_password.update")}}</button>
            </div>
        </div>
    </div>
</div>