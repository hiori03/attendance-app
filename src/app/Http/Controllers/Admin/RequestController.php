<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function requestForm(Request $request)
    {
        $status = (int) $request->query(
            'status',
            AttendanceRequest::REQUEST_STATUS_PENDING
        );

        $query = AttendanceRequest::query();

        $query->where('request_status', $status);

        $requests = $query->get();

        return view('admin.request', compact('requests', 'status'));
    }

    public function requestApproveForm(Request $request, $id)
    {
        $attendanceRequest = AttendanceRequest::findOrFail($id);

        $user = User::findOrFail($attendanceRequest->user_id);

        $date = Carbon::parse($attendanceRequest->request_day);

        if ($attendanceRequest->attendance_id) {
            $attendance = Attendance::with('breakRecords')
                ->find($attendanceRequest->attendance_id);
        } else {
            $attendance = Attendance::with('breakRecords')
                ->where('user_id', $attendanceRequest->user_id)
                ->whereDate('day', $attendanceRequest->request_day)
                ->first();
        }

        $breakRequests = \App\Models\BreakRequest::where(
            'attendance_request_id',
            $attendanceRequest->id
        )->get();

        $canEdit = $attendanceRequest->request_status
            === AttendanceRequest::REQUEST_STATUS_PENDING;

        $breakRows = $breakRequests->map(function ($br, $index) {
            return [
                'index' => $index,
                'label' => '休憩' . ($index + 1),
                'start' => $br->new_break_start_hm,
                'end' => $br->new_break_end_hm,
            ];
        });

        return view('admin.request_approve', compact(
            'user',
            'attendance',
            'attendanceRequest',
            'date',
            'breakRows',
            'canEdit',
        ));
    }

    public function approveConfirmation(Request $request, int $attendance_correct_request_id)
    {
        DB::transaction(function () use ($request) {

            $attendanceRequest = AttendanceRequest::where('user_id', $request->user_id)
                ->whereDate('request_day', $request->date)
                ->where('request_status', AttendanceRequest::REQUEST_STATUS_PENDING)
                ->firstOrFail();

            if ($request->filled('attendance_id')) {
                $attendance = Attendance::findOrFail($request->attendance_id);

                $attendance->update([
                    'work_start' => $attendanceRequest->new_work_start,
                    'work_end' => $attendanceRequest->new_work_end,
                ]);
            } else {
                $attendance = Attendance::create([
                    'user_id' => $request->user_id,
                    'day' => $request->date,
                    'work_start' => $attendanceRequest->new_work_start,
                    'work_end' => $attendanceRequest->new_work_end,
                ]);
            }

            $attendance->breakRecords()->delete();

            foreach ($attendanceRequest->breakRequests as $breakRequest) {
                if (empty($breakRequest->new_break_start)) {
                    continue;
                }

                $attendance->breakRecords()->create([
                    'break_start' => $breakRequest->new_break_start,
                    'break_end'   => $breakRequest->new_break_end,
                ]);
            }

            $attendanceRequest->update([
                'request_status' => AttendanceRequest::REQUEST_STATUS_APPROVED,
            ]);
        });

        return redirect()->route('stamp_correction_request.approve.form', ['attendance_correct_request_id' => $attendance_correct_request_id]);
    }
}
