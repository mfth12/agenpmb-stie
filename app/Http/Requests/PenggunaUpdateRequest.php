<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenggunaUpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $userId = $this->route('pengguna')->user_id;

    return [
      'nama' => 'required|string|max:255',
      'asal_sekolah' => 'nullable|string|max:255',
      'email' => [
        'required',
        'email',
        Rule::unique('users', 'email')->ignore($userId, 'user_id')
      ],
      'username' => [
        'required',
        'string',
        'max:100',
        'regex:/^(?!.*\.\.)(?!\.)(?!.*\.$)[a-zA-Z0-9._]+$/', // Tidak boleh ada spasi
        Rule::unique('users', 'username')->ignore($userId, 'user_id')
      ],
      'nomor_hp' => [
        'required',
        'string',
        'max:20',
        'regex:/^[0-9]+$/',
        Rule::unique('users', 'nomor_hp')->ignore($userId, 'user_id')
      ],
      'nomor_hp2' => [
        'required',
        'string',
        'max:20',
        'regex:/^[0-9]+$/',
        Rule::unique('users', 'nomor_hp2')->ignore($userId, 'user_id')
      ],
      'role' => 'required|string|exists:roles,name',
      'password' => 'sometimes|nullable|min:6|confirmed',
      'status' => 'required|string|in:active,inactive,pending',
      'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'about' => 'nullable|string|max:500', // TAMBAHAN: Field about
    ];
  }

  public function messages(): array
  {
    return [
      'nama.required' => 'Nama lengkap wajib diisi.',
      'email.required' => 'Email wajib diisi.',
      'email.unique' => 'Email sudah digunakan.',
      'username.required' => 'Username wajib diisi.',
      'username.unique' => 'Username sudah digunakan.',
      'username.regex' => 'Hanya huruf, angka, titik, dan underscore, tanpa spasi dan titik diawal/akhir.',
      'nomor_hp.required' => 'Nomor HP wajib diisi.',
      'nomor_hp2.required' => 'Nomor Whatsapp wajib diisi.',
      'nomor_hp.unique' => 'Nomor HP sudah digunakan.',
      'nomor_hp2.unique' => 'Nomor Whatsapp sudah digunakan.',
      'nomor_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
      'nomor_hp2.regex' => 'Nomor Whatsapp hanya boleh berisi angka.',
      'role.required' => 'Role wajib dipilih.',
      'password.min' => 'Password minimal 6 karakter.',
      'password.confirmed' => 'Konfirmasi password tidak sesuai.',
      'status.required' => 'Status wajib dipilih.',
      'status.in' => 'Status tidak valid.',
      'avatar.image' => 'File harus berupa gambar.',
      'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
      'avatar.max' => 'Ukuran gambar maksimal 2MB.',
    ];
  }
}
