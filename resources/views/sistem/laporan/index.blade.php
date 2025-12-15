@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Statistik & Laporan</h2>
          <div class="page-pretitle">Analisis dan dokumen resmi pendaftaran calon mahasiswa</div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      {{-- Grafik --}}
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Jumlah Pendaftar PMB</h3>
              <span class="text-muted text-sm ms-1">(12 Bulan Terakhir)</span>
            </div>
            <div class="card-body">
              <canvas id="chartPendaftaranPerBulan" height="135"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Distribusi Status</h3>
            </div>
            <div class="card-body">
              <div class="width-30">
                <canvas id="chartDistribusiStatus" height="70"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Distribusi Program Studi</h3>
            </div>
            <div class="card-body">
              <div class="width-30">
                <canvas id="chartDistribusiProdi" height="70"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row row-cards mb-4 mt-2">
        <div class="col-sm-4 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-x text-white avatar">
                    <i class="ti ti-file-isr fs-1"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $pendaftaran->count() }}</div>
                  <div class="text-secondary">Seluruh Pendaftar</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-4 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-green text-white avatar">
                    <i class="ti ti-cloud-check fs-1"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $pendaftaran->whereIn('status', ['success', 'synced', 'imported'])->count() }}
                  </div>
                  <div class="text-secondary">Berhasil, Tersinkron, Terimpor</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-4 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-warning text-white avatar">
                    <i class="ti ti-progress-alert fs-1"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $pendaftaran->where('status', 'pending')->count() }}</div>
                  <div class="text-secondary">Pending</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-4 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-danger text-white avatar">
                    <i class="ti ti-cloud-off fs-1"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $pendaftaran->where('status', 'failed')->count() }}</div>
                  <div class="text-secondary">Gagal</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Card Filter --}}
      <div class="card mt-4">
        <div class="card-header">
          <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
          <form id="filter-form" method="GET">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Mitra</label>
                <select name="mitra_filter" id="mitra_filter" class="form-select text-dark bg-light select2-tabler"
                  data-placeholder="{{ auth()->user()->hasRole('mitra') ? '' : 'Semua Mitra' }}">
                  @if (auth()->user()->hasRole('mitra'))
                    {{-- Mitra hanya bisa melihat dirinya sendiri --}}
                    <option value="{{ auth()->id() }}" selected>
                      {{ auth()->user()->name }}
                    </option>
                  @else
                    <option value=""></option> {{-- Placeholder untuk Select2 --}}
                    @foreach ($mitra as $mitraa)
                      <option value="{{ $mitraa->user_id }}"
                        {{ request('mitra_filter') == $mitraa->user_id ? 'selected' : '' }}>
                        {{ $mitraa->name }}
                        {{ $mitraa->asal_sekolah ? '- ' . Str::limit($mitraa->asal_sekolah, 16, '..') : '' }}
                      </option>
                    @endforeach
                  @endif
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <select name="tahun_filter" id="tahun_filter" class="form-select text-dark bg-light">
                  <option value="">Semua Tahun</option>
                  @for ($i = date('Y') + 1; $i >= 2020; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                  @endfor
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Gelombang</label>
                <select name="gelombang_filter" id="gelombang_filter" class="form-select text-dark bg-light">
                  <option value="">Semua Gel.</option>
                  @foreach ($distribusiGel as $gel)
                    <option value="{{ $gel->gelombang }}">{{ ucfirst($gel->gelombang) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Program Studi</label>
                <select name="prodi_filter" id="prodi_filter" class="form-select text-dark bg-light">
                  <option value="">Semua Prodi</option>
                  @foreach (App\Models\PendaftaranModel::daftarProdiAktif() as $id => $nama)
                    <option value="{{ $id }}">S1-{{ $nama }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status_filter" id="status_filter" class="form-select text-dark bg-light">
                  <option value="">Semua Status</option>
                  @foreach ($distribusiStatus as $dstStatus)
                    <option value="{{ $dstStatus->status }}">{{ ucfirst($dstStatus->status) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mt-3">
              <div class="d-flex flex-column flex-md-row bd-highlight">
                <button type="button" id="apply-filter" class="btn btn-primary">
                  <i class="ti ti-filter fs-2 me-1"></i>
                  Terapkan Filter
                </button>
                <button type="button" id="cetak-button" class="btn btn-default ms-md-2 mt-2 mt-md-0">
                  <i class="ti ti-printer fs-2 me-1"></i>
                  Cetak
                </button>
                <button type="button" id="export-pdf" class="btn btn-default ms-md-2 mt-2 mt-md-0">
                  <i class="ti ti-download fs-2 me-1"></i>
                  Ekspor PDF
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Tabel Hasil --}}
      <div class="card mt-4">
        <div class="card-header">
          <h3 class="card-title">Data Pendaftaran</h3>
        </div>
        <div class="table-responsive" style="padding: 1.2rem">
          <table id="laporan-table" class="table table-vcenter table-bordered table-md table-hover">
            <thead>
              <tr>
                <th class="w-1 text-center">No</th>
                <th>Calon Mahasiswa</th>
                <th>Program Studi</th>
                <th>Akademik</th>
                <th>Mitra</th>
                <th>Asal</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- Data akan diisi oleh DataTables --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('style')
  <style>
    /* Tambahkan styling jika perlu */
  </style>

  {{-- DataTables CSS --}}
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endsection

@section('js_atas')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('js_bawah')
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

  <script>
    $(document).ready(function() {
      // Inisialisasi Select2
      $('.select2-tabler').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: $(this).data('placeholder'),
        allowClear: true,
        "language": {
          "noResults": function() {
            return "Tidak ditemukan";
          }
        },
      });
    });

    document.addEventListener('DOMContentLoaded', function() {
      // Inisialisasi Chart.js
      const ctx1 = document.getElementById('chartPendaftaranPerBulan').getContext('2d');
      new Chart(ctx1, {
        type: 'line',
        data: {
          labels: [
            @foreach ($pendaftaranPerBulan as $item)
              '{{ $item->bulan_tahun }}',
            @endforeach
          ],
          datasets: [{
            label: 'Jumlah Pendaftaran',
            data: [
              @foreach ($pendaftaranPerBulan as $item)
                {{ $item->jumlah }},
              @endforeach
            ],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false // Sembunyikan legend jika label dataset tidak perlu
            }
          }
        }
      });

      const ctx2 = document.getElementById('chartDistribusiStatus').getContext('2d');
      new Chart(ctx2, {
        type: 'doughnut',
        data: {
          labels: [
            @foreach ($distribusiStatus as $item)
              '{{ ucfirst($item->status) }}',
            @endforeach
          ],
          datasets: [{
            label: 'Jumlah',
            data: [
              @foreach ($distribusiStatus as $item)
                {{ $item->jumlah }},
              @endforeach
            ],
            backgroundColor: [
              'rgba(255, 99, 132, 0.8)', // Pending (Red-ish)
              'rgba(75, 192, 192, 0.8)', // Success (Blue-ish)
              'rgba(255, 205, 86, 0.8)', // Failed (Yellow-ish)
              // Tambahkan warna lain jika ada status lain
            ],
            hoverOffset: 4
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });

      const ctx3 = document.getElementById('chartDistribusiProdi').getContext('2d');
      new Chart(ctx3, {
        type: 'doughnut',
        data: {
          labels: [
            @foreach ($distribusiProdi as $item)
              '{{ ucfirst(strtolower($item->prodi_nama)) }}',
            @endforeach
          ],
          datasets: [{
            label: 'Jumlah',
            data: [
              @foreach ($distribusiProdi as $item)
                {{ $item->jumlah }},
              @endforeach
            ],
            backgroundColor: [
              'rgba(189, 21, 21, 0.8)', // Manajemen (Red)
              'rgba(46, 107, 201, 0.8)', // Akuntansi (Blue)
            ],
            hoverOffset: 4
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
      // Chart.js

      // Inisialisasi DataTables
      let table = $('#laporan-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        stateSave: true,
        searchDelay: 500,
        ajax: {
          url: "{{ route('laporan.data') }}",
          type: 'GET',
          data: function(d) {
            // Kirim parameter filter ke server
            d.mitra_filter = $('#mitra_filter').val();
            d.tahun_filter = $('#tahun_filter').val();
            d.gelombang_filter = $('#gelombang_filter').val();
            d.prodi_filter = $('#prodi_filter').val();
            d.status_filter = $('#status_filter').val();
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'pendaftaran_id',
            orderable: true,
            searchable: false,
            className: 'text-muted text-center'
          },
          {
            data: 'calon_mahasiswa',
            name: 'calon_mahasiswa'
          },
          {
            data: 'prodi',
            name: 'prodi'
          },
          {
            data: 'akademik',
            name: 'akademik'
          },
          {
            data: 'mitra_name',
            name: 'mitra_name'
          },
          {
            data: 'asal_sekolah',
            name: 'asal_sekolah'
          },
          {
            data: 'aksi',
            name: 'aksi',
            orderable: false,
            searchable: false,
            className: 'text-center'
          }
        ],
        columnDefs: [{
            targets: [6],
            orderable: false
          } // Kolom Aksi tidak bisa diurutkan
        ],
        pageLength: 25,
        lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "Semua"]
        ], //jumlah data yang ditampilkan
        order: [
          [0, 'desc']
        ], // Urutkan berdasarkan pendaftar_id (indeks 0) secara descending
        language: {
          url: '/data/datatables-id.json' // Bahasa Indonesia
        },
      });

      // Handler untuk tombol "Terapkan Filter"
      $('#apply-filter').on('click', function() {
        table.ajax.reload(); // Reload DataTable dengan parameter filter terbaru
      });

      // Handler untuk tombol "Cetak Halaman"
      $('#cetak-button').click(function() {
        const formData = new FormData($('#filter-form')[0]);
        try {
          // Ambil base URL
          let printURL = '{{ route('laporan.generate-pdf-get') }}';
          // Siapkan object URLSearchParams untuk menampung semua parameter GET
          let params = new URLSearchParams();
          // Tambahkan parameter cetak=true
          params.append('cetak', 'true');
          // Loop semua data dari form dan masukkan ke parameter GET
          for (let [key, value] of formData.entries()) {
            if (value !== '') { // hanya kirim jika tidak kosong
              params.append(key, value);
            }
          }
          // Gabungkan dengan URL, buka tab baru
          printURL = printURL + '?' + params.toString();
          window.open(printURL, '_blank');
        } catch (error) {
          console.error("Terjadi kesalahan saat memproses pencetakan:", error.message);
          alert("Terjadi kesalahan saat mencoba mencetak. Silakan ulangi.");
        }
      });

      // Handler untuk tombol "Ekspor ke PDF"
      $('#export-pdf').on('click', function() {
        // Ambil nilai filter saat ini
        const formData = new FormData($('#filter-form')[0]);

        // Buat form sementara untuk submit POST
        const postForm = document.createElement('form');
        postForm.method = 'POST';
        postForm.action = "{{ route('laporan.generate-pdf') }}";

        // Tambahkan token CSRF
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = $('meta[name="csrf-token"]').attr('content');
        postForm.appendChild(csrfInput);

        // Tambahkan data filter ke form
        for (let [key, value] of formData.entries()) {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = key;
          input.value = value;
          postForm.appendChild(input);
        }

        document.body.appendChild(postForm);
        postForm.submit();
        document.body.removeChild(postForm);
      });

      // Fungsi untuk cetak PDF satu data (opsional)
      window.cetakPdfSatu = function(id) {
        // Implementasi cetak satu data jika diperlukan
        // Misalnya, buat route baru dan kirim ID ke controller untuk generate PDF spesifik
        alert('Fitur cetak satu data (' + id + ') akan diimplementasikan.');
      };
    });
  </script>
@endsection
