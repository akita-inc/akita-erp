<div class="col-md-6 col-sm-12">
    @include('Component.form.select-vue',[
        'filed'=>'approval_levels',
        'array'=>$listWfLevel,
        'required'=>true,
        'filedId'=> $table.'_approval_levels_'.$index ,
        'filedMode'=>$filedMode1,
    ])
</div>
<div class="col-md-6 col-sm-12">
    @include('Component.form.select-vue',[
        'filed'=>'approval_kb',
        'array'=>$listWfApprovalIndicator,
        'required'=>true,
        'filedId'=> $table.'_approval_kb_'.$index ,
        'filedMode'=>$filedMode2,
    ])
</div>
<div class="break-row-form"></div>