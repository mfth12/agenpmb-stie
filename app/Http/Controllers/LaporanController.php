<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PendaftaranModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
  /**
   * Menampilkan halaman indeks laporan dengan widget dan filter
   */
  public function index(Request $request): View
  {
    // Filter berdasarkan role user
    $filterMitra = [];
    $mitra = [];
    $query = PendaftaranModel::with(['user', 'mitra']);

    if (auth()->user()->hasRole('mitra')) {
      $filterMitra['mitra_id'] = auth()->id();
      // Ambil 1 data user mitra yang sedang login (format sebagai collection)
      $mitra = User::select('user_id', 'name', 'asal_sekolah')
        ->where('user_id', auth()->id())
        ->get();
      // Ambil hanya data query pendaftaran milik mitra
      $query->where('mitra_id', auth()->id());
    } else {
      // Ambil daftar mitra untuk dropdown (khusus UI admin)
      $mitra = User::role(['superadmin', 'mitra', 'baak'])
        ->select('user_id', 'name', 'asal_sekolah')
        ->get();
    }

    $pendaftaran = $query
      ->where('tahun', konfigs('DEFAULT_TA'))
      ->orderByDesc('created_at')
      ->get();

    // Widget 1: Jumlah Calon Mahasiswa Terdaftar per Bulan (12 bulan terakhir)
    $pendaftaranPerBulan = PendaftaranModel::select(
      DB::raw('DATE_FORMAT(created_at, "%M %Y") as bulan_tahun'),
      DB::raw('COUNT(*) as jumlah')
    )
      ->where($filterMitra)
      ->where('tahun', konfigs('DEFAULT_TA'))
      ->groupBy('bulan_tahun')
      ->orderBy('bulan_tahun', 'DESC')
      ->get();

    // Widget 2: Distribusi Status
    $distribusiStatus = PendaftaranModel::select(
      'status',
      DB::raw('COUNT(*) as jumlah')
    )
      ->where($filterMitra)
      ->where('tahun', konfigs('DEFAULT_TA'))
      ->groupBy('status')
      ->get();

    // Widget 3: Distribusi Prodi
    $distribusiProdi = PendaftaranModel::select(
      'prodi_id',
      'prodi_nama',
      DB::raw('COUNT(*) as jumlah')
    )
      ->where($filterMitra)
      ->where('tahun', konfigs('DEFAULT_TA'))
      ->groupBy('prodi_id', 'prodi_nama')
      ->get();

    // Widget 4: Distribusi Gelombang
    $distribusiGel = PendaftaranModel::select(
      'gelombang',
      DB::raw('COUNT(*) as jumlah')
    )
      ->where($filterMitra)
      ->where('tahun', konfigs('DEFAULT_TA'))
      ->groupBy('gelombang')
      ->get();

    return view('sistem.laporan.index', [
      'title'               => 'Statistik & Laporan ' . konfigs('DEFAULT_TA') . '/' . konfigs('DEFAULT_TA') + 1,
      'pendaftaranPerBulan' => $pendaftaranPerBulan,
      'distribusiStatus'    => $distribusiStatus,
      'distribusiProdi'     => $distribusiProdi,
      'distribusiGel'       => $distribusiGel,
      'pendaftaran'         => $pendaftaran,
      'mitra'               => $mitra,
    ]);
  }


  /**
   * Menyediakan data untuk DataTables
   */
  public function data(Request $request): JsonResponse
  {
    $query = PendaftaranModel::with(['user', 'mitra']); // Eager load relasi

    // Filter berdasarkan role user
    if (auth()->user()->hasRole('mitra')) {
      $query->where('mitra_id', auth()->id());
    }

    // Filter berdasarkan pencarian global (search di nama, email, ID calon mhs, no transaksi)
    if ($request->has('search') && $request->search['value'] != '') {
      $searchValue = $request->search['value'];
      $query->where(function ($q) use ($searchValue) {
        $q->where('nama_lengkap', 'like', "%{$searchValue}%")
          ->orWhere('id_calon_mahasiswa', 'like', "%{$searchValue}%")
          ->orWhere('username_siakad', 'like', "%{$searchValue}%")
          ->orWhere('prodi_nama', 'like', "%{$searchValue}%")
          ->orWhere('tahun', 'like', "%{$searchValue}%")
          ->orWhere('gelombang', 'like', "%{$searchValue}%")
          ->orWhere('biaya', 'like', "%{$searchValue}%")
          ->orWhere('email', 'like', "%{$searchValue}%")
          ->orWhere('nomor_hp', 'like', "%{$searchValue}%")
          ->orWhere('nomor_hp2', 'like', "%{$searchValue}%")
          ->orWhereHas('mitra', function ($q_mitra) use ($searchValue) {
            $q_mitra->where('name', 'like', "%{$searchValue}%")
              ->orWhere('asal_sekolah', 'like', "%{$searchValue}%");
          });
      });
    }

    // Filter berdasarkan kolom (bisa digunakan oleh DataTables jika kolom diaktifkan)
    // Filter Mitra (dari dropdown filter di atas tabel)
    if ($request->has('mitra_filter') && $request->mitra_filter != '') {
      $query->where('mitra_id', $request->mitra_filter);
    }

    // Filter Tahun
    if ($request->has('tahun_filter') && $request->tahun_filter != '') {
      $query->where('tahun', $request->tahun_filter);
    }

    // Filter Gelombang
    if ($request->has('gelombang_filter') && $request->gelombang_filter != '') {
      $query->where('gelombang', $request->gelombang_filter);
    }

    // Filter Prodi
    if ($request->has('prodi_filter') && $request->prodi_filter != '') {
      $query->where('prodi_id', $request->prodi_filter);
    }

    // Filter Status
    if ($request->has('status_filter') && $request->status_filter != '') {
      $query->where('status', $request->status_filter);
    }

    return DataTables::eloquent($query)
      ->addIndexColumn() // Kolom DT_RowIndex
      ->addColumn('calon_mahasiswa', function ($row) {
        $html = '<div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-3 bg-primary-lt">
                    <span class="avatar-text">' . substr($row->nama_lengkap, 0, 2) . '</span>
                  </div>
                  <div>
                    <div class="fw-bold">
                      <a href="' . route('pendaftaran.show', $row) . '" class="text-reset link-hover-underline">
                        ' . $row->nama_lengkap . '
                      </a>
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
        return '<div class="font-weight-medium">' . $row->tahun . '/' . $row->gelombang . '</div>
                <div class="text-muted small">' . $row->created_at->translatedFormat('d M Y') . '</div>';
      })
      ->addColumn('mitra_name', function ($row) {
        return '<div class="font-weight-medium">' . e($row->mitra->name ?? '-') . '</div>';
      })
      ->addColumn('asal_sekolah', function ($row) {
        return '<div class="font-weight-medium">' . $row->mitra->asal_sekolah . '</div>';
      })
      ->addColumn('aksi', function ($row) {
        // Opsi cetak PDF untuk satu data (opsional)
        $html = '<a href="#" onclick="cetakPdfSatu(\'' . $row->id_calon_mahasiswa . '\')" class="btn btn-sm btn-default text-primary" title="Cetak PDF"
                  data-bs-toggle="tooltip" data-bs-placement="top">
                  <i class="ti ti-printer fs-3 me-1"></i>
                  Cetak
                </a>';

        $html .= '</div>';
        return $html;
      })
      ->rawColumns(['calon_mahasiswa', 'prodi', 'akademik', 'mitra_name', 'asal_sekolah', 'aksi']) // Kolom yang berisi HTML
      ->make(true);
  }

  /**
   * Generate PDF dari data yang difilter
   */
  public function generatePdf(Request $request)
  {
    $query = PendaftaranModel::with(['user', 'mitra'])->orderBy('created_at', 'DESC'); // Eager load relasi

    // Filter berdasarkan role user (jika role mitra)
    if (auth()->user()->hasRole('mitra')) {
      $query->where('mitra_id', auth()->id());
    }

    // Terapkan filter dari request (misalnya dari form filter di index)
    // Filter Mitra
    if ($request->has('mitra_filter') && $request->mitra_filter != '') {
      $query->where('mitra_id', $request->mitra_filter);
    }

    // Filter Tahun
    if ($request->has('tahun_filter') && $request->tahun_filter != '') {
      $query->where('tahun', $request->tahun_filter);
    }

    // Filter Gelombang
    if ($request->has('gelombang_filter') && $request->gelombang_filter != '') {
      $query->where('gelombang', $request->gelombang_filter);
    }

    // Filter Prodi
    if ($request->has('prodi_filter') && $request->prodi_filter != '') {
      $query->where('prodi_id', $request->prodi_filter);
    }

    // Filter Status
    if ($request->has('status_filter') && $request->status_filter != '') {
      $query->where('status', $request->status_filter);
    }

    $data = $query->get(); // Ambil semua data yang difilter

    // Filter Cetak/Print
    if ($request->has('cetak') && $request->boolean('cetak')) {
      // dd($request->cetak);
      // $query->where('status', $request->status_filter);
      return view('sistem.laporan.pdf', [
        'title' => 'Laporan Pendaftaran',
        'data'  => $data,
        'filters'     => [
          'mitra'     => User::find($request->mitra_filter)?->name ?? 'Semua',
          'tahun'     => $request->tahun_filter ?? 'Semua',
          'gelombang' => $request->gelombang_filter ?? 'Semua',
          'prodi'     => PendaftaranModel::daftarProdiAktif()[$request->prodi_filter] ?? 'Semua',
          'status'    => $request->status_filter ?? 'Semua',
        ]
      ]);
    } else {
      $pdf = Pdf::loadView('sistem.laporan.pdf', [
        'title' => 'Laporan Pendaftaran',
        'data'  => $data,
        'filters'     => [
          'mitra'     => User::find($request->mitra_filter)?->name ?? 'Semua',
          'tahun'     => $request->tahun_filter ?? 'Semua',
          'gelombang' => $request->gelombang_filter ?? 'Semua',
          'prodi'     => PendaftaranModel::daftarProdiAktif()[$request->prodi_filter] ?? 'Semua',
          'status'    => $request->status_filter ?? 'Semua',
        ]
      ]);

      $filename = 'Laporan_Pendaftaran_' . date('Y-m-d_H-i-s') . '.pdf';
      return $pdf->download($filename);
    }
  }
}
