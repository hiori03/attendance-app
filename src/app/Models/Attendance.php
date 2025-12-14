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

    public const STATUS = [
        0 => '出勤中',
        1 => '休憩中',
        2 => '退勤中',
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
}
