<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$prefix.$filed) }}
    </label>
    @foreach($array as $key => $value)
    <div class="row">
        <div class="col-md-5 col-sm-12">
            <input {!! isset($attr_input) ? $attr_input:"" !!} type="checkbox" class="form-control" id="{!! $filed.$value->date_id  !!}" value="{!! $value->date_id !!}" name="{!! $filed !!}" @change="check($event)" index="{!! $value->date_id !!}">
            <label class="custom-label" for="{!! $filed.$value->date_id !!}">{!! $value->date_nm !!}</label>
        </div>
        <div class="d-flex col-md-7 col-sm-9">
            <div class="wd-100">
                <input {!! isset($attr_input) ? $attr_input:"" !!}
                       @input="input($event)"
                       type="tel"
                       v-bind:class="errors.{!! $filed !!}_value != undefined && errors.{!! $filed !!}_value[0][{!! $value->date_id !!}] ? 'form-control is-invalid':'' "
                       class="form-control"
                       id="{!! $filed !!}_value{!! $value->date_id !!}"
                       index="{!! $value->date_id !!}">
            </div>
            <div class="d-flex align-items-center pl-3">
                <span>{!! $value->contents1 !!}</span>
            </div>
        </div>
        <div class="break-row-form"></div>
    </div>
    <span v-cloak v-if="errors.{!! $filed !!}_value != undefined" class="message-error">@{{errors.{!! $filed !!}_value[0][{!! $value->date_id !!}]!=undefined ? errors.{!! $filed !!}_value[0][{!! $value->date_id !!}]: ''}}</span>
    @endforeach
    <div class="row">
        <div class="col-md-5 col-sm-12">
            <input {!! isset($attr_input) ? $attr_input:"" !!} type="checkbox" class="form-control" id="{!! $filed.'0' !!}" @change="check($event)" index="0" value="0" :checked="checkOther">
            <label class="custom-label" for="{!! $filed.'0' !!}">その他</label>
        </div>
        <div class="col-md-7 col-sm-12">
            <textarea {!! isset($attr_input) ? $attr_input:"" !!}
                      id="{!! $filed !!}_value0" index="0"
                      v-bind:class="errors.{!! $filed !!}_value != undefined && errors.{!! $filed !!}_value[0][0] ? 'form-control is-invalid':'' "
                      class="h-100" id="{!! $filed !!}" rows="3"
                      @input="input($event)"
            ></textarea>
        </div>
    </div>
    <span v-cloak v-if="errors.{!! $filed !!}_value != undefined" class="message-error">@{{errors.{!! $filed !!}_value[0][0]!=undefined ? errors.{!! $filed !!}_value[0][0]: ''}}</span>
</div>
<span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error">@{{errors.{!! $filed !!}[0]}}</span>
