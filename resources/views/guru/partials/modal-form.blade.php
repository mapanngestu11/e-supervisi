<div
    id="guru-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4"
    aria-hidden="true"
    role="dialog"
    aria-labelledby="guru-modal-title"
>
    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 id="guru-modal-title" class="text-lg font-bold text-slate-800">Tambah Data Pendidik</h2>
        </div>

        <form id="guru-form" method="POST" action="{{ route('guru.store') }}" data-store-url="{{ url('/database-guru') }}" class="space-y-4 p-6">
            @csrf
            <input type="hidden" name="_method" id="guru-form-method" value="POST" disabled>

            <div>
                <label for="nama_lengkap" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Nama Lengkap &amp; Gelar</label>
                <input
                    type="text"
                    name="nama_lengkap"
                    id="nama_lengkap"
                    required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                >
            </div>

            <div>
                <label for="nip" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">NIP / NIYPK</label>
                <input
                    type="text"
                    name="nip"
                    id="nip"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                >
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="jenis_kelamin" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Jenis Kelamin</label>
                    <select
                        name="jenis_kelamin"
                        id="jenis_kelamin"
                        required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                        @foreach ($jenisKelaminOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tugas_utama" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Tugas Utama</label>
                    <select
                        name="tugas_utama"
                        id="tugas_utama"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">— Pilih —</option>
                        @foreach ($tugasUtamaOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="status_sertifikasi" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Status Sertifikasi</label>
                    <select
                        name="status_sertifikasi"
                        id="status_sertifikasi"
                        required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                        @foreach ($sertifikasiOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="mata_pelajaran" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-400">Mata Pelajaran Diampu</label>
                    <input
                        type="text"
                        name="mata_pelajaran"
                        id="mata_pelajaran"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >
                </div>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Atribut Tambahan</span>
                    <button
                        type="button"
                        id="btn-add-atribut"
                        class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-600 hover:bg-slate-200"
                    >
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        Tambah Field
                    </button>
                </div>
                <div id="atribut-rows" class="space-y-2"></div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
                <button
                    type="button"
                    id="btn-close-modal"
                    class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="rounded-lg bg-[#2563eb] px-5 py-2.5 text-sm font-bold uppercase tracking-wide text-white hover:bg-blue-700"
                >
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
