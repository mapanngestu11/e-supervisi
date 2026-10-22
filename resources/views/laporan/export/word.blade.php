<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Laporan Supervisi</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>90</w:Zoom>
        </w:WordDocument>
    </xml>
    <![endif]-->
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
        }

        h2 {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a5f;
            margin-top: 20px;
            margin-bottom: 10px;
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
        }

        .score-kurang {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
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
                <th>No</th>
                <th>Instrumen Supervisi</th>
                <th>Skor</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($instrumentNames as $num => $name)
                @php
                    $score = (float) ($scores[$num] ?? 0);
                    $baik = $score >= $thresholdBaik;
                @endphp
                <tr>
                    <td>{{ $num }}</td>
                    <td>{{ $name }}</td>
                    <td class="{{ $baik ? 'score-baik' : 'score-kurang' }}">{{ number_format($score, 0) }}</td>
                    <td>{{ $baik ? 'BAIK' : 'KURANG' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($savedAnalysis)
        <div class="section">
            <h2>ANALISIS & SARAN PEMBINAAN</h2>

            @if ($savedAnalysis->hasSaranAspek())
                @foreach ($aspekItems as $item)
                    @php $saran = $savedAnalysis->getSaranForAspek($item['id']); @endphp
                    @if ($saran)
                        <h3>{{ $item['letter'] }}. {{ $item['label'] }} (Nilai: {{ number_format($item['score'], 0) }} — {{ $item['score'] >= $thresholdBaik ? 'BAIK' : 'KURANG' }})</h3>
                        <p>{{ $saran }}</p>
                    @endif
                @endforeach
            @else
                @foreach ($instrumentNames as $id => $name)
                    @php $saran = $savedAnalysis->getSaranForInstrument($id); @endphp
                    @if ($saran)
                        <h3>{{ $name }}</h3>
                        <p>{{ $saran }}</p>
                    @endif
                @endforeach
            @endif

            @if ($savedAnalysis->kesimpulan_akhir)
                <h3>Kesimpulan Akhir</h3>
                <p>{{ $savedAnalysis->kesimpulan_akhir }}</p>
            @endif
        </div>
    @endif

    <div class="info-box" style="margin-top: 30px;">
        <p><em>Laporan ini dibuat secara otomatis oleh Sistem E-Supervisi.</em></p>
    </div>
</body>

</html>
