<div v-bind:class="errors.{!! $filed !!} != undefined ? 'error-form':'' ">
    <div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
        <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
            {{ trans(@$prefix.$filed) }}
        </label>
        <select {!! isset($attr_input) ? $attr_input:"" !!}
                v-bind:class="errors.{!! $filed !!} != undefined ? 'form-control is-invalid':'form-control' "
                class="custom-select form-control" v-model="field.{!! $filed !!}" id="{!! $filed !!}">
            @if(isset($array) && !empty($array))
                @if(@$hiddenData)
                    <option value=""  data-foo="" >{{trans("common.select_option")}}</option>
                @endif
                @foreach($array as $key => $value)
                    @if(@$hiddenData)
                        <option value="{!! $value['id'] !!}"  data-foo="{{$value['date_nm']}}" >{!! $value["id"] !!}</option>
                    @else
                        <option value="{!! $key !!}">{!! $value !!}</option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>
    <span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error" v-html="errors.{!! $filed !!}.join('<br />')"></span>
</div>
