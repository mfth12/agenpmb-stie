@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Manajemen Pendaftaran</h2>
          <div class="page-pretitle">Gerbang pendaftaran calon mahasiswa baru melalui mitra PMB</div>
        </div>
        <div class="col-4 col-md-auto ms-auto d-print-none">
          @can('pendaftaran_create')
            <a href="{{ route('pendaftaran.create') }}" class="btn btn-primary me-1">
              <i class="ti ti-plus fs-2 me-2"></i>
              Buat Pendaftaran
            </a>
          @endcan
          @can('pendaftaran_create')
            <a href="#" class="btn btn-default mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#sync-modal"
              title="Sinkronisasi Data" data-bs-toggle="tooltip" data-bs-placement="top">
              <i class="ti ti-cloud-down fs-2 me-2"></i>
              Sinkronisasi
            </a>
          @endcan
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      {{-- Stats Cards Baru --}}
      <div class="row row-cards mb-4 mt-0">
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
                  <div class="text-secondary">Pendaftar</div>
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
                  <div class="h2 mb-0">{{ $pendaftaran->whereIn('status', ['success'])->count() }}</div>
                  <div class="text-secondary">Berhasil</div>
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
                  <span class="bg-info text-white avatar">
                    <i class="ti ti-cloud-network fs-1"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $pendaftaran->whereIn('status', ['synced'])->count() }}</div>
                  <div class="text-secondary">Tersinkron</div>
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
                  <span class="bg-light text-dark avatar">
                    <i class="ti ti-cloud-down fs-1"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="h2 mb-0">{{ $pendaftaran->whereIn('status', ['imported'])->count() }}</div>
                  <div class="text-secondary">Terimpor</div>
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

      {{-- <div class="card mb-4">
        <div class="card-body">
          <form method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
              <input type="text" name="cari" class="form-control"
                placeholder="Cari nama, email, ID calon mahasiswa..." value="{{ request('cari') }}">
            </div>
            <div class="col-md-4">
              <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
              </select>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-default w-100">
                <i class="ti ti-filter me-1"></i>
                Filter
              </button>
            </div>
          </form>
        </div>
      </div> --}}

      <div class="card">
        <div class="card">
          <div class="card-header">
            <div class="row w-full">
              <div class="col align-content-center">
                <h3 class="card-title">Daftar Calon Mahasiswa</h3>
              </div>
              <div class="col-md-auto col-sm-12">
                <div class="ms-auto d-flex flex-wrap btn-list">
                  <form method="GET" class="row g-3">
                    <div class="col">
                      <select name="status" class="form-select mt-2 mt-md-0">
                        <option value="">Semua Status</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="synced" {{ request('status') == 'synced' ? 'selected' : '' }}>Tersinkron</option>
                        <option value="imported" {{ request('status') == 'imported' ? 'selected' : '' }}>Terimpor
                        </option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                      </select>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          {{-- Tabel pendaftaran --}}
          <div class="table-responsive" style="padding: 1rem">
            <table id="pendaftaran-table" class="table table-vcenter table-bordered table-md table-hover">
              <thead>
                <tr>
                  <th class="w-1">No</th>
                  <th>Calon Mahasiswa</th>
                  <th>Program Studi</th>
                  <th>Akademik</th>
                  <th>Biaya</th>
                  <th>Status</th>
                  <th>Mitra Asal</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {{-- Data akan diisi oleh DataTables --}}
              </tbody>
            </table>
          </div>
          {{-- end --}}
        </div>
      </div>
    </div>
  </div>
@endsection


