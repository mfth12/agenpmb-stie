@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Manajemen Pengguna</h2>
          <div class="page-pretitle">Semua pengguna {{ konfigs('NAMA_SISTEM') }}</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          @can('user_create')
            <a href="{{ route('pengguna.create') }}" class="btn btn-primary">
              <i class="ti ti-plus fs-2 me-1"></i>
              Tambah
            </a>
          @endcan
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
              <input type="text" name="cari" class="form-control"
                placeholder="Cari nama, sekolah, email, username..." value="{{ request('cari') }}">
            </div>
            <div class="col-md-1">
              <select name="per_page" class="form-select" data-placeholder="Per/hal.">
                <option value="10"{{ request('per_page') == '10' || request('status') === null ? 'selected' : '' }}>
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

          {{-- Tabel user --}}
          <div class="table-responsive">
            <table class="table table-vcenter table-bordered table-striped">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th>Nama</th>
                  <th>Asal Instansi / Sekolah</th>
                  <th>Email</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Terakhir Login</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($pengguna as $user)
                  <tr>
                    <td class="text-center">{{ $pengguna->firstItem() + $loop->index }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm me-2 flex-shrink-0"
                          style="background-image: url({{ $user->avatar_thumb_url }})">
                        </span>
                        <span>
                          <strong>
                            <a href="{{ route('pengguna.show', $user) }}" class="text-reset link-hover-underline">
                              {{ $user->name }}
                            </a>
                          </strong>
                        </span>
                        @if ($user->siakad_id)
                          <i class="ti ti-rosette-discount-check-filled fs-2 text-primary ms-1" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Akun Siakad"></i>
                        @endif
                      </div>
                    </td>
                    <td>{{ $user->asal_sekolah ?? $user->afiliasi_nama }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->username }}</td>
                    <td>
                      <span class="text-uppercase">
                        {{ $user->getRoleNames()->first() }}
                      </span>
                    </td>
                    <td>
                      {!! $user->status_badge !!}
                    </td>
                    <td>
                      <span data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ $user->last_logged_in ? $user->last_logged_in?->diffForHumans() : '' }}">
                        {{ $user->last_logged_in?->translatedFormat('d M Y H:i') ?? 'Belum pernah' }}
                      </span>
                    </td>
                    <td class="text-center" style="width: 1%;">
                      <div class="btn-list justify-content-center flex-nowrap">
                        @can('user_edit')
                          <a href="{{ route('pengguna.edit', $user) }}" class="btn btn-sm btn-default"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                            Edit
                          </a>
                        @else
                          -
                        @endcan
                        @can('user_delete')
                          @if (!$user->hasRole('superadmin') && $user->user_id != auth()->id())
                            <form action="{{ route('pengguna.destroy', $user) }}" method="POST"
                              class="d-inline delete-form">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-sm btn-default text-danger delete-btn"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                data-name="{{ $user->name }}">
                                Hapus
                              </button>
                            </form>
                          @endif
                        @endcan
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted">Tidak ada data pengguna</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- Pagination --}}
          @if ($pengguna->hasPages())
            <div class="mt-4">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted">
                  Menampilkan
                  <strong>{{ $pengguna->firstItem() }}</strong> -
                  <strong>{{ $pengguna->lastItem() }}</strong>
                  dari
                  <strong>{{ $pengguna->total() }}</strong>
                  data
                </div>

                <div>
                  {{ $pengguna->appends(request()->query())->links('vendor.pagination.tabler') }}
                </div>
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
      // Delete confirmation
      document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
          const form = this.closest('form');
          const userName = this.getAttribute('data-name');

          showDeleteConfirmation(() => {
            form.submit();
          }, `pengguna ${userName}`);
        });
      });
    });
  </script>
@endsection
