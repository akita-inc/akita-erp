<div class="wrap-control-group d-flex align-content-center align-self-center {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$prefix.$filed) }}
    </label>
    <div class="custom-control custom-checkbox ml-2 form-control border-0">
        <input {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}" type="checkbox" class="custom-control-input" id="{!! $filed !!}">
        <label class="d-block custom-control-label" for="{!! $filed !!}">{!! isset($checkboxLabel) ? $checkboxLabel:"" !!}</label>
    </div>
</div>
