<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 8) {
            $fail('Mật khẩu phải có ít nhất 8 ký tự.');

            return;
        }
        if (! preg_match('/[A-Z]/', $value)) {
            $fail('Mật khẩu phải chứa ít nhất 1 chữ hoa.');

            return;
        }
        if (! preg_match('/[a-z]/', $value)) {
            $fail('Mật khẩu phải chứa ít nhất 1 chữ thường.');

            return;
        }
        if (! preg_match('/[0-9]/', $value)) {
            $fail('Mật khẩu phải chứa ít nhất 1 số.');

            return;
        }
        if (! preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt.');
        }
    }
}
