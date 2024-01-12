<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "username" => ["required","email","max:100"],
            "password" => ["required", Password::min(6)->letters()->numbers()->symbols()]
        ];
    }

    // mempersiapkan data sebelum validasi formulir dilakukan
    protected function prepareForValidation() : void
    {
        $this->merge([
            "username" => strtolower($this->input("username"))
        ]);
    }

    // setelah validasi formulir berhasil dilakukan
    protected function passedValidation() : void
    {
        $this->merge([
            "password" => bcrypt($this->input("password"))
        ]);
    }


}
