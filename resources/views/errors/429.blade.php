@extends('components.theme.error')
@section('title', 'Eror 429 (Terlalu Banyak Permintaan)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 429 (Terlalu Banyak Permintaan)</p>
  <p class="empty-subtitle text-secondary">Server membatasi permintaan dari pengguna, silakan coba beberapa saat lagi</p>
@endsection
