@extends('layouts.admin')

@section('admin_title', 'Tạo Thẻ Bài Viết Mới')

@section('admin_content')
<div class="mx-auto" style="max-width: 620px;">
    <div class="mb-4 d-none d-md-block">
        <a href="{{ route('admin.tags.index') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại danh sách thẻ
        </a>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-xs border border-danger border-opacity-25 rounded-3 py-2.5 px-3">
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-4 p-sm-5 border-theme bg-theme-surface shadow-xs rounded-xl admin-create-card">
        <form action="{{ route('admin.tags.store') }}" method="POST" class="d-flex flex-column gap-3 admin-tag-create-form">
            @csrf

            <div class="admin-create-field">
                <label for="name" class="label-uppercase text-theme mb-2 d-block">
                    Tên thẻ <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-surface-2 border-theme text-theme-muted font-monospace">#</span>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        placeholder="VD: Laravel, AI, Bảo mật..."
                        required
                        autofocus
                        class="form-control form-control-editorial @error('name') is-invalid @enderror"
                    >
                </div>
                <div class="form-text small text-theme-muted mt-2 admin-create-helper" style="font-size: 11px;">
                    Dấu <code>#</code> thừa hoặc khoảng trắng sẽ tự động được chuẩn hóa.
                </div>
                @error('name')
                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="admin-create-field">
                <label for="slug" class="label-uppercase text-theme mb-2 d-block">
                    Đường dẫn thẻ
                </label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    value="{{ old('slug') }}"
                    placeholder="VD: laravel (để trống nếu muốn hệ thống tự sinh)"
                    class="form-control form-control-editorial font-monospace @error('slug') is-invalid @enderror"
                >
                <div class="form-text small text-theme-muted mt-2 admin-create-helper" style="font-size: 11px;">
                    Chỉ dùng chữ cái thường, số và dấu gạch ngang (-). Nếu để trống, hệ thống sẽ tự động tạo đường dẫn từ tên thẻ.
                </div>
                @error('slug')
                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2 pt-1 border-theme-top mt-0 admin-create-actions">
                <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-theme btn-sm rounded-pill px-3.5 py-1.5 admin-create-cancel">
                    Hủy bỏ
                </a>
                <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-1.5 fw-semibold shadow-xs admin-create-submit">
                    Lưu thẻ mới
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

