<div v-bind:class="errors.{!! $filed !!} != undefined ? 'error-form':'' ">
    <div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
        <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
            {{ trans(@$prefix.$filed) }}
        </label>
        <input {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}"
               type="text"
               v-bind:class="errors.{!! $filed !!} != undefined ? 'form-control is-invalid':'form-control' "
               class="form-control"
               id="{!! $filed !!}">
    </div>
    <span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error" v-html="errors.{!! $filed !!}.join('<br />')"></span>
</div>
