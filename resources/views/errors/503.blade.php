@extends('components.theme.error')
@section('title', 'Eror 503 (Layanan Tidak Tersedia)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 503 (Layanan Tidak Tersedia)</p>
  <p class="empty-subtitle text-secondary">Sistem sedang maintenance. Untuk sementara layanan belum siap menerima
    permintaan</p>
@endsection
