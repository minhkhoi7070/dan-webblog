@extends('layouts.admin')

@section('admin_title', 'Chỉnh sửa Chuyên mục: ' . $category->name)

@section('admin_content')
<div class="mx-auto" style="max-width: 620px;">
    <div class="mb-4">
        <a href="{{ route('admin.categories.index') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại danh sách chuyên mục
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

    <div class="card p-4 p-sm-5 border-theme bg-theme-surface shadow-xs rounded-xl">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="d-flex flex-column gap-3.5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="label-uppercase text-theme mb-1.5 d-block">
                    Tên chuyên mục <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $category->name) }}"
                    required
                    autofocus
                    class="form-control form-control-editorial @error('name') is-invalid @enderror"
                >
                @error('name')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="slug" class="label-uppercase text-theme mb-1.5 d-block">
                    Đường dẫn tĩnh (Slug URL)
                </label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    value="{{ old('slug', $category->slug) }}"
                    class="form-control form-control-editorial font-monospace @error('slug') is-invalid @enderror"
                >
                @error('slug')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex align-items-center gap-2 pt-3 border-theme-top mt-2">
                <button
                    type="submit"
                    class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-2 fw-semibold"
                >
                    Lưu thay đổi
                </button>
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="btn btn-outline-theme btn-sm rounded-pill px-4 py-2 fw-medium"
                >
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
