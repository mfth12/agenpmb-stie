@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Detail Pendaftar - {{ Str::ucfirst(Str::lower(Str::of($pendaftaran->nama_lengkap)->explode(' ')->first())) }}
          </h2>
          <div class="page-pretitle">Informasi lengkap calon mahasiswa STIE</div>
        </div>
        <div class="col-auto">
          <a href="{{ route('pendaftaran.index') }}" class="btn btn-primary">
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
        {{-- KOLOM KIRI --}}
        <div class="col-md-8 mb-4">
          {{-- CARD: Keterangan --}}
          @if ($pendaftaran->keterangan)
            <div class="card mb-4 {{ $pendaftaran->status === 'failed' ? 'bg-danger-lt' : 'bg-info-lt' }}">
              <div class="card-body">
                <h3 class="card-title">
                  <i
                    class="ti ti-{{ $pendaftaran->status === 'failed' ? 'alert-triangle' : 'info-circle' }} fs-2 me-1"></i>
                  <span
                    class="{{ $pendaftaran->status === 'failed' ? 'text-danger-emphasis' : 'text-info-emphasis' }}">Keterangan</span>
                </h3>
                <h4 class="mb-2">

                </h4>
                <p class="text-secondary">
                  {{ $pendaftaran->keterangan }}
                </p>

              </div>
            </div>
          @endif
          
          {{-- CARD: Informasi Calon Mahasiswa --}}
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="ti ti-user fs-2 me-2 text-primary"></i>
                Informasi
              </h3>
              <div class="card-actions">
                {!! $pendaftaran->status_badge !!}
              </div>
            </div>
            <div class="card-body my-2">
              <div class="row">
                <div class="col-md-6">
                  <strong>Nama Lengkap:</strong><br>
                  {{ $pendaftaran->nama_lengkap }}
                </div>
                <div class="col-md-6">
                  <strong>Email:</strong><br>
                  {{ $pendaftaran->email }}
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mt-3">
                  <strong>Nomor HP:</strong><br>
                  {{ $pendaftaran->nomor_hp }}
                </div>
                <div class="col-md-6 mt-3">
                  <strong>Program Studi:</strong><br>
                  {{ $pendaftaran->prodi_nama }}
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mt-3">
                  <strong>Kelas:</strong><br>
                  {{ $pendaftaran->nama_kelas }}
                </div>
                <div class="col-md-6 mt-3">
                  <strong>Tahun Akademik:</strong><br>
                  {{ $pendaftaran->tahun }}/{{ $pendaftaran->tahun + 1 }} - Gel. {{ $pendaftaran->gelombang }}
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 mt-3">
                  <strong>Biaya Pendaftaran:</strong><br>
                  <span class="fw-bold text-success">{{ $pendaftaran->biaya_formatted }}</span>
                </div>
              </div>
            </div>
          </div>

          

          {{-- CARD: Kredensial PMB SIAKAD2 --}}
          @if ($pendaftaran->id_calon_mahasiswa)
            <div class="card mt-4">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="ti ti-check text-success fs-2 me-2"></i>
                  Kredensial PMB SIAKAD2
                </h3>
              </div>
              {{-- <div class="card-stamp">
                <div class="card-stamp-icon bg-success">
                  <i class="ti ti-face-id"></i>
                </div>
              </div> --}}
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Username</label>
                  <div class="input-group">
                    <input type="text" class="form-control font-monospace" value="{{ $pendaftaran->username_siakad }}"
                      readonly>
                    <button class="btn btn-default" type="button"
                      onclick="copyToClipboard('{{ $pendaftaran->username_siakad }}', 'Username')">
                      <i class="ti ti-copy fs-2"></i>
                    </button>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control font-monospace" id="passwordField"
                      value="{{ $pendaftaran->password_text }}" readonly>
                    <button class="btn btn-default" type="button" onclick="togglePassword()">
                      <i class="ti ti-eye fs-2" id="passwordIcon"></i>
                    </button>
                    <button class="btn btn-default" type="button"
                      onclick="copyToClipboard('{{ $pendaftaran->password_text }}', 'Password')">
                      <i class="ti ti-copy fs-2"></i>
                    </button>
                  </div>
                </div>

                <div class="mt-2 text-muted small">
                  <i class="ti ti-alert-triangle fs-3 me-1"></i>
                  Berikan kredensial ini ke calon mahasiswa untuk login ke <a
                    href="{{ env('URL_SERVICE_SIAKAD') }}/login" target="_blank">PMB SIAKAD2</a>
                </div>
              </div>
            </div>
          @endif


        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-md-4 mb-4">
          {{-- CARD: Data Mitra --}}
          @if ($pendaftaran->mitra)
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="ti ti-user-check fs-2 me-2 text-orange"></i>
                  Data Mitra
                </h3>
              </div>
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-3 bg-orange-lt">
                    <span class="avatar-text">{{ substr($pendaftaran->mitra->name, 0, 2) }}</span>
                  </div>
                  <div>
                    <div class="fw-semibold">{{ $pendaftaran->mitra->name }}</div>
                    <div class="text-muted small">{{ $pendaftaran->mitra->email }}</div>
                  </div>
                </div>
              </div>
            </div>
          @endif

          {{-- CARD: Informasi Sistem --}}
          <div class="card mt-4">
            <div class="card-header">
              <h3 class="card-title">
                <i class="ti ti-info-circle fs-2 me-2 text-green"></i>
                Informasi Sistem PMB
              </h3>
            </div>
            <div class="card-body my-2">
              <div class="mb-3">
                <strong>ID Calon Mahasiswa:</strong><br>
                <code>{{ $pendaftaran->id_calon_mahasiswa ?: '-' }}</code>
              </div>
              <div class="mb-3">
                <strong>No. Transaksi:</strong><br>
                <code>{{ $pendaftaran->no_transaksi ?: '-' }}</code>
              </div>
              <div class="mb-3">
                <strong>Tanggal Daftar:</strong><br>
                {{ $pendaftaran->created_at->translatedFormat('d M Y H:i') }} <span
                  class="text-muted text-small">({{ $pendaftaran->created_at->diffForHumans() }})</span>
              </div>
              <div class="mb-3">
                <strong>Terakhir Update:</strong><br>
                {{ $pendaftaran->updated_at->translatedFormat('d M Y H:i') }} <span
                  class="text-muted text-small">({{ $pendaftaran->updated_at->diffForHumans() }})</span>
              </div>
              @if ($pendaftaran->synced_at)
                <div class="mb-3">

                  <strong>Terakhir Sync:</strong><br>
                  {{ $pendaftaran->synced_at->translatedFormat('d M Y H:i') }} <span
                    class="text-muted text-small">({{ $pendaftaran->synced_at->diffForHumans() }})</span>
                </div>
              @endif
            </div>
          </div>



          {{-- CARD: Response PMB SIAKAD2 --}}
          @if ($pendaftaran->response_data && is_array($pendaftaran->response_data))
            <div class="card mt-4">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="ti ti-code fs-2 me-2 text-purple"></i>
                  Respon PMB SIAKAD2
                </h3>
              </div>
              <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>{{ json_encode($pendaftaran->response_data, JSON_PRETTY_PRINT) }}</code></pre>
              </div>
            </div>
          @endif

          {{-- CARD: Aksi --}}
          <div class="card mt-4">
            <div class="card-body">
              <div class="d-grid gap-2">
                @can('pendaftaran_edit')
                  @if ($pendaftaran->status === 'pending')
                    <a href="{{ route('pendaftaran.edit', $pendaftaran) }}" class="btn btn-primary">
                      <i class="ti ti-edit fs-2 me-1"></i>
                      Edit Pendaftaran
                    </a>
                  @endif
                @endcan
                <a href="{{ route('pendaftaran.index') }}" class="btn btn-default">
                  <i class="ti ti-arrow-back-up fs-2 me-1"></i>
                  Kembali ke Daftar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('style')
  <style>
    .font-monospace {
      font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    }

    .avatar-text {
      font-weight: 600;
      text-transform: uppercase;
    }

    /* Toast di tengah atas */
    .custom-toast {
      top: 20px;
      left: 50%;
      transform: translateX(-50%) translateY(-30px);
      opacity: 0;
      transition: all 0.4s ease;
      z-index: 1060;
    }

    /* Saat muncul */
    .custom-toast.showing {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
    }

    /* Saat hilang ke bawah */
    .custom-toast.hiding {
      transform: translateX(-50%) translateY(30px);
      opacity: 0;
    }
  </style>
@endsection

@section('js_atas')
  {{-- kosong --}}
@endsection

@section('js_bawah')
  <script>
    function togglePassword() {
      const passwordField = document.getElementById('passwordField');
      const passwordIcon = document.getElementById('passwordIcon');

      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.className = 'ti ti-eye-off fs-2';
      } else {
        passwordField.type = 'password';
        passwordIcon.className = 'ti ti-eye fs-2';
      }
    }

    function copyToClipboard(text, type) {
      navigator.clipboard.writeText(text).then(function() {
        showToast(`${type} berhasil disalin!`, 'success');
      }).catch(function() {
        // Fallback untuk browser lama
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast(`${type} berhasil disalin!`, 'success');
      });
    }
  </script>
@endsection
