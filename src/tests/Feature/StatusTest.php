<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 勤務外の場合、勤怠ステータスが正しく表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('attendance.form'));

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /** @test */
    public function 出勤中の場合、勤怠ステータスが正しく表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'day' => today(),
            'work_start' => now()->format('H:i:s'),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.form'));

        $response->assertSee('出勤中');
    }

    /** @test */
    public function 休憩中の場合、勤怠ステータスが正しく表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'day' => today(),
            'work_start' => now()->format('H:i:s'),
            'status' => Attendance::STATUS_BREAK,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.form'));

        $response->assertSee('休憩中');
    }

    /** @test */
    public function 退勤済の場合、勤怠ステータスが正しく表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'day' => today(),
            'work_start' => now()->subHours(8)->format('H:i:s'),
            'work_end' => now()->format('H:i:s'),
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.form'));

        $response->assertSee('退勤済');
    }
}