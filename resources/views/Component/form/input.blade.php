<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$label.".create.field.".$filed) }}
    </label>
    <input {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}" type="text" class="form-control" id="{!! $filed !!}">
</div>
