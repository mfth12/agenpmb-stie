@extends('components.theme.error')
@section('title', 'Eror 405 (Method Tidak Diizinkan)')

@section('container')
  <div class="empty-img">
    @include('components.error.illustrations-computer-fix')
  </div>
  <p class="empty-title">Eror 405 (Method Tidak Diizinkan)</p>
  <p class="empty-subtitle text-secondary">Anda tidak memiliki izin untuk mengakses sumber daya</p>
@endsection
