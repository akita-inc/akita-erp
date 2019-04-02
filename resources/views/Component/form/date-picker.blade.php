<div v-bind:class="errors.{!! $filed !!} != undefined ? 'error-form':'' ">
    <div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
        <label class="{!! isset($required) && $required ? "required":"" !!}">
            {{ trans(@$prefix.$filed) }}
        </label>
        <date-picker
            {!! isset($attr_input) ? $attr_input:"" !!}
            :lang='lang'
            id="{!! $filed !!}"
                     placeholder=""
                     format="YYYY/MM/DD"
                     value-type="format"
                     v-model="field.{!! $filed !!}"
                    :input-class="errors.{!! $filed !!} != undefined ? 'form-control w-100 is-invalid':'form-control w-100' "
                    @if(isset($role) && $role!=1) :disabled="true" @endif>
        </date-picker>
    </div>
    <span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error">@{{errors.{!! $filed !!}[0]}}</span>
</div>
