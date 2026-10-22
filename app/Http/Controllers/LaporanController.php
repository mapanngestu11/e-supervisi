<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Instrument;
use App\Models\InstrumentAspek;
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
        $ratings = $selectedGuru
            ? \App\Models\ObservationRating::query()
                ->where('guru_id', $selectedGuru->id)
                ->pluck('nilai', 'instrument_indikator_id')
            : collect();

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

        $saranItems = [];
        foreach ($instrumentNames as $id => $name) {
            $saranItems[] = [
                'id' => $id,
                'label' => $name,
                'score' => (float) ($scores[$id] ?? 0),
                'observed' => isset($observedInstrumentIds[$id]),
            ];
        }

        $aspekItems = $selectedGuru
            ? $this->aspekItems($selectedGuru, $instruments, $calculator)
            : [];

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
            'saranItems' => $saranItems,
            'aspekItems' => $aspekItems,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:gurus,id'],
            'scores' => ['required', 'array'],
            'saran_aspek' => ['nullable', 'array'],
            'saran_aspek.*' => ['nullable', 'string'],
            'kesimpulan_akhir' => ['nullable', 'string'],
        ]);

        $validAspekIds = InstrumentAspek::query()->pluck('id')->all();
        $saranAspek = [];
        foreach ((array) $request->input('saran_aspek', []) as $id => $value) {
            if (in_array((int) $id, $validAspekIds, true) && is_string($value) && trim($value) !== '') {
                $saranAspek[(int) $id] = $value;
            }
        }

        LaporanAnalysis::updateOrCreate(
            ['guru_id' => $data['guru_id']],
            [
                'scores' => $data['scores'],
                'saran_aspek' => $saranAspek,
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
            'aspekItems' => $this->aspekItems($guru, $instruments, $calculator),
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
            'aspekItems' => $this->aspekItems($guru, $instruments, $calculator),
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
            'aspekItems' => $this->aspekItems($guru, $instruments, $calculator),
        ])->render();

        $filename = 'Laporan_Supervisi_' . str_replace(' ', '_', $guru->nama_lengkap) . '.html';

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * @return list<array{id: int, letter: string, label: string, instrument_id: int, instrument_label: string, score: float, observed: bool}>
     */
    private function aspekItems(Guru $guru, $instruments, ObservationScoreCalculator $calculator): array
    {
        $instruments->load(['aspeks.indikators']);
        $aspekScores = $calculator->aspekScoresForGuru($guru->id, $instruments);

        $items = [];
        foreach ($instruments as $instrument) {
            foreach ($instrument->aspeks->values() as $index => $aspek) {
                $items[] = [
                    'id' => $aspek->id,
                    'letter' => chr(65 + $index),
                    'label' => $aspek->judul,
                    'instrument_id' => $instrument->id,
                    'instrument_label' => $instrument->judul,
                    'score' => (float) ($aspekScores[$aspek->id]['score'] ?? 0),
                    'observed' => (bool) ($aspekScores[$aspek->id]['observed'] ?? false),
                ];
            }
        }

        return $items;
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
