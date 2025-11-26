@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Konfigurasi Sistem</h2>
          <div class="page-pretitle">Pengaturan global untuk {{ konfigs('NAMA_SISTEM') }}</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('konfigurasi.create') }}" class="btn btn-primary">
            <i class="ti ti-plus fs-2 me-1"></i>
            Tambah Konfigurasi
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      {{-- Stats Cards Baru --}}
      <div class="row row-cards mb-4 mt-0">
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-x text-white avatar">
                    <i class="ti ti-settings fs-1"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $konfigurasis->total() }}</div>
                  <div class="text-secondary">Total Konfigurasi</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-green text-white avatar">
                    <i class="ti ti-apps fs-1"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $groups->count() }}</div>
                  <div class="text-secondary">Grup Konfigurasi</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Tambahkan card lain sesuai kebutuhan -->
      </div>

      <div class="card">
        <div class="card-header">
          <div class="row w-full">
            <div class="col">
              <h3 class="card-title mb-0">Daftar Konfigurasi</h3>
              <p class="text-secondary m-0">Berikut adalah konfigurasi sistem yang dapat diatur</p>
            </div>
            <div class="col-md-auto col-sm-12">
              <div class="ms-auto d-flex flex-wrap btn-list">
                <form method="GET" class="row g-3">
                  <div class="col">
                    <input type="text" name="cari" class="form-control mt-2 mt-md-0"
                      placeholder="Cari Kunci/Grup" value="{{ request('cari') }}">
                  </div>
                  <div class="col">
                    <select name="group" class="form-select mt-2 mt-md-0">
                      <option value="">Semua Grup</option>
                      @foreach ($groups as $group)
                        <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                          {{ ucfirst($group) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-auto">
                    <button type="submit" class="btn btn-default mt-2 mt-md-0">
                      <i class="ti ti-filter fs-3 me-1"></i>
                      Filter
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="table-responsive" style="padding: 1rem">
          <table class="table table-vcenter table-bordered table-md table-hover">
            <thead>
              <tr>
                <th class="w-1">No</th>
                <th>Grup</th>
                <th>Kunci</th>
                <th>Nilai</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($konfigurasis as $konfigurasi)
                <tr>
                  <td class="text-muted">
                    {{ $loop->iteration + ($konfigurasis->currentPage() - 1) * $konfigurasis->perPage() }}</td>
                  <td>
                    <span
                      class="badge badge-lg font-monospace user-select-all bg-blue-lt">{{ $konfigurasi->config_group }}</span>
                  </td>
                  <td>
                    <span class="badge badge-lg font-monospace user-select-all">{{ $konfigurasi->config_key }}</span>
                  </td>
                  <td>
                    @if (strlen($konfigurasi->config_value) > 50)
                      <span class="text-truncate d-block" style="max-width: 200px;"
                        title="{{ $konfigurasi->config_value }}">
                        {{ substr($konfigurasi->config_value, 0, 50) }}...
                      </span>
                    @else
                      <span class="d-block">{{ $konfigurasi->config_value }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="btn-list justify-content-center">
                      <a href="{{ route('konfigurasi.edit', $konfigurasi) }}" class="btn btn-sm btn-default"
                        title="Edit" data-bs-toggle="tooltip" data-bs-placement="top">
                        <i class="ti ti-edit fs-3"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-default text-danger delete-btn" title="Hapus"
                        data-bs-toggle="tooltip" data-bs-placement="top" data-name="{{ $konfigurasi->config_key }}"
                        data-url="{{ route('konfigurasi.destroy', $konfigurasi) }}">
                        <i class="ti ti-trash fs-3"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4">
                    <div class="empty">
                      <div class=" mb-2">
                        <i class="ti ti-settings fs-1"></i>
                      </div>
                      <p class="empty-title">Tidak ada data konfigurasi</p>
                      <p class="empty-subtitle text-muted">
                        Mulai dengan menambahkan konfigurasi baru
                      </p>
                      <div class="empty-action">
                        <a href="{{ route('konfigurasi.create') }}" class="btn btn-primary">
                          <i class="ti ti-plus fs-2 me-1"></i>
                          Tambah
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if ($konfigurasis->hasPages())
          <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
              Menampilkan <span>{{ $konfigurasis->firstItem() }}</span> sampai
              <span>{{ $konfigurasis->lastItem() }}</span> dari
              <span>{{ $konfigurasis->total() }}</span> data
            </p>
            <ul class="pagination m-0 ms-auto">
              {{ $konfigurasis->links('vendor.pagination.tabler') }}
            </ul>
          </div>
        @endif
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
          }, `konfigurasi ${itemName}`);
        });
      });
    });
  </script>
@endsection
