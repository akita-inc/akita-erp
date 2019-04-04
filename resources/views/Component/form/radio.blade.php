<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$prefix.$filed) }}
    </label>
    <div class="pl-3">
    @foreach($array as $key=>$value)
        <input type="radio" id="{!! $filed.$key !!}" name="{!! $filed !!}" v-model="field.{!! $filed !!}" class="form-control form-check-input" value="{!!  $key!!}">
        <label class="form-check-label custom-label" for="{!! $filed.$key !!}">{!! $value !!}</label>
    @endforeach
    </div>
</div>

