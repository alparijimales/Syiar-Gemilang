<?php require_once 'navbar.php'; ?>

<style>
    body {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .text-2xs { font-size: 0.68rem; line-height: 0.9rem; }
    .text-3xs { font-size: 0.60rem; line-height: 0.8rem; }
    .shadow-2xs { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04); }
</style>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F4F7F6]">

    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Pemeringkatan Siswa';
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-20">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => 'Portal Admin', 'url' => base_url('admin')],
                ['label' => 'Pemeringkatan Santri']
            ],
            'badge' => 'Lembaga Pendidikan E-RGI Syiar Gemilang',
            'title' => 'Pemeringkatan & Analisis Prestasi',
            'subtitle' => 'Leaderboard prestasi santri berdasarkan kalkulasi bobot terpadu: 80% nilai akademik (5 mapel termasuk akumulasi kejuruan) dan 20% nilai adab & akhlaq karimah.',
            'stats' => [
                ['icon' => 'fa-solid fa-trophy', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => 'Top 3', 'label' => 'Peringkat Terbaik'],
                ['icon' => 'fa-solid fa-users', 'bg' => 'bg-teal-400/20 text-teal-200', 'value' => $total_siswa, 'label' => 'Santri Dinilai'],
                ['icon' => 'fa-solid fa-chalkboard-user', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => count($top3_per_kelas ?? []) . ' Kelas', 'label' => 'Kelas Kejuruan'],
                ['icon' => 'fa-solid fa-award', 'bg' => 'bg-amber-400/20 text-amber-200', 'value' => $rata_rata, 'label' => 'Rata-rata Nilai'],
            ]
        ];
        require_once __DIR__ . '/components/hero_header.php';
        ?>

        <div class="px-6 lg:px-10 max-w-7xl mx-auto space-y-8 -mt-6 relative z-10">

            <!-- Summary Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-emerald-200">
                    <div>
                        <p class="text-2xs font-bold uppercase tracking-wider text-slate-400">Total Santri</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-800 mt-1"><?= esc($total_siswa); ?></h3>
                        <span class="text-xs text-emerald-600 font-medium mt-0.5 inline-block">Terdata aktif</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg border border-emerald-100">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-teal-100 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-teal-200">
                    <div>
                        <p class="text-2xs font-bold uppercase tracking-wider text-slate-400">Jurusan Kejuruan</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-800 mt-1"><?= count($top3_per_kelas); ?> <span class="text-sm font-medium text-slate-400">Kelas</span></h3>
                        <span class="text-xs text-teal-600 font-medium mt-0.5 inline-block">100% dinilai</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg border border-teal-100">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-sky-100 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-sky-200">
                    <div>
                        <p class="text-2xs font-bold uppercase tracking-wider text-slate-400">Rata-Rata Angkatan</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-800 mt-1"><?= esc($rata_rata); ?></h3>
                        <span class="text-xs text-sky-600 font-medium mt-0.5 inline-block">Skala 100</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg border border-sky-100">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>

                <!-- Highlight Card: Juara 1 Global (Deep Forest Emerald & Warm Gold Accent) -->
                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-teal-900 to-slate-900 p-5 rounded-2xl border border-emerald-500/30 shadow-md text-white">
                    <div class="absolute -right-3 -bottom-3 text-emerald-400/10 text-7xl pointer-events-none">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-3xs font-bold tracking-wider uppercase bg-amber-400/20 text-amber-300 border border-amber-400/30">
                            <i class="fa-solid fa-crown text-[9px]"></i> Juara 1 Umum
                        </span>
                        <?php if (!empty($ranking_all)): ?>
                            <span class="text-xs font-black text-amber-300 bg-black/20 px-2 py-0.5 rounded-md"><?= number_format($ranking_all[0]['rata_rata'], 2); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-2.5 truncate">
                        <?php if (!empty($ranking_all)): ?>
                            <h3 class="text-base font-bold text-white truncate tracking-tight" title="<?= esc($ranking_all[0]['nama']); ?>">
                                <?= esc($ranking_all[0]['nama']); ?>
                            </h3>
                            <p class="text-xs text-emerald-200/80 truncate mt-0.5"><?= esc($ranking_all[0]['kelas']); ?></p>
                        <?php else: ?>
                            <h3 class="text-sm font-semibold text-emerald-300/50">-</h3>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Section: Chart Bar Modern -->
            <div class="bg-white p-6 lg:p-7 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h3 class="text-base font-bold text-slate-800">Distribusi Nilai Top 3 Antar Kelas</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Komparasi nilai rata-rata juara 1, 2, dan 3 di tiap jurusan kejuruan.</p>
                    </div>

                    <!-- Filter buttons -->
                    <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide py-1">
                        <button type="button" onclick="filterChartByClass('all')" id="btn-chart-all" class="chart-filter-btn px-3.5 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-xs transition">
                            Semua
                        </button>
                        <?php foreach (array_keys($top3_per_kelas) as $idx => $kName): ?>
                            <button type="button" onclick="filterChartByClass('<?= esc($kName, 'js'); ?>')" id="btn-chart-<?= $idx; ?>" class="chart-filter-btn px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 text-xs font-medium transition shrink-0">
                                <?= esc($kName); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="relative w-full h-[320px]">
                    <canvas id="rankingBarChart"></canvas>
                </div>
            </div>

            <!-- Section: Kartu Podium Top 3 Tiap Kelas -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-medal text-emerald-600"></i> Podium Prestasi Per Jurusan
                    </h3>
                    <p class="text-xs text-slate-500">Tiga santri peraih nilai tertinggi dengan rincian capaian akademik & bobot adab.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="classCardsGrid">
                    <?php
                    $classMeta = [
                        'Desain Grafis'                => ['icon' => 'fa-palette', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'iconBg' => 'text-rose-600 bg-rose-50'],
                        'Teknik Komputer dan Jaringan' => ['icon' => 'fa-network-wired', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'iconBg' => 'text-emerald-600 bg-emerald-50'],
                        'Aplikasi Perkantoran'         => ['icon' => 'fa-file-excel', 'badge' => 'bg-teal-50 text-teal-700 border-teal-200', 'iconBg' => 'text-teal-600 bg-teal-50'],
                        'Tata Busana'                  => ['icon' => 'fa-scissors', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'iconBg' => 'text-amber-600 bg-amber-50'],
                        'Otomotif'                     => ['icon' => 'fa-wrench', 'badge' => 'bg-slate-100 text-slate-700 border-slate-300', 'iconBg' => 'text-slate-700 bg-slate-100'],
                        'Fotografi dan Videografi'     => ['icon' => 'fa-camera', 'badge' => 'bg-sky-50 text-sky-700 border-sky-200', 'iconBg' => 'text-sky-600 bg-sky-50'],
                    ];
                    ?>

                    <?php foreach ($top3_per_kelas as $kelasName => $students): ?>
                        <?php 
                            $meta = $classMeta[$kelasName] ?? ['icon' => 'fa-graduation-cap', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'iconBg' => 'text-emerald-600 bg-emerald-50']; 
                        ?>
                        
                        <div class="class-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col transition hover:border-emerald-300 hover:shadow-sm" data-class="<?= esc($kelasName); ?>">
                            
                            <!-- Header Jurusan -->
                            <div class="p-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg <?= $meta['iconBg']; ?> border border-slate-200/60 shadow-2xs flex items-center justify-center text-xs shrink-0">
                                        <i class="fa-solid <?= $meta['icon']; ?>"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800 truncate" title="<?= esc($kelasName); ?>">
                                        <?= esc($kelasName); ?>
                                    </h4>
                                </div>
                                <span class="text-3xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border border-slate-200 bg-white text-slate-700 shadow-2xs">
                                    <?= count($students); ?> Terbaik
                                </span>
                            </div>

                            <!-- List Santri -->
                            <div class="p-3.5 space-y-2.5 flex-1 flex flex-col justify-between">
                                <?php foreach ($students as $i => $s): ?>
                                    <?php
                                        $rank = $s['rank'] ?? ($i + 1);
                                        if ($rank === 1) {
                                            $cardStyle = 'bg-gradient-to-r from-emerald-50/80 via-emerald-50/30 to-transparent border-emerald-200';
                                            $rankBadge = 'bg-emerald-600 text-white shadow-2xs';
                                            $scoreBadge = 'text-emerald-800 font-extrabold';
                                        } elseif ($rank === 2) {
                                            $cardStyle = 'bg-gradient-to-r from-teal-50/60 via-teal-50/20 to-transparent border-teal-200';
                                            $rankBadge = 'bg-teal-600 text-white shadow-2xs';
                                            $scoreBadge = 'text-teal-800 font-bold';
                                        } else {
                                            $cardStyle = 'bg-gradient-to-r from-amber-50/60 via-amber-50/20 to-transparent border-amber-200';
                                            $rankBadge = 'bg-amber-600 text-white shadow-2xs';
                                            $scoreBadge = 'text-amber-800 font-bold';
                                        }
                                    ?>
                                    <div class="p-3 rounded-xl border <?= $cardStyle; ?> transition">
                                        <div class="flex items-center justify-between gap-3 mb-2">
                                            <div class="flex items-center gap-2.5 truncate">
                                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-3xs font-black shrink-0 <?= $rankBadge; ?>">
                                                    <?= $rank; ?>
                                                </span>
                                                <span class="text-xs font-bold text-slate-800 truncate" title="<?= esc($s['nama']); ?>">
                                                    <?= esc($s['nama']); ?>
                                                </span>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="text-sm <?= $scoreBadge; ?>">
                                                    <?= number_format($s['rata_rata'], 2); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Micro Scores Breakdown (5 Mapel + Adab) -->
                                        <div class="grid grid-cols-6 gap-1 text-center pt-2 border-t border-slate-200/50">
                                            <div class="bg-white/90 py-1 rounded border border-slate-100">
                                                <span class="text-[9px] uppercase font-semibold text-slate-400 block">THD</span>
                                                <span class="text-xs font-bold text-slate-700"><?= esc($s['tauhid']); ?></span>
                                            </div>
                                            <div class="bg-white/90 py-1 rounded border border-slate-100">
                                                <span class="text-[9px] uppercase font-semibold text-slate-400 block">FQH</span>
                                                <span class="text-xs font-bold text-slate-700"><?= esc($s['fiqih']); ?></span>
                                            </div>
                                            <div class="bg-white/90 py-1 rounded border border-slate-100">
                                                <span class="text-[9px] uppercase font-semibold text-slate-400 block">ING</span>
                                                <span class="text-xs font-bold text-slate-700"><?= esc($s['b_ing']); ?></span>
                                            </div>
                                            <div class="bg-white/90 py-1 rounded border border-slate-100">
                                                <span class="text-[9px] uppercase font-semibold text-slate-400 block">KRT</span>
                                                <span class="text-xs font-bold text-slate-700"><?= esc($s['menulis_kreatif']); ?></span>
                                            </div>
                                            <div class="bg-blue-50/90 py-1 rounded border border-blue-200" title="<?= esc(($s['kejuruan_1_alias'] ?? 'Mpl 1') . ': ' . ($s['kejuruan_1_nilai'] ?? 0) . ' | ' . ($s['kejuruan_2_alias'] ?? 'Mpl 2') . ': ' . ($s['kejuruan_2_nilai'] ?? 0)); ?>">
                                                <span class="text-[9px] uppercase font-bold text-blue-800 block">KJR</span>
                                                <span class="text-xs font-black text-blue-900"><?= number_format($s['nilai_kejuruan'] ?? 0, 1); ?></span>
                                            </div>
                                            <div class="bg-emerald-100/80 py-1 rounded border border-emerald-200">
                                                <span class="text-[9px] uppercase font-bold text-emerald-800 block">ADB</span>
                                                <span class="text-xs font-black text-emerald-950"><?= esc($s['adab_akhlaq'] ?? 85); ?></span>
                                            </div>
                                        </div>

                                        <!-- Rincian 2 Mapel Kejuruan Spesifik -->
                                        <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500 bg-slate-50/80 px-2 py-1 rounded-lg">
                                            <span class="truncate mr-1" title="<?= esc($s['kejuruan_1_nama'] ?? ''); ?>">
                                                <i class="fa-solid fa-layer-group text-blue-600 mr-1"></i><?= esc($s['kejuruan_1_alias'] ?? 'Mpl 1'); ?>: <strong class="text-slate-800"><?= number_format($s['kejuruan_1_nilai'] ?? 0, 1); ?></strong>
                                            </span>
                                            <span class="truncate" title="<?= esc($s['kejuruan_2_nama'] ?? ''); ?>">
                                                <i class="fa-solid fa-layer-group text-indigo-600 mr-1"></i><?= esc($s['kejuruan_2_alias'] ?? 'Mpl 2'); ?>: <strong class="text-slate-800"><?= number_format($s['kejuruan_2_nilai'] ?? 0, 1); ?></strong>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Section: Tabel Pemeringkatan Lengkap -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 lg:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-table-list text-emerald-600"></i> Rekapitulasi Nilai Santri
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar peringkat komprehensif beserta proporsi adab (20%) dan kehadiran.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Tombol Export CSV & Cetak Ranking Resmi -->
                        <a href="<?= base_url('admin/export-ranking-csv'); ?>" 
                           title="Unduh seluruh data ranking ke file Excel / CSV"
                           class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-file-excel"></i> Export CSV
                        </a>

                        <a href="<?= base_url('admin/cetak-ranking'); ?>" target="_blank"
                           title="Cetak daftar pemeringkatan santri resmi A4 per jurusan"
                           class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-print"></i> Cetak Ranking
                        </a>

                        <!-- Toggle Buttons -->
                        <div class="inline-flex bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                            <button type="button" onclick="setTableView('top3')" id="btnTabTop3" class="px-3.5 py-1.5 rounded-lg bg-white text-emerald-800 shadow-2xs transition">
                                Top 3 (<?= count($top3_per_kelas) * 3; ?>)
                            </button>
                            <button type="button" onclick="setTableView('all')" id="btnTabAll" class="px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-emerald-700 transition">
                                Semua (<?= count($ranking_all); ?>)
                            </button>
                        </div>

                        <!-- Search -->
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
                            <input type="text" id="tableSearchInput" onkeyup="searchRankingTable()" placeholder="Cari santri atau kelas..." class="pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 w-44 sm:w-60 transition">
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap" id="rankingTable">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/70">
                                <th class="py-3.5 px-4 text-center w-14">Rank</th>
                                <th class="py-3.5 px-4">Nama Santri</th>
                                <th class="py-3.5 px-3">Kelas</th>
                                <th class="py-3.5 px-2 text-center">Tauhid</th>
                                <th class="py-3.5 px-2 text-center">Fiqih</th>
                                <th class="py-3.5 px-2 text-center">B. Ing</th>
                                <th class="py-3.5 px-2 text-center">Kreatif</th>
                                <th class="py-3.5 px-3 text-center bg-blue-50/70 text-blue-900 border-x border-blue-100/70">Kejuruan (Akumulasi)</th>
                                <th class="py-3.5 px-2 text-center bg-emerald-50/60 text-emerald-900">Adab 20%</th>
                                <th class="py-3.5 px-3 text-center">Kehadiran</th>
                                <th class="py-3.5 px-5 text-right font-bold text-slate-700">Nilai Akhir</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100" id="rankingTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Chart.js & Table Dynamic Script -->
