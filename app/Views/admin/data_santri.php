<?php require_once 'navbar.php'; ?>

<?php
$data_siswa  = $data_siswa ?? [];
$total_siswa = $total_siswa ?? count($data_siswa);
$jurusanColors = [
    'Desain Grafis'                => ['badge' => 'bg-red-50 text-red-700 border-red-200',         'dot' => 'bg-red-500'],
    'Teknik Komputer dan Jaringan' => ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
    'Aplikasi Perkantoran'         => ['badge' => 'bg-orange-50 text-orange-700 border-orange-200', 'dot' => 'bg-orange-500'],
    'Tata Busana'                  => ['badge' => 'bg-yellow-50 text-yellow-800 border-yellow-300', 'dot' => 'bg-yellow-500'],
    'Otomotif'                     => ['badge' => 'bg-slate-100 text-slate-900 border-slate-300',   'dot' => 'bg-slate-900'],
    'Fotografi dan Videografi'     => ['badge' => 'bg-blue-50 text-blue-700 border-blue-200',       'dot' => 'bg-blue-600'],
];
?>

<?php
/* Tampilan saja: ikon & warna tinta tiap jurusan untuk badge tile. Tidak menyentuh data/backend. */
$jurusanIcons = [
    'Desain Grafis'                => ['icon' => 'fa-pen-nib',        'ink' => 'text-white'],
    'Teknik Komputer dan Jaringan' => ['icon' => 'fa-network-wired',  'ink' => 'text-emerald-950'],
    'Aplikasi Perkantoran'         => ['icon' => 'fa-table-cells',    'ink' => 'text-orange-950'],
    'Tata Busana'                  => ['icon' => 'fa-scissors',       'ink' => 'text-yellow-950'],
    'Otomotif'                     => ['icon' => 'fa-gears',          'ink' => 'text-white'],
    'Fotografi dan Videografi'     => ['icon' => 'fa-camera-retro',   'ink' => 'text-white'],
];
$jurusanFallback = ['icon' => 'fa-graduation-cap', 'ink' => 'text-white'];
$jurusanJs = [];
foreach ($jurusanColors as $namaJurusan => $c) {
    $i = $jurusanIcons[$namaJurusan] ?? $jurusanFallback;
    $jurusanJs[$namaJurusan] = ['icon' => $i['icon'], 'tile' => $c['dot'] . ' ' . $i['ink']];
}
?>

<style>
    /* Satu gerak saja: modal muncul setelah diklik. Mati bila pengguna memilih reduced motion. */
    @keyframes smPop { from { opacity: 0; transform: translateY(8px) scale(.985); } to { opacity: 1; transform: none; } }
    .sm-pop { animation: smPop .16s ease-out; }
    @media (prefers-reduced-motion: reduce) {
        .sm-pop { animation: none; }
        .sm-bar { transition: none !important; }
    }
