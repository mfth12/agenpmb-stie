@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Detail Sesi Pengguna</h2>
          <div class="page-pretitle">Informasi rinci sesi aktif</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('pengguna.online') }}" class="btn btn-default">
            <i class="ti ti-arrow-back-up fs-2 me-1"></i>
            Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row justify-content-center mb-4">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Pengguna</h3>
            </div>
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                  <span class="avatar avatar-xl" style="background-image: url({{ $session->avatar_url }})"></span>
                </div>
                <div class="col-md-10">
                  <div class="datagrid">
                    <div class="datagrid-item">
                      <div class="datagrid-title">Nama</div>
                      <div class="datagrid-content">{{ $session->name }}</div>
                    </div>
                    <div class="datagrid-item">
                      <div class="datagrid-title">Email</div>
                      <div class="datagrid-content">{{ $session->email }}</div>
                    </div>
                    <div class="datagrid-item">
                      <div class="datagrid-title">IP Address</div>
                      <div class="datagrid-content">{{ $session->ip_address }}</div>
                    </div>
                    <div class="datagrid-item">
                      <div class="datagrid-title">Terakhir Aktif</div>
                      <div class="datagrid-content">{{ $session->last_activity }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Detail Perangkat & Browser</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Device</div>
                  <div class="datagrid-content">
                    @if ($session->is_desktop)
                      <i class="ti ti-device-desktop me-1"></i> Desktop
                    @elseif($session->is_mobile)
                      <i class="ti ti-device-mobile me-1"></i> Mobile
                    @else
                      {{ $session->device }}
                    @endif
                  </div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Platform</div>
                  <div class="datagrid-content">{{ $session->platform }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Browser</div>
                  <div class="datagrid-content">{{ $session->browser }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">User Agent Raw</div>
                  <div class="datagrid-content">
                    <code class="small text-break">{{ $session->user_agent_raw }}</code>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-end">
              @if (!$session->is_me)
                <button type="button" class="btn btn-danger revoke-btn" data-id="{{ $session->id }}"
                  data-name="{{ $session->name }}">
                  <i class="ti ti-ban fs-2 me-2"></i> Hentikan Sesi
                </button>
              @else
                <span class="badge bg-blue-lt">Ini adalah sesi Anda saat ini</span>
              @endif
            </div>
          </div>
        </div>
      </div>


      {{-- <div class="row row-cards">
        <div class="col-md-6">

        </div>
        <div class="col-md-6">

        </div>
      </div> --}}
    </div>
  </div>
@endsection

@section('js_bawah')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const revokeBtn = document.querySelector('.revoke-btn');
      if (revokeBtn) {
        revokeBtn.addEventListener('click', function() {
          const sessionId = this.getAttribute('data-id');
          const userName = this.getAttribute('data-name');

          Swal.fire({
            title: 'Apakah anda yakin?',
            text: `Sesi pengguna ${userName} akan dihentikan paksa. Pengguna harus login kembali.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hentikan',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if (result.isConfirmed) {
              revokeSession(sessionId, userName);
            }
          });
        });
      }

      function revokeSession(sessionId, userName) {
        fetch(`{{ url('pengguna/session') }}/${sessionId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire(
                'Berhasil!',
                `Sesi ${userName} telah dihentikan.`,
                'success'
              ).then(() => {
                window.location.href = '{{ route('pengguna.online') }}';
              });
            } else {
              Swal.fire(
                'Gagal!',
                data.message || 'Terjadi kesalahan saat menghentikan sesi.',
                'error'
              );
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire(
              'Gagal!',
              'Terjadi kesalahan koneksi.',
              'error'
            );
          });
      }
    });
  </script>
@endsection
