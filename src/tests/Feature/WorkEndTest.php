<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkEndTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 退勤ボタンが正しく機能する()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'day' => today(),
            'work_start' => now()->subHours(8)->format('H:i:s'),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.form'));
        $response->assertStatus(200);
        $response->assertSee('退勤');

        $this->post(route('attendance.end'));

        $response = $this->get(route('attendance.form'));
        $response->assertSee('退勤済');
    }

    /** @test */
    public function 退勤時刻が正しく記録される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post(route('attendance.start'));

        $this->post(route('attendance.end'));

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'day' => today(),
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $attendance = Attendance::where('user_id', $user->id)
            ->where('day', today())
            ->first();

        $this->assertNotNull($attendance->work_end);
    }
}
