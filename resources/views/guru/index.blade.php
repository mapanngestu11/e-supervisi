@extends('layouts.app')

@section('title', 'Database Pendidik')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Database Pendidik</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola data guru untuk pelaksanaan supervisi.</p>
        </div>
        <button
            type="button"
            id="btn-open-create"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
        >
            <i class="fa-solid fa-plus text-xs"></i>
            Tambah Guru
        </button>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="bg-[#1e3a5f] text-[11px] font-semibold uppercase tracking-wide text-white">
                        <th class="px-4 py-3.5 w-16">No</th>
                        <th class="px-4 py-3.5">Biodata Pendidik</th>
                        <th class="px-4 py-3.5">Tugas &amp; Mapel</th>
                        <th class="px-4 py-3.5 w-36">Sertifikasi</th>
                        <th class="px-4 py-3.5 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($gurus as $index => $guru)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-4 text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800">{{ $guru->nama_lengkap }}</p>
                                @if ($guru->nip)
                                    <p class="mt-0.5 text-xs font-medium text-blue-600">NIP: {{ $guru->nip }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-medium text-slate-800">{{ $guru->tugas_utama ?: '—' }}</p>
                                @if ($guru->mata_pelajaran)
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $guru->mata_pelajaran }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-block rounded-md bg-slate-100 px-3 py-1 text-[11px] font-bold tracking-wide text-slate-600">
                                    {{ $guru->sertifikasiLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        class="btn-edit-guru inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 transition hover:bg-blue-100"
                                        data-guru='@json($guru)'
                                        title="Edit"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <form action="{{ route('guru.destroy', $guru) }}" method="POST" onsubmit="return confirm('Hapus data pendidik ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100"
                                            title="Hapus"
                                        >
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                Belum ada data pendidik. Klik <strong>Tambah Guru</strong> untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('guru.partials.modal-form', [
        'jenisKelaminOptions' => $jenisKelaminOptions,
        'tugasUtamaOptions' => $tugasUtamaOptions,
        'sertifikasiOptions' => $sertifikasiOptions,
    ])
@endsection

@push('scripts')
    @vite('resources/js/guru.js')
@endpush