</style>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">
        
    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Data Santri & Akademik';
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => 'Portal Admin', 'url' => base_url('admin')],
                ['label' => 'Data Santri & Akademik']
            ],
            'badge' => 'Lembaga Pendidikan E-RGI Syiar Gemilang',
            'title' => 'Manajemen Data & Akademik Santri',
            'subtitle' => 'Kelola direktori data induk santri, pantau nilai 4 mata pelajaran kejuruan, skor adab & akhlaq, serta persentase kehadiran per santri.',
            'stats' => [
                ['icon' => 'fa-solid fa-users', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => $total_siswa, 'label' => 'Santri Terdaftar'],
                ['icon' => 'fa-solid fa-book-open', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => $total_mapel ?? 4, 'label' => 'Mata Diklat'],
                ['icon' => 'fa-solid fa-chart-line', 'bg' => 'bg-amber-400/20 text-amber-200', 'value' => $rata_rata, 'label' => 'Rata-rata Nilai'],
                ['icon' => 'fa-solid fa-clipboard-check', 'bg' => 'bg-indigo-400/20 text-indigo-200', 'value' => $rata_kehadiran . '%', 'label' => 'Rata Presensi'],
            ]
        ];
        require_once __DIR__ . '/components/hero_header.php';
        ?>

        <div class="p-4 sm:p-6 space-y-5">

        <!-- Flashdata Notification -->
        <?php if (session()->getFlashdata('notif_msg')) : ?>
            <?php
                $type = session()->getFlashdata('notif_type') ?? 'add';
                $bgClass = $type === 'add' ? 'bg-[#DCFCE7] border-[#86EFAC] text-[#166534]'
                         : ($type === 'edit' ? 'bg-amber-50 border-amber-200 text-amber-900'
                         : 'bg-[#FEE2E2] border-[#FCA5A5] text-[#991B1B]');
                $iconBg  = $type === 'add' ? 'bg-emerald-600'
                         : ($type === 'edit' ? 'bg-amber-500' : 'bg-rose-600');
                $iconFa  = $type === 'add' ? 'fa-circle-check'
                         : ($type === 'edit' ? 'fa-pen-to-square' : 'fa-trash-can');
            ?>
            <div id="ci4FlashAlert" role="alert" class="rounded-lg border p-3.5 flex items-start justify-between gap-3 <?= $bgClass; ?>">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-md <?= $iconBg; ?> text-white flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid <?= $iconFa; ?>"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold"><?= esc(session()->getFlashdata('notif_title') ?? 'Notifikasi Sistem'); ?></h4>
                        <p class="text-xs mt-0.5"><?= esc(session()->getFlashdata('notif_msg')); ?></p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('ci4FlashAlert').remove()" aria-label="Tutup notifikasi"
                        class="w-9 h-9 -m-1 rounded-md flex items-center justify-center opacity-60 hover:opacity-100 hover:bg-black/5 transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/50">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Ringkasan: satu strip polos, angka jadi pusat perhatian -->
        <section aria-label="Ringkasan data santri" class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-slate-200 border border-slate-200 rounded-xl overflow-hidden">
            <div class="bg-white px-5 py-4">
                <p class="text-xs font-medium text-slate-600">Total Santri</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900 tabular-nums leading-none"><?= esc($total_siswa); ?></h3>
                <span class="block mt-1.5 text-2xs text-slate-500">Terdaftar Aktif</span>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-xs font-medium text-slate-600">Aspek Penilaian</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900 tabular-nums leading-none">6 Aspek</h3>
                <span class="block mt-1.5 text-2xs text-slate-500"><?= esc($total_mapel); ?> Mata Diklat + 1 Adab</span>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-xs font-medium text-slate-600">Rata-Rata Terbobot</p>
                <h3 class="mt-1 text-3xl font-extrabold text-brand tabular-nums leading-none"><?= esc($rata_rata); ?></h3>
                <span class="block mt-1.5 text-2xs text-slate-500">80% Akademik + 20% Adab</span>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-xs font-medium text-slate-600">Rata-Rata Kehadiran</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900 tabular-nums leading-none"><?= esc($rata_kehadiran); ?>%</h3>
                <span class="block mt-1.5 text-2xs text-slate-500">Tingkat Disiplin Tinggi</span>
            </div>
        </section>

        <!-- Tabel Data Santri & Fitur Pencarian -->
        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            
            <!-- Judul + aksi utama -->
            <div class="px-5 pt-5 pb-4 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-table-list text-brand"></i> Data Induk & Akademik Santri
                    </h3>
                    <p class="text-xs text-slate-600 mt-1 max-w-xl">
                        Daftar lengkap santri Rumah Gemilang Indonesia: identitas, NIS, cabang, kelas, angkatan, status, dan nilai.
                    </p>
                </div>

                <!-- Action Buttons: Export, Cetak, Tambah Santri -->
                <div class="flex flex-wrap items-center gap-2">
                    <a id="btnExportSantri" href="<?= base_url('admin/export-santri-csv'); ?>" 
                       title="Download data santri dalam format Excel / CSV"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3.5 py-2.5 rounded-lg inline-flex items-center justify-center gap-2 transition shrink-0 shadow-xs">
                        <i class="fa-solid fa-file-excel"></i> Export Excel/CSV
                    </a>

                    <a id="btnCetakSantri" href="<?= base_url('admin/cetak-santri'); ?>" target="_blank"
                       title="Cetak daftar santri atau simpan sebagai PDF A4 resmi"
                       class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-3.5 py-2.5 rounded-lg inline-flex items-center justify-center gap-2 transition shrink-0 shadow-xs">
                        <i class="fa-solid fa-print"></i> Cetak Daftar Santri
                    </a>

                    <!-- Button Tambah Santri -->
                    <a href="<?= base_url('admin/add'); ?>" 
                       class="bg-brand hover:bg-brand-dark text-white text-xs font-semibold px-4 py-2.5 rounded-lg inline-flex items-center justify-center gap-2 transition shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/50 focus-visible:ring-offset-2 shadow-xs">
                        <i class="fa-solid fa-user-plus"></i> Tambah Santri
                    </a>
                </div>
            </div>

            <!-- Pencarian & filter -->
            <div class="px-5 pb-4 flex flex-col md:flex-row md:items-center gap-2.5">
                
                <!-- Search Input Box -->
                <div class="relative w-full md:flex-1 md:max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" id="searchInput" onkeyup="filterSantriTable()" 
                           aria-label="Cari santri"
                           placeholder="Cari nama, NIS, kelas, cabang..."
                           class="w-full pl-8 pr-8 py-2.5 text-xs border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition">
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()" aria-label="Hapus kata kunci"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 hidden">
                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                    </button>
                </div>

                <!-- Filter Dropdown Jurusan -->
                <div class="relative w-full md:w-56">
                    <select id="filterJurusan" onchange="filterSantriTable()" aria-label="Filter jurusan"
                            class="w-full px-3 py-2.5 text-xs border border-slate-300 rounded-lg bg-white text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition appearance-none">
                        <option value="">Semua Jurusan</option>
                        <option value="Teknik Komputer dan Jaringan">Teknik Komputer & Jaringan</option>
                        <option value="Tata Busana">Tata Busana</option>
                        <option value="Fotografi dan Videografi">Fotografi & Videografi</option>
                        <option value="Desain Grafis">Desain Grafis</option>
                        <option value="Otomotif">Otomotif</option>
                        <option value="Aplikasi Perkantoran">Aplikasi Perkantoran</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-2xs"></i>
                    </div>
                </div>

                <!-- Filter Status Siswa -->
                <div class="relative w-full md:w-40">
                    <select id="filterStatus" onchange="filterSantriTable()" aria-label="Filter status"
                            class="w-full px-3 py-2.5 text-xs border border-slate-300 rounded-lg bg-white text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition appearance-none">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Alumni">Alumni</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-2xs"></i>
                    </div>
                </div>
            </div>

            <!-- Active Search Summary -->
            <div id="searchStatsBar" class="flex items-center justify-between gap-3 text-2xs text-slate-600 font-medium px-5 py-2.5 bg-slate-50 border-y border-slate-200">
                <span id="searchResultCount" aria-live="polite">Menampilkan seluruh <strong><?= count($data_siswa); ?></strong> santri</span>
                <button type="button" onclick="resetAllFilters()" id="resetFilterBtn" class="text-brand hover:underline font-semibold hidden">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset Pencarian
                </button>
            </div>

            <!-- Table Wrapper -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-full md:min-w-[1040px] text-left border-collapse" id="santriMainTable">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-semibold text-slate-600">
                            <th class="hidden md:table-cell py-3 px-4 w-14 text-center">No</th>
                            <th class="py-3 px-3 sm:px-4">Nama & Foto</th>
                            <th class="py-3 px-2 sm:px-4 text-center">NIS</th>
                            <th class="hidden md:table-cell py-3 px-4">Cabang</th>
                            <th class="hidden md:table-cell py-3 px-4">Kelas</th>
                            <th class="hidden md:table-cell py-3 px-4">Angkatan</th>
                            <th class="hidden md:table-cell py-3 px-4">Status Siswa</th>
                            <th class="py-3 px-2 sm:px-4 text-center bg-blue-50/60 text-[#0052A3] w-36 sm:w-48">Nilai & Kehadiran</th>
                            <th class="py-3 px-2 sm:px-4 text-center w-20 sm:w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100" id="santriTableBody">
                        <?php foreach ($data_siswa as $index => $s): ?>
                        <?php
                            $pctHadir = (int)($s['kehadiran'] ?? 95);
                            $fotoPath = !empty($s['foto']) && file_exists(FCPATH . 'uploads/santri/' . $s['foto']) 
                                ? base_url('uploads/santri/' . $s['foto']) 
                                : null;
                            $initial = strtoupper(mb_substr(trim($s['nama']), 0, 1));
                            $jCol = $jurusanColors[$s['kelas']] ?? ['badge' => 'bg-blue-50 text-[#0052A3] border-blue-200', 'dot' => 'bg-[#0066CC]'];
                            $isAktif = ($s['status'] ?? 'Aktif') === 'Aktif';
                        ?>
                        <?php $jUi = $jurusanIcons[$s['kelas']] ?? $jurusanFallback; ?>
                        <tr class="santri-data-row bg-white hover:bg-brand-light/30 transition-colors">
                            <!-- No -->
                            <td class="hidden md:table-cell py-3.5 px-4 text-xs font-medium text-slate-500 text-center tabular-nums col-id">
                                <?= $index + 1; ?>
                            </td>

                            <!-- Foto & Nama Santri -->
                            <td class="py-3.5 px-3 sm:px-4 col-nama" data-nama="<?= esc(strtolower($s['nama'])); ?>">
                                <div class="flex items-center gap-2.5 sm:gap-3">
                                    <?php if ($fotoPath): ?>
                                        <img src="<?= esc($fotoPath); ?>" alt="<?= esc($s['nama']); ?>" class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg object-cover shrink-0">
                                    <?php else: ?>
                                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-brand-light text-brand font-extrabold text-xs sm:text-sm flex items-center justify-center shrink-0">
                                            <?= $initial; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 text-xs sm:text-sm leading-tight truncate"><?= esc($s['nama']); ?></p>
                                        <span class="text-2xs text-slate-500 tabular-nums">ID: #<?= esc($s['id']); ?></span>
                                    </div>
                                </div>
                            </td>

                            <!-- NIS -->
                            <td class="py-3.5 px-2 sm:px-4 text-center col-nis" data-nis="<?= esc(strtolower($s['nis'] ?? '')); ?>">
                                <span class="text-xs font-semibold text-slate-700 tabular-nums">
                                    <?= esc($s['nis'] ?? '-'); ?>
                                </span>
                            </td>

                            <!-- Cabang -->
                            <td class="hidden md:table-cell py-3.5 px-4 text-xs text-slate-700 font-medium col-cabang" data-cabang="<?= esc(strtolower($s['cabang'] ?? '')); ?>">
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                    <i class="fa-solid fa-location-dot text-slate-400 text-2xs"></i>
                                    <?= esc($s['cabang'] ?? 'Kampus Utama Depok'); ?>
                                </span>
                            </td>

                            <!-- Kelas: badge tile ikon. Klik untuk memfilter jurusan ini. -->
                            <td class="hidden md:table-cell py-3.5 px-4 text-xs font-medium col-kelas" data-kelas="<?= esc(strtolower($s['kelas'])); ?>">
                                <button type="button"
                                        onclick='filterByJurusan(<?= htmlspecialchars(json_encode($s['kelas']), ENT_QUOTES, "UTF-8"); ?>)'
                                        title="Tampilkan hanya jurusan <?= esc($s['kelas']); ?>"
                                        class="inline-flex items-stretch max-w-full rounded-md overflow-hidden border border-slate-300 bg-white shadow-[2px_2px_0_0_#cbd5e1] hover:shadow-[2px_2px_0_0_#94a3b8] hover:border-slate-400 transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/50 focus-visible:ring-offset-1">
                                    <span class="w-8 flex items-center justify-center shrink-0 <?= $jCol['dot']; ?> <?= $jUi['ink']; ?>">
                                        <i class="fa-solid <?= $jUi['icon']; ?> text-xs"></i>
                                    </span>
                                    <span class="px-2.5 py-1.5 text-xs font-semibold text-slate-800 whitespace-nowrap"><?= esc($s['kelas']); ?></span>
                                </button>
                            </td>

                            <!-- Angkatan -->
                            <td class="hidden md:table-cell py-3.5 px-4 text-xs font-medium text-slate-700 tabular-nums whitespace-nowrap col-angkatan" data-angkatan="<?= esc(strtolower($s['angkatan'] ?? '')); ?>">
                                <?= esc($s['angkatan'] ?? 'Angkatan 35'); ?>
                            </td>

                            <!-- Status Siswa -->
                            <td class="hidden md:table-cell py-3.5 px-4 col-status" data-status="<?= $isAktif ? 'aktif' : 'alumni'; ?>">
                                <?php if ($isAktif): ?>
                                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-800 whitespace-nowrap">
                                        <span class="w-2 h-2 rounded-sm bg-emerald-500"></span> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-600 whitespace-nowrap">
                                        <span class="w-2 h-2 rounded-sm bg-slate-400"></span> Alumni
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Nilai & Kehadiran (Card Modal Trigger) -->
                            <td class="py-3.5 px-2 sm:px-4 bg-blue-50/30">
                                <button type="button" 
                                        onclick='openDetailNilaiModal(<?= htmlspecialchars(json_encode($s), ENT_QUOTES, "UTF-8"); ?>)'
                                        title="Buka Kartu Rapor & Detail Nilai (Mata Diklat + Adab)"
                                        aria-label="Buka rapor <?= esc($s['nama']); ?>"
                                        class="group mx-auto w-full max-w-[170px] flex items-center gap-2 sm:gap-3 px-2 sm:px-2.5 py-1.5 rounded-lg text-left hover:bg-brand transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/50">
                                    <span class="text-lg sm:text-xl font-extrabold text-brand group-hover:text-white tabular-nums leading-none"><?= number_format($s['rata_rata'], 2); ?></span>
                                    <span class="flex-1 min-w-0">
                                        <span class="flex items-center justify-between text-2xs text-slate-600 group-hover:text-blue-100">
                                            <span>Hadir</span>
                                            <strong class="tabular-nums"><?= $pctHadir; ?>%</strong>
                                        </span>
                                        <span class="mt-1 block h-1 rounded-sm bg-slate-200 group-hover:bg-white/30 overflow-hidden">
                                            <span class="block h-full bg-emerald-500 group-hover:bg-white" style="width: <?= max(0, min(100, $pctHadir)); ?>%"></span>
                                        </span>
                                        <span class="mt-1 flex items-center justify-between text-[10px] text-blue-700 font-semibold group-hover:text-blue-100 truncate">
                                            <span>Kejuruan:</span>
                                            <strong class="tabular-nums"><?= number_format($s['nilai_kejuruan'] ?? 0, 1); ?></strong>
                                        </span>
                                    </span>
                                </button>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-2 sm:px-4">
                                <div class="flex items-center justify-center gap-1.5 sm:gap-2">
                                    <button type="button"
                                        onclick='openEditSantriModal(<?= htmlspecialchars(json_encode($s), ENT_QUOTES, "UTF-8"); ?>)'
                                        title="Edit Data Santri"
                                        aria-label="Edit <?= esc($s['nama']); ?>"
                                        class="w-8 h-8 sm:w-9 sm:h-9 inline-flex items-center justify-center rounded-lg text-amber-700 hover:bg-amber-400 hover:text-slate-900 text-xs transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/60">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button"
                                        onclick="confirmDeleteSantri(<?= (int)$s['id']; ?>, '<?= esc(addslashes($s['nama'])); ?>', '<?= esc(addslashes($s['nis'] ?? '-')); ?>')"
                                        title="Hapus Data Santri"
                                        aria-label="Hapus <?= esc($s['nama']); ?>"
                                        class="w-8 h-8 sm:w-9 sm:h-9 inline-flex items-center justify-center rounded-lg text-rose-600 hover:bg-rose-600 hover:text-white text-xs transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/50">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Empty State Row -->
                        <tr id="emptySearchRow" class="hidden">
                            <td colspan="9" class="py-14 text-center text-slate-500 space-y-2">
                                <i class="fa-solid fa-user-slash text-3xl text-slate-300"></i>
                                <p class="text-sm font-semibold text-slate-700">Tidak ada santri yang cocok dengan kriteria pencarian.</p>
                                <button type="button" onclick="resetAllFilters()" class="text-xs text-brand hover:underline font-bold">
                                    Hapus filter & tampilkan semua santri
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        </div> <!-- /p-6 wrapper -->
    </main>
