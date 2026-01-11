<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'request_day',
        'new_work_start',
        'new_work_end',
        'text',
        'request_status',
    ];

    public const REQUEST_STATUS_PENDING = 0;
    public const REQUEST_STATUS_APPROVED = 1;

    public const REQUEST_STATUS = [
        self::REQUEST_STATUS_PENDING  => '承認待ち',
        self::REQUEST_STATUS_APPROVED => '承認済み',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakRequests()
    {
        return $this->hasMany(BreakRequest::class);
    }

    public function getNewWorkStartHmAttribute()
    {
        return $this->new_work_start
            ? Carbon::parse($this->new_work_start)->format('H:i')
            : '';
    }

    public function getNewWorkEndHmAttribute()
    {
        return $this->new_work_end
            ? Carbon::parse($this->new_work_end)->format('H:i')
            : '';
    }

    public function getRequestStatusLabelAttribute()
    {
        return self::REQUEST_STATUS[$this->request_status] ?? '不明';
    }

    public function getFormattedRequestDayAttribute()
    {
        return \Carbon\Carbon::parse($this->request_day)->format('Y/n/j');
    }

    public static function getLatestPendingByAttendance(Attendance $attendance)
    {
        return self::where('attendance_id', $attendance->id)
            ->where('request_status', self::REQUEST_STATUS_PENDING)
            ->latest()
            ->first();
    }

    public static function getLatestPendingByUserAndDate(int $userId, string $date)
    {
        return self::where('user_id', $userId)
            ->where('request_day', $date)
            ->where('request_status', self::REQUEST_STATUS_PENDING)
            ->latest()
            ->first();
    }
}
