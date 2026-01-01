<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceDetailRequest;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\BreakRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListController extends Controller
{

    public function attendanceListForm(Request $request)
    {
        $month = session('attendance_month', now()->format('Y-m'));
        $carbonMonth = Carbon::createFromFormat('Y-m', $month);

        $startOfMonth = $carbonMonth->copy()->startOfMonth();
        $endOfMonth = $carbonMonth->copy()->endOfMonth();

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
        $carbonMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

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

        $id = $request->input('attendance_id');

        return redirect()->route('attendance.detail.form', $id ? ['id' => $id] : []);
    }

    public function attendanceDetailForm(Request $request, $id = null)
    {
        $user = auth()->user();

        $attendanceDate = session('attendance_date');

        if (!$attendanceDate) {
            return redirect()->route('attendance.list.form');
        }

        $date = Carbon::parse($attendanceDate);

        $attendance = null;
        $breakRecords = collect();
        $attendanceRequest = null;

        if ($id !== null ) {
            $attendance = Attendance::with('breakRecords')->find($id);

            if ($attendance) {
                $attendanceRequest = AttendanceRequest::getLatestPendingByAttendance($attendance);
            }
        } else {
            $attendanceRequest = AttendanceRequest::getLatestPendingByUserAndDate(auth()->id(), $date->toDateString());
        }

        $canEdit = !(
            $attendanceRequest &&
            $attendanceRequest->request_status === AttendanceRequest::REQUEST_STATUS_PENDING
        );

        $breakRows = [];

        $total = $breakRecords->count();
        if ($canEdit) {
            $total += 1;
        }

        for ($i = 0; $i < $total; $i++) {
            $breakRows[] = [
                'label' => $i === 0 ? '休憩' : '休憩' . ($i + 1),
                'start' => $breakRecords[$i]->start_hm ?? '',
                'end' => $breakRecords[$i]->end_hm ?? '',
            ];
        }

        return view('staff.attendance_detail', compact(
            'user',
            'attendance',
            'date',
            'breakRows',
            'attendanceRequest',
            'canEdit',
        ));
    }

    public function DetailRequest(AttendanceDetailRequest $request)
    {
        $attendance = null;

        if ($request->filled('attendance_id')) {
            $attendance = Attendance::find($request->attendance_id);
        }

        $attendanceRequest = AttendanceRequest::create([
            'attendance_id' => $attendance?->id,
            'user_id' => auth()->id(),
            'request_day' => $request->date,
            'new_work_start' => $request->work_start,
            'new_work_end' => $request->work_end,
            'text' => $request->text,
            'request_status' => AttendanceRequest::REQUEST_STATUS_PENDING,
        ]);

        foreach ($request->breaks ?? [] as $break) {
            if (empty($break['start']) && empty($break['end'])) {
                continue;
            }

            BreakRequest::create([
                'attendance_request_id' => $attendanceRequest->id,
                'request_day' => $attendanceRequest->request_day,
                'new_break_start' => $break['start'] ?? null,
                'new_break_end' => $break['end'] ?? null,
            ]);
        }

        return redirect()->route('attendance.detail.form', $attendance ? ['id' => $attendance->id] : []);
    }

    public function requestForm(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = AttendanceRequest::query();

        if ($status === 'approved') {
            $query->where('request_status', AttendanceRequest::REQUEST_STATUS_APPROVED);
        } else {
            $query->where('request_status', AttendanceRequest::REQUEST_STATUS_PENDING);
        }

        $requests = $query->get();

        return view('staff.request',compact('requests', 'status'));
    }
}