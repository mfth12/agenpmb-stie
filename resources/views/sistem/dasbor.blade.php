@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Dashboard</h2>
          <div class="page-pretitle">{{ Auth()->user()->getRoleNames()->first() }}</div>
        </div>

        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @can('pendaftaran_create')
              <a href="{{ route('pendaftaran.index') }}" class="btn btn-primary">
                <i class="ti ti-file-isr fs-2 me-2"></i>
                Manajemen Pendaftaran
              </a>
            @endcan

            @can('user_view')
              <a href="{{ route('pengguna.index') }}" class="btn btn-default">
                <i class="ti ti-users fs-2 me-2"></i>
                Kelola Pengguna
              </a>
            @endcan

            {{-- @can('approval_view')
              <a href="{{ route('approval.index') }}" class="btn btn-default">
                Approval Mahasiswa
              </a>
            @endcan --}}
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row row-deck row-cards">
        <div class="col-sm-12 col-lg-6">
          <div class="card">
            <div class="card-body">
              <div class="row gy-3">
                <div class="col-12 col-sm d-flex flex-column">
                  @php
                    $greeting =
                        now()->hour < 11
                            ? 'Pagi'
                            : (now()->hour < 15
                                ? 'Siang'
                                : (now()->hour < 18
                                    ? 'Sore'
                                    : 'Malam'));
                  @endphp
                  <h3 class="h2">Selamat {{ $greeting }},
                    {{ Str::of(auth()->user()->name)->explode(' ')->first() }}
                  </h3>
                  <p class="text-muted">Kamu punya 53 pesan baru dan 2 notifikasi baru.</p>
                  <div class="row g-6 mt-auto">
                    <div class="col-auto">
                      <div class="subheader">Pendaftaran Hari Ini</div>
                      <div class="d-flex align-items-baseline">
                        <div class="h3 me-2">6,782</div>
                        <div class="me-auto">
                          <span class="text-green d-inline-flex align-items-center lh-1">
                            7% <i class="ti ti-trending-up fs-2"></i>
                          </span>
                        </div>
                      </div>
                      <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: 75%" role="progressbar" aria-valuenow="75"
                          aria-valuemin="0" aria-valuemax="100" aria-label="75% Complete">
                          <span class="visually-hidden">75% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="subheader">Tahun Lalu</div>
                      <div class="d-flex align-items-baseline">
                        <div class="h3 me-2">78,4%</div>
                        <div class="me-auto">
                          <span class="text-red d-inline-flex align-items-center lh-1">
                            -1% <i class="ti ti-trending-down fs-2"></i>
                          </span>
                        </div>
                      </div>
                      <div class="progress progress-sm">
                        <div class="progress-bar bg-danger" style="width: 78%" role="progressbar" aria-valuenow="78"
                          aria-valuemin="0" aria-valuemax="100" aria-label="78% Complete">
                          <span class="visually-hidden">78% Complete</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                {{-- ilustrasi dasbor --}}
                <div class="col-12 col-sm-auto d-flex justify-content-center">
                  <a href="javascript:void(0)" class="">
                    @include('components.back.illustrations-dasbor')
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        {{-- berdasarkan role --}}
        @if ($user_role == 'superadmin')
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Total Pengguna</div>
                <div class="d-flex align-items-baseline mb-2">
                  <div class="h1 mb-0 me-2">{{ $dashboard_data['total_users'] }}</div>
                  <div class="me-auto">
                    <span class="text-red d-inline-flex align-items-center lh-1">
                      -1% <i class="ti ti-trending-down fs-2"></i>
                    </span>
                  </div>
                </div>
                <div id="chart-active-users-3" class="position-relative"></div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Pendaftaran per Prodi</div>
                  <div class="ms-auto lh-1">
                    <div class="dropdown">
                      <a class="dropdown-toggle text-secondary" id="sales-dropdown" href="#"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        aria-label="Select time range for sales data">Last 7
                        days</a>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sales-dropdown">
                        <a class="dropdown-item active" href="#" aria-current="true">Last 7 days</a>
                        <a class="dropdown-item" href="#">Last 30 days</a>
                        <a class="dropdown-item" href="#">Last 3 months</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="h1 mb-3">75%</div>
                @foreach ($dashboard_data['pendaftaran_by_prodi'] as $prodi => $total)
                  <div class="d-flex mb-2">
                    {{-- <div>Tingkat Konversi</div> --}}
                    <div>{{ $prodi }}: {{ $total }}</div>
                    {{-- <div class="ms-auto">
                    <span class="text-green d-inline-flex align-items-center lh-1">
                      7% <i class="ti ti-trending-up fs-2"></i>
                    </span>
                  </div> --}}
                  </div>
                @endforeach
                {{-- <div class="progress progress-sm">
                <div class="progress-bar bg-primary" style="width: 75%" role="progressbar" aria-valuenow="75"
                  aria-valuemin="0" aria-valuemax="100" aria-label="75% Complete">
                  <span class="visually-hidden">75% Complete</span>
                </div>
              </div> --}}
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-6">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Total Pendaftaran</div>
                <div class="d-flex align-items-baseline">
                  <div class="h1 mb-0 me-2">{{ $dashboard_data['total_pendaftaran'] }}</div>
                  <div class="me-auto">
                    <span class="text-green d-inline-flex align-items-center lh-1">
                      8% <i class="ti ti-trending-up fs-2"></i>
                    </span>
                  </div>
                </div>
                <div id="chart-pendaftaran" class="mt-2 w-100 h-100"></div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Pendaftaran per Status</div>
                  <div class="ms-auto lh-1">
                    <div class="dropdown">
                      <a class="dropdown-toggle text-secondary" id="revenue-dropdown" href="#"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        aria-label="Select time range for revenue">Last 7 days</a>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="revenue-dropdown">
                        <a class="dropdown-item active" href="#" aria-current="true">Last 7 days</a>
                        <a class="dropdown-item" href="#">Last 30 days</a>
                        <a class="dropdown-item" href="#">Last 3 months</a>
                      </div>
                    </div>
                  </div>
                </div>
                @foreach ($dashboard_data['pendaftaran_by_status'] as $status => $total)
                  <div class="d-flex mb-1 mt-2">
                    <div>{{ $status }}: {{ $total }}</div>
                  </div>
                @endforeach
              </div>
              <div id="chart-revenue-bg" class="position-relative rounded-bottom chart-sm"></div>
            </div>
          </div>
        @elseif ($user_role == 'baak')
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Total Pendaftaran</div>
                <div class="h1 mb-3">{{ $dashboard_data['total_pendaftaran'] }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Persetujuan Tertunda</div>
                <div class="h1 mb-3">{{ $dashboard_data['pending_approvals'] }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="subheader">Disetujui Hari Ini</div>
                <div class="h1 mb-3">{{ $dashboard_data['approved_today'] }}</div>
              </div>
            </div>
          </div>
        @endif



        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Calon Mahasiswa Baru</div>
                <div class="ms-auto lh-1">
                  <div class="dropdown">
                    <a class="dropdown-toggle text-secondary" id="new-clients-dropdown" href="#"
                      data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                      aria-label="Select time range for new clients">Last 7 days</a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="new-clients-dropdown">
                      <a class="dropdown-item active" href="#" aria-current="true">Last 7 days</a>
                      <a class="dropdown-item" href="#">Last 30 days</a>
                      <a class="dropdown-item" href="#">Last 3 months</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-baseline">
                <div class="h1 mb-3 me-2">682</div>
                <div class="me-auto">
                  <span class="text-yellow d-inline-flex align-items-center lh-1">
                    0% <i class="ti ti-minus fs-2"></i>
                  </span>
                </div>
              </div>
              <div id="chart-new-clients" class="position-relative chart-sm"></div>
              <div id="chart-visitors" class="position-relative"></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Tingkat kelulusan</div>
                <div class="ms-auto lh-1">
                  <div class="dropdown">
                    <a class="dropdown-toggle text-secondary" id="active-users-dropdown" href="#"
                      data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                      aria-label="Select time range for active users">Last 7 days</a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="active-users-dropdown">
                      <a class="dropdown-item active" href="#" aria-current="true">Last 7 days</a>
                      <a class="dropdown-item" href="#">Last 30 days</a>
                      <a class="dropdown-item" href="#">Last 3 months</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-baseline">
                <div class="h1 mb-3 me-2">2,986</div>
                <div class="me-auto">
                  <span class="text-green d-inline-flex align-items-center lh-1">
                    4% <i class="ti ti-trending-up fs-2"></i>
                  </span>
                </div>
              </div>
              <div id="chart-active-users" class="position-relative chart-sm"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('style')
  {{-- kosong --}}
@endsection

@section('modals')
  <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New report</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="example-text-input" placeholder="Your report name" />
          </div>
          <label class="form-label">Report type</label>
          <div class="form-selectgroup-boxes row mb-3">
            <div class="col-lg-6">
              <label class="form-selectgroup-item">
                <input type="radio" name="report-type" value="1" class="form-selectgroup-input" checked />
                <span class="form-selectgroup-label d-flex align-items-center p-3">
                  <span class="me-3">
                    <span class="form-selectgroup-check"></span>
                  </span>
                  <span class="form-selectgroup-label-content">
                    <span class="form-selectgroup-title strong mb-1">Simple</span>
                    <span class="d-block text-secondary">Provide only basic data needed for the report</span>
                  </span>
                </span>
              </label>
            </div>
            <div class="col-lg-6">
              <label class="form-selectgroup-item">
                <input type="radio" name="report-type" value="1" class="form-selectgroup-input" />
                <span class="form-selectgroup-label d-flex align-items-center p-3">
                  <span class="me-3">
                    <span class="form-selectgroup-check"></span>
                  </span>
                  <span class="form-selectgroup-label-content">
                    <span class="form-selectgroup-title strong mb-1">Advanced</span>
                    <span class="d-block text-secondary">Insert charts and additional advanced analyses to be inserted
                      in the report</span>
                  </span>
                </span>
              </label>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-8">
              <div class="mb-3">
                <label class="form-label">Report url</label>
                <div class="input-group input-group-flat">
                  <span class="input-group-text"> https://tabler.io/reports/ </span>
                  <input type="text" class="form-control ps-0" value="report-01" autocomplete="off" />
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label">Visibility</label>
                <select class="form-select">
                  <option value="1" selected>Private</option>
                  <option value="2">Public</option>
                  <option value="3">Hidden</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Client name</label>
                <input type="text" class="form-control" />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Reporting period</label>
                <input type="date" class="form-control" />
              </div>
            </div>
            <div class="col-lg-12">
              <div>
                <label class="form-label">Additional information</label>
                <textarea class="form-control" rows="3"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal"> Cancel </a>
          <a href="#" class="btn btn-primary btn-5 ms-auto" data-bs-dismiss="modal">
            <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="icon icon-2">
              <path d="M12 5l0 14" />
              <path d="M5 12l14 0" />
            </svg>
            Create new report
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('style')
  {{-- kosong --}}
@endsection

@section('modals')
  {{-- kosong --}}
@endsection

@section('js_atas')
  {{-- kosong --}}
@endsection

@section('js_bawah')
  {{-- DEPENDENSI UNTUK PAGE DASBOR --}}
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/jsvectormap/dist/jsvectormap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/jsvectormap/dist/maps/world.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/libs/jsvectormap/dist/maps/world-merc.js"></script>
  {{-- TAMBAHAN JS UNTUK PAGE DASBOR --}}
  @vite(['resources/js/pages/dasbor.js'])
  {{-- KOMPONEN INKLUD --}}
  @if (isset($dashboard_data['pendaftaran_chart']))
    <script>
      window.pendaftaranChartData = @json($dashboard_data['pendaftaran_chart']);
    </script>
  @endif
@endsection
