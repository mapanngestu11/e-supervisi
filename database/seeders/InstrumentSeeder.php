<?php

namespace Database\Seeders;

use App\Models\Instrument;
use App\Models\InstrumentAspek;
use App\Models\InstrumentIndikator;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    public static function seedDefault(): void
    {
        if (Instrument::query()->exists()) {
            return;
        }

        $instrument = Instrument::query()->create([
            'judul' => 'SUPERVISI (AKADEMIK PROSES BELAJAR MENGAJAR) GURU',
            'urutan' => 0,
        ]);

        $aspekData = [
            [
                'judul' => 'PENDAHULUAN',
                'indikators' => [
                    'Menyiapkan peserta didik secara psikis dan fisik',
                    'Memberi motivasi belajar peserta didik secara kontekstual',
                    'Menjelaskan tujuan pembelajaran',
                    'Apersepsi',
                    'Memotivasi peserta dalam memulai pembelajaran',
                    'Mengkondisikan suasana belajar yang nyaman',
                    'Menyampaikan cakupan materi',
                ],
            ],
            [
                'judul' => 'KEGIATAN INTI',
                'indikators' => [
                    'Menguasai materi pembelajaran',
                    'Menyajikan materi secara sistematis',
                    'Menguasai kelas',
                    'Menggunakan metode pembelajaran yang variatif',
                    'Melibatkan peserta didik secara aktif',
                    'Menggunakan media pembelajaran yang relevan',
                ],
            ],
            [
                'judul' => 'PENUTUP',
                'indikators' => [
                    'Melakukan refleksi pembelajaran bersama peserta didik',
                    'Menyimpulkan materi pembelajaran',
                    'Memberikan tugas atau evaluasi formatif',
                ],
            ],
        ];

        foreach ($aspekData as $aspekIndex => $aspekItem) {
            $aspek = InstrumentAspek::query()->create([
                'instrument_id' => $instrument->id,
                'judul' => $aspekItem['judul'],
                'urutan' => $aspekIndex,
            ]);

            foreach ($aspekItem['indikators'] as $indikatorIndex => $deskripsi) {
                InstrumentIndikator::query()->create([
                    'instrument_aspek_id' => $aspek->id,
                    'deskripsi' => $deskripsi,
                    'urutan' => $indikatorIndex,
                ]);
            }
        }
    }

    public function run(): void
    {
        self::seedDefault();
    }
}
