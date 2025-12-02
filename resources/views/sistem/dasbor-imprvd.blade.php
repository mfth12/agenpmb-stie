@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Dashboard</h2>
          <div class="page-pretitle">{{ Auth()->user()->getRoleNames()->first() }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row row-deck row-cards">
        @if ($user_role == 'superadmin')
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Total Pengguna</div>
                <div class="h1 mb-3">{{ $dashboard_data['total_users'] }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Total Pendaftaran (30 hari terakhir)</div>
                <div class="h1 mb-3">{{ $dashboard_data['total_pendaftaran'] }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Pendaftaran per Prodi</div>
                @foreach ($dashboard_data['pendaftaran_by_prodi'] as $prodi => $total)
                  <div>{{ $prodi }}: {{ $total }}</div>
                @endforeach
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Pendaftaran per Status</div>
                @foreach ($dashboard_data['pendaftaran_by_status'] as $status => $total)
                  <div>{{ $status }}: {{ $total }}</div>
                @endforeach
              </div>
            </div>
          </div>
        @elseif ($user_role == 'baak')
          <div class="col-sm-6 col-lg-4">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Total Pendaftaran</div>
                <div class="h1 mb-3">{{ $dashboard_data['total_pendaftaran'] }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-4">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Persetujuan Tertunda</div>
                <div class="h1 mb-3">{{ $dashboard_data['pending_approvals'] }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-4">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Disetujui Hari Ini</div>
                <div class="h1 mb-3">{{ $dashboard_data['approved_today'] }}</div>
              </div>
            </div>
          </div>
        @endif
      </div>
      <div class="row row-deck row-cards mt-3">
        <div class="col-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Grafik Pendaftaran (30 Hari Terakhir)</h3>
            </div>
            <div class="card-body">
              <div id="chart-pendaftaran" class="w-100 h-100"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js_bawah')
  {{-- DEPENDENSI UNTUK PAGE DASBOR --}}
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/apexcharts/dist/apexcharts.min.js"></script>
  @vite(['resources/js/pages/dasbor.js'])

  @if (isset($dashboard_data['pendaftaran_chart']))
    <script>
      window.pendaftaranChartData = @json($dashboard_data['pendaftaran_chart']);
    </script>
  @endif
@endsection
