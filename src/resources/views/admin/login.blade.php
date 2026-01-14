@extends('layouts.auth')
@section('title', '管理者ログイン画面')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection
@section('content')
<div class="content">
    <h1 class="content_title">管理者ログイン</h1>
    <form action="{{ route('admin.login') }}" method="POST">
        @csrf
        <div class="content_form">
            <p class="content_form-text">メールアドレス</p>
            <input class="content_form-input" type="text" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="error_message">{{ $message }}</p>
            @enderror
        </div>
        <div class="content_form">
            <p class="content_form-text">パスワード</p>
            <input class="content_form-input" type="password" name="password">
            @error('password')
                <p class="error_message">{{ $message }}</p>
            @enderror
        </div>
        <button class="content_form-button">管理者ログインする</button>
    </form>
</div>
@endsection