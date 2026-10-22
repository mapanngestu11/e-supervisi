<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Instrument;
use App\Models\LaporanAnalysis;
use App\Services\ObservationScoreCalculator;
use Database\Seeders\InstrumentSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * @var array<int, string>
     */
    private array $instrumentNames = [
        1 => 'Perencanaan Pembelajaran',
        2 => 'Pelaksanaan Pembelajaran',
        3 => 'Penilaian Hasil Belajar',
        4 => 'Pengembangan Profesi',
    ];

    public function index(Request $request, ObservationScoreCalculator $calculator): View
    {
        InstrumentSeeder::seedDefault();

        $instruments = Instrument::query()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $instrumentNames = $instruments->isNotEmpty()
            ? $instruments->pluck('judul', 'id')->all()
            : $this->instrumentNames;

        $gurus = Guru::query()->orderBy('nama_lengkap')->get();
        $selectedGuruId = (int) $request->query('guru_id', $gurus->first()?->id ?? 0);
        $selectedGuru = $gurus->firstWhere('id', $selectedGuruId);

        $scores = $selectedGuru
            ? $this->scoresForGuru($selectedGuru, $calculator, $instruments)
            : $this->emptyScores($instrumentNames);

        $saved = $selectedGuru
            ? LaporanAnalysis::query()->where('guru_id', $selectedGuru->id)->latest()->first()
            : null;

        // Get all instruments that have ratings (observed instruments)
        $ratings = \App\Models\ObservationRating::query()
            ->where('guru_id', $selectedGuru->id)
            ->pluck('nilai', 'instrument_indikator_id');

        $observedInstrumentIds = [];
        foreach ($instruments as $instrument) {
            foreach ($instrument->aspeks as $aspek) {
                foreach ($aspek->indikators as $indikator) {
                    if ($ratings->has($indikator->id)) {
                        $observedInstrumentIds[$instrument->id] = true;
                        break 2;
                    }
                }
            }
        }

        // Filter to only show observed instruments
        $filteredInstrumentNames = [];
        $filteredScores = [];
        foreach ($instrumentNames as $id => $name) {
            if (isset($observedInstrumentIds[$id])) {
                $filteredInstrumentNames[$id] = $name;
                $filteredScores[$id] = $scores[$id] ?? 0;
            }
        }

        $chartScores = $selectedGuru
            ? array_values(array_map(fn($n) => $filteredScores[$n] ?? 0, array_keys($filteredInstrumentNames)))
            : array_fill(0, count($filteredInstrumentNames), 0);

        return view('laporan.index', [
            'activeMenu' => 'laporan',
            'gurus' => $gurus,
            'selectedGuru' => $selectedGuru,
            'selectedGuruId' => $selectedGuruId,
            'instrumentNames' => $filteredInstrumentNames,
            'scores' => $filteredScores,
            'thresholdBaik' => 70,
            'savedAnalysis' => $saved,
            'chartScores' => $chartScores,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:gurus,id'],
            'scores' => ['required', 'array'],
            'saran_instrumen' => ['nullable', 'array'],
            'saran_instrumen_1' => ['nullable', 'string'],
            'saran_instrumen_2' => ['nullable', 'string'],
            'saran_instrumen_3' => ['nullable', 'string'],
            'saran_instrumen_4' => ['nullable', 'string'],
            'kesimpulan_akhir' => ['nullable', 'string'],
        ]);

        // Build saran_instrumen array from individual fields
        $saranArray = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'saran_instrumen_') && is_numeric(str_replace('saran_instrumen_', '', $key))) {
                $id = str_replace('saran_instrumen_', '', $key);
                if ($value) {
                    $saranArray[$id] = $value;
                }
            }
        }

        LaporanAnalysis::updateOrCreate(
            ['guru_id' => $data['guru_id']],
            [
                'scores' => $data['scores'],
                'saran_instrumen' => $saranArray,
                'saran_instrumen_1' => $data['saran_instrumen_1'] ?? null,
                'saran_instrumen_2' => $data['saran_instrumen_2'] ?? null,
                'saran_instrumen_3' => $data['saran_instrumen_3'] ?? null,
                'saran_instrumen_4' => $data['saran_instrumen_4'] ?? null,
                'kesimpulan_akhir' => $data['kesimpulan_akhir'] ?? null,
            ],
        );

        return redirect()
            ->route('laporan', ['guru_id' => $data['guru_id']])
            ->with('status', 'Hasil analisis berhasil disimpan.');
    }

    public function preview(Request $request, $guru_id): \Illuminate\View\View
    {
        $guru = Guru::findOrFail($guru_id);

        $calculator = app(ObservationScoreCalculator::class);
        $instruments = Instrument::query()->orderBy('urutan')->orderBy('id')->get();
        $instrumentNames = $instruments->isNotEmpty()
            ? $instruments->pluck('judul', 'id')->all()
            : $this->instrumentNames;

        $scores = $this->scoresForGuru($guru, $calculator, $instruments);
        $saved = LaporanAnalysis::query()->where('guru_id', $guru->id)->latest()->first();

        // Get all instruments that have ratings (observed instruments)
        $ratings = \App\Models\ObservationRating::query()
            ->where('guru_id', $guru->id)
            ->pluck('nilai', 'instrument_indikator_id');

        $observedInstrumentIds = [];
        foreach ($instruments as $instrument) {
            foreach ($instrument->aspeks as $aspek) {
                foreach ($aspek->indikators as $indikator) {
                    if ($ratings->has($indikator->id)) {
                        $observedInstrumentIds[$instrument->id] = true;
                        break 2;
                    }
                }
            }
        }

        // Filter to only show observed instruments
        $filteredInstrumentNames = [];
        $filteredScores = [];
        foreach ($instrumentNames as $id => $name) {
            if (isset($observedInstrumentIds[$id])) {
                $filteredInstrumentNames[$id] = $name;
                $filteredScores[$id] = $scores[$id] ?? 0;
            }
        }

        return view('laporan.preview', [
            'guru' => $guru,
            'instrumentNames' => $filteredInstrumentNames,
            'scores' => $filteredScores,
            'thresholdBaik' => 70,
            'savedAnalysis' => $saved,
        ]);
    }

    public function exportWord(Request $request, $guru_id): \Illuminate\Http\Response
    {
        $guru = Guru::findOrFail($guru_id);

        $calculator = app(ObservationScoreCalculator::class);
        $instruments = Instrument::query()->orderBy('urutan')->orderBy('id')->get();
        $instrumentNames = $instruments->isNotEmpty()
            ? $instruments->pluck('judul', 'id')->all()
            : $this->instrumentNames;

        $scores = $this->scoresForGuru($guru, $calculator, $instruments);
        $saved = LaporanAnalysis::query()->where('guru_id', $guru->id)->latest()->first();

        // Get all instruments that have ratings (observed instruments)
        $ratings = \App\Models\ObservationRating::query()
            ->where('guru_id', $guru->id)
            ->pluck('nilai', 'instrument_indikator_id');

        $observedInstrumentIds = [];
        foreach ($instruments as $instrument) {
            foreach ($instrument->aspeks as $aspek) {
                foreach ($aspek->indikators as $indikator) {
                    if ($ratings->has($indikator->id)) {
                        $observedInstrumentIds[$instrument->id] = true;
                        break 2;
                    }
                }
            }
        }

        // Filter to only show observed instruments
        $filteredInstrumentNames = [];
        $filteredScores = [];
        foreach ($instrumentNames as $id => $name) {
            if (isset($observedInstrumentIds[$id])) {
                $filteredInstrumentNames[$id] = $name;
                $filteredScores[$id] = $scores[$id] ?? 0;
            }
        }

        $html = view('laporan.export.word', [
            'guru' => $guru,
            'instrumentNames' => $filteredInstrumentNames,
            'scores' => $filteredScores,
            'thresholdBaik' => 70,
            'savedAnalysis' => $saved,
        ])->render();

        $filename = 'Laporan_Supervisi_' . str_replace(' ', '_', $guru->nama_lengkap) . '.doc';

        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportPdf(Request $request, $guru_id): \Illuminate\Http\Response
    {
        $guru = Guru::findOrFail($guru_id);

        $calculator = app(ObservationScoreCalculator::class);
        $instruments = Instrument::query()->orderBy('urutan')->orderBy('id')->get();
        $instrumentNames = $instruments->isNotEmpty()
            ? $instruments->pluck('judul', 'id')->all()
            : $this->instrumentNames;

        $scores = $this->scoresForGuru($guru, $calculator, $instruments);
        $saved = LaporanAnalysis::query()->where('guru_id', $guru->id)->latest()->first();

        // Get all instruments that have ratings (observed instruments)
        $ratings = \App\Models\ObservationRating::query()
            ->where('guru_id', $guru->id)
            ->pluck('nilai', 'instrument_indikator_id');

        $observedInstrumentIds = [];
        foreach ($instruments as $instrument) {
            foreach ($instrument->aspeks as $aspek) {
                foreach ($aspek->indikators as $indikator) {
                    if ($ratings->has($indikator->id)) {
                        $observedInstrumentIds[$instrument->id] = true;
                        break 2;
                    }
                }
            }
        }

        // Filter to only show observed instruments
        $filteredInstrumentNames = [];
        $filteredScores = [];
        foreach ($instrumentNames as $id => $name) {
            if (isset($observedInstrumentIds[$id])) {
                $filteredInstrumentNames[$id] = $name;
                $filteredScores[$id] = $scores[$id] ?? 0;
            }
        }

        $html = view('laporan.export.pdf', [
            'guru' => $guru,
            'instrumentNames' => $filteredInstrumentNames,
            'scores' => $filteredScores,
            'thresholdBaik' => 70,
            'savedAnalysis' => $saved,
        ])->render();

        $filename = 'Laporan_Supervisi_' . str_replace(' ', '_', $guru->nama_lengkap) . '.html';

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * @return array<int, float>
     */
    private function scoresForGuru(Guru $guru, ObservationScoreCalculator $calculator, $instruments): array
    {
        $saved = LaporanAnalysis::query()->where('guru_id', $guru->id)->latest()->first();
        if ($saved && is_array($saved->scores) && $saved->scores !== []) {
            return array_map('floatval', $saved->scores);
        }

        if ($instruments->isNotEmpty()) {
            $instruments->load(['aspeks.indikators']);

            return $calculator->instrumentScoresForGuru($guru->id, $instruments);
        }

        $seed = crc32($guru->nip ?: (string) $guru->id);

        return [
            1 => 55 + ($seed % 35),
            2 => 50 + (($seed >> 3) % 40),
            3 => 60 + (($seed >> 6) % 30),
            4 => 45 + (($seed >> 9) % 45),
        ];
    }

    /**
     * @param  array<int, string>  $instrumentNames
     * @return array<int, float>
     */
    private function emptyScores(array $instrumentNames): array
    {
        $scores = [];
        foreach (array_keys($instrumentNames) as $key) {
            $scores[$key] = 0.0;
        }

        return $scores !== [] ? $scores : [1 => 0, 2 => 0, 3 => 0, 4 => 0];
    }
}