<script>
const top3PerKelasData = <?= json_encode($top3_per_kelas); ?>;
const allRankingData   = <?= json_encode($ranking_all); ?>;

const classLabels = Object.keys(top3PerKelasData);
const juara1Scores = [];
const juara2Scores = [];
const juara3Scores = [];
const tooltipMeta = { juara1: [], juara2: [], juara3: [] };

classLabels.forEach(k => {
    const list = top3PerKelasData[k] || [];
    
    if (list[0]) { juara1Scores.push(parseFloat(list[0].rata_rata)); tooltipMeta.juara1.push(list[0]); } 
    else { juara1Scores.push(null); tooltipMeta.juara1.push(null); }

    if (list[1]) { juara2Scores.push(parseFloat(list[1].rata_rata)); tooltipMeta.juara2.push(list[1]); } 
    else { juara2Scores.push(null); tooltipMeta.juara2.push(null); }

    if (list[2]) { juara3Scores.push(parseFloat(list[2].rata_rata)); tooltipMeta.juara3.push(list[2]); } 
    else { juara3Scores.push(null); tooltipMeta.juara3.push(null); }
});

const ctx = document.getElementById('rankingBarChart').getContext('2d');
let currentFilterClass = 'all';

// Skema Warna Bar Chart: Emerald 600, Teal 500, Amber 500
const chartConfig = {
    type: 'bar',
    data: {
        labels: classLabels,
        datasets: [
            {
                label: 'Juara 1',
                data: juara1Scores,
                backgroundColor: '#059669', // Emerald 600
                borderRadius: 5,
                borderSkipped: false,
                barPercentage: 0.75,
                categoryPercentage: 0.75
            },
            {
                label: 'Juara 2',
                data: juara2Scores,
                backgroundColor: '#0D9488', // Teal 600
                borderRadius: 5,
                borderSkipped: false,
                barPercentage: 0.75,
                categoryPercentage: 0.75
            },
            {
                label: 'Juara 3',
                data: juara3Scores,
                backgroundColor: '#F59E0B', // Amber 500
                borderRadius: 5,
                borderSkipped: false,
                barPercentage: 0.75,
                categoryPercentage: 0.75
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    font: { family: "'Inter', sans-serif", size: 11, weight: '600' },
                    color: '#475569',
                    padding: 18
                }
            },
            tooltip: {
                backgroundColor: '#064E3B', // Emerald 950
                titleColor: '#A7F3D0',
                bodyColor: '#F0FDF4',
                titleFont: { family: "'Inter', sans-serif", size: 12, weight: '700' },
                bodyFont: { family: "'Inter', sans-serif", size: 11 },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    title: (items) => 'Kelas: ' + items[0].label,
                    label: function(context) {
                        const dsIndex = context.datasetIndex;
                        const item = dsIndex === 0 ? tooltipMeta.juara1[context.dataIndex] 
                                   : dsIndex === 1 ? tooltipMeta.juara2[context.dataIndex] 
                                   : tooltipMeta.juara3[context.dataIndex];
                        if (item) {
                            return ` ${context.dataset.label}: ${item.nama} (${parseFloat(item.rata_rata).toFixed(2)})`;
                        }
                        return ` ${context.dataset.label}: -`;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    font: { family: "'Inter', sans-serif", size: 11, weight: '500' },
                    color: '#64748B'
                },
                border: { display: false }
            },
            y: {
                min: 60, max: 100,
                ticks: {
                    stepSize: 10,
                    font: { family: "'Inter', sans-serif", size: 11 },
                    color: '#94A3B8'
                },
                grid: { color: '#F1F5F9' },
                border: { display: false }
            }
        }
    }
};

