<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$table.".create.field.".$filed) }}
    </label>
    <input {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}" type="checkbox" class="form-control" id="{!! $filed !!}">
    <span for="{!! $filed !!}">{!! isset($checkboxLabel) ? $checkboxLabel:"" !!}</span>
</div>
