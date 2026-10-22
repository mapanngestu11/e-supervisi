<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'activeMenu' => 'dashboard',
            'stats' => [
                'guru' => Guru::query()->count(),
                'perempuan' => Guru::query()->where('jenis_kelamin', 'Perempuan')->count(),
                'laki_laki' => Guru::query()->where('jenis_kelamin', 'Laki-laki')->count(),
                'sertifikasi' => Guru::query()
                    ->where('status_sertifikasi', 'like', '%Sudah%')
                    ->count(),
            ],
        ]);
    }
}
