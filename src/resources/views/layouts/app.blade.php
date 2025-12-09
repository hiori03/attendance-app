<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header_logo-div">
            <a href="{{ route('attendance.form') }}" class="header_logo-link">
                <img class="header_logo" src="{{ asset('images/logo.png') }}" alt="COACHTECH">
            </a>
        </div>
        <div class="header_menu">
            <a class="header_link" href="{{ route('attendance.form') }}">勤怠</a>
            <a class="header_link" href="{{ route('attendance.list.form') }}">勤怠一覧</a>
            <a class="header_link" href="{{ route('stamp_correction_request.form') }}">申請</a>
            <form class="header_logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="header_logout">ログアウト</button>
            </form>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>