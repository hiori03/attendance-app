<?php

namespace App\Models;

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

    public function getRequestStatusLabelAttribute()
    {
        return self::REQUEST_STATUS[$this->request_status] ?? '不明';
    }
}
