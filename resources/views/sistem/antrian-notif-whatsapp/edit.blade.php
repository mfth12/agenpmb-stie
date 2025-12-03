@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Edit Notifikasi WhatsApp</h2>
          <div class="page-pretitle">#{{ $antrian->antrian_id }} - {{ Str::limit($antrian->isi_pesan, 30) }}</div>
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
      <div class="card">
        <div class="card-body">
          @if ($antrian->status !== \App\Models\AntrianNotifWhatsappModel::PENDING)
            <div class="alert alert-warning">
              <i class="ti ti-alert-triangle fs-2 me-2"></i>
              Peringatan: Antrean ini tidak dalam status Pending. Anda mungkin tidak dapat mengeditnya.
            </div>
          @endif
          <form action="{{ route('antrian-notif-whatsapp.update', $antrian) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label class="form-label required">Isi Pesan</label>
              <textarea name="isi_pesan" class="form-control @error('isi_pesan') is-invalid @enderror" rows="5" required>{{ old('isi_pesan', $antrian->isi_pesan) }}</textarea>
              @error('isi_pesan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Perbarui Antrean</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
