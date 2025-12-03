@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Kirim Notifikasi WhatsApp</h2>
          <div class="page-pretitle">Buat antrean pesan baru untuk dikirim</div>
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
          <form action="{{ route('antrian-notif-whatsapp.store') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Pilih Pengguna (Opsional)</label>
                  <select name="user_id" id="user-select" class="form-select @error('user_id') is-invalid @enderror">
                    <option value="">- Pilih Pengguna -</option>
                    @foreach ($users as $user)
                      <option value="{{ $user->user_id }}" {{ old('user_id') == $user->user_id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->username }}) - {{ $user->nomor_hp2 ?? ($user->nomor_hp ?? 'N/A') }}
                      </option>
                    @endforeach
                  </select>
                  @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-hint">Jika dipilih, nomor HP akan diambil otomatis dari pengguna.</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label required">Nomor Target</label>
                  <input type="text" name="target" id="target-input" value="{{ old('target') }}"
                    class="form-control @error('target') is-invalid @enderror"
                    placeholder="Masukkan nomor WhatsApp (misal: 628xxx)" required>
                  @error('target')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-hint">Format: 628xxxxxx (tanpa spasi atau tanda baca lainnya).</small>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label required">Jenis Pesan</label>
              <select name="tipe" class="form-select @error('tipe') is-invalid @enderror" required>
                <option value="text" {{ old('tipe') == 'text' ? 'selected' : '' }}>Teks</option>
                <!-- Tambahkan opsi lain jika gateway mendukung -->
                <!-- <option value="image" {{ old('tipe') == 'image' ? 'selected' : '' }}>Gambar</option> -->
                <!-- <option value="document" {{ old('tipe') == 'document' ? 'selected' : '' }}>Dokumen</option> -->
              </select>
              @error('tipe')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label class="form-label required">Isi Pesan</label>
              <textarea name="isi_pesan" class="form-control @error('isi_pesan') is-invalid @enderror" rows="5"
                placeholder="Tulis pesan Anda disini..." required>{{ old('isi_pesan') }}</textarea>
              @error('isi_pesan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Tambah ke Antrean</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js_bawah')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const userSelect = document.getElementById('user-select');
      const targetInput = document.getElementById('target-input');

      userSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        // Ambil nomor dari teks option (misalnya dari "(6281234567890)")
        const nomorMatch = selectedOption.text.match(/\((\d+)\)/);
        const nomor = nomorMatch ? nomorMatch[1] : '';

        if (nomor) {
          targetInput.value = nomor;
          targetInput.disabled = true; // Disable input jika diisi dari select
        } else {
          targetInput.value = '';
          targetInput.disabled = false; // Enable kembali jika user direset
        }
      });

      // Enable input jika user direset (pilihan kosong dipilih)
      targetInput.addEventListener('focus', function() {
        if (userSelect.value === '') {
          this.disabled = false;
        }
      });
    });
  </script>
@endsection
