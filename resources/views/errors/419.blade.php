@extends('components.theme.error')
@section('title', 'Eror 419 (Akses Kadaluarsa)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 419 (Akses Kadaluarsa)</p>
  <p class="empty-subtitle text-secondary">Akses yang Anda lakukan tidak dapat diproses lagi</p>
@endsection