const rankingChart = new Chart(ctx, chartConfig);

function filterChartByClass(className) {
    currentFilterClass = className;
    
    document.querySelectorAll('.chart-filter-btn').forEach(btn => {
        btn.className = 'chart-filter-btn px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 text-xs font-medium transition shrink-0';
    });

    if (className === 'all') {
        const btnAll = document.getElementById('btn-chart-all');
        btnAll.className = 'chart-filter-btn px-3.5 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-xs transition';
        
        rankingChart.data.labels = classLabels;
        rankingChart.data.datasets[0].data = juara1Scores;
        rankingChart.data.datasets[1].data = juara2Scores;
        rankingChart.data.datasets[2].data = juara3Scores;
    } else {
        const idx = classLabels.indexOf(className);
        if (idx !== -1) {
            const activeBtn = document.getElementById(`btn-chart-${idx}`);
            if (activeBtn) activeBtn.className = 'chart-filter-btn px-3.5 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-xs transition';
            
            rankingChart.data.labels = [className];
            rankingChart.data.datasets[0].data = [juara1Scores[idx]];
            rankingChart.data.datasets[1].data = [juara2Scores[idx]];
            rankingChart.data.datasets[2].data = [juara3Scores[idx]];
        }
    }
    rankingChart.update();

    const cards = document.querySelectorAll('.class-card');
    cards.forEach(card => {
        card.style.display = (className === 'all' || card.getAttribute('data-class') === className) ? '' : 'none';
    });
}

