<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => '管理ユーザー',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $day = Carbon::today();

        $attendance =Attendance::create([
            'user_id' => $user->id,
            'day' => $day->toDateString(),
            'work_start' => '09:00:00',
            'work_end' => "18:00:00",
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $attendance->breakRecords()->create([
            'break_start' => "12:00:00",
            'break_end'   => "13:00:00",
        ]);
    }
}
