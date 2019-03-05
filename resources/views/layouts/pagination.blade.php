<span>全5ページ：1ページ目　　全48件：10件表示</span><br>
{{--@if ($paginator->hasPages())--}}
    <ul class="pagination pagination-sm">
{{--            @if ($paginator->onFirstPage())--}}
                <li class="page-item disabled"><a class="page-link">◀◀前</a></li>
            {{--@else--}}
                <li class="page-item"><a class="page-link" href="" rel="prev">◀◀前</a></li>
            {{--@endif--}}
            @for ($i = 1; $i <= 5; $i++)
                <li class="page-item {{ (1 == $i) ? ' active' : '' }}">
                    <a class="page-link" href="">{{ $i }}</a>
                </li>
            @endfor
{{--            @if ($paginator->hasMorePages())--}}
                <li class="page-item">
                    <a class="page-link" href="" rel="next">後▶▶</a></li>
            {{--@else--}}
                    <li class="page-item disabled"><a class="page-link">後▶▶</a></li>
            {{--@endif--}}
    </ul>
{{--@endif--}}