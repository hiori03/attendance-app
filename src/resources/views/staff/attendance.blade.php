@extends('layouts.app')
@section('title', '勤怠登録画面')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/staff/attendance.css') }}">
@endsection
@section('content')
    <div class="content">
        @if (!$attendance)
            <p class="status">勤務外</p>
        @elseif ($attendance->status === \App\Models\Attendance::STATUS_WORKING)
            <p class="status">出勤中</p>
        @elseif ($attendance->status === \App\Models\Attendance::STATUS_BREAK)
            <p class="status">休憩中</p>
        @elseif ($attendance->status === \App\Models\Attendance::STATUS_FINISHED)
            <p class="status">退勤済</p>
        @endif
        <p class="day" id="date"></p>
        <p class="clock" id="clock"></p>
        <div></div>
        @if (!$attendance)
            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button class="attendance_button" type="submit">出勤</button>
            </form>
        @elseif ($attendance->status === \App\Models\Attendance::STATUS_WORKING)
            <div class="button_position">
                <form method="POST" action="{{ route('attendance.end') }}">
                    @csrf
                    <button class="attendance_button" type="submit">退勤</button>
                </form>
                <form method="POST" action="{{ route('break.start') }}">
                    @csrf
                    <button class="break_button" type="submit">休憩入</button>
                </form>
            </div>
        @elseif ($attendance->status === \App\Models\Attendance::STATUS_BREAK)
            <form method="POST" action="{{ route('break.end') }}">
                @csrf
                <button class="break_button" type="submit">休憩戻</button>
            </form>
        @elseif ($attendance->status === \App\Models\Attendance::STATUS_FINISHED)
            <p class="message">お疲れ様でした。</p>
        @endif
    </div>
    <script>
        function updateClock() {
            const now = new Date();

            const week = ['日', '月', '火', '水', '木', '金', '土'];

            const year = now.getFullYear();
            const month = now.getMonth() + 1;
            const day = now.getDate();
            const weekday = week[now.getDay()];

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            document.getElementById('date').textContent =
                `${year}年${month}月${day}日(${weekday})`;

            document.getElementById('clock').textContent =
                `${hours}:${minutes}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>
@endsection