<!DOCTYPE html>
<html lang="id">

<head>
  <x-back.header :title="$title" />
  <x-back.favicon />
  @yield('style')
  @yield('js_atas')
</head>

<body>

  <div class="page">
    <x-back.navbar />
    <x-back.menu />

    <div class="page-wrapper">
      @yield('container')
      <x-back.footer />
    </div>
  </div>

  <x-modal.umum />
  @yield('modals')

  {{-- <x-back.izitoast /> --}}
  <x-back.simple-toast />
  <x-back.script />
  <x-back.sweetalert />
  @yield('js_bawah')
  @include('components.back.konfig-tampilan', ['floating' => false])
</body>

</html>
