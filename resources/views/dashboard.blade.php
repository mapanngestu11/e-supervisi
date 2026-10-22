@extends('layouts.app')

@section('title', 'Executive Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Executive Dashboard</h1>
        <p class="mt-1 text-sm italic text-slate-500">Sistem Informasi Supervisi &amp; Penjaminan Mutu Akademik</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Guru', 'value' => $stats['guru'], 'icon' => 'fa-users', 'border' => 'border-t-blue-500', 'iconBg' => 'bg-blue-50 text-blue-600'],
                ['label' => 'Perempuan', 'value' => $stats['perempuan'], 'icon' => 'fa-person-dress', 'border' => 'border-t-red-500', 'iconBg' => 'bg-red-50 text-red-500'],
                ['label' => 'Laki-Laki', 'value' => $stats['laki_laki'], 'icon' => 'fa-person', 'border' => 'border-t-cyan-500', 'iconBg' => 'bg-cyan-50 text-cyan-600'],
                ['label' => 'Sertifikasi', 'value' => $stats['sertifikasi'], 'icon' => 'fa-award', 'border' => 'border-t-green-500', 'iconBg' => 'bg-green-50 text-green-600'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div @class(['rounded-xl border border-slate-200/80 border-t-4 bg-white p-5 shadow-sm', $card['border']])>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-4xl font-bold text-slate-800">{{ $card['value'] }}</p>
                    </div>
                    <div @class(['flex h-12 w-12 items-center justify-center rounded-xl', $card['iconBg']])>
                        <i @class(['fa-solid text-xl', $card['icon']])></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl bg-gradient-to-br from-[#1e4976] to-[#163a61] p-6 text-white shadow-md sm:p-8">
        <div class="flex gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15">
                <i class="fa-solid fa-circle-info text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold">Sistem Enterprise Local Persistence</h2>
                <p class="mt-2 max-w-4xl text-sm leading-relaxed text-blue-100/95">
                    Selamat datang di platform <strong class="font-semibold text-white">E-Supervisi PRO</strong>.
                    Aplikasi ini bekerja menggunakan Local Storage. Seluruh data tersimpan aman secara privat di browser perangkat Anda.
                    Data akan tetap ada meskipun browser ditutup.
                </p>
            </div>
        </div>
    </div>
@endsection
