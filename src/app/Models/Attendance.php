<?php

namespace App\Models;

use App\Models\AttendanceRequest;
use App\Models\BreakRecord;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    public function breakRecords()
    {
        return $this->hasMany(BreakRecord::class);
    }

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? '不明';
    }

    public function getWorkStartHmAttribute()
    {
        return $this->work_start
            ? Carbon::parse($this->work_start)->format('H:i')
            : null;
    }

    public function getWorkEndHmAttribute()
    {
        return $this->work_end
            ? Carbon::parse($this->work_end)->format('H:i')
            : null;
    }

    public function getTotalBreakSecondsAttribute()
    {
        return $this->breakRecords->sum(function ($break) {
            if (!$break->break_end) {
                return 0;
            }

            return Carbon::parse($break->break_start)
                ->diffInSeconds(Carbon::parse($break->break_end));
        });
    }

    public function getBreakTimeHmAttribute()
    {
        if ($this->total_break_seconds <= 0) {
            return '';
        }

        return gmdate('H:i', $this->total_break_seconds);
    }

    public function getTotalWorkTimeHmAttribute()
    {
        if (!$this->work_start || !$this->work_end) {
            return '';
        }

        $workSeconds = Carbon::parse($this->work_start)
            ->diffInSeconds(Carbon::parse($this->work_end));

        $actualWorkSeconds = $workSeconds - $this->total_break_seconds;

        if ($actualWorkSeconds <= 0) {
            return '00:00';
        }

        $hours = floor($actualWorkSeconds / 3600);
        $minutes = floor(($actualWorkSeconds % 3600) / 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public static function todayForUser(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->where('day', today())
            ->first();
    }

    public static function getByUserAndMonth(
        int $userId,
        Carbon $start,
        Carbon $end
    ) {
        return self::with('breakRecords')
            ->where('user_id', $userId)
            ->whereBetween('day', [$start, $end])
            ->get()
            ->keyBy('day');
    }

    public static function getByDay(Carbon $day): Collection
    {
        return self::with('user')
            ->whereDate('day', $day)
            ->get();
    }

    public static function getByDayOrderByWorkStart(Carbon $day): Collection
    {
        return self::with('user')
            ->whereDate('day', $day)
            ->orderBy('work_start')
            ->get();
    }
}
