<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BreakRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['attendanceForm']);
    }

    public function attendanceForm()
    {
        $attendance = Attendance::where('user_id', auth()->id())->where('day', now()->toDateString())->first();

        return view('staff.attendance', compact('attendance'));
    }

    public function attendanceStart()
    {
        Attendance::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'day'     => now()->toDateString(),
            ],
            [
                'work_start' => now()->format('H:i:s'),
                'status'     => 0,
            ]
        );

        return redirect()->route('attendance.form');
    }

    public function attendanceEnd()
    {
        Attendance::where('user_id', Auth::id())->where('day', today())->update([
            'work_end' => now()->format('H:i:s'),
            'status' => 2,
        ]);

        return redirect()->route('attendance.form');
    }

    public function breakStart()
    {
        $attendance = Attendance::where('user_id', Auth::id())->where('day', today())->first();

        $attendance->update([
            'status' => 1,
        ]);

        BreakRecord::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->format('H:i:s'),
        ]);

        return redirect()->route('attendance.form');
    }

    public function breakEnd()
    {
        $attendance = Attendance::where('user_id', Auth::id())->where('day', today())->first();

        $attendance->update([
            'status' => 0,
        ]);

        $breakRecord = BreakRecord::where('attendance_id', $attendance->id)->whereNull('break_end')->latest()->first();

        $breakRecord->update([
            'break_end' => now()->format('H:i:s'),
        ]);

        return redirect()->route('attendance.form');
    }
}
