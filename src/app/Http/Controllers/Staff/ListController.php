<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListController extends Controller
{

    public function attendanceListForm(Request $request)
    {
        $month = session('attendance_month', now()->format('Y-m'));
        $carbonMonth = Carbon::createFromFormat('Y-m', $month);

        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $displayMonth = $startOfMonth->format('Y/m');

        $attendances = Attendance::getByUserAndMonth(
            auth()->id(),
            $startOfMonth,
            $endOfMonth
        );

        $days = [];
        $date = $startOfMonth->copy();

        while ($date <= $endOfMonth) {
            $days[] = [
                'date'       => $date->copy(),
                'attendance' => $attendances[$date->toDateString()] ?? null,
            ];
            $date->addDay();
        }

        return view('staff.attendance_list', compact(
            'month',
            'startOfMonth',
            'endOfMonth',
            'displayMonth',
            'days'
        ));
    }

    public function changeMonth(Request $request)
    {
        $month = session('attendance_month', now()->format('Y-m'));
        $carbonMonth = Carbon::createFromFormat('Y-m', $month);

        if ($request->input('action') === 'prev') {
            $carbonMonth->subMonth();
        } elseif ($request->input('action') === 'next') {
            $carbonMonth->addMonth();
        }

        session(['attendance_month' => $carbonMonth->format('Y-m')]);

        return redirect()->route('attendance.list.form');
    }

    public function prepareDetail(Request $request)
    {
        session([
            'attendance_date' => $request->input('date'),
        ]);

        return redirect()->route('attendance.detail.form', [
            'id' => $request->input('attendance_id')
        ]);
    }

    public function attendanceDetailForm()
    {
        return view('staff.attendance_detail');
    }
}