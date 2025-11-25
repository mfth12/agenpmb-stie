@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Buat Pendaftaran</h2>
          <div class="page-pretitle">Daftarkan calon mahasiswa baru ke PMB SIAKAD2</div>
        </div>
        <div class="col-auto">
          <a href="{{ route('pendaftaran.index') }}" class="btn btn-default">
            <i class="ti ti-arrow-back-up fs-2 me-1"></i>
            Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          @if (isset($error))
            <div class="alert alert-danger">
              <div class="d-flex">
                <div>
                  <i class="ti ti-alert-triangle fs-2 me-2 text-danger"></i>
                </div>
                <div>
                  <h4 class="alert-title text-danger-emphasis">Koneksi Gagal</h4>
                  <div class="text-muted">{{ $error }}</div>
                </div>
              </div>
            </div>
            <div class="text-center mt-4">
              <a href="{{ route('pendaftaran.index') }}" class="btn btn-primary">
                <i class="ti ti-brand-whatsapp fs-2 me-1"></i>
                Hubungi Tim PMB
              </a>
            </div>
          @elseif(!$jadwal)
            <div class="alert alert-warning">
              <div class="d-flex">
                <div>
                  <i class="ti ti-alert-triangle fs-2 text-warning me-2"></i>
                </div>
                <div>
                  <h4 class="alert-title text-warning-emphasis">Jadwal PMB Belum Tersedia</h4>
                  <div class="text-muted">Jadwal pendaftaran PMB belum tersedia. Silakan coba lagi nanti. Atau jika ini
                    sebuah kesalahan, segera hubungi Kami.</div>
                </div>
              </div>
            </div>
            <div class="text-center mt-4">
              <a href="{{ route('pendaftaran.index') }}" class="btn btn-primary">
                <i class="ti ti-brand-whatsapp fs-2 me-1"></i>
                Hubungi Tim PMB
              </a>
            </div>
          @else
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="ti ti-user-plus fs-2 me-2 text-blue"></i>
                  Form Pendaftaran Calon Mahasiswa
                </h3>
              </div>
              <div class="card-body">
                {{-- Info Jadwal --}}
                <div class="alert alert-info">
                  <div class="d-flex">
                    <div>
                      <i class="ti ti-info-circle fs-2 text-info me-2"></i>
                    </div>
                    <div>
                      <h4 class="alert-title text-info-emphasis mb-2">Jadwal Pendaftaran Aktif</h4>
                      <table class="table table-sm table-borderless text-muted mb-0">
                        <tr>
                          <td><strong>Gelombang</strong></td>
                          <td>: {{ $jadwal['GELOMBANG'] }}</td>
                        </tr>
                        <tr>
                          <td><strong>Tahun Akademik</strong></td>
                          <td>: {{ $jadwal['TAHUN'] }}/{{ $jadwal['TAHUN'] + 1 }}</td>
                        </tr>
                        <tr>
                          <td><strong>Periode Daftar</strong></td>
                          <td>:
                            {{ \Carbon\Carbon::parse($jadwal['TANGGALMULAI'])->translatedFormat('d M Y') }} -
                            {{ \Carbon\Carbon::parse($jadwal['TANGGALSELESAI'])->translatedFormat('d M Y') }}
                          </td>
                        </tr>
                        <tr>
                          <td><strong>Biaya</strong></td>
                          <td>:
                            <strong>Rp {{ number_format($jadwal['BIAYA'], 0, ',', '.') }}</strong>
                            <span class="text-success">(Gratis Formulir Rp 100.000)</span>
                          </td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>

                <form action="{{ route('pendaftaran.store') }}" method="POST" id="formPendaftaran">
                  @csrf

                  <div class="row">
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label required">Program Studi</label>
                        <select name="prodi_id" class="form-select @error('prodi_id') is-invalid @enderror" required>
                          <option value="">Pilih Program Studi</option>
                          @foreach ($prodi as $id => $nama)
                            @if (!in_array($id, \App\Models\PendaftaranModel::daftarProdiWithNonaktif()))
                              <option value="{{ $id }}" {{ old('prodi_id') == $id ? 'selected' : '' }}>
                                S1 - {{ $nama }}
                              </option>
                            @endif
                          @endforeach
                        </select>
                        @error('prodi_id')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label required">Kelas</label>
                        <select name="kelas" class="form-select @error('kelas') is-invalid @enderror" required>
                          <option value="">Pilih Kelas</option>
                          @foreach ($kelasList as $id => $nama)
                            <option value="{{ $id }}" {{ old('kelas') == $id ? 'selected' : '' }}>
                              {{ $nama }}
                            </option>
                          @endforeach
                        </select>
                        @error('kelas')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      <div class="mb-3">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap"
                          class="form-control @error('nama_lengkap') is-invalid @enderror"
                          value="{{ old('nama_lengkap') }}" required placeholder="Masukkan nama lengkap calon mahasiswa">
                        @error('nama_lengkap')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                          value="{{ old('email') }}" required placeholder="email@example.com">
                        @error('email')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label required">Nomor HP</label>
                        <input type="text" name="nomor_hp" class="form-control @error('nomor_hp') is-invalid @enderror"
                          value="{{ old('nomor_hp') }}" required placeholder="081234567890">
                        @error('nomor_hp')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label">Nomor HP (WhatsApp)</label>
                        <input type="text" name="nomor_hp2"
                          class="form-control @error('nomor_hp2') is-invalid @enderror" value="{{ old('nomor_hp2') }}"
                          placeholder="081234567891">
                        @error('nomor_hp2')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label required">Password</label>
                        <input type="password" name="password"
                          class="form-control @error('password') is-invalid @enderror" required
                          placeholder="Minimal 8 karakter">
                        @error('password')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label required">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required
                          placeholder="Ulangi password">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label">Total Biaya Pendaftaran</label>
                        <div class="form-control-plaintext">
                          <span class="text-success fw-bold mt-0">Rp
                            {{ number_format($jadwal['BIAYA'] - 100000, 0, ',', '.') }}
                          </span>
                          <span class="text-default mt-0">(Terpotong diskon formulir Rp 100.000)
                          </span>
                          <small class="text-muted d-block">Biaya akan dikirim ke PMB SIAKAD2</small>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="alert alert-warning mt-0">
                    <div class="d-flex">
                      <div>
                        <i class="ti ti-alert-triangle fs-2 text-warning me-2"></i>
                      </div>
                      <div>
                        <h4 class="alert-title text-warning-emphasis">Perhatian</h4>
                        <div class="text-muted">
                          Data akan dikirim langsung ke sistem PMB SIAKAD2.
                          Pastikan semua data sudah benar sebelum lanjut proses daftar.
                        </div>
                      </div>
                    </div>
                  </div>
              </div>

              <div class="card-footer">
                <div class="d-flex justify-content-between">
                  <a href="{{ route('pendaftaran.index') }}" class="btn btn-default">
                    <i class="ti ti-arrow-back-up fs-2 me-1"></i>
                    Kembali
                  </a>
                  <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <i class="ti ti-send fs-2 me-1"></i>
                    Daftarkan Sekarang
                  </button>
                </div>
              </div>
              </form>
            </div>
        </div>
        @endif
      </div>
    </div>
  </div>
  </div>
@endsection

@section('js_bawah')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('formPendaftaran');
      const btnSubmit = document.getElementById('btnSubmit');

      if (form) {
        form.addEventListener('submit', function(e) {
          btnSubmit.disabled = true;
          btnSubmit.innerHTML = '<i class="ti ti-loader-2 fs-2 spinner me-1"></i> Memproses...';
        });
      }
    });
  </script>

  <style>
    .spinner {
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
    }

    .form-label.required::after {
      content: " *";
      color: #d63939;
    }
  </style>
@endsection
