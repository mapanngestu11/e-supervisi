const page = window.laporanPage;
const threshold = page?.threshold ?? 70;

function generateSaran(item) {
    const nama = `Aspek ${item.letter}. ${item.label}`;

    if (!item.observed) {
        return `${nama} belum diobservasi. Lakukan observasi terlebih dahulu agar capaian dan saran pembinaan dapat dianalisis.`;
    }

    if (item.score >= threshold) {
        return `Capaian ${nama} berada pada kategori BAIK dengan skor ${Math.round(item.score)}. Pertahankan praktik baik yang sudah dilakukan dan dokumentasikan sebagai model pembelajaran bagi rekan sejawat.`;
    }

    return `Capaian ${nama} masih KURANG dengan skor ${Math.round(item.score)}. Disarankan pelaksanaan pendampingan intensif, refleksi bersama supervisor, serta perbaikan berkelanjutan sesuai indikator pada aspek ini.`;
}

function generateKesimpulan(scores, guruName) {
    const avg = scores.reduce((a, b) => a + b, 0) / (scores.length || 1);
    const kurang = scores.filter((s) => s < threshold).length;
    const nama = guruName || 'Pendidik';

    if (kurang === 0) {
        return `Kesimpulan: ${nama} menunjukkan capaian supervisi yang konsisten baik (rata-rata ${Math.round(avg)}). Rekomendasi: fokus pada inovasi dan sharing best practice.`;
    }

    if (kurang >= 3) {
        return `Kesimpulan: ${nama} memerlukan pembinaan komprehensif (rata-rata ${Math.round(avg)}). Prioritaskan instrumen dengan skor terendah melalui supervisi akademik terstruktur dan tindak lanjut berkala.`;
    }

    return `Kesimpulan: ${nama} berada pada level capaian menengah (rata-rata ${Math.round(avg)}). Perkuat aspek yang masih kurang sambil mempertahankan kekuatan pada instrumen dengan skor baik.`;
}

document.getElementById('btn-auto-analisis')?.addEventListener('click', () => {
    const scores = page?.scores ?? [];

    (page?.aspekItems ?? []).forEach((item) => {
        const field = document.getElementById(`saran_aspek_${item.id}`);
        if (field) {
            field.value = generateSaran(item);
        }
    });

    const kesimpulan = document.getElementById('kesimpulan_akhir');
    if (kesimpulan) {
        kesimpulan.value = generateKesimpulan(scores, page?.guruName);
    }
});

const canvas = document.getElementById('chart-capaian');
if (canvas && page?.scores?.length && typeof Chart !== 'undefined') {
    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: page.labels.map((_, i) => `INS ${page.ids?.[i] ?? i + 1}`),
            datasets: [{
                label: 'Skor Capaian',
                data: page.scores,
                backgroundColor: page.scores.map((s) => (s >= threshold ? 'rgba(37, 99, 235, 0.75)' : 'rgba(239, 68, 68, 0.75)')),
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { stepSize: 20 },
                },
            },
            plugins: {
                legend: { display: false },
            },
        },
    });
}
