@extends('layouts.admin')
@section('title', 'スタッフ一覧画面(管理者)')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff_list.css') }}">
@endsection
@section('content')
<div class="content">
    <h2 class="page_title">スタッフ一覧</h2>
    <div class="table_div">
        <table class="attendance_table">
            <tr class="thead_tr">
                <th class="name_th">名前</th>
                <th class="email_th">メールアドレス</th>
                <th class="detail_th">月次勤怠</th>
            </tr>
            @foreach ($users as $user)
                <tr class="tbody_tr">
                    <td class="name_td">
                        {{ $user->name }}
                    </td>
                    <td class="email_td">
                        {{ $user->email }}
                    </td>
                    <td class="detail_td">
                        <a href="{{ route('admin.attendance.staff.form', ['id' => $user->id]) }}" class="detail_button">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection