@extends('components.theme.back')

@section('container')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">Profil Saya</h2>
          <div class="page-pretitle">Informasi akun dan profil Anda</div>
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
                  <div class="avatar avatar-xl mb-3" style="background-image: url({{ $user->avatar_url }})"></div>
                  <h4>{{ $user->name }}</h4>
                  <span class="badge bg-secondary text-secondary-fg text-uppercase">
                    {{ $user->getRoleNames()->first() }}
                  </span>

                  @if ($user->siakad_id)
                    <div class="mt-2 d-flex justify-content-center align-items-center">
                      <i class="ti ti-rosette-discount-check-filled text-primary fs-3 me-1"></i>
                      <small class="text-muted">Terhubung Siakad</small>
                    </div>
                  @endif
                </div>
                <div class="col-md-8">
                  <div class="row">
                    <div class="col-md-6 mt-3 mt-md-0">
                      <strong>Asal Instansi/Sekolah:</strong><br>
                      {{ $user->asal_sekolah ?? $user->afiliasi_nama }}
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                      <strong>Status:</strong><br>
                      {!! $user->status_badge !!}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Email:</strong><br>
                      {{ $user->email }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Username:</strong><br>
                      {{ $user->username }}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Nomor HP:</strong><br>
                      {{ $user->nomor_hp }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Nomor Whatsapp:</strong><br>
                      {{ $user->nomor_hp2 ?? '-' }}
                    </div>
                  </div>

                  @if ($user->about)
                    <div class="row">
                      <div class="col-12 mt-3">
                        <strong>Tentang:</strong><br>
                        {{ $user->about }}
                      </div>
                    </div>
                  @endif

                  <div class="row">
                    <div class="col-md-6 mt-3">
                      <strong>Terakhir Login:</strong><br>
                      {{ $user->last_logged_in?->translatedFormat('d M Y H:i') ?? 'Belum pernah' }}
                    </div>
                    <div class="col-md-6 mt-3">
                      <strong>Bergabung Sejak:</strong><br>
                      {{ $user->created_at?->translatedFormat('d M Y H:i') ?? 'Belum pernah' }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-footer">
              <div class="d-flex flex-column-reverse flex-md-row-reverse bd-highlight">
                <a href="{{ route('profil.edit') }}" class="btn btn-default ms-md-2 mt-2 mt-md-0">
                  <i class="ti ti-edit fs-2 me-1"></i>
                  Edit Profil
                </a>
                <a href="{{ route('dashboard.index') }}" class="btn btn-default ms-md-2">
                  <i class="ti ti-arrow-back-up fs-2 me-1"></i>
                  Dasbor
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
