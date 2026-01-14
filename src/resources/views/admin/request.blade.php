@extends('layouts.admin')
@section('title', '申請一覧画面(管理者)')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/request.css') }}">
@endsection
@section('content')
<div class="content">
    <h2 class="page_title">申請一覧</h2>
    <div class="tab_div">
        <a href="{{ route('stamp_correction_request.form', ['status' => \App\Models\AttendanceRequest::REQUEST_STATUS_PENDING]) }}" class="tab {{ (int)$status === \App\Models\AttendanceRequest::REQUEST_STATUS_PENDING ? 'active' : '' }}">
            {{ \App\Models\AttendanceRequest::REQUEST_STATUS[\App\Models\AttendanceRequest::REQUEST_STATUS_PENDING] }}
        </a>
        <a href="{{ route('stamp_correction_request.form', ['status' => \App\Models\AttendanceRequest::REQUEST_STATUS_APPROVED]) }}" class="tab {{ (int)$status === \App\Models\AttendanceRequest::REQUEST_STATUS_APPROVED ? 'active' : '' }}">
            {{ \App\Models\AttendanceRequest::REQUEST_STATUS[\App\Models\AttendanceRequest::REQUEST_STATUS_APPROVED] }}
        </a>
    </div>
    <div class="table_div">
        <table class="attendance_table">
            <tr class="thead_tr">
                <th class="status_th">状態</th>
                <th class="name_th">名前</th>
                <th class="tag_th">対象日時</th>
                <th class="tag_th">申請理由</th>
                <th class="tag_th">申請日時</th>
                <th class="detail_th">詳細</th>
            </tr>
            @foreach ($requests as $request)
                <tr class="tbody_tr">
                    <td class="status_td">
                        {{ $request->request_status_label }}
                    </td>
                    <td class="name_td ellipsis">
                        {{ $request->user->name }}
                    </td>
                    <td class="tag_td">
                        {{ $request->formatted_request_day }}
                    </td>
                    <td class="tag_td ellipsis">
                        {{ $request->text }}
                    </td>
                    <td class="tag_td">
                        {{ $request->created_at->format('Y/m/d') }}
                    </td>
                    <td class="detail_td">
                        <a href="{{ route('stamp_correction_request.approve.form', $request->id) }}" class="detail_button">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection