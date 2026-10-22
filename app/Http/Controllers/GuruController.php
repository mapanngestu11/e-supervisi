<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(): View
    {
        return view('guru.index', [
            'activeMenu' => 'guru',
            'gurus' => Guru::query()->orderBy('nama_lengkap')->get(),
            'jenisKelaminOptions' => ['Laki-laki', 'Perempuan'],
            'tugasUtamaOptions' => [
                'Wali Kelas',
                'Guru Mapel',
                'Guru Bimbingan Konseling',
                'Kepala Sekolah',
                'Wakil Kepala Sekolah',
            ],
            'sertifikasiOptions' => ['Belum Sertifikasi', 'Sudah Sertifikasi'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Guru::query()->create($this->validated($request));

        return redirect()->route('guru')->with('status', 'Data pendidik berhasil ditambahkan.');
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $guru->update($this->validated($request));

        return redirect()->route('guru')->with('status', 'Data pendidik berhasil diperbarui.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        $guru->delete();

        return redirect()->route('guru')->with('status', 'Data pendidik berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'string', 'in:Laki-laki,Perempuan'],
            'tugas_utama' => ['nullable', 'string', 'max:100'],
            'status_sertifikasi' => ['required', 'string', 'max:100'],
            'mata_pelajaran' => ['nullable', 'string', 'max:255'],
            'atribut_label' => ['nullable', 'array'],
            'atribut_label.*' => ['nullable', 'string', 'max:100'],
            'atribut_value' => ['nullable', 'array'],
            'atribut_value.*' => ['nullable', 'string', 'max:255'],
        ]);

        $data['atribut_tambahan'] = $this->parseAtributTambahan(
            $data['atribut_label'] ?? [],
            $data['atribut_value'] ?? [],
        );

        unset($data['atribut_label'], $data['atribut_value']);

        return $data;
    }

    /**
     * @param  array<int, string|null>  $labels
     * @param  array<int, string|null>  $values
     * @return array<int, array{label: string, value: string}>
     */
    private function parseAtributTambahan(array $labels, array $values): array
    {
        $result = [];

        foreach ($labels as $index => $label) {
            $label = trim((string) $label);
            $value = trim((string) ($values[$index] ?? ''));

            if ($label === '' && $value === '') {
                continue;
            }

            $result[] = [
                'label' => $label !== '' ? $label : 'Atribut',
                'value' => $value,
            ];
        }

        return $result;
    }
}
