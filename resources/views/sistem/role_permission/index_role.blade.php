@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Manajemen Roles</h2>
          <div class="page-pretitle">Pengaturan role dan hak akses untuk sistem Mitra PMB</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('role_permission.create_role') }}" class="btn btn-primary">
            <i class="ti ti-plus fs-2 me-1"></i>
            Tambah Role
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-vcenter table-bordered table-md table-hover">
              <thead>
                <tr>
                  <th>Nama Role</th>
                  <th>Permission Terkait</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($roles as $role)
                  <tr>
                    <td>
                      <span class="badge bg-blue-lt">{{ $role->name }}</span>
                    </td>
                    <td>
                      @if ($role->permissions->count() > 0)
                        <div class="d-flex flex-wrap gap-1">
                          @foreach ($role->permissions as $perm)
                            <span class="badge bg-green-lt">{{ $perm->name }}</span>
                          @endforeach
                        </div>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td class="text-center">
                      <div class="btn-list justify-content-center">
                        <a href="{{ route('role_permission.edit_role', $role) }}" class="btn btn-sm btn-default"
                          title="Edit" data-bs-toggle="tooltip" data-bs-placement="top">
                          <i class="ti ti-edit fs-3"></i>
                        </a>
                        @if ($role->name !== 'superadmin')
                          <button type="button" class="btn btn-sm btn-default text-danger delete-btn" title="Hapus"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-name="{{ $role->name }}"
                            data-url="{{ route('role_permission.destroy_role', $role) }}">
                            <i class="ti ti-trash fs-3"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center py-4">
                      <div class="empty">
                        <div class=" mb-2">
                          <i class="ti ti-users fs-1"></i>
                        </div>
                        <p class="empty-title">Tidak ada role</p>
                        <p class="empty-subtitle text-muted">
                          Mulai dengan menambahkan role baru
                        </p>
                        <div class="empty-action">
                          <a href="{{ route('role_permission.create_role') }}" class="btn btn-primary">
                            <i class="ti ti-plus fs-2 me-1"></i>
                            Tambah Role
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if ($roles->hasPages())
            <div class="card-footer d-flex align-items-center">
              <p class="m-0 text-muted">
                Menampilkan <span>{{ $roles->firstItem() }}</span> sampai
                <span>{{ $roles->lastItem() }}</span> dari
                <span>{{ $roles->total() }}</span> data
              </p>
              <ul class="pagination m-0 ms-auto">
                {{ $roles->links('vendor.pagination.tabler') }}
              </ul>
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
      // Delete confirmation for roles
      document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
          const itemName = this.getAttribute('data-name');
          const url = this.getAttribute('data-url');

          showDeleteConfirmation(() => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `
            @csrf
            @method('DELETE')
          `;
            document.body.appendChild(form);
            form.submit();
          }, `role ${itemName}`);
        });
      });
    });
  </script>
@endsection
