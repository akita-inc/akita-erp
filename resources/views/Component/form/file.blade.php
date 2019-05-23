<div v-bind:class="errors.{!! $filed !!} != undefined ? 'error-form':'' ">
    <div class="wrap-control-group {!! isset($class) ? $class:"" !!}">
        <label class="{!! isset($required) && $required ? "required":"" !!}" for="{!! $filed !!}">
            {{ trans(@$prefix.$filed) }}
        </label>
        <input  type="text" v-bind:class="errors.{!! $filed !!} != undefined ? 'form-control is-invalid':'form-control' "
               class="form-control"
               v-model="image_{!! $filed !!}" readonly style="background-color: white; {{isset($role) && $role==2 ? ';background-color: #D9D9D9;opacity: 1;' : ''}}">
    </div>
    <span v-cloak v-if="errors.{!! $filed !!} != undefined" class="message-error" v-html="errors.{!! $filed !!}.join('<br />')"></span>
</div>
<div class="wrap-control-group">
    <input type="file" name="{!! $filed !!}" id="{!! $filed !!}" ref="{!! $filed !!}"
           class="input-file" {!! isset($attr_input) ? $attr_input:"" !!} ref="{!! $ref!!}" accept="image/jpeg, image/jpg, image/png"/>
    <label  class="label-file-image" for="{!! $filed !!}">{{trans($prefix.'btn_browse_license_picture')}}</label>
    @if(isset($item) && !empty($item) && !empty($item[$filed]))
        <modal-viewer-file
            :style="'float: left;margin-left: 120px;'"
            :class-element="'btnPreview{{$filed}}'"
            :header="'{{$item[$filed]}}'"
            :path="'{{isset($pathPreview) && !empty($pathPreview) ? $pathPreview:""}}'"
        >
            <button type="button" class="btn btn-primary" style="line-height: 25px;" >
                プレビュー
            </button>
        </modal-viewer-file>
    @endif
    @if(isset($attr_delete_path))
     <button  type="button" class="btn btn-dark float-right" {!! isset($attr_delete_path) ? $attr_delete_path:"" !!}>
         {{trans($prefix.'btn_delete_file_license_picture')}}</button>
   @endif
</div>
