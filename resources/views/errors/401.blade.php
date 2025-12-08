@extends('components.theme.error')
@section('title', 'Eror 401 (Tidak Terautorisasi)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 401 (Tidak Terautorisasi)</p>
  <p class="empty-subtitle text-secondary">Server tidak mendapatkan autorisasi pada permintaan Anda</p>
@endsection
