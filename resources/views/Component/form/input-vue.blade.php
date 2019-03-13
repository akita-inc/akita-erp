<div v-bind:class="errors.{!! $filed !!} != undefined ? 'error-form':'' ">
    <div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
        <label class="{!! isset($required) && $required ? "required":"" !!}" v-bind:for="{!! $filedId !!}">
            {{ trans(@$prefix.$filed) }}
        </label>
        <input {!! isset($attr_input) ? $attr_input:"" !!}
               v-model="{!! $filedMode !!}"
               v-bind:id="{!! $filedId !!}"
               type="text"
               v-bind:class="errors.{!! $filed !!} != undefined ? 'form-control is-invalid':'form-control' "
               class="form-control">
    </div>
    <span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error">@{{errors.{!! $filed !!}[0]}}</span>
</div>
