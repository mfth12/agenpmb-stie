@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Detail Notifikasi WhatsApp</h2>
          <div class="page-pretitle">#{{ $antrian->antrian_id }}</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('antrian-notif-whatsapp.index') }}" class="btn btn-default">
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
        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">ID Antrian</label>
                    <input type="text" readonly class="form-control" value="{{ $antrian->antrian_id }}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">ID Pengguna</label>
                    <input type="text" readonly class="form-control" value="{{ $antrian->user_id ?? 'N/A' }}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Nama Pengguna</label>
                    <input type="text" readonly class="form-control" value="{{ $antrian->user?->name ?? 'N/A' }}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Sesi</label>
                    <input type="text" readonly class="form-control" value="{{ $antrian->sesi }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Target (Nomor)</label>
                    <input type="text" readonly class="form-control" value="{{ $antrian->target }}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Jenis Pesan</label>
                    <input type="text" readonly class="form-control" value="{{ $antrian->tipe }}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Status</label>
                    <span class="d-block">
                      @php
                        $statusClass = '';
                        $statusText = '';
                        switch ($antrian->status) {
                            case \App\Models\AntrianNotifWhatsappModel::PENDING:
                                $statusClass = 'badge badge-lg text-yellow bg-yellow-lt ';
                                $statusText = 'Pending';
                                break;
                            case \App\Models\AntrianNotifWhatsappModel::SUKSES:
                                $statusClass = 'badge badge-lg text-green bg-green-lt ';
                                $statusText = 'Sukses';
                                break;
                            case \App\Models\AntrianNotifWhatsappModel::GAGAL:
                                $statusClass = 'badge badge-lg text-orange bg-orange-lt ';
                                $statusText = 'Gagal';
                                break;
                            case \App\Models\AntrianNotifWhatsappModel::DEAD:
                                $statusClass = 'badge badge-lg text-red bg-red-lt ';
                                $statusText = 'Dead';
                                break;
                            default:
                                $statusClass = 'badge badge-lg text-gray bg-gray-lt ';
                                $statusText = 'Tidak Dikenal';
                        }
                      @endphp
                      <span class="{{ $statusClass }}">{{ $statusText }}</span>
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Jumlah Retry</label>
                    <input type="text" readonly class="form-control" value="{{ $antrian->retry_count }}">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="mb-3">
                    <label class="form-label">Isi Pesan</label>
                    <textarea readonly class="form-control" rows="5" style="white-space: pre-wrap;">{{ $antrian->isi_pesan }}</textarea>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="mb-3">
                    <label class="form-label">Waktu Dibuat</label>
                    <input type="text" readonly class="form-control"
                      value="{{ $antrian->created_at->translatedFormat('d M Y H:i:s') }}">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="mb-3">
                    <label class="form-label">Waktu Diupdate</label>
                    <input type="text" readonly class="form-control"
                      value="{{ $antrian->updated_at->translatedFormat('d M Y H:i:s') }}">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
