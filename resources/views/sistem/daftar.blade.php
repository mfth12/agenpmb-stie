@extends('components.theme.front')

@section('container')
  <div class="page page-center">
    <div class="container container-normal py-4">
      <div class="row align-items-center g-4">
        {{-- BAGIAN FORM --}}
        <div class="col-lg">
          <div class="container-tight py-6">
            <div class="text-center mb-2">
              <a href="javascript:void()" aria-label="{{ konfigs('NAMA_SISTEM_ALIAS') }}"
                class="navbar-brand navbar-brand-autodark d-flex align-items-center justify-content-center">
                <span class=" d-flex align-items-center">
                  @include('components.back.macros', ['height' => 20, 'withbg' => 'fill: #fff;'])
                </span>
                <h1 class="mb-0">{{ konfigs('NAMA_SISTEM') }}</h1>
              </a>
            </div>
            <h3 class="text-center mb-4">Portal Mitra Penerimaan Mahasiswa Baru STIE</h3>
            <div class="card card-md">
              <div class="card-body">
                {{-- ALERTS --}}
                @if ($errors->has('masuk'))
                  <div
                    class="alert alert-hilang alert-danger text-danger alert-dismissible d-flex align-items-center animate__animated animate__shakeX"
                    role="alert">
                    <div class="alert-icon">
                      <i class="ti ti-ban fs-2 text-danger"></i>
                    </div>
                    {!! $errors->first('masuk') !!}
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                  </div>
                @elseif ($errors->has('koneksi'))
                  <div
                    class="alert alert-hilang alert-danger text-danger alert-dismissible d-flex align-items-center animate__animated animate__shakeX"
                    role="alert">
                    <div class="alert-icon">
                      <i class="ti ti-plug-connected-x fs-2 text-danger"></i>
                    </div>
                    {!! $errors->first('koneksi') !!}
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                  </div>
                @elseif ($errors->has('turnstile_notvalid'))
                  <div
                    class="alert alert-hilang alert-danger text-danger alert-dismissible d-flex align-items-center animate__animated animate__shakeX"
                    role="alert">
                    <div class="alert-icon">
                      <i class="ti ti-cloud-x fs-2 text-danger"></i>
                    </div>
                    {!! $errors->first('turnstile_notvalid') !!}
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                  </div>
                @elseif (session()->has('no_session'))
                  <div
                    class="alert alert-hilang alert-danger text-danger alert-dismissible d-flex align-items-center animate__animated animate__shakeX"
                    role="alert">
                    <div class="alert-icon">
                      <i class="ti ti-ban fs-2 text-danger"></i>
                    </div>
                    {!! session('no_session') !!}
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                  </div>
                @elseif (session()->has('keluar'))
                  <div
                    class="alert alert-hilang alert-secondary text-secondary alert-dismissible d-flex align-items-center"
                    role="alert">
                    <div class="alert-icon">
                      <i class="ti ti-lock fs-2 text-secondary"></i>
                    </div>
                    {!! session('keluar') !!}
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                  </div>
                @else
                  <div class="alert alert-hilang alert-info text-info d-flex align-items-center" role="alert">
                    <i class="ti ti-fingerprint fs-2 text-info"></i>
                    Gunakan Akun Mitra atau Siakad Anda.
                  </div>
                @endif
                {{-- END OF ALERTS --}}

                {{-- LOGIN FORM --}}
                {!! html()->form('post')->route('login.do')->attributes(['name' => 'formAuthentication', 'id' => 'formAuthentication', 'class' => 'mb-0 mt-0'])->open() !!}
                <div class="mb-2">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="form-label mb-0">Username</label>
                    <div class="form-check form-switch mb-0">
                      <input type="hidden" name="via_siakad" value="0">
                      <input class="form-check-input" type="checkbox" role="switch" id="switchCheckDefault"
                        name="via_siakad" value="1" {{ old('via_siakad') ? 'checked' : '' }}>
                      <label class="form-check-label ms-1" for="switchCheckDefault">
                        <span id="switchLabel">{{ old('via_siakad') ? 'Siakad' : 'Normal' }}</span>
                      </label>
                    </div>
                  </div>
                  {!! html()->text('username')->class('form-control' . ($errors->has('username') ? ' is-invalid' : ''))->placeholder('Username')->attributes(['aria-describedby' => 'username'])->value(old('username')) !!}
                </div>
                <div class="mb-2">
                  <label class="form-label">
                    Password
                    <span class="form-label-description"><a href="/lupa-password" class="text-muted">
                        Lupa password?</a>
                    </span>
                  </label>
                  {!! html()->password('password')->class('form-control' . ($errors->has('password') ? ' is-invalid' : ''))->placeholder('&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;')->id('password')->attributes(['aria-describedby' => 'toggle-password', 'autocomplete' => 'off']) !!}
                </div>
                {{-- Clouflare turnstile script --}}
                @if (env('USING_TURNSTILE', false))
                  <div class="mt-4" style="display: block; flex-flow: row;">
                    <div id="cf-turnstile-widget" class="cf-turnstile" style="min-width: 100px;"
                      data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}" data-size="flexible" data-refresh-expired="auto"
                      data-callback="javascriptCallback" data-theme="light"
                      data-language="{{ env('TURNSTILE_LANGUAGE', 'en-US') }}">
                    </div>
                  </div>
                @endif

                {{-- Submit button --}}
                <div class="form-footer">
                  {!! html()->button(
                          '<span><span class="button-text">Masuk</span><div class="spinner-border spinner-border-sm ms-2 d-none " role="status"></div></span>',
                          'submit',
                      )->class('btn btn-primary d-grid w-100')->id('loginButton') !!}
                </div>

                {!! html()->form()->close() !!}
                {{-- END OF LOGIN FORM --}}
              </div>
            </div>
            <div class="text-center text-secondary mt-3">
              Copyright © {{ now()->year }}
              <a href="https://www.stie-pembangunan.ac.id/" target="_blank" data-bs-toggle="tooltip"
                data-bs-placement="top" title="Kunjungi situs">STIE Pembangunan Tanjungpinang</a>.
              {{ env('APP_VERSION') }}
            </div>
          </div>
        </div>
        {{-- BAGIAN ILUSTRASI --}}
        <div class="col-lg d-none d-lg-block">
          <img id="login-illustration" src="{{ asset('img/login-illustration.png') }}" alt="Login Illustration">
        </div>
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
  @vite(['resources/js/pages/masuk.js'])
  {{-- KOMPONEN INKLUD --}}
  @include('components.back.konfig-tampilan', ['floating' => true])
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const widget = document.getElementById("cf-turnstile-widget");
      const img = document.getElementById("login-illustration");

      function applyTheme() {
        let theme = localStorage.getItem("tabler-theme") || "light";
        console.log("Current theme:", theme);

        // Terapkan ke Turnstile
        if (widget) {
          widget.setAttribute("data-theme", theme);
        }

        // Terapkan ke ilustrasi login
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
