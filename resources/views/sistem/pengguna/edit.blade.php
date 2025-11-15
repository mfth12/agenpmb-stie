@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Edit Pengguna - {{ Str::of($pengguna->name)->explode(' ')->first() }}</h2>
          <div class="page-pretitle">Edit data pengguna</div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body my-2">
              <form action="{{ route('pengguna.update', $pengguna) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                      <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama', $pengguna->name) }}" required>
                      @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Asal Sekolah <span class="text-danger">*</span></label>
                      <input type="text" name="asal_sekolah"
                        class="form-control @error('asal_sekolah') is-invalid @enderror"
                        value="{{ old('asal_sekolah', $pengguna->asal_sekolah) }}" required>
                      @error('asal_sekolah')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Username <span class="text-danger">*</span></label>
                      <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $pengguna->username) }}" required>
                      @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Email <span class="text-danger">*</span></label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $pengguna->email) }}" required>
                      @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                      <input type="text" name="nomor_hp" class="form-control @error('nomor_hp') is-invalid @enderror"
                        value="{{ old('nomor_hp', $pengguna->nomor_hp) }}" required>
                      @error('nomor_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Nomor Whatsapp</label>
                      <input type="text" name="nomor_hp2" class="form-control @error('nomor_hp2') is-invalid @enderror"
                        value="{{ old('nomor_hp2', $pengguna->nomor_hp2) }}">
                      @error('nomor_hp2')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Foto Profil</label>

                  @if ($pengguna->has_custom_avatar)
                    <div class="mb-2">
                      <img src="{{ $pengguna->avatar_thumb_url }}" alt="Avatar" class="rounded"
                        style="max-width: 100px;">
                      <div class="mt-1">
                        <a href="#" class="text-danger small delete-avatar-btn"
                          data-user-id="{{ $pengguna->user_id }}">
                          <i class="ti ti-trash"></i> Hapus foto
                        </a>
                      </div>
                    </div>
                  @endif

                  <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror"
                    accept="image/jpeg,image/png,image/jpg,image/webp">
                  <small class="form-hint">Format: JPEG, PNG, JPG, WebP. Maksimal 2MB. Kosongkan jika tidak ingin
                    mengubah.</small>
                  @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- TAMBAHAN: Field Tentang --}}
                <div class="mb-3">
                  <label class="form-label">Tentang</label>
                  <textarea name="about" class="form-control @error('about') is-invalid @enderror" rows="3"
                    placeholder="Ceritakan sedikit tentang pengguna ini...">{{ old('about', $pengguna->about) }}</textarea>
                  @error('about')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Role <span class="text-danger">*</span></label>
                      <select name="role" class="form-select @error('role') is-invalid @enderror" required
                        {{ $pengguna->hasRole('superadmin') ? 'disabled' : '' }}>
                        <option value="">Pilih Role</option>
                        @foreach ($roles as $role)
                          <option value="{{ $role->name }}"
                            {{ old('role', $pengguna->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                          </option>
                        @endforeach
                      </select>
                      @if ($pengguna->hasRole('superadmin'))
                        <input type="hidden" name="role" value="superadmin">
                        <small class="text-muted">Role Superadmin tidak dapat diubah</small>
                      @endif
                      @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Status <span class="text-danger">*</span></label>
                      <select name="status" class="form-select">
                        <option value="active" {{ old('status', $pengguna->status) == 'active' ? 'selected' : '' }}>
                          Aktif</option>
                        <option value="inactive" {{ old('status', $pengguna->status) == 'inactive' ? 'selected' : '' }}>
                          Nonaktif</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Password</label>
                      <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Kosongkan jika tidak ingin mengubah">
                      @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Konfirmasi Password</label>
                      <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>
                  </div>
                </div>
            </div>

            <div class="card-footer">
              <div class="d-flex flex-column-reverse flex-md-row-reverse bd-highlight">
                <button type="submit" class="btn btn-primary ms-md-2 mt-2 mt-md-0">
                  <i class="ti ti-device-floppy fs-2 me-1"></i>
                  Perbarui
                </button>

                @can('user_edit')
                  @if (!$pengguna->hasRole('superadmin'))
                    {{-- PERUBAHAN: Ganti onclick dengan id untuk JavaScript --}}
                    <button type="button" class="btn btn-secondary ms-md-2 mt-2 mt-md-0" id="reset-password-btn">
                      <i class="ti ti-restore fs-2 me-1"></i>
                      Reset Password
                    </button>
                  @endif
                @endcan

                <a href="{{ route('pengguna.index') }}" class="btn btn-default ms-md-2">
                  <i class="ti ti-arrow-back-up fs-2 me-1"></i>
                  Kembali
                </a>
              </div>
            </div>
            </form>

            @can('user_edit')
              @if (!$pengguna->hasRole('superadmin'))
                <form id="reset-password-form" action="{{ route('pengguna.reset-password', $pengguna) }}" method="POST"
                  class="d-none">
                  @csrf
                </form>
              @endif
            @endcan

            <form id="delete-avatar-form" action="{{ route('pengguna.avatar.delete', $pengguna) }}" method="POST"
              class="d-none">
              @csrf
              @method('DELETE')
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js_bawah')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Delete avatar confirmation
      const deleteAvatarBtn = document.querySelector('.delete-avatar-btn');
      if (deleteAvatarBtn) {
        deleteAvatarBtn.addEventListener('click', function(e) {
          e.preventDefault();

          showConfirm({
            title: 'Hapus Foto Profil?',
            text: 'Foto akan dihapus secara permanen dan tidak dapat kembali!',
            icon: 'warning',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if (result.isConfirmed) {
              document.getElementById('delete-avatar-form').submit();
            }
          });
        });
      }

      // Reset password confirmation
      const resetPasswordBtn = document.getElementById('reset-password-btn');
      if (resetPasswordBtn) {
        resetPasswordBtn.addEventListener('click', function() {
          showConfirm({
            title: 'Reset Password?',
            html: `Password untuk username <strong>{{ $pengguna->username }}</strong>, akan direset menjadi default:
                      <code class="text-danger">{{ $pengguna->username }}</code><br><br>
                      <small class="text-muted">Pastikan untuk memberi tahu pengguna tentang password barunya.</small>`,
            icon: 'warning',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
          }).then((result) => {
            if (result.isConfirmed) {
              document.getElementById('reset-password-form').submit();
            }
          });
        });
      }
    });
  </script>
@endsection
