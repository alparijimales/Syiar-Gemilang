<?php
$selectedJurusan = $selectedJurusan ?? 'all';
$daftarJurusan   = $daftarJurusan ?? [];
$ranking         = $ranking ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Cetak Ranking Santri'); ?> - Syiar Gemilang</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Konfigurasi default halaman cetak ke A4 Landscape agar seluruh 13 kolom nilai pas dan tidak terpotong */
        @page {
            size: A4 landscape;
            margin: 8mm 10mm;
        }

        @media print {
            .no-print { display: none !important; }
            html, body {
                background: white !important;
                color: #0f172a !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .overflow-x-auto {
                overflow: visible !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
            th, td {
                padding: 4px 3px !important;
                line-height: 1.25 !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
            }
            th {
                font-size: 7.5pt !important;
            }
            td {
                font-size: 7.5pt !important;
            }
        }

        @media screen {
            .print-container {
                max-width: 1240px;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen p-4 sm:p-8">

    <!-- Toolbar Atas (No-Print) -->
    <div class="no-print max-w-7xl mx-auto mb-6 p-4 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/pemeringkatan'); ?>" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div>
                <h1 class="text-sm font-bold text-slate-800">Cetak Daftar Ranking &amp; Prestasi</h1>
                <p class="text-3xs text-slate-500">Pratinjau cetak resmi A4 Landscape pemeringkatan per jurusan dan angkatan.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-sky-50 text-sky-700 text-2xs font-semibold border border-sky-200">
                <i class="fa-solid fa-arrows-left-right text-[10px]"></i> Format: A4 Landscape (Anti-Potong)
            </span>

            <!-- Filter Jurusan -->
            <form method="GET" action="<?= base_url('admin/cetak-ranking'); ?>" class="flex items-center gap-2">
                <select name="jurusan" onchange="this.form.submit()" class="px-3 py-1.5 text-xs rounded-xl border border-slate-300 bg-white font-medium focus:outline-none focus:ring-2 focus:ring-[#0066CC]">
                    <option value="all" <?= ($selectedJurusan === 'all' || empty($selectedJurusan)) ? 'selected' : ''; ?>>-- Semua Jurusan --</option>
                    <?php foreach ($daftarJurusan as $j): ?>
                        <option value="<?= esc($j); ?>" <?= ($selectedJurusan === $j) ? 'selected' : ''; ?>><?= esc($j); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <a href="<?= base_url('admin/export-ranking-csv?jurusan=' . urlencode($selectedJurusan)); ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> Export CSV
            </a>

            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-[#0066CC] hover:bg-[#0052A3] text-white text-xs font-bold shadow-md shadow-[#0066CC]/25 transition flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Paper Container A4 -->
    <div class="print-container max-w-7xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-md border border-slate-200">
        
        <!-- KOP SURAT RESMI -->
        <div class="flex items-center justify-between pb-4 border-b-2 border-slate-800 gap-4">
            <div class="flex items-center gap-4">
                <img src="<?= base_url('assets/img/rgi.png'); ?>" alt="Logo RGI" class="w-16 h-16 object-contain">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-slate-900 uppercase">Rumah Gemilang Indonesia (RGI)</h2>
                    <h3 class="text-xs font-bold tracking-wider text-[#0066CC] uppercase">Dewan Evaluasi &amp; Penilaian Prestasi Santri</h3>
                    <p class="text-3xs text-slate-600 mt-0.5">
                        Kampus Utama: Jl. Raya Sawangan No. 1, Sawangan, Kota Depok, Jawa Barat 16511 | Website: rgi.or.id
                    </p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="inline-block px-2.5 py-1 rounded border border-amber-300 text-3xs font-mono font-bold bg-amber-50 text-amber-800 uppercase">
                    PEMERINGKATAN RESMI
                </span>
                <span class="block text-3xs text-slate-400 mt-1"><?= date('d F Y'); ?></span>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="text-center my-6">
            <h2 class="text-base font-extrabold uppercase tracking-wide text-slate-900">
                SURAT KEPUTUSAN DAFTAR PEMERINGKATAN SANTRI TERBAIK
            </h2>
            <p class="text-xs text-slate-600 mt-1">
                <?= ($selectedJurusan && $selectedJurusan !== 'all') ? 'Jurusan: <strong>' . esc($selectedJurusan) . '</strong> &bull; ' : 'Seluruh Jurusan Kejuruan &bull; '; ?>
                Angkatan 35 &bull; Bobot Penilaian: 80% Akademik (5 Mapel Termasuk Kejuruan) + 20% Adab &amp; Karakter
            </p>
        </div>

        <!-- Tabel Ranking -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-slate-300 text-xs table-fixed">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 text-3xs uppercase font-extrabold tracking-wider border-b border-slate-300">
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[4%]">Rank</th>
                        <th class="py-2.5 px-2 border-r border-slate-300 w-[8%] font-mono">NIS</th>
                        <th class="py-2.5 px-2 border-r border-slate-300 w-[17%]">Nama Santri</th>
                        <th class="py-2.5 px-2 border-r border-slate-300 w-[12%]">Jurusan</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">Tauhid</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">Fiqih</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">B.Ing</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">Kreatif</th>
                        <th class="py-2.5 px-2 text-center border-r border-slate-300 w-[12%] font-bold bg-blue-50/80 text-[#0066CC]">Kejuruan</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[6%] font-bold bg-amber-50">Adab 20%</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">Hadir</th>
                        <th class="py-2.5 px-2 text-center border-r border-slate-300 w-[7.5%] font-bold bg-blue-50 text-[#0066CC]">Nilai Akhir</th>
                        <th class="py-2.5 px-1 text-center w-[8.5%]">Predikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-3xs">
                    <?php if (empty($ranking)): ?>
                        <tr>
                            <td colspan="13" class="py-6 text-center text-slate-400 italic">Tidak ada data santri ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $rank = 1; foreach ($ranking as $st): ?>
                        <?php
                            $isTop3 = $rank <= 3;
                            $medalBadge = '';
                            if ($rank === 1) $medalBadge = '🥇 Juara 1';
                            elseif ($rank === 2) $medalBadge = '🥈 Juara 2';
                            elseif ($rank === 3) $medalBadge = '🥉 Juara 3';

                            $score = (float)$st['rata_rata'];
                            $predikat = 'Memuaskan';
                            if ($score >= 90) $predikat = 'Sangat Memuaskan (Mumtaz)';
                            elseif ($score >= 80) $predikat = 'Baik (Jayyid Jiddan)';
                            elseif ($score >= 70) $predikat = 'Cukup (Jayyid)';
                        ?>
                        <tr class="hover:bg-slate-50 <?= $isTop3 ? 'bg-amber-50/20 font-medium' : (($rank % 2 === 0) ? 'bg-slate-50/50' : 'bg-white'); ?>">
                            <td class="py-2 px-1 text-center font-bold border-r border-slate-200">
                                <?php if ($isTop3): ?>
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full <?= $rank === 1 ? 'bg-amber-400 text-slate-900 font-black' : ($rank === 2 ? 'bg-slate-300 text-slate-900 font-bold' : 'bg-amber-600 text-white font-bold'); ?> text-[9px] shadow-xs">
                                        <?= $rank; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-600 font-mono"><?= $rank; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-1.5 font-mono text-2xs border-r border-slate-200 truncate"><?= esc($st['nis'] ?: '-'); ?></td>
                            <td class="py-2 px-2 font-bold text-slate-900 border-r border-slate-200 leading-tight">
                                <div><?= esc($st['nama']); ?></div>
                                <?php if ($medalBadge): ?>
                                    <span class="inline-block text-[9px] font-extrabold text-amber-700 leading-none"><?= $medalBadge; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-2 border-r border-slate-200 leading-tight"><?= esc($st['kelas']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['tauhid']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['fiqih']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['b_ing']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['menulis_kreatif']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200 bg-blue-50/30">
                                <span class="font-bold text-[#0066CC]"><?= number_format($st['nilai_kejuruan'] ?? 0, 1); ?></span>
                                <span class="block text-[8px] text-slate-500 font-normal leading-tight">
                                    <?= esc($st['kejuruan_1_alias'] ?? 'M1'); ?>: <?= number_format($st['kejuruan_1_nilai'] ?? 0, 1); ?><br><?= esc($st['kejuruan_2_alias'] ?? 'M2'); ?>: <?= number_format($st['kejuruan_2_nilai'] ?? 0, 1); ?>
                                </span>
                            </td>
                            <td class="py-2 px-1 text-center font-bold text-amber-900 border-r border-slate-200 bg-amber-50/40"><?= esc($st['adab_akhlaq']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['kehadiran']); ?>%</td>
                            <td class="py-2 px-1 text-center font-bold text-[#0066CC] border-r border-slate-200 bg-blue-50/40 text-xs">
                                <?= number_format($st['rata_rata'], 2); ?>
                            </td>
                            <td class="py-2 px-1 text-center leading-tight">
                                <span class="text-[9px] font-semibold text-slate-700"><?= $predikat; ?></span>
                            </td>
                        </tr>
                        <?php $rank++; endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kolom Tanda Tangan Resmi -->
        <div class="mt-12 pt-6 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="text-3xs text-slate-500">Mengetahui &amp; Mengesahkan,</p>
                <p class="font-bold text-slate-800 mt-0.5">Kepala Diklat RGI</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">Ustadz Kamal Malawi</p>
                <p class="text-3xs text-slate-500">NIP. 2021-RGI-001</p>
            </div>
            <div>
                <p class="text-3xs text-slate-500">Depok, <?= date('d F Y'); ?></p>
                <p class="font-bold text-slate-800 mt-0.5">Dewan Guru &amp; Pemeringkatan</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">Administrator Syiar Gemilang</p>
                <p class="text-3xs text-slate-500">Sistem Informasi Akademik</p>
            </div>
        </div>

    </div>

</body>
</html>
