<?php require_once 'navbar.php'; ?>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">

    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Manajemen Mapel & Instruktur Kejuruan';
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => 'Portal Admin', 'url' => base_url('admin')],
                ['label' => 'Mapel & Instruktur Kejuruan']
            ],
            'badge' => 'Kurikulum Vokasi E-RGI Syiar Gemilang',
            'title' => 'Pengelolaan Mapel & Instruktur Kejuruan Dinamis',
            'subtitle' => 'Konfigurasi mata pelajaran kejuruan per jurusan, penugasan instruktur pengampu, dan pengaturan pembobotan nilai (Tugas, Teori, dan Praktik Projek).',
            'stats' => [
                ['icon' => 'fa-solid fa-book-bookmark', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => count($mapels), 'label' => 'Total Mapel'],
                ['icon' => 'fa-solid fa-graduation-cap', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => count($daftarJurusan), 'label' => 'Jurusan'],
                ['icon' => 'fa-solid fa-chalkboard-user', 'bg' => 'bg-amber-400/20 text-amber-200', 'value' => count($instrukturList), 'label' => 'Instruktur'],
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
                            <i class="fa-solid fa-layer-group text-brand"></i> Daftar Mapel / Kompetensi Kejuruan
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola instruktur pengampu dan bobot evaluasi kompetensi tugas, teori, serta praktik.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Filter Jurusan -->
                        <form method="GET" action="<?= base_url('admin/mapel'); ?>" class="flex items-center gap-2">
                            <select name="jurusan" onchange="this.form.submit()" 
                                    class="px-3 py-2 text-xs border border-slate-300 rounded-xl bg-white text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-[#0066CC]">
                                <option value="all">-- Semua Jurusan --</option>
                                <?php foreach ($daftarJurusan as $j): ?>
                                    <option value="<?= esc($j); ?>" <?= ($filterJurusan === $j) ? 'selected' : ''; ?>><?= esc($j); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                        <button type="button" onclick="openAddMapelModal()" 
                                class="px-4 py-2 bg-[#0066CC] hover:bg-[#0052A3] text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-plus"></i> Tambah Mapel Kejuruan
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[850px]">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase tracking-wider text-3xs font-bold">
                            <tr>
                                <th class="py-3 px-3 text-center w-12">No</th>
                                <th class="py-3 px-3 w-28">Kode Mapel</th>
                                <th class="py-3 px-4">Nama Mapel Kejuruan</th>
                                <th class="py-3 px-3">Jurusan</th>
                                <th class="py-3 px-4">Instruktur Pengampu</th>
                                <th class="py-3 px-4 text-center">Bobot Penilaian Dinamis</th>
                                <th class="py-3 px-3 text-center w-16">KKM</th>
                                <th class="py-3 px-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($mapels)): ?>
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400 italic">
                                        Belum ada mata pelajaran kejuruan yang terdaftar.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($mapels as $m): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-3 text-center font-mono text-slate-400"><?= $no++; ?></td>
                                    
                                    <td class="py-3 px-3 font-mono font-bold text-brand uppercase">
                                        <?= esc($m['kode_mapel']); ?>
                                    </td>

                                    <td class="py-3 px-4">
                                        <h5 class="font-bold text-slate-900"><?= esc($m['nama_mapel']); ?></h5>
                                        <p class="text-3xs text-slate-500 line-clamp-1 mt-0.5"><?= esc($m['deskripsi'] ?: '-'); ?></p>
                                    </td>

                                    <td class="py-3 px-3">
                                        <span class="inline-block px-2.5 py-1 rounded-full text-3xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <?= esc($m['jurusan']); ?>
                                        </span>
                                    </td>

                                    <td class="py-3 px-4">
                                        <?php if (!empty($m['nama_instruktur'])): ?>
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-3xs shrink-0">
                                                    <i class="fa-solid fa-chalkboard-user"></i>
                                                </div>
                                                <span class="font-bold text-slate-800 text-xs truncate"><?= esc($m['nama_instruktur']); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-3xs text-rose-500 font-semibold italic">Belum Ditugaskan</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="py-3 px-4 text-center">
                                        <div class="inline-flex items-center gap-1 text-3xs font-mono font-bold">
                                            <span class="px-2 py-0.5 rounded bg-sky-50 text-sky-800 border border-sky-200" title="Bobot Tugas">
                                                Tugas: <?= (int)$m['bobot_tugas']; ?>%
                                            </span>
                                            <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-800 border border-indigo-200" title="Bobot Teori">
                                                Teori: <?= (int)$m['bobot_teori']; ?>%
                                            </span>
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200" title="Bobot Praktik Projek">
                                                Praktik: <?= (int)$m['bobot_praktik']; ?>%
                                            </span>
                                        </div>
                                    </td>

                                    <td class="py-3 px-3 text-center font-bold text-slate-700">
                                        <?= esc($m['kkm']); ?>
                                    </td>

                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" onclick='openEditMapelModal(<?= json_encode($m); ?>)' class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-3xs font-bold border border-slate-200 transition cursor-pointer" title="Edit Mapel & Bobot">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                            <form action="<?= base_url('admin/mapel/delete/' . $m['id']); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mapel ini?')" class="inline">
                                                <?= csrf_field(); ?>
                                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg transition" title="Hapus Mapel">
                                                    <i class="fa-regular fa-trash-can text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
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

<!-- ================= MODAL TAMBAH MAPEL ================= -->
<div id="modalAddMapel" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div onclick="closeAddMapelModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

    <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden z-10 border border-slate-200 my-8">
        <div class="h-2.5 bg-gradient-to-r from-[#0066CC] to-[#073B66]"></div>

        <form action="<?= base_url('admin/mapel/save'); ?>" method="POST" class="p-6 space-y-4">
            <?= csrf_field(); ?>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-brand"></i> Tambah Mapel Kejuruan Baru
                </h3>
                <button type="button" onclick="closeAddMapelModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Kode Mapel *</label>
                    <input type="text" name="kode_mapel" placeholder="Contoh: TKJ-03" required 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand font-mono uppercase">
                </div>
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">KKM Kelulusan</label>
                    <input type="number" name="kkm" value="75" min="50" max="100" required 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand text-center font-bold">
                </div>
            </div>

            <div>
                <label class="block text-3xs font-bold text-slate-700 mb-1">Nama Mata Pelajaran / Kompetensi *</label>
                <input type="text" name="nama_mapel" placeholder="Contoh: Keamanan Siber & Firewall" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand font-semibold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Pilih Jurusan Kejuruan *</label>
                    <select name="jurusan" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand bg-white font-medium">
                        <?php foreach ($daftarJurusan as $j): ?>
                            <option value="<?= esc($j); ?>"><?= esc($j); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Tugaskan Instruktur</label>
                    <select name="instruktur_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand bg-white font-medium">
                        <option value="">-- Pilih Instruktur Pengampu --</option>
                        <?php foreach ($instrukturList as $ins): ?>
                            <option value="<?= esc($ins['id']); ?>"><?= esc($ins['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Pembobotan Nilai -->
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <span class="block text-3xs font-bold text-slate-700 uppercase">Pengaturan Bobot Evaluasi (Total Harus 100%):</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-3xs text-slate-500 mb-0.5">Tugas (%)</label>
                        <input type="number" name="bobot_tugas" value="30" min="0" max="100" required
                               class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg text-center font-bold bg-white">
                    </div>
                    <div>
                        <label class="block text-3xs text-slate-500 mb-0.5">Ujian Teori (%)</label>
                        <input type="number" name="bobot_teori" value="30" min="0" max="100" required
                               class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg text-center font-bold bg-white">
                    </div>
                    <div>
                        <label class="block text-3xs text-slate-500 mb-0.5">Praktik Projek (%)</label>
                        <input type="number" name="bobot_praktik" value="40" min="0" max="100" required
                               class="w-full px-2 py-1.5 text-xs border border-emerald-300 text-emerald-900 bg-emerald-50 rounded-lg text-center font-bold">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-3xs font-bold text-slate-700 mb-1">Deskripsi Silabus / Materi</label>
                <textarea name="deskripsi" rows="2" placeholder="Capaian pembelajaran dan standar industri..." 
                          class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeAddMapelModal()" class="px-4 py-2 border border-slate-300 text-xs font-semibold rounded-xl text-slate-700 hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#0066CC] hover:bg-[#0052A3] text-white text-xs font-bold rounded-xl shadow-sm cursor-pointer">
                    Simpan Mapel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT MAPEL ================= -->
<div id="modalEditMapel" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div onclick="closeEditMapelModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

    <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden z-10 border border-slate-200 my-8">
        <div class="h-2.5 bg-gradient-to-r from-emerald-600 to-[#0066CC]"></div>

        <form id="formEditMapel" action="" method="POST" class="p-6 space-y-4">
            <?= csrf_field(); ?>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Edit Mapel &amp; Bobot Penilaian
                </h3>
                <button type="button" onclick="closeEditMapelModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Kode Mapel *</label>
                    <input type="text" id="editKodeMapel" name="kode_mapel" required 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand font-mono uppercase">
                </div>
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">KKM Kelulusan</label>
                    <input type="number" id="editKkm" name="kkm" min="50" max="100" required 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand text-center font-bold">
                </div>
            </div>

            <div>
                <label class="block text-3xs font-bold text-slate-700 mb-1">Nama Mata Pelajaran *</label>
                <input type="text" id="editNamaMapel" name="nama_mapel" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand font-semibold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Jurusan Kejuruan *</label>
                    <select id="editJurusan" name="jurusan" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand bg-white font-medium">
                        <?php foreach ($daftarJurusan as $j): ?>
                            <option value="<?= esc($j); ?>"><?= esc($j); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-3xs font-bold text-slate-700 mb-1">Instruktur Pengampu</label>
                    <select id="editInstrukturId" name="instruktur_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand bg-white font-medium">
                        <option value="">-- Pilih Instruktur Pengampu --</option>
                        <?php foreach ($instrukturList as $ins): ?>
                            <option value="<?= esc($ins['id']); ?>"><?= esc($ins['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Pembobotan Nilai Dinamis -->
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <span class="block text-3xs font-bold text-slate-700 uppercase">Pembobotan Nilai Dinamis (%):</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-3xs text-slate-500 mb-0.5">Tugas (%)</label>
                        <input type="number" id="editBobotTugas" name="bobot_tugas" min="0" max="100" required
                               class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg text-center font-bold bg-white">
                    </div>
                    <div>
                        <label class="block text-3xs text-slate-500 mb-0.5">Teori (%)</label>
                        <input type="number" id="editBobotTeori" name="bobot_teori" min="0" max="100" required
                               class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg text-center font-bold bg-white">
                    </div>
                    <div>
                        <label class="block text-3xs text-slate-500 mb-0.5">Praktik (Projek) (%)</label>
                        <input type="number" id="editBobotPraktik" name="bobot_praktik" min="0" max="100" required
                               class="w-full px-2 py-1.5 text-xs border border-emerald-300 text-emerald-900 bg-emerald-50 rounded-lg text-center font-bold">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-3xs font-bold text-slate-700 mb-1">Deskripsi Silabus</label>
                <textarea id="editDeskripsi" name="deskripsi" rows="2" 
                          class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditMapelModal()" class="px-4 py-2 border border-slate-300 text-xs font-semibold rounded-xl text-slate-700 hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm cursor-pointer">
                    Perbarui Mapel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddMapelModal() {
    document.getElementById('modalAddMapel').classList.remove('hidden');
}
function closeAddMapelModal() {
    document.getElementById('modalAddMapel').classList.add('hidden');
}

function openEditMapelModal(data) {
    document.getElementById('formEditMapel').action = '<?= base_url('admin/mapel/update'); ?>/' + data.id;
    document.getElementById('editKodeMapel').value = data.kode_mapel || '';
    document.getElementById('editNamaMapel').value = data.nama_mapel || '';
    document.getElementById('editJurusan').value = data.jurusan || '';
    document.getElementById('editInstrukturId').value = data.instruktur_id || '';
    document.getElementById('editBobotTugas').value = parseInt(data.bobot_tugas) || 30;
    document.getElementById('editBobotTeori').value = parseInt(data.bobot_teori) || 30;
    document.getElementById('editBobotPraktik').value = parseInt(data.bobot_praktik) || 40;
    document.getElementById('editKkm').value = data.kkm || 75;
    document.getElementById('editDeskripsi').value = data.deskripsi || '';

    document.getElementById('modalEditMapel').classList.remove('hidden');
}
function closeEditMapelModal() {
    document.getElementById('modalEditMapel').classList.add('hidden');
}
</script>
</body>
</html>
