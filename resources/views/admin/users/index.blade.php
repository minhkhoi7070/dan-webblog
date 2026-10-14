@extends('layouts.admin')

@section('admin_title', 'Quản lý Người dùng')

@section('admin_content')
<<<<<<< HEAD
<!-- Filter & Search Bar -->
<div class="card p-3 border shadow-sm rounded-3 mb-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
=======
<!-- Filter & Search Toolbar (Professional Admin Controls) -->
<div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2.5 align-items-center">
        <!-- Search Input -->
        <div class="col-12 col-md-5">
            <div class="position-relative">
>>>>>>> origin/main
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
<<<<<<< HEAD
                    placeholder="Tìm kiếm người dùng theo tên hoặc email..."
                    class="form-control border-start-0 ps-0"
                >
=======
                    placeholder="Tìm theo tên hoặc email người dùng..."
                    class="form-control form-control-sm form-control-editorial rounded-pill ps-4 pe-3 py-1.5"
                    style="font-size: 13px;"
                >
                <svg style="width: 14px; height: 14px; position: absolute; left: 12px; top: 10px; pointer-events: none;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
>>>>>>> origin/main
            </div>
        </div>

        <!-- Role Filter -->
        <div class="col-12 col-sm-6 col-md-3">
            <select
                name="role"
                onchange="this.form.submit()"
<<<<<<< HEAD
                class="form-select"
=======
                class="form-select form-select-sm form-control-editorial rounded-pill"
                style="font-size: 13px;"
>>>>>>> origin/main
            >
                <option value="">-- Tất cả vai trò --</option>
                <option value="admin" @selected($roleFilter === 'admin')>Admin (Quản trị)</option>
                <option value="author" @selected($roleFilter === 'author')>Author (Tác giả)</option>
                <option value="viewer" @selected($roleFilter === 'viewer')>Viewer (Độc giả)</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="col-12 col-sm-6 col-md-3">
            <select
                name="status"
                onchange="this.form.submit()"
<<<<<<< HEAD
                class="form-select"
=======
                class="form-select form-select-sm form-control-editorial rounded-pill"
                style="font-size: 13px;"
>>>>>>> origin/main
            >
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" @selected($statusFilter === 'active')>Đang hoạt động</option>
                <option value="locked" @selected($statusFilter === 'locked')>Đã bị khóa</option>
            </select>
        </div>

        <div class="col-12 col-md-1">
<<<<<<< HEAD
            <button type="submit" class="btn btn-dark rounded-pill w-100 fw-bold small">
=======
            <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill w-100 fw-semibold" style="font-size: 12.5px;">
>>>>>>> origin/main
                Lọc
            </button>
        </div>
    </form>
</div>

<<<<<<< HEAD
<!-- Users Table -->
<div class="card border shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase fw-bold text-secondary">
                <tr>
                    <th class="px-4 py-3">Người dùng</th>
                    <th class="px-4 py-3">Vai trò</th>
                    <th class="px-4 py-3">Bài viết</th>
                    <th class="px-4 py-3">Bình luận</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3 text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <!-- User Identity -->
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <x-avatar :user="$user" size="sm" />
                                <div class="min-w-0">
                                    <a href="{{ route('admin.users.show', $user) }}" class="fw-bold text-body text-decoration-none text-truncate d-block small">
                                        {{ $user->name }}
                                    </a>
                                    <p class="small text-secondary mb-0 text-truncate" style="font-size: 0.75rem;">{{ $user->email }}</p>
=======
<!-- Users Database Table (High Information Density) -->
<div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
            <thead class="border-theme-bottom bg-surface-2">
                <tr>
                    <th class="ps-3 ps-sm-4 py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Người dùng</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Vai trò</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Bài viết</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Bình luận</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Trạng thái</th>
                    <th class="pe-3 pe-sm-4 py-3 text-end label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-theme">
                @forelse($users as $user)
                    <tr>
                        <!-- User Identity -->
                        <td class="ps-3 ps-sm-4 py-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <x-avatar :user="$user" size="sm" class="border border-theme flex-shrink-0" />
                                <div class="min-w-0" style="max-width: 280px;">
                                    <a href="{{ route('admin.users.show', $user) }}" class="fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block small" style="font-size: 13.5px;">
                                        {{ $user->name }}
                                    </a>
                                    <p class="small text-theme-muted mb-0 text-truncate" style="font-size: 11.5px;">{{ $user->email }}</p>
