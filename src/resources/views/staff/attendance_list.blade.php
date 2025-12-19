@extends('layouts.app')
@section('title', '勤怠一覧画面')
@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/attendance_list.css') }}">
@endsection
@section('content')
<div class="content">
    <h2 class="page_title">勤怠一覧</h2>
    <div class="month_switch">
        <form method="POST" action="{{ route('attendance.list.changeMonth') }}">
            @csrf
            <button type="submit" name="action" value="prev" class="month_btn prev">
                <span class="arrow"></span>
                <span class="btn_text">前月</span>
            </button>
        </form>
        <div class="month_display">
            <img src="/images/calendar-icon.png" alt="カレンダー" class="calendar_icon">
            <p class="month_text">{{ $displayMonth }}</p>
        </div>
        <form method="POST" action="{{ route('attendance.list.changeMonth') }}">
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
            @php
            $date = $startOfMonth->copy();
            @endphp
            @while ($date <= $endOfMonth)
                @php
                $attendance=$attendances[$date->toDateString()] ?? null;
                @endphp

                <tr class="tbody_tr">
                    <td class="day_td">
                        {{ $date->locale('ja')->isoFormat('MM/DD(ddd)') }}
                    </td>
                    <td class="tag_td">
                        {{ $attendance?->work_start_hm ?? '' }}
                    </td>
                    <td class="tag_td">
                        {{ $attendance?->work_end_hm ?? '' }}
                    </td>
                    <td class="tag_td">
                        {{ $attendance?->break_time_hm ?? '' }}
                    </td>
                    <td class="tag_td">
                        {{ $attendance?->total_work_time_hm ?? '' }}
                    </td>
                    <td class="detail_td">
                        <form method="POST" action="{{ route('attendance.detail.prepare') }}">
                            @csrf
                            <input type="hidden" name="attendance_id" value="{{ $attendance?->id ?? 0 }}">
                            <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                            <button type="submit" class="detail_button">詳細</button>
                        </form>
                    </td>
                </tr>
                @php
                $date->addDay();
                @endphp
                @endwhile
        </table>
    </div>
</div>
@endsection