<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\SiakadService;
use App\Models\PendaftaranModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\PendaftaranStoreRequest;
use App\Http\Requests\PendaftaranUpdateRequest;

class PendaftaranController extends Controller
{
  protected $siakadService;

  public function __construct(SiakadService $siakadService)
  {
    $this->siakadService = $siakadService;
  }

  /**
   * Menyediakan data untuk DataTables
   */
  public function data(Request $request): JsonResponse
  {
    $query = PendaftaranModel::with(['user', 'mitra']);

    // Filter berdasarkan role user
    if (auth()->user()->hasRole('mitra')) {
      $query->where('mitra_id', auth()->id());
    }

    // Filter berdasarkan pencarian
    if ($request->has('cari') && $request->cari != '') {
      $search = $request->cari;
      $query->where(function ($q) use ($search) {
        $q->where('nama_lengkap', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%")
          ->orWhere('id_calon_mahasiswa', 'like', "%{$search}%")
          ->orWhere('no_transaksi', 'like', "%{$search}%");
      });
    }

    // Filter berdasarkan status
    if ($request->has('status') && $request->status != '') {
      $query->where('status', $request->status);
    }

    // Gunakan Yajra DataTables untuk mengolah query
    return DataTables::eloquent($query)
      ->addIndexColumn() // Kolom DT_RowIndex
      ->addColumn('calon_mahasiswa', function ($row) {
        $html = '<div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-3 bg-blue-lt">
                      <span class="avatar-text">' . substr($row->nama_lengkap, 0, 2) . '</span>
                  </div>
                  <div>
                      <div class="fw-bold">
                          <a href="' . route('pendaftaran.show', $row) . '" class="text-reset link-hover-underline">
                          ' . $row->nama_lengkap . '
                      </a>
                  </div>
                  <div class="text-muted small">
                      <i class="ti ti-mail me-1"></i>' . $row->email . '
                  </div>';
        if ($row->id_calon_mahasiswa) {
          $html .= '<div class="text-muted small">
                      <i class="ti ti-id me-1"></i>' . $row->id_calon_mahasiswa . '
                    </div>';
        }
        $html .= '</div></div>';
        return $html;
      })
      ->addColumn('prodi', function ($row) {
        return '<div class="font-weight-medium">S1-' . $row->prodi_nama . '</div>
                <div class="text-muted small">Kelas: ' . $row->nama_kelas . '</div>';
      })
      ->addColumn('akademik', function ($row) {
        return '<div class="font-weight-medium">' . $row->tahun . '/' . $row->tahun + 1  . '</div>
                  <div class="text-muted small">Gelombang ' . $row->gelombang  . '</div>
                <div class="text-muted small">' . $row->created_at->translatedFormat('d M Y') . '</div>';
      })
      ->addColumn('biaya', function ($row) {
        return '<div class="font-weight-medium">' . $row->biaya_formatted . '</div>';
      })
      ->addColumn('status_badge', function ($row) {
        return $row->status_badge;
      })
      ->addColumn('mitra_id', function ($row) {
        $asal = Str::limit($row->mitra->asal_sekolah, 15, '...');
        $mitra = Str::limit($row->mitra->name, 15, '...');
        return '<div class="font-weight-medium">' . e($mitra) . '</div>
            <div class="text-muted small">' . e($asal) . '</div>';
      })
      ->addColumn('aksi', function ($row) {
        $html = '<div class="btn-list justify-content-center">
                <a href="' . route('pendaftaran.show', $row) . '" class="btn btn-sm btn-default" title="Detail"
                    data-bs-toggle="tooltip" data-bs-placement="top">
                    <i class="ti ti-eye fs-3 me-1"></i>
                    Detail
                </a>';

        // Edit Button
        if (auth()->user()->can('pendaftaran_edit') && $row->status === 'pending') {
          $html .= '<a href="' . route('pendaftaran.edit', $row) . '" class="btn btn-sm btn-default"
                      title="Edit" data-bs-toggle="tooltip" data-bs-placement="top">
                      <i class="ti ti-edit fs-3"></i>
                    </a>';
        }

        // Credential Button
        if ($row->password_text && $row->username_siakad) {
          $html .= '<a href="#"
                      onclick="showCredentials(\'' . addslashes($row->username_siakad) . '\', \'' . addslashes($row->password_text) . '\')"
                      class="btn btn-sm btn-default text-success d-none d-sm-inline-block" data-bs-toggle="modal"
                      data-bs-target="#credentials-modal" title="Kredensial" data-bs-toggle="tooltip"
                      data-bs-placement="top">
                      <i class="ti ti-key fs-3"></i>
                    </a>';
        }

        // Delete Button
        if (auth()->user()->can('pendaftaran_delete')) {
          $html .= '<button type="button" class="btn btn-sm btn-default text-danger delete-btn" title="Hapus"
                        data-bs-toggle="tooltip" data-bs-placement="top" data-name="' . addslashes($row->nama_lengkap) . '"
                        data-url="' . route('pendaftaran.destroy', $row) . '">
                        <i class="ti ti-trash fs-3"></i>
                    </button>';
        }

        $html .= '</div>';
        return $html;
      })
      ->rawColumns(['calon_mahasiswa', 'prodi', 'akademik', 'biaya', 'status_badge', 'mitra_id', 'aksi']) // Kolom yang berisi HTML
      ->make(true);
  }

  /**
   * Menampilkan daftar pendaftaran
   */
  public function index(Request $request): View
  {
    $query = PendaftaranModel::with(['user', 'mitra']);

    // Filter berdasarkan role user
    if (auth()->user()->hasRole('mitra')) {
      $query->where('mitra_id', auth()->id());
    }

    // Filter berdasarkan pencarian
    if ($request->has('cari') && $request->cari != '') {
      $search = $request->cari;
      $query->where(function ($q) use ($search) {
        $q->where('nama_lengkap', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%")
          ->orWhere('id_calon_mahasiswa', 'like', "%{$search}%")
          ->orWhere('no_transaksi', 'like', "%{$search}%");
      });
    }

    // Filter berdasarkan status
    if ($request->has('status') && $request->status != '') {
      $query->where('status', $request->status);
    }

    // $pendaftaran = $query->latest()->paginate(15);
    $pendaftaran = $query->orderByDesc('created_at')->get();

    return view('sistem.pendaftaran.index', [
      'title' => 'Manajemen Pendaftaran',
      'pendaftaran' => $pendaftaran,
    ]);
  }

  /**
   * Menampilkan form buat pendaftaran
   */
  public function create(): View
  {
    // Ambil info prodi dan jadwal dari PMB SIAKAD2
    $infoPendaftaran = $this->siakadService->getInfoPendaftaran();
    $kelasList = PendaftaranModel::daftarKelas();

    if (!$infoPendaftaran['success']) {
      return view('sistem.pendaftaran.create', [
        'title' => 'Buat Pendaftaran',
        'error' => $infoPendaftaran['message'] ?? 'Gagal mengambil data dari PMB SIAKAD2',
        'prodi' => [],
        'jadwal' => null,
      ]);
    }

    return view('sistem.pendaftaran.create', [
      'title' => 'Buat Pendaftaran',
      'prodi' => $infoPendaftaran['data']['prodi'] ?? [],
      'jadwal' => $infoPendaftaran['data']['jadwal'] ?? null,
      'kelasList' => $kelasList
    ]);
  }

  /**
   * Menyimpan pendaftaran baru
   */
  public function store(PendaftaranStoreRequest $request): RedirectResponse
  {
    try {
      // Cek ketersediaan jadwal
      $infoPendaftaran = $this->siakadService->getInfoPendaftaran();
      if (!$infoPendaftaran['success'] || !isset($infoPendaftaran['data']['jadwal'])) {
        return back()->withInput()
          ->with('error', 'Jadwal pendaftaran tidak tersedia. Silakan coba lagi nanti.');
      }

      $jadwal = $infoPendaftaran['data']['jadwal'];
      $prodi = $infoPendaftaran['data']['prodi'][$request->prodi_id] ?? '';
      if ($jadwal['GELOMBANG'] == 1) {
        // setiap gelombang pertama (1) akan mendapatkan diskon
        $biaya_daftar = $jadwal['BIAYA'] - 100000;
      } else {
        $biaya_daftar = $jadwal['BIAYA'];
      }

      // Data untuk dikirim ke PMB SIAKAD2
      $dataSiakad = [
        'prodi_id' => $request->prodi_id,
        'tahun' => $jadwal['TAHUN'],
        'gelombang' => $jadwal['GELOMBANG'],
        'biaya' => $biaya_daftar,
        'kelas' => $request->kelas,
        'nama' => $request->nama_lengkap,
        'email' => $request->email,
        'hp' => $request->nomor_hp,
        'hp2' => $request->nomor_hp2,
        'password' => $request->password,
        'mitra_id' => auth()->user()->username,
        'mitra_nama' => auth()->user()->name,
        'mitra_asalsma' => auth()->user()->asal_sekolah,
      ];

      // Kirim ke PMB SIAKAD2
      $response = $this->siakadService->registerCalonMahasiswa($dataSiakad);

      if (!$response['success']) {
        // Simpan sebagai pendaftaran gagal - TAMBAHKAN password_text
        $pendaftaran = PendaftaranModel::create([
          'user_id' => null, // Belum ada user
          'mitra_id' => auth()->id(),
          'id_calon_mahasiswa' => '',
          'username_siakad' => '',
          'no_transaksi' => '',
          'prodi_id' => $request->prodi_id,
          'prodi_nama' => $prodi,
          'tahun' => $jadwal['TAHUN'],
          'gelombang' => $jadwal['GELOMBANG'],
          'biaya' => $jadwal['BIAYA'],
          'kelas' => $request->kelas,
          'nama_lengkap' => $request->nama_lengkap,
          'email' => $request->email,
          'nomor_hp' => $request->nomor_hp,
          'nomor_hp2' => $request->nomor_hp2,
          'password_text' => $request->password, // SIMPAN PASSWORD PLAIN TEXT
          'status' => 'failed',
          'keterangan' => $response['message'] ?? 'Gagal terhubung ke PMB SIAKAD2',
          'response_data' => $response,
        ]);

        // return back()->withInput()->with('error', 'Pendaftaran gagal: ' . ($response['message'] ?? 'Terjadi kesalahan'));
        return redirect()->route('pendaftaran.index')->with('error', 'Proses pendaftaran gagal: ' . ($response['message'] ?? 'Terjadi kesalahan fatal'));
      }

      if ($jadwal['GELOMBANG'] == 1) {
        $pesan = "Diskon formulir pendaftaran berhasil diterapkan sebesar Rp 100.000.";
      } else {
        $pesan = ".";
      }

      // Simpan sebagai pendaftaran berhasil - TAMBAHKAN password_text
      $pendaftaran = PendaftaranModel::create([
        'user_id' => null, // Opsional: bisa dibuat user lokal nanti untuk mhs ybs jika diperlukan
        'mitra_id' => auth()->id(),
        'id_calon_mahasiswa' => $response['data']['id_calon_mahasiswa'],
        'username_siakad' => $response['data']['username'],
        'no_transaksi' => $response['data']['no_transaksi'],
        'prodi_id' => $request->prodi_id,
        'prodi_nama' => $prodi,
        'tahun' => $jadwal['TAHUN'],
        'gelombang' => $jadwal['GELOMBANG'],
        'biaya' => $response['data']['total_biaya'],
        'kelas' => $request->kelas,
        'nama_lengkap' => $response['data']['nama'],
        'email' => $response['data']['email'],
        'nomor_hp' => $request->nomor_hp,
        'nomor_hp2' => $request->nomor_hp2,
        'password_text' => $request->password, // SIMPAN PASSWORD PLAIN TEXT
        'status' => 'success',
        'keterangan' => 'Pendaftaran berhasil via ' . konfigs('NAMA_SISTEM') . $pesan,
        'response_data' => $response,
        'synced_at' => now(),
      ]);

      return redirect()->route('pendaftaran.index')
        ->with('success', 'Pendaftaran ' . $pendaftaran->nama_lengkap . ' berhasil dibuat.');
    } catch (\Exception $e) {
      return back()->withInput()
        ->with('error', 'Gagal menyimpan pendaftaran: ' . $e->getMessage());
    }
  }

  /**
   * Menampilkan detail pendaftaran
   */
  public function show(PendaftaranModel $pendaftaran): View
  {
    // Authorization check
    if (auth()->user()->hasRole('mitra') && $pendaftaran->mitra_id != auth()->id()) {
      abort(403);
    }

    return view('sistem.pendaftaran.show', [
      'title' => 'Detail Pendaftaran - ' . $pendaftaran->nama_lengkap,
      'pendaftaran' => $pendaftaran,
    ]);
  }

  /**
   * Menampilkan form edit pendaftaran
   */
  public function edit(PendaftaranModel $pendaftaran): View
  {
    // Authorization check
    if (auth()->user()->hasRole('mitra') && $pendaftaran->mitra_id != auth()->id()) {
      abort(403);
    }

    // Hanya bisa edit pendaftaran yang pending
    if ($pendaftaran->status !== 'pending') {
      abort(403, 'Hanya pendaftaran dengan status pending yang dapat diedit');
    }

    $infoPendaftaran = $this->siakadService->getInfoPendaftaran();
    $prodi = $infoPendaftaran['data']['prodi'] ?? [];
    $kelas = PendaftaranModel::daftarKelas();

    return view('sistem.pendaftaran.edit', [
      'title' => 'Edit Pendaftaran - ' . $pendaftaran->nama_lengkap,
      'pendaftaran' => $pendaftaran,
      'prodi' => $prodi,
      'kelas' => $kelas,
    ]);
  }

  /**
   * Update data pendaftaran
   */
  public function update(PendaftaranUpdateRequest $request, PendaftaranModel $pendaftaran): RedirectResponse
  {
    // Authorization check
    if (auth()->user()->hasRole('mitra') && $pendaftaran->mitra_id != auth()->id()) {
      abort(403);
    }

    try {
      $pendaftaran->update([
        'prodi_id' => $request->prodi_id,
        'kelas' => $request->kelas,
        'nama_lengkap' => $request->nama_lengkap,
        'email' => $request->email,
        'nomor_hp' => $request->nomor_hp,
        'nomor_hp2' => $request->nomor_hp2,
      ]);

      return redirect()->route('pendaftaran.index')
        ->with('success', 'Data pendaftaran ' . $pendaftaran->nama_lengkap . ' berhasil diperbarui.');
    } catch (Exception $e) {
      return back()->withInput()
        ->with('error', 'Gagal memperbarui pendaftaran: ' . $e->getMessage());
    }
  }

  /**
   * Proses sinkronisasi data pendaftaran dari PMB SIAKAD2
   */
  public function sync(Request $request): JsonResponse
  {
    try {
      $user = auth()->user();
      $tahun = $request->input('tahun'); // Ambil tahun dari request
      $mitraId = $user->username; // Gunakan username sebagai referensi mitra

      if (!$tahun) {
        return response()->json([
          'success' => false,
          'message' => 'Tahun wajib diisi.'
        ], 400);
      }

      // 1. Ambil data terbaru dari PMB SIAKAD2 untuk mitra ini
      if ($user->hasRole('mitra')) {
        $apiResponse = $this->siakadService->getCalonMahasiswaByMitra($tahun, $mitraId);
      } elseif ($user->hasRole('superadmin')) {
        $apiResponse = $this->siakadService->getCalonMahasiswaAll($tahun);
      }

      if (!$apiResponse['success']) {
        return response()->json([
          'success' => false,
          'message' => $apiResponse['message'] ?? 'Gagal mengambil data dari SIAKAD2.'
        ], 500);
      }

      $apiData = collect($apiResponse['data'] ?? []);

      if ($user->hasRole('mitra')) {
        // 2. Ambil data lokal dari tabel pendaftaran untuk mitra ini dan tahun yang sama
        $localData = PendaftaranModel::where('mitra_id', $user->user_id)
          ->where('tahun', $tahun)
          // ->orderBy('created_at', 'desc')
          ->get()
          ->keyBy('id_calon_mahasiswa'); // Index by ID calon mahasiswa untuk pencarian cepat
      } else {
        // 2. Ambil data lokal dari tabel pendaftaran. Jika bukan mitra (misalnya admin), cari saja tanpa batasan mitra
        $localData = PendaftaranModel::where('tahun', $tahun)
          // ->orderBy('created_at', 'desc')
          ->get()
          ->keyBy('id_calon_mahasiswa'); // Index by ID calon mahasiswa untuk pencarian cepat
      }


      $results = [];

      // 3. Loop data dari API dan bandingkan dengan lokal
      foreach ($apiData as $apiRecord) {
        $idCalonMhs = $apiRecord['id_calon_mahasiswa'];
        $localRecord = $localData->get($idCalonMhs);

        $status = 'unknown';
        $keterangan = '';
        $field_berbeda = [];
        $data_baru = null;

        if (!$localRecord) {
          // Data baru dari API, tidak ada di lokal
          $status = 'baru_dari_api';
          $keterangan = 'Data baru dari PMB SIAKAD2.';
        } else {
          // Data ditemukan di lokal, bandingkan
          $isMatch = true;
          $fieldsToCompare = [
            'nama_lengkap' => 'nama',
            'email' => 'email',
            'nomor_hp' => 'nomor_hp',
            'nomor_hp2' => 'nomor_hp2',
            'prodi_id' => 'prodi_id',
            'prodi_nama' => 'prodi_nama',
            'tahun' => 'tahun',
            'gelombang' => 'gelombang',
            'kelas' => 'kelas',
            'no_transaksi' => 'transaksi_pendaftaran', // Nomor transaksi
            'biaya' => 'total_pembayaran_pendaftaran', // Bandingkan dengan biaya pembayaran
            // 'status' => 'registrasi_ulang_lunas', // Bandingkan status utama
            // Tambahkan field lain yang ingin dibandingkan
          ];

          foreach ($fieldsToCompare as $localField => $apiField) {
            $localValue = $localRecord->$localField;
            $apiValue = $apiRecord[$apiField] ?? null;

            // Konversi tipe data jika perlu sebelum dibandingkan
            if ($localField === 'biaya') {
              $apiValue = (float) $apiRecord['transaksi_pendaftaran']['total_biaya'] ?? 0;
              $localValue = (float) $localValue;
            }
            if ($apiField === 'transaksi_pendaftaran') {
              $apiValue = (float) $apiRecord['transaksi_pendaftaran']['no_transaksi'] ?? 0;
              $localValue = (float) $localValue;
            }
            if ($localField === 'tahun' || $localField === 'gelombang') {
              $apiValue = (int) ($apiValue ?? 0);
              $localValue = (int) $localValue;
            }

            if ($localValue != $apiValue) {
              $isMatch = false;
              $field_berbeda[] = [
                'field' => $localField,
                'local' => $localValue,
                'api' => $apiValue
              ];
            }
          }

          if ($isMatch) {
            $status = 'sudah_sama';
            $keterangan = 'Data ' . konfigs('NAMA_SISTEM_ALIAS') . ' sudah sinkron dengan PMB SIAKAD2.';
          } else {
            $status = 'butuh_synchronisasi';
            $keterangan = 'Data ' . konfigs('NAMA_SISTEM_ALIAS') . ' berbeda dengan PMB SIAKAD2, perlu disinkronkan.';
            $data_baru = $apiRecord; // Kirim data baru untuk digunakan saat sinkronisasi
          }
        }

        $results[] = [
          'id_calon_mahasiswa' => $idCalonMhs,
          'nama' => $apiRecord['nama'],
          'email' => $apiRecord['email'],
          'status' => $status,
          'keterangan' => $keterangan,
          'field_berbeda' => $field_berbeda,
          'data_baru' => $data_baru,
          'data_lokal' => $localRecord ? $localRecord->toArray() : null,
        ];
      }

      // 4. Tambahkan data lokal yang tidak ada di API (opsional, tergantung kebijakan)
      foreach ($localData as $localRecord) {
        if (!$apiData->contains('id_calon_mahasiswa', $localRecord->id_calon_mahasiswa)) {
          $results[] = [
            'id_calon_mahasiswa' => $localRecord->id_calon_mahasiswa,
            'nama' => $localRecord->nama_lengkap,
            'email' => $localRecord->email,
            'status' => 'hanya_di_lokal',
            'keterangan' => 'Data hanya ada di ' . konfigs('NAMA_SISTEM_ALIAS') . ', tidak ditemukan di PMB SIAKAD2.',
            'field_berbeda' => [],
            'data_baru' => null,
            'data_lokal' => $localRecord->toArray(),
          ];
        }
      }

      return response()->json([
        'success' => true,
        'message' => 'Memproses data sinkronisasi.',
        'data' => $results
      ]);
    } catch (Exception $e) {
      Log::error('Sync Error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Sinkronisasi satu data pendaftaran dari PMB SIAKAD2 berdasarkan id_calon_mahasiswa
   */
  public function syncOne(Request $request, string $id_calon_mahasiswa): JsonResponse
  {
    try {
      // Authorization check: Pastikan user adalah mitra
      $user = auth()->user();
      if ($user->hasRole('mitra')) {
        // Jika role adalah mitra, pastikan pendaftaran milik mereka
        $pendaftaran = PendaftaranModel::where('id_calon_mahasiswa', $id_calon_mahasiswa)
          ->where('mitra_id', $user->user_id) // Pastikan milik mitra ini
          ->first();
      } else {
        // Jika bukan mitra (misalnya admin), cari saja tanpa batasan mitra
        $pendaftaran = PendaftaranModel::where('id_calon_mahasiswa', $id_calon_mahasiswa)->first();
      }

      // Periksa apakah record ditemukan
      if (!$pendaftaran) {
        return response()->json([
          'success' => false,
          'message' => 'Data pendaftaran tidak ditemukan.'
        ], 404);
      }

      // Ambil data terbaru dari PMB SIAKAD2 untuk ID calon mhs ini
      $apiResponse = $this->siakadService->getDetailCalonMahasiswa($id_calon_mahasiswa);

      if (!$apiResponse['success']) {
        return response()->json([
          'success' => false,
          'message' => $apiResponse['message'] ?? 'Gagal mengambil data dari SIAKAD2.'
        ], 500);
      }

      $apiData = $apiResponse['data'];

      // Lakukan update ke record lokal
      // Perhatikan: mapping field dari API ke kolom database mungkin perlu disesuaikan
      $pendaftaran->update([
        'nama_lengkap' => $apiData['nama'],
        'email' => $apiData['email'],
        'nomor_hp' => $apiData['user']['nomor_hp'],
        'nomor_hp2' => $apiData['user']['nomor_hp2'] ?? null,
        'prodi_id' => $apiData['prodi_id'],
        'prodi_nama' => $apiData['prodi_nama'],
        'tahun' => $apiData['tahun'],
        'gelombang' => $apiData['gelombang'],
        'kelas' => $apiData['kelas'],
        'no_transaksi' => $apiData['transaksi_pendaftaran']['no_transaksi'], // Contoh, sesuaikan
        'biaya' => $apiData['transaksi_pendaftaran']['total_biaya'], // Contoh, sesuaikan
        'status' => 'synced', // Gunakan fungsi mapping
        // 'status' => $this->mapStatusApiToLokal($apiData['status_calon_mahasiswa']), // Gunakan fungsi mapping
        // 'keterangan' => 'Hasil sinkronisasi dari SIAKAD2',
        // 'response_data' => $apiData, // Simpan seluruh data dari API
        'synced_at' => now(),
        // Tambahkan field lain yang ingin disinkronkan berdasarkan struktur $apiData
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Data pendaftaran berhasil disinkronisasi.',
        'data' => $pendaftaran
      ]);
    } catch (\Exception $e) {
      \Log::error('Sync One Error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Menambahkan satu data pendaftaran baru dari PMB SIAKAD2 berdasarkan id_calon_mahasiswa
   */
  public function syncNew(Request $request, string $id_calon_mahasiswa): JsonResponse
  {
    try {
      // Authorization check: Pastikan user adalah mitra
      $user = auth()->user();
      if ($user->hasRole('mitra')) {
        // Untuk menambahkan data baru, kita hanya perlu memastikan user login sebagai mitra
        // Kita tidak mencari data lokal dulu karena memang belum ada
      } else {
        // Jika bukan mitra, mungkin bisa ditolak atau diberi akses sesuai kebijakan
        // Untuk saat ini, kita tetap lanjutkan untuk admin jika bukan mitra
      }

      // Cek apakah data dengan ID ini sudah ada di tabel lokal (mencegah duplikat jika proses sinkronisasi diulang)
      $existingPendaftaran = PendaftaranModel::where('id_calon_mahasiswa', $id_calon_mahasiswa)->first();
      if ($existingPendaftaran) {
        return response()->json([
          'success' => false,
          'message' => 'Data pendaftaran sudah ada di sistem.'
        ], 409); // Conflict: Sudah ada
      }

      // Ambil data terbaru dari PMB SIAKAD2 untuk ID calon mhs ini
      $apiResponse = $this->siakadService->getDetailCalonMahasiswa($id_calon_mahasiswa);

      if (!$apiResponse['success']) {
        return response()->json([
          'success' => false,
          'message' => $apiResponse['message'] ?? 'Gagal mengambil data dari SIAKAD2.'
        ], 500);
      }

      $apiData = $apiResponse['data'];
      $synctouser = User::where('username', $apiData['referensi'])->first(); // Index by ID calon mahasiswa untuk pencarian cepat

      // Buat record baru di tabel pendaftaran berdasarkan data dari API
      // Catatan: Ini hanya contoh mapping. Anda mungkin perlu menyesuaikan field-fieldnya.
      $pendaftaran = PendaftaranModel::create([
        'user_id' => null, // Karena ini hanya dari API, mungkin tidak ada user lokal
        'mitra_id' => $synctouser->user_id, // Asumsikan data baru ini ditambahkan oleh mitra yang sedang login
        'id_calon_mahasiswa' => $apiData['id_calon_mahasiswa'],
        'username_siakad' => $apiData['user']['username'] ?? null, // Ambil dari data user di API
        'password_text' => null, // Tidak menyimpan password plain dari API
        'no_transaksi' => $apiData['transaksi_pendaftaran']['no_transaksi'] ?? null, // Ambil dari data transaksi di API
        'prodi_id' => $apiData['prodi_id'],
        'prodi_nama' => $apiData['prodi_nama'],
        'tahun' => $apiData['tahun'],
        'gelombang' => $apiData['gelombang'],
        'biaya' => $apiData['transaksi_pendaftaran']['total_biaya'] ?? 0, // Ambil dari data transaksi di API
        'kelas' => $apiData['kelas'],
        'nama_lengkap' => $apiData['nama'],
        'email' => $apiData['email'],
        'nomor_hp' => $apiData['user']['nomor_hp'] ?? null, // Ambil dari data user di API
        'nomor_hp2' => $apiData['user']['nomor_hp2'] ?? null, // Ambil dari data user di API
        'status' => 'imported', // Gunakan fungsi mapping
        'keterangan' => 'Data baru dari SIAKAD2 via sinkronisasi',
        'response_data' => $apiData, // Simpan seluruh data dari API
        'synced_at' => now(),
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Data pendaftaran baru berhasil ditambahkan.',
        'data' => $pendaftaran
      ]);
    } catch (\Exception $e) {
      \Log::error('Sync New Error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menambahkan data baru: ' . $e->getMessage()
      ], 500);
    }
  }

  // Fungsi bantu untuk mapping status dari API ke status lokal
  private function mapStatusApiToLokal($apiStatus)
  {
    // Contoh mapping, sesuaikan dengan logika bisnis Anda
    // API: 0=Belum, 1=Lulus Ujian, 2=Lulus Seleksi, dll (lihat contoh API sebelumnya)
    // Lokal: 'pending', 'success', 'failed'
    switch ($apiStatus) {
      case 0:
        return 'pending'; // Misalnya, 0 berarti belum lulus ujian/seleksi
      case 1:
      case 2: // Misalnya 1 dan 2 berarti lulus ujian/seleksi
        return 'success'; // Dianggap berhasil
      default:
        return 'pending'; // Default jika status API tidak dikenal atau null
    }
  }

  /**
   * Hapus pendaftaran
   */
  public function destroy(PendaftaranModel $pendaftaran): RedirectResponse
  {
    // Authorization check
    if (auth()->user()->hasRole('mitra') && $pendaftaran->mitra_id != auth()->id()) {
      abort(403);
    }

    try {
      $nama = $pendaftaran->nama_lengkap;
      $pendaftaran->delete();

      return redirect()->route('pendaftaran.index')
        ->with('success', 'Pendaftaran ' . $nama . ' berhasil dihapus.');
    } catch (Exception $e) {
      return back()->with('error', 'Gagal menghapus pendaftaran: ' . $e->getMessage());
    }
  }
}
