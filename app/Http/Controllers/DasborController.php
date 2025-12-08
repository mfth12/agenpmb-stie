<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PendaftaranModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DasborController extends Controller
{
  public function index(): View|RedirectResponse
  {
    $user = Auth()->user();
    $role = $user->getRoleNames()->first();

    // Data berdasarkan role
    $data = [
      'title' => konfigs('NAMA_SISTEM'),
      'user_role' => $role,
      'dashboard_data' => $this->getDashboardData($role)
    ];

    return view('sistem.dasbor', $data);
  }

  private function getDashboardData($role)
  {
    $endDate = Carbon::now();
    $startDate = Carbon::now()->subDays(30);

    switch ($role) {
      case 'superadmin':
        return [
          'total_users'           => User::count(),
          'total_pendaftaran'     => $this->getTotalPendaftaranByDateRange($startDate, $endDate),
          'pendaftaran_by_prodi'  => $this->getPendaftaranByProdi(),
          'pendaftaran_by_status' => $this->getPendaftaranByStatus(),
          'pendaftaran_chart'     => $this->getPendaftaranChartData($startDate, $endDate),
        ];

      case 'baak':
        return [
          'total_pendaftaran' => PendaftaranModel::count(),
          'pending_approvals' => PendaftaranModel::where('status', 'pending')->count(),
          'approved_today'    => PendaftaranModel::where('status', 'success')->whereDate('updated_at', Carbon::today())->count(),
          'pendaftaran_chart' => $this->getPendaftaranChartData($startDate, $endDate),
        ];

      case 'keuangan':
        // Implementasi untuk keuangan
        return [
          'total_pembayaran'  => 0,
          'pending_payments'  => 0,
        ];

      case 'mitra':
        // Implementasi untuk mahasiswa
        return [
          'status_pendaftaran'    => 'Belum ada pendaftaran',
          'last_activity'         => null,
        ];

      default:
        return [];
    }
  }

  private function getTotalPendaftaranByDateRange(Carbon $startDate, Carbon $endDate)
  {
    return PendaftaranModel::whereBetween('created_at', [$startDate, $endDate])->count();
  }

  private function getPendaftaranByProdi()
  {
    return PendaftaranModel::select('prodi_id', DB::raw('count(*) as total'))
      ->groupBy('prodi_id')
      ->pluck('total', 'prodi_id');
  }

  private function getPendaftaranByStatus()
  {
    return PendaftaranModel::select('status', DB::raw('count(*) as total'))
      ->groupBy('status')
      ->pluck('total', 'status');
  }

  private function getPendaftaranChartData(Carbon $startDate, Carbon $endDate)
  {
    $pendaftaranData = PendaftaranModel::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
      ->whereBetween('created_at', [$startDate, $endDate])
      ->groupBy('date')
      ->orderBy('date', 'asc')
      ->get();

    $labels = [];
    $data = [];

    foreach ($pendaftaranData as $item) {
      $labels[] = Carbon::parse($item->date)->format('d M');
      $data[] = $item->total;
    }

    return [
      'labels' => $labels,
      'data' => $data,
    ];
  }
}
