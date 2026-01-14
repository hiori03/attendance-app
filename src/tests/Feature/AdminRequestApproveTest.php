<?php

namespace Tests\Feature;

use App\Models\AttendanceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRequestApproveTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 承認待ちの修正申請が全て表示されている()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        AttendanceRequest::create([
            'user_id' => $user->id,
            'request_day' => '2026-01-10',
            'request_status' => AttendanceRequest::REQUEST_STATUS_PENDING,
            'text' => '承認待ち申請',
        ]);

        AttendanceRequest::create([
            'user_id' => $user->id,
            'request_day' => '2026-01-10',
            'request_status' => AttendanceRequest::REQUEST_STATUS_APPROVED,
            'text' => '承認済み申請',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('stamp_correction_request.form', [
                'status' => AttendanceRequest::REQUEST_STATUS_PENDING,
            ]));

        $response->assertStatus(200);
        $response->assertSee('承認待ち申請');
        $response->assertDontSee('承認済み申請');
    }

    /** @test */
    public function 承認済みの修正申請が全て表示されている()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        AttendanceRequest::create([
            'user_id' => $user->id,
            'request_day' => '2026-01-10',
            'request_status' => AttendanceRequest::REQUEST_STATUS_PENDING,
            'text' => '未承認',
        ]);

        AttendanceRequest::create([
            'user_id' => $user->id,
            'request_day' => '2026-01-10',
            'request_status' => AttendanceRequest::REQUEST_STATUS_APPROVED,
            'text' => '承認済み',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('stamp_correction_request.form', [
                'status' => AttendanceRequest::REQUEST_STATUS_APPROVED,
            ]));

        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertDontSee('未承認');
    }

    /** @test */
    public function 修正申請の詳細内容が正しく表示されている()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['name' => 'テストユーザー']);

        $request = AttendanceRequest::create([
            'user_id' => $user->id,
            'request_day' => '2026-01-10',
            'new_work_start' => '09:00',
            'new_work_end' => '18:00',
            'text' => '修正理由',
            'request_status' => AttendanceRequest::REQUEST_STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('stamp_correction_request.approve.form', $request->id));

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('2026年');
        $response->assertSee('1月10日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('修正理由');
    }

    /** @test */
    public function 修正申請の承認処理が正しく行われる()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create();

        $attendanceRequest = AttendanceRequest::create([
            'user_id' => $user->id,
            'request_day' => '2026-01-10',
            'new_work_start' => '10:00',
            'new_work_end' => '19:00',
            'text' => '修正理由',
            'request_status' => AttendanceRequest::REQUEST_STATUS_PENDING,
        ]);

        $this->actingAs($admin)->post(
            route('stamp_correction_request.approve.confirmation', $attendanceRequest->id),
            [
                'user_id' => $user->id,
                'date' => '2026-01-10',
            ]
        );

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $attendanceRequest->id,
            'request_status' => AttendanceRequest::REQUEST_STATUS_APPROVED,
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'day' => '2026-01-10',
            'work_start' => '10:00:00',
            'work_end' => '19:00:00',
        ]);
    }
}
