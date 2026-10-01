@extends('layouts.admin')

@section('admin_title', 'Quản lý Người dùng')

@section('admin_content')
<!-- Filter & Search Bar -->
<div class="card p-3 border shadow-sm rounded-3 mb-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Tìm kiếm người dùng theo tên hoặc email..."
                    class="form-control border-start-0 ps-0"
                >
            </div>
        </div>

        <!-- Role Filter -->
        <div class="col-12 col-sm-6 col-md-3">
            <select
                name="role"
                onchange="this.form.submit()"
                class="form-select"
            >
                <option value="">-- Tất cả vai trò --</option>
                <option value="admin" @selected($roleFilter === 'admin')>Admin</option>
                <option value="author" @selected($roleFilter === 'author')>Author (Tác giả)</option>
                <option value="viewer" @selected($roleFilter === 'viewer')>Viewer (Độc giả)</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="col-12 col-sm-6 col-md-3">
            <select
                name="status"
                onchange="this.form.submit()"
                class="form-select"
            >
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" @selected($statusFilter === 'active')>Đang hoạt động</option>
                <option value="locked" @selected($statusFilter === 'locked')>Đã bị khóa</option>
            </select>
        </div>

        <div class="col-12 col-md-1">
            <button type="submit" class="btn btn-dark rounded-pill w-100 fw-bold small">
                Lọc
            </button>
        </div>
    </form>
</div>

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
                                </div>
                            </div>
                        </td>

                        <!-- Role Badge -->
                        <td class="px-4 py-3">
                            <x-badge variant="status-draft" size="xs" class="text-uppercase">
                                {{ $user->role }}
                            </x-badge>
                        </td>

                        <!-- Posts Count -->
                        <td class="px-4 py-3 small fw-semibold text-body">
                            {{ number_format($user->posts_count) }}
                        </td>

                        <!-- Comments Count -->
                        <td class="px-4 py-3 small fw-semibold text-body">
                            {{ number_format($user->comments_count) }}
                        </td>

                        <!-- Account Status Badge -->
                        <td class="px-4 py-3">
                            @if($user->is_locked)
                                <x-badge variant="status-rejected" size="xs">
                                    Đã bị khóa
                                </x-badge>
                            @else
                                <x-badge variant="status-published" size="xs">
                                    Hoạt động
                                </x-badge>
                            @endif
                        </td>

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
                                              data-confirm-btn-text="Mở khóa tài khoản"
                                              data-confirm-btn-class="btn-success"
                                              data-confirm-type="success">
                                            @csrf
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
                                              data-confirm-btn-text="Khóa tài khoản"
                                              data-confirm-btn-class="btn-danger"
                                              data-confirm-type="danger">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                                Khóa
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="small text-secondary fst-italic" style="font-size: 0.75rem;">Bạn</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-5 text-center text-secondary small">
                            Không tìm thấy người dùng nào phù hợp với bộ lọc tìm kiếm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
