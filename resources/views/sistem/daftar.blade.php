@extends('components.theme.front')

@section('container')
  <div class="page page-center">
    <div class="container container-tight py-4 my-4">
      <div class="text-center mb-2">
        <a href="javascript:void()" aria-label="{{ konfigs('NAMA_SISTEM_ALIAS') }}"
          class="navbar-brand navbar-brand-autodark d-flex align-items-center justify-content-center">
          <span class=" d-flex align-items-center">
            @include('components.back.macros', ['height' => 20, 'withbg' => 'fill: #fff;'])
          </span>
          <h1 class="mb-0">{{ konfigs('NAMA_SISTEM') }}</h1>
        </a>
      </div>
      <h3 class="text-center mb-4">Daftar Akun</h3>
      <form class="card card-md" method="POST" action="{{ route('register.do') }}" autocomplete="off" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="card-body">
          {{-- ALERTS --}}
          @if ($errors->any())
            <div
              class="alert alert-hilang alert-danger text-danger alert-dismissible d-flex align-items-center animate__animated animate__shakeX"
              role="alert">
              <div class="alert-icon">
                <i class="ti ti-ban fs-2 text-danger"></i>
              </div>
              @foreach ($errors->all() as $error)
                {{ $error }} <br>
              @endforeach
              <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
          @endif
          {{-- END OF ALERTS --}}

          <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama" value="{{ old('nama') }}" {{-- Ganti 'name' menjadi 'nama' --}}
              class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama lengkap" required />
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" value="{{ old('username') }}"
              class="form-control @error('username') is-invalid @enderror" placeholder="Masukkan username" required />
            @error('username')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}"
              class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email" required />
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Sekolah/Instansi Asal <span class="text-danger">*</span></label>
            <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}"
              class="form-control @error('asal_sekolah') is-invalid @enderror"
              placeholder="Masukkan nama sekolah atau instansi asal" required />
            @error('asal_sekolah')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nomor HP <span class="text-danger">*</span></label> {{-- Jadikan opsional jika di rules register --}}
            <input type="text" name="nomor_hp" value="{{ old('nomor_hp') }}"
              class="form-control @error('nomor_hp') is-invalid @enderror" placeholder="Masukkan nomor HP" />
            @error('nomor_hp')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nomor Whatsapp</label> {{-- Jadikan opsional --}}
            <input type="text" name="nomor_hp2" value="{{ old('nomor_hp2') }}"
              class="form-control @error('nomor_hp2') is-invalid @enderror" placeholder="Masukkan whatsapp aktif" />
            @error('nomor_hp2')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group input-group-flat">
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Password" autocomplete="off" required />
            </div>
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
            <div class="input-group input-group-flat">
              <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi Password"
                autocomplete="off" required />
            </div>
            @error('password_confirmation')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <input type="hidden" name="role" value="agen" />
          {{-- START: Turnstile Widget --}}
          @if (env('USING_TURNSTILE', false))
            <div class="mb-3" style="display: block; flex-flow: row;">
              <label class="form-label">Verifikasi Keamanan <span class="text-danger">*</span></label>
              <div id="cf-turnstile-widget" class="cf-turnstile" style="min-width: 100px;"
                data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}" data-size="flexible" data-refresh-expired="auto"
                data-callback="javascriptCallbackRegister" data-theme="light"
                data-language="{{ env('TURNSTILE_LANGUAGE', 'en-US') }}"></div>
            </div>
          @endif
          {{-- END: Turnstile Widget --}}
          <div class="mb-3">
            <label class="form-check">
              <input type="checkbox" name="syarat_dan_ketentuan" class="form-check-input" required />
              <span class="form-check-label">Saya setuju dengan <a href="#" tabindex="-1">
                  syarat dan ketentuan</a> yang berlaku.</span>
            </label>
            @error('syarat_dan_ketentuan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-footer">
            <span>
              <button type="submit" id="daftarButton" class="btn btn-primary w-100">Daftar Akun Mitra
                <div class="spinner-border spinner-border-sm ms-2 d-none" role="status"></div>
              </button>
            </span>
          </div>
        </div>
      </form>
      <div class="text-center text-secondary mt-3">
        Sudah punya akun? <a href="{{ route('login') }}" tabindex="-1">Masuk</a>
      </div>
    </div>
  </div>
@endsection

@section('style')
  {{-- kosong --}}
@endsection

@section('js_atas')
  {{-- kosong --}}
@endsection

@section('js_bawah')
  {{-- DEPENDENSI UNTUK PAGE MASUK --}}
  @vite(['resources/assets/vendor/libs/@form-validation/popular.js'])
  @vite(['resources/assets/vendor/libs/@form-validation/bootstrap5.js'])
  @vite(['resources/assets/vendor/libs/@form-validation/auto-focus.js'])
  {{-- TAMBAHAN JS UNTUK PAGE MASUK --}}
  @vite(['resources/js/pages/konfig-tampilan.js'])
  @vite(['resources/js/pages/daftar.js'])
  {{-- KOMPONEN INKLUD --}}
  @include('components.back.konfig-tampilan', ['floating' => true])
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const widget = document.getElementById("cf-turnstile-widget");
      const img = document.getElementById("login-illustration");

      function applyTheme() {
        let theme = localStorage.getItem("tabler-theme") || "light";
        console.log("Current theme:", theme);

        // Terapkan ke Turnstile (jika ada di halaman register)
        if (widget) {
          widget.setAttribute("data-theme", theme);
        }

        // Terapkan ke ilustrasi login (jika ada di halaman register)
        if (img) {
          if (theme === "dark") {
            img.src = "{{ asset('img/login-illustration-dark.png') }}";
          } else {
            img.src = "{{ asset('img/login-illustration.png') }}";
          }
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
