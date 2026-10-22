import Swal from 'sweetalert2';

const swalDefaults = {
    confirmButtonColor: '#1e3a5f',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, lanjutkan',
    cancelButtonText: 'Batal',
};

async function confirmDelete(title, text) {
    const result = await Swal.fire({
        title,
        text,
        icon: 'warning',
        showCancelButton: true,
        ...swalDefaults,
    });

    return result.isConfirmed;
}

function swapSibling(el, direction, selector, list) {
    const items = [...list.querySelectorAll(selector)];
    const index = items.indexOf(el);
    if (index === -1) {
        return;
    }
    const targetIndex = direction === 'up' ? index - 1 : index + 1;
    if (targetIndex < 0 || targetIndex >= items.length) {
        return;
    }
    if (direction === 'up') {
        list.insertBefore(el, items[targetIndex]);
    } else {
        list.insertBefore(items[targetIndex], el);
    }
}

function reindexAll(root) {
    const cards = root.querySelectorAll(':scope > .js-instrument-card');

    cards.forEach((card, instIndex) => {
        const idInput = card.querySelector('.js-instrument-id');
        const judulInput = card.querySelector('.js-instrument-judul');
        const badge = card.querySelector('.js-instrument-badge');
        const persisted = card.dataset.persisted === '1';

        if (idInput) {
            idInput.name = `instruments[${instIndex}][id]`;
        }
        if (judulInput) {
            judulInput.name = `instruments[${instIndex}][judul]`;
        }
        if (badge) {
            badge.textContent = persisted ? `ID: ${idInput?.value ?? '—'}` : 'BARU';
            badge.classList.toggle('bg-[#1e3a5f]', persisted);
            badge.classList.toggle('bg-emerald-600', !persisted);
        }

        const aspekBlocks = card.querySelectorAll('.js-aspek-list > .js-aspek-block');
        aspekBlocks.forEach((block, aspekIndex) => {
            const letter = block.querySelector('.js-aspek-letter');
            if (letter) {
                letter.textContent = `${String.fromCharCode(65 + aspekIndex)}.`;
            }

            const aspekIdInput = block.querySelector('.js-aspek-id');
            if (aspekIdInput) {
                aspekIdInput.name = `instruments[${instIndex}][aspeks][${aspekIndex}][id]`;
            }

            const judulAspek = block.querySelector('.js-aspek-judul');
            if (judulAspek) {
                judulAspek.name = `instruments[${instIndex}][aspeks][${aspekIndex}][judul]`;
            }

            const rows = block.querySelectorAll('.js-indikator-row');
            rows.forEach((row, indIndex) => {
                const num = row.querySelector('.js-indikator-num');
                if (num) {
                    num.textContent = `${indIndex + 1}.`;
                }

                const hiddenId = row.querySelector('.js-indikator-id');
                const deskripsi = row.querySelector('.js-indikator-deskripsi');

                if (hiddenId) {
                    hiddenId.name = `instruments[${instIndex}][aspeks][${aspekIndex}][indikators][${indIndex}][id]`;
                }
                if (deskripsi) {
                    deskripsi.name = `instruments[${instIndex}][aspeks][${aspekIndex}][indikators][${indIndex}][deskripsi]`;
                }
            });
        });
    });
}

function createIndikatorRow() {
    const tpl = document.getElementById('tpl-indikator-row');
    return tpl.content.firstElementChild.cloneNode(true);
}

function createAspekBlock() {
    const tpl = document.getElementById('tpl-aspek-block');
    return tpl.content.firstElementChild.cloneNode(true);
}

function createInstrumentCard() {
    const tpl = document.getElementById('tpl-instrument-card');
    return tpl.content.firstElementChild.cloneNode(true);
}

function initInstrumenPage() {
    const form = document.getElementById('form-instrumen-sync');
    if (!form) {
        return;
    }

    const list = form.querySelector('.js-instruments-list');
    const addInstrumentBtn = document.querySelector('.js-add-instrument');
    const flashMessage = form.dataset.flashStatus;

    if (flashMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: flashMessage,
            confirmButtonColor: '#1e3a5f',
        });
    }

    form.addEventListener('click', async (e) => {
        const moveInstrument = e.target.closest('.js-move-instrument');
        if (moveInstrument) {
            const card = moveInstrument.closest('.js-instrument-card');
            swapSibling(card, moveInstrument.dataset.direction, '.js-instrument-card', list);
            reindexAll(list);
            return;
        }

        const deleteInstrument = e.target.closest('.js-delete-instrument');
        if (deleteInstrument) {
            const card = deleteInstrument.closest('.js-instrument-card');
            const persisted = card.dataset.persisted === '1';
            const ok = await confirmDelete(
                'Hapus instrumen?',
                persisted
                    ? 'Instrumen ini akan dihapus setelah Anda menyimpan perubahan.'
                    : 'Instrumen draft ini akan dihapus dari daftar.',
            );
            if (ok) {
                card.remove();
                reindexAll(list);
            }
            return;
        }

        const card = e.target.closest('.js-instrument-card');
        if (!card) {
            return;
        }

        const moveAspek = e.target.closest('.js-move-aspek');
        if (moveAspek) {
            const block = moveAspek.closest('.js-aspek-block');
            const aspekList = card.querySelector('.js-aspek-list');
            swapSibling(block, moveAspek.dataset.direction, '.js-aspek-block', aspekList);
            reindexAll(list);
            return;
        }

        const moveIndikator = e.target.closest('.js-move-indikator');
        if (moveIndikator) {
            const row = moveIndikator.closest('.js-indikator-row');
            const indikatorList = moveIndikator.closest('.js-indikator-list');
            swapSibling(row, moveIndikator.dataset.direction, '.js-indikator-row', indikatorList);
            reindexAll(list);
            return;
        }

        const addIndikator = e.target.closest('.js-add-indikator');
        if (addIndikator) {
            const block = addIndikator.closest('.js-aspek-block');
            const indikatorList = block.querySelector('.js-indikator-list');
            indikatorList.appendChild(createIndikatorRow());
            reindexAll(list);
            indikatorList.lastElementChild.querySelector('.js-indikator-deskripsi')?.focus();
            return;
        }

        const addAspek = e.target.closest('.js-add-aspek');
        if (addAspek) {
            const aspekList = card.querySelector('.js-aspek-list');
            const addBtn = aspekList.querySelector('.js-add-aspek');
            const block = createAspekBlock();
            aspekList.insertBefore(block, addBtn);
            reindexAll(list);
            block.querySelector('.js-aspek-judul')?.focus();
            return;
        }

        const removeIndikator = e.target.closest('.js-remove-indikator');
        if (removeIndikator) {
            const ok = await confirmDelete('Hapus indikator?', 'Indikator akan dihapus setelah Anda menyimpan perubahan.');
            if (ok) {
                removeIndikator.closest('.js-indikator-row')?.remove();
                reindexAll(list);
            }
            return;
        }

        const removeAspek = e.target.closest('.js-remove-aspek');
        if (removeAspek) {
            const ok = await confirmDelete('Hapus aspek?', 'Aspek beserta indikatornya akan dihapus setelah Anda menyimpan.');
            if (ok) {
                removeAspek.closest('.js-aspek-block')?.remove();
                reindexAll(list);
            }
        }
    });

    addInstrumentBtn?.addEventListener('click', () => {
        list.querySelector('.js-empty-hint')?.remove();
        const card = createInstrumentCard();
        list.appendChild(card);
        reindexAll(list);
        card.querySelector('.js-instrument-judul')?.focus();
    });

    reindexAll(list);
}

document.addEventListener('DOMContentLoaded', initInstrumenPage);
