<?php 
require_once 'navbar.php'; 
$isPendamping = (session()->get('role') === 'pendamping_agama');
$routeBase = $isPendamping ? 'pendamping' : 'admin';
$hafalanUrl = base_url($routeBase . ($isPendamping ? '' : '/hafalan'));
$saveUrl = base_url($routeBase . '/hafalan/save');
?>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">

    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Catatan Setoran Hafalan & Tasmik Santri';
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => $isPendamping ? 'Portal Pendamping' : 'Portal Admin', 'url' => base_url($routeBase)],
                ['label' => 'Setoran Hafalan Santri']
            ],
            'badge' => 'Bimbingan Tahfidz & Karakter Qur\'ani Diklat RGI',
            'title' => 'Catatan Setoran Hafalan & Status Tasmik',
            'subtitle' => 'Pemantauan target hafalan Juz 30 dan surat-surat pilihan, verifikasi status tasmik, tingkat kelancaran (itqan), dan catatan tajwid pendamping agama.',
            'stats' => [
                ['icon' => 'fa-solid fa-book-quran', 'bg' => 'bg-teal-400/20 text-teal-200', 'value' => $summary['total_setoran'] ?? 0, 'label' => 'Total Setoran'],
                ['icon' => 'fa-solid fa-circle-check', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => $summary['lulus_tasmik'] ?? 0, 'label' => 'Lulus Tasmik'],
                ['icon' => 'fa-solid fa-spinner', 'bg' => 'bg-blue-400/20 text-blue-200', 'value' => $summary['sedang_proses'] ?? 0, 'label' => 'Sedang Proses'],
            ]
        ];
        require_once __DIR__ . '/components/hero_header.php';
        ?>

        <div class="p-6 space-y-6">

            <!-- Flash Notification -->
            <?php if (session()->getFlashdata('notif_msg')) : ?>
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800"><?= esc(session()->getFlashdata('notif_title') ?? 'Berhasil'); ?></h4>
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

            <!-- Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-book-open-reader text-teal-600"></i> Rekapitulasi Catatan Setoran Hafalan Santri
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau dan filter setoran harian santri yang dinilai dan dibimbing oleh pendamping agama.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full md:w-auto">
                        <!-- Filter Form -->
                        <form method="GET" action="<?= $hafalanUrl; ?>" class="flex flex-wrap items-center gap-2">
                            <!-- Filter Harian (Date Picker) -->
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl focus-within:ring-2 focus-within:ring-teal-500 focus-within:border-teal-500 transition">
                                <i class="fa-regular fa-calendar-days text-teal-600 text-xs"></i>
                                <input type="date" name="tanggal" value="<?= esc($tanggalFilter ?? ''); ?>" onchange="this.form.submit()" 
                                       title="Filter Harian / Tanggal Setoran"
                                       class="bg-transparent text-xs font-semibold text-slate-700 focus:outline-none cursor-pointer">
                            </div>

                            <select name="status" onchange="this.form.submit()" 
                                    class="px-3 py-2 text-xs border border-slate-300 rounded-xl bg-white text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="all">-- Semua Status --</option>
                                <option value="Lulus Tasmik" <?= ($statusFilter === 'Lulus Tasmik') ? 'selected' : ''; ?>>Lulus Tasmik</option>
                                <option value="Sedang Proses" <?= ($statusFilter === 'Sedang Proses') ? 'selected' : ''; ?>>Sedang Proses</option>
                                <option value="Perlu Mengulang" <?= ($statusFilter === 'Perlu Mengulang') ? 'selected' : ''; ?>>Perlu Mengulang</option>
                                <option value="Belum Tasmik" <?= ($statusFilter === 'Belum Tasmik') ? 'selected' : ''; ?>>Belum Tasmik</option>
                            </select>

                            <select name="target" onchange="this.form.submit()" 
                                    class="px-3 py-2 text-xs border border-slate-300 rounded-xl bg-white text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="all">-- Semua Target --</option>
                                <?php foreach ($targets as $tg): ?>
                                    <option value="<?= esc($tg); ?>" <?= ($targetFilter === $tg) ? 'selected' : ''; ?>><?= esc($tg); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                        <button type="button" onclick="openAddHafalanModal()" 
                                class="w-full sm:w-auto justify-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-plus"></i> <span>Catat Setoran Baru</span>
                        </button>
                    </div>
                </div>

                <!-- Daily Filter Quick Presets -->
                <div class="px-5 py-2.5 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-3xs font-extrabold uppercase text-slate-400 tracking-wider">Filter Cepat:</span>
                        <a href="<?= $hafalanUrl; ?>?tanggal=<?= date('Y-m-d'); ?>" 
                           class="px-2.5 py-1 rounded-lg text-3xs font-bold transition <?= ($tanggalFilter === date('Y-m-d')) ? 'bg-teal-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'; ?>">
                            <i class="fa-solid fa-sun text-amber-400 mr-1"></i> Hari Ini (<?= date('d M'); ?>)
                        </a>
                        <a href="<?= $hafalanUrl; ?>?tanggal=<?= date('Y-m-d', strtotime('-1 day')); ?>" 
                           class="px-2.5 py-1 rounded-lg text-3xs font-bold transition <?= ($tanggalFilter === date('Y-m-d', strtotime('-1 day'))) ? 'bg-teal-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'; ?>">
                            Kemarin
                        </a>
                        <a href="<?= $hafalanUrl; ?>" 
                           class="px-2.5 py-1 rounded-lg text-3xs font-bold transition <?= empty($tanggalFilter) ? 'bg-teal-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'; ?>">
                            Semua Tanggal
                        </a>
                    </div>

                    <?php if (!empty($tanggalFilter)): ?>
                        <div class="flex items-center gap-2 text-teal-800 text-3xs font-semibold">
                            <span><i class="fa-solid fa-filter text-teal-600"></i> Menampilkan setoran: <strong><?= date('d M Y', strtotime($tanggalFilter)); ?></strong> (<?= count($setoranList); ?> data)</span>
                            <a href="<?= $hafalanUrl; ?>" class="text-rose-600 hover:underline font-bold ml-1">Reset Filter</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[900px]">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase tracking-wider text-3xs font-bold">
                            <tr>
                                <th class="py-3 px-3 text-center w-12">No</th>
                                <th class="py-3 px-4">Nama Santri</th>
                                <th class="py-3 px-3">Jurusan</th>
                                <th class="py-3 px-3">Target Hafalan</th>
                                <th class="py-3 px-4">Surat / Ayat Disetor</th>
                                <th class="py-3 px-3 text-center">Status Tasmik</th>
                                <th class="py-3 px-3 text-center">Kelancaran (Itqan)</th>
                                <th class="py-3 px-4">Catatan Tajwid Pendamping</th>
                                <th class="py-3 px-3 text-center">Tgl &amp; Nilai</th>
                                <th class="py-3 px-3 text-center w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($setoranList)): ?>
                                <tr>
                                    <td colspan="10" class="py-10 text-center text-slate-400 italic">
                                        <i class="fa-solid fa-book-quran text-3xl mb-2 block text-slate-300"></i>
                                        <span>Belum ada catatan setoran hafalan yang cocok dengan filter.</span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($setoranList as $hf): ?>
                                <?php
                                    $tasmikBadge = [
                                        'Lulus Tasmik'    => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'Sedang Proses'   => 'bg-blue-50 text-blue-800 border-blue-200',
                                        'Perlu Mengulang' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'Belum Tasmik'    => 'bg-slate-100 text-slate-700 border-slate-200',
                                    ][$hf['status_tasmik']] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-3 text-center font-mono text-slate-400"><?= $no++; ?></td>
                                    
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-teal-50 text-teal-700 font-bold text-xs flex items-center justify-center border border-teal-200 shrink-0 overflow-hidden">
                                                <?php if (!empty($hf['foto_santri']) && file_exists(FCPATH . 'uploads/santri/' . $hf['foto_santri'])): ?>
                                                    <img src="<?= base_url('uploads/santri/' . $hf['foto_santri']); ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <?= strtoupper(substr($hf['nama_santri'], 0, 1)); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="min-w-0">
                                                <h5 class="font-bold text-slate-900 truncate"><?= esc($hf['nama_santri']); ?></h5>
                                                <span class="text-3xs text-slate-500 font-mono">NIS: <?= esc($hf['nis'] ?: '-'); ?></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3 px-3">
                                        <span class="text-3xs font-semibold text-slate-600 block"><?= esc($hf['jurusan']); ?></span>
                                    </td>

                                    <td class="py-3 px-3 font-semibold text-teal-800">
                                        <?= esc($hf['target_hafalan']); ?>
                                    </td>

                                    <td class="py-3 px-4 font-bold text-slate-800">
                                        <?= esc($hf['surat_ayat']); ?>
                                    </td>

                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-3xs font-bold border <?= $tasmikBadge; ?>">
                                            <?= esc($hf['status_tasmik']); ?>
                                        </span>
                                    </td>

                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-3xs font-semibold bg-slate-100 text-slate-700">
                                            <?= esc($hf['kelancaran']); ?>
                                        </span>
                                    </td>

                                    <td class="py-3 px-4 text-3xs text-slate-600 max-w-xs leading-relaxed">
                                        <p class="line-clamp-2"><?= esc($hf['catatan_tajwid'] ?: '-'); ?></p>
                                        <span class="text-slate-400 mt-0.5 block">Oleh: <?= esc($hf['nama_pendamping'] ?? 'Pendamping Agama'); ?></span>
                                    </td>

                                    <td class="py-3 px-3 text-center">
                                        <span class="font-black text-xs text-slate-800 block"><?= esc($hf['nilai']); ?></span>
                                        <span class="text-3xs text-slate-400"><?= date('d/m/y', strtotime($hf['tanggal_setoran'])); ?></span>
                                    </td>

                                    <td class="py-3 px-3 text-center">
                                        <form action="<?= base_url($routeBase . '/hafalan/delete/' . $hf['id']); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan setoran ini?')" class="inline">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition" title="Hapus Catatan">
                                                <i class="fa-regular fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ================= MODAL CATAT SETORAN BARU ================= -->
