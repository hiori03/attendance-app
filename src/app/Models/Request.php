<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'new_work_start',
        'new_work_end',
        'text',
        'request_status',
    ];

    public const REQUEST_STATUS = [
        0 => '承認待ち',
        1 => '承認済み',
    ];

    public function getRequestStatusLabelAttribute()
    {
        return self::REQUEST_STATUS[$this->request_status] ?? '不明';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function newBreaks()
    {
        return $this->hasMany(NewBreak::class);
    }
}
