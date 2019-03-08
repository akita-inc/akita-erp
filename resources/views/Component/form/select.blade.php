<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans("customers.create.field.".$filed) }}
    </label>
    <select {!! isset($attr_input) ? $attr_input:"" !!} class="form-control" v-model="field.{!! $filed !!}" id="{!! $filed !!}">
        @if(isset($array) && !empty($array))
            @foreach($array as $key => $value)
                <option value="{!! $key !!}">{!! $value !!}</option>
            @endforeach
        @endif
    </select>
</div>
