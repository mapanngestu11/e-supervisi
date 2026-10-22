<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Laporan - {{ $guru->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Toolbar -->
    <div class="bg-white shadow-md sticky top-0 z-50">
        <div class="mx-auto max-w-4xl px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('laporan', ['guru_id' => $guru->id]) }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <h1 class="text-lg font-semibold text-gray-800">Preview Laporan</h1>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    <i class="fa-solid fa-print"></i>
                    Print PDF
                </button>
                <a href="{{ route('laporan.export.word', ['guru_id' => $guru->id]) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    <i class="fa-solid fa-file-word"></i>
                    Download Word
                </a>
            </div>
        </div>
    </div>

    <!-- Document Preview -->
    <div class="mx-auto max-w-4xl p-8">
        <div id="document-content" class="bg-white shadow-lg rounded-lg p-8">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">LAPORAN SUPERVISI AKADEMIK</h1>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <p class="text-sm"><strong>Nama Guru:</strong> {{ $guru->nama_lengkap }}</p>
                @if ($guru->nip)
                    <p class="text-sm"><strong>NIP:</strong> {{ $guru->nip }}</p>
                @endif
                <p class="text-sm"><strong>Tanggal Laporan:</strong> {{ now()->format('d F Y') }}</p>
            </div>

            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-gray-800 pb-2">HASIL ANALISIS CAPAIAN
                SUPERVISI</h2>

            <table class="w-full border-collapse border border-gray-300 mb-6">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="border border-gray-300 px-4 py-2 text-left">No</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Instrumen Supervisi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Skor</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($instrumentNames as $num => $name)
                        @php
                            $score = (float) ($scores[$num] ?? 0);
                            $baik = $score >= $thresholdBaik;
                        @endphp
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $num }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $name }}</td>
                            <td
                                class="border border-gray-300 px-4 py-2 text-center font-bold {{ $baik ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($score, 0) }}</td>
                            <td
                                class="border border-gray-300 px-4 py-2 text-center font-bold {{ $baik ? 'text-green-600' : 'text-red-600' }}">
                                {{ $baik ? 'BAIK' : 'KURANG' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($savedAnalysis)
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-gray-800 pb-2">ANALISIS & SARAN
                        PEMBINAAN</h2>

                    @if ($savedAnalysis->hasSaranAspek())
                        @foreach ($aspekItems as $item)
                            @php $saran = $savedAnalysis->getSaranForAspek($item['id']); @endphp
                            @if ($saran)
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $item['letter'] }}. {{ $item['label'] }} (Nilai: {{ number_format($item['score'], 0) }} — {{ $item['score'] >= $thresholdBaik ? 'BAIK' : 'KURANG' }})</h3>
                                    <p class="text-gray-600">{{ $saran }}</p>
                                </div>
                            @endif
                        @endforeach
                    @else
                        @foreach ($instrumentNames as $id => $name)
                            @php $saran = $savedAnalysis->getSaranForInstrument($id); @endphp
                            @if ($saran)
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $name }}</h3>
                                    <p class="text-gray-600">{{ $saran }}</p>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    @if ($savedAnalysis->kesimpulan_akhir)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Kesimpulan Akhir</h3>
                            <p class="text-gray-600">{{ $savedAnalysis->kesimpulan_akhir }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-8 text-center text-sm text-gray-500">
                <p><em>Laporan ini dibuat secara otomatis oleh Sistem E-Supervisi</em></p>
                <p>{{ now()->timezone('Asia/Jakarta')->format('d F Y H:i') }} WIB</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white;
            }

            .bg-white.shadow-md.sticky.top-0.z-50 {
                display: none;
            }

            #document-content {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</body>

</html>
