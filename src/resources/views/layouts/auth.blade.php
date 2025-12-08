<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/auth.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header_logo-div">
            <a href="{{ route('login.form') }}" class="header_logo-link">
                <img class="header_logo" src="{{ asset('images/logo.png') }}" alt="COACHTECH">
            </a>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>