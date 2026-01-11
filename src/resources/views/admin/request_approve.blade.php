@extends('layouts.admin')
@section('title', '修正申請承認画面(管理者)')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/request_approve.css') }}">
@endsection
@section('content')
<div class="content">
    <h2 class="page_title">勤怠詳細</h2>
    <form method="POST" action="{{ route('stamp_correction_request.approve.confirmation', $attendanceRequest->id) }}">
        @csrf
        <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        @if ($attendance)
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        @endif
        <div class="table_div">
            <table class="detail_table">
                <tr class="detail_tr">
                    <th class="detail_th">名前</th>
                    <td class="name_td">
                        {{ $user->name }}
                    </td>
                </tr>
                <tr class="detail_tr">
                    <th class="detail_th">日付</th>
                    <td class="day_td">
                        <div class="list_position">
                            {{ $date->format('Y年') }}
                        </div>
                        <div class="list_interval"></div>
                        <div class="list_position">
                            {{ $date->format('n月j日') }}
                        </div>
                    </td>
                </tr>
                <tr class="detail_tr">
                    <th class="detail_th">出勤・退勤</th>
                    <td class="detail_td">
                        <div class="list_time">
                            <div class="list_position">
                                <p class="time_text">
                                    {{ $attendanceRequest->new_work_start_hm ?? $attendance?->work_start_hm }}
                                </p>
                            </div>
                            <div class="list_interval">〜</div>
                            <div class="list_position">
                                <p class="time_text">
                                    {{ $attendanceRequest->new_work_end_hm ?? $attendance?->work_end_hm }}
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
                @foreach ($breakRows as $row)
                    <tr class="detail_tr">
                        <th class="detail_th">{{ $row['label'] }}</th>
                        <td class="detail_td">
                            <div class="list_time">
                                <div class="list_position">
                                    <p class="time_text">
                                        {{ $row['start']}}
                                    </p>
                                </div>
                                <div class="list_interval">〜</div>
                                <div class="list_position">
                                    <p class="time_text">
                                        {{ $row['end'] }}
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <tr class="detail_tr">
                    <th class="detail_th">備考</th>
                    <td class="remarks_td">
                        <p class="remarks_text">
                            {{ $attendanceRequest->text }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="button_area">
            @if ($canEdit)
                <button class="approve_button">承認</button>
            @else
                <p class="approve_message">承認済み</p>
            @endif
        </div>
    </form>
</div>
@endsection