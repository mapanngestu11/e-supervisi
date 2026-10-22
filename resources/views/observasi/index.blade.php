@extends('layouts.app')

@section('title', 'Lembar Observasi')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Lembar Observasi</h1>
        <p class="mt-1 text-sm text-slate-500">Pelaksanaan penilaian di kelas secara real-time.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-6 rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('observasi') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <input type="hidden" name="buka" value="1">

            <div class="min-w-0 flex-1">
                <label for="guru_id" class="mb-2 block text-[11px] font-bold uppercase tracking-wide text-[#1e3a5f]">
                    Target Supervisi
                </label>
                <select name="guru_id" id="guru_id" required
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Pilih Guru / Pendidik --</option>
                    @foreach ($gurus as $guru)
                        <option value="{{ $guru->id }}" @selected($selectedGuruId === $guru->id)>
                            {{ $guru->nama_lengkap }}@if ($guru->nip)
                                ({{ $guru->nip }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            @if ($instruments->count() > 1)
                <div class="min-w-0 flex-1 lg:max-w-sm">
                    <label for="instrument_id"
                        class="mb-2 block text-[11px] font-bold uppercase tracking-wide text-[#1e3a5f]">
                        Instrumen
                    </label>
                    <select name="instrument_id" id="instrument_id"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        @foreach ($instruments as $instrument)
                            <option value="{{ $instrument->id }}" @selected($selectedInstrumentId === $instrument->id)>
                                INS-{{ $instrument->id }} — {{ $instrument->judul }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @elseif ($selectedInstrument)
                <input type="hidden" name="instrument_id" value="{{ $selectedInstrument->id }}">
            @endif

            <button type="submit"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border-2 border-[#1e3a5f] bg-white px-5 py-2.5 text-sm font-bold uppercase tracking-wide text-[#1e3a5f] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                @disabled($gurus->isEmpty() || $instruments->isEmpty())>
                <i class="fa-solid fa-clipboard-list text-xs"></i>
                Buka Instrumen
            </button>
        </form>
    </div>

    @if ($gurus->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
            Belum ada data guru. Tambahkan di menu <strong>Database Guru</strong> terlebih dahulu.
        </div>
    @elseif ($instruments->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
            Belum ada instrumen. Atur di menu <strong>Instrumen</strong> terlebih dahulu.
        </div>
    @elseif ($sheetOpen && $selectedGuru && $selectedInstrument)
        <div
            class="mb-6 overflow-hidden rounded-xl border border-blue-200 bg-gradient-to-r from-[#2563eb] to-[#1d4ed8] p-5 text-white shadow-md">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-blue-100">Observasi Aktif</p>
            <p class="mt-1 text-xl font-bold">{{ $selectedGuru->nama_lengkap }}</p>
            <p class="mt-2 text-sm text-blue-100">
                Skala: <span class="font-semibold text-white">1 (Kurang)</span> s/d
                <span class="font-semibold text-white">4 (Sangat Baik)</span>
            </p>
        </div>

        <form action="{{ route('observasi.store') }}" method="POST"
            class="space-y-0 overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm">
            @csrf
            <input type="hidden" name="guru_id" value="{{ $selectedGuru->id }}">
            <input type="hidden" name="instrument_id" value="{{ $selectedInstrument->id }}">

            <div
                class="flex flex-col gap-2 border-b border-slate-200 bg-[#1e3a5f] px-4 py-4 sm:flex-row sm:items-center sm:gap-4 sm:px-6">
                <span
                    class="inline-flex shrink-0 items-center rounded-md bg-white/15 px-3 py-1 text-xs font-bold tracking-wide text-white">
                    INS-{{ $selectedInstrument->id }}
                </span>
                <p class="text-sm font-bold uppercase tracking-wide text-white sm:text-base">
                    {{ $selectedInstrument->judul }}
                </p>
            </div>

            @foreach ($selectedInstrument->aspeks as $aspekIndex => $aspek)
                <div class="border-b border-slate-200 last:border-b-0">
                    <div class="bg-[#dbeafe] px-4 py-3 sm:px-6">
                        <p class="text-sm font-bold uppercase tracking-wide text-[#1e3a5f]">
                            {{ chr(65 + $aspekIndex) }}. {{ $aspek->judul }}
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr
                                    class="border-b border-slate-100 bg-slate-50/80 text-center text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                    <th class="px-4 py-3 text-left sm:px-6">Indikator Pengamatan</th>
                                    <th class="w-16 px-2 py-3">4<br><span
                                            class="font-normal normal-case text-[10px] text-slate-400">Sangat Baik</span>
                                    </th>
                                    <th class="w-16 px-2 py-3">3<br><span
                                            class="font-normal normal-case text-[10px] text-slate-400">Cukup Baik</span>
                                    </th>
                                    <th class="w-16 px-2 py-3">2<br><span
                                            class="font-normal normal-case text-[10px] text-slate-400">Baik</span></th>
                                    <th class="w-16 px-2 py-3">1<br><span
                                            class="font-normal normal-case text-[10px] text-slate-400">Kurang</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($aspek->indikators as $indikator)
                                    @php
                                        $current = old(
                                            'ratings.' . $indikator->id,
                                            $existingRatings[$indikator->id] ?? null,
                                        );
                                    @endphp
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-4 py-4 text-slate-700 sm:px-6">{{ $indikator->deskripsi }}</td>
                                        @foreach ([4, 3, 2, 1] as $skala)
                                            <td class="px-2 py-4 text-center">
                                                <label class="inline-flex cursor-pointer items-center justify-center">
                                                    <input type="radio" name="ratings[{{ $indikator->id }}]"
                                                        value="{{ $skala }}" @checked((int) $current === $skala) required
                                                        class="h-4 w-4 border-slate-300 text-[#2563eb] focus:ring-[#2563eb]">
                                                    <span class="sr-only">Skor {{ $skala }}</span>
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div
                class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50/80 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs text-slate-500">
                    Data indikator diambil dari menu <strong>Instrumen</strong>. Skor tersimpan untuk laporan analisis.
                </p>
                <button type="submit"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#1e3a5f] px-5 py-2.5 text-sm font-bold uppercase tracking-wide text-white hover:bg-[#163049]">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Penilaian
                </button>
            </div>
        </form>
    @elseif ($selectedGuruId && !$sheetOpen)
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
            Pilih guru lalu klik <strong>Buka Instrumen</strong> untuk memulai penilaian.
        </div>
    @endif
@endsection
