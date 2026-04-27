<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        h1, h2, p {
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #16A34A;
            margin-bottom: 16px;
            padding-bottom: 12px;
            text-align: center;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
        }

        .header p {
            color: #4B5563;
            margin-top: 4px;
        }

        .meta {
            margin-bottom: 14px;
            width: 100%;
        }

        .meta td {
            padding: 2px 0;
            vertical-align: top;
        }

        .report-table {
            border-collapse: collapse;
            width: 100%;
        }

        .report-table th {
            background: #16A34A;
            color: #FFFFFF;
            font-weight: 700;
            text-align: left;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #D1D5DB;
            padding: 6px;
        }

        .report-table tbody tr:nth-child(even) {
            background: #F9FAFB;
        }

        .total-row {
            background: #DCFCE7;
            font-weight: 700;
        }

        .signature {
            margin-left: auto;
            margin-top: 42px;
            text-align: center;
            width: 220px;
        }

        .signature-space {
            height: 64px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIMAS Al-Amiin</h1>
        <p>Laporan Rekap Absensi Siswa</p>
    </div>

    <table class="meta">
        <tr>
            <td width="120">Periode</td>
            <td>: {{ $report['periode'] ? $report['periode']->tahun_ajaran.' '.ucfirst($report['periode']->semester) : 'Semua Periode' }}</td>
            <td width="120">Tanggal Cetak</td>
            <td>: {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $report['kelas']->nama_kelas }}</td>
            <td>Rentang Tanggal</td>
            <td>
                :
                @if(($report['filters']['tanggal_mulai'] ?? null) || ($report['filters']['tanggal_selesai'] ?? null))
                    {{ $report['filters']['tanggal_mulai'] ?? 'Awal' }} s.d. {{ $report['filters']['tanggal_selesai'] ?? 'Akhir' }}
                @else
                    Seluruh periode
                @endif
            </td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th width="28">No</th>
                <th>Nama</th>
                <th width="90">NIS</th>
                <th width="60">Total Hadir</th>
                <th width="45">Izin</th>
                <th width="45">Sakit</th>
                <th width="45">Alfa</th>
                <th width="80">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['rows'] as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['nis'] }}</td>
                    <td>{{ $row['hadir'] }}</td>
                    <td>{{ $row['izin'] }}</td>
                    <td>{{ $row['sakit'] }}</td>
                    <td>{{ $row['alfa'] }}</td>
                    <td>{{ $row['persentase'] }}%</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>{{ $report['totals']['hadir'] }}</td>
                <td>{{ $report['totals']['izin'] }}</td>
                <td>{{ $report['totals']['sakit'] }}</td>
                <td>{{ $report['totals']['alfa'] }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <p>Kepala Sekolah</p>
        <div class="signature-space"></div>
        <p>(________________________)</p>
    </div>
</body>
</html>
