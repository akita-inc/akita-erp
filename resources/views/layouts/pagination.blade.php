@if ($paginator->hasPages())
<span>全{{ $paginator->lastPage() }}ページ：{{ $paginator->currentPage() }}ページ目　　全{{ $paginator->total() }}件：{{ $paginator->count() }}件表示</span><br>
    <ul class="pagination pagination-sm">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><a class="page-link">◀◀前</a></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">◀◀前</a></li>
            @endif
                @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                    <li class="paginate_button page-item {{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                        <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">後▶▶</a></li>
            @else
                    <li class="page-item disabled"><a class="page-link">後▶▶</a></li>
            @endif
    </ul>
@endif