const flatTop3List = [];
classLabels.forEach(k => {
    (top3PerKelasData[k] || []).forEach((s, idx) => {
        flatTop3List.push({ ...s, rankKelas: idx + 1 });
    });
});

let currentTableView = 'top3';

function renderTable(dataList, isTop3View = true) {
    const tbody = document.getElementById('rankingTableBody');
    tbody.innerHTML = '';

    if (!dataList || dataList.length === 0) {
        tbody.innerHTML = `<tr><td colspan="12" class="py-10 text-center text-slate-400 font-medium">Tidak ada data ditemukan</td></tr>`;
        return;
    }

    dataList.forEach((s, idx) => {
        const globalRank = idx + 1;
        const scoreNum = parseFloat(s.rata_rata);
        let rankBadge = '';

        if (isTop3View) {
            const rk = s.rankKelas || s.rank || 1;
            if (rk === 1) rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white font-black text-3xs shadow-2xs">1</span>`;
            else if (rk === 2) rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white font-black text-3xs shadow-2xs">2</span>`;
            else rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-600 text-white font-black text-3xs shadow-2xs">3</span>`;
        } else {
            if (globalRank === 1) rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white font-black text-3xs shadow-2xs">1</span>`;
            else if (globalRank === 2) rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white font-black text-3xs shadow-2xs">2</span>`;
            else if (globalRank === 3) rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-600 text-white font-black text-3xs shadow-2xs">3</span>`;
            else rankBadge = `<span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-slate-100 text-slate-500 font-semibold text-3xs">${globalRank}</span>`;
        }

        let statusBadge = '';
        if (scoreNum >= 90) {
            statusBadge = `<span class="px-2 py-0.5 rounded-md text-3xs font-bold bg-white text-emerald-700 border border-slate-200 shadow-2xs">Istimewa</span>`;
        } else if (scoreNum >= 80) {
            statusBadge = `<span class="px-2 py-0.5 rounded-md text-3xs font-bold bg-white text-teal-700 border border-slate-200 shadow-2xs">Sangat Baik</span>`;
        } else {
            statusBadge = `<span class="px-2 py-0.5 rounded-md text-3xs font-semibold bg-white text-slate-600 border border-slate-200 shadow-2xs">Baik</span>`;
        }

        const kjrVal = parseFloat(s.nilai_kejuruan || 0);
        const k1Alias = s.kejuruan_1_alias || 'Mapel 1';
        const k1Nilai = parseFloat(s.kejuruan_1_nilai || 0).toFixed(1);
        const k2Alias = s.kejuruan_2_alias || 'Mapel 2';
        const k2Nilai = parseFloat(s.kejuruan_2_nilai || 0).toFixed(1);

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-emerald-50/30 transition-colors';
        tr.innerHTML = `
            <td class="py-3 px-4 text-center">${rankBadge}</td>
            <td class="py-3 px-4">
                <span class="font-bold text-slate-800 block">${s.nama}</span>
                <span class="text-3xs text-slate-400 font-mono">ID: ${s.id || '-'}</span>
            </td>
            <td class="py-3 px-3 text-slate-600 font-medium">${s.kelas}</td>
            <td class="py-3 px-2 text-center font-medium text-slate-700">${s.tauhid}</td>
            <td class="py-3 px-2 text-center font-medium text-slate-700">${s.fiqih}</td>
            <td class="py-3 px-2 text-center font-medium text-slate-700">${s.b_ing}</td>
            <td class="py-3 px-2 text-center font-medium text-slate-700">${s.menulis_kreatif}</td>
            <td class="py-2.5 px-3 text-center bg-blue-50/40 border-x border-blue-100/70">
                <span class="font-extrabold text-[#0052A3] block text-xs tabular-nums">${kjrVal.toFixed(2)}</span>
                <span class="text-[9px] text-slate-500 block leading-tight mt-0.5 truncate max-w-[155px] mx-auto" title="${s.kejuruan_1_nama || 'Mapel 1'}: ${k1Nilai} | ${s.kejuruan_2_nama || 'Mapel 2'}: ${k2Nilai}">
                    ${k1Alias}: <strong>${k1Nilai}</strong> &bull; ${k2Alias}: <strong>${k2Nilai}</strong>
                </span>
            </td>
            <td class="py-3 px-2 text-center font-black text-emerald-900 bg-emerald-50/50">${s.adab_akhlaq || 85}</td>
            <td class="py-3 px-3 text-center">
                <span class="text-3xs font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200/70">${s.kehadiran || 95}%</span>
            </td>
            <td class="py-3 px-5 text-right font-black text-emerald-900 text-sm tracking-tight">
                ${scoreNum.toFixed(2)}
            </td>
            <td class="py-3 px-4 text-center">${statusBadge}</td>
        `;
        tbody.appendChild(tr);
    });
}

