@if ($paginator->hasPages())
    <nav class="admin-posts-pagination__nav" role="navigation" aria-label="Phân trang danh sách bài viết">
        <div class="admin-posts-pagination__summary small text-theme-muted">
            Hiển thị
            <span class="fw-semibold text-theme">{{ $paginator->firstItem() }}</span>
            đến
            <span class="fw-semibold text-theme">{{ $paginator->lastItem() }}</span>
            trong tổng số
            <span class="fw-semibold text-theme">{{ $paginator->total() }}</span>
            bài viết
        </div>

        <div class="admin-posts-pagination__links">
            <ul class="pagination mb-0 admin-pagination-compact" aria-label="Các trang bài viết">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="Trang trước">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">&lsaquo;</a>
                    </li>
                @endif

                <li class="admin-pagination-compact__position">
                    <span class="admin-pagination-compact__current" aria-current="page">
                        <span class="visually-hidden">Trang hiện tại </span>{{ $paginator->currentPage() }}
                    </span>
                    <span class="admin-pagination-compact__total">
                        <span aria-hidden="true">/</span>
                        <span class="visually-hidden">trên tổng số </span>{{ $paginator->lastPage() }}
                        <span class="visually-hidden"> trang</span>
                    </span>
                </li>

                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau">&rsaquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="Trang sau">
                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </div>

    </nav>
@endif
