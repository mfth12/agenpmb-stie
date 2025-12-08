@extends('components.theme.error')
@section('title', 'Eror 404 (Terlarang)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 403 (Terlarang)</p>
  <p class="empty-subtitle text-secondary">Permintaan/aksi yang Anda lakukan tidak diizinkan sistem</p>
@endsection
