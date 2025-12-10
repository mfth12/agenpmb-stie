<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Laporan Pendaftaran</title>
  <style>
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 12pt;
      line-height: 1.3;
      color: #000;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
    }

    .header img {
      max-height: 60px;
      margin-bottom: 10px;
    }

    .header h1 {
      margin: 0;
      font-size: 18pt;
    }

    .header p {
      margin: 5px 0 0 0;
      font-size: 12pt;
    }

    .filters {
      margin-bottom: 20px;
      font-size: 10pt;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10pt;
    }

    .table th,
    .table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: left;
    }

    .table th {
      background-color: #f2f2f2;
    }

    .footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      text-align: center;
      font-size: 8pt;
    }
  </style>
</head>

<body>
  <div class="header">
    {{-- <img src="{{ public_path('path/to/your/logo.png') }}" alt="Logo"> --}}
    <h1>{{ konfigs('NAMA_SISTEM') }}</h1>
    <p>Laporan Pendaftaran Calon Mahasiswa Baru</p>
    <hr>
    <div class="filters">
      <strong>Filter:</strong>
      Mitra: {{ $filters['mitra'] }},
      Tahun: {{ $filters['tahun'] }},
      Gelombang: {{ $filters['gelombang'] }},
      Prodi: {{ $filters['prodi'] }},
      Status: {{ $filters['status'] }}
    </div>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Email</th>
        <th>No HP</th>
        <th>Prodi</th>
        <th>Tahun/Gel.</th>
        <th>Status</th>
        <th>Mitra</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->nama_lengkap }}</td>
          <td>{{ Str::limit($item->email, 15, '...') }} </td>
          <td>{{ $item->nomor_hp }}</td>
          <td>{{ $item->prodi_nama }}</td>
          <td>{{ $item->tahun }}/{{ $item->gelombang }}</td>
          <td>{{ ucfirst($item->status) }}</td>
          <td>{{ $item->mitra->name ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Laporan dicetak pada {{ now()->format('d/m/Y H:i:s') }}
  </div>

  @if (request()->boolean('cetak'))
    <script>
      window.addEventListener('load', function() {
        window.print();
      });
    </script>
  @endif
</body>

</html>
