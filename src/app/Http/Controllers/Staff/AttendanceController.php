<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BreakRecord;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function attendanceForm()
    {
        $attendance = Attendance::todayForUser(auth()->id());

        return view('staff.attendance', compact('attendance'));
    }

    public function attendanceStart()
    {
        Attendance::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'day' => now()->toDateString(),
            ],
            [
                'work_start' => now()->format('H:i:s'),
                'status' => Attendance::STATUS_WORKING,
            ]
        );

        return redirect()->route('attendance.form');
    }

    public function attendanceEnd()
    {
        Attendance::where('user_id', auth()->id())->where('day', today())->update([
            'work_end' => now()->format('H:i:s'),
            'status' => Attendance::STATUS_FINISHED,
        ]);

        return redirect()->route('attendance.form');
    }

    public function breakStart()
    {
        $attendance = Attendance::todayForUser(auth()->id());

        $attendance->update([
            'status' => Attendance::STATUS_BREAK,
        ]);

        BreakRecord::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->format('H:i:s'),
        ]);

        return redirect()->route('attendance.form');
    }

    public function breakEnd()
    {
        $attendance = Attendance::todayForUser(auth()->id());

        $attendance->update([
            'status' => Attendance::STATUS_WORKING,
        ]);

        $breakRecord = BreakRecord::where('attendance_id', $attendance->id)->whereNull('break_end')->latest()->first();

        $breakRecord->update([
            'break_end' => now()->format('H:i:s'),
        ]);

        return redirect()->route('attendance.form');
    }
}