@section('modals')
  {{-- Modal Kredensial --}}
  <div class="modal modal-blur fade" id="credentials-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="ti ti-key fs-2 me-2 text-success"></i>
            Kredensial Login PMB SIAKAD2
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <div class="d-flex">
              <div>
                <i class="ti ti-info-circle fs-2 me-2"></i>
              </div>
              <div>
                <small>Berikan kredensial ini ke calon mahasiswa untuk login ke <a
                    href="{{ env('URL_SERVICE_SIAKAD') }}/login" target="_blank">PMB SIAKAD2</a></small>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
              <input type="text" class="form-control font-monospace" id="modal-username" readonly value="">
              <button class="btn btn-default" type="button" onclick="copyModalUsername()">
                <i class="ti ti-copy fs-3"></i>
              </button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" class="form-control font-monospace" id="modal-password" readonly value="">
              <button class="btn btn-default" type="button" onclick="toggleModalPassword()">
                <i class="ti ti-eye fs-3" id="modal-password-icon"></i>
              </button>
              <button class="btn btn-default" type="button" onclick="copyModalPassword()">
                <i class="ti ti-copy fs-3"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">
            Oke
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Sinkronisasi --}}
  <div class="modal modal-blur fade" id="sync-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="ti ti-refresh fs-2 me-2 text-blue"></i>
            Proses Sinkronisasi Data
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            {{-- <label for="tahun-sync" class="form-label">Tahun Akademik</label> --}}
            <h3>Tahun Akademik:</h3>
            <select name="tahun" id="tahun-sync" class="form-select">
              <option value="">- Pilih Tahun -</option>
              @for ($i = date('Y') + 1; $i >= 2020; $i--)
                <option value="{{ $i }}">{{ $i }}</option>
              @endfor
            </select>
          </div>
          <button type="button" class="btn btn-primary w-100 mb-3" id="start-sync-btn">Cek Sinkronisasi</button>
          <div id="sync-results-container" class="d-none">
            <h3>Hasil Pembandingan:</h3>
            <div id="sync-results"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success d-none" id="sync-all-btn">Sinkronisasi Semua</button>
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('style')
  <style>
    .avatar-text {
      font-weight: 600;
      text-transform: uppercase;
    }

    .font-monospace {
      font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    }

    /* Toast di tengah atas */
    .custom-toast {
      top: 20px;
      left: 50%;
      transform: translateX(-50%) translateY(-30px);
      opacity: 0;
      transition: all 0.4s ease;
      z-index: 1060;
    }

    /* Saat muncul */
    .custom-toast.showing {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
    }

    /* Saat hilang ke bawah */
    .custom-toast.hiding {
      transform: translateX(-50%) translateY(30px);
      opacity: 0;
    }

    /* Gaya untuk loading di DataTables */
    .dataTables_processing {
      margin-top: 4rem;
      /* background: rgba(255, 255, 255, 0.9); */
      /* position: absolute; */
      /* top: 50%; */
      /* left: 50%; */
      /* transform: translate(-50%, -50%); */
      /* padding: 50px; */
      /* border: 1px solid #ccc; */
      /* z-index: 9999; */
    }
  </style>

  {{-- DataTables CSS --}}
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endsection

@section('js_atas')
  {{-- kosong --}}
@endsection

