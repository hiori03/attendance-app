<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'new_break_start',
        'new_break_end',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
