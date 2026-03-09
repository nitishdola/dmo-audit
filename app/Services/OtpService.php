<?php 
namespace App\Services;

use App\Models\User;
use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OtpService
{
    public function generate(User $user): string
    {
        $otp = rand(100000, 999999);

        DB::transaction(function () use ($user, $otp) {
            $user->otps()->delete();

            OtpCode::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
            ]);
        });

        // Here integrate SMS provider
        return $otp; // return for testing
    }

    public function verify(User $user, string $otp): bool
    {
        $record = $user->otps()
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();
        if (!$record) {
            return false;
        }

        $record->update(['is_used' => true]);

        return true;
    }
}