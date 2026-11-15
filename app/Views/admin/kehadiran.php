<?php 
require_once 'navbar.php'; 

// Helper Nama Hari & Bulan Bahasa Indonesia untuk Realtime Date
$daftarHariIndo = [
    'Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa',
    'Wed' => 'Rabu',   'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'
];
$daftarBulanIndo = [
    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
    '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
    '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
];

$todayRealtimeDate     = date('Y-m-d');
$yesterdayRealtimeDate = date('Y-m-d', strtotime('-1 day'));
$todayLabel            = date('d') . ' ' . $daftarBulanIndo[date('m')];
$yesterdayLabel        = date('d', strtotime('-1 day')) . ' ' . $daftarBulanIndo[date('m', strtotime('-1 day'))];
$todayFullIndo         = $daftarHariIndo[date('D')] . ', ' . date('d') . ' ' . $daftarBulanIndo[date('m')] . ' ' . date('Y');

$currentMonthKey       = date('Y-m');
$currentMonthLabel     = $daftarBulanIndo[date('m')] . ' ' . date('Y');

$periodeAwal           = date('d', strtotime('-1 month')) . ' ' . $daftarBulanIndo[date('m', strtotime('-1 month'))] . ' ' . date('Y', strtotime('-1 month'));
$periodeAkhir          = date('d') . ' ' . $daftarBulanIndo[date('m')] . ' ' . date('Y');

// Ekstrak minggu-minggu sesi yang ada secara otomatis dari database
$listMingguSesi = [];
$seenWeeks = [];
foreach ($sesi_kehadiran as $s) {
    $t = $s['tanggal'];
    $ts = strtotime($t);
    $mon = date('Y-m-d', strtotime('monday this week', $ts));
    $fri = date('Y-m-d', strtotime('friday this week', $ts));
    $wKey = $mon . '_' . $fri;
    if (!isset($seenWeeks[$wKey])) {
        $seenWeeks[$wKey] = true;
        $dMon = date('d', strtotime($mon));
        $dFri = date('d', strtotime($fri));
        $mFri = $daftarBulanIndo[date('m', strtotime($fri))];
        $mMon = $daftarBulanIndo[date('m', strtotime($mon))];
        $label = ($mMon === $mFri) ? "{$dMon} - {$dFri} {$mFri}" : "{$dMon} {$mMon} - {$dFri} {$mFri}";
        
        $listMingguSesi[] = [
            'start' => $mon,
            'end'   => $fri,
            'label' => $label
        ];
    }
}
?>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">
        
    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Sesi & Rekap Kehadiran Santri';
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => 'Portal Admin', 'url' => base_url('admin')],
                ['label' => 'Sesi & Rekap Kehadiran']
            ],
            'badge' => 'Lembaga Pendidikan E-RGI Syiar Gemilang',
            'title' => 'Sesi & Rekap Kehadiran Santri',
            'subtitle' => 'Pusat pengelolaan log absensi santri, pembuatan sesi halaqoh/kelas harian, serta analisis persentase kedisiplinan belajar santri.',
            'stats' => [
                ['icon' => 'fa-solid fa-calendar-check', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => $rata_kehadiran . '%', 'label' => 'Rata Presensi'],
                ['icon' => 'fa-solid fa-clock-rotate-left', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => count($sesi_kehadiran ?? []), 'label' => 'Total Sesi'],
                ['icon' => 'fa-solid fa-users', 'bg' => 'bg-amber-400/20 text-amber-200', 'value' => $total_siswa, 'label' => 'Santri Aktif'],
            ]
        ];
        require_once __DIR__ . '/components/hero_header.php';
        ?>

        <div class="p-6 space-y-6">

        <!-- Flashdata Notification -->
        <?php if (session()->getFlashdata('notif_msg')) : ?>
            <?php
                $type = session()->getFlashdata('notif_type') ?? 'add';
                $alertBg = ($type === 'delete') ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-emerald-50 border-emerald-200 text-emerald-900';
                $iconBg  = ($type === 'delete') ? 'bg-rose-600' : 'bg-emerald-600';
                $iconFa  = ($type === 'delete') ? 'fa-triangle-exclamation' : 'fa-circle-check';
            ?>
            <div id="ci4FlashAlert" class="rounded-xl border p-4 shadow-sm flex items-start justify-between gap-3 <?= $alertBg; ?>">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg <?= $iconBg; ?> text-white flex items-center justify-center text-base shrink-0 mt-0.5">
                        <i class="fa-solid <?= $iconFa; ?>"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold"><?= esc(session()->getFlashdata('notif_title') ?? 'Notifikasi Presensi'); ?></h4>
                        <p class="text-xs mt-0.5"><?= esc(session()->getFlashdata('notif_msg')); ?></p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('ci4FlashAlert').remove()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Summary Stats Cards (Riwayat 1 Bulan) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Rata-Rata Kehadiran</p>
                    <h3 class="text-2xl font-bold text-slate-800"><?= esc($rata_kehadiran); ?>%</h3>
                    <span class="text-3xs text-emerald-600 font-semibold">Akumulasi 1 Bulan Terakhir</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-brand-light text-brand flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Total Santri Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-800"><?= esc($total_siswa); ?> Santri</h3>
                    <span class="text-3xs text-slate-400">Terdata di 6 Jurusan</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Total Sesi 1 Bulan</p>
                    <h3 class="text-2xl font-bold text-slate-800"><?= count($sesi_kehadiran); ?> Sesi</h3>
                    <span class="text-3xs text-amber-600 font-semibold"><?= count($all_logs); ?> Log Presensi</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Presensi Prima (&ge;95%)</p>
                    <?php
                        $primaCount = 0;
                        foreach ($data_siswa as $s) {
                            if (($s['kehadiran'] ?? 0) >= 95) $primaCount++;
                        }
                    ?>
                    <h3 class="text-2xl font-bold text-slate-800"><?= $primaCount; ?> Santri</h3>
                    <span class="text-3xs text-sky-600 font-semibold">Kedisiplinan Sangat Baik</span>
                </div>
            </div>
        </div>

        <!-- Banner Info Riwayat 1 Bulan & Dinamisasi Tanggal -->
        <div class="bg-gradient-to-r from-emerald-50/80 via-white to-brand-light/80 border border-emerald-200/80 rounded-xl p-4 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-base shrink-0 mt-0.5">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        Presensi Berjalan Dinamis Sesuai Kalender & Tanggal
                        <div class="inline-flex items-center divide-x divide-slate-200 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-2xs">
  <span class="px-2.5 py-1"><?= $periodeAwal; ?></span>
  <span class="px-2.5 py-1 text-slate-500"><?= $periodeAkhir; ?></span>
