const modal = document.getElementById('guru-modal');
const form = document.getElementById('guru-form');
const formMethod = document.getElementById('guru-form-method');
const modalTitle = document.getElementById('guru-modal-title');
const atributRows = document.getElementById('atribut-rows');
const storeUrl = form?.dataset.storeUrl ?? form?.action.replace(/\/$/, '');

function openModal() {
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeModal() {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

function clearAtributRows() {
    if (atributRows) {
        atributRows.innerHTML = '';
    }
}

function addAtributRow(label = '', value = '') {
    if (!atributRows) return;

    const row = document.createElement('div');
    row.className = 'atribut-row grid grid-cols-1 gap-2 sm:grid-cols-[1fr_1fr_auto]';
    row.innerHTML = `
        <input type="text" name="atribut_label[]" placeholder="Label" value="${escapeAttr(label)}"
            class="rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        <input type="text" name="atribut_value[]" placeholder="Nilai" value="${escapeAttr(value)}"
            class="rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        <button type="button" class="btn-remove-atribut inline-flex h-10 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 hover:bg-red-50" title="Hapus baris">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;

    row.querySelector('.btn-remove-atribut')?.addEventListener('click', () => row.remove());
    atributRows.appendChild(row);
}

function escapeAttr(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;');
}

function setFormCreate() {
    if (!form || !formMethod || !modalTitle) return;

    modalTitle.textContent = 'Tambah Data Pendidik';
    form.action = storeUrl;
    formMethod.value = 'POST';
    formMethod.disabled = true;
    form.reset();
    clearAtributRows();
}

function setFormEdit(guru) {
    if (!form || !formMethod || !modalTitle) return;

    modalTitle.textContent = 'Ubah Data Pendidik';
    form.action = `${storeUrl}/${guru.uuid}`;
    formMethod.value = 'PUT';
    formMethod.disabled = false;

    document.getElementById('nama_lengkap').value = guru.nama_lengkap ?? '';
    document.getElementById('nip').value = guru.nip ?? '';
    document.getElementById('jenis_kelamin').value = guru.jenis_kelamin ?? 'Laki-laki';
    document.getElementById('tugas_utama').value = guru.tugas_utama ?? '';
    document.getElementById('status_sertifikasi').value = guru.status_sertifikasi ?? 'Belum Sertifikasi';
    document.getElementById('mata_pelajaran').value = guru.mata_pelajaran ?? '';

    clearAtributRows();
    const attrs = guru.atribut_tambahan ?? [];
    if (Array.isArray(attrs) && attrs.length) {
        attrs.forEach((item) => addAtributRow(item.label ?? '', item.value ?? ''));
    }
}

document.getElementById('btn-open-create')?.addEventListener('click', () => {
    setFormCreate();
    openModal();
});

document.getElementById('btn-close-modal')?.addEventListener('click', closeModal);

modal?.addEventListener('click', (e) => {
    if (e.target === modal) {
        closeModal();
    }
});

document.getElementById('btn-add-atribut')?.addEventListener('click', () => addAtributRow());

document.querySelectorAll('.btn-edit-guru').forEach((btn) => {
    btn.addEventListener('click', () => {
        try {
            const guru = JSON.parse(btn.getAttribute('data-guru') ?? '{}');
            setFormEdit(guru);
            openModal();
        } catch {
            console.error('Invalid guru data');
        }
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
        closeModal();
    }
});
