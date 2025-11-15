@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Detail Pengguna - {{ Str::of($pengguna->name)->explode(' ')->first() }}</h2>
          <div class="page-pretitle">Informasi lengkap pengguna</div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body my-2">
              <div class="row mb-4">
                <div class="col-md-4 text-center">
                  <div class="avatar avatar-xl mb-3" style="background-image: url({{ $pengguna->avatar_url }})"></div>
                  <h4>{{ $pengguna->name }}</h4>
                  <span class="badge bg-secondary text-secondary-fg text-uppercase">
                    {{ $pengguna->getRoleNames()->first() }}
                  </span>

                  @if ($pengguna->siakad_id)
                    <div class="mt-2 d-flex justify-content-center align-items-center">
                      <i class="ti ti-rosette-discount-check-filled text-primary fs-3 me-1"></i>
                      <small class="text-muted">Terhubung Siakad</small>
                    </div>
                  @endif
                </div>
                <div class="col-md-8">
                  <div class="row">
                    <div class="col-md-6 mt-3 mt-md-0">
                      <strong>Asal Sekolah:</strong><br>
                      {{ $pengguna->asal_sekolah }}
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                      <strong>Status:</strong><br>
                      <span {!! $pengguna->status_badge !!} </span>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Email:</strong><br>
                      {{ $pengguna->email }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Username:</strong><br>
                      {{ $pengguna->username }}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Nomor HP:</strong><br>
                      {{ $pengguna->nomor_hp }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Nomor Whatsapp:</strong><br>
                      {{ $pengguna->nomor_hp2 ?? '-' }}
                    </div>
                  </div>

                  {{-- TAMBAHAN: Section Tentang --}}
                  @if ($pengguna->about)
                    <div class="row">
                      <div class="col-12 mt-3">
                        <strong>Tentang:</strong><br>
                        {{ $pengguna->about }}
                      </div>
                    </div>
                  @endif

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Terakhir Login:</strong><br>
                      {{ $pengguna->last_logged_in ? $pengguna->last_logged_in->format('d/m/Y H:i') : 'Belum pernah' }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Bergabung Sejak:</strong><br>
                      {{ $pengguna->created_at->format('d/m/Y') }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-footer">
              <div class="d-flex flex-column-reverse flex-md-row-reverse bd-highlight">
                @can('user_edit')
                  <a href="{{ route('pengguna.edit', $pengguna) }}" class="btn btn-default ms-md-2 mt-2 mt-md-0">
                    <i class="ti ti-edit fs-2 me-1"></i>
                    Edit Pengguna
                  </a>
                @endcan
                <a href="{{ route('pengguna.index') }}" class="btn btn-default ms-md-2">
                  <i class="ti ti-arrow-back-up fs-2 me-1"></i>
                  Kembali
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
