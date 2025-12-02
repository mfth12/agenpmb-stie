<footer class="footer footer-transparent d-print-none">
  <div class="container-xl">
    <div class="row text-center align-items-center flex-row-reverse">
      {{-- Footer right --}}
      <div class="col-lg-auto ms-lg-auto">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item">
            <a href="https://github.com/stiepemb/" target="_blank" class="link-secondary" rel="noopener">
              Made with<i class="ti ti-heart-filled mx-1"></i>IT STIE Pembangunan. {{ env('APP_VERSION') ?? '' }}
            </a>
          </li>
        </ul>
      </div>
      {{-- Copyright --}}
      <div class="col-12 col-lg-auto mt-3 mt-lg-0">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item">
            Copyright &copy; {{ now()->year }}
            <a href="https://stie-pembangunan.ac.id/" target="_blank" class="link-secondary">{{ konfigs('NAMA_PT') ?? '' }}</a>.
            All rights reserved.
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>
