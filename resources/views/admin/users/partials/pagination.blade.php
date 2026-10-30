@if ($paginator->hasPages())
    <nav class="admin-users-pagination__nav" role="navigation" aria-label="Phân trang danh sách người dùng">
        <div class="admin-users-pagination__summary small text-theme-muted">
            Hiển thị
            <span class="fw-semibold text-theme">{{ $paginator->firstItem() }}</span>
            đến
            <span class="fw-semibold text-theme">{{ $paginator->lastItem() }}</span>
            trong tổng số
            <span class="fw-semibold text-theme">{{ $paginator->total() }}</span>
            kết quả
        </div>

        <div class="admin-users-pagination__links">
            <ul class="pagination mb-0" aria-label="Các trang">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">&lsaquo;</a>
                    </li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau">&rsaquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
