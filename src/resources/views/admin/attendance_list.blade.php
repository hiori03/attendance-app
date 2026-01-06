@extends('layouts.admin')
@section('title', '勤怠一覧画面(管理者)')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css') }}">
@endsection
@section('content')
<div class="content">
    <h2 class="page_title">{{ $titleDay }}の勤怠</h2>
    <div class="day_switch">
        <form method="POST" action="{{ route('attendance.list.changeDay') }}">
            @csrf
            <button type="submit" name="action" value="prev" class="day_btn prev">
                <span class="arrow"></span>
                <span class="btn_text">前日</span>
            </button>
        </form>
        <div class="day_display">
            <img src="{{ asset('images/calendar-icon.png') }}" alt="カレンダー" class="calendar_icon">
            <p class="day_text">{{ $displayDay }}</p>
        </div>
        <form method="POST" action="{{ route('attendance.list.changeDay') }}">
            @csrf
            <button type="submit" name="action" value="next" class="day_btn next">
                <span class="btn_text">翌日</span>
                <span class="arrow"></span>
            </button>
        </form>
    </div>
    <div class="table_div">
        <table class="attendance_table">
            <tr class="thead_tr">
                <th class="name_th">名前</th>
                <th class="tag_th">出勤</th>
                <th class="tag_th">退勤</th>
                <th class="tag_th">休憩</th>
                <th class="tag_th">合計</th>
                <th class="detail_th">詳細</th>
            </tr>
            @foreach ($days as $day)
            <tr class="tbody_tr">
                <td class="name_td">
                    {{ $day['attendance']?->user->name }}
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
                    <a href="{{ route('admin.attendance.detail.form', $day['attendance']->id) }}" class="detail_button">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection