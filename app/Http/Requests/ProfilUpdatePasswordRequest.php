<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ProfilUpdatePasswordRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'password_lama' => ['required', 'current_password'],
      'password_baru' => 'required|string|min:6|confirmed',
    ];
  }

  public function messages(): array
  {
    return [
      'password_lama.required' => 'Password lama wajib diisi.',
      'password_lama.current_password' => 'Password lama tidak sesuai.',
      'password_baru.required' => 'Password baru wajib diisi.',
      'password_baru.min' => 'Password baru minimal 6 karakter.',
      'password_baru.confirmed' => 'Konfirmasi password baru tidak sesuai.',
    ];
  }
}
