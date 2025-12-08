@extends('components.theme.error')
@section('title', 'Eror 504 (Batas Waktu Habis)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 504 (Batas Waktu Habis)</p>
  <p class="empty-subtitle text-secondary">Gerbang akses server tidak mampu merespon permintaan Anda</p>
@endsection
