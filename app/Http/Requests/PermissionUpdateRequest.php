<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionUpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return auth()->user()->hasRole('superadmin');
  }

  public function rules(): array
  {
    $permissionId = $this->route('permission')->id;

    return [
      'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique('permissions', 'name')->ignore($permissionId), // Nama permission unik, kecuali untuk permission ini
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'Nama permission wajib diisi.',
      'name.unique' => 'Nama permission sudah digunakan.',
    ];
  }
}
