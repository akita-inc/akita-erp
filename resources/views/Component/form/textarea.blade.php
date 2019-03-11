<div class="wrap-control-group textarea {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans("customers.create.field.".$filed) }}
    </label>
    <textarea {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}" type="text" class="form-control" id="{!! $filed !!}"></textarea>
</div>
