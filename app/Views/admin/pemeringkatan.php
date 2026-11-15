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

    /* =========================================================
       ANIMASI TAB KAPSUL, PODIUM OMBAK & TRANSISI JURUSAN
       ========================================================= */
    .podium-card-bg {
        background-color: #042f4b;
        box-shadow: 0 22px 45px -12px rgba(4, 47, 75, 0.35);
    }

    /* Indikator Kapsul Biru Meluncur di Tab Jurusan */
    #tabSlidingPill {
        position: absolute;
        top: 6px;
        bottom: 6px;
        left: 6px;
        background-color: #0077cc;
        border-radius: 9999px;
        z-index: 1;
        pointer-events: none;
        box-shadow: 0 4px 14px rgba(0, 119, 204, 0.45);
        transition: left 0.38s cubic-bezier(0.22, 1, 0.36, 1),
                    width 0.38s cubic-bezier(0.22, 1, 0.36, 1);
    }

    /* Slot & Pilar Podium */
    .podium-slot {
        cursor: pointer;
        user-select: none;
        transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                    opacity 0.25s ease;
    }
    .podium-slot:hover {
        transform: translateY(-4px);
    }

    /* Animasi Info Santri (Avatar, Nama, Skor) Saat Ganti Jurusan */
    .podium-info-group {
        transition: transform 0.36s cubic-bezier(0.22, 1, 0.36, 1),
                    opacity 0.28s ease;
         display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    /* Animasi Tinggi Pilar Podium Saat Ganti Jurusan */
    .podium-pillar-wrap {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        transform-origin: bottom center;
        transition: transform 0.48s cubic-bezier(0.22, 1, 0.36, 1),
                    opacity 0.3s ease;
    }

    .podium-pillar {
        transition: height 0.52s cubic-bezier(0.22, 1, 0.36, 1),
                    background-color 0.35s ease;
    }

    /* State Saat Sedang Berpindah Jurusan (Turun Sejenak Sebelum Tumbuh Naik) */
    .is-switching-class .podium-info-group {
        opacity: 0;
        transform: translateY(18px) scale(0.92);
    }
    .is-switching-class .podium-pillar-wrap {
        transform: scaleY(0.18);
        opacity: 0.4;
    }

    /* Judul Podium & Kartu Rincian Saat Berpindah Jurusan */
    #podiumHeaderBox,
    #detailContentBox {
        transition: opacity 0.26s ease, transform 0.32s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .panel-switching {
        opacity: 0 !important;
        transform: translateY(8px) !important;
    }

    /* Avatar Lingkaran di Atas Podium */
    .podium-avatar-wrap {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    }

    /* Cincin Luar untuk Santri yang Sedang Dipilih */
    .podium-avatar-wrap.is-selected::after {
        content: '';
        position: absolute;
        inset: -7px;
        border-radius: 9999px;
        border: 2px solid #57a6e6;
        opacity: 0.95;
        animation: ringPulse 2.6s ease-in-out infinite;
    }

    @keyframes ringPulse {
        0%, 100% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(1.04); opacity: 0.55; }
    }

    /* Ombak Berlapis di Dasar Podium */
    .podium-wave-container {
        position: absolute;
        inset-inline: 0;
        bottom: 0;
        height: 155px;
        overflow: hidden;
        pointer-events: none;
        z-index: 20;
    }

    .podium-wave-track {
        position: absolute;
        left: 0;
        width: 200%;
        display: flex;
        transition: transform 0.45s ease;
    }

    .podium-wave-back {
        bottom: 0;
        height: 145px;
        opacity: 0.38;
        animation: podiumWaveDrift 13s linear infinite;
    }

    .podium-wave-mid {
        bottom: -4px;
        height: 130px;
        opacity: 0.45;
        animation: podiumWaveDriftReverse 9.5s linear infinite;
    }

    .podium-wave-front {
        bottom: -8px;
        height: 115px;
        opacity: 0.55;
        animation: podiumWaveDrift 7.5s linear infinite;
    }

    /* Efek Bergolak Sesaat Saat Ganti Jurusan / Klik Santri */
    #podiumMainCard.wave-agitate .podium-wave-back {
        animation-duration: 2.5s;
    }
    #podiumMainCard.wave-agitate .podium-wave-mid {
        animation-duration: 1.8s;
    }
    #podiumMainCard.wave-agitate .podium-wave-front {
        animation-duration: 1.4s;
    }

    @keyframes podiumWaveDrift {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    @keyframes podiumWaveDriftReverse {
        0% { transform: translateX(-50%); }
        100% { transform: translateX(0); }
    }

    /* Progress Bar Rincian Nilai */
    .score-bar-fill {
        transition: width 0.55s cubic-bezier(0.22, 1, 0.36, 1);
    }
</style>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#EEF2F6]">
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

    <!-- Topbar Mobile Trigger (Hanya tampil di layar kecil agar menu sidebar tetap bisa dibuka) -->
    <div class="lg:hidden flex items-center justify-between px-5 py-3.5 bg-white border-b border-slate-200/80 shrink-0">
        <div class="flex items-center gap-2.5">
            <button type="button" onclick="toggleMobileSidebar()" class="w-9 h-9 rounded-xl bg-[#042f4b] text-white flex items-center justify-center shadow-xs cursor-pointer">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>
            <span class="font-bold text-slate-800 text-sm sg-display">Pemeringkatan Santri</span>
        </div>
        <span class="text-2xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">T.A 2026/2027</span>
    </div>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto px-5 sm:px-8 lg:px-10 py-6 lg:py-8">
        <div class="max-w-[1280px] mx-auto space-y-7">

            <!-- ===================== 1. HEADER & TAB KAPSUL JURUSAN ===================== -->
            <div class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                    <div>
                        <h1 class="text-2xl sm:text-[28px] font-extrabold text-[#042f4b] tracking-tight sg-display leading-tight">
                            Pemeringkatan santri
                        </h1>
                        <p class="text-xs sm:text-sm text-[#64829c] font-medium mt-1">
                            Tahun ajaran 2026/2027, semester ganjil
                        </p>
                    </div>

                    <!-- Tombol Pintas ke Rekap Tabel & Export -->
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <a href="<?= base_url('admin/export-ranking-csv'); ?>" 
                           title="Unduh seluruh data ranking ke file Excel / CSV"
                           class="px-3.5 py-2 rounded-full bg-white hover:bg-slate-50 text-[#042f4b] border border-slate-200/90 text-xs font-bold shadow-2xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-file-excel text-[#0077cc]"></i>
                            <span>Export CSV</span>
                        </a>
                        <a href="<?= base_url('admin/cetak-ranking'); ?>" target="_blank"
                           title="Cetak daftar pemeringkatan santri resmi A4 per jurusan"
                           class="px-3.5 py-2 rounded-full bg-[#042f4b] hover:bg-[#063d61] text-white text-xs font-bold shadow-2xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-print text-sky-300"></i>
                            <span>Cetak Ranking</span>
                        </a>
                    </div>
                </div>

                <!-- Bar Kapsul Pilihan Kelas / Jurusan dengan Indikator Meluncur -->
                <?php
                $shortClassNames = [
                    'Desain Grafis'                => 'Desain Grafis',
                    'Tata Busana'                  => 'Tata Busana',
                    'Aplikasi Perkantoran'         => 'Aplikasi Perkantoran',
                    'Teknik Komputer dan Jaringan' => 'Teknik Komputer',
                    'Fotografi dan Videografi'     => 'Fotografi',
                    'Otomotif'                     => 'Otomotif',
                ];
                $availableClasses = array_keys($top3_per_kelas ?? []);
                ?>
                <div class="inline-flex items-center bg-[#042f4b] p-1.5 rounded-full shadow-md max-w-full overflow-x-auto scrollbar-hide relative">
                    <div class="flex items-center gap-1 relative" id="classTabsContainer">
                        <!-- Elemen Kapsul Biru yang Meluncur Antar Tab -->
                        <div id="tabSlidingPill"></div>

                        <?php foreach ($availableClasses as $idx => $kName): ?>
                            <?php 
                                $shortLabel = $shortClassNames[$kName] ?? $kName;
                                $isFirst = ($idx === 0);
                            ?>
                            <button type="button"
                                    onclick="selectClassTab('<?= esc($kName, 'js'); ?>', this)"
                                    data-class-key="<?= esc($kName); ?>"
                                    class="class-tab-pill relative z-10 px-4 sm:px-5 py-4 rounded-full text-xs sm:text-[13px] font-bold transition-colors duration-300 whitespace-nowrap cursor-pointer focus:outline-none <?= $isFirst ? 'text-white' : 'text-[#8ab4d8] hover:text-white'; ?>">
                                <?= esc($shortLabel); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ===================== 2. GRID UTAMA: PODIUM OMBAK (KIRI) & RINCIAN NILAI (KANAN) ===================== -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

                <!-- KIRI: KARTU PODIUM OMBAK (8 Kolom) -->
                <div id="podiumMainCard" class="lg:col-span-8 podium-card-bg rounded-[30px] p-6 sm:p-8 relative overflow-hidden flex flex-col justify-between min-h-[510px] sm:min-h-[540px]">
                    
                    <!-- Judul Kartu Podium -->
                    <div id="podiumHeaderBox" class="relative z-30">
                        <h2 id="podiumClassTitle" class="text-lg sm:text-[21px] font-extrabold text-white tracking-tight sg-display">
                            Podium Desain Grafis
                        </h2>
                        <p class="text-xs sm:text-[13px] text-[#7aa6c9] font-medium mt-0.5">
                            Tiga santri dengan nilai tertinggi
                        </p>
                    </div>

                    <!-- Area Podium 3 Kolom (Juara 2 - Juara 1 - Juara 3) -->
                    <div id="podiumStageGrid" class="relative z-30 grid grid-cols-3 gap-3 sm:gap-5 items-end pt-8 sm:pt-10 px-1 sm:px-4 mt-auto">

                        <!-- ================= SLOT JUARA 2 (KIRI) ================= -->
                        <div id="podiumSlot2" onclick="selectPodiumRank(1)" class="podium-slot flex flex-col items-center">
                            <div class="podium-info-group" style="transition-delay: 40ms;">
                                <!-- Avatar Lingkaran Inisial -->
                                <div id="podiumAvatar2" class="podium-avatar-wrap w-14 h-14 sm:w-[70px] sm:h-[70px] bg-[#0c4a73] border border-[#2d76a8] text-white font-extrabold text-base sm:text-xl shadow-md mb-3">
                                    <span id="podiumInitials2">PM</span>
                                </div>
                                <!-- Nama & Nilai -->
                                <p id="podiumName2" class="text-xs sm:text-[15px] font-bold text-white text-center truncate max-w-full px-1">
                                    -
                                </p>
                                <p id="podiumScore2" class="text-sm sm:text-[18px] font-extrabold text-white mt-0.5 mb-2.5 tabular-nums">
                                    0.00
                                </p>
                            </div>

                            <div class="podium-pillar-wrap" style="transition-delay: 40ms;">
                                <!-- Garis Aksen Atas Pilar -->
                                <div class="w-full h-1 rounded-full bg-[#2e85c7] mb-1.5 opacity-90"></div>
                                <!-- Pilar Podium 2 (Tanpa Gelembung) -->
                                <div id="podiumBar2" class="podium-pillar w-full h-[185px] sm:h-[215px] rounded-t-2xl rounded-b-xl bg-[#095285]/90 relative overflow-hidden flex flex-col items-center pt-4">
                                    <span class="text-3xl sm:text-[42px] font-extrabold text-[#8ab8de] leading-none select-none">2</span>
                                </div>
                            </div>
                        </div>

                        <!-- ================= SLOT JUARA 1 (TENGAH - TERTINGGI) ================= -->
                        <div id="podiumSlot1" onclick="selectPodiumRank(0)" class="podium-slot flex flex-col items-center">
                            <div class="podium-info-group" style="transition-delay: 0ms;">
                                <!-- Mahkota Kecil di Atas Juara 1 -->
                                <div class="mb-2.5 text-[#7cc2f5]">
                                    <svg class="w-6 h-4 sm:w-7 sm:h-5 mx-auto fill-current" viewBox="0 0 28 18">
                                        <path d="M2 15L5 3L11 10L14 1L17 10L23 3L26 15H2Z" />
                                    </svg>
                                </div>

                                <!-- Avatar Lingkaran Inisial Juara 1 -->
                                <div id="podiumAvatar1" class="podium-avatar-wrap is-selected w-16 h-16 sm:w-[82px] sm:h-[82px] bg-[#0077cc] text-white font-extrabold text-lg sm:text-2xl shadow-lg mb-3.5">
                                    <span id="podiumInitials1">ZA</span>
                                </div>

                                <!-- Nama & Nilai -->
                                <p id="podiumName1" class="text-xs sm:text-[15px] font-bold text-white text-center truncate max-w-full px-1">
                                    -
                                </p>
                                <p id="podiumScore1" class="text-base sm:text-[21px] font-extrabold text-[#6ebbf5] mt-0.5 mb-2.5 tabular-nums">
                                    0.00
                                </p>
                            </div>

                            <div class="podium-pillar-wrap" style="transition-delay: 0ms;">
                                <!-- Garis Aksen Atas Pilar -->
                                <div class="w-full h-1 rounded-full bg-[#63b8f7] mb-1.5"></div>
                                <!-- Pilar Podium 1 (Tanpa Gelembung) -->
                                <div id="podiumBar1" class="podium-pillar w-full h-[230px] sm:h-[268px] rounded-t-2xl rounded-b-xl bg-[#0077cc] relative overflow-hidden flex flex-col items-center pt-4 shadow-lg shadow-[#0077cc]/30">
                                    <span class="text-4xl sm:text-[50px] font-extrabold text-white leading-none select-none">1</span>
                                </div>
                            </div>
                        </div>

                        <!-- ================= SLOT JUARA 3 (KANAN) ================= -->
                        <div id="podiumSlot3" onclick="selectPodiumRank(2)" class="podium-slot flex flex-col items-center">
                            <div class="podium-info-group" style="transition-delay: 80ms;">
                                <!-- Avatar Lingkaran Inisial -->
                                <div id="podiumAvatar3" class="podium-avatar-wrap w-14 h-14 sm:w-[70px] sm:h-[70px] bg-[#0a4369] border border-[#266794] text-white font-extrabold text-base sm:text-xl shadow-md mb-3">
                                    <span id="podiumInitials3">AP</span>
                                </div>
                                <!-- Nama & Nilai -->
                                <p id="podiumName3" class="text-xs sm:text-[15px] font-bold text-white text-center truncate max-w-full px-1">
                                    -
                                </p>
                                <p id="podiumScore3" class="text-sm sm:text-[18px] font-extrabold text-white mt-0.5 mb-2.5 tabular-nums">
                                    0.00
                                </p>
                            </div>

                            <div class="podium-pillar-wrap" style="transition-delay: 80ms;">
                                <!-- Garis Aksen Atas Pilar -->
                                <div class="w-full h-1 rounded-full bg-[#2472ad] mb-1.5 opacity-85"></div>
                                <!-- Pilar Podium 3 (Tanpa Gelembung) -->
                                <div id="podiumBar3" class="podium-pillar w-full h-[150px] sm:h-[178px] rounded-t-2xl rounded-b-xl bg-[#084773]/90 relative overflow-hidden flex flex-col items-center pt-4">
                                    <span class="text-3xl sm:text-[42px] font-extrabold text-[#8ab8de] leading-none select-none">3</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Lapis Ombak Bergerak di Dasar Kartu Podium -->
                    <div class="podium-wave-container">
                        <!-- Lapis Ombak 1 (Belakang) -->
                        <div class="podium-wave-track podium-wave-back">
                            <svg class="w-1/2 h-full" viewBox="0 0 800 140" preserveAspectRatio="none">
                                <path d="M0,45 C130,90 270,5 400,45 C530,85 670,10 800,45 L800,140 L0,140 Z" fill="#1b5c87"/>
                            </svg>
                            <svg class="w-1/2 h-full" viewBox="0 0 800 140" preserveAspectRatio="none">
                                <path d="M0,45 C130,90 270,5 400,45 C530,85 670,10 800,45 L800,140 L0,140 Z" fill="#1b5c87"/>
                            </svg>
                        </div>
                        <!-- Lapis Ombak 2 (Tengah) -->
                        <div class="podium-wave-track podium-wave-mid">
                            <svg class="w-1/2 h-full" viewBox="0 0 800 140" preserveAspectRatio="none">
                                <path d="M0,60 C150,15 260,95 400,60 C540,25 650,95 800,60 L800,140 L0,140 Z" fill="#0d4a73"/>
                            </svg>
                            <svg class="w-1/2 h-full" viewBox="0 0 800 140" preserveAspectRatio="none">
                                <path d="M0,60 C150,15 260,95 400,60 C540,25 650,95 800,60 L800,140 L0,140 Z" fill="#0d4a73"/>
                            </svg>
                        </div>
                        <!-- Lapis Ombak 3 (Depan) -->
                        <div class="podium-wave-track podium-wave-front">
                            <svg class="w-1/2 h-full" viewBox="0 0 800 140" preserveAspectRatio="none">
                                <path d="M0,72 C120,40 280,100 400,72 C520,44 680,100 800,72 L800,140 L0,140 Z" fill="#043558"/>
                            </svg>
                            <svg class="w-1/2 h-full" viewBox="0 0 800 140" preserveAspectRatio="none">
                                <path d="M0,72 C120,40 280,100 400,72 C520,44 680,100 800,72 L800,140 L0,140 Z" fill="#043558"/>
                            </svg>
                        </div>
                    </div>

                </div>

                <!-- KANAN: KARTU RINCIAN NILAI INTERAKTIF (4 Kolom) -->
                <div class="lg:col-span-4 bg-white rounded-[30px] p-6 sm:p-8 shadow-sm border border-slate-200/60 flex flex-col justify-between">
                    
                    <div id="detailContentBox">
                        <h3 class="text-base sm:text-[18px] font-extrabold text-[#042f4b] sg-display">
                            Rincian nilai
                        </h3>

                        <!-- Info Santri Terpilih -->
                        <div class="mt-6 pb-6 border-b border-slate-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div id="detailAvatar" class="w-12 h-12 rounded-full bg-[#e3f1fc] text-[#0077cc] font-extrabold text-sm flex items-center justify-center shrink-0">
                                    ZA
                                </div>
                                <div class="min-w-0">
                                    <h4 id="detailName" class="text-[15px] font-bold text-[#042f4b] truncate">
                                        -
                                    </h4>
                                    <p id="detailSubtitle" class="text-xs text-[#64829c] font-medium truncate mt-0.5">
                                        Juara 1
                                    </p>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span id="detailTotalScore" class="text-2xl sm:text-[28px] font-extrabold text-[#0077cc] tracking-tight tabular-nums">
                                    0.00
                                </span>
                            </div>
                        </div>

                        <!-- Daftar 6 Bar Nilai (THD, FQH, ING, KRT, KJR, ADB) -->
                        <div class="mt-6 space-y-5">
                            
                            <!-- 1. THD (Tauhid) -->
                            <div class="grid grid-cols-12 items-center gap-3">
                                <span class="col-span-2 text-xs font-semibold text-[#64829c] uppercase">THD</span>
                                <div class="col-span-8 h-3 rounded-full bg-[#f1f5f9] overflow-hidden">
                                    <div id="barTHD" class="score-bar-fill h-full rounded-full bg-[#cbd5e1]" style="width: 0%;"></div>
                                </div>
                                <span id="valTHD" class="col-span-2 text-right text-xs font-bold text-[#042f4b] tabular-nums">0</span>
                            </div>

                            <!-- 2. FQH (Fiqih) -->
                            <div class="grid grid-cols-12 items-center gap-3">
                                <span class="col-span-2 text-xs font-semibold text-[#64829c] uppercase">FQH</span>
                                <div class="col-span-8 h-3 rounded-full bg-[#f1f5f9] overflow-hidden">
                                    <div id="barFQH" class="score-bar-fill h-full rounded-full bg-[#cbd5e1]" style="width: 0%;"></div>
                                </div>
                                <span id="valFQH" class="col-span-2 text-right text-xs font-bold text-[#042f4b] tabular-nums">0</span>
                            </div>

                            <!-- 3. ING (Bahasa Inggris) -->
                            <div class="grid grid-cols-12 items-center gap-3">
                                <span class="col-span-2 text-xs font-semibold text-[#64829c] uppercase">ING</span>
                                <div class="col-span-8 h-3 rounded-full bg-[#f1f5f9] overflow-hidden">
                                    <div id="barING" class="score-bar-fill h-full rounded-full bg-[#cbd5e1]" style="width: 0%;"></div>
                                </div>
                                <span id="valING" class="col-span-2 text-right text-xs font-bold text-[#042f4b] tabular-nums">0</span>
                            </div>

                            <!-- 4. KRT (Menulis Kreatif) -->
                            <div class="grid grid-cols-12 items-center gap-3">
                                <span class="col-span-2 text-xs font-semibold text-[#64829c] uppercase">KRT</span>
                                <div class="col-span-8 h-3 rounded-full bg-[#f1f5f9] overflow-hidden">
                                    <div id="barKRT" class="score-bar-fill h-full rounded-full bg-[#cbd5e1]" style="width: 0%;"></div>
                                </div>
                                <span id="valKRT" class="col-span-2 text-right text-xs font-bold text-[#042f4b] tabular-nums">0</span>
                            </div>

                            <!-- 5. KJR (Kejuruan Akumulasi - Warna Biru Aktif) -->
                            <div class="grid grid-cols-12 items-center gap-3" id="rowKJR" title="">
                                <span class="col-span-2 text-xs font-semibold text-[#64829c] uppercase">KJR</span>
                                <div class="col-span-8 h-3 rounded-full bg-[#f1f5f9] overflow-hidden">
                                    <div id="barKJR" class="score-bar-fill h-full rounded-full bg-[#0077cc]" style="width: 0%;"></div>
                                </div>
                                <span id="valKJR" class="col-span-2 text-right text-xs font-bold text-[#042f4b] tabular-nums">0</span>
                            </div>

                            <!-- 6. ADB (Adab & Akhlaq - Warna Biru Aktif) -->
                            <div class="grid grid-cols-12 items-center gap-3">
                                <span class="col-span-2 text-xs font-semibold text-[#64829c] uppercase">ADB</span>
                                <div class="col-span-8 h-3 rounded-full bg-[#f1f5f9] overflow-hidden">
                                    <div id="barADB" class="score-bar-fill h-full rounded-full bg-[#0077cc]" style="width: 0%;"></div>
                                </div>
                                <span id="valADB" class="col-span-2 text-right text-xs font-bold text-[#042f4b] tabular-nums">0</span>
                            </div>

                        </div>

                        <!-- Sub-info kecil Mapel Kejuruan 1 & 2 -->
                        <div id="detailKejuruanSub" class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-[#64829c]">
                            <span id="detailKjr1" class="truncate">-</span>
                            <span id="detailKjr2" class="truncate">-</span>
                        </div>
                    </div>

                    <!-- Petunjuk Interaksi di Bawah Kartu -->
                    <p class="text-xs text-[#64829c] font-medium mt-8">
                        Klik santri di podium untuk ganti rincian.
                    </p>
                </div>

            </div>

            <!-- ===================== 3. TABEL REKAPITULASI LENGKAP ===================== -->
            <div class="bg-white rounded-[28px] border border-slate-200/70 shadow-xs overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-extrabold text-[#042f4b] sg-display flex items-center gap-2">
                            <i class="fa-solid fa-table-list text-[#0077cc]"></i>
                            <span>Rekapitulasi Nilai Seluruh Santri</span>
                        </h3>
                        <p class="text-xs text-[#64829c] mt-0.5">
                            Daftar peringkat komprehensif beserta proporsi kejuruan, adab (20%), dan kehadiran.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Toggle Buttons Top 3 vs Semua -->
                        <div class="inline-flex bg-[#eef2f6] p-1 rounded-full text-xs font-bold">
                            <button type="button" onclick="setTableView('top3')" id="btnTabTop3" class="px-4 py-1.5 rounded-full bg-[#0077cc] text-white shadow-2xs transition cursor-pointer">
                                Top 3 (<?= count($top3_per_kelas) * 3; ?>)
                            </button>
                            <button type="button" onclick="setTableView('all')" id="btnTabAll" class="px-4 py-1.5 rounded-full text-[#64829c] hover:text-[#042f4b] transition cursor-pointer">
                                Semua (<?= count($ranking_all); ?>)
                            </button>
                        </div>

                        <!-- Input Pencarian -->
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-2.5 text-xs text-slate-400"></i>
                            <input type="text" id="tableSearchInput" onkeyup="searchRankingTable()" placeholder="Cari santri atau kelas..." class="pl-9 pr-4 py-1.5 text-xs rounded-full border border-slate-200 bg-[#f8fafc] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0077cc]/20 focus:border-[#0077cc] w-48 sm:w-64 transition">
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap" id="rankingTable">
                        <thead>
                            <tr class="bg-[#f8fafc] text-[10px] font-bold text-[#64829c] uppercase tracking-wider border-b border-slate-200/70">
                                <th class="py-3.5 px-4 text-center w-14">Rank</th>
                                <th class="py-3.5 px-4">Nama Santri</th>
                                <th class="py-3.5 px-3">Kelas</th>
                                <th class="py-3.5 px-2 text-center">THD</th>
                                <th class="py-3.5 px-2 text-center">FQH</th>
                                <th class="py-3.5 px-2 text-center">ING</th>
                                <th class="py-3.5 px-2 text-center">KRT</th>
                                <th class="py-3.5 px-3 text-center bg-sky-50/70 text-[#0077cc] border-x border-sky-100/70">KJR (Akumulasi)</th>
                                <th class="py-3.5 px-2 text-center bg-sky-50/40 text-[#042f4b]">ADB 20%</th>
                                <th class="py-3.5 px-3 text-center">Kehadiran</th>
                                <th class="py-3.5 px-5 text-right font-bold text-[#042f4b]">Nilai Akhir</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100" id="rankingTableBody">
                            <!-- Diisi secara dinamis oleh Vanilla JS -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ===================== VANILLA JAVASCRIPT: ANIMASI PINDAH JURUSAN, PODIUM & RINCIAN ===================== -->
<script>
const top3PerKelasData = <?= json_encode($top3_per_kelas); ?>;
const allRankingData   = <?= json_encode($ranking_all); ?>;

const classLabels = Object.keys(top3PerKelasData);
let activeClassKey = classLabels[0] || '';
let activeRankIndex = 0; // 0 = Juara 1, 1 = Juara 2, 2 = Juara 3
let isClassTransitioning = false;

/* Helper: Ambil 2 huruf inisial dari nama lengkap santri */
function getInitials(name) {
    if (!name) return '--';
    const parts = name.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.trim().substring(0, 2).toUpperCase();
}

/* Helper: Format angka nilai (bulat jika .0, 1 desimal jika ada pecahan) */
function formatSubScore(val) {
    const num = parseFloat(val);
    if (isNaN(num)) return '0';
    return Number.isInteger(num) ? num.toString() : num.toFixed(1);
}

/* Helper: Animasi Angka Berhitung Naik (Count-Up Score) */
function animateNumberValue(el, targetVal, decimals = 2, duration = 420) {
    if (!el) return;
    const startVal = parseFloat(el.textContent) || 0;
    const diff = targetVal - startVal;
    const startTime = performance.now();

    function step(now) {
        const progress = Math.min((now - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3); // Cubic ease-out
        const current = startVal + diff * eased;
        el.textContent = current.toFixed(decimals);
        if (progress < 1) {
            requestAnimationFrame(step);
        } else {
            el.textContent = targetVal.toFixed(decimals);
        }
    }
    requestAnimationFrame(step);
}

/* Gerakkan Kapsul Biru (Sliding Pill) pada Tab Jurusan */
function moveTabSlidingPill(targetBtn) {
    const pill = document.getElementById('tabSlidingPill');
    if (!pill || !targetBtn) return;

    pill.style.left = targetBtn.offsetLeft + 'px';
    pill.style.width = targetBtn.offsetWidth + 'px';
}

/* 1. Fungsi Ganti Tab Jurusan / Kelas dengan Animasi Staggered Podium */
function selectClassTab(className, btnElement) {
    if (!top3PerKelasData[className] || className === activeClassKey || isClassTransitioning) return;
    isClassTransitioning = true;
    activeClassKey = className;
    activeRankIndex = 0; // Kembali ke Juara 1 setiap ganti jurusan

    // Update warna teks tab & luncurkan kapsul biru ke tab yang diklik
    const buttons = document.querySelectorAll('.class-tab-pill');
    let activeBtn = btnElement;
    buttons.forEach(btn => {
        const key = btn.getAttribute('data-class-key');
        if (key === className) {
            btn.classList.remove('text-[#8ab4d8]');
            btn.classList.add('text-white');
            if (!activeBtn) activeBtn = btn;
        } else {
            btn.classList.remove('text-white');
            btn.classList.add('text-[#8ab4d8]');
        }
    });
    if (activeBtn) moveTabSlidingPill(activeBtn);

    // Picu efek ombak bergolak
    triggerPodiumWaveAgitate();

    const stageGrid = document.getElementById('podiumStageGrid');
    const headerBox = document.getElementById('podiumHeaderBox');
    const detailBox = document.getElementById('detailContentBox');

    // Fase 1: Turunkan pilar podium & sembunyikan teks sejenak
    if (stageGrid) stageGrid.classList.add('is-switching-class');
    if (headerBox) headerBox.classList.add('panel-switching');
    if (detailBox) detailBox.classList.add('panel-switching');

    // Kecilkan progress bar rincian ke 0% terlebih dahulu agar terlihat mengisi ulang
    ['THD', 'FQH', 'ING', 'KRT', 'KJR', 'ADB'].forEach(code => {
        const bar = document.getElementById('bar' + code);
        if (bar) bar.style.width = '0%';
    });

    // Fase 2: Ganti data & tumbuhkan kembali pilar podium ke atas
    setTimeout(() => {
        renderPodiumForClass(className, true);
        renderScoreDetailPanel(true);

        if (stageGrid) stageGrid.classList.remove('is-switching-class');
        if (headerBox) headerBox.classList.remove('panel-switching');
        if (detailBox) detailBox.classList.remove('panel-switching');

        setTimeout(() => {
            isClassTransitioning = false;
        }, 380);
    }, 230);
}

/* 2. Render Podium 3 Besar (Juara 1, 2, 3) */
function renderPodiumForClass(className, animateScores = false) {
    const list = top3PerKelasData[className] || [];
    const titleEl = document.getElementById('podiumClassTitle');
    if (titleEl) {
        titleEl.textContent = 'Podium ' + className;
    }

    const slots = [
        { idx: 0, num: 1 },
        { idx: 1, num: 2 },
        { idx: 2, num: 3 }
    ];

    slots.forEach(slot => {
        const student  = list[slot.idx] || null;
        const initEl   = document.getElementById('podiumInitials' + slot.num);
        const nameEl   = document.getElementById('podiumName' + slot.num);
        const scoreEl  = document.getElementById('podiumScore' + slot.num);
        const slotWrap = document.getElementById('podiumSlot' + slot.num);

        if (student) {
            initEl.textContent = getInitials(student.nama);
            nameEl.textContent = student.nama;
            const targetScore  = parseFloat(student.rata_rata || 0);
            if (animateScores) {
                animateNumberValue(scoreEl, targetScore, 2, 460);
            } else {
                scoreEl.textContent = targetScore.toFixed(2);
            }
            slotWrap.style.opacity = '1';
            slotWrap.style.pointerEvents = 'auto';
        } else {
            initEl.textContent  = '-';
            nameEl.textContent  = 'Belum ada data';
            scoreEl.textContent = '0.00';
            slotWrap.style.opacity = '0.45';
            slotWrap.style.pointerEvents = 'none';
        }
    });

    updatePodiumSelectionHighlight();
}

/* 3. Klik Santri di Podium untuk Ganti Rincian Kanan */
function selectPodiumRank(rankIdx) {
    const list = top3PerKelasData[activeClassKey] || [];
    if (!list[rankIdx] || rankIdx === activeRankIndex) return;

    activeRankIndex = rankIdx;
    triggerPodiumWaveAgitate();
    updatePodiumSelectionHighlight();

    const detailBox = document.getElementById('detailContentBox');
    if (detailBox) {
        detailBox.classList.add('panel-switching');
        setTimeout(() => {
            renderScoreDetailPanel(true);
            detailBox.classList.remove('panel-switching');
        }, 160);
    } else {
        renderScoreDetailPanel(true);
    }
}

/* Update cincin fokus & warna avatar santri yang sedang dipilih di podium */
function updatePodiumSelectionHighlight() {
    const mapRankToNum = [1, 2, 3];

    mapRankToNum.forEach((slotNum, idx) => {
        const avatar = document.getElementById('podiumAvatar' + slotNum);
        const bar    = document.getElementById('podiumBar' + slotNum);
        const score  = document.getElementById('podiumScore' + slotNum);
        if (!avatar || !bar || !score) return;

        const isSelected = (idx === activeRankIndex);

        if (isSelected) {
            avatar.classList.add('is-selected');
            avatar.style.backgroundColor = '#0077cc';
            avatar.style.borderColor = '#63b8f7';
            score.className = 'text-base sm:text-[20px] font-extrabold text-[#6ebbf5] mt-0.5 mb-2.5 tabular-nums transition-all';
        } else {
            avatar.classList.remove('is-selected');
            avatar.style.backgroundColor = (slotNum === 1) ? '#0b5a8f' : '#0c4a73';
            avatar.style.borderColor = '#2d76a8';
            score.className = 'text-sm sm:text-[18px] font-extrabold text-white mt-0.5 mb-2.5 tabular-nums transition-all';
        }
    });
}

/* 4. Render Panel Rincian Nilai di Sebelah Kanan */
function renderScoreDetailPanel(animate = false) {
    const list = top3PerKelasData[activeClassKey] || [];
    const student = list[activeRankIndex] || null;

    const avatarEl   = document.getElementById('detailAvatar');
    const nameEl     = document.getElementById('detailName');
    const subtitleEl = document.getElementById('detailSubtitle');
    const totalEl    = document.getElementById('detailTotalScore');

    if (!student) {
        avatarEl.textContent   = '--';
        nameEl.textContent     = 'Tidak ada data';
        subtitleEl.textContent = '-';
        totalEl.textContent    = '0.00';
        ['THD', 'FQH', 'ING', 'KRT', 'KJR', 'ADB'].forEach(code => {
            document.getElementById('bar' + code).style.width = '0%';
            document.getElementById('val' + code).textContent = '0';
        });
        return;
    }

    const juaraNumber = student.rank || (activeRankIndex + 1);
    avatarEl.textContent   = getInitials(student.nama);
    nameEl.textContent     = student.nama;
    subtitleEl.textContent = `Juara ${juaraNumber} ${activeClassKey}`;

    const totalScoreNum = parseFloat(student.rata_rata || 0);
    if (animate) {
        animateNumberValue(totalEl, totalScoreNum, 2, 420);
    } else {
        totalEl.textContent = totalScoreNum.toFixed(2);
    }

    const metrics = [
        { code: 'THD', val: parseFloat(student.tauhid || 0) },
        { code: 'FQH', val: parseFloat(student.fiqih || 0) },
        { code: 'ING', val: parseFloat(student.b_ing || 0) },
        { code: 'KRT', val: parseFloat(student.menulis_kreatif || 0) },
        { code: 'KJR', val: parseFloat(student.nilai_kejuruan || 0) },
        { code: 'ADB', val: parseFloat(student.adab_akhlaq || 85) }
    ];

    metrics.forEach((m, i) => {
        const barEl = document.getElementById('bar' + m.code);
        const valEl = document.getElementById('val' + m.code);
        const pct   = Math.max(0, Math.min(100, m.val));
        if (valEl) valEl.textContent = formatSubScore(m.val);
        if (barEl) {
            setTimeout(() => {
                barEl.style.width = pct + '%';
            }, i * 35);
        }
    });

    // Rincian 2 mapel kejuruan spesifik
    const k1Alias = student.kejuruan_1_alias || 'Mapel 1';
    const k1Nilai = formatSubScore(student.kejuruan_1_nilai || 0);
    const k2Alias = student.kejuruan_2_alias || 'Mapel 2';
    const k2Nilai = formatSubScore(student.kejuruan_2_nilai || 0);

    const rowKJR = document.getElementById('rowKJR');
    if (rowKJR) {
        rowKJR.title = `${k1Alias}: ${k1Nilai} | ${k2Alias}: ${k2Nilai}`;
    }

    const kjr1El = document.getElementById('detailKjr1');
    const kjr2El = document.getElementById('detailKjr2');
    if (kjr1El) kjr1El.innerHTML = `${k1Alias}: <strong class="text-[#042f4b]">${k1Nilai}</strong>`;
    if (kjr2El) kjr2El.innerHTML = `${k2Alias}: <strong class="text-[#042f4b]">${k2Nilai}</strong>`;
}

function triggerPodiumWaveAgitate() {
    const card = document.getElementById('podiumMainCard');
    if (!card) return;
    card.classList.add('wave-agitate');
    setTimeout(() => {
        card.classList.remove('wave-agitate');
    }, 900);
}

/* =========================================================
   5. TABEL REKAPITULASI & PENCARIAN
   ========================================================= */
const flatTop3List = [];
classLabels.forEach(k => {
    (top3PerKelasData[k] || []).forEach((s, idx) => {
        flatTop3List.push({ ...s, rankKelas: idx + 1 });
    });
});

let currentTableView = 'top3';

function renderTable(dataList, isTop3View = true) {
    const tbody = document.getElementById('rankingTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (!dataList || dataList.length === 0) {
        tbody.innerHTML = `<tr><td colspan="12" class="py-10 text-center text-slate-400 font-medium">Tidak ada data ditemukan</td></tr>`;
        return;
    }

    dataList.forEach((s, idx) => {
        const globalRank = idx + 1;
        const scoreNum = parseFloat(s.rata_rata);
        let rankBadge = '';

        const rk = isTop3View ? (s.rankKelas || s.rank || 1) : globalRank;
        if (rk === 1) {
            rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#0077cc] text-white font-black text-3xs shadow-2xs">1</span>`;
        } else if (rk === 2) {
            rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#0c4a73] text-white font-black text-3xs shadow-2xs">2</span>`;
        } else if (rk === 3) {
            rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#1e6091] text-white font-black text-3xs shadow-2xs">3</span>`;
        } else {
            rankBadge = `<span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-slate-100 text-slate-500 font-semibold text-3xs">${globalRank}</span>`;
        }

        let statusBadge = '';
        if (scoreNum >= 90) {
            statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-3xs font-bold bg-[#e3f1fc] text-[#0077cc]">Istimewa</span>`;
        } else if (scoreNum >= 80) {
            statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-3xs font-bold bg-slate-100 text-[#042f4b]">Sangat Baik</span>`;
        } else {
            statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-3xs font-semibold bg-slate-100 text-slate-600">Baik</span>`;
        }

        const kjrVal  = parseFloat(s.nilai_kejuruan || 0);
        const k1Alias = s.kejuruan_1_alias || 'Mapel 1';
        const k1Nilai = parseFloat(s.kejuruan_1_nilai || 0).toFixed(1);
        const k2Alias = s.kejuruan_2_alias || 'Mapel 2';
        const k2Nilai = parseFloat(s.kejuruan_2_nilai || 0).toFixed(1);

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-sky-50/40 transition-colors';
        tr.innerHTML = `
            <td class="py-3 px-4 text-center">${rankBadge}</td>
            <td class="py-3 px-4">
                <span class="font-bold text-[#042f4b] block">${s.nama}</span>
                <span class="text-3xs text-slate-400 font-mono">ID: ${s.id || '-'}</span>
            </td>
            <td class="py-3 px-3 text-slate-600 font-medium">${s.kelas}</td>
            <td class="py-3 px-2 text-center font-medium text-slate-700">${s.tauhid}</td>
            <td class="py-3 px-2 text-center font-medium text-slate-700">${s.fiqih}</td>
            <td class="py-3 px-2 text-center font-medium text-slate-700">${s.b_ing}</td>
            <td class="py-3 px-2 text-center font-medium text-slate-700">${s.menulis_kreatif}</td>
            <td class="py-2.5 px-3 text-center bg-sky-50/30 border-x border-sky-100/60">
                <span class="font-extrabold text-[#0077cc] block text-xs tabular-nums">${kjrVal.toFixed(1)}</span>
                <span class="text-[9px] text-slate-500 block leading-tight mt-0.5 truncate max-w-[155px] mx-auto" title="${s.kejuruan_1_nama || 'Mapel 1'}: ${k1Nilai} | ${s.kejuruan_2_nama || 'Mapel 2'}: ${k2Nilai}">
                    ${k1Alias}: <strong>${k1Nilai}</strong> &bull; ${k2Alias}: <strong>${k2Nilai}</strong>
                </span>
            </td>
            <td class="py-3 px-2 text-center font-black text-[#042f4b] bg-sky-50/20">${s.adab_akhlaq || 85}</td>
            <td class="py-3 px-3 text-center">
                <span class="text-3xs font-bold text-[#0077cc] bg-[#e3f1fc] px-2 py-0.5 rounded-full">${s.kehadiran || 95}%</span>
            </td>
            <td class="py-3 px-5 text-right font-extrabold text-[#0077cc] text-sm tracking-tight tabular-nums">
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
        btnTop3.className = 'px-4 py-1.5 rounded-full bg-[#0077cc] text-white shadow-2xs transition cursor-pointer';
        btnAll.className  = 'px-4 py-1.5 rounded-full text-[#64829c] hover:text-[#042f4b] transition cursor-pointer';
        renderTable(flatTop3List, true);
    } else {
        btnAll.className  = 'px-4 py-1.5 rounded-full bg-[#0077cc] text-white shadow-2xs transition cursor-pointer';
        btnTop3.className = 'px-4 py-1.5 rounded-full text-[#64829c] hover:text-[#042f4b] transition cursor-pointer';
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

/* Inisialisasi Saat Halaman Dimuat */
window.addEventListener('DOMContentLoaded', function() {
    const firstTabBtn = document.querySelector('.class-tab-pill');
    if (firstTabBtn) {
        moveTabSlidingPill(firstTabBtn);
    }
    if (activeClassKey) {
        renderPodiumForClass(activeClassKey, false);
        renderScoreDetailPanel(false);
    }
    renderTable(flatTop3List, true);
});

window.addEventListener('resize', function() {
    const activeBtn = document.querySelector(`.class-tab-pill[data-class-key="${CSS.escape(activeClassKey)}"]`);
    if (activeBtn) moveTabSlidingPill(activeBtn);
});
</script>
</body>
</html>