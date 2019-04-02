<div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
    <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
        {{ trans(@$prefix.$filed) }}
    </label>
    @foreach($array as $key => $value)
    <div class="row">
        <div class="col-md-5 col-sm-12">
            <input {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}" type="checkbox" class="form-control" id="{!! $filed.$key !!}">
            <span for="{!! $filed !!}">{!! $value->date_nm !!}</span>
        </div>
        <div class="d-flex col-md-7 col-sm-9">
            <div class="wd-100">
                <input {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}"
                       type="text"
                       v-bind:class="errors.{!! $filed !!} != undefined ? 'form-control is-invalid':'' "
                       class="form-control"
                       id="{!! $filed !!}">
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
            <input {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}" type="checkbox" class="form-control" id="{!! $filed.$key !!}">
            <span for="{!! $filed !!}">その他</span>
        </div>
        <div class="col-md-7 col-sm-12">
            <textarea {!! isset($attr_input) ? $attr_input:"" !!} v-model="field.{!! $filed !!}" type="text"
                      v-bind:class="errors.{!! $filed !!} != undefined ? 'form-control is-invalid':'form-control' "
                      class="h-100" id="{!! $filed !!}" rows="3"></textarea>
        </div>
    </div>
</div>
