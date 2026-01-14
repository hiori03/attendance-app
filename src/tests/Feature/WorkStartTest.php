<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WorkStartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出勤ボタンが正しく機能する()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('attendance.form'));

        $response->assertStatus(200);
        $response->assertSee('出勤');

        $this->post(route('attendance.start'));

        $response = $this->get(route('attendance.form'));

        $response->assertSee('出勤中');
    }

    /** @test */
    public function 出勤は一日一回のみできる()
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

        $response->assertStatus(200);
        $response->assertDontSee(
            'action="' . route('attendance.start') . '"', false
        );
    }

    /** @test */
    public function 出勤時刻が勤怠一覧画面で確認できる()
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 12, 9, 0, 0));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post(route('attendance.start'));

        $response = $this->get(route('attendance.list.form'));

        $response->assertStatus(200);
        $response->assertSee('09:00');

        Carbon::setTestNow();
    }
}
