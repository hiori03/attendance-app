<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('staff.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        $this->sendVerificationMail($user);

        Auth::login($user);
        session(['unverified_user_id' => $user->id]);
        Auth::logout();

        return redirect()->route('email');
    }

    public function loginForm()
    {
        return view('staff.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'ログイン情報が登録されていません'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        if (! $user->email_verified_at) {
            $this->sendVerificationMail($user);
            session(['unverified_user_id' => $user->id]);
            Auth::logout();

            return redirect()->route('email');
        }

        return redirect()->route('attendance.form');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->forget('url.intended');

        return redirect()->route('login.form');
    }

    public function adminLoginForm()
    {
        return view('admin.login');
    }

    public function adminLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'ログイン情報が登録されていません'])->withInput();
        }

        if ($user->role !== 'admin') {
            return back()->withErrors(['email' => '管理者ユーザーではありません'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.attendance.list.form');
    }

    public function adminLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->forget('url.intended');

        return redirect()->route('admin.login.form');
    }

    public function emailForm()
    {
        $userId = session('unverified_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        return view('staff.email', ['user' => $user]);
    }

    public function certification(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            Auth::login($user);

            return redirect()->route('attendance.form');
        }

        $user->email_verified_at = now();
        $user->save();

        Auth::login($user);

        session()->forget('unverified_user_id');

        return redirect()->route('attendance.form');
    }

    public function resend()
    {
        $userId = session('unverified_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if ($user->email_verified_at) {
            session()->forget('unverified_user_id');

            return redirect()->route('attendance.form');
        }

        $this->sendVerificationMail($user);

        return back();
    }

    private function sendVerificationMail(User $user)
    {
        $url = URL::temporarySignedRoute(
            'email.certification',
            now()->addHour(),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        Mail::to($user->email)->send(new VerifyEmail($user, $url));
    }

}
