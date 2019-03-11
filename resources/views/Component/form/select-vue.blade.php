<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" v-bind:for="{!! $filedId !!}">
        {{ trans("customers.create.field.".$filed) }}
    </label>
    <select {!! isset($attr_input) ? $attr_input:"" !!}
            class="form-control"
            v-model="{!! $filedMode !!}"
            v-bind:id="{!! $filedId !!}">
        @if(isset($array) && !empty($array))
            @foreach($array as $key => $value)
                <option value="{!! $key !!}">{!! $value !!}</option>
            @endforeach
        @endif
    </select>
</div>
