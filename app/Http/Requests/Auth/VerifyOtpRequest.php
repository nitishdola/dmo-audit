<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => 'required|exists:users,mobile',
            'otp' => 'required|digits:6'
        ];
    }
}
