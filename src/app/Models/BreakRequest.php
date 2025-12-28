<?php

namespace App\Models;

use App\Models\AttendanceRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_request_id',
        'request_day',
        'new_break_start',
        'new_break_end',
    ];

    public function attendanceRequests()
    {
        return $this->belongsTo(AttendanceRequest::class);
    }
}
