<?php
// Perhitungan Nilai & Predikat Akademik
function hitungGrade($score) {
    $score = (float)$score;
    if ($score >= 93) return ['predikat' => 'A+', 'keterangan' => 'Istimewa'];
    if ($score >= 88) return ['predikat' => 'A',  'keterangan' => 'Sangat Baik'];
    if ($score >= 83) return ['predikat' => 'B+', 'keterangan' => 'Baik'];
    if ($score >= 75) return ['predikat' => 'B',  'keterangan' => 'Cukup Baik'];
    if ($score >= 70) return ['predikat' => 'C',  'keterangan' => 'Cukup'];
    return ['predikat' => 'D', 'keterangan' => 'Perlu Bimbingan'];
}

$tauhidGrade  = hitungGrade($santri['tauhid'] ?? 0);
$fiqihGrade   = hitungGrade($santri['fiqih'] ?? 0);
$bingGrade    = hitungGrade($santri['b_ing'] ?? 0);
$kreatifGrade = hitungGrade($santri['menulis_kreatif'] ?? 0);
$kejuruanVal  = (float)($santri['nilai_kejuruan'] ?? 0);
$kejuruanGrade= hitungGrade($kejuruanVal);

// Rata-rata Akademik & Predikat
$rataAkademik = (float)($santri['rata_akademik'] ?? 0);
$akademikGrade= hitungGrade($rataAkademik);

// Perhitungan Indikator Adab (Bobot 20%)
$baseAdab = (float)($santri['adab_akhlaq'] ?? 85);
$adab1 = round($baseAdab);
$adab2 = min(98, round($baseAdab + 2));
$adab3 = max(70, round(($baseAdab * 3) - $adab1 - $adab2));

function hitungSikap($score) {
    $score = (float)$score;
    if ($score >= 90) return 'Sangat Terpuji';
    if ($score >= 80) return 'Terpuji';
    if ($score >= 70) return 'Baik';
    return 'Cukup';
}

$adab1Sikap = hitungSikap($adab1);
$adab2Sikap = ($adab2 >= 90) ? 'Sangat Disiplin' : 'Disiplin';
$adab3Sikap = ($adab3 >= 85) ? 'Baik & Mandiri' : 'Cukup Mandiri';

$predikatAdab = 'Baik (B)';
if ($baseAdab >= 90) $predikatAdab = 'Mumtaz (A)';
elseif ($baseAdab >= 80) $predikatAdab = 'Jayyid Jiddan (B+)';
elseif ($baseAdab >= 70) $predikatAdab = 'Jayyid (B)';

// Predikat Rapor Keseluruhan (80% + 20%)
$finalScore = (float)($santri['rata_rata'] ?? 0);
$predikatUmum = 'Sangat Memuaskan (Mumtaz)';
if ($finalScore < 75) $predikatUmum = 'Cukup (Jayyid)';
elseif ($finalScore < 85) $predikatUmum = 'Baik (Jayyid Jiddan)';
elseif ($finalScore < 92) $predikatUmum = 'Sangat Baik (A)';
else $predikatUmum = 'Istimewa (Mumtaz Syaraf)';

