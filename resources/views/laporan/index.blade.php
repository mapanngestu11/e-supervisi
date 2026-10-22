@extends('layouts.app')

@section('title', 'Analisis & Ekspor Laporan')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Analisis &amp; Ekspor Laporan</h1>
        <p class="mt-1 text-sm text-slate-500">Visualisasi capaian supervisi dan pembinaan pendidik.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-6 rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm">
        <label for="guru-select" class="mb-2 block text-[11px] font-bold uppercase tracking-wide text-[#1e3a5f]">Pilih Objek
            Analisis</label>
        <form method="GET" action="{{ route('laporan') }}" id="guru-filter-form">
            <select name="guru_id" id="guru-select" onchange="this.form.submit()"
                class="w-full max-w-xl rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-medium text-slate-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                @forelse ($gurus as $guru)
                    <option value="{{ $guru->id }}" @selected($selectedGuruId === $guru->id)>
                        {{ $guru->nama_lengkap }}@if ($guru->nip)
                            — NIP {{ $guru->nip }}
                        @endif
                    </option>
                    @empty
                        <option value="">Belum ada data guru</option>
                    @endforelse
                </select>
            </form>
        </div>

        @if ($selectedGuru)
            @if (empty($instrumentNames))
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
                    <i class="fa-solid fa-clipboard-list text-4xl mb-4 text-slate-300"></i>
                    <p class="text-lg font-medium">Guru ini belum memiliki data observasi</p>
                    <p class="text-sm mt-2">Silakan lakukan observasi terlebih dahulu di menu Observasi.</p>
                    <a href="{{ route('observasi', ['guru_id' => $selectedGuru->id]) }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        <i class="fa-solid fa-arrow-right"></i>
                        Ke Halaman Observasi
                    </a>
                </div>
            @else
                <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#1e3a5f]">Grafis Capaian N</h2>
                        <div class="h-64">
                            <canvas id="chart-capaian" aria-label="Grafik capaian instrumen"></canvas>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#1e3a5f]">Rincian Skor Per Instrumen
                        </h2>
                        <ul class="space-y-3" id="score-list">
                            @foreach ($instrumentNames as $num => $name)
                                @php
                                    $score = (float) ($scores[$num] ?? 0);
                                    $baik = $score >= $thresholdBaik;
                                @endphp
                                <li
                                    class="flex items-center justify-between gap-3 rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400">INS-{{ $num }}</p>
                                        <p class="text-sm font-medium text-slate-800">{{ $name }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg font-bold text-slate-800"
                                            data-score-ins="{{ $num }}">{{ number_format($score, 0) }}</span>
                                        <span @class([
                                            'rounded-md px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                                            'bg-blue-100 text-blue-700' => $baik,
                                            'bg-red-100 text-red-700' => !$baik,
                                        ])>
                                            {{ $baik ? 'Baik' : 'Kurang' }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <form action="{{ route('laporan.store') }}" method="POST" id="laporan-form" class="space-y-6">
                    @csrf
                    <input type="hidden" name="guru_id" value="{{ $selectedGuru->id }}">
                    @foreach ($saranItems as $item)
                        <input type="hidden" name="scores[{{ $item['id'] }}]" value="{{ $item['score'] }}"
                            id="score-input-{{ $item['id'] }}">
                    @endforeach

                    <div class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-sm font-bold uppercase tracking-wide text-[#1e3a5f]">Analisis &amp; Saran Pembinaan
                            </h2>
                            <button type="button" id="btn-auto-analisis"
                                class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2 text-xs font-bold uppercase tracking-wide text-white shadow-sm hover:bg-violet-700">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                Auto-Generate Analisis
                            </button>
                        </div>

                        @foreach ($saranItems as $item)
                            @php
                                $id = $item['id'];
                                $baik = $item['score'] >= $thresholdBaik;
                            @endphp
                            <div class="mb-4">
                                <div class="mb-1.5 flex items-center justify-between gap-3">
                                    <label for="saran_instrumen_{{ $id }}"
                                        class="block text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        INS-{{ $id }} — {{ $item['label'] }}
                                    </label>
                                    <div class="flex shrink-0 items-center gap-2">
                                        @if ($item['observed'])
                                            <span class="text-sm font-bold text-slate-800">Nilai:
                                                {{ number_format($item['score'], 0) }}</span>
                                            <span @class([
                                                'rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                                                'bg-blue-100 text-blue-700' => $baik,
                                                'bg-red-100 text-red-700' => !$baik,
                                            ])>{{ $baik ? 'Baik' : 'Kurang' }}</span>
                                        @else
                                            <span
                                                class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">Belum
                                                diobservasi</span>
                                        @endif
                                    </div>
                                </div>
                                <textarea name="saran_instrumen_{{ $id }}" id="saran_instrumen_{{ $id }}" rows="3"
                                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">{{ old('saran_instrumen_' . $id, $savedAnalysis ? $savedAnalysis->getSaranForInstrument($id) : '') }}</textarea>
                            </div>
                        @endforeach

                        <div>
                            <label for="kesimpulan_akhir"
                                class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Kesimpulan
                                Akhir</label>
                            <textarea name="kesimpulan_akhir" id="kesimpulan_akhir" rows="4"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">{{ old('kesimpulan_akhir', $savedAnalysis?->kesimpulan_akhir) }}</textarea>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit"
                                class="rounded-lg bg-[#1e3a5f] px-5 py-2.5 text-sm font-bold uppercase tracking-wide text-white hover:bg-[#163049]">
                                Simpan Hasil Analisis
                            </button>
                        </div>
                    </div>
                </form>

                <div
                    class="mt-6 flex flex-col gap-3 rounded-xl bg-[#1e3a5f] p-5 text-white sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold uppercase tracking-wide">Export Dokumen Resmi</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('laporan.preview', ['guru_id' => $selectedGuru->id]) }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/20">
                            <i class="fa-solid fa-eye"></i>
                            Preview & Export
                        </a>
                    </div>
                </div>
            @endif
        @else
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
                Tambahkan data guru terlebih dahulu di menu Database Guru.
            </div>
        @endif
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
        <script>
            window.laporanPage = {
                scores: @json($chartScores),
                labels: @json(array_values($instrumentNames)),
                threshold: {{ $thresholdBaik }},
                guruName: @json($selectedGuru?->nama_lengkap ?? ''),
                saranItems: @json($saranItems ?? []),
            };
        </script>
        @vite('resources/js/laporan.js')
    @endpush
