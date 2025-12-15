<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day',
        'work_start',
        'work_end',
        'status',
    ];

    public const STATUS_WORKING = 0;
    public const STATUS_BREAK = 1;
    public const STATUS_FINISHED = 2;

    public const STATUS = [
        self::STATUS_WORKING => '出勤中',
        self::STATUS_BREAK => '休憩中',
        self::STATUS_FINISHED => '退勤中',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function break_records()
    {
        return $this->hasMany(BreakRecord::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? '不明';
    }

    public static function todayForUser(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->where('day', today())
            ->first();
    }
}
