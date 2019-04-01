<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$prefix.$filed) }}
    </label>
    @foreach($array as $key => $value)
    <div class="row">
        <div class="col-md-5 col-sm-12">
            <input {!! isset($attr_input) ? $attr_input:"" !!} type="checkbox" class="form-control" id="{!! $filed.$value->date_id  !!}" value="{!! $value->date_id !!}" name="{!! $filed !!}" @change="check($event)" index="{!! $value->date_id !!}">
            <span for="{!! $filed.$value->date_id !!}">{!! $value->date_nm !!}</span>
        </div>
        <div class="d-flex col-md-7 col-sm-9">
            <div class="wd-100">
                <input {!! isset($attr_input) ? $attr_input:"" !!}
                       @change="input($event)"
                       type="text"
                       v-bind:class="errors.{!! $filed !!} != undefined ? 'form-control is-invalid':'' "
                       class="form-control"
                       id="{!! $filed !!}_value{!! $value->date_id !!}" index="{!! $value->date_id !!}">
            </div>
            <div class="d-flex align-items-center pl-3">
                <span>{!! $value->contents1 !!}</span>
            </div>
        </div>
        <div class="break-row-form"></div>
    </div>
    @endforeach
    <div class="row">
        <div class="col-md-5 col-sm-12">
            <input {!! isset($attr_input) ? $attr_input:"" !!} type="checkbox" class="form-control" id="{!! $filed.'0' !!}" @change="check($event)" index="0" value="0">
            <span for="{!! $filed !!}">その他</span>
        </div>
        <div class="col-md-7 col-sm-12">
            <textarea {!! isset($attr_input) ? $attr_input:"" !!}
                      id="{!! $filed !!}_value0" index="0"
                      v-bind:class="errors.{!! $filed !!} != undefined ? 'form-control is-invalid':'form-control' "
                      class="h-100" id="{!! $filed !!}" rows="3"></textarea>
        </div>
    </div>
</div>
