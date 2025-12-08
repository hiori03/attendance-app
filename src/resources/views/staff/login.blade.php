@extends('layouts.auth')
@section('title', 'ログイン画面')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/staff/login.css') }}">
@endsection
@section('content')
    <div class="content">
        <h1 class="content_title">ログイン</h1>
        <form action="{{ route('login') }}" method="POST">
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
            <button class="content_form-button">ログインする</button>
        </form>
        <a href="{{ route('register.form') }}" class="link_login">会員登録はこちら</a>
    </div>
@endsection