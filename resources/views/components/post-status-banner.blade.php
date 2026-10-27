@props(['post'])

@php
    $status = $post->status ?? 'draft';
@endphp

<div class="post-status-banner post-status-banner-{{ $status }} mb-4 shadow-xs" role="region" aria-label="Trạng thái bài viết">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <!-- Left: Status Icon & Message Content -->
        <div class="d-flex align-items-start align-items-sm-center gap-3">
            <!-- Icon Container in dedicated subtle circle -->
            <div class="status-banner-icon-box rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width: 40px; height: 40px;">
                @if($status === 'pending')
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif($status === 'rejected')
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @elseif($status === 'published')
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                @endif
            </div>

            <!-- Content Body -->
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-2 mb-0.5">
                    <h3 class="h6 fw-bold mb-0 status-banner-title" style="font-size: 14.5px;">
                        @if($status === 'pending')
                            Bài viết đang chờ duyệt
                        @elseif($status === 'rejected')
                            Bài viết bị từ chối / Cần chỉnh sửa
                        @elseif($status === 'published')
                            Bài viết đã được xuất bản
                        @else
                            Bài viết là bản nháp
                        @endif
                    </h3>
                </div>
                <p class="small mb-0 status-banner-desc" style="font-size: 13px; line-height: 1.45;">
                    @if($status === 'pending')
                        Bài viết đã được gửi đến Admin. Nội dung sẽ hiển thị công khai sau khi được phê duyệt.
                    @elseif($status === 'rejected')
                        Vui lòng rà soát lại tiêu đề, tóm tắt và nội dung bài viết theo tiêu chuẩn cộng đồng, sau đó nhấn <strong>"Gửi bài viết để Admin duyệt"</strong> ở góc phải để gửi lại cho Ban biên tập.
                    @elseif($status === 'published')
                        Bài viết đang hiển thị công khai trên CloudBlog. Mọi chỉnh sửa của bạn sẽ được cập nhật trực tiếp.
                    @else
                        Bản nháp chỉ hiển thị với bạn. Hãy hoàn thiện nội dung và gửi duyệt khi sẵn sàng.
                    @endif
                </p>

                @if($status === 'rejected' && !empty($post->rejection_reason))
                    <div class="mt-2.5 p-2.5 rounded-2 small font-monospace status-banner-reason">
                        <strong>Lý do từ Ban biên tập:</strong> {{ $post->rejection_reason }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Status Badge / Action -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0 align-self-start align-self-sm-center ms-auto ms-sm-0">
            @if($status === 'published')
                <a href="{{ route('posts.show', $post->slug) }}" target="_blank" rel="noopener noreferrer"
                   class="btn btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5 status-banner-badge text-decoration-none"
                   style="font-size: 12px;"
                   title="Xem bài viết trên trang công khai"
                   aria-label="Xem bài viết công khai">
                    <span>Xem bài viết</span>
                    <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            @else
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold status-banner-badge" style="font-size: 12px; letter-spacing: 0.02em;">
                    @if($status === 'pending')
                        Chờ duyệt
                    @elseif($status === 'rejected')
                        Bị từ chối
                    @else
                        Bản nháp
                    @endif
                </span>
            @endif
        </div>
    </div>
</div>

