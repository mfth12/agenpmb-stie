@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Detail Pengguna - {{ Str::of($pengguna->name)->explode(' ')->first() }}</h2>
          <div class="page-pretitle">Informasi lengkap pengguna</div>
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
              <div class="row mb-4">
                <div class="col-md-4 text-center">
                  <div class="avatar avatar-xl mb-3" style="background-image: url({{ $pengguna->avatar_url }})"></div>
                  <h4>{{ $pengguna->name }}</h4>
                  <span class="badge bg-secondary text-secondary-fg text-uppercase">
                    {{ $pengguna->getRoleNames()->first() }}
                  </span>

                  @if ($pengguna->siakad_id)
                    <div class="mt-2 d-flex justify-content-center align-items-center">
                      <i class="ti ti-rosette-discount-check-filled text-primary fs-3 me-1"></i>
                      <small class="text-muted">Terhubung Siakad</small>
                    </div>
                  @endif
                </div>
                <div class="col-md-8">
                  <div class="row">
                    <div class="col-md-6 mt-3 mt-md-0">
                      <strong>Asal Instansi/Sekolah:</strong><br>
                      {{ $pengguna->asal_sekolah }}
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                      <strong>Status:</strong><br>
                      <span {!! $pengguna->status_badge !!} </span>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Email:</strong><br>
                      {{ $pengguna->email }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Username:</strong><br>
                      {{ $pengguna->username }}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Nomor HP:</strong><br>
                      {{ $pengguna->nomor_hp }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Nomor Whatsapp:</strong><br>
                      {{ $pengguna->nomor_hp2 ?? '-' }}
                    </div>
                  </div>

                  {{-- TAMBAHAN: Section Tentang --}}
                  @if ($pengguna->about)
                    <div class="row">
                      <div class="col-12 mt-3">
                        <strong>Tentang:</strong><br>
                        {{ $pengguna->about }}
                      </div>
                    </div>
                  @endif

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Terakhir Login:</strong><br>
                      {{ $pengguna->last_logged_in ? $pengguna->last_logged_in->format('d/m/Y H:i') : 'Belum pernah' }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Bergabung Sejak:</strong><br>
                      {{ $pengguna->created_at->format('d/m/Y') }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-footer">
              <div class="d-flex flex-column-reverse flex-md-row-reverse bd-highlight">
                {{-- Tombol Approve --}}
                @if ($pengguna->status === 'pending')
                  <button type="button" class="btn btn-primary ms-md-2 mt-2 mt-md-0 approve-btn"
                    data-url="{{ route('pengguna.approve', $pengguna) }}" data-name="{{ $pengguna->name }}">
                    <i class="ti ti-check fs-2 me-1"></i>
                    Keputusan
                  </button>
                @endif
                @can('user_edit')
                  {{-- Tombol Edit --}}
                  <a href="{{ route('pengguna.edit', $pengguna) }}" class="btn btn-default ms-md-2 mt-2 mt-md-0">
                    <i class="ti ti-edit fs-2 me-1"></i>
                    Edit Pengguna
                  </a>
                @endcan
                <a href="{{ route('pengguna.index') }}" class="btn btn-default ms-md-2">
                  <i class="ti ti-arrow-back-up fs-2 me-1"></i>
                  Kembali
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js_bawah')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Handler untuk tombol approve
      document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', function() {
          const userName = this.getAttribute('data-name');
          const url = this.getAttribute('data-url');
          // Kita butuh URL khusus untuk update status, bukan hanya approve
          // Misalnya route('pengguna.update-status', $pengguna)
          // Untuk saat ini, kita asumsikan satu route untuk approve, dan satu untuk tolak
          // Kita buat URL baru untuk tolak
          const rejectUrl = url.replace('/approve', '/reject'); // Ganti '/approve' dengan '/reject' di URL

          // Gunakan Swal.fire untuk konfirmasi tindakan
          Swal.fire({
            title: 'Apa tindakan Anda?',
            text: `Berikan keputusan untuk calon pengguna (${userName})`,
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true, // Tambahkan deny button untuk "Tolak"
            confirmButtonText: 'Verifikasi',
            denyButtonText: 'Tolak',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            reverseButtons: false // Urutan: Batal, Tolak, Verifikasi
          }).then((result) => {
            if (result.isConfirmed) {
              submitStatusChange(url, 'active', userName);
            } else if (result.isDenied) {
              submitStatusChange(rejectUrl, 'inactive', userName); // Ganti ke URL reject
            }
            // Jika result.dismiss === Swal.DismissReason.cancel, tidak ada aksi
          });
        });
      });

      // Fungsi helper untuk submit perubahan status
      function submitStatusChange(actionUrl, newStatus, userName) {
        // Buat form dinamis
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;
        form.innerHTML = `
          @csrf
          @method('PUT')
          <input type="hidden" name="status" value="${newStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    });
  </script>
@endsection
