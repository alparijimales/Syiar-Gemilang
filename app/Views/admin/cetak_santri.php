<?php
$selectedJurusan = $selectedJurusan ?? 'all';
$daftarJurusan   = $daftarJurusan ?? [];
$students        = $students ?? [];
$rataRata        = $rataRata ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Cetak Data Santri'); ?> - Syiar Gemilang</title>
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
            <a href="<?= base_url('admin/data'); ?>" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div>
                <h1 class="text-sm font-bold text-slate-800">Cetak Daftar Santri</h1>
                <p class="text-3xs text-slate-500">Pratinjau cetak resmi A4 Landscape lembaga pendidikan RGI.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-sky-50 text-sky-700 text-2xs font-semibold border border-sky-200">
                <i class="fa-solid fa-arrows-left-right text-[10px]"></i> Format: A4 Landscape (Anti-Potong)
            </span>

            <!-- Filter Jurusan -->
            <form method="GET" action="<?= base_url('admin/cetak-santri'); ?>" class="flex items-center gap-2">
                <select name="jurusan" onchange="this.form.submit()" class="px-3 py-1.5 text-xs rounded-xl border border-slate-300 bg-white font-medium focus:outline-none focus:ring-2 focus:ring-[#0066CC]">
                    <option value="all" <?= ($selectedJurusan === 'all' || empty($selectedJurusan)) ? 'selected' : ''; ?>>-- Semua Jurusan --</option>
                    <?php foreach ($daftarJurusan as $j): ?>
                        <option value="<?= esc($j); ?>" <?= ($selectedJurusan === $j) ? 'selected' : ''; ?>><?= esc($j); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <a href="<?= base_url('admin/export-santri-csv?jurusan=' . urlencode($selectedJurusan)); ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> Export Excel/CSV
            </a>

            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-[#0066CC] hover:bg-[#0052A3] text-white text-xs font-bold shadow-md shadow-[#0066CC]/25 transition flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Paper Container A4 -->
    <div class="print-container max-w-7xl mx-auto bg-white p-8 sm:p-10 rounded-2xl shadow-md border border-slate-200">
        
        <!-- KOP SURAT RESMI -->
        <div class="flex items-center justify-between pb-4 border-b-2 border-slate-800 gap-4">
            <div class="flex items-center gap-4">
                <img src="<?= base_url('assets/img/rgi.png'); ?>" alt="Logo RGI" class="w-16 h-16 object-contain">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-slate-900 uppercase">Rumah Gemilang Indonesia (RGI)</h2>
                    <h3 class="text-xs font-bold tracking-wider text-[#0066CC] uppercase">Lembaga Pendidikan &amp; Pemberdayaan Generasi Muda</h3>
                    <p class="text-3xs text-slate-600 mt-0.5">
                        Kampus Utama: Jl. Raya Sawangan No. 1, Sawangan, Kota Depok, Jawa Barat 16511 | Website: rgi.or.id | Email: diklat@rgi.or.id
                    </p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="inline-block px-2.5 py-1 rounded border border-slate-300 text-3xs font-mono font-bold bg-slate-50 uppercase">
                    DOKUMEN RESMI
                </span>
                <span class="block text-3xs text-slate-400 mt-1"><?= date('d F Y'); ?></span>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="text-center my-6">
            <h2 class="text-base font-extrabold uppercase tracking-wide text-slate-900">
                DAFTAR INDUK SANTRI &amp; CAPAIAN AKADEMIK
            </h2>
            <p class="text-xs text-slate-600 mt-1">
                <?= ($selectedJurusan && $selectedJurusan !== 'all') ? 'Jurusan: <strong>' . esc($selectedJurusan) . '</strong> &bull; ' : 'Semua Jurusan Kejuruan &bull; '; ?>
                Angkatan 35 &bull; Tahun Ajaran 2026/2027
            </p>
        </div>

        <!-- Tabel Santri -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-slate-300 text-xs table-fixed">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 text-3xs uppercase font-extrabold tracking-wider border-b border-slate-300">
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[3.5%]">No</th>
                        <th class="py-2.5 px-2 border-r border-slate-300 w-[8%] font-mono">NIS</th>
                        <th class="py-2.5 px-2 border-r border-slate-300 w-[17%]">Nama Lengkap</th>
                        <th class="py-2.5 px-2 border-r border-slate-300 w-[12%]">Jurusan</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">Tauhid</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">Fiqih</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">B.Ing</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%]">Kreatif</th>
                        <th class="py-2.5 px-2 text-center border-r border-slate-300 w-[12%] font-bold bg-blue-50/70 text-[#0066CC]">Kejuruan</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5%] font-bold bg-slate-200">Adab</th>
                        <th class="py-2.5 px-1 text-center border-r border-slate-300 w-[5.5%]">Hadir</th>
                        <th class="py-2.5 px-2 text-center border-r border-slate-300 w-[8%] font-bold bg-blue-50 text-[#0066CC]">Nilai Akhir</th>
                        <th class="py-2.5 px-1 text-center w-[8%]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-3xs">
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="13" class="py-6 text-center text-slate-400 italic">Tidak ada data santri ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($students as $st): ?>
                        <tr class="hover:bg-slate-50 <?= ($no % 2 === 0) ? 'bg-slate-50/50' : 'bg-white'; ?>">
                            <td class="py-2 px-1 text-center font-mono border-r border-slate-200"><?= $no++; ?></td>
                            <td class="py-2 px-2 text-center font-mono font-semibold border-r border-slate-200 truncate"><?= esc($st['nis'] ?: '-'); ?></td>
                            <td class="py-2 px-2 font-bold text-slate-900 border-r border-slate-200 leading-tight"><?= esc($st['nama']); ?></td>
                            <td class="py-2 px-2 border-r border-slate-200 leading-tight"><?= esc($st['kelas']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['tauhid']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['fiqih']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['b_ing']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['menulis_kreatif']); ?></td>
                            <td class="py-1.5 px-1 text-center border-r border-slate-200 bg-blue-50/20">
                                <span class="font-bold text-[#0066CC]"><?= number_format($st['nilai_kejuruan'] ?? 0, 1); ?></span>
                                <span class="block text-[7.5px] text-slate-500 font-normal leading-tight">
                                    <?= esc($st['kejuruan_1_alias'] ?? 'M1'); ?>:<?= number_format($st['kejuruan_1_nilai'] ?? 0, 1); ?> | <?= esc($st['kejuruan_2_alias'] ?? 'M2'); ?>:<?= number_format($st['kejuruan_2_nilai'] ?? 0, 1); ?>
                                </span>
                            </td>
                            <td class="py-2 px-1 text-center font-bold text-amber-900 border-r border-slate-200 bg-amber-50/30"><?= esc($st['adab_akhlaq']); ?></td>
                            <td class="py-2 px-1 text-center border-r border-slate-200"><?= esc($st['kehadiran']); ?>%</td>
                            <td class="py-2 px-2 text-center font-bold text-[#0066CC] border-r border-slate-200 bg-blue-50/40">
                                <?= number_format($st['rata_rata'], 2); ?>
                            </td>
                            <td class="py-2 px-1 text-center">
                                <span class="font-semibold text-2xs <?= $st['status'] === 'Aktif' ? 'text-emerald-700' : 'text-slate-500'; ?>">
                                    <?= esc($st['status'] ?: 'Aktif'); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Ringkasan Statistik -->
        <div class="mt-4 pt-3 border-t border-slate-200 flex flex-wrap items-center justify-between text-3xs text-slate-600">
            <div>
                Total Santri Terdata: <strong><?= count($students); ?> Orang</strong> &bull; Rata-Rata Angkatan: <strong><?= $rataRata; ?></strong>
            </div>
            <div>
                * Rumus Nilai Akhir: (80% Akademik + 20% Adab &amp; Karakter Pesantren)
            </div>
        </div>

        <!-- Kolom Tanda Tangan Resmi -->
        <div class="mt-12 pt-6 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="text-3xs text-slate-500">Mengetahui,</p>
                <p class="font-bold text-slate-800 mt-0.5">Kepala Diklat RGI</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">Ustadz Kamal Malawi</p>
                <p class="text-3xs text-slate-500">NIP. 2021-RGI-001</p>
            </div>
            <div>
                <p class="text-3xs text-slate-500">Depok, <?= date('d F Y'); ?></p>
                <p class="font-bold text-slate-800 mt-0.5">Koordinator Akademik &amp; Santri</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">Administrator Syiar Gemilang</p>
                <p class="text-3xs text-slate-500">Sistem Informasi Akademik</p>
            </div>
        </div>

    </div>

</body>
</html>
