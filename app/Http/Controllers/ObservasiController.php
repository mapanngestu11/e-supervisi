<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Instrument;
use App\Models\LaporanAnalysis;
use App\Models\ObservationRating;
use App\Services\ObservationScoreCalculator;
use Database\Seeders\InstrumentSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObservasiController extends Controller
{
    public function index(Request $request): View
    {
        InstrumentSeeder::seedDefault();

        $gurus = Guru::query()->orderBy('nama_lengkap')->get();

        $instruments = Instrument::query()
            ->with(['aspeks.indikators'])
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $selectedGuruId = (int) $request->query('guru_id', 0);
        $selectedInstrumentId = (int) $request->query('instrument_id', 0);

        $selectedGuru = $gurus->firstWhere('id', $selectedGuruId);
        $selectedInstrument = $instruments->firstWhere('id', $selectedInstrumentId) ?? $instruments->first();
        $selectedInstrumentId = $selectedInstrument?->id ?? 0;
        $sheetOpen = $selectedGuru && $selectedInstrument && $request->boolean('buka');

        $existingRatings = [];

        if ($sheetOpen && $selectedGuru) {
            $indikatorIds = $instruments
                ->flatMap(fn(Instrument $instrument) => $instrument->aspeks->flatMap->indikators)
                ->pluck('id');

            $existingRatings = ObservationRating::query()
                ->where('guru_id', $selectedGuru->id)
                ->whereIn('instrument_indikator_id', $indikatorIds)
                ->pluck('nilai', 'instrument_indikator_id')
                ->all();
        }

        return view('observasi.index', [
            'activeMenu' => 'observasi',
            'gurus' => $gurus,
            'instruments' => $instruments,
            'selectedGuru' => $selectedGuru,
            'selectedGuruId' => $selectedGuruId,
            'selectedInstrument' => $selectedInstrument,
            'selectedInstrumentId' => $selectedInstrumentId,
            'sheetOpen' => $sheetOpen,
            'existingRatings' => $existingRatings,
            'skalaLabels' => [
                4 => 'Sangat Baik',
                3 => 'Cukup Baik',
                2 => 'Baik',
                1 => 'Kurang',
            ],
        ]);
    }

    public function store(Request $request, ObservationScoreCalculator $calculator): RedirectResponse
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:gurus,id'],
            'instrument_id' => ['required', 'exists:instruments,id'],
            'ratings' => ['required', 'array'],
            'ratings.*' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        $instrument = Instrument::query()
            ->with(['aspeks.indikators'])
            ->findOrFail($data['instrument_id']);

        $validIndikatorIds = $instrument->aspeks
            ->flatMap->indikators
            ->pluck('id')
            ->all();

        foreach ($data['ratings'] as $indikatorId => $nilai) {
            if (! in_array((int) $indikatorId, $validIndikatorIds, true)) {
                continue;
            }

            ObservationRating::query()->updateOrCreate(
                [
                    'guru_id' => $data['guru_id'],
                    'instrument_indikator_id' => (int) $indikatorId,
                ],
                ['nilai' => (int) $nilai],
            );
        }

        $instruments = Instrument::query()
            ->with(['aspeks.indikators'])
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $scores = $calculator->instrumentScoresForGuru((int) $data['guru_id'], $instruments);

        if ($scores !== []) {
            LaporanAnalysis::query()->updateOrCreate(
                ['guru_id' => $data['guru_id']],
                ['scores' => $scores],
            );
        }

        return redirect()
            ->route('observasi', [
                'guru_id' => $data['guru_id'],
                'instrument_id' => $data['instrument_id'],
                'buka' => 1,
            ])
            ->with('status', 'Penilaian observasi berhasil disimpan.');
    }
}
