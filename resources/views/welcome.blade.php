@extends('layouts.base')
@section('html_class', '')
@section('body_class', 'bg-theme text-theme d-flex flex-column align-items-center justify-content-center min-vh-100 p-3 p-sm-4 position-relative')

@section('body')
        <div class="card p-4 p-sm-5 border-theme text-center rounded-2xl w-100 shadow-sm" style="max-width: 460px;">
            <div class="mb-3 d-flex justify-content-center">
                <a href="{{ route('posts.index') }}" class="brand-link inline-flex items-center gap-2.5 font-bold text-2xl tracking-tight text-decoration-none" aria-label="CloudBlog Trang chủ">
                    <x-logo class="w-14 h-14" />
                    <span class="brand-text">CloudBlog</span>
                </a>
            </div>
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
