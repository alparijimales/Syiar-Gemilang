<?php 
require_once 'navbar.php'; 

$jurusanColors = [
    'Desain Grafis'                => ['bg' => 'bg-red-500',     'badge' => 'bg-red-50 text-red-700 border-red-200',         'dot' => 'bg-red-500'],
    'Teknik Komputer dan Jaringan' => ['bg' => 'bg-emerald-600', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
    'Aplikasi Perkantoran'         => ['bg' => 'bg-orange-500',  'badge' => 'bg-orange-50 text-orange-700 border-orange-200', 'dot' => 'bg-orange-500'],
    'Tata Busana'                  => ['bg' => 'bg-yellow-500',  'badge' => 'bg-yellow-50 text-yellow-800 border-yellow-300', 'dot' => 'bg-yellow-500'],
    'Otomotif'                     => ['bg' => 'bg-slate-900',   'badge' => 'bg-slate-100 text-slate-900 border-slate-300',   'dot' => 'bg-slate-900'],
    'Fotografi dan Videografi'     => ['bg' => 'bg-[#0066CC]',   'badge' => 'bg-blue-50 text-blue-700 border-blue-200',       'dot' => 'bg-blue-600'],
];
$myKelasBadge = $jurusanColors[$santri['kelas']]['badge'] ?? 'bg-blue-50 text-blue-700 border-blue-200';
?>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">
        
    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Dashboard Santri';
    ob_start();
    ?>
    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border <?= $myKelasBadge; ?>">
        <i class="fa-solid fa-graduation-cap mr-1.5"></i> <?= esc($santri['kelas']); ?>
    </span>
    <?php
    $topbar_extra_right = ob_get_clean();
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        ob_start();
        ?>
        <a href="<?= base_url('siswa/presensi'); ?>" class="px-3.5 py-2.5 bg-white text-brand-deep hover:bg-brand-light rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-clipboard-user text-brand"></i> Presensi Saya
        </a>
        <?php
        $extra_presensi = ob_get_clean();

        $hero = [
            'breadcrumb' => [
                ['label' => 'Portal Santri', 'url' => base_url('siswa')],
                ['label' => 'Dashboard Mandiri']
            ],
            'badge' => 'Lembaga Pendidikan E-RGI Syiar Gemilang',
            'title' => 'Portal Belajar & Capaian: ' . esc($santri['nama']),
            'subtitle' => 'NIS: ' . esc($santri['nis'] ?? '-') . ' &bull; Jurusan: ' . esc($santri['kelas']) . ' &bull; ' . esc($santri['cabang'] ?? 'Kampus Utama Depok') . '. Pantau rekapitulasi nilai 4 mapel kejuruan, penilaian adab akhlaq, serta catatan kehadiran mandiri.',
            'extra_action' => $extra_presensi,
            'stats' => [
                ['icon' => 'fa-solid fa-graduation-cap', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => esc($santri['kelas']), 'label' => 'Kelas Kejuruan'],
                ['icon' => 'fa-solid fa-trophy', 'bg' => 'bg-amber-400/20 text-amber-200', 'value' => '#' . ($rank ?? '-'), 'label' => 'Peringkat Kelas'],
                ['icon' => 'fa-solid fa-calendar-check', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => esc($santri['kehadiran']) . '%', 'label' => 'Kehadiran Anda'],
            ]
        ];
        require_once __DIR__ . '/components/hero_header.php';
        ?>

        <div class="p-4 sm:p-6 space-y-6">

        <!-- ================= PWA INSTALL BANNER UNTUK SANTRI ================= -->
        <div id="pwaInstallBanner" class="p-4 rounded-2xl bg-gradient-to-r from-[#0066CC] to-[#073B66] text-white shadow-md flex items-center justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-white/15 border border-white/25 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs sm:text-sm font-bold truncate">Pasang Syiar Gemilang di Smartphone</h4>
                    <p class="text-3xs sm:text-2xs text-blue-100 mt-0.5 line-clamp-1">Install langsung ke layar utama HP santri untuk akses cepat presensi &amp; rapor tanpa Play Store.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="triggerPwaInstall()" class="px-3.5 py-1.5 bg-white text-[#0066CC] hover:bg-blue-50 text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-download text-2xs"></i> Pasang
                </button>
                <button type="button" onclick="dismissPwaBanner()" class="text-white/70 hover:text-white p-1 text-xs cursor-pointer" title="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Alert Peringatan Absen Melebihi Batas (>3x) -->
        <?php if (!empty($is_warning_absen)) : ?>
            <div class="rounded-2xl bg-gradient-to-r from-rose-500 via-rose-600 to-red-700 text-white p-5 sm:p-6 shadow-lg shadow-rose-600/20 border border-rose-400/40 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/10 pointer-events-none"></div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center text-2xl shrink-0 border border-white/30 shadow-inner">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/25 text-white text-3xs font-extrabold uppercase tracking-wider mb-1.5 border border-white/30">
                                <i class="fa-solid fa-bell"></i> Peringatan Kedisiplinan Akademik
                            </div>
                            <h3 class="text-base sm:text-lg font-black tracking-tight text-white leading-tight">
                                Perhatian: Anda Telah Tercatat Absen / Alpa Sebanyak <?= (int)$absen_count; ?> Kali!
                            </h3>
                            <p class="text-xs text-white/90 mt-1 max-w-2xl leading-relaxed">
                                Kehadiran Anda telah melampaui batas toleransi ketidakhadiran tanpa keterangan (&gt; 3 kali). Berdasarkan tata tertib Rumah Gemilang Indonesia, santri dengan status ini diwajibkan segera menghadap <strong>Instruktur Kejuruan / Wali Kelas</strong> untuk klarifikasi dan pembinaan sebelum dikenakan sanksi kedisiplinan lanjutan.
                            </p>
                        </div>
                    </div>
                    <a href="<?= base_url('siswa/presensi'); ?>" class="px-4 py-2.5 bg-white hover:bg-slate-100 text-rose-700 rounded-xl font-bold text-xs shadow-md transition duration-150 flex items-center gap-2 shrink-0 cursor-pointer whitespace-nowrap">
                        <i class="fa-solid fa-clipboard-user"></i>
                        <span>Cek Riwayat Presensi</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Grade Summary & Final Score -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

            <!-- Left: Final Score Hero (5 cols) -->
            <div class="md:col-span-5 bg-white rounded-2xl p-6 border border-[#E2E8F0] shadow-xs flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Nilai Akhir Terbobot</span>
                        <span class="text-2xs bg-brand-light text-brand font-extrabold px-2.5 py-0.5 rounded-full border border-brand-border">
                            80% Akad. + 20% Adab
                        </span>
                    </div>
                    <div class="mt-4 text-center py-4 bg-gradient-to-b from-brand-light/60 to-white rounded-xl border border-brand-border">
                        <div class="text-5xl font-black text-brand tracking-tight">
                            <?= number_format($santri['rata_rata'], 2); ?>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-1">Akumulasi Rata-Rata Terbobot</p>
                        <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-2xs font-bold">
                            <i class="fa-solid fa-award text-amber-500"></i> Peringkat Ke-<?= $rank; ?> Keseluruhan
                        </div>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Rata-Rata Akademik (80%)</span>
                        <span class="font-bold text-slate-800"><?= number_format($santri['rata_akademik'], 2); ?></span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Nilai Adab & Akhlaq (20%)</span>
                        <span class="font-bold text-amber-700"><?= esc($santri['adab_akhlaq']); ?></span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Persentase Kehadiran</span>
                        <span class="font-bold text-emerald-700"><?= esc($santri['kehadiran']); ?>%</span>
                    </div>
                </div>
            </div>

            <!-- Right: Subject Scores Cards (7 cols) -->
            <div class="md:col-span-7 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 sg-display">
                    <i class="fa-solid fa-book-bookmark text-brand"></i> Rincian Nilai per Mata Pelajaran & Adab
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <!-- Tauhid -->
                    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
                        <span class="text-3xs font-bold text-slate-400 uppercase">Mata Pelajaran</span>
                        <h4 class="text-xs font-bold text-slate-700 mt-0.5">Tauhid</h4>
                        <div class="text-2xl font-black text-slate-800 mt-2"><?= esc($santri['tauhid']); ?></div>
                        <span class="text-3xs text-brand font-semibold">Bobot Akademik</span>
                    </div>

                    <!-- Fiqih -->
                    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
                        <span class="text-3xs font-bold text-slate-400 uppercase">Mata Pelajaran</span>
                        <h4 class="text-xs font-bold text-slate-700 mt-0.5">Fiqih</h4>
                        <div class="text-2xl font-black text-slate-800 mt-2"><?= esc($santri['fiqih']); ?></div>
                        <span class="text-3xs text-brand font-semibold">Bobot Akademik</span>
                    </div>

                    <!-- B. Inggris -->
                    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
                        <span class="text-3xs font-bold text-slate-400 uppercase">Mata Pelajaran</span>
                        <h4 class="text-xs font-bold text-slate-700 mt-0.5">B. Inggris</h4>
                        <div class="text-2xl font-black text-slate-800 mt-2"><?= esc($santri['b_ing']); ?></div>
                        <span class="text-3xs text-brand font-semibold">Bobot Akademik</span>
                    </div>

                    <!-- Menulis Kreatif -->
                    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
                        <span class="text-3xs font-bold text-slate-400 uppercase">Mata Pelajaran</span>
                        <h4 class="text-xs font-bold text-slate-700 mt-0.5">Menulis Kreatif</h4>
                        <div class="text-2xl font-black text-slate-800 mt-2"><?= esc($santri['menulis_kreatif']); ?></div>
                        <span class="text-3xs text-brand font-semibold">Bobot Akademik</span>
                    </div>

                    <!-- Adab & Akhlaq (Special) -->
                    <div class="col-span-2 bg-gradient-to-r from-amber-50 to-orange-50 p-3.5 rounded-xl border border-amber-200 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-3xs font-bold text-amber-700 uppercase">Karakter & Disiplin</span>
                            <h4 class="text-xs font-extrabold text-amber-900 mt-0.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-heart text-amber-600"></i> Adab & Akhlaq
                            </h4>
                            <span class="text-3xs text-amber-800 font-semibold mt-1 inline-block">
                                Berkontribusi 20% langsung ke nilai akhir
                            </span>
                        </div>
                        <div class="text-3xl font-black text-amber-700">
                            <?= esc($santri['adab_akhlaq']); ?>
                        </div>
                    </div>
                </div>

                <!-- Presensi Stats Badges -->
                <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-700">Rekap Presensi Saya:</span>
                    <div class="flex items-center gap-2 font-bold text-2xs">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Hadir: <?= $my_rekap['count_hadir'] ?? 0; ?></span>
                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800">Telat: <?= $my_rekap['count_telat'] ?? 0; ?></span>
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800">Sakit: <?= $my_rekap['count_sakit'] ?? 0; ?></span>
                        <span class="px-2 py-0.5 rounded bg-red-100 text-red-800">Absen: <?= $my_rekap['count_absen'] ?? 0; ?></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Attendance Logs Table for this Student -->
        <div class="bg-white p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-[#E2E8F0] shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 sg-display">
                    <i class="fa-solid fa-clock-rotate-left text-brand"></i> Riwayat Presensi Kehadiran Saya
                </h3>
                <a href="<?= base_url('siswa/presensi'); ?>" class="text-xs font-semibold text-brand hover:text-brand-dark transition">
                    Isi Presensi Sesi Baru &rarr;
                </a>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200 max-h-80">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-slate-50 border-b border-slate-200 text-2xs font-bold text-slate-600 uppercase">
                        <tr>
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3">Sesi Pembelajaran</th>
                            <th class="py-2.5 px-3">Waktu & Lokasi</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 px-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100">
                        <?php if (empty($my_logs)): ?>
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400 italic">Belum ada catatan presensi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($my_logs as $log): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-3 text-slate-600 font-medium"><?= esc($log['tanggal']); ?></td>
                                <td class="py-2.5 px-3 font-bold text-slate-800"><?= esc($log['nama_sesi']); ?></td>
                                <td class="py-2.5 px-3 text-slate-500 text-2xs"><?= esc($log['waktu']); ?> &bull; <?= esc($log['lokasi'] ?? 'Aula Serbaguna'); ?></td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2.5 py-0.5 rounded text-3xs font-bold 
                                        <?= $log['status'] === 'Hadir' ? 'bg-emerald-100 text-emerald-800' : 
                                           ($log['status'] === 'Telat' ? 'bg-amber-100 text-amber-800' : 
                                           ($log['status'] === 'Sakit' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')); ?>">
                                        <?= esc($log['status']); ?>
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-slate-500 text-2xs"><?= esc($log['keterangan'] ?: '-'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= CATATAN SETORAN HAFALAN (TAHFIDZ & AL-QURAN) ================= -->
        <div class="bg-white p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-[#E2E8F0] shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 sg-display">
                        <i class="fa-solid fa-book-quran text-teal-600"></i> Catatan Setoran Hafalan &amp; Tasmik Anda
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Target hafalan Juz 30 dan surat pilihan, status kelulusan tasmik, kelancaran (itqan), dan tajwid.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-2xs font-bold bg-teal-50 text-teal-800 border border-teal-200">
                    <?= count($santri_hafalan ?? []); ?> Catatan Setoran
                </span>
            </div>

            <?php if (empty($santri_hafalan)): ?>
                <div class="py-8 text-center text-slate-400 italic">
                    <i class="fa-solid fa-book-open-reader text-2xl mb-2 block text-slate-300"></i>
                    <span>Belum ada catatan setoran hafalan tercatat untuk Anda.</span>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($santri_hafalan as $hf): ?>
                    <?php
                        $tasmikBadge = [
                            'Lulus Tasmik'    => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'Sedang Proses'   => 'bg-blue-50 text-blue-800 border-blue-200',
                            'Perlu Mengulang' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'Belum Tasmik'    => 'bg-slate-100 text-slate-700 border-slate-200',
                        ][$hf['status_tasmik']] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    ?>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-3xs font-extrabold uppercase tracking-wider text-teal-700 block">
                                    <?= esc($hf['target_hafalan']); ?>
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 mt-0.5"><?= esc($hf['surat_ayat']); ?></h4>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black text-slate-800"><?= esc($hf['nilai']); ?></span>
                                <span class="block text-3xs text-slate-400">Nilai</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            <span class="px-2 py-0.5 rounded-full text-3xs font-bold border <?= $tasmikBadge; ?>">
                                <i class="fa-solid fa-check-circle text-2xs mr-1"></i><?= esc($hf['status_tasmik']); ?>
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-3xs font-semibold bg-white text-slate-700 border border-slate-200">
                                Itqan: <?= esc($hf['kelancaran']); ?>
                            </span>
                        </div>

                        <?php if (!empty($hf['catatan_tajwid'])): ?>
                        <div class="p-2.5 rounded-lg bg-teal-50/60 border border-teal-100 text-3xs sm:text-2xs text-teal-900 leading-relaxed">
                            <strong class="font-bold flex items-center gap-1 text-teal-950 mb-0.5">
                                <i class="fa-solid fa-pen-nib text-teal-600"></i> Bimbingan Tajwid dari Pendamping Agama:
                            </strong>
                            <?= esc($hf['catatan_tajwid']); ?>
                        </div>
                        <?php endif; ?>

                        <div class="pt-1 flex items-center justify-between text-3xs text-slate-400 border-t border-slate-200/60">
                            <span>Pendamping: <strong class="text-slate-600"><?= esc($hf['nama_pendamping'] ?? 'Ustadz Pendamping'); ?></strong></span>
                            <span><?= date('d M Y', strtotime($hf['tanggal_setoran'])); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ================= KOMPETENSI KEJURUAN DINAMIS ================= -->
        <div class="bg-white p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-[#E2E8F0] shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 sg-display">
                        <i class="fa-solid fa-laptop-code text-brand"></i> Capaian Kompetensi Kejuruan Terbobot
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Penilaian dinamis per mapel: Tugas, Ujian Teori, dan Ujian Praktik (Project-Based).</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-2xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                    <?= esc($santri['kelas']); ?>
                </span>
            </div>

            <?php if (empty($santri_kompetensi)): ?>
                <div class="py-6 text-center text-slate-400 italic">
                    Belum ada rincian kompetensi kejuruan yang diinput oleh instruktur.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-2xs font-bold text-slate-600 uppercase">
                            <tr>
                                <th class="py-2.5 px-3">Kode &amp; Mata Pelajaran</th>
                                <th class="py-2.5 px-3 text-center">Tugas (<?= esc($santri_kompetensi[0]['bobot_tugas'] ?? 30); ?>%)</th>
                                <th class="py-2.5 px-3 text-center">Teori (<?= esc($santri_kompetensi[0]['bobot_teori'] ?? 30); ?>%)</th>
                                <th class="py-2.5 px-3 text-center">Praktik Projek (<?= esc($santri_kompetensi[0]['bobot_praktik'] ?? 40); ?>%)</th>
                                <th class="py-2.5 px-3 text-center font-bold text-[#0066CC]">Nilai Akhir</th>
                                <th class="py-2.5 px-3">Catatan Instruktur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($santri_kompetensi as $kp): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-3">
                                    <span class="text-3xs font-mono font-bold text-brand uppercase"><?= esc($kp['kode_mapel']); ?></span>
                                    <h5 class="text-xs font-bold text-slate-800"><?= esc($kp['nama_mapel']); ?></h5>
                                    <span class="text-3xs text-slate-400">Instruktur: <?= esc($kp['nama_instruktur'] ?? 'Pengampu Kejuruan'); ?></span>
                                </td>
                                <td class="py-3 px-3 text-center font-semibold text-slate-700"><?= esc($kp['nilai_tugas']); ?></td>
                                <td class="py-3 px-3 text-center font-semibold text-slate-700"><?= esc($kp['nilai_teori']); ?></td>
                                <td class="py-3 px-3 text-center font-bold text-emerald-700"><?= esc($kp['nilai_praktik']); ?></td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-blue-50 text-[#0066CC] border border-blue-200">
                                        <?= number_format($kp['nilai_akhir'], 1); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-2xs text-slate-600 max-w-xs leading-relaxed">
                                    <?= esc($kp['catatan'] ?: 'Sesuai capaian target kompetensi kejuruan.'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        </div> <!-- /p-4 wrapper -->
    </main>
</div>
</body>
</html>

