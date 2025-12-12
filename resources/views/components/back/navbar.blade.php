<header class="navbar navbar-expand-md d-print-none">
  <div class="container-xl">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
      aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
      <a href="/" class="d-flex align-items-center text-decoration-none text-reset"
        aria-label="{{ konfigs('NAMA_SISTEM') }}">
        <span class="app-brand-logo me-2">@include('components.back.macros', ['height' => 20])</span>
        <span class="app-brand-text menu-text fw-bold">{{ konfigs('NAMA_SISTEM') }}</span>
      </a>
    </div>

    <div class="navbar-nav flex-row order-md-last">
      <div class="nav-item d-none d-md-flex me-3">
      </div>
      <div class="d-none d-md-flex">
        <div class="nav-item">
          <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Mode Gelap" data-bs-toggle="tooltip"
            data-bs-placement="bottom">
            <!-- Download SVG icon from http://tabler.io/icons/icon/moon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
              <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
            </svg>
          </a>
          <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Mode Terang" data-bs-toggle="tooltip"
            data-bs-placement="bottom">
            <!-- Download SVG icon from http://tabler.io/icons/icon/sun -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
              <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
              <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
            </svg>
          </a>
        </div>
        <div class="nav-item dropdown d-none d-md-flex">
          <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" title="Lihat Notifikasi"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-auto-close="outside">
            <!-- Download SVG icon from http://tabler.io/icons/icon/bell -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
              <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
              <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
            </svg>
            {{-- <span class="badge bg-red"></span> --}}
          </a>
          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
            <div class="card">
              <div class="card-header d-flex">
                <h3 class="card-title">Notifikasi</h3>
                <div class="btn-close ms-auto" data-bs-dismiss="dropdown"></div>
              </div>
              <div class="list-group list-group-flush list-group-hoverable">
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto"><span class="status-dot status-dot-animated bg-red d-block"></span>
                    </div>
                    <div class="col text-truncate">
                      <a href="#" class="text-body d-block">Contoh 1</a>
                      <div class="d-block text-secondary text-truncate mt-n1">Change deprecated html tags to text
                        decoration classes (#29604)</div>
                    </div>
                    <div class="col-auto">
                      <a href="#" class="list-group-item-actions">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round" class="icon text-muted icon-2">
                          <path
                            d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto"><span class="status-dot d-block"></span></div>
                    <div class="col text-truncate">
                      <a href="#" class="text-body d-block">Contoh 2</a>
                      <div class="d-block text-secondary text-truncate mt-n1">justify-content:between ⇒
                        justify-content:space-between (#29734)</div>
                    </div>
                    <div class="col-auto">
                      <a href="#" class="list-group-item-actions show">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round" class="icon text-yellow icon-2">
                          <path
                            d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto"><span class="status-dot d-block"></span></div>
                    <div class="col text-truncate">
                      <a href="#" class="text-body d-block">Contoh 3</a>
                      <div class="d-block text-secondary text-truncate mt-n1">Update change-version.js (#29736)
                      </div>
                    </div>
                    <div class="col-auto">
                      <a href="#" class="list-group-item-actions">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round" class="icon text-muted icon-2">
                          <path
                            d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto"><span class="status-dot status-dot-animated bg-green d-block"></span>
                    </div>
                    <div class="col text-truncate">
                      <a href="#" class="text-body d-block">Contoh 4</a>
                      <div class="d-block text-secondary text-truncate mt-n1">Regenerate package-lock.json
                        (#29730)</div>
                    </div>
                    <div class="col-auto">
                      <a href="#" class="list-group-item-actions">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round" class="icon text-muted icon-2">
                          <path
                            d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <a href="#" class="btn btn-2 w-100">Arsipkan Semua</a>
                  </div>
                  <div class="col">
                    <a href="#" class="btn btn-2 w-100">Tandai Telah Dibaca Semua</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="nav-item dropdown">
        <a href="#" class="nav-link d-flex lh-1 pb-2 px-2" data-bs-toggle="dropdown"
          aria-label="Open user menu">
          <span class="avatar avatar-sm" style="background-image: url({{ Auth()->user()->avatar_thumb_url }})">
          </span>
          <div class="d-none d-xl-block ps-2">
            <div>{{ Auth()->user()->name }}</div>
            <div class="mt-1 small text-secondary">{{ Auth()->user()->getRoleNames()->first() }}</div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
          <a href="{{ route('profil.show') }}" class="dropdown-item">
            <i class="ti ti-user fs-2"></i>
            Profil Saya
          </a>
          <div class="dropdown-divider"></div>
          <a href="./panduan.pdf" target="_blank" class="dropdown-item">
            <i class="ti ti-book-2 fs-2"></i>
            Panduan
          </a>
          @can('konfigurasi_manage')
            <a href="{{ route('konfigurasi.index') }}" class="dropdown-item">
              <i class="ti ti-settings-2 fs-2"></i>
              Konfigurasi
            </a>
          @endcan
          {{-- Ganti form submit menjadi link --}}
          <a href="#" class="dropdown-item text-danger" id="logout-btn">
            <i class="ti ti-logout fs-2"></i>
            Keluar
          </a>
          {{-- Form logout disembunyikan --}}
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
      </div>
    </div>
  </div>
</header>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logout-btn');
    const logoutForm = document.getElementById('logout-form');

    if (logoutBtn) {
      logoutBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Cegah aksi default dari link
        // Tampilkan konfirmasi SweetAlert
        Swal.fire({
          title: 'Konfirmasi',
          text: "Apakah Anda yakin ingin keluar?",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#d33', // Merah untuk bahaya
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Keluar',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            // Jika dikonfirmasi, submit form logout
            logoutForm.submit();
          }
          // Jika dibatalkan (result.isDismissed), tidak ada aksi
        });
      });
    }
  });
</script>
