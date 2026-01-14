<?php

namespace Tests\Feature;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 会員登録後、認証メールが送信される()
    {
        Mail::fake();

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        Mail::assertSent(VerifyEmail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });

        $response->assertRedirect(route('email'));
    }

    /** @test */
    public function メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        session(['unverified_user_id' => $user->id]);

        $url = URL::temporarySignedRoute(
            'email.certification',
            now()->addHour(),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('attendance.form'));
    }

    /** @test */
    public function メール認証サイトのメール認証を完了すると、勤怠登録画面に遷移する()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute(
            'email.certification',
            now()->addHour(),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('attendance.form'));

        $this->assertNotNull(
            $user->fresh()->email_verified_at
        );

        $this->assertAuthenticatedAs($user);
    }
}
