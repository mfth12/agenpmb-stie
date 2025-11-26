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
      <h3 class="text-center mb-4">Formulir Daftar</h3>
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
            <input type="text" name="nama" value="{{ old('nama') }}"
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
            <label class="form-label">Jenis Mitra <span class="text-danger">*</span></label>
            <select name="afiliasi" id="afiliasi-select" class="form-select @error('afiliasi') is-invalid @enderror"
              required>
              <option value="" selected>- Pilih Jenis -</option>
              @foreach ($afiliasis_root as $afiliasi)
                <option value="{{ $afiliasi->afiliasi_id }}">
                  {{ $afiliasi->nama }}
                </option>
              @endforeach
            </select>
            @error('afiliasi')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Dynamic Field Container -->
          <div id="dynamic-afiliasi-fields" class="d-none">
            <!-- Jenis Civitas -->
            <div id="jenis-civitas-container" class="mb-3 d-none">
              <label class="form-label">Jenis Civitas <span class="text-danger">*</span></label>
              <select name="afiliasi_child_civitas" id="jenis-civitas-select" class="form-select">
                <option value="">- Pilih Jenis Civitas -</option>
                <!-- Options will be populated by JS -->
              </select>
            </div>

            <!-- Instansi/Sekolah Asal (untuk Mitra) -->
            <div id="asal-sekolah-container" class="mb-3 d-none">
              <label class="form-label">Instansi/Sekolah Asal <span class="text-danger">*</span></label>
              <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}"
                class="form-control @error('asal_sekolah') is-invalid @enderror"
                placeholder="Masukkan nama instansi / sekolah asal" />
              @error('asal_sekolah')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <!-- End Dynamic Field Container -->

          <div class="mb-3">
            <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
            <input type="text" name="nomor_hp" value="{{ old('nomor_hp') }}"
              class="form-control @error('nomor_hp') is-invalid @enderror" placeholder="Masukkan nomor HP" required />
            @error('nomor_hp')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nomor Whatsapp</label>
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
          <input type="hidden" name="role" value="mitra" />
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

  <!-- Load Cloudflare Turnstile Script -->
  @if (env('USING_TURNSTILE', false))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  @endif


  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const widget = document.getElementById("cf-turnstile-widget");
      const img = document.getElementById("login-illustration");

      // Ambil data afiliasi dari server (anda perlu mengirimnya dari controller)
      // Contoh: let afiliasiData = !a!json($afiliasi_children); // Harus di-pass dari controller
      // Kita gunakan fetch untuk mendapatkan data child dari server secara dinamis
      const afiliasiSelect = document.getElementById('afiliasi-select');
      const dynamicFieldsContainer = document.getElementById('dynamic-afiliasi-fields');
      const jenisCivitasContainer = document.getElementById('jenis-civitas-container');
      const jenisCivitasSelect = document.getElementById('jenis-civitas-select');
      const asalSekolahContainer = document.getElementById('asal-sekolah-container');

      // Fetch child afiliasi berdasarkan parent_id
      async function fetchChildAfiliasi(parentId) {
        try {
          const response = await fetch(`/afiliasi/children/${parentId}`);
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          const data = await response.json();
          return data.afiliasis || []; // Asumsikan response memiliki struktur { afiliasis: [...] }
        } catch (error) {
          console.error('Error fetching child afiliasi:', error);
          return [];
        }
      }

      // Handler untuk perubahan select afiliasi
      afiliasiSelect.addEventListener('change', async function() {
        const selectedValue = this.value;
        dynamicFieldsContainer.classList.add('d-none'); // Sembunyikan dulu
        jenisCivitasContainer.classList.add('d-none'); // Sembunyikan
        asalSekolahContainer.classList.add('d-none'); // Sembunyikan

        if (selectedValue === '2') { // Jika Civitas dipilih
          dynamicFieldsContainer.classList.remove('d-none');
          jenisCivitasContainer.classList.remove('d-none');

          // Kosongkan option dulu
          jenisCivitasSelect.innerHTML = '<option value="">- Pilih Jenis Civitas -</option>';
          // Fetch dan populate options
          const children = await fetchChildAfiliasi(selectedValue);
          children.forEach(child => {
            const option = document.createElement('option');
            option.value = child.afiliasi_id;
            option.textContent = child.nama;
            jenisCivitasSelect.appendChild(option);
          });
        } else if (selectedValue === '3') { // Jika Mitra dipilih
          dynamicFieldsContainer.classList.remove('d-none');
          asalSekolahContainer.classList.remove('d-none');
        }
        // Jika Alumni (1) atau kosong, biarkan container disembunyikan
      });

      // Handler untuk submit form, pastikan nilai yang benar dikirim
      document.querySelector('form').addEventListener('submit', function(e) {
        // <-- Ambil dari select dengan id 'afiliasi-select' (yg sekarang namanya 'afiliasi_parent')
        const selectedAfiliasi = afiliasiSelect.value;
        const jenisCivitasValue = jenisCivitasSelect.value;
        const asalSekolahValue = document.querySelector('input[name="asal_sekolah"]').value;

        let finalAfiliasiId = selectedAfiliasi;

        // Jika afiliasi utama adalah Civitas dan jenis civitas dipilih
        if (selectedAfiliasi === '2' && jenisCivitasValue) {
          finalAfiliasiId = jenisCivitasValue;
        }
        // Jika afiliasi utama adalah Mitra, gunakan ID Mitra (3) dan isi asal_sekolah
        // Jika afiliasi utama adalah Alumni (1), gunakan ID Alumni (1)
        // Jika kosong, biarkan null

        // Tambahkan input hidden untuk afiliasi yang benar
        let afiliasiInput = document.querySelector('input[name="afiliasi"]'); // <-- Cari input hidden
        if (!afiliasiInput) { // <-- Jika input hidden belum ada
          afiliasiInput = document.createElement('input');
          afiliasiInput.type = 'hidden';
          afiliasiInput.name = 'afiliasi'; // <-- Nama input hidden TEPAT 'afiliasi'
          document.querySelector('form').appendChild(afiliasiInput); // <-- Tambahkan ke form
        }
        afiliasiInput.value = finalAfiliasiId; // <-- Set nilai ke ID YANG BENAR (e.g., '5') -> INI BENAR

        // Jika afiliasi bukan Mitra, hapus nilai asal_sekolah agar tidak disimpan
        // Pastikan 'selectedAfiliasi' merujuk ke ID asli yang dipilih di parent, bukan finalAfiliasiId
        // Gunakan nilai dari select sebelum di-overwrite oleh hidden input untuk pengecekan ini
        const originalSelectedParentId = document.getElementById('afiliasi-select')
          .value; // Ambil dari select asli
        if (originalSelectedParentId !== '3') { // Gunakan nilai asli dari select
          document.querySelector('input[name="asal_sekolah"]').value = '';
        }
      });

      function applyTheme() {
        let theme = localStorage.getItem("tabler-theme") || "light";
        console.log("Current theme:", theme);

        if (widget) {
          widget.setAttribute("data-theme", theme);
        }

        if (img) {
          if (theme === "dark") {
            img.src = "{{ asset('img/login-illustration-dark.png') }}";
          } else {
            img.src = "{{ asset('img/login-illustration.png') }}";
          }
        }
      }

      applyTheme();

      const observer = new MutationObserver(() => {
        applyTheme();
      });

      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
      });
    });
  </script>
@endsection
