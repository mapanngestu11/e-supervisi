<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Supervisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 20px;
        }

        h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 10px;
            text-align: center;
        }

        h2 {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a5f;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 5px;
        }

        h3 {
            font-size: 12pt;
            font-weight: bold;
            color: #333;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #1e3a5f;
            color: white;
            font-weight: bold;
        }

        .score-baik {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
            text-align: center;
        }

        .score-kurang {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
            text-align: center;
        }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin: 10px 0;
        }

        .section {
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }
    </style>
</head>

<body>
    <h1>LAPORAN SUPERVISI AKADEMIK</h1>

    <div class="info-box">
        <p><strong>Nama Guru:</strong> {{ $guru->nama_lengkap }}</p>
        @if ($guru->nip)
            <p><strong>NIP:</strong> {{ $guru->nip }}</p>
        @endif
        <p><strong>Tanggal Laporan:</strong> {{ now()->timezone('Asia/Jakarta')->format('d F Y') }}</p>
    </div>

    <h2>HASIL ANALISIS CAPAIAN SUPERVISI</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Instrumen Supervisi</th>
                <th style="width: 80px;">Skor</th>
                <th style="width: 100px;">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($instrumentNames as $num => $name)
                @php
                    $score = (float) ($scores[$num] ?? 0);
                    $baik = $score >= $thresholdBaik;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $num }}</td>
                    <td>{{ $name }}</td>
                    <td class="{{ $baik ? 'score-baik' : 'score-kurang' }}">{{ number_format($score, 0) }}</td>
                    <td class="{{ $baik ? 'score-baik' : 'score-kurang' }}">{{ $baik ? 'BAIK' : 'KURANG' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($savedAnalysis)
        <div class="section">
            <h2>ANALISIS & SARAN PEMBINAAN</h2>

            @foreach ($instrumentNames as $id => $name)
                @php $saran = $savedAnalysis->getSaranForInstrument($id); @endphp
                @if ($saran)
                    <h3>{{ $name }}</h3>
                    <p>{{ $saran }}</p>
                @endif
            @endforeach

            @if ($savedAnalysis->kesimpulan_akhir)
                <h3>Kesimpulan Akhir</h3>
                <p>{{ $savedAnalysis->kesimpulan_akhir }}</p>
            @endif
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem E-Supervisi</p>
        <p>{{ now()->timezone('Asia/Jakarta')->format('d F Y H:i') }} WIB</p>
    </div>
</body>

</html>