@section('js_bawah')
  {{-- DEPENDENSI UNTUK PAGE DASBOR --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/apexcharts/dist/apexcharts.min.js"></script> --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/jsvectormap/dist/jsvectormap.min.js"></script> --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/jsvectormap/dist/maps/world.js"></script> --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/jsvectormap/dist/maps/world-merc.js"></script> --}}
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
  {{-- TAMBAHAN JS UNTUK PAGE DASBOR --}}
  {{-- @vite(['resources/js/pages/...']) --}}
  {{-- KOMPONEN INKLUD --}}

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Inisialisasi DataTables
      let table = $('#pendaftaran-table').DataTable({
        processing: true, // Tampilkan proses loading
        serverSide: true, // Aktifkan server-side processing
        responsive: false, // Aktifkan responsif
        stateSave: true,
        searchDelay: 500,
        ajax: {
          url: "{{ route('pendaftaran.data') }}", // Ganti dengan route yang akan kita buat untuk data
          type: 'GET',
          data: function(d) {
            // Kirim parameter filter ke server
            d.cari = $('input[name="cari"]').val();
            d.status = $('select[name="status"]').val();
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'pendaftaran_id',
            orderable: true,
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
            name: 'biaya',
            className: 'text-nowrap'
          },
          {
            data: 'status_badge',
            name: 'status'
          },
          {
            data: 'mitra_id',
            name: 'mitra_id'
          },
          {
            data: 'aksi',
            name: 'aksi',
            orderable: false,
            className: 'text-center'
          }
        ],
        columnDefs: [{
            targets: [6],
            orderable: true
          } // Kolom No dan Aksi tidak bisa diurutkan
        ],
        pageLength: 25,
        lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "Semua"]
        ], //jumlah data yang ditampilkan
        order: [
          [0, 'desc']
        ], // Urutkan berdasarkan kolom tahun (indeks 0) secara descending
        language: {
          url: '/data/datatables-id.json' // Bahasa Indonesia
        },
      });

      // Trigger ulang pencarian saat filter diubah
      $('input[name="cari"], select[name="status"]').on('keyup change', function() {
        table.draw();
      });

      // Delete confirmation (gunakan event delegation karena elemen di-load oleh DataTables)
      $(document).on('click', '.delete-btn', function() {
        const userName = $(this).data('name');
        const url = $(this).data('url');

        showDeleteConfirmation({}, `pendaftaran ${userName}`).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: url,
              type: 'POST',
              data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                '_method': 'DELETE'
              },
              success: function(response) {
                table.ajax.reload(); // Reload tabel setelah penghapusan
                // Gunakan Toast.fire untuk pesan sukses
                Swal.fire({
                  icon: 'success',
                  title: response.success || 'Pendaftaran berhasil dihapus.'
                });
              },
              error: function(xhr) {
                let msg = 'Gagal menghapus pendaftaran.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                  msg = xhr.responseJSON.message;
                }
                // Gunakan Toast.fire untuk pesan error
                Swal.fire({
                  icon: 'error',
                  title: msg
                });
              }
            });
          }
        });
      });
    });

    // Fungsi untuk menampilkan kredensial
    function showCredentials(username, password) {
      document.getElementById('modal-username').value = username;
      document.getElementById('modal-password').value = password;

      // Reset ke state awal
      document.getElementById('modal-password').type = 'password';
      document.getElementById('modal-password-icon').className = 'ti ti-eye fs-2';

      // Show modal menggunakan Bootstrap 5 (Tabler)
      const modalElement = document.getElementById('credentials-modal');
      modalElement.show();
    }

    // Toggle show/hide password di modal
    function toggleModalPassword() {
      const passwordField = document.getElementById('modal-password');
      const passwordIcon = document.getElementById('modal-password-icon');

      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.className = 'ti ti-eye-off fs-2';
      } else {
        passwordField.type = 'password';
        passwordIcon.className = 'ti ti-eye fs-2';
      }
    }

    // Copy username dari modal - GUNAKAN iziToast
    function copyModalUsername() {
      const field = document.getElementById('modal-username');
      field.select();
      field.setSelectionRange(0, 99999);

      navigator.clipboard.writeText(field.value).then(function() {
        showToast('Username berhasil disalin!', 'success');
      }).catch(function() {
        document.execCommand('copy');
        showToast('Username berhasil disalin!', 'success');
      });
    }

    // Copy password dari modal - GUNAKAN iziToast
    function copyModalPassword() {
      const field = document.getElementById('modal-password');
      field.select();
      field.setSelectionRange(0, 99999);

      navigator.clipboard.writeText(field.value).then(function() {
        showToast('Password berhasil disalin!', 'success');
      }).catch(function() {
        document.execCommand('copy');
        showToast('Password berhasil disalin!', 'success');
      });
    }

    // Fungsi untuk membuka modal sinkronisasi
    $('#sync-modal').on('shown.bs.modal', function() {
      // Reset form dan hasil sebelumnya
      $('#tahun-sync').val('');
      $('#sync-results-container').addClass('d-none');
      $('#sync-results').empty();
      $('#sync-all-btn').addClass('d-none');
    });

    // Trigger modal sinkronisasi
    $(document).on('click', 'a[href="#"]:contains("Sinkronisasi")', function(e) {
      e.preventDefault();
      const modal = bootstrap.Modal.getInstance(document.getElementById('sync-modal')) || new bootstrap.Modal(document
        .getElementById('sync-modal'));
      modal.show();
    });

    // Mulai proses sinkronisasi
    $('#start-sync-btn').on('click', function() {
      const tahun = $('#tahun-sync').val();
      if (!tahun) {
        Swal.fire({
          icon: 'warning',
          title: 'Tahun belum dipilih',
          text: 'Silakan pilih tahun akademik terlebih dahulu.'
        });
        return;
      }

      $(this).prop('disabled', true).text('Memproses...');

      $.ajax({
        url: "{{ route('pendaftaran.sync') }}",
        type: 'POST',
        data: {
          '_token': $('meta[name="csrf-token"]').attr('content'),
          'tahun': tahun
        },
        success: function(response) {
          if (response.success) {
            displaySyncResults(response.data);
            $('#sync-results-container').removeClass('d-none');
            $('#sync-all-btn').removeClass(
              'd-none'); // Tombol sinkronisasi semua bisa ditampilkan jika diperlukan
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: response.message || 'Terjadi kesalahan saat sinkronisasi.'
            });
          }
        },
        error: function(xhr) {
          let msg = 'Gagal menghubungi server.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: msg
          });
        },
        complete: function() {
          $('#start-sync-btn').prop('disabled', false).text('Cek Sinkronisasi');
        }
      });
    });

    // Fungsi untuk menampilkan hasil sinkronisasi
    function displaySyncResults(data) {
      const resultsDiv = $('#sync-results');
      resultsDiv.empty();

      if (data.length === 0) {
        resultsDiv.html('<p class="text-muted">Tidak ada data ditemukan untuk tahun dan mitra ini.</p>');
        return;
      }

      data.forEach(function(item) {
        let statusClass = '';
        let statusText = '';
        let actionBtn = '';

        switch (item.status) {
          case 'sudah_sama':
            statusClass = 'bg-success text-success-fg px-2';
            statusText = 'TERSINKRON';
            break;
          case 'butuh_synchronisasi':
            statusClass = 'bg-warning text-warning-fg px-2';
            statusText = 'BUTUH SINKRON';
            actionBtn =
              `<button class="btn btn-sm btn-primary sync-individual-btn" data-id="${item.id_calon_mahasiswa}">Sinkron</button>`;
            break;
          case 'baru_dari_api':
            statusClass = 'bg-info text-info-fg px-2';
            statusText = 'DATA BARU';
            actionBtn =
              `<button class="btn btn-sm btn-success sync-new-btn" data-id="${item.id_calon_mahasiswa}">Tambahkan</button>`; // Tambahkan tombol Tambahkan
            break;
          case 'hanya_di_lokal':
            statusClass = 'bg-secondary text-secondary-fg px-2';
            statusText = 'HANYA DI MITRA';
            // Tidak ada tombol sinkron untuk data lokal saja
            break;
          default:
            statusClass = 'bg-danger text-danger-fg px-2';
            statusText = 'STATUS TIDAK DIKENAL';
        }

        let fieldsDiff = '';
        if (item.field_berbeda.length > 0) {
          fieldsDiff = '<div class="text-muted mt-2"><strong>Field Berbeda:</strong> ';
          item.field_berbeda.forEach(function(diff) {
            fieldsDiff +=
              `<br><span class="text-wrap">- ${diff.field}: "${diff.local}" vs "${diff.api}"</span>`;
          });
          fieldsDiff += '</div>';
        }

        const cardHtml = `
                  <div class="card mb-2">
                      <div class="card-body d-flex justify-content-between align-items-center">
                          <div>
                              <div class="font-weight-bolder"><strong>${item.nama}</strong> (${item.id_calon_mahasiswa})</div>
                              <div class="text-muted">${item.keterangan}</div>
                              ${fieldsDiff}
                          </div>
                          <div class="d-flex align-items-center">
                              <span class="badge badge-pill ${statusClass} me-2">${statusText}</span>
                              ${actionBtn}
                          </div>
                      </div>
                  </div>
              `;
        resultsDiv.append(cardHtml);
      });
    }

    // Event delegation untuk tombol sinkronisasi individual
    $(document).on('click', '.sync-individual-btn', function() {
      const id = $(this).data('id');
      const btn = $(this);

      btn.prop('disabled', true).text('Menyinkronkan...');

      // Kirim permintaan ke route syncOne
      $.ajax({
        url: `/pendaftaran/sync/${id}`, // Sesuaikan dengan route Anda
        type: 'POST',
        data: {
          '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            // Refresh hasil atau update status card
            // Untuk sederhananya, kita reload modal atau hasilnya
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            }).then(() => {
              // Refresh DataTable jika perlu
              table.ajax.reload();
              // Dan refresh hasil sinkronisasi jika modal tetap terbuka
              $('#start-sync-btn').click(); // Trigger ulang sync
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: response.message || 'Gagal menyinkronkan data.'
            });
          }
        },
        error: function(xhr) {
          let msg = 'Gagal menyinkronkan data.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: msg
          });
        },
        complete: function() {
          btn.prop('disabled', false).text('Sinkron');
          $('#start-sync-btn').click(); // Trigger ulang sync
        }
      });
    });

    // Event delegation untuk tombol tambah data baru
    $(document).on('click', '.sync-new-btn', function() {
      const id = $(this).data('id');
      const btn = $(this);

      btn.prop('disabled', true).text('Menambahkan...');

      // Kirim permintaan ke route syncNew
      $.ajax({
        url: `/pendaftaran/sync-new/${id}`, // Sesuaikan dengan route Anda
        type: 'POST',
        data: {
          '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            }).then(() => {
              // Refresh DataTable jika perlu
              table.ajax.reload();
              // Dan refresh hasil sinkronisasi jika modal tetap terbuka
              $('#start-sync-btn').click(); // Trigger ulang sync
            });
          } else {
            // Periksa apakah error 409 (Conflict - sudah ada)
            if (response.status === 409) {
              Swal.fire({
                icon: 'info',
                title: 'Data Sudah Ada',
                text: response.message
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: response.message || 'Gagal menambahkan data baru.'
              });
            }
          }
        },
        error: function(xhr) {
          let msg = 'Gagal menambahkan data baru.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: msg
          });
        },
        complete: function() {
          btn.prop('disabled', false).text('Tambahkan');
          $('#start-sync-btn').click(); // Trigger ulang sync
        }
      });
    });

    // Event handler untuk tombol sinkronisasi semua
    $('#sync-all-btn').on('click', function() {
      const btn = $(this);
      // Ambil semua ID yang statusnya 'butuh_synchronisasi'
      const idsToSync = [];
      $('#sync-results .sync-individual-btn').each(function() {
        // Ambil ID dari tombol yang saat ini berlabel 'Sinkron' (artinya perlu disinkronkan)
        // Kita bisa mengandalkan struktur DOM atau menyimpan data secara terstruktur
        // Lebih aman jika kita ambil dari data yang ditampilkan di card
        const card = $(this).closest('.card');
        const statusBadge = card.find('.badge'); // Ambil elemen badge status
        if (statusBadge.hasClass(
            'bg-warning')) { // Jika kelasnya bg-warning, berarti statusnya 'butuh_synchronisasi'
          const id = $(this).data('id');
          idsToSync.push(id);
        }
      });

      if (idsToSync.length === 0) {
        Swal.fire({
          icon: 'info',
          title: 'Tidak Ada Data',
          text: 'Tidak ada data yang perlu disinkronkan.'
        });
        return;
      }

      // Konfirmasi sebelum proses massal
      Swal.fire({
        title: 'Konfirmasi',
        text: `Anda akan menyinkronkan ${idsToSync.length} data sekaligus. Lanjutkan?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Sinkronisasi!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          btn.prop('disabled', true).text('Memproses...');

          let successCount = 0;
          let errorCount = 0;
          let processedCount = 0;

          // Fungsi rekursif untuk memproses satu per satu secara serial
          const processNext = () => {
            if (idsToSync.length === 0) {
              // Selesai memproses semua
              Swal.fire({
                icon: 'success',
                title: 'Sinkronisasi Selesai',
                text: `Berhasil: ${successCount}, Gagal: ${errorCount}`
              }).then(() => {
                // Refresh tampilan
                table.ajax.reload();
                $('#start-sync-btn').click(); // Trigger ulang sync
              });
              btn.prop('disabled', false).text('Sinkronisasi Semua');
              $('#start-sync-btn').click(); // Trigger ulang sync
              return;
            }

            const id = idsToSync.shift(); // Ambil ID pertama dari array

            $.ajax({
              url: `/pendaftaran/sync/${id}`, // Gunakan route syncOne
              type: 'POST',
              data: {
                '_token': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                if (response.success) {
                  successCount++;
                } else {
                  errorCount++;
                  console.error('Gagal sync ID ' + id + ':', response.message);
                }
              },
              error: function(xhr) {
                errorCount++;
                console.error('Error AJAX sync ID ' + id + ':', xhr.responseJSON?.message ||
                  'Unknown error');
              },
              complete: function() {
                processedCount++;
                // Update progress jika diperlukan (opsional)
                // console.log(`Progress: ${processedCount}/${totalToProcess}`);

                // Proses item berikutnya
                processNext();
              }
            });
          };

          // Mulai proses
          processNext();
        }
      });
    });
  </script>
@endsection
