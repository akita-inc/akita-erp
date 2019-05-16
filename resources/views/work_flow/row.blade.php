<div class="col-md-6 col-sm-12">
    @include('Component.form.select',['filed'=>'approval_levels','array'=>$listWfLevel,'required'=>true])
</div>
<div class="col-md-6 col-sm-12">
    @include('Component.form.select',['filed'=>'approval_kb','array'=>$listWfApprovalIndicator,'required'=>true])
</div>
<div class="break-row-form"></div>