<div id="modalAddHafalan" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div onclick="closeAddHafalanModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

    <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden z-10 border border-slate-200 my-8">
        <div class="h-2.5 bg-gradient-to-r from-teal-600 to-emerald-600"></div>

        <form action="<?= $saveUrl; ?>" method="POST" class="p-6 space-y-4">
            <?= csrf_field(); ?>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-book-quran text-teal-600"></i> Catat Setoran Hafalan &amp; Bimbingan Tajwid
                </h3>
                <button type="button" onclick="closeAddHafalanModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div>
                <label class="block text-3xs font-bold text-slate-700 mb-1">Pilih Santri *</label>
                <select name="santri_id" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white font-medium">
                    <option value="">-- Pilih Santri E-RGI --</option>
                    <?php foreach ($santriList as $st): ?>
                        <option value="<?= esc($st['id']); ?>"><?= esc($st['nama']); ?> (NIS: <?= esc($st['nis'] ?: '-'); ?> - <?= esc($st['kelas']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Target Hafalan *</label>
                    <select name="target_hafalan" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white font-medium">
                        <?php foreach ($targets as $tg): ?>
                            <option value="<?= esc($tg); ?>"><?= esc($tg); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Surat / Ayat yang Disetor *</label>
                    <input type="text" name="surat_ayat" placeholder="Contoh: An-Naba: 1-40" required 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Status Tasmik *</label>
                    <select name="status_tasmik" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white font-medium">
                        <option value="Lulus Tasmik">Lulus Tasmik</option>
                        <option value="Sedang Proses" selected>Sedang Proses</option>
                        <option value="Perlu Mengulang">Perlu Mengulang</option>
                        <option value="Belum Tasmik">Belum Tasmik</option>
                    </select>
                </div>

                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Tingkat Kelancaran (Itqan) *</label>
                    <select name="kelancaran" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white font-medium">
                        <option value="Mumtaz (Sangat Lancar)">Mumtaz (Sangat Lancar)</option>
                        <option value="Jayyid Jiddan (Lancar)" selected>Jayyid Jiddan (Lancar)</option>
                        <option value="Jayyid (Cukup Lancar)">Jayyid (Cukup Lancar)</option>
                        <option value="Maqbul (Perlu Murajaah)">Maqbul (Perlu Murajaah)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Nilai Angka (0-100)</label>
                    <input type="number" name="nilai" value="85" min="0" max="100" required 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-center">
                </div>

                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Tanggal Setoran</label>
                    <input type="date" name="tanggal_setoran" value="<?= date('Y-m-d'); ?>" required 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div>
                <label class="block text-3xs font-bold text-slate-700 mb-1">Catatan Tajwid dari Pendamping Agama *</label>
                <textarea name="catatan_tajwid" rows="3" required placeholder="Catatan makharijul huruf, mad thabi'i, ghunnah, sifat huruf, dan waqaf ibtida..." 
                          class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeAddHafalanModal()" class="px-4 py-2 border border-slate-300 text-xs font-semibold rounded-xl text-slate-700 hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm cursor-pointer">
                    Simpan Catatan Setoran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddHafalanModal() {
    document.getElementById('modalAddHafalan').classList.remove('hidden');
}
function closeAddHafalanModal() {
    document.getElementById('modalAddHafalan').classList.add('hidden');
}
</script>
</body>
</html>
