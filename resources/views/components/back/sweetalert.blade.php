{{-- SweetAlert2 Notifications --}}
@if (session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      showSuccess('{{ session('success') }}');
    });
  </script>
@endif

@if (session('error'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      showError('{{ session('error') }}');
    });
  </script>
@endif

@if (session('warning'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      showWarning('{{ session('warning') }}');
    });
  </script>
@endif

@if (session('info'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      showInfo('{{ session('info') }}');
    });
  </script>
@endif

@if (!Request::is('login*'))
  {{-- tidak boleh tampil di halaman login --}}
  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        let errorMessage = '';
        @foreach ($errors->all() as $error)
          errorMessage += '{{ $error }}\n';
        @endforeach

        showError(errorMessage.trim(), 'Terjadi Kesalahan');
      });
    </script>
  @endif
@endif
