@if(!empty($listWApprovalStatus))
    <div class="grid-form">
        <div class="row">
            @foreach($listWApprovalStatus as $item)
                <div class="wd-180 text-right font-weight-bold">{{$item->title}}</div>
                <div class="col-md-1 col-sm-12">{{$item->status}}</div>
                <div class="col-md-2 col-sm-12">{{$item->approval_date}}</div>
                <div class="col-md-7 col-sm-12">{{$item->send_back_reason}}</div>
                <div class="break-row-form"></div>
            @endforeach
        </div>
    </div>
@endif