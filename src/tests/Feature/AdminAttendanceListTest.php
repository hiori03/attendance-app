<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 1, 10));

        $this->admin = User::factory()->create([
            'role' => "admin",
        ]);

        $this->user = User::factory()->create();

        $this->user = User::factory()->create();

        Attendance::create([
            'user_id' => $this->admin->id,
            'day' => '2026-01-10',
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
            'status' => Attendance::STATUS_FINISHED,
        ]);

        Attendance::create([
            'user_id' => $this->user->id,
            'day' => '2026-01-10',
            'work_start' => '10:00:00',
            'work_end' => '19:00:00',
            'status' => Attendance::STATUS_FINISHED,
        ]);
    }

    /** @test */
    public function 管理者はその日の全ユーザーの勤怠情報を確認できる()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.attendance.list.form'));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee('2026/01/10');
    }

    /** @test */
    public function 勤怠一覧画面に現在の日付が表示される()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.attendance.list.form'));

        $response->assertStatus(200);
        $response->assertSee('2026/01/10');
        $response->assertSee('2026年01月10日');
    }

    /** @test */
    public function 前日ボタン押下で前日の勤怠情報が表示される()
    {
        $this->actingAs($this->admin);

        Attendance::create([
            'user_id' => $this->admin->id,
            'day' => '2026-01-09',
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
            'status' => Attendance::STATUS_FINISHED,
        ]);

        session(['attendance_day' => '2026-01-10']);

        $this->post(route('attendance.list.changeDay'), [
            'action' => 'prev',
        ]);

        $response = $this->get(route('admin.attendance.list.form'));

        $response->assertStatus(200);
        $response->assertSee('2026/01/09');
    }

    /** @test */
    public function 翌日ボタン押下で翌日の勤怠情報が表示される()
    {
        $this->actingAs($this->admin);

        Attendance::create([
            'user_id' => $this->admin->id,
            'day' => '2026-01-11',
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
            'status' => Attendance::STATUS_FINISHED,
        ]);

        session(['attendance_day' => '2026-01-10']);

        $this->post(route('attendance.list.changeDay'), [
            'action' => 'next',
        ]);

        $response = $this->get(route('admin.attendance.list.form'));

        $response->assertStatus(200);
        $response->assertSee('2026/01/11');
    }
}
