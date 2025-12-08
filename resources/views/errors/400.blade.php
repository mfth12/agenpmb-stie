@extends('components.theme.error')
@section('title', 'Eror 400 (Permintaan Buruk)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 400 (Permintaan Buruk)</p>
  <p class="empty-subtitle text-secondary">Server tidak dapat merespon permintaan Anda</p>
@endsection
