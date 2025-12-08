@extends('components.theme.error')
@section('title', 'Eror 413 (Konten Terlalu Besar)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 413 (Konten Terlalu Besar)</p>
  <p class="empty-subtitle text-secondary">Konten atau file yang diproses terlalu besar</p>
@endsection
