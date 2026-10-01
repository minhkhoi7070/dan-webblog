@extends('layouts.admin')

@section('admin_title', 'Chỉnh sửa Chuyên mục: ' . $category->name)

@section('admin_content')
<div class="mx-auto" style="max-width: 650px;">
    <div class="mb-4">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-link btn-sm text-secondary text-decoration-none p-0">
            &larr; Quay lại danh sách chuyên mục
        </a>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger mb-4 rounded-3 p-3">
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-4 p-sm-5 border shadow-sm rounded-3">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="d-flex flex-column gap-3">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="form-label fw-bold small text-body">
                    Tên chuyên mục <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $category->name) }}"
                    required
                    autofocus
                    class="form-control @error('name') is-invalid @enderror"
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="slug" class="form-label fw-bold small text-body">
                    Đường dẫn tĩnh (Slug)
                </label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    value="{{ old('slug', $category->slug) }}"
                    class="form-control font-monospace @error('slug') is-invalid @enderror"
                >
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex align-items-center gap-2 pt-3 border-top mt-2">
                <button
                    type="submit"
                    class="btn btn-primary rounded-pill px-4 py-2 fw-bold small"
                >
                    Lưu thay đổi
                </button>
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold small"
                >
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
