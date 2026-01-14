<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 自分の勤怠情報が全て表示されている()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        Attendance::create([
            'user_id' => $user->id,
            'day' => today(),
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $response = $this->get(route('attendance.list.form'));

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function 勤怠一覧画面に現在の月が表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $currentMonth = now()->format('Y/m');

        $response = $this->get(route('attendance.list.form'));

        $response->assertStatus(200);
        $response->assertSee($currentMonth);
    }

    /** @test */
    public function 前月ボタンで前月の勤怠が表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $prevMonth = now()->subMonth()->format('Y/m');

        $this->post(route('attendance.list.changeMonth'), [
            'action' => 'prev',
        ]);

        $response = $this->get(route('attendance.list.form'));

        $response->assertSee($prevMonth);
    }

    /** @test */
    public function 翌月ボタンで翌月の勤怠が表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $nextMonth = now()->addMonth()->format('Y/m');

        $this->post(route('attendance.list.changeMonth'), [
            'action' => 'next',
        ]);

        $response = $this->get(route('attendance.list.form'));

        $response->assertSee($nextMonth);
    }

    /** @test */
    public function 詳細ボタンを押すと勤怠詳細画面に遷移する()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'day' => today(),
            'work_start' => '09:00:00',
            'status' => Attendance::STATUS_WORKING,
        ]);

        $response = $this->post(route('attendance.detail.prepare'), [
            'attendance_id' => $attendance->id,
            'date' => today()->toDateString(),
        ]);

        $response->assertRedirect(
            route('attendance.detail.form', ['id' => $attendance->id])
        );
    }
}