function setTableView(view) {
    currentTableView = view;
    const btnTop3 = document.getElementById('btnTabTop3');
    const btnAll  = document.getElementById('btnTabAll');
    document.getElementById('tableSearchInput').value = '';

    if (view === 'top3') {
        btnTop3.className = 'px-3.5 py-1.5 rounded-lg bg-white text-emerald-800 shadow-2xs transition';
        btnAll.className  = 'px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-emerald-700 transition';
        renderTable(flatTop3List, true);
    } else {
        btnAll.className  = 'px-3.5 py-1.5 rounded-lg bg-white text-emerald-800 shadow-2xs transition';
        btnTop3.className = 'px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-emerald-700 transition';
        renderTable(allRankingData, false);
    }
}

function searchRankingTable() {
    const q = document.getElementById('tableSearchInput').value.toLowerCase().trim();
    const sourceList = (currentTableView === 'top3') ? flatTop3List : allRankingData;
    
    if (!q) return renderTable(sourceList, currentTableView === 'top3');

    const filtered = sourceList.filter(s => 
        (s.nama && s.nama.toLowerCase().includes(q)) ||
        (s.kelas && s.kelas.toLowerCase().includes(q)) ||
        (s.id && s.id.toString().toLowerCase().includes(q))
    );
    renderTable(filtered, currentTableView === 'top3');
}

// Inisialisasi awal
renderTable(flatTop3List, true);
</script>
</body>
</html>