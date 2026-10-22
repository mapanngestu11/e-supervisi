@extends('layouts.app')

@section('title', 'Profil & Identitas Laporan')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Profil &amp; Identitas Laporan</h1>
        <p class="mt-1 text-sm text-slate-500">Informasi ini akan muncul pada Kop Surat dan Lembar Pengesahan.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('institusi.update') }}" method="POST" class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm">
        @csrf
        @method('PUT')

        <div class="space-y-5 p-6 sm:p-8">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="judul_laporan" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Judul Laporan</label>
                    <input
                        type="text"
                        name="judul_laporan"
                        id="judul_laporan"
                        value="{{ old('judul_laporan', $profile->judul_laporan) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100 @error('judul_laporan') border-red-400 @enderror"
                    >
                    @error('judul_laporan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nama_instansi" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Nama Instansi / Sekolah</label>
                    <input
                        type="text"
                        name="nama_instansi"
                        id="nama_instansi"
                        value="{{ old('nama_instansi', $profile->nama_instansi) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100 @error('nama_instansi') border-red-400 @enderror"
                    >
                    @error('nama_instansi')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="nama_supervisor" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Nama Supervisor/Pengawas</label>
                    <input
                        type="text"
                        name="nama_supervisor"
                        id="nama_supervisor"
                        value="{{ old('nama_supervisor', $profile->nama_supervisor) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                </div>
                <div>
                    <label for="nip_supervisor" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">NIP Supervisor</label>
                    <input
                        type="text"
                        name="nip_supervisor"
                        id="nip_supervisor"
                        value="{{ old('nip_supervisor', $profile->nip_supervisor) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="nama_kepala_sekolah" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Nama Kepala Sekolah</label>
                    <input
                        type="text"
                        name="nama_kepala_sekolah"
                        id="nama_kepala_sekolah"
                        value="{{ old('nama_kepala_sekolah', $profile->nama_kepala_sekolah) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                </div>
                <div>
                    <label for="nip_kepala_sekolah" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">NIP Kepala Sekolah</label>
                    <input
                        type="text"
                        name="nip_kepala_sekolah"
                        id="nip_kepala_sekolah"
                        value="{{ old('nip_kepala_sekolah', $profile->nip_kepala_sekolah) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                </div>
            </div>

            <div>
                <label for="alamat_instansi" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Alamat Lengkap Instansi</label>
                <textarea
                    name="alamat_instansi"
                    id="alamat_instansi"
                    rows="4"
                    class="w-full resize-y rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                >{{ old('alamat_instansi', $profile->alamat_instansi) }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="kontak_wa" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Kontak WA</label>
                    <input
                        type="text"
                        name="kontak_wa"
                        id="kontak_wa"
                        value="{{ old('kontak_wa', $profile->kontak_wa) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                </div>
                <div>
                    <label for="email_dinas" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Email Dinas</label>
                    <input
                        type="email"
                        name="email_dinas"
                        id="email_dinas"
                        value="{{ old('email_dinas', $profile->email_dinas) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100 @error('email_dinas') border-red-400 @enderror"
                    >
                    @error('email_dinas')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4 sm:px-8">
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1e3a5f] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#163352] sm:w-auto sm:min-w-[220px]"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Konfigurasi
                </button>
            </div>
        </div>
    </form>
@endsection
