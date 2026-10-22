@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">{{ $pageTitle }}</h1>
        <p class="mt-1 text-sm italic text-slate-500">{{ $pageSubtitle }}</p>
    </div>

    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
        <i class="fa-solid fa-screwdriver-wrench mb-4 text-3xl text-slate-300"></i>
        <p class="text-slate-600">Modul <strong>{{ $pageTitle }}</strong> akan dikembangkan di tahap berikutnya.</p>
    </div>
@endsection
