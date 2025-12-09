@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Tambah Baru</h2>
          <div class="page-pretitle">Tambah pengguna baru ke {{ konfigs('NAMA_SISTEM') }}</div>
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
              <form action="{{ route('pengguna.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Nama Lengkap</label>
                      <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama') }}" required>
                      @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Asal Instansi/Sekolah</label>
                      <input type="text" name="asal_sekolah"
                        class="form-control @error('asal_sekolah') is-invalid @enderror" value="{{ old('asal_sekolah') }}"
                        required>
                      @error('asal_sekolah')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Username</label>
                      <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username') }}" required>
                      @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Email</label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required>
                      @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Nomor HP</label>
                      <input type="text" name="nomor_hp" class="form-control @error('nomor_hp') is-invalid @enderror"
                        value="{{ old('nomor_hp') }}" required>
                      @error('nomor_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Nomor Whatsapp</label>
                      <input type="text" name="nomor_hp2" class="form-control @error('nomor_hp2') is-invalid @enderror"
                        value="{{ old('nomor_hp2') }}">
                      @error('nomor_hp2')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Foto Profil</label>
                  <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror"
                    accept="image/jpeg,image/png,image/jpg,image/webp">
                  <small class="form-hint">Format: JPEG, PNG, JPG, WebP. Maksimal 2MB. Opsional.</small>
                  @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Role</label>
                      <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">- Pilih Role -</option>
                        @foreach ($roles as $role)
                          <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                          </option>
                        @endforeach
                      </select>
                      @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Status</label>
                      <select name="status" class="form-select">
                        <option value="" selected>- Pilih Status-</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="pending" {{ old('status') }} disabled>Pending
                        </option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Password</label>
                      <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        required>
                      @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Konfirmasi Password</label>
                      <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                  </div>
                </div>

                {{-- START: Turnstile Widget --}}
                <div class="row">
                  <div class="col-md-12">
                    @if (env('USING_TURNSTILE', false))
                      <div class="mb-3" style="display: block; flex-flow: row;">
                        <label class="form-label required">Verifikasi Keamanan</label>
                        @if ($errors->has('cf-turnstile-response'))
                          <div
                            class="alert alert-danger text-danger alert-dismissible d-flex align-items-center animate__animated animate__shakeX"
                            role="alert">
                            <div class="alert-icon">
                              <i class="ti ti-cloud-x fs-2 text-danger"></i>
                            </div>
                            {!! $errors->first('cf-turnstile-response') !!}
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                          </div>
                        @endif
                        <div id="cf-turnstile-widget" class="cf-turnstile" style="min-width: 100px;"
                          data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}" data-size="flexible"
                          data-refresh-expired="auto" data-callback="javascriptCallbackRegister" data-theme="light"
                          data-language="{{ env('TURNSTILE_LANGUAGE', 'en-US') }}"></div>
                      </div>
                    @endif
                  </div>
                </div>
                {{-- END: Turnstile Widget --}}
            </div>

            <div class="card-footer">
              <div class="d-flex flex-column-reverse flex-md-row-reverse bd-highlight">
                <button type="submit" class="btn btn-primary ms-md-2 mt-2 mt-md-0">
                  <i class="ti ti-device-floppy fs-2 me-1"></i>
                  Simpan
                </button>
                <a href="{{ route('pengguna.index') }}" class="btn btn-default ms-md-2">
                  Batal
                </a>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js_bawah')
  {{-- KOMPONEN INKLUD --}}
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const widget = document.getElementById("cf-turnstile-widget");

      function applyTheme() {
        let theme = localStorage.getItem("tabler-theme") || "light";
        console.log("Current theme:", theme);

        // Terapkan ke Turnstile (jika ada di halaman register)
        if (widget) {
          widget.setAttribute("data-theme", theme);
        }
      }

      // Jalankan pertama kali
      applyTheme();

      // Pantau perubahan tema secara dinamis (misal dari switcher Tabler)
      const observer = new MutationObserver(() => {
        applyTheme();
      });

      // Amati perubahan attribute data-bs-theme di <html> (Tabler ganti tema di sana)
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
      });
    });
  </script>
@endsection
