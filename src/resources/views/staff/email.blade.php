@extends('layouts.auth')
@section('title', 'メール認証誘導画面')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/staff/email.css') }}">
@endsection
@section('content')
    <div class="content">
        <p class="text">登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>
        <a class="certification" href="http://localhost:8025">認証はこちらから</a>
        <form method="POST" action="{{ route('email.resend') }}">
            @csrf
            <button type="submit" class="resend">認証メールを再送する</button>
        </form>
    </div>
@endsection