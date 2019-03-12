<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}">
        {{ trans(@$label.".create.field.".$filed) }}
    </label>
    <date-picker
        :lang='lang'
        id="{!! $filed !!}"
                 placeholder=""
                 format="YYYY/MM/DD"
                 value-type="format"
                 v-model="field.{!! $filed !!}">
    </date-picker>
</div>
