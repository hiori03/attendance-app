<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function attendanceListForm(Request $request)
    {
        $day = session('attendance_day', now()->toDateString());
        $carbonDay = Carbon::parse($day);

        $titleDay = $carbonDay->format('Y年m月d日');

        $displayDay = $carbonDay->format('Y/m/d');

        $attendances = Attendance::with('user')
            ->whereDate('day', $carbonDay)
            ->get()
            ->keyBy('user_id');

        $days = Attendance::with('user')
            ->whereDate('day', $carbonDay)
            ->orderBy('work_start')
            ->get()
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

    public function adminAttendanceDetailForm()
    {
        return view('admin.attendance_detail');
    }
}
