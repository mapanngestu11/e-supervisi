<?php

namespace App\Http\Controllers;

use App\Models\InstitusiProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitusiController extends Controller
{
    public function edit(): View
    {
        $profile = InstitusiProfile::query()->first() ?? new InstitusiProfile;

        return view('institusi.edit', [
            'activeMenu' => 'institusi',
            'profile' => $profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul_laporan' => ['nullable', 'string', 'max:255'],
            'nama_instansi' => ['nullable', 'string', 'max:255'],
            'nama_supervisor' => ['nullable', 'string', 'max:255'],
            'nip_supervisor' => ['nullable', 'string', 'max:50'],
            'nama_kepala_sekolah' => ['nullable', 'string', 'max:255'],
            'nip_kepala_sekolah' => ['nullable', 'string', 'max:50'],
            'alamat_instansi' => ['nullable', 'string', 'max:2000'],
            'kontak_wa' => ['nullable', 'string', 'max:30'],
            'email_dinas' => ['nullable', 'email', 'max:255'],
        ]);

        $profile = InstitusiProfile::query()->first();

        if ($profile) {
            $profile->update($validated);
        } else {
            InstitusiProfile::query()->create($validated);
        }

        return redirect()
            ->route('institusi')
            ->with('status', 'Konfigurasi institusi berhasil disimpan.');
    }
}
