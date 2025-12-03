@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Notifikasi WhatsApp</h2>
          <div class="page-pretitle">Daftar pesan yang menunggu dikirim atau telah diproses</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @can('antrian_whatsapp_create')
              <a href="{{ route('antrian-notif-whatsapp.create') }}" class="btn btn-primary">
                <i class="ti ti-plus fs-2 me-2"></i>
                Kirim Pesan Baru
              </a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Daftar Antrean</h3>
          <div class="card-actions">
            <form method="GET" class="row g-2">
              <div class="col-auto">
                <select name="status" class="form-select">
                  <option value="">Semua Status</option>
                  <option value="{{ \App\Models\AntrianNotifWhatsappModel::PENDING }}"
                    {{ request('status') == \App\Models\AntrianNotifWhatsappModel::PENDING ? 'selected' : '' }}>Pending
                  </option>
                  <option value="{{ \App\Models\AntrianNotifWhatsappModel::SUKSES }}"
                    {{ request('status') == \App\Models\AntrianNotifWhatsappModel::SUKSES ? 'selected' : '' }}>Sukses
                  </option>
                  <option value="{{ \App\Models\AntrianNotifWhatsappModel::GAGAL }}"
                    {{ request('status') == \App\Models\AntrianNotifWhatsappModel::GAGAL ? 'selected' : '' }}>Gagal
                  </option>
                  <option value="{{ \App\Models\AntrianNotifWhatsappModel::DEAD }}"
                    {{ request('status') == \App\Models\AntrianNotifWhatsappModel::DEAD ? 'selected' : '' }}>Dead</option>
                </select>
              </div>
              <div class="col-auto">
                <select name="user_id" class="form-select">
                  <option value="">Semua Pengguna</option>
                  @foreach ($users as $user)
                    <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                      {{ $user->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-auto">
                <input type="text" name="cari" class="form-control" placeholder="Cari target atau pesan..."
                  value="{{ request('cari') }}">
              </div>
              <div class="col-auto">
                <button type="submit" class="btn btn-default">
                  <i class="ti ti-search fs-2 me-1"></i>
                  Filter
                </button>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-vcenter table-bordered table-md table-hover">
            <thead>
              <tr>
                <th class="text-center">ID</th>
                <th>Pengguna</th>
                <th>Target</th>
                <th>Jenis</th>
                <th>Isi Pesan</th>
                <th>Status</th>
                <th>Retry</th>
                <th>Dibuat</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($antrians as $antrian)
                <tr>
                  <td>{{ $antrian->antrian_id }}</td>
                  <td>
                    <span class="text-nowrap">{{ $antrian->user?->name ?? 'N/A' }}</span>
                    <br>
                    <small class="text-muted">{{ $antrian->user?->username ?? 'N/A' }}</small>
                  </td>
                  <td>{{ $antrian->target }}</td>
                  <td><span class="badge bg-blue-lt">{{ $antrian->tipe }}</span></td>
                  <td>
                    <div style="max-height: 3rem; overflow-y: auto;">{!! nl2br(e($antrian->isi_pesan)) !!}</div>
                  </td>
                  <td>
                    @php
                      $statusClass = '';
                      $statusText = '';
                      switch ($antrian->status) {
                          case \App\Models\AntrianNotifWhatsappModel::PENDING:
                              $statusClass = 'text-yellow bg-yellow-lt';
                              $statusText = 'Pending';
                              break;
                          case \App\Models\AntrianNotifWhatsappModel::SUKSES:
                              $statusClass = 'text-green bg-green-lt';
                              $statusText = 'Sukses';
                              break;
                          case \App\Models\AntrianNotifWhatsappModel::GAGAL:
                              $statusClass = 'text-orange bg-orange-lt';
                              $statusText = 'Gagal';
                              break;
                          case \App\Models\AntrianNotifWhatsappModel::DEAD:
                              $statusClass = 'text-red bg-red-lt';
                              $statusText = 'Dead';
                              break;
                          default:
                              $statusClass = 'text-gray bg-gray-lt';
                              $statusText = 'Tidak Dikenal';
                      }
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                  </td>
                  <td class="text-center">{{ $antrian->retry_count }}</td>
                  <td>{{ $antrian->created_at->translatedFormat('d M Y H:i:s') }}</td>
                  <td class="text-center">
                    <div class="btn-list justify-content-center">
                      <a href="{{ route('antrian-notif-whatsapp.show', $antrian) }}" class="btn btn-sm btn-default">
                        <i class="ti ti-eye fs-3 me-1"></i>
                        Detail
                      </a>
                      @if (in_array($antrian->status, [
                              \App\Models\AntrianNotifWhatsappModel::GAGAL,
                              \App\Models\AntrianNotifWhatsappModel::DEAD,
                          ]))
                        <form method="POST" action="{{ route('antrian-notif-whatsapp.retry', $antrian) }}"
                          class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-default text-warning" title="Retry">
                            <i class="ti ti-refresh fs-3"></i>
                          </button>
                        </form>
                      @endif
                      @can('antrian_whatsapp_edit')
                        @if ($antrian->status === \App\Models\AntrianNotifWhatsappModel::PENDING)
                          <a href="{{ route('antrian-notif-whatsapp.edit', $antrian) }}" class="btn btn-sm btn-default">
                            <i class="ti ti-edit fs-3 me-1"></i>
                            Edit
                          </a>
                        @endif
                      @endcan
                      @can('antrian_whatsapp_delete')
                        @if ($antrian->status === \App\Models\AntrianNotifWhatsappModel::PENDING)
                          <form method="POST" action="{{ route('antrian-notif-whatsapp.destroy', $antrian) }}"
                            class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-default text-danger delete-btn" title="Hapus"
                              data-name="{{ $antrian->isi_pesan }}">
                              <i class="ti ti-trash fs-3"></i>
                            </button>
                          </form>
                        @endif
                      @endcan
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-4">
                    <div class="empty">
                      <div class=" mb-2">
                        <i class="ti ti-message-circle fs-1"></i>
                      </div>
                      <p class="empty-title">Tidak ada antrean</p>
                      <p class="empty-subtitle text-muted">Belum ada pesan yang masuk ke antrean.</p>
                      <div class="empty-action">
                        <a href="{{ route('antrian-notif-whatsapp.create') }}" class="btn btn-primary">
                          <i class="ti ti-plus fs-2 me-1"></i>
                          Buat Antrean Baru
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if ($antrians->hasPages())
          <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
              Menampilkan <span>{{ $antrians->firstItem() }}</span> sampai
              <span>{{ $antrians->lastItem() }}</span> dari
              <span>{{ $antrians->total() }}</span> data
            </p>
            <ul class="pagination m-0 ms-auto">
              {{ $antrians->links('vendor.pagination.tabler') }}
            </ul>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
