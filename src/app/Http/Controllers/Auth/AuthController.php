<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyEmail;

class AuthController extends Controller
{
    public function registerform()
    {
        return view('staff/register');
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

    public function loginform()
    {
        return view('staff/login');
    }

    public function emailform()
    {
        $userId = session('unverified_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        return view('staff/email', ['user' => $user]);
    }

    public function certification(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            Auth::login($user);

            return redirect('/attendance');
        }

        $user->email_verified_at = now();
        $user->save();

        Auth::login($user);

        session()->forget('unverified_user_id');

        return redirect('/attendance');
    }

    public function resend(Request $request)
    {
        $userId = session('unverified_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if ($user->email_verified_at) {
            session()->forget('unverified_user_id');

            return redirect('/attendance');
        }

        $this->sendVerificationMail($user);

        return back();
    }

    public function sendVerificationMail(User $user)
    {
        $url = URL::temporarySignedRoute(
            'email.certification',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        Mail::to($user->email)->send(new VerifyEmail($user, $url));
    }

}
