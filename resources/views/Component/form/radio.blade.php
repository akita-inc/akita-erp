<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$prefix.$filed) }}
    </label>
    <div class="pl-3 {{isset($role) && $role!=1 ?  'disabled-bg' : ''}}">
    @foreach($array as $key=>$value)
        <input type="radio" id="{!! $filed.$key !!}" name="{!! $filed !!}" v-model="field.{!! $filed !!}" class="form-control form-check-input" value="{!!  $key!!}">
        <label class="form-check-label custom-label {{isset($role) && $role!=1 ?  'disabled-bg' : ''}}" for="{!! $filed.$key !!}">{!! $value !!}</label>
    @endforeach
    </div>
</div>
<span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error" v-html="errors.{!! $filed !!}.join('<br />')"></span>

