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
      {{-- Widget Statistik --}}
      <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-blue text-white avatar">
                    <i class="ti ti-users fs-2"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $totalPendaftaran }}</div>
                  <div class="text-secondary">Total Pendaftaran</div>
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
                    <i class="ti ti-check fs-2"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $totalPendaftaranBerhasil }}</div>
                  <div class="text-secondary">Berhasil</div>
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
                  <span class="bg-warning text-white avatar">
                    <i class="ti ti-clock fs-2"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $totalPendaftaranPending }}</div>
                  <div class="text-secondary">Pending</div>
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
                  <span class="bg-red text-white avatar">
                    <i class="ti ti-x fs-2"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $totalPendaftaranGagal }}</div>
                  <div class="text-secondary">Gagal</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Grafik --}}
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Jumlah Pendaftaran per Bulan (6 Bulan Terakhir)</h3>
            </div>
            <div class="card-body">
              <canvas id="chartPendaftaranPerBulan" height="200"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-4 mt-4 mt-md-0">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Distribusi Status Pendaftaran</h3>
            </div>
            <div class="card-body">
              <div class="width-30">
                <canvas id="chartDistribusiStatus" height="70"></canvas>
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
              <div class="col-md-3">
                <label class="form-label">Agen</label>
                <select name="mitra_filter" id="mitra_filter" class="form-select">
                  <option value="">Semua Mitra</option>
                  @foreach ($mitra as $mitraa)
                    <option value="{{ $mitraa->user_id }}">{{ $mitraa->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <select name="tahun_filter" id="tahun_filter" class="form-select">
                  <option value="">Semua Tahun</option>
                  @for ($i = date('Y') + 1; $i >= 2020; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                  @endfor
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Gelombang</label>
                <select name="gelombang_filter" id="gelombang_filter" class="form-select">
                  <option value="">Semua Gelombang</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Program Studi</label>
                <select name="prodi_filter" id="prodi_filter" class="form-select">
                  <option value="">Semua Prodi</option>
                  @foreach (App\Models\PendaftaranModel::daftarProdiAktif() as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status_filter" id="status_filter" class="form-select">
                  <option value="">Semua Status</option>
                  <option value="pending">Pending</option>
                  <option value="success">Berhasil</option>
                  <option value="failed">Gagal</option>
                </select>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-12">
                <button type="button" id="apply-filter" class="btn btn-primary me-2">
                  <i class="ti ti-filter me-1"></i>
                  Terapkan Filter
                </button>
                <button type="button" id="export-pdf" class="btn btn-success">
                  <i class="ti ti-download me-1"></i>
                  Ekspor ke PDF
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
        <div class="table-responsive" style="padding: 1rem">
          <table id="laporan-table" class="table table-vcenter table-bordered table-md table-hover">
            <thead>
              <tr>
                <th class="w-1">No</th>
                <th>Calon Mahasiswa</th>
                <th>Program Studi</th>
                <th>Akademik</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Agen</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <!-- Data akan diisi oleh DataTables -->
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
@endsection

@section('js_atas')
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('js_bawah')
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // --- Inisialisasi Chart.js ---
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
      // --- END Chart.js ---

      // --- Inisialisasi DataTables ---
      let table = $('#laporan-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
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
            orderable: false,
            searchable: false,
            className: 'text-muted text-center'
          },
          {
            data: 'calon_mahasiswa',
            name: 'nama_lengkap'
          },
          {
            data: 'prodi',
            name: 'prodi_nama'
          },
          {
            data: 'akademik',
            name: 'tahun'
          },
          {
            data: 'biaya',
            name: 'biaya'
          },
          {
            data: 'status_badge',
            name: 'status'
          },
          {
            data: 'mitra_nama',
            name: 'mitra_id'
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
            targets: [0, 7],
            orderable: false
          } // Kolom No dan Aksi tidak bisa diurutkan
        ],
        order: [
          [3, 'desc']
        ], // Urutkan berdasarkan kolom tahun (indeks 3) secara descending
        language: {
          url: '/data/datatables-id.json' // Bahasa Indonesia
        },
      });
      // --- END DataTables ---

      // Handler untuk tombol "Terapkan Filter"
      $('#apply-filter').on('click', function() {
        table.ajax.reload(); // Reload DataTable dengan parameter filter terbaru
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
