<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 勤怠打刻画面を開いた時点の現在日時が取得できている()
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 1, 10, 15));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('attendance.form'));

        $response->assertStatus(200);

        $this->assertTrue(
            now()->equalTo(Carbon::create(2026, 1, 1, 10, 15))
        );
    }
}
