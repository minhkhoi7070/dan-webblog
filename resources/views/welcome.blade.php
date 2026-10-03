@extends('layouts.base')
@section('html_class', '')
@section('body_class', 'bg-theme text-theme d-flex flex-column align-items-center justify-content-center min-vh-100 p-3 p-sm-4 position-relative')

@section('body')
        <div class="card p-4 p-sm-5 border-theme text-center rounded-2xl w-100 shadow-sm" style="max-width: 460px;">
            <div class="rounded-4 brand-gradient text-white mx-auto d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 54px; height: 54px; font-weight: 800; font-size: 1.5rem;" data-theme-preserve>
                B
            </div>
            <h1 class="h4 fw-bold text-theme mb-2">Blog<span class="text-accent">MNM</span></h1>
            <p class="small text-theme-secondary mb-4 leading-relaxed">Nền tảng xuất bản nội dung xã hội hiện đại. Đọc sâu, viết thật, kết nối ý nghĩa.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('home') }}" class="btn btn-editorial-primary rounded-pill px-4 fw-semibold small">
                    Trang chủ
                </a>
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-theme rounded-pill px-4 fw-semibold small">
                        Đăng nhập
                    </a>
                @endguest
            </div>
        </div>
@endsection
