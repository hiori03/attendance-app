@extends('layouts.app')
@section('title', '勤怠詳細画面')
@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/attendance_detail.css') }}">
@endsection
@section('content')
<div class="content">
    <h2 class="page_title">勤怠詳細</h2>
    <form method="POST" action="{{ route('attendance.detail.request') }}">
        @csrf
            <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
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
                                @if ($canEdit)
                                    <input class="time_input" type="text" name="work_start" value="{{ old('work_start', $attendance?->work_start_hm) }}">
                                @else
                                    <p class="time_text">
                                        {{ $attendance?->work_start_hm }}
                                    </p>
                                @endif
                            </div>
                            <div class="list_interval">〜</div>
                            <div class="list_position">
                                @if ($canEdit)
                                    <input class="time_input" type="text" name="work_end" value="{{ old('work_end', $attendance?->work_end_hm) }}">
                                @else
                                    <p class="time_text">
                                        {{ $attendance?->work_end_hm }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="list_error">
                            <div class="list_position">
                                @error('work_start')
                                    <p class="error_message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="list_position">
                                @error('work_end')
                                    <p class="error_message">{{ $message }}</p>
                                @enderror
                            </div>
                    </td>
                </tr>
                @foreach ($breakRows as $index => $row)
                    <tr class="detail_tr">
                        <th class="detail_th">{{ $row['label'] }}</th>
                        <td class="detail_td">
                            <div class="list_time">
                                <div class="list_position">
                                    @if ($canEdit)
                                        <input class="time_input" type="text" name="breaks[{{ $index }}][start]" value="{{ old('breaks.' . $index . '.start', $row['start']) }}">
                                    @else
                                        <p class="time_text">
                                            {{ $row['start'] }}
                                        </p>
                                    @endif
                                </div>
                                <div class="list_interval">〜</div>
                                <div class="list_position">
                                    @if ($canEdit)
                                        <input class="time_input" type="text" name="breaks[{{ $index }}][end]" value="{{ old('breaks.' . $index . '.end', $row['end']) }}">
                                    @else
                                        <p class="time_text">
                                            {{ $row['end'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="list_error">
                                <div class="list_position">
                                    @foreach ($errors->get('breaks.' . $index . '.start') as $message)
                                        <p class="error_message">{{ $message }}</p>
                                    @endforeach
                                </div>
                                <div class="list_position">
                                    @foreach ($errors->get('breaks.' . $index . '.end') as $message)
                                        <p class="error_message">{{ $message }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <tr class="detail_tr">
                    <th class="detail_th">備考</th>
                    <td class="remarks_td">
                        @if ($canEdit)
                            <textarea class="remarks_textarea" name="text" rows="3">{{ old('text', $attendance?->text) }}</textarea>
                            @error('text')
                                <p class="error_textarea">{{ $message }}</p>
                            @enderror
                        @else
                            <p class="remarks_text">
                                {{ $attendanceRequest->text }}
                            </p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="button_area">
            @if ($canEdit)
                <button class="detail_button">修正</button>
            @else
                <p class="request_message">*承認待ちのため修正はできません。</p>
            @endif
        </div>
    </form>
</div>
@endsection