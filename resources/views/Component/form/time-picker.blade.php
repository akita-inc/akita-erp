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
                format="hh:mm A"
                v-model="field.{!! $filed !!}"
                :input-class="'form-control w-100'"
                :time-picker-options="{ start: '00:00', step: '00:30', end: '23:30' }"
                :minute-step=30
                type="time"
                @if(isset($role) && $role!=1) :disabled="true" @endif>
        </date-picker>
    </div>
    <span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error">@{{errors.{!! $filed !!}[0]}}</span>
</div>
