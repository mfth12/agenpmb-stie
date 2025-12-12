<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PendaftaranUpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $pendaftaranId = $this->route('pendaftaran')->pendaftaran_id;

    return [
      'prodi_id' => 'required|string|max:10',
      'kelas' => 'required|string|in:0,1,2,3,5',
      'nama_lengkap' => 'required|string|max:255',
      'email' => [
        'required',
        'email',
        Rule::unique('pendaftaran', 'email')->ignore($pendaftaranId, 'pendaftaran_id')
      ],
      // 'nomor_hp' => 'required|string|max:20',
      // 'nomor_hp2' => 'required|string|max:20',
      'nomor_hp' => [
        'required',
        'string',
        'max:20',
        'regex:/^[0-9]+$/',
        Rule::unique('users', 'nomor_hp')->ignore($pendaftaranId, 'user_id')
      ],
      'nomor_hp2' => [
        'required',
        'string',
        'max:20',
        'regex:/^[0-9]+$/',
        Rule::unique('users', 'nomor_hp2')->ignore($pendaftaranId, 'user_id')
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'prodi_id.required' => 'Program studi wajib dipilih.',
      'kelas.required' => 'Kelas wajib dipilih.',
      'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
      'email.required' => 'Email wajib diisi.',
      'nomor_hp.required' => 'Nomor HP wajib diisi.',
      'nomor_hp2.required' => 'Nomor Whatsapp wajib diisi.',
      'nomor_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
      'nomor_hp2.regex' => 'Nomor Whatsapp hanya boleh berisi angka.',
    ];
  }
}
