<?php

namespace App\Http\Controllers;

use App\Models\Instrument;
use App\Models\InstrumentAspek;
use App\Models\InstrumentIndikator;
use Database\Seeders\InstrumentSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstrumenController extends Controller
{
    public function index(): View
    {
        InstrumentSeeder::seedDefault();

        $instruments = Instrument::query()
            ->with(['aspeks.indikators'])
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return view('instrumen.index', [
            'activeMenu' => 'instrumen',
            'instruments' => $instruments,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
        ]);

        $maxUrutan = (int) Instrument::query()->max('urutan');

        $instrument = Instrument::query()->create([
            'judul' => $data['judul'] ?? 'Instrumen Baru',
            'urutan' => $maxUrutan + 1,
        ]);

        InstrumentAspek::query()->create([
            'instrument_id' => $instrument->id,
            'judul' => 'ASPEK BARU',
            'urutan' => 0,
        ]);

        return redirect()
            ->route('instrumen')
            ->with('status', 'Instrumen baru berhasil ditambahkan.');
    }

    public function update(Request $request, Instrument $instrument): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'aspeks' => ['nullable', 'array'],
            'aspeks.*.id' => ['nullable', 'integer'],
            'aspeks.*.judul' => ['required', 'string', 'max:255'],
            'aspeks.*.indikators' => ['nullable', 'array'],
            'aspeks.*.indikators.*.id' => ['nullable', 'integer'],
            'aspeks.*.indikators.*.deskripsi' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($instrument, $data): void {
            $instrument->update(['judul' => $data['judul']]);

            foreach ($data['aspeks'] ?? [] as $aspekUrutan => $aspekInput) {
                $aspekId = $aspekInput['id'] ?? null;

                if ($aspekId) {
                    $aspek = InstrumentAspek::query()
                        ->where('instrument_id', $instrument->id)
                        ->whereKey($aspekId)
                        ->first();
                } else {
                    $aspek = null;
                }

                if ($aspek) {
                    $aspek->update([
                        'judul' => $aspekInput['judul'],
                        'urutan' => $aspekUrutan,
                    ]);
                } else {
                    $aspek = InstrumentAspek::query()->create([
                        'instrument_id' => $instrument->id,
                        'judul' => $aspekInput['judul'],
                        'urutan' => $aspekUrutan,
                    ]);
                }

                foreach ($aspekInput['indikators'] ?? [] as $indikatorUrutan => $indikatorInput) {
                    $indikatorId = $indikatorInput['id'] ?? null;

                    if ($indikatorId) {
                        InstrumentIndikator::query()
                            ->where('instrument_aspek_id', $aspek->id)
                            ->whereKey($indikatorId)
                            ->update([
                                'deskripsi' => $indikatorInput['deskripsi'],
                                'urutan' => $indikatorUrutan,
                            ]);
                    } else {
                        InstrumentIndikator::query()->create([
                            'instrument_aspek_id' => $aspek->id,
                            'deskripsi' => $indikatorInput['deskripsi'],
                            'urutan' => $indikatorUrutan,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('instrumen')
            ->with('status', 'Instrumen berhasil disimpan.');
    }

    public function sync(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'snapshot_instrument_ids' => ['nullable', 'array'],
            'snapshot_instrument_ids.*' => ['integer'],
            'instruments' => ['nullable', 'array'],
            'instruments.*.id' => ['nullable', 'integer'],
            'instruments.*.judul' => ['required', 'string', 'max:255'],
            'instruments.*.aspeks' => ['nullable', 'array'],
            'instruments.*.aspeks.*.id' => ['nullable', 'integer'],
            'instruments.*.aspeks.*.judul' => ['required', 'string', 'max:255'],
            'instruments.*.aspeks.*.indikators' => ['nullable', 'array'],
            'instruments.*.aspeks.*.indikators.*.id' => ['nullable', 'integer'],
            'instruments.*.aspeks.*.indikators.*.deskripsi' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data): void {
            $snapshot = $data['snapshot_instrument_ids'] ?? [];
            $submittedIds = collect($data['instruments'] ?? [])
                ->pluck('id')
                ->filter()
                ->values()
                ->all();

            if ($snapshot !== []) {
                Instrument::query()
                    ->whereIn('id', $snapshot)
                    ->whereNotIn('id', $submittedIds)
                    ->each(fn (Instrument $instrument) => $instrument->delete());
            }

            foreach ($data['instruments'] ?? [] as $instUrutan => $instInput) {
                $this->persistInstrumentTree($instInput, $instUrutan);
            }
        });

        return redirect()
            ->route('instrumen')
            ->with('status', 'Semua perubahan instrumen berhasil disimpan.');
    }

    public function destroy(Instrument $instrument): RedirectResponse
    {
        $instrument->delete();

        return redirect()
            ->route('instrumen')
            ->with('status', 'Instrumen berhasil dihapus.');
    }

    public function moveInstrument(Request $request, Instrument $instrument): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $this->swapSortOrder(
            Instrument::query()->orderBy('urutan')->orderBy('id')->get(),
            $instrument,
            $direction,
            'urutan',
        );

        return redirect()->route('instrumen')->with('status', 'Urutan instrumen diperbarui.');
    }

    public function storeAspek(Instrument $instrument): RedirectResponse
    {
        $maxUrutan = (int) $instrument->aspeks()->max('urutan');

        InstrumentAspek::query()->create([
            'instrument_id' => $instrument->id,
            'judul' => 'ASPEK BARU',
            'urutan' => $maxUrutan + 1,
        ]);

        return redirect()
            ->route('instrumen')
            ->with('status', 'Aspek baru berhasil ditambahkan.');
    }

    public function destroyAspek(InstrumentAspek $instrumentAspek): RedirectResponse
    {
        $instrumentAspek->delete();

        return redirect()
            ->route('instrumen')
            ->with('status', 'Aspek berhasil dihapus.');
    }

    public function moveAspek(Request $request, InstrumentAspek $instrumentAspek): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $siblings = InstrumentAspek::query()
            ->where('instrument_id', $instrumentAspek->instrument_id)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $this->swapSortOrder($siblings, $instrumentAspek, $direction, 'urutan');

        return redirect()->route('instrumen')->with('status', 'Urutan aspek diperbarui.');
    }

    public function storeIndikator(InstrumentAspek $instrumentAspek): RedirectResponse
    {
        $maxUrutan = (int) $instrumentAspek->indikators()->max('urutan');

        InstrumentIndikator::query()->create([
            'instrument_aspek_id' => $instrumentAspek->id,
            'deskripsi' => 'Indikator baru',
            'urutan' => $maxUrutan + 1,
        ]);

        return redirect()
            ->route('instrumen')
            ->with('status', 'Indikator berhasil ditambahkan.');
    }

    public function destroyIndikator(InstrumentIndikator $instrumentIndikator): RedirectResponse
    {
        $instrumentIndikator->delete();

        return redirect()
            ->route('instrumen')
            ->with('status', 'Indikator berhasil dihapus.');
    }

    public function moveIndikator(Request $request, InstrumentIndikator $instrumentIndikator): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $siblings = InstrumentIndikator::query()
            ->where('instrument_aspek_id', $instrumentIndikator->instrument_aspek_id)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $this->swapSortOrder($siblings, $instrumentIndikator, $direction, 'urutan');

        return redirect()->route('instrumen')->with('status', 'Urutan indikator diperbarui.');
    }

    /**
     * @param  array<string, mixed>  $instInput
     */
    private function persistInstrumentTree(array $instInput, int $instUrutan): void
    {
        $instId = $instInput['id'] ?? null;

        if ($instId) {
            $instrument = Instrument::query()->find($instId);
            if (! $instrument) {
                return;
            }
            $instrument->update([
                'judul' => $instInput['judul'],
                'urutan' => $instUrutan,
            ]);
        } else {
            $instrument = Instrument::query()->create([
                'judul' => $instInput['judul'],
                'urutan' => $instUrutan,
            ]);
        }

        $keptAspekIds = [];

        foreach ($instInput['aspeks'] ?? [] as $aspekUrutan => $aspekInput) {
            $aspekId = $aspekInput['id'] ?? null;

            if ($aspekId) {
                $aspek = InstrumentAspek::query()
                    ->where('instrument_id', $instrument->id)
                    ->whereKey($aspekId)
                    ->first();
            } else {
                $aspek = null;
            }

            if ($aspek) {
                $aspek->update([
                    'judul' => $aspekInput['judul'],
                    'urutan' => $aspekUrutan,
                ]);
            } else {
                $aspek = InstrumentAspek::query()->create([
                    'instrument_id' => $instrument->id,
                    'judul' => $aspekInput['judul'],
                    'urutan' => $aspekUrutan,
                ]);
            }

            $keptAspekIds[] = $aspek->id;

            $keptIndikatorIds = [];

            foreach ($aspekInput['indikators'] ?? [] as $indikatorUrutan => $indikatorInput) {
                $indikatorId = $indikatorInput['id'] ?? null;

                if ($indikatorId) {
                    $updated = InstrumentIndikator::query()
                        ->where('instrument_aspek_id', $aspek->id)
                        ->whereKey($indikatorId)
                        ->update([
                            'deskripsi' => $indikatorInput['deskripsi'],
                            'urutan' => $indikatorUrutan,
                        ]);
                    if ($updated) {
                        $keptIndikatorIds[] = (int) $indikatorId;
                    }
                } else {
                    $indikator = InstrumentIndikator::query()->create([
                        'instrument_aspek_id' => $aspek->id,
                        'deskripsi' => $indikatorInput['deskripsi'],
                        'urutan' => $indikatorUrutan,
                    ]);
                    $keptIndikatorIds[] = $indikator->id;
                }
            }

            $indikatorQuery = InstrumentIndikator::query()->where('instrument_aspek_id', $aspek->id);
            if ($keptIndikatorIds === []) {
                $indikatorQuery->delete();
            } else {
                $indikatorQuery->whereNotIn('id', $keptIndikatorIds)->delete();
            }
        }

        $aspekQuery = InstrumentAspek::query()->where('instrument_id', $instrument->id);
        if ($keptAspekIds === []) {
            $aspekQuery->delete();
        } else {
            $aspekQuery->whereNotIn('id', $keptAspekIds)->delete();
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model>  $items
     */
    private function swapSortOrder($items, $current, string $direction, string $column): void
    {
        $index = $items->search(fn ($item) => $item->is($current));

        if ($index === false) {
            return;
        }

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if ($swapIndex < 0 || $swapIndex >= $items->count()) {
            return;
        }

        $other = $items[$swapIndex];
        $currentOrder = $current->{$column};
        $otherOrder = $other->{$column};

        $current->update([$column => $otherOrder]);
        $other->update([$column => $currentOrder]);
    }
}