// Evaluasi Otomatis Instruktur
$catatanEvaluasi = '';
if (!empty($santri_kompetensi[0]['catatan'])) {
    $catatanEvaluasi = $santri_kompetensi[0]['catatan'];
} else {
    $catatanEvaluasi = "Ananda " . esc($santri['nama']) . " menunjukkan kesungguhan dan kemajuan yang sangat positif selama menempuh pelatihan di kejuruan " . esc($santri['kelas']) . ". ";
    if ($finalScore >= 90) {
        $catatanEvaluasi .= "Menguasai kompetensi praktik dan teori dengan keunggulan di atas rata-rata. Kedisiplinan ibadah dan adab terhadap sesama sangat terpuji. Disarankan terus mengembangkan portofolio karya mandiri untuk persiapan magang industri.";
    } elseif ($finalScore >= 80) {
        $catatanEvaluasi .= "Mampu menuntaskan target penugasan kejuruan dengan baik dan aktif berpartisipasi dalam kegiatan asrama. Terus tingkatkan inisiatif, konsistensi belajar, dan ketelitian teknis.";
    } else {
        $catatanEvaluasi .= "Telah menyelesaikan materi kejuruan dasar dengan cukup baik. Perlu ditingkatkan konsistensi kehadiran dan latihan praktik mandiri agar semakin kompeten.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Rapor <?= esc($santri['nama']); ?> - RGI Syiar Gemilang</title>
    <!-- FontAwesome untuk Icon Toolbar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Pengaturan Cetak Halaman A4 Portrait */
        @page {
            size: A4 portrait;
            margin: 8mm 12mm 8mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.3;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        /* Screen Toolbar (No-Print) */
        .no-print-toolbar {
            background: #073B66;
            color: #ffffff;
            padding: 10px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .no-print-toolbar a {
            color: #bae6fd;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.2s;
        }

        .no-print-toolbar a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .no-print-toolbar .btn-print {
            background: #0066CC;
            color: white;
            border: none;
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 2px 8px rgba(0, 102, 204, 0.4);
            transition: all 0.2s;
        }

        .no-print-toolbar .btn-print:hover {
            background: #0052A3;
        }

        /* Paper Wrapper */
        .rapor-wrapper {
            max-width: 820px;
            margin: 18px auto 30px auto;
            background: #ffffff;
            padding: 10mm 13mm;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.1);
            border-radius: 6px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .rapor-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .kop-table td {
            vertical-align: middle;
        }

        .kop-logo-azhar {
            max-width: 135px;
            max-height: 52px;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .kop-logo-rgi {
            max-width: 105px;
            max-height: 52px;
            height: auto;
            object-fit: contain;
            display: block;
            margin-left: auto;
        }

        .kop-text {
            text-align: center;
            padding: 0 8px;
        }

        .kop-text h4 {
            margin: 0;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #047857;
            text-transform: uppercase;
        }

        .kop-text h2 {
            margin: 2px 0 1px 0;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.3px;
        }

        .kop-text p {
            margin: 1px 0;
            font-size: 8.5px;
            color: #475569;
            line-height: 1.25;
        }

        .kop-divider {
            border-top: 2px solid #0f172a;
            border-bottom: 1px solid #0f172a;
            height: 2px;
            margin: 6px 0 8px 0;
        }

        /* Judul Rapor */
        .rapor-title {
            text-align: center;
            margin-bottom: 8px;
        }

        .rapor-title h3 {
            margin: 0;
            font-size: 12.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .rapor-title span {
            font-size: 9.5px;
            color: #64748b;
            font-weight: 600;
        }

        /* Identitas Santri */
        .identitas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            background: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        .identitas-table td {
            font-size: 9.5px;
            padding: 2.5px 6px;
            vertical-align: middle;
        }

        .identitas-table .label {
            width: 16%;
            color: #475569;
            font-weight: 500;
        }

        .identitas-table .colon {
            width: 2%;
            text-align: center;
        }

        .identitas-table .val {
            width: 32%;
            font-weight: 700;
            color: #0f172a;
        }

        /* Tabel Penilaian */
        .section-heading {
            font-size: 10px;
            font-weight: 800;
            color: #065f46;
            text-transform: uppercase;
            margin: 6px 0 3px 0;
            display: block;
            letter-spacing: 0.3px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 9.5px;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.2px;
        }

        .data-table td.center {
            text-align: center;
        }

        .data-table td.right {
            text-align: right;
        }

        .data-table tr.total-row {
            background-color: #f8fafc;
            font-weight: 800;
        }

        /* Kotak Catatan & Kehadiran */
        .notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .notes-table td {
            vertical-align: top;
        }

        .attendance-box {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-box th,
        .attendance-box td {
            border: 1px solid #cbd5e1;
            padding: 3.5px 6px;
            font-size: 9px;
        }

        .attendance-box th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            text-align: center;
            font-size: 8.5px;
        }

        .feedback-box {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            border-radius: 4px;
            background-color: #fafafa;
            font-size: 9px;
            min-height: 55px;
            line-height: 1.35;
            color: #334155;
        }

        /* Kolom Tanda Tangan */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            page-break-inside: avoid;
        }

        .ttd-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            font-size: 9.5px;
        }

        .ttd-space {
            height: 48px;
        }

        .ttd-name {
            font-weight: 800;
            text-decoration: underline;
            color: #0f172a;
        }

        .ttd-role {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- TOOLBAR LAYAR (Hanya tampil di monitor, disembunyikan saat cetak PDF) -->
    <div class="no-print no-print-toolbar">
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="<?= base_url('wali'); ?>">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <span style="color: #38bdf8; font-size: 12px; font-weight: 700;">
                E-Rapor Santri: <?= esc($santri['nama']); ?> (<?= esc($santri['kelas']); ?>)
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 11px; color: #bae6fd;">Format: A4 Portrait</span>
            <button onclick="window.print()" class="btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- WRAPPER KERTAS RAPOR A4 -->
    <div class="rapor-wrapper">

        <!-- KOP SURAT RESMI (AL-AZHAR & RGI) -->
        <table class="kop-table">
            <tr>
                <!-- Logo Al-Azhar (Kiri) -->
                <td style="width: 22%; text-align: left;">
                    <img src="<?= base_url('assets/img/logo-alazhar-rapor.png'); ?>" alt="Logo LAZ Al-Azhar" class="kop-logo-azhar">
                </td>
                
                <!-- Teks Kop Surat -->
                <td style="width: 56%;" class="kop-text">
                    <h4>Lembaga Amil Zakat &amp; Wakaf Al-Azhar</h4>
                    <h2>RUMAH GEMILANG INDONESIA (RGI)</h2>
                    <p>Kampus Pemberdayaan Pemuda Mandiri — Pusat Pelatihan &amp; Keterampilan Kejuruan</p>
                    <p>Jl. Pengasinan Raya, Sawangan, Kota Depok, Jawa Barat 16518 | Telp: (021) 7788-9900</p>
                    <p>Website: www.alazharpeduli.org &bull; Email: rgi@alazharpeduli.org</p>
                </td>

                <!-- Logo RGI (Kanan) -->
                <td style="width: 22%; text-align: right;">
                    <img src="<?= base_url('assets/img/logo-rgi-rapor.png'); ?>" alt="Logo RGI" class="kop-logo-rgi">
                </td>
            </tr>
        </table>

        <div class="kop-divider"></div>
        <br>

        <!-- JUDUL RAPOR -->
        <div class="rapor-title">
            <h3>Laporan Hasil Evaluasi Santri (E-Rapor)</h3>
            <span>Periode Evaluasi Akademik, Karakter &amp; Kejuruan &bull; Angkatan 35</span>
        </div>
        <br>


        <!-- IDENTITAS SANTRI -->
        <table class="identitas-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="colon">:</td>
                <td class="val"><?= esc($santri['nama']); ?></td>

                <td class="label">Nomor Induk Santri</td>
                <td class="colon">:</td>
                <td class="val"><?= esc($santri['nis'] ?: '-'); ?></td>
            </tr>
            <tr>
                <td class="label">Program Kejuruan</td>
                <td class="colon">:</td>
                <td class="val"><?= esc($santri['kelas']); ?></td>

                <td class="label">Angkatan / Tahun</td>
                <td class="colon">:</td>
                <td class="val"><?= esc($santri['angkatan'] ?? 'Angkatan 35'); ?> / <?= date('Y'); ?></td>
            </tr>
            <tr>
                <td class="label">Semester / Fase</td>
                <td class="colon">:</td>
                <td class="val">Ganjil (Evaluasi Akhir)</td>

                <td class="label">Predikat Umum</td>
                <td class="colon">:</td>
                <td class="val"><?= $predikatUmum; ?></td>
            </tr>
        </table>

        <!-- 1. CAPAIAN NILAI AKADEMIK & KEJURUAN (BOBOT 80%) -->
        <span class="section-heading">A. Capaian Kompetensi Dasar &amp; Kejuruan (80%)</span>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Mata Pelajaran / Bidang Kompetensi</th>
                    <th style="width: 12%;">Kriteria (KKM)</th>
                    <th style="width: 12%;">Nilai Angka</th>
                    <th style="width: 12%;">Predikat</th>
                    <th style="width: 14%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td>Tauhid &amp; Aqidah Islamiyah</td>
                    <td class="center">75</td>
                    <td class="center"><strong><?= (int)($santri['tauhid'] ?? 0); ?></strong></td>
                    <td class="center"><?= $tauhidGrade['predikat']; ?></td>
                    <td class="center"><?= $tauhidGrade['keterangan']; ?></td>
                </tr>
                <tr>
                    <td class="center">2</td>
                    <td>Fiqih Ibadah &amp; Muamalah</td>
                    <td class="center">75</td>
                    <td class="center"><strong><?= (int)($santri['fiqih'] ?? 0); ?></strong></td>
                    <td class="center"><?= $fiqihGrade['predikat']; ?></td>
                    <td class="center"><?= $fiqihGrade['keterangan']; ?></td>
                </tr>
                <tr>
                    <td class="center">3</td>
                    <td>Bahasa Inggris (Komunikasi &amp; IT)</td>
                    <td class="center">70</td>
                    <td class="center"><strong><?= (int)($santri['b_ing'] ?? 0); ?></strong></td>
                    <td class="center"><?= $bingGrade['predikat']; ?></td>
                    <td class="center"><?= $bingGrade['keterangan']; ?></td>
                </tr>
                <tr>
                    <td class="center">4</td>
                    <td>Literasi &amp; Menulis Kreatif</td>
                    <td class="center">70</td>
                    <td class="center"><strong><?= (int)($santri['menulis_kreatif'] ?? 0); ?></strong></td>
                    <td class="center"><?= $kreatifGrade['predikat']; ?></td>
                    <td class="center"><?= $kreatifGrade['keterangan']; ?></td>
                </tr>
                <tr>
                    <td class="center">5</td>
                    <td>
                        <strong>Kompetensi Kejuruan (Praktik Lab &amp; Teori)</strong>
                        <div style="font-size: 8.5px; color: #475569; margin-top: 1px;">
                            Jurusan: <?= esc($santri['kelas']); ?>
                            <?php if (!empty($santri_kompetensi)): ?>
                                &bull; Modul: 
                                <?php 
                                    $modulList = [];
                                    foreach ($santri_kompetensi as $skItem) {
                                        $modulList[] = esc($skItem['kode_mapel'] ?? '') . ' (' . number_format($skItem['nilai_akhir'] ?? 0, 1) . ')';
                                    }
                                    echo implode(', ', $modulList);
                                ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="center">75</td>
                    <td class="center"><strong><?= number_format($kejuruanVal, 1); ?></strong></td>
                    <td class="center"><?= $kejuruanGrade['predikat']; ?></td>
                    <td class="center"><?= $kejuruanGrade['keterangan']; ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="center">Rata-Rata Nilai Akademik (Skala 100)</td>
                    <td class="center" style="color: #0066CC;"><strong><?= number_format($rataAkademik, 2); ?></strong></td>
                    <td class="center"><?= $akademikGrade['predikat']; ?></td>
                    <td class="center"><?= ($rataAkademik >= 75) ? 'Kompeten' : 'Cukup Kompeten'; ?></td>
                </tr>
            </tbody>
        </table>
<br>
<br>

        <!-- 2. ASPEK ADAB, AKHLAQ KARIMAH & SIKAP (BOBOT 20%) -->
        <span class="section-heading">B. Pembinaan Karakter, Adab &amp; Kedisiplinan (20%)</span>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 55%;">Indikator Penilaian Adab &amp; Perilaku</th>
                    <th style="width: 18%;">Skor Nilai</th>
                    <th style="width: 22%;">Predikat Sikap</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td>Adab kepada Instruktur, Pembina &amp; Sesama Santri</td>
                    <td class="center"><strong><?= $adab1; ?></strong></td>
                    <td class="center"><?= $adab1Sikap; ?></td>
                </tr>
                <tr>
                    <td class="center">2</td>
                    <td>Kedisiplinan Shalat Berjamaah &amp; Ibadah Yaumiyah</td>
                    <td class="center"><strong><?= $adab2; ?></strong></td>
                    <td class="center"><?= $adab2Sikap; ?></td>
                </tr>
                <tr>
                    <td class="center">3</td>
                    <td>Kebersihan Kamar, Lingkungan &amp; Tanggung Jawab Asrama</td>
                    <td class="center"><strong><?= $adab3; ?></strong></td>
                    <td class="center"><?= $adab3Sikap; ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" class="center">Rata-rata Nilai Adab &amp; Akhlaq</td>
                    <td class="center" style="color: #b45309;"><strong><?= number_format($baseAdab, 2); ?></strong></td>
                    <td class="center"><?= $predikatAdab; ?></td>
                </tr>
            </tbody>
        </table>

        <!-- 3. PRESENSI & CATATAN EVALUASI -->
        <table class="notes-table">
            <tr>
                <!-- Presensi Kehadiran -->
                <td style="width: 38%; padding-right: 8px;">
                    <span class="section-heading" style="margin-top: 0;">C. Rekapitulasi Presensi</span>
                    <table class="attendance-box">
                        <thead>
                            <tr>
                                <th>Keterangan</th>
                                <th style="width: 35%;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Hadir Praktik / Sesi Kelas</td>
                                <td class="center"><strong><?= esc($santri['kehadiran'] ?? 95); ?> %</strong></td>
                            </tr>
                            <tr>
                                <td>Izin / Terlambat (Terverifikasi)</td>
                                <td class="center"><?= (int)($my_rekap['count_telat'] ?? 0); ?> Sesi</td>
                            </tr>
                            <tr>
                                <td>Sakit (Surat Dokter / Pembina)</td>
                                <td class="center"><?= (int)($my_rekap['count_sakit'] ?? 0); ?> Sesi</td>
                            </tr>
                            <tr>
                                <td>Tanpa Keterangan (Alpa)</td>
                                <td class="center"><?= (int)($my_rekap['count_absen'] ?? 0); ?> Sesi</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <!-- Catatan Instruktur -->
                <td style="width: 62%;">
                    <span class="section-heading" style="margin-top: 0;">D. Catatan Perkembangan &amp; Rekomendasi</span>
                    <div class="feedback-box">
                        <strong>Evaluasi Instruktur Kejuruan (<?= esc($instruktur_nama); ?>):</strong><br>
                        <?= $catatanEvaluasi; ?>
                    </div>
                </td>
            </tr>
        </table>
<br>
<br>

        <!-- TANDA TANGAN RESMI (3 KOLOM) -->
        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Orang Tua / Wali Santri</strong>
                    <div class="ttd-space"></div>
                    <div class="ttd-name"><?= esc(session()->get('nama') ?? 'Wali Santri'); ?></div>
                    <div class="ttd-role">Tanda Tangan &amp; Nama Terang</div>
                </td>
                <td>
                    Depok, <?= date('d F Y'); ?><br>
                    <strong>Instruktur Kejuruan</strong>
                    <div class="ttd-space"></div>
                    <div class="ttd-name"><?= esc($instruktur_nama); ?></div>
                    <div class="ttd-role">Instruktur <?= esc($santri['kelas']); ?></div>
                </td>
                <td>
                    Menyetujui,<br>
                    <strong>Kepala Diklat RGI</strong>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">Alam Maptullah</div>
                    <div class="ttd-role">Kepala Diklat RGI LAZ Al-Azhar</div>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>

