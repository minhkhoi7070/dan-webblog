<div id="feed-results" tabindex="-1" aria-label="Bảng danh sách người dùng"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $users->total() }}">

    <!-- Users Database Table & Mobile Card View Container -->
    <div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4 admin-users-table-card">
        <!-- Desktop Table View (>= 768px Tablet & Desktop) -->
        <div class="admin-users-table-scroll-tools" data-table-scroll-tools>
            <p id="admin-users-table-scroll-hint" class="admin-users-table-scroll-hint small text-theme-muted mb-0">
                Cuộn ngang để xem các cột còn lại.
            </p>
            <div class="admin-users-table-scroll-controls" role="group" aria-label="Điều khiển cuộn ngang bảng">
                <button type="button" class="btn btn-sm btn-outline-theme" data-table-scroll="left" aria-label="Cuộn bảng sang trái" disabled>
                    <span aria-hidden="true">&lsaquo;</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-theme" data-table-scroll="right" aria-label="Cuộn bảng sang phải" disabled>
                    <span aria-hidden="true">&rsaquo;</span>
                </button>
            </div>
        </div>
        <div class="table-responsive d-none d-md-block admin-users-table-scroll" role="region" tabindex="0" aria-label="Bảng người dùng có thể cuộn ngang" aria-describedby="admin-users-table-scroll-hint">
            <table class="table table-hover align-middle mb-0 admin-users-table" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
                <thead class="border-theme-bottom bg-surface-2">
                    <tr>
                        <th class="ps-3 ps-sm-4 py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Người dùng</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Vai trò</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Bài viết</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Bình luận</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Trạng thái</th>
                        <th class="pe-3 pe-sm-4 py-3 text-center label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme">
                    @forelse($users as $user)
                        <tr>
                            <!-- User Identity -->
                            <td class="ps-3 ps-sm-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <x-avatar :user="$user" size="sm" class="border border-theme flex-shrink-0" />
                                    <div class="min-w-0" style="min-width: 0; max-width: 280px;">
                                        <a href="{{ route('admin.users.show', $user) }}" class="fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block small" style="font-size: 13.5px;">
                                            {{ $user->name }}
                                        </a>
                                        <p class="small text-theme-muted mb-0 text-truncate" style="font-size: 11.5px;">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-3 text-start">
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill text-uppercase" style="font-size: 10.5px;">
                                        Admin
                                    </span>
                                @elseif($user->role === 'author')
                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill text-uppercase" style="font-size: 10.5px;">
                                        Author
                                    </span>
                                @else
                                    <span class="badge bg-surface-2 border border-theme text-theme-secondary rounded-pill text-uppercase" style="font-size: 10.5px;">
                                        Viewer
                                    </span>
                                @endif
                            </td>

                            <!-- Posts Count -->
                            <td class="py-3 text-start small text-theme fw-semibold" style="font-size: 13px;">
                                {{ number_format($user->posts_count) }}
                            </td>

                            <!-- Comments Count -->
                            <td class="py-3 text-start small text-theme fw-semibold" style="font-size: 13px;">
                                {{ number_format($user->comments_count) }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 text-start">
                                @if($user->is_locked)
                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 11px;">
                                        Đã bị khóa
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 11px;">
                                        Hoạt động
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="pe-3 pe-sm-4 py-3 text-center">
                                <div class="d-inline-flex align-items-center gap-1.5 admin-users-table__actions">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-theme rounded-pill px-2.5 py-1 text-theme-secondary" style="font-size: 11px;" aria-label="Xem chi tiết người dùng {{ $user->name }}">
                                        Chi tiết
                                    </a>

                                    <!-- Lock / Unlock Action Button -->
                                    @if(auth()->id() !== $user->id)
                                        @if($user->is_locked)
                                            <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="d-inline m-0"
                                                  data-confirm="true"
                                                  data-confirm-title="Mở khóa tài khoản"
                                                  data-confirm-message="Mở khóa tài khoản người dùng '{{ $user->name }}'?"
                                                  data-confirm-target-name="{{ $user->name }}"
                                                  data-confirm-target-meta="{{ $user->email }}"
                                                  data-confirm-description="Người dùng này sẽ có thể đăng nhập lại và thực hiện các tương tác bình thường trên nền tảng CloudBlog."
                                                  data-confirm-btn-text="Mở khóa tài khoản"
                                                  data-confirm-btn-class="btn-success"
                                                  data-confirm-type="success">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Mở khóa tài khoản {{ $user->name }}">
                                                    Mở khóa
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="d-inline m-0"
                                                  data-confirm="true"
                                                  data-confirm-title="Khóa tài khoản"
                                                  data-confirm-message="Khóa tài khoản người dùng '{{ $user->name }}'?"
                                                  data-confirm-target-name="{{ $user->name }}"
                                                  data-confirm-target-meta="{{ $user->email }}"
                                                  data-confirm-description="Người dùng bị khóa sẽ không thể đăng nhập hoặc thực hiện bất kỳ hành động nào trên nền tảng."
                                                  data-confirm-btn-text="Khóa tài khoản"
                                                  data-confirm-btn-class="btn-danger"
                                                  data-confirm-type="danger">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Khóa tài khoản {{ $user->name }}">
                                                    Khóa
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0 border-0">
                                <x-empty-state
                                    title="Không tìm thấy người dùng"
                                    description="Không tìm thấy người dùng nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm."
                                    :action-url="route('admin.users.index')"
                                    action-label="Đặt lại bộ lọc"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (< 768px: Mobile-First Responsive Cards) -->
        <div class="d-block d-md-none divide-y divide-theme admin-users-mobile-list">
            @forelse($users as $user)
                <div class="p-3 admin-users-mobile-card">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            @if($user->role === 'admin')
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill text-uppercase" style="font-size: 10px;">
                                    Admin
                                </span>
                            @elseif($user->role === 'author')
                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill text-uppercase" style="font-size: 10px;">
                                    Author
                                </span>
                            @else
                                <span class="badge bg-surface-2 border border-theme text-theme-secondary rounded-pill text-uppercase" style="font-size: 10px;">
                                    Viewer
                                </span>
                            @endif

                            @if($user->is_locked)
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 10px;">
                                    Đã bị khóa
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 10px;">
                                    Hoạt động
                                </span>
                            @endif
                        </div>
                        <span class="small text-theme-muted" style="font-size: 11px;">
                            ID: #{{ $user->id }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2.5 mb-2.5">
                        <x-avatar :user="$user" size="sm" class="border border-theme flex-shrink-0" />
                        <div class="min-w-0 flex-grow-1">
                            <a href="{{ route('admin.users.show', $user) }}" class="fw-bold text-theme text-decoration-none hover-accent text-truncate d-block" style="font-size: 14px;">
                                {{ $user->name }}
                            </a>
                            <p class="small text-theme-muted mb-0 text-truncate" style="font-size: 12px;">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-2 pt-2 border-theme-top flex-wrap">
                        <div class="small text-theme-muted" style="font-size: 11.5px;">
                            <span>{{ number_format($user->posts_count) }} bài viết</span>
                            <span class="mx-1">&bull;</span>
                            <span>{{ number_format($user->comments_count) }} bình luận</span>
                        </div>

                        <div class="d-inline-flex align-items-center gap-1.5 ms-auto">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-theme rounded-pill px-2.5 py-1 text-theme-secondary" style="font-size: 11px;" aria-label="Xem chi tiết người dùng {{ $user->name }}">
                                Chi tiết
                            </a>

                            @if(auth()->id() !== $user->id)
                                @if($user->is_locked)
                                    <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Mở khóa tài khoản"
                                          data-confirm-message="Mở khóa tài khoản người dùng '{{ $user->name }}'?"
                                          data-confirm-target-name="{{ $user->name }}"
                                          data-confirm-target-meta="{{ $user->email }}"
                                          data-confirm-description="Người dùng này sẽ có thể đăng nhập lại và thực hiện các tương tác bình thường trên nền tảng CloudBlog."
                                          data-confirm-btn-text="Mở khóa tài khoản"
                                          data-confirm-btn-class="btn-success"
                                          data-confirm-type="success">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Mở khóa tài khoản {{ $user->name }}">
                                            Mở khóa
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Khóa tài khoản"
                                          data-confirm-message="Khóa tài khoản người dùng '{{ $user->name }}'?"
                                          data-confirm-target-name="{{ $user->name }}"
                                          data-confirm-target-meta="{{ $user->email }}"
                                          data-confirm-description="Người dùng bị khóa sẽ không thể đăng nhập hoặc thực hiện bất kỳ hành động nào trên nền tảng."
                                          data-confirm-btn-text="Khóa tài khoản"
                                          data-confirm-btn-class="btn-danger"
                                          data-confirm-type="danger">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Khóa tài khoản {{ $user->name }}">
                                            Khóa
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-3">
                    <x-empty-state
                        title="Không tìm thấy người dùng"
                        description="Không tìm thấy người dùng nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm."
                        :action-url="route('admin.users.index')"
                        action-label="Đặt lại bộ lọc"
                    />
                </div>
            @endforelse
        </div>
    </div>

        @if($users->hasPages())
            <div data-feed-pagination class="admin-users-pagination px-3 px-sm-4 py-3 border-theme-top bg-theme-surface">
                {{ $users->onEachSide(1)->links('admin.users.partials.pagination') }}
            </div>
        @endif
    </div>
</div>
