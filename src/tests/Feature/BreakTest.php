<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 休憩ボタンが正しく機能する()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'day' => today(),
            'work_start' => now()->format('H:i:s'),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.form'));
        $response->assertSee('休憩入');

        $this->post(route('break.start'));

        $response = $this->get(route('attendance.form'));
        $response->assertSee('休憩中');

        $this->assertDatabaseHas('break_records', [
            'attendance_id' => $attendance->id,
        ]);
    }

    /** @test */
    public function 休憩は一日に何回でもできる()
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

        $this->post(route('break.start'));
        $this->post(route('break.end'));

        $response = $this->get(route('attendance.form'));
        $response->assertSee('休憩入');
    }

    /** @test */
    public function 休憩戻ボタンが正しく機能する()
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

        $this->post(route('break.start'));

        $response = $this->get(route('attendance.form'));
        $response->assertSee('休憩戻');

        $this->post(route('break.end'));

        $response = $this->get(route('attendance.form'));
        $response->assertSee('出勤中');
    }

    /** @test */
    public function 休憩戻は一日に何回でもできる()
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

        $this->post(route('break.start'));
        $this->post(route('break.end'));

        $this->post(route('break.start'));

        $response = $this->get(route('attendance.form'));
        $response->assertSee('休憩戻');
    }

    /** @test */
    public function 休憩時刻が正しく記録される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'day' => today(),
            'work_start' => now()->format('H:i:s'),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        $this->post(route('break.start'));
        $this->post(route('break.end'));

        $this->assertDatabaseHas('break_records', [
            'attendance_id' => $attendance->id,
        ]);
    }
}
