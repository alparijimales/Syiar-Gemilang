<?php require_once 'navbar.php'; ?>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">

    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Kompetensi Kejuruan & Bobot Nilai';
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => 'Portal Instruktur', 'url' => base_url('instruktur')],
                ['label' => 'Kompetensi Kejuruan']
            ],
            'badge' => 'Kurikulum Berbasis Proyek (Project-Based Learning) RGI',
            'title' => 'Kompetensi Kejuruan & Pembobotan Nilai',
            'subtitle' => 'Atur bobot dinamis tugas, ujian teori, dan ujian praktik projek per mapel kejuruan, serta input capaian nilai santri secara terpadu.',
            'stats' => [
                ['icon' => 'fa-solid fa-laptop-code', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => count($myMapels), 'label' => 'Mapel Diampu'],
                ['icon' => 'fa-solid fa-users', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => count($santriNilai), 'label' => 'Santri Terdaftar'],
                ['icon' => 'fa-solid fa-sliders', 'bg' => 'bg-amber-400/20 text-amber-200', 'value' => '100%', 'label' => 'Total Bobot'],
            ]
        ];
        require_once __DIR__ . '/components/hero_header.php';
        ?>

        <div class="p-6 space-y-6">

            <!-- Flash Notification -->
            <?php if (session()->getFlashdata('notif_msg')) : ?>
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800"><?= esc(session()->getFlashdata('notif_title') ?? 'Pemberitahuan'); ?></h4>
                            <p class="text-3xs text-slate-500 mt-0.5"><?= esc(session()->getFlashdata('notif_msg')); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
                    <span><?= esc(session()->getFlashdata('error')); ?></span>
                </div>
            <?php endif; ?>

            <!-- PILIH MATA DIKLAT KEJURUAN / KOMPETENSI -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pilih Mata Diklat Kejuruan:</h3>
                    <p class="text-sm font-extrabold text-slate-800 mt-0.5 truncate">
                        <?= esc($currentMapel['nama_mapel'] ?? 'Pilih Mata Diklat'); ?>
                        <span class="text-xs font-semibold text-brand ml-1">(<?= esc($currentMapel['kode_mapel'] ?? '-'); ?>)</span>
                    </p>
                    <span class="text-3xs font-semibold text-slate-500 block mt-0.5">Jurusan: <?= esc($currentMapel['jurusan'] ?? '-'); ?> &bull; KKM: <?= esc($currentMapel['kkm'] ?? 75); ?></span>
                </div>

                <div class="w-full md:w-auto shrink-0">
                    <form method="GET" action="<?= base_url('instruktur/kompetensi'); ?>" class="w-full">
                        <select name="mapel_id" onchange="this.form.submit()" 
                                class="w-full md:max-w-md px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0066CC] truncate block">
                            <?php foreach ($allMapels as $m): ?>
                                <option value="<?= esc($m['id']); ?>" <?= ($selectedMapelId == $m['id']) ? 'selected' : ''; ?>>
                                    [<?= esc($m['kode_mapel']); ?>] <?= esc($m['nama_mapel']); ?> (<?= esc($m['jurusan']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>

            <?php if ($currentMapel): ?>
            <!-- CARD PENGATURAN BOBOT NILAI DINAMIS OLEH INSTRUKTUR -->
            <div class="bg-gradient-to-r from-blue-900 via-sky-900 to-[#073B66] text-white p-6 rounded-2xl shadow-lg border border-sky-700/50">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-1.5 max-w-xl">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 text-sky-200 text-3xs font-extrabold uppercase tracking-wider border border-white/20">
                            <i class="fa-solid fa-sliders"></i> Pengaturan Bobot Nilai Instruktur
                        </div>
                        <h3 class="text-lg font-black tracking-tight">Atur Proporsi Pembobotan Nilai Kejuruan</h3>
                        <p class="text-xs text-sky-100/90 leading-relaxed">
                            Sebagai instruktur pengampu mata diklat <strong><?= esc($currentMapel['nama_mapel']); ?></strong>, Anda dapat menentukan bobot persentase nilai tugas harian, ujian teori, dan ujian praktik berbasis projek.
                        </p>
                    </div>

                    <!-- Form Bobot Dinamis -->
                    <form action="<?= base_url('instruktur/kompetensi/bobot'); ?>" method="POST" class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/20 flex flex-wrap items-end gap-3 shrink-0">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="mapel_id" value="<?= esc($currentMapel['id']); ?>">

                        <div>
                            <label class="block text-3xs font-extrabold uppercase text-sky-200 mb-1">Bobot Tugas (%)</label>
                            <input type="number" id="inputBobotTugas" name="bobot_tugas" min="0" max="100" step="1" 
                                   value="<?= (int)$currentMapel['bobot_tugas']; ?>" oninput="checkTotalBobot()" required
                                   class="w-20 px-3 py-1.5 text-xs font-bold text-slate-900 bg-white rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-sky-400">
                        </div>

                        <div>
                            <label class="block text-3xs font-extrabold uppercase text-sky-200 mb-1">Ujian Teori (%)</label>
                            <input type="number" id="inputBobotTeori" name="bobot_teori" min="0" max="100" step="1" 
                                   value="<?= (int)$currentMapel['bobot_teori']; ?>" oninput="checkTotalBobot()" required
                                   class="w-20 px-3 py-1.5 text-xs font-bold text-slate-900 bg-white rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-sky-400">
                        </div>

                        <div>
                            <label class="block text-3xs font-extrabold uppercase text-sky-200 mb-1">Praktik Projek (%)</label>
                            <input type="number" id="inputBobotPraktik" name="bobot_praktik" min="0" max="100" step="1" 
                                   value="<?= (int)$currentMapel['bobot_praktik']; ?>" oninput="checkTotalBobot()" required
                                   class="w-24 px-3 py-1.5 text-xs font-bold text-slate-900 bg-white rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-sky-400">
                        </div>

                        <div>
                            <span class="block text-3xs font-extrabold uppercase text-sky-200 mb-1">Total:</span>
                            <span id="labelTotalBobot" class="inline-block px-2.5 py-1.5 rounded-lg text-xs font-black bg-white/20 text-white min-w-14 text-center">
                                <?= (int)($currentMapel['bobot_tugas'] + $currentMapel['bobot_teori'] + $currentMapel['bobot_praktik']); ?>%
                            </span>
                        </div>

                        <button type="submit" id="btnSimpanBobot" class="px-4 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg shadow-sm transition cursor-pointer flex items-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Bobot
                        </button>
                    </form>
                </div>
            </div>

            <!-- TABEL INPUT NILAI SANTRI PER KOMPONEN -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base flex items-center gap-2">
                            <i class="fa-solid fa-table-list text-brand"></i> Input Capaian Nilai Santri Kelas <?= esc($currentMapel['jurusan']); ?>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Ketikkan nilai tugas, ujian teori, dan ujian praktik projek. Nilai akhir dihitung otomatis berdasarkan bobot terkonfigurasi.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-2xs font-semibold">
                        <span class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200">
                            Tugas: <?= (int)$currentMapel['bobot_tugas']; ?>%
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-800 border border-indigo-200">
                            Teori: <?= (int)$currentMapel['bobot_teori']; ?>%
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Praktik Projek: <?= (int)$currentMapel['bobot_praktik']; ?>%
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[900px]">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase tracking-wider text-3xs font-bold">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-4">Identitas Santri</th>
                                <th class="py-3 px-3 text-center w-28">Nilai Tugas (<?= (int)$currentMapel['bobot_tugas']; ?>%)</th>
                                <th class="py-3 px-3 text-center w-28">Ujian Teori (<?= (int)$currentMapel['bobot_teori']; ?>%)</th>
                                <th class="py-3 px-3 text-center w-32">Praktik Projek (<?= (int)$currentMapel['bobot_praktik']; ?>%)</th>
                                <th class="py-3 px-3 text-center w-28 font-bold text-[#0066CC]">Nilai Akhir</th>
                                <th class="py-3 px-4">Catatan Projek &amp; Evaluasi</th>
                                <th class="py-3 px-3 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($santriNilai)): ?>
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400 italic">
                                        Tidak ada santri di kelas <?= esc($currentMapel['jurusan']); ?>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($santriNilai as $sn): ?>
                                <tr class="hover:bg-slate-50 transition" id="rowSantri-<?= $sn['santri_id']; ?>">
                                    <form action="<?= base_url('instruktur/kompetensi/save-nilai'); ?>" method="POST">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="santri_id" value="<?= esc($sn['santri_id']); ?>">
                                        <input type="hidden" name="mapel_id" value="<?= esc($currentMapel['id']); ?>">

                                        <td class="py-3 px-3 text-center font-mono text-slate-400"><?= $no++; ?></td>
                                        
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 text-brand font-bold text-xs flex items-center justify-center border border-slate-200 shrink-0 overflow-hidden">
                                                    <?php if (!empty($sn['foto']) && file_exists(FCPATH . 'uploads/santri/' . $sn['foto'])): ?>
                                                        <img src="<?= base_url('uploads/santri/' . $sn['foto']); ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <?= strtoupper(substr($sn['nama'], 0, 1)); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <h5 class="font-bold text-slate-900 truncate"><?= esc($sn['nama']); ?></h5>
                                                    <span class="text-3xs text-slate-500 font-mono">NIS: <?= esc($sn['nis'] ?: '-'); ?></span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-3 px-3 text-center">
                                            <input type="number" name="nilai_tugas" min="0" max="100" step="0.5" 
                                                   value="<?= esc($sn['nilai_tugas']); ?>" 
                                                   oninput="calcRowNilai(<?= $sn['santri_id']; ?>)"
                                                   id="tugas-<?= $sn['santri_id']; ?>"
                                                   class="w-20 px-2.5 py-1.5 text-center text-xs font-bold border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066CC] bg-white">
                                        </td>

                                        <td class="py-3 px-3 text-center">
                                            <input type="number" name="nilai_teori" min="0" max="100" step="0.5" 
                                                   value="<?= esc($sn['nilai_teori']); ?>" 
                                                   oninput="calcRowNilai(<?= $sn['santri_id']; ?>)"
                                                   id="teori-<?= $sn['santri_id']; ?>"
                                                   class="w-20 px-2.5 py-1.5 text-center text-xs font-bold border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066CC] bg-white">
                                        </td>

                                        <td class="py-3 px-3 text-center">
                                            <input type="number" name="nilai_praktik" min="0" max="100" step="0.5" 
                                                   value="<?= esc($sn['nilai_praktik']); ?>" 
                                                   oninput="calcRowNilai(<?= $sn['santri_id']; ?>)"
                                                   id="praktik-<?= $sn['santri_id']; ?>"
                                                   class="w-20 px-2.5 py-1.5 text-center text-xs font-bold border border-emerald-300 bg-emerald-50/30 text-emerald-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        </td>

                                        <td class="py-3 px-3 text-center">
                                            <span id="akhir-<?= $sn['santri_id']; ?>" class="inline-block px-2.5 py-1 rounded-lg text-xs font-black bg-blue-50 text-[#0066CC] border border-blue-200">
                                                <?= number_format($sn['nilai_akhir'], 1); ?>
                                            </span>
                                        </td>

                                        <td class="py-3 px-4">
                                            <input type="text" name="catatan" 
                                                   value="<?= esc($sn['catatan'] ?? ''); ?>" 
                                                   placeholder="Catatan hasil penilaian projek..." 
                                                   class="w-full px-2.5 py-1.5 text-3xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#0066CC] text-slate-700">
                                        </td>

                                        <td class="py-3 px-3 text-center">
                                            <button type="submit" class="px-3 py-1.5 bg-[#0066CC] hover:bg-[#0052A3] text-white rounded-lg text-3xs font-bold transition shadow-xs flex items-center gap-1 mx-auto cursor-pointer">
                                                <i class="fa-solid fa-check"></i> Simpan
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </main>
</div>

<script>
const bobotTugasDefault   = <?= (float)($currentMapel['bobot_tugas'] ?? 30); ?>;
const bobotTeoriDefault   = <?= (float)($currentMapel['bobot_teori'] ?? 30); ?>;
const bobotPraktikDefault = <?= (float)($currentMapel['bobot_praktik'] ?? 40); ?>;

function checkTotalBobot() {
    const t = parseFloat(document.getElementById('inputBobotTugas').value) || 0;
    const te = parseFloat(document.getElementById('inputBobotTeori').value) || 0;
    const p = parseFloat(document.getElementById('inputBobotPraktik').value) || 0;
    const total = t + te + p;

    const lbl = document.getElementById('labelTotalBobot');
    const btn = document.getElementById('btnSimpanBobot');

    lbl.textContent = total + '%';
    if (Math.abs(total - 100) < 0.01) {
        lbl.className = 'inline-block px-2.5 py-1.5 rounded-lg text-xs font-black bg-emerald-500 text-white min-w-14 text-center';
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        lbl.className = 'inline-block px-2.5 py-1.5 rounded-lg text-xs font-black bg-rose-500 text-white min-w-14 text-center';
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

function calcRowNilai(santriId) {
    const t = parseFloat(document.getElementById('tugas-' + santriId).value) || 0;
    const te = parseFloat(document.getElementById('teori-' + santriId).value) || 0;
    const p = parseFloat(document.getElementById('praktik-' + santriId).value) || 0;

    const totalBobot = bobotTugasDefault + bobotTeoriDefault + bobotPraktikDefault || 100;
    const akhir = ((t * bobotTugasDefault) + (te * bobotTeoriDefault) + (p * bobotPraktikDefault)) / totalBobot;

    const akhirEl = document.getElementById('akhir-' + santriId);
    if (akhirEl) {
        akhirEl.textContent = akhir.toFixed(1);
    }
}
</script>
</body>
</html>
