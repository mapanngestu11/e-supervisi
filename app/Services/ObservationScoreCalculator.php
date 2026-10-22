<?php

namespace App\Services;

use App\Models\Instrument;
use App\Models\ObservationRating;
use Illuminate\Support\Collection;

class ObservationScoreCalculator
{
    /**
     * Persentase capaian per instrumen (1–4 skala → 0–100) dari rating observasi.
     *
     * @return array<int, float>
     */
    public function instrumentScoresForGuru(int $guruId, Collection $instruments): array
    {
        $indikatorIds = $instruments
            ->flatMap(fn (Instrument $instrument) => $instrument->aspeks->flatMap->indikators)
            ->pluck('id');

        if ($indikatorIds->isEmpty()) {
            return [];
        }

        $ratings = ObservationRating::query()
            ->where('guru_id', $guruId)
            ->whereIn('instrument_indikator_id', $indikatorIds)
            ->pluck('nilai', 'instrument_indikator_id');

        $scores = [];

        foreach ($instruments as $instrument) {
            $values = [];

            foreach ($instrument->aspeks as $aspek) {
                foreach ($aspek->indikators as $indikator) {
                    if ($ratings->has($indikator->id)) {
                        $values[] = (int) $ratings->get($indikator->id);
                    }
                }
            }

            if ($values === []) {
                $scores[$instrument->id] = 0.0;

                continue;
            }

            $average = array_sum($values) / count($values);
            $scores[$instrument->id] = round(($average / 4) * 100, 1);
        }

        return $scores;
    }

    /**
     * Persentase capaian per aspek (1–4 skala → 0–100) dari rating observasi.
     *
     * @return array<int, array{score: float, observed: bool}>
     */
    public function aspekScoresForGuru(int $guruId, Collection $instruments): array
    {
        $indikatorIds = $instruments
            ->flatMap(fn (Instrument $instrument) => $instrument->aspeks->flatMap->indikators)
            ->pluck('id');

        $ratings = $indikatorIds->isEmpty()
            ? collect()
            : ObservationRating::query()
                ->where('guru_id', $guruId)
                ->whereIn('instrument_indikator_id', $indikatorIds)
                ->pluck('nilai', 'instrument_indikator_id');

        $scores = [];

        foreach ($instruments as $instrument) {
            foreach ($instrument->aspeks as $aspek) {
                $values = $aspek->indikators
                    ->filter(fn ($indikator) => $ratings->has($indikator->id))
                    ->map(fn ($indikator) => (int) $ratings->get($indikator->id))
                    ->all();

                $scores[$aspek->id] = [
                    'score' => $values === [] ? 0.0 : round((array_sum($values) / count($values) / 4) * 100, 1),
                    'observed' => $values !== [],
                ];
            }
        }

        return $scores;
    }
}
