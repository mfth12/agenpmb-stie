@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Pengguna Online</h2>
          <div class="page-pretitle">Daftar pengguna yang aktif</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('pengguna.index') }}" class="btn btn-default">
            <i class="ti ti-arrow-back-up fs-2 me-1"></i>
            Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="card">
        <div class="card-body my-2">
          {{-- Form filter --}}
          <form method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
              <input type="text" name="cari" class="form-control" placeholder="Cari nama, email, IP..."
                value="{{ request('cari') }}">
            </div>
            <div class="col-md-1">
              <select name="per_page" class="form-select">
                <option value="10"
                  {{ request('per_page') == '10' || request('per_page') === null ? 'selected' : '' }}>
                  10</option>
                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
              </select>
            </div>
            <div class="col-md-2">
              <select name="role" class="form-select">
                <option value="">Semua Role</option>
                @foreach ($roles as $role)
                  <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
              </select>
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-default w-100">
                <i class="ti ti-filter me-1"></i>
                Filter
              </button>
            </div>
          </form>
          <div class="table-responsive">
            <table class="table table-vcenter table-hover table-bordered">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th>Nama</th>
                  <th>IP Address & Device</th>
                  <th>Terakhir Aktif</th>
                  <th class="w-1">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($onlineUsers as $user)
                  <tr>
                    <td class="text-center">
                      {{ ($onlineUsers->currentPage() - 1) * $onlineUsers->perPage() + $loop->iteration }}</td>
                    <td data-label="Nama">
                      <div class="d-flex align-items-center text-reset">
                        <div class="flex-column">
                          <div class="fw-bold">
                            <a href="{{ route('pengguna.session.show', $user->id) }}"
                              class="text-reset link-hover-underline">
                              {{ $user->name }}
                            </a>
                            @if ($user->is_me)
                              <span class="badge bg-blue-lt ms-1">Anda</span>
                            @endif
                          </div>
                          <div class="text-secondary small">{{ $user->email }}</div>
                        </div>
                      </div>
                    </td>
                    <td data-label="Device">
                      <div>{{ $user->ip_address }}</div>
                      <div class="text-secondary small text-truncate" style="max-width: 300px;"
                        title="{{ $user->user_agent }}">
                        {{ $user->user_agent }}
                      </div>
                    </td>
                    <td data-label="Terakhir Aktif">
                      {{ $user->last_activity_human }}
                    </td>
                    <td>
                      <div class="btn-list flex-nowrap">
                        @if (!$user->is_me)
                          <button type="button" class="btn btn-sm btn-ghost-danger revoke-btn"
                            data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                            Hentikan
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted p-4">
                      Tidak ada pengguna online saat ini.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if ($onlineUsers->hasPages())
            <div class="card-footer d-flex align-items-center">
              <p class="m-0 text-secondary">
                Menampilkan <span>{{ $onlineUsers->firstItem() }}</span> ke <span>{{ $onlineUsers->lastItem() }}</span>
                dari
                <span>{{ $onlineUsers->total() }}</span> entri
              </p>
              <div class="m-0 ms-auto">
                {{ $onlineUsers->links() }}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  @endsection

  @section('js_bawah')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.revoke-btn').forEach(btn => {
          btn.addEventListener('click', function() {
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
        });

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
                  window.location.reload();
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
