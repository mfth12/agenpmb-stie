@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Tambah Permission Baru</h2>
          <div class="page-pretitle">Buat permission akses dasar</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('role_permission.index_permission') }}" class="btn btn-default">
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
          <form action="{{ route('role_permission.store_permission') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label required">Nama Permission</label>
              <input type="text" name="name" value="{{ old('name') }}"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="Misal: view-posts, edit-users, delete-comments" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Simpan Permission</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
