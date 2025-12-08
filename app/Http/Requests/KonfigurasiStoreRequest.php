<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KonfigurasiStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    // Sesuaikan dengan kebijakan otorisasi Anda
    // Misalnya, hanya superadmin
    return auth()->user()->hasRole('superadmin');
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'config_group' => 'required|string|max:255',
      'config_key' => 'required|string|max:255|unique:konfigurasis,config_key', // Pastikan key unik
      'config_value' => 'nullable|string',
      'value_1' => 'nullable|string|max:255',
      'value_2' => 'nullable|string|max:255',
      'value_3' => 'nullable|string|max:255',
      'value_4' => 'nullable|string|max:255',
      'value_5' => 'nullable|string|max:255',
    ];
  }

  public function messages(): array
  {
    return [
      'config_group.required' => 'Grup konfigurasi wajib diisi.',
      'config_key.required' => 'Kunci konfigurasi wajib diisi.',
      'config_key.unique' => 'Kunci konfigurasi sudah digunakan.',
    ];
  }
}
