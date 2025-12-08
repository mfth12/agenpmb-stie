@extends('components.theme.error')
@section('title', 'Anti-Spam')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">⚠️ Anti-spam Aktif</p>
  <p class="empty-subtitle text-secondary">Sistem memastikan bahwa Anda bukan robot, silakan coba lagi</p>
@endsection
