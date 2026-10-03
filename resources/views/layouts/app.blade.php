@extends('layouts.public')
@section('title', config('app.name', 'BlogMNM') . ' - Quản lý tài khoản')

@section('content')
    <div class="container-fluid py-3 py-sm-4 content-container">
        @isset($header)
            <div class="mb-4 pb-3 border-theme-bottom">
                {{ $header }}
            </div>
        @endisset

        <div>
            {{ $slot }}
        </div>
    </div>
@endsection

