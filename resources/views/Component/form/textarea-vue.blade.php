<div v-bind:class="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!} != undefined
                && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index] != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!} != undefined ? 'error-form':''">
    <div class="wrap-control-group textarea {!! isset($class) ? $class:"" !!}">
        <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
            {{ trans(@$prefix.$filed) }}
        </label>
        <textarea
                v-model="{!! $filedMode !!}"
                v-bind:id="{!! $filedId !!}"
                {!! isset($attr_input) ? $attr_input:"" !!}
                type="text"
                v-bind:class="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!} != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index] != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!} != undefined ? 'form-control is-invalid':'form-control' "
                ></textarea>
    </div>
    <span v-cloak v-if="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!} != undefined
                && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index] != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!} != undefined" class="message-error" v-html="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!}.join('<br />')"></span>
</div>
