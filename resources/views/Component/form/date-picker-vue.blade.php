<div v-bind:class="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!} != undefined
                && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index] != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!} != undefined ? 'error-form':'' ">
    <div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
        <label class="{!! isset($required) && $required ? "required":"NotField" !!}" v-bind:for="{!! $filedId !!}">
            {{ trans(@$prefix.$filed) }}
        </label>
        <date-picker
                :lang='lang'
                v-bind:id="{!! $filedId !!}"
                placeholder=""
                format="YYYY/MM/DD"
                value-type="format"
                :input-class="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!} != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index] != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!} != undefined ? 'form-control w-100 is-invalid':'form-control w-100' "
                v-model="{!! $filedMode !!}"
                @if(isset($role) && $role!=1) :disabled="true" @endif>
        </date-picker>
    </div>
    <span v-cloak v-if="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!} != undefined
                && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index] != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!} != undefined" class="message-error" v-html="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!}.join('<br />')"></span>
</div>
