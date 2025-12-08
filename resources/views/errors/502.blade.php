@extends('components.theme.error')
@section('title', 'Eror 502 (Gerbang Akses Buruk)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 502 (Gerbang Akses Buruk)</p>
  <p class="empty-subtitle text-secondary">Permintaan Anda tidak dapat diproses melalui gerbang akses ini</p>
@endsection
