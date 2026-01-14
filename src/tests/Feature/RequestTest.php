<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Attendance $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'day' => '2026-01-01',
            'status' => Attendance::STATUS_FINISHED,
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
        ]);
    }

    /** @test */
    public function 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $this->actingAs($this->user);

        $response = $this->post(
            route('attendance.detail.request'),
            [
                'attendance_id' => $this->attendance->id,
                'date' => '2026-01-01',
                'work_start' => '19:00',
                'work_end' => '18:00',
                'text' => '修正理由',
            ]
        );

        $response->assertSessionHasErrors([
            'work_start' => '出勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $this->actingAs($this->user);

        $response = $this->post(
            route('attendance.detail.request'),
            [
                'attendance_id' => $this->attendance->id,
                'date' => '2026-01-01',
                'work_start' => '09:00',
                'work_end' => '18:00',
                'text' => '修正理由',
                'breaks' => [
                    [
                        'start' => '19:00',
                        'end' => '19:30',
                    ],
                ],
            ]
        );

        $response->assertSessionHasErrors([
            'breaks.0.start' => '休憩時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $this->actingAs($this->user);

        $response = $this->post(
            route('attendance.detail.request'),
            [
                'attendance_id' => $this->attendance->id,
                'date' => '2026-01-01',
                'work_start' => '09:00',
                'work_end' => '18:00',
                'text' => '修正理由',
                'breaks' => [
                    [
                        'start' => '17:00',
                        'end' => '19:00',
                    ],
                ],
            ]
        );

        $response->assertSessionHasErrors([
            'breaks.0.end' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 備考欄が未入力の場合のエラーメッセージが表示される()
    {
        $this->actingAs($this->user);

        $response = $this->post(
            route('attendance.detail.request'),
            [
                'attendance_id' => $this->attendance->id,
                'date' => '2026-01-01',
                'work_start' => '09:00',
                'work_end' => '18:00',
            ]
        );

        $response->assertSessionHasErrors([
            'text' => '備考を記入してください',
        ]);
    }

    /** @test */
    public function 修正申請処理が実行される()
    {
        $this->actingAs($this->user);

        $this->post(
            route('attendance.detail.request'),
            [
                'attendance_id' => $this->attendance->id,
                'date' => '2026-01-01',
                'work_start' => '10:00',
                'work_end' => '18:00',
                'text' => '修正理由',
            ]
        );

        $this->assertDatabaseHas('attendance_requests', [
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'request_status' => \App\Models\AttendanceRequest::REQUEST_STATUS_PENDING,
        ]);
    }

    /** @test */
    public function 「承認待ち」にログインユーザーが行った申請が全て表示されていること()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('stamp_correction_request.form'));

        $response->assertStatus(200);
        $response->assertSee('承認待ち');
    }

    /** @test */
    public function 「承認済み」に管理者が承認した修正申請が全て表示されている()
    {
        $this->actingAs($this->user);

        AttendanceRequest::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'request_day' => '2026-01-01',
            'text' => '修正理由',
            'request_status' => AttendanceRequest::REQUEST_STATUS_APPROVED,
        ]);

        $response = $this->get(
            route('stamp_correction_request.form', [
                'status' => AttendanceRequest::REQUEST_STATUS_APPROVED,
            ])
        );

        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertSee($this->user->name);
        $response->assertSee('修正理由');
        $response->assertSee('2026/1/1');
    }

    /** @test */
    public function 各申請の「詳細」を押下すると勤怠詳細画面に遷移する()
    {
        $this->actingAs($this->user);

        $response = $this->post(
            route('attendance.detail.prepare'),
            [
                'attendance_id' => $this->attendance->id,
                'date' => $this->attendance->day,
            ]
        );

        $response->assertRedirect(
            route('attendance.detail.form', ['id' => $this->attendance->id])
        );
    }
}
