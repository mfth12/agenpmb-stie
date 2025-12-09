<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Tambahkan ini untuk aturan exists

class PenggunaStoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $isRegister = $this->routeIs('register.do');

    $rules = [
      'nama' => 'required|string|max:255',
      'asal_sekolah' => 'nullable|string|max:255', // Jadikan opsional secara default
      'afiliasi' => 'nullable|exists:user_afiliasis,afiliasi_id', // Validasi afiliasi
      'email' => 'required|email|unique:users,email',
      'username' => 'required|string|unique:users,username|max:100',
      'nomor_hp' => 'required|string|unique:users,nomor_hp|max:20', // Jadikan wajib
      'nomor_hp2' => 'required|string|unique:users,nomor_hp2|max:20',
      'password' => 'required|string|min:6',
      'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'about' => 'nullable|string|max:500', // TAMBAHAN: Field about
      'cf-turnstile-response' => 'required', // TAMBAHAN: Field about
    ];

    if ($isRegister) {
      // Aturan khusus untuk register
      $rules['password'] .= '|confirmed';
      // Untuk register, asal_sekolah bisa wajib jika afiliasi bukan Mitra atau Alumni
      // Kita handle validasi ini secara logis di controller atau di level form
      // Misalnya, jika afiliasi = 3 (Mitra), maka asal_sekolah wajib
      // Jika afiliasi = 1 (Alumni), asal_sekolah bisa opsional atau wajib
      // Jika afiliasi = null atau jenis lain, aturan bisa berbeda
      // Kita bisa tambahkan aturan kondisional di sini atau di controller
      // Contoh aturan kondisional (meski lebih rumit):
      // $rules['asal_sekolah'][] = Rule::requiredIf(fn() => $this->afiliasi == 3); // Wajib jika Mitra
      // Kita sederhanakan, asal_sekolah opsional, dan validasi spesifik di controller

      // $rules['status'] = 'required|string|in:active,inactive';
    } else {
      // Aturan untuk admin (PenggunaController)
      $rules['asal_sekolah'] = 'required|string|max:255'; // Jadikan wajib untuk admin
      $rules['role'] = [
        'required',
        'string',
        Rule::exists('roles', 'name')->whereNot('name', 'superadmin')
      ];
      $rules['password'] .= '|confirmed';
      $rules['status'] = 'required|string|in:active,inactive,pending';
    }

    return $rules;
  }

  public function messages(): array
  {
    $messages = [
      'nama.required' => 'Nama lengkap wajib diisi.',
      // 'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
      'afiliasi.exists' => 'Afiliasi yang dipilih tidak valid.',
      'email.required' => 'Email wajib diisi.',
      'email.unique' => 'Email sudah digunakan.',
      'username.required' => 'Username wajib diisi.',
      'username.unique' => 'Username sudah digunakan.',
      'nomor_hp.required' => 'Nomor HP wajib diisi.',
      'nomor_hp2.required' => 'Nomor Whatsapp wajib diisi.',
      'nomor_hp.unique' => 'Nomor HP sudah digunakan.',
      'nomor_hp2.unique' => 'Nomor Whatsapp sudah digunakan.',
      'password.required' => 'Password wajib diisi.',
      'password.min' => 'Password minimal 6 karakter.',
      'password.confirmed' => 'Konfirmasi password tidak sesuai.',
      'avatar.image' => 'File harus berupa gambar.',
      'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
      'avatar.max' => 'Ukuran gambar maksimal 2MB.',
      'about.max' => 'Bio maksimal :max karakter.',
      'role.required' => 'Role wajib dipilih.',
      'role.exists' => 'Role yang dipilih tidak valid.',
      'status.required' => 'Status wajib dipilih.',
      'status.in' => 'Status yang dipilih tidak valid.',
      'cf-turnstile-response.required' => 'Verifikasi keamanan gagal.',
    ];

    return $messages;
  }
}