</div>

<!-- ===================== CARD MODAL: INFO NILAI & KEHADIRAN ===================== -->
<div id="detailNilaiModal" role="dialog" aria-modal="true" aria-labelledby="detailNilaiTitle" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeDetailNilaiModal()"></div>

    <!-- Modal Card Content -->
    <div class="sm-pop relative bg-white w-full max-w-2xl rounded-xl shadow-2xl border border-slate-200 overflow-hidden z-10 max-h-[92vh] flex flex-col">
        
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-[#0066CC] to-[#073B66] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/15 text-white flex items-center justify-center text-sm">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h3 id="detailNilaiTitle" class="text-sm sm:text-base font-bold leading-tight">Kartu Capaian & Rapor Nilai Santri</h3>
                    <p class="text-2xs text-sky-100">Rumah Gemilang Indonesia, Tahun Ajaran 2026/2027</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailNilaiModal()" aria-label="Tutup rapor" class="w-9 h-9 rounded-lg hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Scrollable Content -->
        <div class="p-6 overflow-y-auto space-y-5">
            
            <!-- Profil Singkat Santri -->
            <div class="pb-5 border-b border-slate-200 flex flex-col sm:flex-row items-center gap-4">
                <div id="mDetailFotoBox" class="w-20 h-20 rounded-xl bg-brand-light border border-slate-200 shrink-0 overflow-hidden flex items-center justify-center">
                    <img id="mDetailFotoImg" src="" alt="Foto Santri" class="w-full h-full object-cover hidden">
                    <span id="mDetailInitial" class="text-2xl font-black text-brand">A</span>
                </div>
                <div class="text-center sm:text-left min-w-0 flex-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-3 gap-y-1 mb-1.5 text-xs">
                        <span id="mDetailStatusBadge" class="inline-flex items-center gap-2 font-semibold text-emerald-800">Aktif</span>
                        <span id="mDetailAngkatan" class="font-medium text-slate-600 tabular-nums">Angkatan 35</span>
                    </div>
                    <h4 id="mDetailNama" class="text-lg sm:text-xl font-extrabold text-slate-800 leading-tight">Nama Santri</h4>
                    <div class="mt-2 flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-2 text-xs text-slate-600">
                        <span class="font-bold text-slate-700 tabular-nums"><i class="fa-solid fa-id-badge text-brand mr-1.5"></i>NIS: <span id="mDetailNis">-</span></span>
                        <span class="inline-flex items-stretch rounded-md overflow-hidden border border-slate-300 bg-white shadow-[2px_2px_0_0_#cbd5e1]">
                            <span id="mDetailKelasTile" class="w-7 flex items-center justify-center shrink-0 bg-[#0066CC] text-white"><i id="mDetailKelasIcon" class="fa-solid fa-graduation-cap text-xs"></i></span>
                            <span id="mDetailKelas" class="px-2.5 py-1 font-semibold text-slate-800">-</span>
                        </span>
                        <span><i class="fa-solid fa-location-dot text-slate-400 mr-1.5"></i><span id="mDetailCabang">-</span></span>
                    </div>
                </div>
            </div>

            <!-- Nilai Akhir (Terbobot 80:20): elemen utama, warna solid -->
            <div class="rounded-xl bg-[#073B66] text-white px-5 py-4 flex items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-medium text-sky-100 block">Nilai Akhir (80:20)</span>
                    <h3 id="mDetailNilaiAkhir" class="text-5xl font-black leading-none mt-2 tabular-nums">0.00</h3>
                </div>
                <div class="text-right">
                    <span id="mDetailPredikatBadge" class="inline-block px-2.5 py-1 rounded-md text-2xs font-bold bg-sky-100 text-sky-900">Grade A</span>
                    <p class="text-2xs text-sky-100 mt-2">80% akademik + 20% adab</p>
                </div>
            </div>

            <!-- Tiga metrik pendukung -->
            <div class="grid grid-cols-3 divide-x divide-slate-200 rounded-xl border border-slate-200">
                <!-- Rata Akademik (80%) -->
                <div class="px-3.5 py-3">
                    <span class="text-2xs font-semibold text-purple-700 block">Akademik (80%)</span>
                    <h3 id="mDetailRataAkademik" class="text-xl sm:text-2xl font-black text-purple-700 mt-0.5 tabular-nums">0.00</h3>
                    <span class="text-2xs text-slate-600 block mt-0.5">5 Mata Pelajaran</span>
                </div>

                <!-- Adab & Akhlaq (20%) -->
                <div class="px-3.5 py-3">
                    <span class="text-2xs font-semibold text-amber-800 block">Adab (20%)</span>
                    <h3 id="mDetailAdab" class="text-xl sm:text-2xl font-black text-amber-700 mt-0.5 tabular-nums">0</h3>
                    <span class="text-2xs text-slate-600 block mt-0.5">Bobot Karakter</span>
                </div>

                <!-- Kehadiran (%) -->
                <div class="px-3.5 py-3">
                    <span class="text-2xs font-semibold text-emerald-800 block">Presensi</span>
                    <h3 id="mDetailKehadiran" class="text-xl sm:text-2xl font-black text-emerald-700 mt-0.5 tabular-nums">0%</h3>
                    <span id="mDetailKehadiranStatus" class="text-2xs text-emerald-700 font-semibold block mt-0.5">Sangat Disiplin</span>
                </div>
            </div>

            <!-- Detail Nilai Tiap Aspek Breakdown -->
            <div>
                <h5 class="text-sm font-bold text-slate-800 flex items-center gap-2 pb-2.5 mb-3.5 border-b border-slate-200">
                    <i class="fa-solid fa-list-check text-brand"></i> Rincian Nilai Aspek Pembelajaran
                </h5>

                <div class="space-y-3.5 text-xs">
                    <!-- Tauhid -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-sm bg-blue-500"></span> Ilmu Tauhid & Keimanan
                            </span>
                            <span id="mScoreTauhid" class="font-extrabold text-slate-800 tabular-nums">0</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-sm h-1.5 overflow-hidden">
                            <div id="mBarTauhid" class="sm-bar bg-blue-600 h-1.5 transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Fiqih -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-sm bg-indigo-500"></span> Fiqih Ibadah & Muamalah
                            </span>
                            <span id="mScoreFiqih" class="font-extrabold text-slate-800 tabular-nums">0</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-sm h-1.5 overflow-hidden">
                            <div id="mBarFiqih" class="sm-bar bg-indigo-600 h-1.5 transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- B. Inggris -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-sm bg-sky-500"></span> Bahasa Inggris Komunikatif
                            </span>
                            <span id="mScoreInggris" class="font-extrabold text-slate-800 tabular-nums">0</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-sm h-1.5 overflow-hidden">
                            <div id="mBarInggris" class="sm-bar bg-sky-500 h-1.5 transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Menulis Kreatif -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-sm bg-emerald-500"></span> Menulis Kreatif & Literasi
                            </span>
                            <span id="mScoreKreatif" class="font-extrabold text-slate-800 tabular-nums">0</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-sm h-1.5 overflow-hidden">
                            <div id="mBarKreatif" class="sm-bar bg-emerald-600 h-1.5 transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Nilai Kejuruan (Akumulasi Mata Diklat Kejuruan Terpadu) -->
                    <div class="p-3.5 bg-blue-50/70 rounded-xl border border-blue-200/80 space-y-2.5">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-blue-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#0066CC]"></span>
                                <span id="mLabelKejuruanAkumulasi">Nilai Kejuruan (Akumulasi Mata Diklat)</span>
                            </span>
                            <span id="mScoreKejuruan" class="font-black text-[#0066CC] text-sm tabular-nums">0.00</span>
                        </div>
                        <div class="w-full bg-blue-200/60 rounded-sm h-2 overflow-hidden">
                            <div id="mBarKejuruan" class="sm-bar bg-[#0066CC] h-2 transition-all duration-500" style="width: 0%"></div>
                        </div>

                        <!-- Dynamic Mata Diklat Kejuruan (4 untuk TKJ, 2 untuk Tata Busana, dll.) -->
                        <div id="mContainerMataDiklatKejuruan" class="pt-2 border-t border-blue-200/70 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <!-- Diisi dinamis di JS -->
                        </div>
                    </div>

                    <!-- Adab Akhlaq -->
                    <div class="pt-3.5 border-t border-dashed border-slate-300">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-bold text-amber-800 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-sm bg-amber-500"></span> Adab & Akhlaq Karimah (Bobot 20%)
                            </span>
                            <span id="mScoreAdab" class="font-extrabold text-amber-800 tabular-nums">0</span>
                        </div>
                        <div class="w-full bg-amber-100 rounded-sm h-1.5 overflow-hidden">
                            <div id="mBarAdab" class="sm-bar bg-amber-500 h-1.5 transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Kehadiran -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-bold text-emerald-800 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-sm bg-emerald-500"></span> Persentase Kehadiran
                            </span>
                            <span id="mScoreHadir" class="font-extrabold text-emerald-800 tabular-nums">0%</span>
                        </div>
                        <div class="w-full bg-emerald-100 rounded-sm h-1.5 overflow-hidden">
                            <div id="mBarHadir" class="sm-bar bg-emerald-500 h-1.5 transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
            <button type="button" onclick="closeDetailNilaiModal()" 
                    class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/50">
                Tutup
            </button>
            <button type="button" id="mDetailEditTriggerBtn" onclick="switchToEditFromDetail()" 
                    class="px-5 py-2 rounded-lg bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/60 focus-visible:ring-offset-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Data & Nilai Santri
            </button>
        </div>

    </div>
</div>

<!-- ===================== MODAL FORM EDIT SANTRI ===================== -->
<?php $fld = 'w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white font-medium focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition'; ?>
<div id="ci4Modal" role="dialog" aria-modal="true" aria-labelledby="ci4ModalTitle" class="fixed inset-0 bg-slate-900/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-xs">
    <div class="sm-pop bg-white rounded-xl border border-slate-200 shadow-2xl w-full max-w-xl overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-6 py-4 bg-brand text-white flex items-center justify-between shrink-0">
            <h3 id="ci4ModalTitle" class="text-base font-bold flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Data Santri
            </h3>
            <button type="button" onclick="closeSantriModal()" aria-label="Tutup form edit" class="w-9 h-9 rounded-lg text-white/80 hover:text-white hover:bg-white/15 flex items-center justify-center transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="ci4SantriForm" action="<?= base_url('admin/update'); ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-6 overflow-y-auto">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="f_id">

            <!-- Identitas -->
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-200">Identitas santri</h4>

                <!-- Foto Profil Edit -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Foto Profil Santri</label>
                    <div class="flex items-center gap-3.5 p-3 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                        <div id="f_foto_box" class="w-14 h-14 rounded-lg bg-brand-light border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">
                            <img id="f_foto_img" src="" alt="Foto" class="w-full h-full object-cover hidden">
                            <span id="f_foto_initial" class="text-lg font-black text-brand">A</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <input type="file" name="foto" id="f_foto_input" accept="image/png, image/jpeg, image/jpg, image/webp"
                                   onchange="previewEditFoto(this)"
                                   class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-brand file:text-white hover:file:bg-brand-dark cursor-pointer">
                            <p class="text-2xs text-slate-500 mt-1">Unggah foto baru untuk memperbarui (JPG/PNG/WEBP).</p>
                        </div>
                    </div>
                </div>

                <!-- Nama & NIS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="f_nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Santri <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" id="f_nama" required class="<?= $fld; ?>">
                    </div>
                    <div>
                        <label for="f_nis" class="block text-xs font-semibold text-slate-700 mb-1">NIS (Nomor Induk Santri)</label>
                        <input type="text" name="nis" id="f_nis" class="<?= $fld; ?> tabular-nums font-bold">
                    </div>
                </div>
            </div>

            <!-- Penempatan -->
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-200">Penempatan</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="f_cabang" class="block text-xs font-semibold text-slate-700 mb-1">Cabang Kampus</label>
                        <select name="cabang" id="f_cabang" class="<?= $fld; ?>">
                            <option value="Kampus Utama Depok">Kampus Utama Depok</option>
                            <option value="Kampus Surabaya">Kampus Surabaya</option>
                            <option value="Kampus Aceh">Kampus Aceh</option>
                            <option value="Kampus Yogyakarta">Kampus Yogyakarta</option>
                            <option value="Kampus Magelang">Kampus Magelang</option>
                        </select>
                    </div>
                    <div>
                        <label for="f_kelas" class="block text-xs font-semibold text-slate-700 mb-1">Kelas / Kejuruan <span class="text-rose-500">*</span></label>
                        <select name="kelas" id="f_kelas" required class="<?= $fld; ?>">
                            <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan</option>
                            <option value="Tata Busana">Tata Busana</option>
                            <option value="Fotografi dan Videografi">Fotografi dan Videografi</option>
                            <option value="Desain Grafis">Desain Grafis</option>
                            <option value="Otomotif">Otomotif</option>
                            <option value="Aplikasi Perkantoran">Aplikasi Perkantoran</option>
                        </select>
                    </div>
                    <div>
                        <label for="f_angkatan" class="block text-xs font-semibold text-slate-700 mb-1">Angkatan</label>
                        <input type="text" name="angkatan" id="f_angkatan" required class="<?= $fld; ?>">
                    </div>
                    <div>
                        <label for="f_status" class="block text-xs font-semibold text-slate-700 mb-1">Status Siswa</label>
                        <select name="status" id="f_status" required class="<?= $fld; ?>">
                            <option value="Aktif">Aktif</option>
                            <option value="Alumni">Alumni</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 4 Nilai Akademik -->
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-200">Nilai akademik <span class="font-medium text-slate-500 text-xs">(0 sampai 100)</span></h4>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="f_tauhid" class="block text-xs font-medium text-slate-700 mb-1">Nilai Tauhid</label>
                        <input type="number" step="0.1" name="tauhid" id="f_tauhid" min="0" max="100" required class="<?= $fld; ?> tabular-nums">
                    </div>
                    <div>
                        <label for="f_fiqih" class="block text-xs font-medium text-slate-700 mb-1">Nilai Fiqih</label>
                        <input type="number" step="0.1" name="fiqih" id="f_fiqih" min="0" max="100" required class="<?= $fld; ?> tabular-nums">
                    </div>
                    <div>
                        <label for="f_inggris" class="block text-xs font-medium text-slate-700 mb-1">Nilai B. Inggris</label>
                        <input type="number" step="0.1" name="b_ing" id="f_inggris" min="0" max="100" required class="<?= $fld; ?> tabular-nums">
                    </div>
                    <div>
                        <label for="f_kreatif" class="block text-xs font-medium text-slate-700 mb-1">Nilai Menulis Kreatif</label>
                        <input type="number" step="0.1" name="menulis_kreatif" id="f_kreatif" min="0" max="100" required class="<?= $fld; ?> tabular-nums">
                    </div>

                    <!-- Mata Diklat Kejuruan Dinamis Sesuai Jurusan -->
                    <div class="col-span-2 p-3 bg-blue-50/60 rounded-xl border border-blue-200/80">
                        <span class="block text-xs font-bold text-blue-900 mb-2">
                            <i class="fa-solid fa-graduation-cap text-[#0066CC] mr-1"></i> Nilai Mata Diklat Kejuruan
                        </span>
                        <input type="hidden" name="kejuruan_1_mapel_id" id="f_kejuruan_1_mapel_id" value="">
                        <input type="hidden" name="kejuruan_2_mapel_id" id="f_kejuruan_2_mapel_id" value="">
                        <div id="editMataDiklatContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Diisi secara dinamis di JS -->
                            <div>
                                <label for="f_kejuruan_1" id="lbl_kejuruan_1" class="block text-xs font-medium text-slate-700 mb-1 truncate">Mata Diklat 1</label>
                                <input type="number" step="0.1" name="kejuruan_1" id="f_kejuruan_1" min="0" max="100" class="<?= $fld; ?> tabular-nums" placeholder="0 - 100">
                            </div>
                            <div>
                                <label for="f_kejuruan_2" id="lbl_kejuruan_2" class="block text-xs font-medium text-slate-700 mb-1 truncate">Mata Diklat 2</label>
                                <input type="number" step="0.1" name="kejuruan_2" id="f_kejuruan_2" min="0" max="100" class="<?= $fld; ?> tabular-nums" placeholder="0 - 100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adab & Kehadiran -->
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-200">Karakter & kehadiran</h4>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="f_adab" class="block text-xs font-bold text-amber-800 mb-1">
                            <i class="fa-solid fa-scale-balanced mr-1"></i> Adab & Akhlaq (20%)
                        </label>
                        <input type="number" step="0.1" name="adab_akhlaq" id="f_adab" min="0" max="100" required 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-amber-300 bg-amber-50/40 font-bold text-amber-900 tabular-nums focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400 transition">
                    </div>
                    <div>
                        <label for="f_kehadiran" class="block text-xs font-bold text-emerald-800 mb-1">
                            <i class="fa-solid fa-clipboard-check mr-1"></i> Kehadiran (%)
                        </label>
                        <input type="number" step="0.1" name="kehadiran" id="f_kehadiran" min="0" max="100" required 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-emerald-300 bg-emerald-50/40 font-bold text-emerald-900 tabular-nums focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 transition">
                    </div>
                </div>
            </div>

            <div class="sticky bottom-0 -mx-6 -mb-6 px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5">
                <button type="button" onclick="closeSantriModal()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/50">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-brand hover:bg-brand-dark text-white text-xs font-bold cursor-pointer flex items-center gap-1.5 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/50 focus-visible:ring-offset-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentActiveSantriData = null;

// Ikon & warna tile tiap jurusan, dikirim dari PHP agar sama dengan badge di tabel.
const JURUSAN_UI = <?= json_encode($jurusanJs); ?>;
const JURUSAN_UI_FALLBACK = { icon: 'fa-graduation-cap', tile: 'bg-[#0066CC] text-white' };

function filterSantriTable() {
    const searchVal = document.getElementById('searchInput').value.trim().toLowerCase();
    const rawJurusan = document.getElementById('filterJurusan').value;
    const jurusanVal = rawJurusan.trim().toLowerCase();
    const statusVal = document.getElementById('filterStatus').value.trim().toLowerCase();
    const clearBtn = document.getElementById('clearSearchBtn');
    const resetBtn = document.getElementById('resetFilterBtn');
    const statsText = document.getElementById('searchResultCount');
    const emptyRow = document.getElementById('emptySearchRow');

    // Sinkronisasi link tombol Export & Cetak sesuai filter jurusan aktif
    const exportBtn = document.getElementById('btnExportSantri');
    const cetakBtn = document.getElementById('btnCetakSantri');
    if (exportBtn) {
        exportBtn.href = '<?= base_url('admin/export-santri-csv'); ?>' + (rawJurusan ? '?jurusan=' + encodeURIComponent(rawJurusan) : '');
    }
    if (cetakBtn) {
        cetakBtn.href = '<?= base_url('admin/cetak-santri'); ?>' + (rawJurusan ? '?jurusan=' + encodeURIComponent(rawJurusan) : '');
    }

    clearBtn.classList.toggle('hidden', searchVal === '');
    resetBtn.classList.toggle('hidden', searchVal === '' && jurusanVal === '' && statusVal === '');

    const rows = document.querySelectorAll('.santri-data-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const idText = row.querySelector('.col-id')?.textContent.toLowerCase() || '';
        const namaText = row.querySelector('.col-nama')?.getAttribute('data-nama') || '';
        const nisText = row.querySelector('.col-nis')?.getAttribute('data-nis') || '';
        const cabangText = row.querySelector('.col-cabang')?.getAttribute('data-cabang') || '';
        const kelasText = row.querySelector('.col-kelas')?.getAttribute('data-kelas') || '';
        const angkatanText = row.querySelector('.col-angkatan')?.getAttribute('data-angkatan') || '';
        const statusText = row.querySelector('.col-status')?.getAttribute('data-status') || '';

        const matchSearch = searchVal === '' || 
                            namaText.includes(searchVal) || 
                            nisText.includes(searchVal) || 
                            cabangText.includes(searchVal) || 
                            kelasText.includes(searchVal) || 
                            angkatanText.includes(searchVal) || 
                            idText.includes(searchVal);
        
        const matchJurusan = jurusanVal === '' || kelasText === jurusanVal;
        const matchStatus = statusVal === '' || statusText === statusVal;

        if (matchSearch && matchJurusan && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    emptyRow.classList.toggle('hidden', visibleCount > 0);

    if (searchVal !== '' || jurusanVal !== '' || statusVal !== '') {
        statsText.innerHTML = `Ditemukan <strong>${visibleCount}</strong> dari ${rows.length} santri`;
    } else {
        statsText.innerHTML = `Menampilkan seluruh <strong>${rows.length}</strong> santri`;
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    filterSantriTable();
    document.getElementById('searchInput').focus();
}

function resetAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterJurusan').value = '';
    document.getElementById('filterStatus').value = '';
    filterSantriTable();
}

// Klik badge jurusan di tabel: pakai filter dropdown yang sudah ada.
function filterByJurusan(nama) {
    document.getElementById('filterJurusan').value = nama;
    filterSantriTable();
}

function openDetailNilaiModal(data) {
    currentActiveSantriData = data;

    // Foto & Inisial
    const fotoImg = document.getElementById('mDetailFotoImg');
    const initialSpan = document.getElementById('mDetailInitial');
    if (data.foto) {
        fotoImg.src = '<?= base_url('uploads/santri/'); ?>/' + data.foto;
        fotoImg.classList.remove('hidden');
        initialSpan.classList.add('hidden');
    } else {
        fotoImg.src = '';
        fotoImg.classList.add('hidden');
        initialSpan.textContent = (data.nama || 'A').trim().charAt(0).toUpperCase();
        initialSpan.classList.remove('hidden');
    }

    // Identitas
    document.getElementById('mDetailNama').textContent = data.nama;
    document.getElementById('mDetailNis').textContent = data.nis || '-';
    document.getElementById('mDetailKelas').textContent = data.kelas;
    document.getElementById('mDetailCabang').textContent = data.cabang || 'Kampus Utama Depok';
    document.getElementById('mDetailAngkatan').textContent = data.angkatan || 'Angkatan 35';

    // Badge tile jurusan
    const ui = JURUSAN_UI[data.kelas] || JURUSAN_UI_FALLBACK;
    document.getElementById('mDetailKelasTile').className = 'w-7 flex items-center justify-center shrink-0 ' + ui.tile;
    document.getElementById('mDetailKelasIcon').className = 'fa-solid ' + ui.icon + ' text-xs';

    // Status
    const statusBadge = document.getElementById('mDetailStatusBadge');
    const isAktif = (data.status || 'Aktif') === 'Aktif';
    statusBadge.innerHTML = isAktif
        ? '<span class="w-2 h-2 rounded-sm bg-emerald-500"></span> Aktif'
        : '<span class="w-2 h-2 rounded-sm bg-slate-400"></span> Alumni';
    statusBadge.className = isAktif
        ? 'inline-flex items-center gap-2 font-semibold text-emerald-800'
        : 'inline-flex items-center gap-2 font-medium text-slate-600';

    // Perhitungan Nilai (5 Mapel: Tauhid, Fiqih, B.Inggris, Menulis Kreatif, dan Kejuruan)
    const tauhid = parseFloat(data.tauhid) || 0;
    const fiqih = parseFloat(data.fiqih) || 0;
    const bing = parseFloat(data.b_ing) || 0;
    const menulis = parseFloat(data.menulis_kreatif) || 0;
    const kejuruan = parseFloat(data.nilai_kejuruan) || 0;
    const adab = parseFloat(data.adab_akhlaq) || 85;
    const kehadiran = parseFloat(data.kehadiran) || 95;

    const rataAkademik = ((tauhid + fiqih + bing + menulis + kejuruan) / 5);
    const nilaiAkhir = parseFloat(data.rata_rata) || ((rataAkademik * 0.8) + (adab * 0.2));

    // KPI Cards
    document.getElementById('mDetailNilaiAkhir').textContent = Number(nilaiAkhir).toFixed(2);
    document.getElementById('mDetailRataAkademik').textContent = Number(rataAkademik).toFixed(2);
    document.getElementById('mDetailAdab').textContent = Number(adab).toFixed(1);
    document.getElementById('mDetailKehadiran').textContent = Math.round(kehadiran) + '%';

    // Grade & Predikat (badge berada di atas blok biru tua, jadi memakai latar terang)
    const predikatBadge = document.getElementById('mDetailPredikatBadge');
    const badgeBase = 'inline-block px-2.5 py-1 rounded-md text-2xs font-bold ';
    if (nilaiAkhir >= 88) {
        predikatBadge.textContent = 'Grade A (Mumtaz)';
        predikatBadge.className = badgeBase + 'bg-sky-100 text-sky-900';
    } else if (nilaiAkhir >= 78) {
        predikatBadge.textContent = 'Grade B (Jayyid Jiddan)';
        predikatBadge.className = badgeBase + 'bg-emerald-100 text-emerald-900';
    } else if (nilaiAkhir >= 68) {
        predikatBadge.textContent = 'Grade C (Jayyid)';
        predikatBadge.className = badgeBase + 'bg-amber-100 text-amber-900';
    } else {
        predikatBadge.textContent = 'Grade D (Perlu Evaluasi)';
        predikatBadge.className = badgeBase + 'bg-rose-100 text-rose-900';
    }

    // Status Kehadiran
    const kehadiranStatus = document.getElementById('mDetailKehadiranStatus');
    if (kehadiran >= 95) {
        kehadiranStatus.textContent = 'Sangat Disiplin';
        kehadiranStatus.className = 'text-2xs text-emerald-700 font-semibold block mt-0.5';
    } else if (kehadiran >= 90) {
        kehadiranStatus.textContent = 'Cukup Disiplin';
        kehadiranStatus.className = 'text-2xs text-sky-700 font-semibold block mt-0.5';
    } else {
        kehadiranStatus.textContent = 'Perlu Perhatian';
        kehadiranStatus.className = 'text-2xs text-amber-700 font-semibold block mt-0.5';
    }

    // Progress Bars & Values
    document.getElementById('mScoreTauhid').textContent = tauhid;
    document.getElementById('mBarTauhid').style.width = Math.min(100, Math.max(0, tauhid)) + '%';

    document.getElementById('mScoreFiqih').textContent = fiqih;
    document.getElementById('mBarFiqih').style.width = Math.min(100, Math.max(0, fiqih)) + '%';

    document.getElementById('mScoreInggris').textContent = bing;
    document.getElementById('mBarInggris').style.width = Math.min(100, Math.max(0, bing)) + '%';

    document.getElementById('mScoreKreatif').textContent = menulis;
    document.getElementById('mBarKreatif').style.width = Math.min(100, Math.max(0, menulis)) + '%';

    // Kejuruan Akumulasi
    document.getElementById('mScoreKejuruan').textContent = Number(kejuruan).toFixed(2);
    document.getElementById('mBarKejuruan').style.width = Math.min(100, Math.max(0, kejuruan)) + '%';

    // Rincian Seluruh Mata Diklat Kejuruan Dinamis (4 untuk TKJ, 2 untuk Tata Busana, dll)
    const containerMataDiklat = document.getElementById('mContainerMataDiklatKejuruan');
    if (containerMataDiklat) {
        containerMataDiklat.innerHTML = '';
        const listMapel = Array.isArray(data.mapel_kejuruan) && data.mapel_kejuruan.length > 0
            ? data.mapel_kejuruan
            : [
                { nama_mapel: data.kejuruan_1_nama || 'Mata Diklat 1', nilai_akhir: data.kejuruan_1_nilai || 0, kode_mapel: data.kejuruan_1_kode || 'MD-01' },
                { nama_mapel: data.kejuruan_2_nama || 'Mata Diklat 2', nilai_akhir: data.kejuruan_2_nilai || 0, kode_mapel: data.kejuruan_2_kode || 'MD-02' }
            ];

        const lblAkumulasi = document.getElementById('mLabelKejuruanAkumulasi');
        if (lblAkumulasi) {
            lblAkumulasi.textContent = `Nilai Kejuruan (Akumulasi ${listMapel.length} Mata Diklat)`;
        }

        const colorClasses = [
            'bg-sky-500',
            'bg-indigo-500',
            'bg-teal-500',
            'bg-violet-500',
            'bg-emerald-500',
            'bg-blue-600'
        ];

        listMapel.forEach((m, idx) => {
            const val = parseFloat(m.nilai_akhir) || 0;
            const barCol = colorClasses[idx % colorClasses.length];
            const badgeKode = m.kode_mapel ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 mr-1.5 shrink-0">${m.kode_mapel}</span>` : '';
            const tgs = m.nilai_tugas !== undefined ? `<div class="mt-1.5 pt-1 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                <span>Tugas: <strong class="text-slate-700">${parseFloat(m.nilai_tugas || 0).toFixed(0)}</strong></span>
                <span>Teori: <strong class="text-slate-700">${parseFloat(m.nilai_teori || 0).toFixed(0)}</strong></span>
                <span>Praktik: <strong class="text-slate-700">${parseFloat(m.nilai_praktik || 0).toFixed(0)}</strong></span>
            </div>` : '';

            const card = document.createElement('div');
            card.className = 'bg-white p-2.5 rounded-lg border border-blue-100 shadow-2xs hover:border-blue-300 transition-colors';
            card.innerHTML = `
                <div class="flex items-center justify-between gap-1 mb-1">
                    <span class="font-semibold text-slate-700 truncate text-[11px] flex items-center" title="${m.nama_mapel}">
                        ${badgeKode}<span class="truncate">${m.nama_mapel}</span>
                    </span>
                    <span class="font-bold text-slate-900 tabular-nums text-xs ml-1 shrink-0">${val.toFixed(2)}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-sm h-1.5 overflow-hidden">
                    <div class="${barCol} h-1.5 transition-all duration-500" style="width: ${Math.min(100, Math.max(0, val))}%"></div>
                </div>
                ${tgs}
            `;
            containerMataDiklat.appendChild(card);
        });
    }

    document.getElementById('mScoreAdab').textContent = adab;
    document.getElementById('mBarAdab').style.width = Math.min(100, Math.max(0, adab)) + '%';

    document.getElementById('mScoreHadir').textContent = Math.round(kehadiran) + '%';
    document.getElementById('mBarHadir').style.width = Math.min(100, Math.max(0, kehadiran)) + '%';

    document.getElementById('detailNilaiModal').classList.remove('hidden');
}

function closeDetailNilaiModal() {
    document.getElementById('detailNilaiModal').classList.add('hidden');
}

function switchToEditFromDetail() {
    if (currentActiveSantriData) {
        closeDetailNilaiModal();
        openEditSantriModal(currentActiveSantriData);
    }
}

function openEditSantriModal(data) {
    document.getElementById('ci4ModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit Santri: ' + data.nama;
    document.getElementById('f_id').value = data.id;
    document.getElementById('f_nama').value = data.nama;
    document.getElementById('f_nis').value = data.nis || '';
    document.getElementById('f_cabang').value = data.cabang || 'Kampus Utama Depok';
    document.getElementById('f_kelas').value = data.kelas;
    document.getElementById('f_angkatan').value = data.angkatan || 'Angkatan 35';
    document.getElementById('f_status').value = data.status || 'Aktif';
    document.getElementById('f_tauhid').value = data.tauhid;
    document.getElementById('f_fiqih').value = data.fiqih;
    document.getElementById('f_inggris').value = data.b_ing;
    document.getElementById('f_kreatif').value = data.menulis_kreatif;

    // Render Mata Diklat Kejuruan Dinamis di Form Edit
    const editContainer = document.getElementById('editMataDiklatContainer');
    if (editContainer) {
        editContainer.innerHTML = '';
        const listMapel = Array.isArray(data.mapel_kejuruan) && data.mapel_kejuruan.length > 0
            ? data.mapel_kejuruan
            : [
                { mapel_id: data.kejuruan_1_id || 1, nama_mapel: data.kejuruan_1_nama || 'Mata Diklat 1', nilai_akhir: data.kejuruan_1_nilai, kode_mapel: data.kejuruan_1_kode || 'MD-01' },
                { mapel_id: data.kejuruan_2_id || 2, nama_mapel: data.kejuruan_2_nama || 'Mata Diklat 2', nilai_akhir: data.kejuruan_2_nilai, kode_mapel: data.kejuruan_2_kode || 'MD-02' }
            ];

        listMapel.forEach((m, idx) => {
            const val = (m.nilai_akhir !== undefined && m.nilai_akhir !== null) ? m.nilai_akhir : '';
            const mId = m.mapel_id || (idx + 1);
            const div = document.createElement('div');
            div.innerHTML = `
                <label class="block text-xs font-medium text-slate-700 mb-1 truncate" title="${m.nama_mapel}">
                    ${m.kode_mapel ? '[' + m.kode_mapel + '] ' : ''}${m.nama_mapel}
                </label>
                <input type="number" step="0.1" name="kejuruan_scores[${mId}]" min="0" max="100" value="${val}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0066CC] tabular-nums" placeholder="0 - 100">
                ${idx === 0 ? `<input type="hidden" name="kejuruan_1_mapel_id" value="${mId}"><input type="hidden" name="kejuruan_1" value="${val}">` : ''}
                ${idx === 1 ? `<input type="hidden" name="kejuruan_2_mapel_id" value="${mId}"><input type="hidden" name="kejuruan_2" value="${val}">` : ''}
            `;
            editContainer.appendChild(div);
        });
    }

    document.getElementById('f_adab').value = data.adab_akhlaq || 85;
    document.getElementById('f_kehadiran').value = data.kehadiran || 95;

    // Reset file input
    document.getElementById('f_foto_input').value = '';

    // Preview Foto saat ini
    const fotoImg = document.getElementById('f_foto_img');
    const initialSpan = document.getElementById('f_foto_initial');
    if (data.foto) {
        fotoImg.src = '<?= base_url('uploads/santri/'); ?>/' + data.foto;
        fotoImg.classList.remove('hidden');
        initialSpan.classList.add('hidden');
    } else {
        fotoImg.src = '';
        fotoImg.classList.add('hidden');
        initialSpan.textContent = (data.nama || 'A').trim().charAt(0).toUpperCase();
        initialSpan.classList.remove('hidden');
    }

    document.getElementById('ci4Modal').classList.remove('hidden');
}

function closeSantriModal() {
    document.getElementById('ci4Modal').classList.add('hidden');
}

function previewEditFoto(input) {
    const fotoImg = document.getElementById('f_foto_img');
    const initialSpan = document.getElementById('f_foto_initial');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            fotoImg.src = e.target.result;
            fotoImg.classList.remove('hidden');
            initialSpan.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function confirmDeleteSantri(id, name, nis) {
    const form = document.getElementById('deleteSantriForm');
    form.action = '<?= base_url('admin/delete/'); ?>/' + encodeURIComponent(id);
    document.getElementById('deleteSantriName').textContent = name;
    document.getElementById('deleteSantriNis').textContent = nis;
    document.getElementById('modalConfirmDeleteSantri').classList.remove('hidden');
}

function closeDeleteSantriModal() {
    document.getElementById('modalConfirmDeleteSantri').classList.add('hidden');
}

// Tombol Esc menutup modal yang sedang terbuka
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeDetailNilaiModal();
        closeSantriModal();
        closeDeleteSantriModal();
    }
});
</script>

<!-- ===================== MODAL KONFIRMASI HAPUS SANTRI ===================== -->
<div id="modalConfirmDeleteSantri" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div onclick="closeDeleteSantriModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Hapus Data Santri</h3>
                <p class="text-xs text-slate-500">Konfirmasi tindakan penghapusan permanen.</p>
            </div>
        </div>

        <div class="p-3.5 bg-rose-50/70 border border-rose-100 rounded-xl text-xs text-slate-700 leading-relaxed">
            Apakah Anda yakin ingin menghapus data santri <strong id="deleteSantriName" class="text-rose-700">-</strong> (NIS: <span id="deleteSantriNis" class="font-mono text-slate-900 font-bold">-</span>)? Seluruh riwayat nilai, absensi, dan data santri ini akan dihapus permanen dari sistem.
        </div>

        <form id="deleteSantriForm" method="post" action="">
            <?= csrf_field(); ?>
            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeDeleteSantriModal()" class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-600 hover:bg-slate-100 border border-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-trash-can"></i> Ya, Hapus Santri
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>