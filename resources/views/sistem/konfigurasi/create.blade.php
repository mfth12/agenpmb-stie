@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Tambah Konfigurasi</h2>
          <div class="page-pretitle">Buat entri konfigurasi baru</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('konfigurasi.index') }}" class="btn btn-default">
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
          <form action="{{ route('konfigurasi.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label required">Grup Konfigurasi</label>
              <input type="text" name="config_group" value="{{ old('config_group') }}"
                class="form-control @error('config_group') is-invalid @enderror"
                placeholder="Misal: basic, identitas, server" required>
              @error('config_group')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label class="form-label required">Kunci Konfigurasi</label>
              <input type="text" name="config_key" value="{{ old('config_key') }}"
                class="form-control @error('config_key') is-invalid @enderror"
                placeholder="Misal: NAMA_PT, DEFAULT_TA, WA_ENDPOINT" required>
              @error('config_key')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Nilai Utama</label>
              <textarea name="config_value" class="form-control @error('config_value') is-invalid @enderror" rows="3"
                placeholder="Masukkan nilai konfigurasi">{{ old('config_value') }}</textarea>
              @error('config_value')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Nilai Tambahan 1</label>
                  <input type="text" name="value_1" value="{{ old('value_1') }}"
                    class="form-control @error('value_1') is-invalid @enderror" placeholder="Optional value">
                  @error('value_1')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Nilai Tambahan 2</label>
                  <input type="text" name="value_2" value="{{ old('value_2') }}"
                    class="form-control @error('value_2') is-invalid @enderror" placeholder="Optional value">
                  @error('value_2')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Nilai Tambahan 3</label>
                  <input type="text" name="value_3" value="{{ old('value_3') }}"
                    class="form-control @error('value_3') is-invalid @enderror" placeholder="Optional value">
                  @error('value_3')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Nilai Tambahan 4</label>
                  <input type="text" name="value_4" value="{{ old('value_4') }}"
                    class="form-control @error('value_4') is-invalid @enderror" placeholder="Optional value">
                  @error('value_4')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Nilai Tambahan 5</label>
                  <input type="text" name="value_5" value="{{ old('value_5') }}"
                    class="form-control @error('value_5') is-invalid @enderror" placeholder="Optional value">
                  @error('value_5')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Simpan Konfigurasi</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
