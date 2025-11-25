@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Edit Role</h2>
          <div class="page-pretitle">Ubah role: {{ $role->name }}</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('role_permission.index_role') }}" class="btn btn-default">
            <i class="ti ti-arrow-back-up fs-2 me-1"></i>
            Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="card">
        <div class="card-body">
          <form action="{{ route('role_permission.update_role', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label class="form-label required">Nama Role</label>
              <input type="text" name="name" value="{{ old('name', $role->name) }}"
                class="form-control @error('name') is-invalid @enderror" placeholder="Misal: admin, editor, viewer"
                required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Permission</label>
              <div class="row">
                @foreach ($permissions as $permission)
                  <div class="col-md-3 col-sm-6">
                    <label class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                      <span class="form-check-label">{{ $permission->name }}</span>
                    </label>
                  </div>
                @endforeach
              </div>
              @error('permissions')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Perbarui Role</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
