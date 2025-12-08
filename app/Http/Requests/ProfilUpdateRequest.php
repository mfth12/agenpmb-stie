<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfilUpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $userId = auth()->id();

    return [
      'nama' => 'required|string|max:255',
      'asal_sekolah' => 'required|string|max:255',
      'email' => [
        'required',
        'email',
        Rule::unique('users', 'email')->ignore($userId, 'user_id')
      ],
      'nomor_hp' => 'required|string|max:20',
      'nomor_hp2' => 'nullable|string|max:20',
      'about' => 'nullable|string|max:500',
      'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ];
  }

  public function messages(): array
  {
    return [
      'nama.required' => 'Nama lengkap wajib diisi.',
      'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
      'email.required' => 'Email wajib diisi.',
      'email.unique' => 'Email sudah digunakan.',
      'nomor_hp.required' => 'Nomor HP wajib diisi.',
      'avatar.image' => 'File harus berupa gambar.',
      'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
      'avatar.max' => 'Ukuran gambar maksimal 2MB.',
    ];
  }
}
