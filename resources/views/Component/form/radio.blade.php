<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$prefix.$filed) }}
    </label>
    <div class="pl-3 {{isset($role) && $role!=1 ?  'disabled-bg' : ''}}">
    @foreach($array as $key=>$value)
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="{!! $filed.$key !!}" name="{!! $filed !!}" v-model="field.{!! $filed !!}" class="custom-control-input" value="{!!  $key!!}"  {!! isset($attr_input) ? $attr_input:"" !!}>
            <label class="custom-control-label custom-label {{isset($role) && $role!=1 ?  'disabled-bg' : ''}}" for="{!! $filed.$key !!}">{!! $value !!}</label>
        </div>
    @endforeach
    </div>
</div>
<span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error" v-html="errors.{!! $filed !!}.join('<br />')"></span>