</div>
                    </h4>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                        Sistem mencatat presensi secara <strong class="text-brand-deep">spesifik per tanggal realtime</strong>. Anda dapat melihat <strong>akumulasi 1 bulan</strong> (total <?= count($sesi_kehadiran); ?> sesi kegiatan), atau memfilter untuk memeriksa status presensi santri pada <strong>hari ini (<?= $todayFullIndo; ?>)</strong>, kemarin, maupun tanggal tertentu.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 self-end md:self-auto">
                <div class="inline-flex items-center divide-x divide-slate-200 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-2xs">
                    <span class="px-2.5 py-1 text-slate-500"><i class="fa-solid fa-calendar-check text-emerald-600 mr-1.5"></i>Hari Ini</span>
                    <span class="px-2.5 py-1 font-semibold text-slate-700"><?= $todayFullIndo; ?></span>
                </div>
            </div>
        </div>

        <!-- Section: Riwayat Sesi Pertemuan (Elegan & Minimalis sesuai Desain Mockup) -->
        <?php
        $sesiPreparedList = [];
        foreach ($sesi_kehadiran as $sesi) {
            $tgl = $sesi['tanggal'];
            $dayIndo = $daftarHariIndo[date('D', strtotime($tgl))] ?? date('D', strtotime($tgl));
            $d = date('d', strtotime($tgl));
            $m = $daftarBulanIndo[date('m', strtotime($tgl))] ?? date('M', strtotime($tgl));
            $y = date('Y', strtotime($tgl));
            $tglFormatted = "{$dayIndo}, {$d} {$m} {$y}";
            
            $cleanWaktu = trim(str_ireplace('WIB', '', $sesi['waktu']));
            
            $totPeserta = (int)$sesi['total_hadir'] + (int)($sesi['total_telat'] ?? 0) + (int)$sesi['total_sakit'] + (int)$sesi['total_alpa'];
            if ($totPeserta === 0) {
                $totPeserta = (int)($total_siswa ?: 50);
            }
            $persen = $totPeserta > 0 ? round(($sesi['total_hadir'] / $totPeserta) * 100, 1) : 100;
            
            $rawNama = $sesi['nama_sesi'];
            $judul = $rawNama;
            $pengajar = '';
            if (strpos($rawNama, ' - ') !== false) {
                $partsNama = explode(' - ', $rawNama, 2);
                $judul = trim($partsNama[0]);
                $pengajar = trim($partsNama[1]);
            }
            
            $rawKet = $sesi['keterangan'] ?? '';
            $materi = $rawKet;
            if (strpos($rawKet, '|') !== false) {
                $partsKet = explode('|', $rawKet);
                $p1 = trim($partsKet[0]);
                $p2 = trim($partsKet[1]);
                if (stripos($p1, 'Pengampu:') !== false) {
                    if (empty($pengajar)) {
                        $pengajar = trim(str_ireplace('Pengampu:', '', $p1));
                    }
                    $materi = $p2;
                } elseif (stripos($p2, 'Pengampu:') !== false) {
                    if (empty($pengajar)) {
                        $pengajar = trim(str_ireplace('Pengampu:', '', $p2));
                    }
                    $materi = $p1;
                }
            }
            
            if (empty($pengajar)) {
                $pengajar = 'Ustadz Kamal Malawi';
            }
            if (empty($materi)) {
                $materi = 'Sesi Pembelajaran Praktikum';
            }
            
            $sesiPreparedList[] = [
                'id'             => (int)$sesi['id'],
                'nama_sesi'      => $sesi['nama_sesi'],
                'judul'          => $judul,
                'materi'         => $materi,
                'pengajar'       => $pengajar,
                'lokasi'         => $sesi['lokasi'] ?: 'Aula Serbaguna',
                'tanggal'        => $sesi['tanggal'],
                'tanggal_format' => $tglFormatted,
                'waktu'          => $cleanWaktu,
                'waktu_raw'      => $sesi['waktu'],
                'total_hadir'    => (int)$sesi['total_hadir'],
                'total_telat'    => (int)($sesi['total_telat'] ?? 0),
                'total_sakit'    => (int)$sesi['total_sakit'],
                'total_alpa'     => (int)$sesi['total_alpa'],
                'total_peserta'  => $totPeserta,
                'persentase'     => $persen,
                'keterangan'     => $sesi['keterangan'] ?? '',
            ];
        }
        ?>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-brand"></i> Riwayat Sesi Pertemuan
                        <span id="sesiCountBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 shadow-2xs">
                            <?= count($sesi_kehadiran); ?> Sesi
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500">Daftar seluruh sesi kegiatan halaqah, pembelajaran teori, dan praktikum yang telah dilaksanakan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Toggle Tampilan: Kartu vs Tabel -->
                    <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-100 border border-slate-200 text-xs">
                        <button type="button" id="btnViewCards" onclick="switchSesiView('cards')" class="px-2.5 py-1 rounded-md bg-white text-brand shadow-2xs font-semibold flex items-center gap-1.5 cursor-pointer" title="Tampilan Kartu Elegan">
                            <i class="fa-solid fa-grip"></i> <span class="hidden sm:inline">Kartu</span>
                        </button>
                        <button type="button" id="btnViewTable" onclick="switchSesiView('table')" class="px-2.5 py-1 rounded-md text-slate-500 hover:text-slate-800 font-medium flex items-center gap-1.5 cursor-pointer" title="Tampilan Tabel Minimalis">
                            <i class="fa-solid fa-table-list"></i> <span class="hidden sm:inline">Tabel</span>
                        </button>
                    </div>

                    <button type="button" onclick="openAddSesiModal()" class="bg-brand hover:bg-brand-dark text-white text-xs font-semibold px-3.5 py-2 rounded-lg shadow-xs flex items-center gap-1.5 transition shrink-0 cursor-pointer">
                        <i class="fa-solid fa-plus text-xs"></i> Catat Sesi Baru
                    </button>
                </div>
            </div>

            <!-- Filter Tanggal, Pencarian & Realtime Datepicker -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pt-1">
                <!-- Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                    <span class="text-slate-400 font-medium shrink-0 flex items-center gap-1">
                        <i class="fa-solid fa-filter text-slate-400 text-[11px]"></i> Filter Sesi:
                    </span>
                    <button type="button" onclick="filterSesiByFilter('all', event)" id="btnSesiAll" class="btn-filter-sesi px-2.5 py-1 rounded-lg bg-brand text-white font-semibold shadow-xs transition shrink-0 cursor-pointer">
                        Semua (<?= count($sesi_kehadiran); ?>)
                    </button>
                    <button type="button" onclick="filterSesiByFilter('today', event)" id="btnSesiToday" class="btn-filter-sesi px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition shrink-0 cursor-pointer">
                        Hari Ini (<?= $todayLabel; ?>)
                    </button>
                    <button type="button" onclick="filterSesiByFilter('yesterday', event)" id="btnSesiYesterday" class="btn-filter-sesi px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition shrink-0 cursor-pointer">
                        Kemarin (<?= $yesterdayLabel; ?>)
                    </button>
                    <button type="button" onclick="filterSesiByFilter('month:<?= $currentMonthKey; ?>', event)" id="btnSesiNov" class="btn-filter-sesi px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition shrink-0 cursor-pointer">
                        <?= $currentMonthLabel; ?>
                    </button>
                    <?php foreach ($listMingguSesi as $wIdx => $w): ?>
                        <button type="button" onclick="filterSesiByFilter('range:<?= $w['start']; ?>:<?= $w['end']; ?>', event)" class="btn-filter-sesi px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition shrink-0 cursor-pointer">
                            <?= esc($w['label']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Search Input & Custom Date Picker -->
                <div class="flex items-center gap-2.5 text-xs shrink-0 self-start lg:self-auto flex-wrap">
                    <!-- Search Input -->
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
                        <input type="text" id="searchSesiInput" oninput="onSearchSesiChange(this.value)" placeholder="Cari sesi atau materi..." class="pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand w-44 sm:w-56 bg-white placeholder:text-slate-400">
                    </div>

                    <!-- Date Picker -->
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                        <i class="fa-regular fa-calendar text-brand"></i>
                        <span class="hidden sm:inline">Pilih Tanggal:</span>
                        <input type="date" id="inputCustomDateSesi" value="<?= $todayRealtimeDate; ?>" onchange="filterSesiByCustomDate(this.value)" class="px-2.5 py-1 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand bg-white cursor-pointer" title="Pilih Tanggal Sesi Realtime">
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div id="sesiEmptyState" class="hidden py-12 text-center bg-slate-50/80 rounded-xl border border-dashed border-slate-300">
                <div class="w-12 h-12 rounded-full bg-slate-200/80 text-slate-500 flex items-center justify-center mx-auto mb-2 text-xl">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <p class="text-sm font-bold text-slate-700">Tidak ada sesi pertemuan yang sesuai</p>
                <p class="text-xs text-slate-500 mt-0.5">Coba sesuaikan kata kunci pencarian, filter tanggal, atau klik "Catat Sesi Baru".</p>
            </div>

            <!-- View 1: 2-Column Minimalist Grid of Cards (Persis seperti Image Mockup) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" id="sesiCardsGrid">
                <!-- Di-render secara dinamis via JS -->
            </div>

            <!-- View 2: Minimalist Table View (Alternatif Mode Tampilan) -->
            <div class="hidden overflow-x-auto rounded-xl border border-slate-200 shadow-2xs" id="sesiTableView">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <th class="py-3 px-3.5 text-center w-12 border-r border-slate-200">No</th>
                            <th class="py-3 px-4 border-r border-slate-200 w-44">Tanggal & Waktu</th>
                            <th class="py-3 px-4 border-r border-slate-200">Sesi & Materi</th>
                            <th class="py-3 px-4 border-r border-slate-200 w-48">Pengajar & Lokasi</th>
                            <th class="py-3 px-4 text-center border-r border-slate-200 w-36">Kehadiran</th>
                            <th class="py-3 px-4 text-center border-r border-slate-200 w-48">Breakdown</th>
                            <th class="py-3 px-3 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100" id="sesiTableBody">
                        <!-- Di-render via JS -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer (Persis seperti Image Mockup) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
                <div class="text-slate-500 font-medium" id="sesiPaginationInfo">
                    Menampilkan 1 sampai 6 dari <?= count($sesi_kehadiran); ?> sesi
                </div>
                <div class="flex items-center gap-1.5 self-center sm:self-auto" id="sesiPaginationButtons">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
        </div>

        <!-- Section: Tabel Santri dengan Pilihan Kehadiran & Rekap 1 Bulan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check text-brand"></i> Presensi Santri & Rekap Kehadiran
                    </h3>
                    <p class="text-xs text-slate-500">
                        Mode Tampilan: <span id="labelTampilanMode" class="font-bold text-brand">Akumulasi 1 Bulan (<?= count($sesi_kehadiran); ?> Sesi)</span>. Ubah filter tanggal di samping untuk memeriksa status santri pada hari spesifik.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Filter Tanggal Tabel Santri (Realtime) -->
                    <div class="flex items-center gap-1 bg-slate-100/90 p-1 rounded-lg border border-slate-200 text-xs">
                        <button type="button" id="btnTblAll" onclick="setTableDateFilter('all')" class="btn-tbl-date px-2.5 py-1 rounded-md font-bold bg-brand text-white shadow-2xs transition cursor-pointer">
                            Semua (<?= count($sesi_kehadiran); ?> Sesi)
                        </button>
                        <button type="button" id="btnTblToday" onclick="setTableDateFilter('today')" class="btn-tbl-date px-2.5 py-1 rounded-md font-semibold text-slate-600 hover:text-slate-900 transition cursor-pointer">
                            Hari Ini (<?= $todayLabel; ?>)
                        </button>
                        <button type="button" id="btnTblYesterday" onclick="setTableDateFilter('yesterday')" class="btn-tbl-date px-2.5 py-1 rounded-md font-semibold text-slate-600 hover:text-slate-900 transition cursor-pointer">
                            Kemarin (<?= $yesterdayLabel; ?>)
                        </button>
                        <div class="flex items-center gap-1 pl-1 border-l border-slate-200">
                            <input type="date" id="inputCustomDateTbl" value="<?= $todayRealtimeDate; ?>" onchange="setTableDateFilter(this.value)" class="px-2 py-0.5 text-xs rounded border border-slate-300 focus:outline-none focus:ring-1 focus:ring-brand bg-white cursor-pointer" title="Pilih Tanggal Presensi Santri Realtime">
                        </div>
                    </div>

                    <select id="filterKelasKehadiran" onchange="filterKehadiranTable()" class="px-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand">
                        <option value="all">Semua Jurusan</option>
                        <?php foreach ($rekap_kehadiran_kelas as $rk): ?>
                            <option value="<?= esc($rk['kelas']); ?>"><?= esc($rk['kelas']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
                        <input type="text" id="searchKehadiranInput" onkeyup="filterKehadiranTable()" placeholder="Cari santri..." class="pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand w-36 sm:w-44">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full text-left border-collapse" id="tableKehadiran">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700 uppercase">
                            <th class="py-3 px-3 text-center w-12 border-r border-slate-200">No</th>
                            <th class="py-3 px-3 border-r border-slate-200 w-16">ID</th>
                            <th class="py-3 px-3 border-r border-slate-200">Nama Santri</th>
                            <th class="py-3 px-3 border-r border-slate-200">Kelas / Jurusan</th>
                            <th class="py-3 px-3 text-center border-r border-slate-200 bg-slate-100/80 text-slate-800 w-64 tracking-wider text-[11px]">
                                Catat Kehadiran (Pilih Sesi)
                            </th>
                            <th class="py-3 px-3 text-center border-r border-slate-200 bg-slate-100/60 text-slate-800 w-56 tracking-wider text-[11px]" id="thRekapHeader">
                                Rekap 1 Bulan (<?= count($sesi_kehadiran); ?> Sesi)
                            </th>
                            <th class="py-3 px-3 text-center border-r border-slate-200 bg-slate-100/80 text-slate-800 w-24 tracking-wider text-[11px]">
                                Kehadiran (%)
                            </th>
                            <th class="py-3 px-3 text-center w-28">Riwayat</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200" id="tbodyKehadiran">
                        <?php foreach ($data_siswa as $idx => $s): ?>
                        <?php 
                            $pct = (int)($s['kehadiran'] ?? 95);
                            $lastLog = $presensi_terbaru[$s['id']] ?? null;
                            $rekap = $rekap_bulanan[$s['id']] ?? ['total_sesi' => count($sesi_kehadiran), 'count_hadir' => count($sesi_kehadiran), 'count_telat' => 0, 'count_sakit' => 0, 'count_absen' => 0];

                            if ($pct >= 95) {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                $statusLabel = 'Sangat Rajin';
                            } elseif ($pct >= 90) {
                                $badgeClass = 'bg-brand-light text-brand border-brand-border';
                                $statusLabel = 'Disiplin';
                            } else {
                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                $statusLabel = 'Cukup';
                            }
                        ?>
                        <tr class="row-santri <?= $idx % 2 === 1 ? 'bg-slate-50/70' : 'bg-white'; ?> hover:bg-slate-100/50 transition" data-nama="<?= strtolower(esc($s['nama'])); ?>" data-kelas="<?= esc($s['kelas']); ?>">
                            <td class="py-3 px-3 text-center border-r border-slate-200 font-bold text-xs text-slate-500"><?= $idx + 1; ?></td>
                            <td class="py-3 px-3 border-r border-slate-200 font-mono text-xs text-slate-500 font-semibold"><?= esc($s['id']); ?></td>
                            <td class="py-3 px-3 border-r border-slate-200 font-medium text-slate-800">
                                <?= esc($s['nama']); ?>
                                <?php if ($lastLog): ?>
                                    <span class="block text-3xs text-slate-400 mt-0.5 truncate" title="<?= esc($lastLog['nama_sesi']); ?>">
                                        Terakhir: <strong class="<?= $lastLog['status'] === 'Hadir' ? 'text-emerald-600' : ($lastLog['status'] === 'Telat' ? 'text-amber-600' : 'text-sky-600'); ?>"><?= esc($lastLog['status']); ?></strong> (<?= date('d/m', strtotime($lastLog['tanggal'])); ?>)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <?php
                                $jThemeKehadiran = [
                                    'Desain Grafis'                => 'bg-red-50 text-red-700 border-red-200',
                                    'Teknik Komputer dan Jaringan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Aplikasi Perkantoran'         => 'bg-orange-50 text-orange-700 border-orange-200',
                                    'Tata Busana'                  => 'bg-yellow-50 text-yellow-800 border-yellow-300',
                                    'Otomotif'                     => 'bg-slate-100 text-slate-900 border-slate-300',
                                    'Fotografi dan Videografi'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                ];
                                $bdgK = $jThemeKehadiran[$s['kelas']] ?? 'bg-blue-50 text-blue-700 border-blue-200';
                            ?>
                            <td class="py-3 px-3 border-r border-slate-200 text-xs">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-2xs font-semibold border <?= $bdgK; ?> shadow-2xs">
                                    <i class="fa-solid fa-graduation-cap text-[10px]"></i>
                                    <?= esc($s['kelas']); ?>
                                </span>
                            </td>
                            
                            <!-- Pilihan Tombol Kehadiran: Segmented Micro-Toolbar Elegan -->
                            <td class="py-2.5 px-3 border-r border-slate-200 text-center bg-slate-50/50">
                                <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-100/90 border border-slate-200/90 shadow-2xs divide-x divide-slate-200">
                                    <!-- Tombol Hadir -->
                                    <button type="button" 
                                        onclick='openPilihSesiModal(<?= json_encode($s); ?>, "Hadir")'
                                        title="Tandai Hadir"
                                        class="px-2 py-1 text-2xs font-bold text-slate-700 hover:text-emerald-700 hover:bg-white rounded transition-all flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Hadir
                                    </button>

                                    <!-- Tombol Telat -->
                                    <button type="button" 
                                        onclick='openPilihSesiModal(<?= json_encode($s); ?>, "Telat")'
                                        title="Tandai Terlambat"
                                        class="px-2 py-1 text-2xs font-bold text-slate-700 hover:text-amber-700 hover:bg-white rounded transition-all flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-clock text-amber-500 text-[10px]"></i> Telat
                                    </button>

                                    <!-- Tombol Sakit -->
                                    <button type="button" 
                                        onclick='openPilihSesiModal(<?= json_encode($s); ?>, "Sakit")'
                                        title="Tandai Izin Sakit"
                                        class="px-2 py-1 text-2xs font-bold text-slate-700 hover:text-sky-700 hover:bg-white rounded transition-all flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-stethoscope text-sky-500 text-[10px]"></i> Sakit
                                    </button>

                                    <!-- Tombol Absen -->
                                    <button type="button" 
                                        onclick='openPilihSesiModal(<?= json_encode($s); ?>, "Absen")'
                                        title="Tandai Alpa / Tidak Hadir"
                                        class="px-2 py-1 text-2xs font-bold text-slate-700 hover:text-rose-700 hover:bg-white rounded transition-all flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-xmark text-rose-500 text-[10px]"></i> Absen
                                    </button>
                                </div>
                            </td>

                            <!-- Rekapitulasi 1 Bulan: Strip Metrik Presensi Eksekutif -->
                            <td class="py-3 px-2 border-r border-slate-200 text-center td-status-col" data-santri-id="<?= $s['id']; ?>">
                                <!-- View 1: Akumulasi 1 Bulan (<?= count($sesi_kehadiran); ?> Sesi) -->
                                <div class="view-rekap-akumulasi inline-flex items-center divide-x divide-slate-200/90 rounded border border-slate-200/90 bg-white shadow-2xs text-[11px] font-mono tracking-tight overflow-hidden" id="view-rekap-<?= $s['id']; ?>">
                                    <span class="px-2 py-0.5 font-bold <?= $rekap['count_hadir'] > 0 ? 'text-emerald-700 bg-emerald-50/50' : 'text-slate-400'; ?>" title="Hadir: <?= $rekap['count_hadir']; ?> Sesi">
                                        <span class="text-slate-400 font-normal mr-0.5 text-[9.5px]">H:</span><?= $rekap['count_hadir']; ?>
                                    </span>
                                    <span class="px-2 py-0.5 font-semibold <?= $rekap['count_telat'] > 0 ? 'text-amber-800 bg-amber-50/50' : 'text-slate-400'; ?>" title="Telat: <?= $rekap['count_telat']; ?> Sesi">
                                        <span class="text-slate-400 font-normal mr-0.5 text-[9.5px]">T:</span><?= $rekap['count_telat']; ?>
                                    </span>
                                    <span class="px-2 py-0.5 font-semibold <?= $rekap['count_sakit'] > 0 ? 'text-sky-800 bg-sky-50/50' : 'text-slate-400'; ?>" title="Sakit: <?= $rekap['count_sakit']; ?> Sesi">
                                        <span class="text-slate-400 font-normal mr-0.5 text-[9.5px]">S:</span><?= $rekap['count_sakit']; ?>
                                    </span>
                                    <span class="px-2 py-0.5 font-bold <?= $rekap['count_absen'] > 0 ? 'text-rose-700 bg-rose-50/70' : 'text-slate-400'; ?>" title="Absen: <?= $rekap['count_absen']; ?> Sesi">
                                        <span class="text-slate-400 font-normal mr-0.5 text-[9.5px]">A:</span><?= $rekap['count_absen']; ?>
                                    </span>
                                </div>
                                <!-- View 2: Status Spesifik Tanggal (Dinamis via JS) -->
                                <div class="view-status-tanggal hidden" id="view-tgl-<?= $s['id']; ?>"></div>
                            </td>

                            <!-- Persentase Kehadiran 1 Bulan -->
                            <td class="py-3 px-3 border-r border-slate-200 text-center bg-slate-50/30">
                                <span class="font-bold text-slate-800 text-xs font-mono"><?= $pct; ?>%</span>
                            </td>

                            <!-- Tombol Aksi Detail Riwayat -->
                            <td class="py-3 px-3 text-center">
                                <button type="button" 
                                    onclick='openRiwayatSantriModal(<?= json_encode($s); ?>)'
                                    class="px-2.5 py-1 bg-white hover:bg-brand-light border border-brand-border text-brand hover:text-brand-deep rounded-md text-xs font-semibold shadow-2xs transition flex items-center justify-center gap-1 mx-auto">
                                    <i class="fa-solid fa-list-ul"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        </div> <!-- /p-6 wrapper -->
    </main>
</div>

<!-- ========================================================================= -->
<!-- MODAL: Pilih Sesi Kehadiran (Muncul saat klik Hadir / Telat / Sakit / Absen) -->
<!-- ========================================================================= -->
<div id="modalPilihSesiKehadiran" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in duration-200">
        
        <!-- Header Modal -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-brand-light text-brand flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Catat Presensi Sesi Santri</h3>
                    <p class="text-xs text-slate-500">Pilih sesi kegiatan yang dihadiri santri</p>
                </div>
            </div>
            <button type="button" onclick="closePilihSesiModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form Presensi Sesi -->
        <form action="<?= base_url('admin/kehadiran/catat'); ?>" method="post" class="p-6 space-y-4">
            <?= csrf_field(); ?>
            <input type="hidden" name="santri_id" id="modal_santri_id">

            <!-- Card Profil Santri Terpilih -->
            <div class="p-3.5 bg-brand-light/80 rounded-xl border border-brand-border flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center font-bold text-sm shadow-2xs shrink-0">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900" id="modal_santri_nama">Nama Santri</h4>
                        <div class="flex items-center gap-2 text-2xs text-slate-500 mt-0.5">
                            <span id="modal_santri_id_badge" class="font-mono font-semibold">ID: -</span>
                            <span>•</span>
                            <span id="modal_santri_kelas" class="font-semibold text-brand">Kelas</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center divide-x divide-slate-200 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-2xs">
                        <span class="px-2.5 py-1 text-slate-500">Kehadiran</span>
                        <span id="modal_santri_kehadiran_badge" class="px-2.5 py-1 font-bold text-emerald-600">95%</span>
                    </div>
                </div>
            </div>

            <!-- Pilihan Status Kehadiran (Hadir / Telat / Sakit / Absen) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Status Presensi Santri
                </label>
                <div class="grid grid-cols-4 gap-2" id="modalStatusGroup">
                    <label class="status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-emerald-400 transition" id="label-status-hadir">
                        <input type="radio" name="status" value="Hadir" id="radio-hadir" class="sr-only" onchange="updateStatusVisuals('Hadir')">
                        <i class="fa-solid fa-check text-base text-emerald-600 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">Hadir</span>
                    </label>

                    <label class="status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-amber-400 transition" id="label-status-telat">
                        <input type="radio" name="status" value="Telat" id="radio-telat" class="sr-only" onchange="updateStatusVisuals('Telat')">
                        <i class="fa-solid fa-clock text-base text-amber-600 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">Telat</span>
                    </label>

                    <label class="status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-sky-400 transition" id="label-status-sakit">
                        <input type="radio" name="status" value="Sakit" id="radio-sakit" class="sr-only" onchange="updateStatusVisuals('Sakit')">
                        <i class="fa-solid fa-stethoscope text-base text-sky-600 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">Sakit</span>
                    </label>

                    <label class="status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-rose-400 transition" id="label-status-absen">
                        <input type="radio" name="status" value="Absen" id="radio-absen" class="sr-only" onchange="updateStatusVisuals('Absen')">
                        <i class="fa-solid fa-xmark text-base text-rose-600 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">Absen</span>
                    </label>
                </div>
            </div>

            <!-- Pilihan Sesi Pertemuan ("dia hadir di sesi apa") -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    <i class="fa-solid fa-calendar-days text-brand mr-1"></i>Pilih Sesi Kegiatan (Riwayat 1 Bulan)
                </label>
                <select name="sesi_id" id="modal_sesi_id" onchange="onModalSesiChange()" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand bg-white">
                    <?php foreach ($sesi_kehadiran as $idx => $sesi): ?>
                        <option value="<?= $sesi['id']; ?>" data-tanggal="<?= $sesi['tanggal']; ?>" <?= $idx === 0 ? 'selected' : ''; ?>>
                            [<?= date('d M Y', strtotime($sesi['tanggal'])); ?>] <?= esc($sesi['nama_sesi']); ?> (<?= esc($sesi['waktu']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-3xs text-slate-400 mt-1">Sesi & tanggal otomatis tersinkronisasi. Anda dapat mengubah tanggal atau memilih sesi kapan pun.</p>
            </div>

            <!-- Tanggal Presensi -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Presensi</label>
                    <input type="date" name="tanggal" id="modal_tanggal" value="<?= date('Y-m-d'); ?>" onchange="onModalTanggalChange()" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Pencatatan</label>
                    <input type="text" readonly value="<?= date('H:i'); ?> WIB" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-500 font-mono">
                </div>
            </div>

            <!-- Catatan / Keterangan Alasan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Alasan (Opsional)</label>
                <input type="text" name="keterangan" id="modal_keterangan" placeholder="Contoh: Hadir tepat waktu / Izin sakit flu / Terlambat 15 menit..." class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="closePilihSesiModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-brand hover:bg-brand-dark text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i> Simpan Presensi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: Detail Riwayat Presensi 1 Bulan Santri -->
<!-- ========================================================================= -->
<div id="modalDetailRiwayatSantri" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in duration-200">
        
        <!-- Header Modal -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-slate-800" id="hist_nama">Riwayat Presensi Santri</h3>
                    <p class="text-xs text-slate-500" id="hist_sub">Catatan kehadiran seluruh sesi dalam 1 bulan terakhir</p>
                </div>
            </div>
            <button type="button" onclick="closeRiwayatSantriModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Body Modal: Ringkasan & Tabel Sesi -->
        <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <!-- Ringkasan Statistik Santri -->
            <div class="grid grid-cols-4 gap-2 text-center text-xs" id="hist_stat_grid">
                <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200">
                    <span class="text-3xs text-emerald-700 block font-semibold uppercase">Hadir</span>
                    <span class="text-lg font-black text-emerald-800" id="hist_cnt_hadir">0</span>
                </div>
                <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200">
                    <span class="text-3xs text-amber-700 block font-semibold uppercase">Telat</span>
                    <span class="text-lg font-black text-amber-800" id="hist_cnt_telat">0</span>
                </div>
                <div class="p-2.5 rounded-xl bg-sky-50 border border-sky-200">
                    <span class="text-3xs text-sky-700 block font-semibold uppercase">Sakit</span>
                    <span class="text-lg font-black text-sky-800" id="hist_cnt_sakit">0</span>
                </div>
                <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200">
                    <span class="text-3xs text-rose-700 block font-semibold uppercase">Absen</span>
                    <span class="text-lg font-black text-rose-800" id="hist_cnt_absen">0</span>
                </div>
            </div>

            <!-- List Catatan Sesi -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Sesi Presensi (<?= count($sesi_kehadiran); ?> Sesi)</span>
                    <div class="inline-flex items-center divide-x divide-slate-200 rounded-lg border border-slate-200 bg-white text-2xs font-medium text-slate-700 shadow-2xs">
                        <span class="px-2 py-0.5 text-slate-500">Kehadiran</span>
                        <span id="hist_pct_span" class="px-2 py-0.5 font-bold text-emerald-600">95%</span>
                    </div>
                </div>
                <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto" id="hist_log_list">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end shrink-0">
            <button type="button" onclick="closeRiwayatSantriModal()" class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: Tambah Sesi Kehadiran Baru -->
<!-- ========================================================================= -->
<div id="modalAddSesi" class="fixed inset-0 bg-slate-900/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Catat Sesi Kehadiran Baru</h3>
            <button type="button" onclick="closeAddSesiModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= base_url('admin/kehadiran/add_sesi'); ?>" method="post" class="p-6 space-y-4">
            <?= csrf_field(); ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Sesi / Kegiatan</label>
                <input type="text" name="nama_sesi" required placeholder="Contoh: Kelas Tauhid - Ustadz Kamal Malawi / Kelas Fiqih - Ustadzah Yanni Susanti" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu</label>
                    <input type="text" name="waktu" value="07:00 - 09:00 WIB" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Pertemuan</label>
                <input type="text" name="lokasi" value="Aula Serbaguna" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div class="grid grid-cols-4 gap-2">
                <div>
                    <label class="block text-2xs font-semibold text-emerald-700 mb-1">Hadir</label>
                    <input type="number" name="total_hadir" value="48" min="0" max="50" class="w-full px-2 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block text-2xs font-semibold text-amber-700 mb-1">Telat</label>
                    <input type="number" name="total_telat" value="1" min="0" max="50" class="w-full px-2 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block text-2xs font-semibold text-sky-700 mb-1">Sakit</label>
                    <input type="number" name="total_sakit" value="1" min="0" max="50" class="w-full px-2 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block text-2xs font-semibold text-rose-700 mb-1">Absen</label>
                    <input type="number" name="total_alpa" value="0" min="0" max="50" class="w-full px-2 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Materi Pembelajaran</label>
                <input type="text" name="keterangan" placeholder="Contoh: Pembelajaran adab & kejuruan semester ganjil" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="closeAddSesiModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-brand text-white text-xs font-semibold hover:bg-brand-dark shadow-xs">Simpan Sesi</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: Detail Sesi Pertemuan (Informasi Lengkap & Log Kehadiran) -->
<!-- ========================================================================= -->
<div id="modalDetailSesi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in duration-200">
        <!-- Header -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-brand-light text-brand flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-slate-800" id="detail_sesi_judul">Detail Sesi Pertemuan</h3>
                    <p class="text-xs text-slate-500" id="detail_sesi_sub">Informasi lengkap kegiatan dan rekapan presensi</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailSesiModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <!-- Info Box Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-calendar text-brand"></i>
                    </div>
                    <div>
                        <span class="text-3xs text-slate-400 block font-medium uppercase">Tanggal Pelaksanaan</span>
                        <span class="font-bold text-slate-800" id="detail_sesi_tanggal">-</span>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-clock text-brand"></i>
                    </div>
                    <div>
                        <span class="text-3xs text-slate-400 block font-medium uppercase">Waktu Sesi</span>
                        <span class="font-bold text-slate-800 font-mono" id="detail_sesi_waktu">-</span>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-user text-brand"></i>
                    </div>
                    <div>
                        <span class="text-3xs text-slate-400 block font-medium uppercase">Pengajar / Instruktur</span>
                        <span class="font-bold text-slate-800" id="detail_sesi_pengajar">-</span>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-location-dot text-brand"></i>
                    </div>
                    <div>
                        <span class="text-3xs text-slate-400 block font-medium uppercase">Ruangan / Tempat</span>
                        <span class="font-bold text-slate-800" id="detail_sesi_lokasi">-</span>
                    </div>
                </div>
                <div class="col-span-full pt-2 border-t border-slate-200/70">
                    <span class="text-3xs text-slate-400 block font-medium uppercase">Materi Pembelajaran</span>
                    <p class="font-semibold text-slate-700 mt-0.5" id="detail_sesi_materi">-</p>
                </div>
            </div>

            <!-- 4 Stat Badges -->
            <div class="grid grid-cols-4 gap-2 text-center text-xs">
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <span class="text-3xs text-emerald-700 block font-semibold uppercase">Hadir</span>
                    <span class="text-xl font-black text-emerald-800" id="detail_sesi_hadir">0</span>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                    <span class="text-3xs text-amber-700 block font-semibold uppercase">Telat</span>
                    <span class="text-xl font-black text-amber-800" id="detail_sesi_telat">0</span>
                </div>
                <div class="p-3 rounded-xl bg-sky-50 border border-sky-200">
                    <span class="text-3xs text-sky-700 block font-semibold uppercase">Sakit</span>
                    <span class="text-xl font-black text-sky-800" id="detail_sesi_sakit">0</span>
                </div>
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200">
                    <span class="text-3xs text-rose-700 block font-semibold uppercase">Absen</span>
                    <span class="text-xl font-black text-rose-800" id="detail_sesi_alpa">0</span>
                </div>
            </div>

            <!-- Tingkat Kehadiran Bar -->
            <div class="p-3.5 rounded-xl bg-white border border-slate-200 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-700">Tingkat Kedisiplinan & Presensi</span>
                    <span class="font-bold text-emerald-600" id="detail_sesi_persen">0% Hadir</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div id="detail_sesi_bar" class="h-full rounded-full bg-emerald-500 transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <!-- Log Santri Terkait Sesi Ini -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Santri Terdata Pada Sesi Ini</span>
                    <span class="text-2xs font-semibold text-slate-700 bg-white px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs" id="detail_sesi_santri_count">0 Santri</span>
                </div>
                <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto" id="detail_sesi_santri_list">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
            <button type="button" id="btnEditFromDetail" class="px-4 py-2 rounded-lg bg-brand hover:bg-brand-dark text-white text-xs font-semibold transition cursor-pointer flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-pen text-xs"></i> Edit Sesi Ini
            </button>
            <button type="button" onclick="closeDetailSesiModal()" class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: Edit Sesi Pertemuan -->
<!-- ========================================================================= -->
<div id="modalEditSesi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in duration-200">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-brand-light text-brand flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-pen"></i>
                </span>
                <h3 class="text-base font-bold text-slate-800">Edit Sesi Pertemuan</h3>
            </div>
            <button type="button" onclick="closeEditSesiModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="<?= base_url('admin/kehadiran/update_sesi'); ?>" method="post" class="p-6 space-y-4">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="edit_sesi_id">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Sesi / Judul</label>
                <input type="text" name="nama_sesi" id="edit_sesi_nama" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_sesi_tanggal" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu</label>
                    <input type="text" name="waktu" id="edit_sesi_waktu" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Pertemuan</label>
                <input type="text" name="lokasi" id="edit_sesi_lokasi" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div class="grid grid-cols-4 gap-2">
                <div>
                    <label class="block text-2xs font-semibold text-emerald-700 mb-1">Hadir</label>
                    <input type="number" name="total_hadir" id="edit_sesi_hadir" min="0" max="100" class="w-full px-2 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block text-2xs font-semibold text-amber-700 mb-1">Telat</label>
                    <input type="number" name="total_telat" id="edit_sesi_telat" min="0" max="100" class="w-full px-2 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block text-2xs font-semibold text-sky-700 mb-1">Sakit</label>
                    <input type="number" name="total_sakit" id="edit_sesi_sakit" min="0" max="100" class="w-full px-2 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block text-2xs font-semibold text-rose-700 mb-1">Absen</label>
                    <input type="number" name="total_alpa" id="edit_sesi_alpa" min="0" max="100" class="w-full px-2 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Materi Pembelajaran</label>
                <input type="text" name="keterangan" id="edit_sesi_keterangan" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                <button type="button" onclick="confirmDeleteSesi()" class="px-3 py-2 text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                    <i class="fa-regular fa-trash-can mr-1"></i> Hapus Sesi
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeEditSesiModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-brand text-white text-xs font-semibold hover:bg-brand-dark shadow-xs cursor-pointer">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: Konfirmasi Hapus Sesi -->
<!-- ========================================================================= -->
<div id="modalDeleteSesi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md p-6 text-center animate-in fade-in duration-200">
        <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3 text-xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800">Hapus Sesi Pertemuan?</h3>
        <p class="text-xs text-slate-500 mt-1 mb-4 leading-relaxed">
            Apakah Anda yakin ingin menghapus sesi <strong class="text-slate-800" id="del_sesi_nama">Sesi</strong>? Tindakan ini akan menghapus log presensi terkait pada sesi ini.
        </p>
        <form action="<?= base_url('admin/kehadiran/delete_sesi'); ?>" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="del_sesi_id">
            <div class="flex items-center justify-center gap-2">
                <button type="button" onclick="closeDeleteSesiModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs cursor-pointer">
                    Ya, Hapus Sesi
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// Data Logs Lengkap dari PHP
const allLogsData = <?= json_encode($all_logs); ?>;
const rekapBulananData = <?= json_encode($rekap_bulanan); ?>;

// Kelompokkan log per santri_id
const logsBySantri = {};
// Indeks cepat per tanggal dan santri_id
const logsByDateAndSantri = {};

allLogsData.forEach(l => {
    // Grouping per santri
    if (!logsBySantri[l.santri_id]) {
        logsBySantri[l.santri_id] = [];
    }
    logsBySantri[l.santri_id].push(l);

    // Grouping per date + santri
    const dateKey = l.tanggal + '_' + l.santri_id;
    if (!logsByDateAndSantri[dateKey]) {
        logsByDateAndSantri[dateKey] = [];
    }
    logsByDateAndSantri[dateKey].push(l);
});

// Format Tanggal ke Bahasa Indonesia (DD Mmm YYYY)
function formatDateIndo(dateStr) {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    if (parts.length !== 3) return dateStr;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const d = parseInt(parts[2], 10);
    const m = months[parseInt(parts[1], 10) - 1] || parts[1];
    const y = parts[0];
    return `${d < 10 ? '0' + d : d} ${m} ${y}`;
}

// Tanggal Realtime dari Server
const REALTIME_TODAY     = '<?= $todayRealtimeDate; ?>';
const REALTIME_YESTERDAY = '<?= $yesterdayRealtimeDate; ?>';

// State Aktif Tanggal Tabel
let activeTableDate = 'all';

// Fungsi Switch Filter Tanggal Tabel Santri
function setTableDateFilter(dateVal) {
    // Reset visual tombol tanggal
    document.querySelectorAll('.btn-tbl-date').forEach(btn => {
        btn.classList.remove('bg-brand', 'text-white', 'shadow-2xs', 'font-bold');
        btn.classList.add('text-slate-600', 'font-semibold');
    });

    let targetDate = dateVal;
    if (dateVal === 'today') {
        targetDate = REALTIME_TODAY;
        const b = document.getElementById('btnTblToday');
        if (b) { b.classList.add('bg-brand', 'text-white', 'shadow-2xs', 'font-bold'); b.classList.remove('text-slate-600'); }
        const inputTbl = document.getElementById('inputCustomDateTbl');
        if (inputTbl) inputTbl.value = targetDate;
    } else if (dateVal === 'yesterday') {
        targetDate = REALTIME_YESTERDAY;
        const b = document.getElementById('btnTblYesterday');
        if (b) { b.classList.add('bg-brand', 'text-white', 'shadow-2xs', 'font-bold'); b.classList.remove('text-slate-600'); }
        const inputTbl = document.getElementById('inputCustomDateTbl');
        if (inputTbl) inputTbl.value = targetDate;
    } else if (dateVal === 'all') {
        targetDate = 'all';
        const b = document.getElementById('btnTblAll');
        if (b) { b.classList.add('bg-brand', 'text-white', 'shadow-2xs', 'font-bold'); b.classList.remove('text-slate-600'); }
    } else {
        // Custom date picker
        targetDate = dateVal;
        const inputTbl = document.getElementById('inputCustomDateTbl');
        if (inputTbl) inputTbl.value = targetDate;
    }

    activeTableDate = targetDate;
    const labelMode = document.getElementById('labelTampilanMode');
    const thHeader  = document.getElementById('thRekapHeader');

    const allRekapEls = document.querySelectorAll('.view-rekap-akumulasi');
    const allDateEls  = document.querySelectorAll('.view-status-tanggal');

    if (targetDate === 'all') {
        const totalCount = <?= count($sesi_kehadiran); ?>;
        if (labelMode) labelMode.textContent = 'Akumulasi 1 Bulan (' + totalCount + ' Sesi)';
        if (thHeader) thHeader.innerHTML = 'Rekap 1 Bulan (' + totalCount + ' Sesi)';
        
        allRekapEls.forEach(el => el.classList.remove('hidden'));
        allDateEls.forEach(el => el.classList.add('hidden'));
    } else {
        const formatted = formatDateIndo(targetDate);
        if (labelMode) labelMode.textContent = 'Status Presensi Tanggal ' + formatted;
        if (thHeader) thHeader.innerHTML = `<span class="text-brand-dark"><i class="fa-solid fa-calendar-day mr-1"></i>Presensi: ${formatted}</span>`;

        allRekapEls.forEach(el => el.classList.add('hidden'));
        allDateEls.forEach(el => el.classList.remove('hidden'));

        // Render status masing-masing santri pada tanggal targetDate
        document.querySelectorAll('.td-status-col').forEach(td => {
            const santriId = td.getAttribute('data-santri-id');
            const container = document.getElementById('view-tgl-' + santriId);
            if (!container) return;

            const logs = logsByDateAndSantri[targetDate + '_' + santriId] || [];
            if (logs.length === 0) {
                container.innerHTML = `
                    <span class="px-2 py-0.5 rounded text-3xs text-slate-400 bg-slate-100 font-medium inline-flex items-center gap-1 border border-slate-200">
                        <i class="fa-solid fa-minus text-slate-300"></i> Tidak ada sesi
                    </span>
                `;
            } else {
                let html = '<div class="flex flex-col items-center gap-1">';
                logs.forEach(l => {
                    let badgeCls = 'bg-emerald-50 text-emerald-700 border-emerald-300';
                    let iconCls  = 'fa-check text-emerald-600';
                    if (l.status === 'Telat') {
                        badgeCls = 'bg-amber-50 text-amber-700 border-amber-300';
                        iconCls  = 'fa-clock text-amber-600';
                    } else if (l.status === 'Sakit') {
                        badgeCls = 'bg-sky-50 text-sky-700 border-sky-300';
                        iconCls  = 'fa-stethoscope text-sky-600';
                    } else if (l.status === 'Absen') {
                        badgeCls = 'bg-rose-50 text-rose-700 border-rose-300';
                        iconCls  = 'fa-xmark text-rose-600';
                    }

                    html += `
                        <div class="flex items-center gap-1.5" title="${l.nama_sesi || 'Sesi'}: ${l.keterangan || l.status}">
                            <span class="px-2 py-0.5 rounded text-2xs font-semibold border ${badgeCls} inline-flex items-center gap-1 shadow-2xs">
                                <i class="fa-solid ${iconCls} text-[10px]"></i> ${l.status}
                            </span>
                            <span class="text-3xs text-slate-400 font-medium truncate max-w-[110px]">
                                ${l.waktu ? l.waktu.split('-')[0].trim() : ''}
                            </span>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        });
    }
}

// State & Data Sesi Pertemuan (Client-Side Reactive Controller)
const allSesiData = <?= json_encode($sesiPreparedList ?? []); ?>;
let currentSesiView   = 'cards';
let currentSesiFilter = 'all';
let currentSesiSearch = '';
let currentSesiPage   = 1;
const SESI_PER_PAGE   = 6;

// Ganti Mode Tampilan Sesi: Kartu vs Tabel
function switchSesiView(viewMode) {
    currentSesiView = viewMode;
    const cardsEl = document.getElementById('sesiCardsGrid');
    const tableEl = document.getElementById('sesiTableView');
    const btnCards = document.getElementById('btnViewCards');
    const btnTable = document.getElementById('btnViewTable');

    if (viewMode === 'cards') {
        if (cardsEl) cardsEl.classList.remove('hidden');
        if (tableEl) tableEl.classList.add('hidden');
        if (btnCards) btnCards.className = 'px-2.5 py-1 rounded-md bg-white text-brand shadow-2xs font-semibold flex items-center gap-1.5 cursor-pointer';
        if (btnTable) btnTable.className = 'px-2.5 py-1 rounded-md text-slate-500 hover:text-slate-800 font-medium flex items-center gap-1.5 cursor-pointer';
    } else {
        if (cardsEl) cardsEl.classList.add('hidden');
        if (tableEl) tableEl.classList.remove('hidden');
        if (btnTable) btnTable.className = 'px-2.5 py-1 rounded-md bg-white text-brand shadow-2xs font-semibold flex items-center gap-1.5 cursor-pointer';
        if (btnCards) btnCards.className = 'px-2.5 py-1 rounded-md text-slate-500 hover:text-slate-800 font-medium flex items-center gap-1.5 cursor-pointer';
    }
    applySesiFilterAndRender();
}

// Filter Sesi Berdasarkan Tombol Cepat
function filterSesiByFilter(filterVal, event) {
    document.querySelectorAll('.btn-filter-sesi').forEach(btn => {
        btn.classList.remove('bg-brand', 'text-white', 'shadow-xs', 'font-semibold');
        btn.classList.add('bg-slate-100', 'text-slate-700');
    });

    if (event && event.currentTarget) {
        event.currentTarget.classList.add('bg-brand', 'text-white', 'shadow-xs', 'font-semibold');
        event.currentTarget.classList.remove('bg-slate-100', 'text-slate-700');
    } else {
        const activeBtnMap = {
            'all': 'btnSesiAll',
            'today': 'btnSesiToday',
            'yesterday': 'btnSesiYesterday',
        };
        const activeId = activeBtnMap[filterVal];
        if (activeId) {
            const btn = document.getElementById(activeId);
            if (btn) {
                btn.classList.add('bg-brand', 'text-white', 'shadow-xs', 'font-semibold');
                btn.classList.remove('bg-slate-100', 'text-slate-700');
            }
        }
    }

    currentSesiFilter = filterVal;
    currentSesiPage = 1;
    applySesiFilterAndRender();
}

// Filter Sesi Berdasarkan Custom Datepicker
function filterSesiByCustomDate(val) {
    if (!val) return;
    document.querySelectorAll('.btn-filter-sesi').forEach(btn => {
        btn.classList.remove('bg-brand', 'text-white', 'shadow-xs', 'font-semibold');
        btn.classList.add('bg-slate-100', 'text-slate-700');
    });
    currentSesiFilter = val;
    currentSesiPage = 1;
    applySesiFilterAndRender();
}

// Filter Sesi Berdasarkan Realtime Search Input
function onSearchSesiChange(keyword) {
    currentSesiSearch = keyword.toLowerCase().trim();
    currentSesiPage = 1;
    applySesiFilterAndRender();
}

// Ambil List Sesi yang Memenuhi Kriteria Filter & Pencarian
function getFilteredSesi() {
    return allSesiData.filter(item => {
        // 1. Filter Tanggal
        let dateMatch = false;
        const d = item.tanggal;
        if (currentSesiFilter === 'all') {
            dateMatch = true;
        } else if (currentSesiFilter === 'today') {
            dateMatch = (d === REALTIME_TODAY);
        } else if (currentSesiFilter === 'yesterday') {
            dateMatch = (d === REALTIME_YESTERDAY);
        } else if (currentSesiFilter.startsWith('month:')) {
            const mKey = currentSesiFilter.substring(6);
            dateMatch = d.startsWith(mKey);
        } else if (currentSesiFilter.startsWith('range:')) {
            const parts = currentSesiFilter.substring(6).split(':');
            dateMatch = (d >= parts[0] && d <= parts[1]);
        } else {
            dateMatch = (d === currentSesiFilter);
        }

        if (!dateMatch) return false;

        // 2. Filter Pencarian
        if (currentSesiSearch) {
            const q = currentSesiSearch;
            const inJudul = (item.judul || '').toLowerCase().includes(q);
            const inMateri = (item.materi || '').toLowerCase().includes(q);
            const inPengajar = (item.pengajar || '').toLowerCase().includes(q);
            const inLokasi = (item.lokasi || '').toLowerCase().includes(q);
            const inNama = (item.nama_sesi || '').toLowerCase().includes(q);
            if (!inJudul && !inMateri && !inPengajar && !inLokasi && !inNama) {
                return false;
            }
        }

        return true;
    });
}

// Render Card HTML (Sesuai Persis dengan Image Mockup)
function renderCardHtml(s) {
    const isEmerald = s.persentase >= 90;
    const isAmber = s.persentase >= 75 && s.persentase < 90;
    const pctColor = isEmerald ? 'text-emerald-600' : (isAmber ? 'text-amber-600' : 'text-rose-600');
    const barBg = isEmerald ? 'bg-emerald-500' : (isAmber ? 'bg-amber-500' : 'bg-rose-500');

    // Safe JSON encoded data
    const sJson = JSON.stringify(s).replace(/'/g, "&#39;");

    return `
    <div class="card-sesi bg-white p-4 rounded-xl border border-slate-200 shadow-2xs hover:border-slate-300 hover:shadow-xs transition flex flex-col justify-between">
        <div>
            <!-- Card Header: Tanggal & Waktu -->
            <div class="flex items-center justify-between text-xs text-slate-600 mb-2.5">
                <div class="flex items-center gap-1.5 font-medium text-slate-700">
                    <i class="fa-regular fa-calendar text-slate-400"></i>
                    <span>${s.tanggal_format}</span>
                </div>
                <div class="flex items-center gap-1.5 font-mono text-slate-500 text-2xs font-semibold">
                    <i class="fa-regular fa-clock text-slate-400"></i>
                    <span>${s.waktu}</span>
                </div>
            </div>

            <!-- Card Body: Judul, Materi, Pengajar, Lokasi & Persentase -->
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="space-y-1 min-w-0 flex-1">
                    <h4 class="text-sm font-bold text-slate-800 flex items-center gap-1.5 truncate">
                        <i class="fa-solid fa-circle-info text-slate-400 text-xs shrink-0"></i>
                        <span class="truncate" title="${s.judul}">${s.judul}</span>
                    </h4>
                    <p class="text-xs text-slate-500 truncate" title="${s.materi}">${s.materi}</p>
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 pt-0.5">
                        <i class="fa-regular fa-user text-slate-400 w-3.5 text-center"></i>
                        <span class="truncate font-medium">${s.pengajar}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <i class="fa-solid fa-location-dot text-slate-400 w-3.5 text-center"></i>
                        <span class="truncate">${s.lokasi}</span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-sm font-bold ${pctColor}">
                        ${Number(s.persentase).toFixed(1)}% Hadir
                    </div>
                    <div class="text-xs font-semibold text-slate-700 mt-0.5">
                        ${s.total_hadir}/${s.total_peserta} Santri
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden my-2">
                <div class="h-full rounded-full ${barBg} transition-all duration-300" style="width: ${Math.min(100, Math.max(0, s.persentase))}%"></div>
            </div>

            <!-- Breakdown Row -->
            <div class="text-xs text-slate-600 font-medium mb-3 flex items-center justify-between">
                <span>Hadir: <strong class="text-slate-800">${s.total_hadir}</strong></span>
                <span class="text-slate-300">|</span>
                <span>Telat: <strong class="text-slate-800">${s.total_telat}</strong></span>
                <span class="text-slate-300">|</span>
                <span>Sakit: <strong class="text-slate-800">${s.total_sakit}</strong></span>
                <span class="text-slate-300">|</span>
                <span>Absen: <strong class="text-slate-800">${s.total_alpa}</strong></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <button type="button" onclick='openDetailSesiModal(${sJson})' class="flex-1 bg-brand hover:bg-brand-dark text-white text-xs font-semibold py-2 px-3 rounded-lg shadow-2xs hover:shadow-xs transition text-center cursor-pointer">
                Lihat Detail Sesi
            </button>
            <button type="button" onclick='openEditSesiModal(${sJson})' class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition shrink-0 cursor-pointer" title="Edit Sesi">
                <i class="fa-solid fa-pen text-xs"></i>
            </button>
        </div>
    </div>
    `;
}

// Render Table Row HTML (Tampilan Tabel Minimalis)
function renderSesiTableView(items, startIndex) {
    const tbody = document.getElementById('sesiTableBody');
    if (!tbody) return;

    let html = '';
    items.forEach((s, idx) => {
        const isEmerald = s.persentase >= 90;
        const isAmber = s.persentase >= 75 && s.persentase < 90;
        const pctColor = isEmerald ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : (isAmber ? 'text-amber-800 bg-amber-50 border-amber-200' : 'text-rose-700 bg-rose-50 border-rose-200');
        const barBg = isEmerald ? 'bg-emerald-500' : (isAmber ? 'bg-amber-500' : 'bg-rose-500');
        const sJson = JSON.stringify(s).replace(/'/g, "&#39;");

        html += `
        <tr class="hover:bg-slate-50/70 transition">
            <td class="py-3 px-3.5 text-center font-bold text-slate-500 border-r border-slate-100">${startIndex + idx + 1}</td>
            <td class="py-3 px-4 border-r border-slate-100">
                <span class="font-bold text-slate-800 block">${s.tanggal_format}</span>
                <span class="font-mono text-3xs text-slate-500 flex items-center gap-1 mt-0.5">
                    <i class="fa-regular fa-clock text-slate-400"></i> ${s.waktu}
                </span>
            </td>
            <td class="py-3 px-4 border-r border-slate-100">
                <span class="font-bold text-slate-800 block">${s.judul}</span>
                <span class="text-3xs text-slate-500 block truncate max-w-xs">${s.materi}</span>
            </td>
            <td class="py-3 px-4 border-r border-slate-100">
                <span class="font-semibold text-slate-700 block truncate">${s.pengajar}</span>
                <span class="text-3xs text-slate-500 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-location-dot text-slate-400"></i> ${s.lokasi}
                </span>
            </td>
            <td class="py-3 px-4 text-center border-r border-slate-100">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-extrabold border ${pctColor} mb-1">
                    ${Number(s.persentase).toFixed(1)}%
                </span>
                <div class="w-24 mx-auto bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full rounded-full ${barBg}" style="width: ${Math.min(100, Math.max(0, s.persentase))}%"></div>
                </div>
                <span class="text-3xs text-slate-500 block mt-0.5">${s.total_hadir}/${s.total_peserta} Santri</span>
            </td>
            <td class="py-3 px-4 text-center border-r border-slate-100">
                <div class="inline-flex items-center divide-x divide-slate-200 rounded border border-slate-200 bg-white text-3xs font-mono">
                    <span class="px-1.5 py-0.5 text-emerald-700 font-bold" title="Hadir"><span class="text-slate-400 font-normal">H:</span>${s.total_hadir}</span>
                    <span class="px-1.5 py-0.5 text-amber-700 font-semibold" title="Telat"><span class="text-slate-400 font-normal">T:</span>${s.total_telat}</span>
                    <span class="px-1.5 py-0.5 text-sky-700 font-semibold" title="Sakit"><span class="text-slate-400 font-normal">S:</span>${s.total_sakit}</span>
                    <span class="px-1.5 py-0.5 text-rose-700 font-bold" title="Absen"><span class="text-slate-400 font-normal">A:</span>${s.total_alpa}</span>
                </div>
            </td>
            <td class="py-3 px-3 text-center">
                <div class="inline-flex items-center gap-1.5">
                    <button type="button" onclick='openDetailSesiModal(${sJson})' class="w-7 h-7 rounded-lg bg-brand-light text-brand hover:bg-brand hover:text-white transition flex items-center justify-center cursor-pointer shadow-2xs" title="Lihat Detail">
                        <i class="fa-regular fa-eye text-xs"></i>
                    </button>
                    <button type="button" onclick='openEditSesiModal(${sJson})' class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition flex items-center justify-center cursor-pointer" title="Edit Sesi">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    });
    tbody.innerHTML = html;
}

// Controller Render Utama & Paginasi
function applySesiFilterAndRender() {
    const filtered = getFilteredSesi();
    const totalItems = filtered.length;
    const totalPages = Math.ceil(totalItems / SESI_PER_PAGE) || 1;
    if (currentSesiPage > totalPages) currentSesiPage = totalPages;
    if (currentSesiPage < 1) currentSesiPage = 1;

    const startIndex = (currentSesiPage - 1) * SESI_PER_PAGE;
    const endIndex = Math.min(startIndex + SESI_PER_PAGE, totalItems);
    const pageItems = filtered.slice(startIndex, endIndex);

    const countBadge = document.getElementById('sesiCountBadge');
    if (countBadge) countBadge.textContent = `${totalItems} Sesi`;

    const emptyState = document.getElementById('sesiEmptyState');
    const cardsGrid  = document.getElementById('sesiCardsGrid');

    if (totalItems === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (cardsGrid) cardsGrid.innerHTML = '';
        const tbody = document.getElementById('sesiTableBody');
        if (tbody) tbody.innerHTML = '';
    } else {
        if (emptyState) emptyState.classList.add('hidden');
        if (currentSesiView === 'cards') {
            if (cardsGrid) {
                cardsGrid.innerHTML = pageItems.map(renderCardHtml).join('');
            }
        } else {
            renderSesiTableView(pageItems, startIndex);
        }
    }

    renderSesiPagination(totalItems);
}

// Render Tombol Pagination (Sebelumnya, 1, 2, 3..., Berikutnya)
function renderSesiPagination(filteredTotal) {
    const totalPages = Math.ceil(filteredTotal / SESI_PER_PAGE) || 1;
    const container = document.getElementById('sesiPaginationButtons');
    const info = document.getElementById('sesiPaginationInfo');

    const startIndex = (currentSesiPage - 1) * SESI_PER_PAGE;
    const endIndex = Math.min(startIndex + SESI_PER_PAGE, filteredTotal);

    if (info) {
        if (filteredTotal === 0) {
            info.textContent = 'Menampilkan 0 sesi';
        } else {
            info.textContent = `Menampilkan ${startIndex + 1} sampai ${endIndex} dari ${filteredTotal} sesi`;
        }
    }

    if (!container) return;
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    // Tombol Sebelumnya
    const prevDisabled = currentSesiPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-slate-50';
    html += `<button type="button" onclick="goToSesiPage(${currentSesiPage - 1})" ${currentSesiPage === 1 ? 'disabled' : ''} class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 ${prevDisabled} transition">Sebelumnya</button>`;

    // Tombol Angka Halaman
    for (let p = 1; p <= totalPages; p++) {
        if (totalPages > 7) {
            if (p !== 1 && p !== totalPages && Math.abs(p - currentSesiPage) > 1) {
                if (p === 2 || p === totalPages - 1) {
                    html += `<span class="px-1 text-slate-400">...</span>`;
                }
                continue;
            }
        }
        if (p === currentSesiPage) {
            html += `<button type="button" class="w-8 h-8 rounded-lg bg-brand text-white font-bold shadow-2xs">${p}</button>`;
        } else {
            html += `<button type="button" onclick="goToSesiPage(${p})" class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 transition cursor-pointer">${p}</button>`;
        }
    }

    // Tombol Berikutnya
    const nextDisabled = currentSesiPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-slate-50';
    html += `<button type="button" onclick="goToSesiPage(${currentSesiPage + 1})" ${currentSesiPage === totalPages ? 'disabled' : ''} class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 ${nextDisabled} transition">Berikutnya</button>`;

    container.innerHTML = html;
}

function goToSesiPage(page) {
    const filtered = getFilteredSesi();
    const totalPages = Math.ceil(filtered.length / SESI_PER_PAGE) || 1;
    if (page < 1 || page > totalPages) return;
    currentSesiPage = page;
    applySesiFilterAndRender();
}

// Modal Detail Sesi Pertemuan
function openDetailSesiModal(s) {
    document.getElementById('detail_sesi_judul').textContent = s.judul || s.nama_sesi;
    document.getElementById('detail_sesi_sub').textContent = 'Sesi: ' + (s.nama_sesi || '-') + ' • ' + s.tanggal_format;
    document.getElementById('detail_sesi_tanggal').textContent = s.tanggal_format;
    document.getElementById('detail_sesi_waktu').textContent = s.waktu_raw || s.waktu;
    document.getElementById('detail_sesi_pengajar').textContent = s.pengajar || '-';
    document.getElementById('detail_sesi_lokasi').textContent = s.lokasi || '-';
    document.getElementById('detail_sesi_materi').textContent = s.materi || '-';

    document.getElementById('detail_sesi_hadir').textContent = s.total_hadir;
    document.getElementById('detail_sesi_telat').textContent = s.total_telat;
    document.getElementById('detail_sesi_sakit').textContent = s.total_sakit;
    document.getElementById('detail_sesi_alpa').textContent = s.total_alpa;

    const pct = Number(s.persentase).toFixed(1);
    const pctSpan = document.getElementById('detail_sesi_persen');
    pctSpan.textContent = pct + '% Hadir';
    pctSpan.className = s.persentase >= 90 ? 'font-bold text-emerald-600' : (s.persentase >= 75 ? 'font-bold text-amber-600' : 'font-bold text-rose-600');

    const barEl = document.getElementById('detail_sesi_bar');
    barEl.style.width = Math.min(100, Math.max(0, s.persentase)) + '%';
    barEl.className = 'h-full rounded-full transition-all duration-300 ' + (s.persentase >= 90 ? 'bg-emerald-500' : (s.persentase >= 75 ? 'bg-amber-500' : 'bg-rose-500'));

    // Filter santri logs for this sesi
    const listContainer = document.getElementById('detail_sesi_santri_list');
    listContainer.innerHTML = '';

    const matchingLogs = allLogsData.filter(l => (Number(l.sesi_id) === Number(s.id)) || (l.tanggal === s.tanggal && l.nama_sesi === s.nama_sesi));
    document.getElementById('detail_sesi_santri_count').textContent = matchingLogs.length + ' Log Santri';

    if (matchingLogs.length === 0) {
        listContainer.innerHTML = `
            <div class="p-6 text-center text-xs text-slate-400">
                <i class="fa-solid fa-clipboard-user text-slate-300 text-2xl mb-1 block"></i>
                Belum ada log presensi santri individual yang dicatat khusus untuk sesi ini.<br>
                Gunakan toolbar presensi santri di tabel bawah untuk mencatat kehadiran per santri.
            </div>
        `;
    } else {
        matchingLogs.forEach(l => {
            let badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            let iconFa = 'fa-check text-emerald-600';
            if (l.status === 'Telat') {
                badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                iconFa = 'fa-clock text-amber-600';
            } else if (l.status === 'Sakit') {
                badgeBg = 'bg-sky-50 text-sky-700 border-sky-200';
                iconFa = 'fa-stethoscope text-sky-600';
            } else if (l.status === 'Absen') {
                badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                iconFa = 'fa-xmark text-rose-600';
            }

            const item = document.createElement('div');
            item.className = 'p-3 flex items-center justify-between gap-3 hover:bg-slate-50 transition';
            item.innerHTML = `
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid ${iconFa}"></i>
                    </span>
                    <div class="truncate">
                        <h5 class="text-xs font-bold text-slate-800 truncate">${l.santri_nama || l.nama || ('Santri #' + l.santri_id)}</h5>
                        <p class="text-3xs text-slate-400">${l.santri_kelas || l.kelas || ''} ${l.keterangan ? '• ' + l.keterangan : ''}</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded text-2xs font-bold border ${badgeBg} shrink-0">
                    ${l.status}
                </span>
            `;
            listContainer.appendChild(item);
        });
    }

    // Sambungkan tombol Edit dari dalam Modal Detail
    const btnEdit = document.getElementById('btnEditFromDetail');
    if (btnEdit) {
        btnEdit.onclick = function() {
            closeDetailSesiModal();
            openEditSesiModal(s);
        };
    }

    document.getElementById('modalDetailSesi').classList.remove('hidden');
}

function closeDetailSesiModal() {
    document.getElementById('modalDetailSesi').classList.add('hidden');
}

// Modal Edit Sesi Pertemuan
let currentEditSesiObj = null;

function openEditSesiModal(s) {
    currentEditSesiObj = s;
    document.getElementById('edit_sesi_id').value = s.id;
    document.getElementById('edit_sesi_nama').value = s.nama_sesi;
    document.getElementById('edit_sesi_tanggal').value = s.tanggal;
    document.getElementById('edit_sesi_waktu').value = s.waktu_raw || s.waktu;
    document.getElementById('edit_sesi_lokasi').value = s.lokasi;
    document.getElementById('edit_sesi_keterangan').value = s.keterangan || '';
    document.getElementById('edit_sesi_hadir').value = s.total_hadir;
    document.getElementById('edit_sesi_telat').value = s.total_telat;
    document.getElementById('edit_sesi_sakit').value = s.total_sakit;
    document.getElementById('edit_sesi_alpa').value = s.total_alpa;

    document.getElementById('modalEditSesi').classList.remove('hidden');
}

function closeEditSesiModal() {
    document.getElementById('modalEditSesi').classList.add('hidden');
}

// Konfirmasi Hapus Sesi
function confirmDeleteSesi() {
    if (!currentEditSesiObj) return;
    document.getElementById('del_sesi_id').value = currentEditSesiObj.id;
    document.getElementById('del_sesi_nama').textContent = currentEditSesiObj.nama_sesi;

    closeEditSesiModal();
    document.getElementById('modalDeleteSesi').classList.remove('hidden');
}

function closeDeleteSesiModal() {
    document.getElementById('modalDeleteSesi').classList.add('hidden');
}

// Sinkronisasi Sesi & Tanggal pada Modal
function onModalSesiChange() {
    const sel = document.getElementById('modal_sesi_id');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.tanggal) {
        document.getElementById('modal_tanggal').value = opt.dataset.tanggal;
    }
}

function onModalTanggalChange() {
    const tgl = document.getElementById('modal_tanggal').value;
    const sel = document.getElementById('modal_sesi_id');
    // Cari opsi sesi yang sesuai dengan tanggal ini
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].dataset.tanggal === tgl) {
            sel.selectedIndex = i;
            break;
        }
    }
}

// Fungsi Buka Modal Pilih Sesi Kehadiran (saat klik Hadir / Telat / Sakit / Absen)
function openPilihSesiModal(santri, statusTerpilih) {
    document.getElementById('modal_santri_id').value = santri.id;
    document.getElementById('modal_santri_nama').textContent = santri.nama;
    document.getElementById('modal_santri_kelas').textContent = santri.kelas;
    document.getElementById('modal_santri_id_badge').textContent = 'ID: ' + santri.id;
    document.getElementById('modal_santri_kehadiran_badge').textContent = (santri.kehadiran || 95) + '%';

    // Jika sedang dalam filter tanggal tertentu di tabel, sesuaikan modal ke tanggal tersebut
    if (activeTableDate && activeTableDate !== 'all') {
        document.getElementById('modal_tanggal').value = activeTableDate;
        onModalTanggalChange();
    } else {
        // Default ke sesi teratas
        const sel = document.getElementById('modal_sesi_id');
        if (sel && sel.options.length > 0) {
            onModalSesiChange();
        }
    }

    // Pilih radio status sesuai tombol yang diklik
    updateStatusVisuals(statusTerpilih);

    // Buka modal
    document.getElementById('modalPilihSesiKehadiran').classList.remove('hidden');
}

function updateStatusVisuals(status) {
    const radioHadir = document.getElementById('radio-hadir');
    const radioTelat = document.getElementById('radio-telat');
    const radioSakit = document.getElementById('radio-sakit');
    const radioAbsen = document.getElementById('radio-absen');

    const lblHadir = document.getElementById('label-status-hadir');
    const lblTelat = document.getElementById('label-status-telat');
    const lblSakit = document.getElementById('label-status-sakit');
    const lblAbsen = document.getElementById('label-status-absen');

    // Reset styles
    [lblHadir, lblTelat, lblSakit, lblAbsen].forEach(lbl => {
        lbl.className = 'status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 cursor-pointer transition bg-white';
    });

    if (status === 'Hadir') {
        radioHadir.checked = true;
        lblHadir.className = 'status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-emerald-500 bg-emerald-50/80 shadow-xs cursor-pointer transition';
        document.getElementById('modal_keterangan').placeholder = 'Hadir tepat waktu & mengikuti sesi penuh';
    } else if (status === 'Telat') {
        radioTelat.checked = true;
        lblTelat.className = 'status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-amber-500 bg-amber-50/80 shadow-xs cursor-pointer transition';
        document.getElementById('modal_keterangan').placeholder = 'Terlambat masuk sesi (alasan...)';
    } else if (status === 'Sakit') {
        radioSakit.checked = true;
        lblSakit.className = 'status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-sky-500 bg-sky-50/80 shadow-xs cursor-pointer transition';
        document.getElementById('modal_keterangan').placeholder = 'Izin sakit / istirahat di asrama / UKS';
    } else if (status === 'Absen') {
        radioAbsen.checked = true;
        lblAbsen.className = 'status-option-label flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-rose-500 bg-rose-50/80 shadow-xs cursor-pointer transition';
        document.getElementById('modal_keterangan').placeholder = 'Tidak hadir / tanpa keterangan';
    }
}

function closePilihSesiModal() {
    document.getElementById('modalPilihSesiKehadiran').classList.add('hidden');
}

function openAddSesiModal() {
    document.getElementById('modalAddSesi').classList.remove('hidden');
}

function closeAddSesiModal() {
    document.getElementById('modalAddSesi').classList.add('hidden');
}

// Buka Modal Detail Riwayat 1 Bulan Santri
function openRiwayatSantriModal(santri) {
    document.getElementById('hist_nama').textContent = 'Riwayat Presensi: ' + santri.nama;
    document.getElementById('hist_sub').textContent = 'ID: ' + santri.id + ' • Kelas: ' + santri.kelas + ' • Periode: <?= $periodeAwal; ?> - <?= $periodeAkhir; ?>';
    
    const rekap = rekapBulananData[santri.id] || { count_hadir: 30, count_telat: 1, count_sakit: 1, count_absen: 0 };
    document.getElementById('hist_cnt_hadir').textContent = rekap.count_hadir || 0;
    document.getElementById('hist_cnt_telat').textContent = rekap.count_telat || 0;
    document.getElementById('hist_cnt_sakit').textContent = rekap.count_sakit || 0;
    document.getElementById('hist_cnt_absen').textContent = rekap.count_absen || 0;
    document.getElementById('hist_pct_span').textContent = (santri.kehadiran || 95) + '%';

    const listContainer = document.getElementById('hist_log_list');
    listContainer.innerHTML = '';

    const logs = logsBySantri[santri.id] || [];
    if (logs.length === 0) {
        listContainer.innerHTML = '<p class="p-4 text-center text-xs text-slate-400">Belum ada catatan log sesi.</p>';
    } else {
        logs.forEach(l => {
            let badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            let iconFa = 'fa-check text-emerald-600';
            if (l.status === 'Telat') {
                badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                iconFa = 'fa-clock text-amber-600';
            } else if (l.status === 'Sakit') {
                badgeBg = 'bg-sky-50 text-sky-700 border-sky-200';
                iconFa = 'fa-stethoscope text-sky-600';
            } else if (l.status === 'Absen') {
                badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                iconFa = 'fa-xmark text-rose-600';
            }

            const item = document.createElement('div');
            item.className = 'p-3 flex items-start justify-between gap-3 hover:bg-slate-50 transition';
            item.innerHTML = `
                <div class="flex items-start gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i class="fa-solid ${iconFa}"></i>
                    </span>
                    <div>
                        <h5 class="text-xs font-bold text-slate-800">${l.nama_sesi || 'Sesi Pertemuan'}</h5>
                        <p class="text-3xs text-slate-400 mt-0.5">
                            <i class="fa-solid fa-calendar mr-1"></i>${formatDateIndo(l.tanggal)} • <i class="fa-solid fa-clock mr-1"></i>${l.waktu || '-'}
                        </p>
                        ${l.keterangan ? `<p class="text-3xs text-slate-600 mt-0.5 italic">"${l.keterangan}"</p>` : ''}
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-2xs font-extrabold border ${badgeBg} shrink-0">
                    ${l.status}
                </span>
            `;
            listContainer.appendChild(item);
        });
    }

    document.getElementById('modalDetailRiwayatSantri').classList.remove('hidden');
}

function closeRiwayatSantriModal() {
    document.getElementById('modalDetailRiwayatSantri').classList.add('hidden');
}

function filterKehadiranTable() {
    const kSelected = document.getElementById('filterKelasKehadiran').value;
    const qSearch   = document.getElementById('searchKehadiranInput').value.toLowerCase().trim();
    const rows      = document.querySelectorAll('.row-santri');

    rows.forEach(r => {
        const rNama  = r.getAttribute('data-nama');
        const rKelas = r.getAttribute('data-kelas');

        const matchKelas = (kSelected === 'all' || rKelas === kSelected);
        const matchSearch = (!qSearch || rNama.includes(qSearch) || rKelas.toLowerCase().includes(qSearch));

        if (matchKelas && matchSearch) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

// Inisialisasi awal render sesi pertemuan pada saat halaman dimuat
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applySesiFilterAndRender);
} else {
    applySesiFilterAndRender();
}
</script>
</body>
</html>
