@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Edit Profil</h2>
          <div class="page-pretitle">Perbarui informasi profil Anda</div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body my-2">
              <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                      <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama', $user->name) }}" required>
                      @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Asal Instansi/Sekolah <span class="text-danger">*</span></label>
                      <input type="text" name="asal_sekolah"
                        class="form-control @error('asal_sekolah') is-invalid @enderror"
                        value="{{ old('asal_sekolah', $user->asal_sekolah) }}" required>
                      @error('asal_sekolah')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Email <span class="text-danger">*</span></label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}" required>
                      @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Username</label>
                      <input type="text" class="form-control" value="{{ $user->username }}" disabled>
                      <small class="form-hint">Username tidak dapat diubah</small>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                      <input type="text" name="nomor_hp" class="form-control @error('nomor_hp') is-invalid @enderror"
                        value="{{ old('nomor_hp', $user->nomor_hp) }}" required>
                      @error('nomor_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Nomor Whatsapp</label>
                      <input type="text" name="nomor_hp2" class="form-control @error('nomor_hp2') is-invalid @enderror"
                        value="{{ old('nomor_hp2', $user->nomor_hp2) }}">
                      @error('nomor_hp2')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Foto Profil</label>

                  @if ($user->has_custom_avatar)
                    <div class="mb-2">
                      <img src="{{ $user->avatar_thumb_url }}" alt="Avatar" class="rounded" style="max-width: 100px;">
                      <div class="mt-1">
                        <a href="#" class="text-danger small delete-avatar-btn">
                          <i class="ti ti-trash"></i> Hapus foto
                        </a>
                      </div>
                    </div>
                  @endif

                  <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror"
                    accept="image/jpeg,image/png,image/jpg,image/webp">
                  <small class="form-hint">Format: JPEG, PNG, JPG, WebP. Maksimal 2MB.</small>
                  @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Tentang Saya</label>
                  <textarea name="about" class="form-control @error('about') is-invalid @enderror" rows="3"
                    placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('about', $user->about) }}</textarea>
                  @error('about')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
            </div>

            <div class="card-footer">
              <div class="d-flex flex-column-reverse flex-md-row-reverse bd-highlight">
                <button type="submit" class="btn btn-primary ms-md-2 mt-2 mt-md-0">
                  <i class="ti ti-device-floppy fs-2 me-1"></i>
                  Simpan
                </button>
                <a href="{{ route('profil.show') }}" class="btn btn-default ms-md-2">
                  <i class="ti ti-arrow-back-up fs-2 me-1"></i>
                  Kembali
                </a>
              </div>
            </div>
            </form>

            <form id="delete-avatar-form" action="{{ route('profil.avatar.delete') }}" method="POST" class="d-none">
              @csrf
              @method('DELETE')
            </form>
          </div>
        </div>

        <div class="col-md-4 mt-4 mt-md-0">
          <div class="card">
            <div class="card-body my-2">
              <form action="{{ route('profil.update-password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                  <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                  <input type="password" name="password_lama"
                    class="form-control @error('password_lama') is-invalid @enderror" required>
                  @error('password_lama')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                  <input type="password" name="password_baru"
                    class="form-control @error('password_baru') is-invalid @enderror" required>
                  @error('password_baru')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                  <input type="password" name="password_baru_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-secondary w-100">
                <i class="ti ti-lock fs-2 me-1"></i>
                Ubah Password
              </button>
            </div>
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
    });
  </script>
@endsection
