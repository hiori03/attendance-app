<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceDetailRequest;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListController extends Controller
{
    public function attendanceListForm(Request $request)
    {
        $day = session('attendance_day', now()->toDateString());
        $carbonDay = Carbon::parse($day);

        $titleDay = $carbonDay->format('Y年m月d日');

        $displayDay = $carbonDay->format('Y/m/d');

        $attendances = Attendance::getByDay($carbonDay)
            ->keyBy('user_id');

        $days = Attendance::getByDayOrderByWorkStart($carbonDay)
            ->map(function ($attendance) use ($carbonDay) {
                return [
                    'date'       => $carbonDay,
                    'attendance' => $attendance,
                ];
            });

        return view('admin.attendance_list', compact(
            'titleDay',
            'displayDay',
            'days'
        ));
    }

    public function changeDay(Request $request)
    {
        $day = session('attendance_day', now()->toDateString());
        $carbonDay = Carbon::parse($day);

        if ($request->input('action') === 'prev') {
            $carbonDay->subDay();
        } elseif ($request->input('action') === 'next') {
            $carbonDay->addDay();
        }

        session(['attendance_day' => $carbonDay->toDateString()]);

        return redirect()->route('admin.attendance.list.form');
    }

    public function adminAttendanceDetailForm(int $id)
    {
        $attendance = Attendance::with(['breakRecords', 'user'])->findOrFail($id);

        $user = $attendance->user;
        $date = Carbon::parse($attendance->day);

        $attendanceRequest = AttendanceRequest::where('attendance_id', $attendance->id)->where('request_status', AttendanceRequest::REQUEST_STATUS_PENDING)->latest()->first();

        $canEdit = !($attendanceRequest && $attendanceRequest->request_status === AttendanceRequest::REQUEST_STATUS_PENDING);

        $breakRows = [];

        $breakRecords = $attendance->breakRecords;
        $total = $breakRecords->count();

        if ($canEdit) {
            $total += 1;
        }

        for ($i = 0; $i < $total; $i++) {
            $breakRecord = $breakRecords->get($i);

            $breakRows[] = [
                'index' => $i,
                'label' => $i === 0 ? '休憩' : '休憩' . ($i + 1),
                'start' => $breakRecord ? $breakRecord->start_hm : '',
                'end'   => $breakRecord ? $breakRecord->end_hm : '',
            ];
        }

        return view('admin.attendance_detail', compact(
            'user',
            'attendance',
            'date',
            'breakRows',
            'attendanceRequest',
            'canEdit',
        ));
    }

    public function adminDetailRequest(AttendanceDetailRequest $request)
    {
        $attendance = DB::transaction(function () use ($request) {
            if ($request->filled('attendance_id')) {
                $attendance = Attendance::findOrFail($request->attendance_id);
            } else {
                $attendance = Attendance::create([
                    'user_id' => $request->user_id,
                    'day' => $request->date,
                    'work_start' => $request->work_start,
                    'work_end' => $request->work_end,
                    'status' => Attendance::STATUS_FINISHED,
                ]);
            }

            $attendance->update([
                'work_start' => $request->work_start,
                'work_end' => $request->work_end,
            ]);

            $attendance->breakRecords()->delete();

            foreach ($request->input('breaks', []) as $break) {
                if (empty($break['start']) && empty($break['end'])) {
                    continue;
                }

                $attendance->breakRecords()->create([
                    'break_start' => $break['start'],
                    'break_end' => $break['end'],
                ]);
            }

            return $attendance;
        });

        return redirect()->route('admin.attendance.detail.form', ['id' => $attendance->id]);
    }
}