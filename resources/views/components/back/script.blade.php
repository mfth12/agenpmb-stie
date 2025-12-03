{{-- SCRIPT UNTUK BACK --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler-theme.min.js" async defer></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@vite(['resources/js/pages/konfig-tampilan.js'])
<script>
  // document.addEventListener("DOMContentLoaded", function(e) {
  //   Turbo.session.drive = false;
  //   if (window.Turbo) {
  //     var loader = Turbo.navigator.delegate.adapter.progressBar;
  //     console.log(loader);
  //     loader.show(); //tampil
  //     // loader.setValue(1); //100%
  //     e.preventDefault();
  //   }
  // });

  // // Tunggu sampai semua elemen halaman (DOM) siap
  // document.addEventListener("DOMContentLoaded", function() {
  //   // Pastikan Turbo sudah ter-load di window object
  //   if (window.Turbo) {

  //     // Ambil instance progress bar bawaan dari Turbo
  //     // Path ini sudah benar sesuai yang Anda temukan
  //     const progressBar = Turbo.navigator.delegate.adapter.progressBar;

  //     // 1. Tampilkan loader KETIKA navigasi DIMULAI
  //     document.addEventListener("turbo:visit", function() {
  //       progressBar.show();
  //       console.log("Turbo visit: Loader ditampilkan.");
  //     });

  //     // 2. Sembunyikan loader KETIKA halaman SELESAI dimuat
  //     document.addEventListener("turbo:load", function() {
  //       progressBar.setValue(1);
  //       progressBar.hide();
  //       console.log("Turbo load: Loader selesai disembunyikan.");
  //     });

  //     // 3. (Opsional tapi direkomendasikan)
  //     // Sembunyikan loader jika request gagal atau ada redirect dari server
  //     document.addEventListener("turbo:request-end", function(event) {
  //       // Jika request tidak berhasil, progress bar mungkin masih terlihat.
  //       // Kita sembunyikan untuk memastikan tidak ada loader yang "nyangkut".
  //       if (event.detail.failed) {
  //         progressBar.hide();
  //         console.log("Turbo request failed: Loader disembunyikan.");
  //       }
  //     });

  //   } else {
  //     console.error("Hotwired Turbo tidak ditemukan. Pastikan sudah di-import dengan benar.");
  //   }
  // });
</script>
