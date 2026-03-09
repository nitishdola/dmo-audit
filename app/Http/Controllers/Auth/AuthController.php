<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
class AuthController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function sendOtp(SendOtpRequest $request)
    {
        $user = User::where('mobile', $request->mobile)->firstOrFail();
        $otp = $this->otpService->generate($user);

        return redirect()
            ->route('auth.verify.form', ['mobile' => $user->mobile])
            ->with('success', "OTP sent (Testing: $otp)");
    }

    public function showVerifyForm()
    {
        return view('auth.verify');
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $user = User::where('mobile', $request->mobile)->firstOrFail();

        if (!$this->otpService->verify($user, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        Auth::login($user);

        return redirect()->intended(
            match ($user->role) {
                UserRole::ADMIN => route('admin.dashboard'),
                UserRole::DMO   => route('dmo.dashboard'),
                default         => route('auth.login'),
            }
        );
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('auth.login');
    }
}