>>>>>>> origin/main
                                </div>
                            </div>
                        </td>

                        <!-- Role Badge -->
<<<<<<< HEAD
                        <td class="px-4 py-3">
                            <x-badge variant="status-draft" size="xs" class="text-uppercase">
                                {{ $user->role }}
                            </x-badge>
                        </td>

                        <!-- Posts Count -->
                        <td class="px-4 py-3 small fw-semibold text-body">
=======
                        <td class="py-3">
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
                        <td class="py-3 small text-theme fw-semibold" style="font-size: 13px;">
>>>>>>> origin/main
                            {{ number_format($user->posts_count) }}
                        </td>

                        <!-- Comments Count -->
<<<<<<< HEAD
                        <td class="px-4 py-3 small fw-semibold text-body">
                            {{ number_format($user->comments_count) }}
                        </td>

                        <!-- Account Status Badge -->
                        <td class="px-4 py-3">
=======
                        <td class="py-3 small text-theme fw-semibold" style="font-size: 13px;">
                            {{ number_format($user->comments_count) }}
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3">
>>>>>>> origin/main
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

<<<<<<< HEAD
                        <!-- Actions (Lock / Unlock / View) -->
                        <td class="px-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                    Chi tiết
                                </a>

                                @if($user->id !== Auth::id())
                                    @if($user->is_locked)
                                        <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="d-inline"
                                              data-confirm="true"
                                              data-confirm-title="Mở khóa tài khoản"
                                              data-confirm-message="Bạn có chắc muốn mở khóa tài khoản này?"
                                              data-confirm-target-name="{{ $user->name }}"
                                              data-confirm-target-meta="{{ $user->email }}"
                                              data-confirm-description="Tài khoản sẽ được kích hoạt lại và người dùng có thể đăng nhập bình thường vào hệ thống."
=======
                        <!-- Actions -->
                        <td class="pe-3 pe-sm-4 py-3 text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
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
                                              data-confirm-description="Người dùng này sẽ có thể đăng nhập lại và thực hiện các tương tác bình thường trên nền tảng BlogMNM."
>>>>>>> origin/main
                                              data-confirm-btn-text="Mở khóa tài khoản"
                                              data-confirm-btn-class="btn-success"
                                              data-confirm-type="success">
                                            @csrf
<<<<<<< HEAD
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                                Mở khóa
                                            </button>
                                        </form>
                                    @elseif($user->isAdmin())
                                        <span class="small text-primary fw-medium" style="font-size: 0.75rem;">Quản trị viên</span>
                                    @else
                                        <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="d-inline"
                                              data-confirm="true"
                                              data-confirm-title="Khóa tài khoản"
                                              data-confirm-message="Bạn có chắc muốn khóa tài khoản này?"
                                              data-confirm-target-name="{{ $user->name }}"
                                              data-confirm-target-meta="{{ $user->email }}"
                                              data-confirm-description="Người dùng sẽ không thể đăng nhập hoặc thực hiện các chức năng yêu cầu tài khoản hoạt động cho đến khi được mở khóa."
=======
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
>>>>>>> origin/main
                                              data-confirm-btn-text="Khóa tài khoản"
                                              data-confirm-btn-class="btn-danger"
                                              data-confirm-type="danger">
                                            @csrf
<<<<<<< HEAD
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
=======
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Khóa tài khoản {{ $user->name }}">
>>>>>>> origin/main
                                                Khóa
                                            </button>
                                        </form>
                                    @endif
<<<<<<< HEAD
                                @else
                                    <span class="small text-secondary fst-italic" style="font-size: 0.75rem;">Bạn</span>
=======
>>>>>>> origin/main
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
<<<<<<< HEAD
                        <td colspan="6" class="px-4 py-5 text-center text-secondary small">
                            Không tìm thấy người dùng nào phù hợp với bộ lọc tìm kiếm.
=======
                        <td colspan="6" class="p-0 border-0">
                            <x-empty-state
                                title="Không tìm thấy người dùng"
                                description="Không tìm thấy người dùng nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm."
                                :action-url="route('admin.users.index')"
                                action-label="Đặt lại bộ lọc"
                            />
>>>>>>> origin/main
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
<<<<<<< HEAD
        <div class="px-4 py-3 border-top">
=======
        <div class="px-3 px-sm-4 py-3 border-theme-top bg-theme-surface d-flex justify-content-center">
>>>>>>> origin/main
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
