<?php

namespace App\Models;

use App\Models\AttendanceRequest;
use Carbon\Carbon;
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

    public function getNewBreakStartHmAttribute()
    {
        return $this->new_break_start
            ? Carbon::parse($this->new_break_start)->format('H:i')
            : '';
    }

    public function getNewBreakEndHmAttribute()
    {
        return $this->new_break_end
            ? Carbon::parse($this->new_break_end)->format('H:i')
            : '';
    }
}
