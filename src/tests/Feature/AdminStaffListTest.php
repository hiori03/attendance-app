<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffListTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser()
    {
        return User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function 管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる()
    {
        $admin = $this->adminUser();

        User::create([
            'name' => 'テスト1',
            'email' => 'test1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'テスト2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.staff.list.form'));

        $response->assertStatus(200);
        $response->assertSee('テスト1');
        $response->assertSee('test1@example.com');
        $response->assertSee('テスト2');
        $response->assertSee('test2@example.com');
    }

    /** @test */
    public function ユーザーの勤怠情報が正しく表示される()
    {
        $admin = $this->adminUser();

        $user = User::create([
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'day' => '2026-01-10',
            'work_start' => '09:00',
            'work_end' => '18:00',
            'status' => Attendance::STATUS_FINISHED,
        ]);

        session(['attendance_month' => '2026-01']);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.staff.form', ['id' => $user->id]));

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function 「前月」を押下した時に表示月の前月の情報が表示される()
    {
        $admin = $this->adminUser();

        $user = User::create([
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        session(['attendance_month' => '2026-02']);

        $this->actingAs($admin)
            ->post(route('admin.attendance.staff.changeMonth', ['id' => $user->id]), [
                'action' => 'prev',
            ]);

        $this->assertEquals('2026-01', session('attendance_month'));
    }

    /** @test */
    public function 「翌月」を押下した時に表示月の前月の情報が表示される()
    {
        $admin = $this->adminUser();

        $user = User::create([
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        session(['attendance_month' => '2026-01']);

        $this->actingAs($admin)
            ->post(route('admin.attendance.staff.changeMonth', ['id' => $user->id]), [
                'action' => 'next',
            ]);

        $this->assertEquals('2026-02', session('attendance_month'));
    }

    /** @test */
    public function 「詳細」を押下すると、その日の勤怠詳細画面に遷移する()
    {
        $admin = $this->adminUser();

        $user = User::create([
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.staff.list.form'));

        $response->assertSee(
            route('admin.attendance.staff.form', ['id' => $user->id])
        );
    }
}
