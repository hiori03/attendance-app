@extends('layouts.admin')
@section('title', 'スタッフ別勤怠一覧画面(管理者)')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_staff.css') }}">
@endsection
@section('content')
<div class="content">
    <h2 class="page_title">{{ $user->name }}さんの勤怠</h2>
    <div class="month_switch">
        <form method="POST" action="{{ route('admin.attendance.staff.changeMonth', ['id' => $user->id]) }}">
            @csrf
            <button type="submit" name="action" value="prev" class="month_btn prev">
                <span class="arrow"></span>
                <span class="btn_text">前月</span>
            </button>
        </form>
        <div class="month_display">
            <img src="{{ asset('images/calendar-icon.png') }}" alt="カレンダー" class="calendar_icon">
            <p class="month_text">{{ $displayMonth }}</p>
        </div>
        <form method="POST" action="{{ route('admin.attendance.staff.changeMonth', ['id' => $user->id]) }}">
            @csrf
            <button type="submit" name="action" value="next" class="month_btn next">
                <span class="btn_text">翌月</span>
                <span class="arrow"></span>
            </button>
        </form>
    </div>
    <div class="table_div">
        <table class="attendance_table">
            <tr class="thead_tr">
                <th class="day_th">日付</th>
                <th class="tag_th">出勤</th>
                <th class="tag_th">退勤</th>
                <th class="tag_th">休憩</th>
                <th class="tag_th">合計</th>
                <th class="detail_th">詳細</th>
            </tr>
            @foreach ($days as $day)
            <tr class="tbody_tr">
                <td class="day_td">
                    {{ $day['date']->locale('ja')->isoFormat('MM/DD(ddd)') }}
                </td>
                <td class="tag_td">
                    {{ $day['attendance']?->work_start_hm ?? '' }}
                </td>
                <td class="tag_td">
                    {{ $day['attendance']?->work_end_hm ?? '' }}
                </td>
                <td class="tag_td">
                    {{ $day['attendance']?->break_time_hm ?? '' }}
                </td>
                <td class="tag_td">
                    {{ $day['attendance']?->total_work_time_hm ?? '' }}
                </td>
                <td class="detail_td">
                    <form method="POST" action="{{ route('admin.attendance.detail.prepare') }}">
                        @csrf
                        @if($day['attendance'])
                            <input type="hidden" name="attendance_id" value="{{ $day['attendance']->id }}">
                        @endif
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="date" value="{{ $day['date']->toDateString() }}">
                        <button type="submit" class="detail_button">詳細</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="csv_div">
        <form method="GET" action="{{ route('admin.attendance.export') }}">
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <input type="hidden" name="month" value="{{ session('attendance_month') }}">
            <button type="submit" class="csv_button">CSV出力</button>
        </form>
    </div>
</div>
@endsection