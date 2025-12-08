@extends('layouts.auth')
@section('title', '会員登録画面')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/staff/register.css') }}">
@endsection
@section('content')
    <div class="content">
        <h1 class="content_title">会員登録</h1>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="content_form">
                <p class="content_form-text">名前</p>
                <input class="content_form-input" type="text" name="name" value="{{ old('name') }}">
                @error('name')
                    <p class="error_message">{{ $message }}</p>
                @enderror
            </div>
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
            <div class="content_form">
                <p class="content_form-text">パスワード確認</p>
                <input class="content_form-input" type="password" name="password_confirmation">
                @error('password_confirmation')
                    <p class="error_message">{{ $message }}</p>
                @enderror
            </div>
            <button class="content_form-button">登録する</button>
        </form>
        <a href="{{ route('login.form') }}" class="link_login">ログインはこちら</a>
    </div>
@endsection