<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" v-bind:for="{!! $filedId !!}">
        {{ trans(@$prefix.$filed) }}
    </label>
    <input {!! isset($attr_input) ? $attr_input:"" !!}
           v-model="{!! $filedMode !!}"
           v-bind:id="{!! $filedId !!}"
           type="text"
           class="form-control">
</div>
