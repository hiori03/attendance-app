<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Attendance $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-01-10');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user  = User::factory()->create();

        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'day' => '2026-01-10',
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
            'status' => Attendance::STATUS_FINISHED,
        ]);

        AttendanceRequest::create([
            'user_id' => $this->user->id,
            'attendance_id' => $this->attendance->id,
            'request_day' => '2026-01-10',
            'text' => '通常勤務',
            'request_status' => AttendanceRequest::REQUEST_STATUS_PENDING,
        ]);
    }

    /** @test */
    public function 勤怠詳細画面に表示されるデータが選択したものになっている()
    {
        $this->actingAs($this->admin);

        session([
            'attendance_date' => $this->attendance->day,
            'attendance_user_id' => $this->user->id,
        ]);

        $response = $this->get(
            route('admin.attendance.detail.form', ['id' => $this->attendance->id])
        );

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('通常勤務');
    }

    /** @test */
    public function 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $this->actingAs($this->admin);

        session([
            'attendance_date' => $this->attendance->day,
            'attendance_user_id' => $this->user->id,
        ]);

        $response = $this->post(route('admin.attendance.detail.request'), [
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'date' => $this->attendance->day,
            'work_start' => '19:00',
            'work_end' => '18:00',
            'text' => 'テスト',
        ]);

        $response->assertSessionHasErrors([
            'work_start' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $this->actingAs($this->admin);

        session([
            'attendance_date' => $this->attendance->day,
            'attendance_user_id' => $this->user->id,
        ]);

        $response = $this->post(route('admin.attendance.detail.request'), [
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'date' => $this->attendance->day,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'breaks' => [
                ['start' => '19:00', 'end' => '19:30'],
            ],
            'text' => 'テスト',
        ]);

        $response->assertSessionHasErrors([
            'breaks.0.start' => '休憩時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $this->actingAs($this->admin);

        session([
            'attendance_date'    => $this->attendance->day,
            'attendance_user_id' => $this->user->id,
        ]);

        $response = $this->post(route('admin.attendance.detail.request'), [
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'date' => $this->attendance->day,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'breaks' => [
                ['start' => '17:00', 'end' => '19:00'],
            ],
            'text' => 'テスト',
        ]);

        $response->assertSessionHasErrors([
            'breaks.0.end' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 備考欄が未入力の場合のエラーメッセージが表示される()
    {
        $this->actingAs($this->admin);

        session([
            'attendance_date'    => $this->attendance->day,
            'attendance_user_id' => $this->user->id,
        ]);

        $response = $this->post(route('admin.attendance.detail.request'), [
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'date' => $this->attendance->day,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'text' => '',
        ]);

        $response->assertSessionHasErrors([
            'text' => '備考を記入してください',
        ]);
    }
}