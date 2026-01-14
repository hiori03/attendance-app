<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Attendance $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-01-01');

        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'day' => '2026-01-01',
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
            'status' => Attendance::STATUS_WORKING,
        ]);

        BreakRecord::create([
            'attendance_id' => $this->attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
    }

    private function openDetailPage()
    {
        $this->actingAs($this->user);

        session([
            'attendance_date' => '2026-01-01',
        ]);

        return $this->get(
            route('attendance.detail.form', ['id' => $this->attendance->id])
        );
    }

    /** @test */
    public function 勤怠詳細画面の名前がログインユーザーの氏名になっている()
    {
        $response = $this->openDetailPage();

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
    }

    /** @test */
    public function 勤怠詳細画面の日付が選択した日付になっている()
    {
        $response = $this->openDetailPage();

        $response->assertSee('2026年');
        $response->assertSee('1月1日');
    }

    /** @test */
    public function 出勤退勤時間がログインユーザーの打刻と一致している()
    {
        $response = $this->openDetailPage();

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function 休憩時間がログインユーザーの打刻と一致している()
    {
        $response = $this->openDetailPage();

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
