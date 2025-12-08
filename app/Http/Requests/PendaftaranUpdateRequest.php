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
      'nomor_hp' => 'required|string|max:20',
      'nomor_hp2' => 'nullable|string|max:20',
    ];
  }

  public function messages(): array
  {
    return [
      'prodi_id.required' => 'Program studi wajib dipilih.',
      'kelas.required' => 'Kelas wajib dipilih.',
      'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
      'email.required' => 'Email wajib diisi.',
      'email.unique' => 'Email sudah digunakan.',
      'nomor_hp.required' => 'Nomor HP wajib diisi.',
    ];
  }
}
