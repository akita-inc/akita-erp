<div v-bind:class="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!} != undefined
                && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index] != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!} != undefined ? 'error-form':'' ">
    <div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
        <label class="{!! isset($required) && $required ? "required":"" !!}" v-bind:for="{!! $filedId !!}">
            {{ trans(@$prefix.$filed) }}{!! isset($index) ? '<span v-html="'.$index.'"> ':"" !!}
        </label>
        <select {!! isset($attr_input) ? $attr_input:"" !!}
                v-bind:class="errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!} != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index] != undefined
               && errors.{!! isset($filedErrors) ? $filedErrors:"NotField" !!}[0][index].{!! $filed !!} != undefined ? 'form-control is-invalid':'form-control' "
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
    <span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error" v-html="errors.{!! $filed !!}.join('<br />')"></span>
</div>
