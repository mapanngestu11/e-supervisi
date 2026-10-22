@extends('layouts.app')

@section('title', 'Master Instrumen')

@section('content')
    <form
        id="form-instrumen-sync"
        action="{{ route('instrumen.sync') }}"
        method="POST"
        @if (session('status')) data-flash-status="{{ e(session('status')) }}" @endif
    >
        @csrf
        @method('PUT')

        @foreach ($instruments as $instrument)
            <input type="hidden" name="snapshot_instrument_ids[]" value="{{ $instrument->id }}">
        @endforeach

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Master Instrumen</h1>
                <p class="mt-1 text-sm text-slate-500">Atur semua komponen lalu simpan sekaligus di bagian bawah halaman.</p>
            </div>
            <button
                type="button"
                class="js-add-instrument inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Instrumen Baru
            </button>
        </div>

        <div class="js-instruments-list space-y-6">
            @forelse ($instruments as $instrument)
                <article class="js-instrument-card overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm" data-persisted="1">
                    <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/80 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="flex min-w-0 flex-1 items-start gap-3">
                            <span class="js-instrument-badge inline-flex shrink-0 items-center rounded-md bg-[#1e3a5f] px-2.5 py-1 text-[11px] font-bold tracking-wide text-white">
                                ID: {{ $instrument->id }}
                            </span>
                            <input type="hidden" class="js-instrument-id" name="instruments[0][id]" value="{{ $instrument->id }}">
                            <input
                                type="text"
                                class="js-instrument-judul min-w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1 text-sm font-bold uppercase tracking-wide text-slate-800 outline-none transition focus:border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                name="instruments[0][judul]"
                                value="{{ $instrument->judul }}"
                            >
                        </div>
                        <div class="flex shrink-0 items-center gap-1.5">
                            <button type="button" class="js-move-instrument inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100" data-direction="up" title="Pindah ke atas">
                                <i class="fa-solid fa-chevron-up text-xs"></i>
                            </button>
                            <button type="button" class="js-move-instrument inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100" data-direction="down" title="Pindah ke bawah">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </button>
                            <button type="button" class="js-delete-instrument inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100" title="Hapus instrumen">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="js-aspek-list space-y-4 p-4 sm:p-6">
                        @foreach ($instrument->aspeks as $aspekIndex => $aspek)
                            <section class="js-aspek-block rounded-xl border border-slate-200 bg-[#f8fafc]" data-persisted="1">
                                <div class="flex flex-col gap-2 border-b border-slate-200/80 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex min-w-0 flex-1 items-center gap-2">
                                        <span class="js-aspek-letter shrink-0 text-sm font-bold text-[#1e3a5f]">{{ chr(65 + $aspekIndex) }}.</span>
                                        <input type="hidden" class="js-aspek-id" value="{{ $aspek->id }}">
                                        <input
                                            type="text"
                                            class="js-aspek-judul min-w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1 text-sm font-bold uppercase tracking-wide text-[#1e3a5f] outline-none transition focus:border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                            value="{{ $aspek->judul }}"
                                        >
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button type="button" class="js-move-aspek inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-100" data-direction="up" title="Aspek ke atas">
                                            <i class="fa-solid fa-chevron-up text-[10px]"></i>
                                        </button>
                                        <button type="button" class="js-move-aspek inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-100" data-direction="down" title="Aspek ke bawah">
                                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                        </button>
                                        <button type="button" class="js-remove-aspek text-[11px] font-bold uppercase tracking-wide text-red-500 transition hover:text-red-700">
                                            Hapus Aspek
                                        </button>
                                    </div>
                                </div>

                                <ul class="js-indikator-list space-y-2 p-4">
                                    @foreach ($aspek->indikators as $indikatorIndex => $indikator)
                                        <li class="js-indikator-row flex items-center gap-2 rounded-lg border border-slate-200/80 bg-white px-3 py-2 shadow-sm" data-persisted="1">
                                            <span class="js-indikator-num w-6 shrink-0 text-xs font-semibold text-slate-400">{{ $indikatorIndex + 1 }}.</span>
                                            <input type="hidden" class="js-indikator-id" value="{{ $indikator->id }}">
                                            <input
                                                type="text"
                                                class="js-indikator-deskripsi min-w-0 flex-1 rounded-md border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-700 outline-none transition focus:border-blue-200 focus:bg-blue-50/30 focus:ring-2 focus:ring-blue-100"
                                                value="{{ $indikator->deskripsi }}"
                                            >
                                            <button type="button" class="js-move-indikator inline-flex h-7 w-7 shrink-0 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-600" data-direction="up" title="Indikator ke atas">
                                                <i class="fa-solid fa-chevron-up text-[9px]"></i>
                                            </button>
                                            <button type="button" class="js-move-indikator inline-flex h-7 w-7 shrink-0 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-600" data-direction="down" title="Indikator ke bawah">
                                                <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                            </button>
                                            <button type="button" class="js-remove-indikator inline-flex h-7 w-7 shrink-0 items-center justify-center rounded text-red-400 transition hover:bg-red-50 hover:text-red-600" title="Hapus indikator">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="border-t border-slate-200/80 px-4 py-3">
                                    <button type="button" class="js-add-indikator inline-flex items-center gap-2 rounded-lg bg-[#2563eb] px-4 py-2 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm transition hover:bg-blue-700">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                        Tambah Indikator
                                    </button>
                                </div>
                            </section>
                        @endforeach

                        <button type="button" class="js-add-aspek inline-flex w-full items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-200 py-3 text-sm font-semibold text-slate-500 transition hover:border-blue-300 hover:bg-blue-50/50 hover:text-blue-700">
                            <i class="fa-solid fa-plus text-xs"></i>
                            Tambah Aspek (Kategori)
                        </button>
                    </div>
                </article>
            @empty
                <div class="js-empty-hint rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500">
                    Belum ada instrumen. Klik <strong>Instrumen Baru</strong> untuk memulai, lalu simpan di bawah.
                </div>
            @endforelse
        </div>

        <div class="sticky bottom-4 z-10 mt-6 rounded-xl border border-slate-200 bg-white p-4 shadow-lg sm:p-5">
            <p class="mb-3 text-sm text-slate-600">
                Perubahan urutan, penambahan, penghapusan, dan teks <strong>belum tersimpan</strong> sampai Anda menekan tombol di bawah.
            </p>
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1e3a5f] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#152a45] sm:w-auto"
            >
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                Simpan Semua Perubahan
            </button>
        </div>
    </form>

    <template id="tpl-instrument-card">
        <article class="js-instrument-card overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm" data-persisted="0">
            <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/80 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex min-w-0 flex-1 items-start gap-3">
                    <span class="js-instrument-badge inline-flex shrink-0 items-center rounded-md bg-emerald-600 px-2.5 py-1 text-[11px] font-bold tracking-wide text-white">BARU</span>
                    <input
                        type="text"
                        class="js-instrument-judul min-w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1 text-sm font-bold uppercase tracking-wide text-slate-800 outline-none transition focus:border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-100"
                        value="Instrumen Baru"
                    >
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <button type="button" class="js-move-instrument inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100" data-direction="up" title="Pindah ke atas">
                        <i class="fa-solid fa-chevron-up text-xs"></i>
                    </button>
                    <button type="button" class="js-move-instrument inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100" data-direction="down" title="Pindah ke bawah">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <button type="button" class="js-delete-instrument inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100" title="Hapus instrumen">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            </div>
            <div class="js-aspek-list space-y-4 p-4 sm:p-6">
                <section class="js-aspek-block rounded-xl border border-slate-200 bg-[#f8fafc]" data-persisted="0">
                    <div class="flex flex-col gap-2 border-b border-slate-200/80 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 flex-1 items-center gap-2">
                            <span class="js-aspek-letter shrink-0 text-sm font-bold text-[#1e3a5f]">A.</span>
                            <input type="text" class="js-aspek-judul min-w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1 text-sm font-bold uppercase tracking-wide text-[#1e3a5f] outline-none transition focus:border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-100" value="ASPEK BARU">
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" class="js-move-aspek inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-100" data-direction="up" title="Aspek ke atas">
                                <i class="fa-solid fa-chevron-up text-[10px]"></i>
                            </button>
                            <button type="button" class="js-move-aspek inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-100" data-direction="down" title="Aspek ke bawah">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </button>
                            <button type="button" class="js-remove-aspek text-[11px] font-bold uppercase tracking-wide text-red-500 transition hover:text-red-700">
                                Hapus Aspek
                            </button>
                        </div>
                    </div>
                    <ul class="js-indikator-list space-y-2 p-4"></ul>
                    <div class="border-t border-slate-200/80 px-4 py-3">
                        <button type="button" class="js-add-indikator inline-flex items-center gap-2 rounded-lg bg-[#2563eb] px-4 py-2 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm transition hover:bg-blue-700">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            Tambah Indikator
                        </button>
                    </div>
                </section>
                <button type="button" class="js-add-aspek inline-flex w-full items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-200 py-3 text-sm font-semibold text-slate-500 transition hover:border-blue-300 hover:bg-blue-50/50 hover:text-blue-700">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Aspek (Kategori)
                </button>
            </div>
        </article>
    </template>

    <template id="tpl-aspek-block">
        <section class="js-aspek-block rounded-xl border border-slate-200 bg-[#f8fafc]" data-persisted="0">
            <div class="flex flex-col gap-2 border-b border-slate-200/80 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <span class="js-aspek-letter shrink-0 text-sm font-bold text-[#1e3a5f]">A.</span>
                    <input type="text" class="js-aspek-judul min-w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1 text-sm font-bold uppercase tracking-wide text-[#1e3a5f] outline-none transition focus:border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-100" value="ASPEK BARU">
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" class="js-move-aspek inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-100" data-direction="up" title="Aspek ke atas">
                        <i class="fa-solid fa-chevron-up text-[10px]"></i>
                    </button>
                    <button type="button" class="js-move-aspek inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-100" data-direction="down" title="Aspek ke bawah">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                    <button type="button" class="js-remove-aspek text-[11px] font-bold uppercase tracking-wide text-red-500 transition hover:text-red-700">
                        Hapus Aspek
                    </button>
                </div>
            </div>
            <ul class="js-indikator-list space-y-2 p-4"></ul>
            <div class="border-t border-slate-200/80 px-4 py-3">
                <button type="button" class="js-add-indikator inline-flex items-center gap-2 rounded-lg bg-[#2563eb] px-4 py-2 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm transition hover:bg-blue-700">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    Tambah Indikator
                </button>
            </div>
        </section>
    </template>

    <template id="tpl-indikator-row">
        <li class="js-indikator-row flex items-center gap-2 rounded-lg border border-slate-200/80 bg-white px-3 py-2 shadow-sm" data-persisted="0">
            <span class="js-indikator-num w-6 shrink-0 text-xs font-semibold text-slate-400">1.</span>
            <input type="text" class="js-indikator-deskripsi min-w-0 flex-1 rounded-md border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-700 outline-none transition focus:border-blue-200 focus:bg-blue-50/30 focus:ring-2 focus:ring-blue-100" value="Indikator baru">
            <button type="button" class="js-move-indikator inline-flex h-7 w-7 shrink-0 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-600" data-direction="up" title="Indikator ke atas">
                <i class="fa-solid fa-chevron-up text-[9px]"></i>
            </button>
            <button type="button" class="js-move-indikator inline-flex h-7 w-7 shrink-0 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-600" data-direction="down" title="Indikator ke bawah">
                <i class="fa-solid fa-chevron-down text-[9px]"></i>
            </button>
            <button type="button" class="js-remove-indikator inline-flex h-7 w-7 shrink-0 items-center justify-center rounded text-red-400 transition hover:bg-red-50 hover:text-red-600" title="Hapus indikator">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </li>
    </template>
@endsection

@push('scripts')
    @vite(['resources/js/instrumen.js'])
@endpush
