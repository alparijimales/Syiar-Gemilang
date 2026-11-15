<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title ?? 'Tanda Bukti Pendaftaran PPDB'); ?></title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/img/favicon-32x32.png'); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/img/favicon-16x16.png'); ?>">
  <link rel="shortcut icon" href="<?= base_url('favicon.ico'); ?>">
  <link rel="apple-touch-icon" href="<?= base_url('assets/img/apple-touch-icon.png'); ?>">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            blue: '#007ACE',
            deep: '#04243F',
            ink: '#0C1F31',
            muted: '#5A6D7F',
            line: '#DDE6EE',
            soft: '#EEF5FB',
            pencil: '#FFD23F',
          },
          fontFamily: {
            disp: ['"Bricolage Grotesque"', 'system-ui', 'sans-serif'],
            sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <style>
    @media print {
      body {
        background: white !important;
        color: black !important;
      }
      .no-print {
        display: none !important;
      }
      .print-card {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
      }
    }
  </style>
</head>
<body class="bg-[#F8FAFC] text-ink font-sans leading-[1.6] antialiased min-h-screen flex flex-col justify-between">

  <!-- Top Simple Nav (No Print) -->
  <header class="bg-blue text-white py-3.5 px-6 shadow-sm no-print">
    <div class="max-w-[800px] mx-auto flex items-center justify-between">
      <a href="<?= base_url('ppdb'); ?>" class="flex items-center gap-2.5 font-disp font-bold text-lg text-white">
        <img src="<?= base_url('assets/img/rgi.png'); ?>" alt="Logo Syiar Gemilang" class="w-7 h-7 rounded-lg object-contain"> Syiar Gemilang PPDB
      </a>
      <div class="flex items-center gap-3 text-xs">
        <a href="<?= base_url('/'); ?>" class="text-white/80 hover:text-white transition">Beranda</a>
        <a href="<?= base_url('ppdb/cek-status'); ?>" class="text-white/80 hover:text-white transition">Cek Status</a>
      </div>
    </div>
  </header>

  <!-- Main Content Card -->
  <main class="flex-1 max-w-[760px] w-full mx-auto p-4 sm:p-6 my-4 sm:my-8">
    
    <!-- Success Alert Header (No Print) -->
    <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3.5 no-print shadow-xs">
      <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shrink-0 mt-0.5">
        <i class="fa-solid fa-circle-check"></i>
      </div>
      <div>
        <h2 class="font-disp font-bold text-base sm:text-lg text-emerald-950">Pendaftaran Online Berhasil Dicatat!</h2>
        <p class="text-xs sm:text-sm text-emerald-800/90 mt-0.5">
          Simpan atau cetak bukti pendaftaran ini. Panitia akan memverifikasi berkas Anda dan menghubungi melalui WhatsApp.
        </p>
      </div>
    </div>

    <!-- Official Printable Registration Certificate Card -->
    <div class="print-card bg-white rounded-3xl border border-line shadow-xl overflow-hidden">
      
      <!-- Card Header Accent -->
      <div class="h-3 bg-gradient-to-r from-deep via-blue to-pencil"></div>

      <!-- Card Top Brand & Title -->
      <div class="p-6 sm:p-8 pb-6 border-b border-line flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2.5 mb-1">
            <img src="<?= base_url('assets/img/rgi.png'); ?>" alt="Logo RGI" class="w-10 h-10 rounded-xl object-contain shadow-xs shrink-0">
            <div>
              <h1 class="font-disp font-extrabold text-xl text-deep leading-tight">Rumah Gemilang Indonesia</h1>
              <span class="text-3xs text-muted block uppercase tracking-wider font-semibold">Lembaga Pelatihan &amp; Pemberdayaan Diklat Vokasi</span>
            </div>
          </div>
          <p class="text-xs text-muted mt-2">Tanda Bukti Registrasi Calon Santri Baru Angkatan 36 Tahun 2026</p>
        </div>

        <!-- Registration Number Badge -->
        <div class="text-left sm:text-right bg-soft sm:bg-transparent p-3 sm:p-0 rounded-xl border border-line sm:border-0">
          <span class="text-3xs text-muted font-bold uppercase tracking-wider block">Nomor Registrasi PPDB</span>
          <span class="font-disp font-extrabold text-2xl text-blue tracking-tight block mt-0.5 select-all">
            <?= esc($pendaftar['no_pendaftaran']); ?>
          </span>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-2xs font-semibold tracking-tight bg-amber-500/10 text-amber-900 border border-amber-500/30 mt-1 shadow-2xs">
            <i class="fa-regular fa-clock text-amber-600 text-[10.5px]"></i> <?= esc($pendaftar['status']); ?>
          </span>
        </div>
      </div>

      <!-- Card Body: Biodata Santri -->
      <div class="p-6 sm:p-8 space-y-5 text-xs sm:text-sm">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          
          <div class="p-3.5 rounded-xl bg-soft/50 border border-line">
            <span class="text-3xs text-muted uppercase font-bold block mb-0.5">Nama Lengkap Santri</span>
            <span class="font-bold text-deep text-base block"><?= esc($pendaftar['nama_lengkap']); ?></span>
            <span class="text-3xs text-muted">NIK: <?= esc($pendaftar['nik'] ?: '-'); ?> • <?= $pendaftar['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
          </div>

          <div class="p-3.5 rounded-xl bg-soft/50 border border-line">
            <span class="text-3xs text-muted uppercase font-bold block mb-0.5">Nomor WhatsApp Aktif</span>
            <span class="font-bold text-deep text-base block"><?= esc($pendaftar['whatsapp']); ?></span>
            <span class="text-3xs text-muted"><?= esc($pendaftar['email'] ?: 'Email tidak diisi'); ?></span>
          </div>

          <div class="p-3.5 rounded-xl bg-soft/50 border border-line">
            <span class="text-3xs text-muted uppercase font-bold block mb-0.5">Jurusan Pilihan Utama</span>
            <span class="font-bold text-blue text-sm block"><?= esc($pendaftar['jurusan_pilihan1']); ?></span>
            <span class="text-3xs text-muted">Cadangan: <?= esc($pendaftar['jurusan_pilihan2'] ?: 'Tidak memilih'); ?></span>
          </div>

          <div class="p-3.5 rounded-xl bg-soft/50 border border-line">
            <span class="text-3xs text-muted uppercase font-bold block mb-0.5">Pilihan Kampus Diklat</span>
            <span class="font-bold text-deep text-sm block"><?= esc($pendaftar['cabang_kampus']); ?></span>
            <span class="text-3xs text-muted">Pendidikan: <?= esc($pendaftar['pendidikan_terakhir']); ?> (<?= esc($pendaftar['asal_sekolah'] ?: '-'); ?>)</span>
          </div>

        </div>

        <!-- Alamat & Domisili -->
        <div class="p-3.5 rounded-xl bg-soft/50 border border-line">
          <span class="text-3xs text-muted uppercase font-bold block mb-0.5">Alamat Domisili</span>
          <span class="text-deep font-medium block"><?= esc($pendaftar['alamat']); ?><?= !empty($pendaftar['kota']) ? ', ' . esc($pendaftar['kota']) : ''; ?></span>
        </div>

        <!-- Instruksi Selanjutnya -->
        <div class="p-4 rounded-2xl bg-blue/10 border border-blue/20 text-deep space-y-2">
          <h4 class="font-disp font-bold text-sm flex items-center gap-1.5 text-blue">
            <i class="fa-solid fa-list-check"></i> Langkah Selanjutnya Bagi Calon Santri:
          </h4>
          <ol class="list-decimal list-inside space-y-1 text-xs text-muted pl-1">
            <li>Simpan bukti pendaftaran ini (screenshot atau klik <strong>Cetak / Unduh Bukti</strong> di bawah).</li>
            <li>Klik tombol <strong>Konfirmasi ke Panitia via WhatsApp</strong> untuk mengirim nomor registrasi Anda.</li>
            <li>Siapkan berkas scan KTP, Ijazah terakhir, dan Kartu Keluarga (KK).</li>
            <li>Pantau perkembangan seleksi berkas Anda melalui menu <strong>Cek Status PPDB</strong> secara berkala.</li>
          </ol>
        </div>

      </div>

      <!-- Card Footer Actions (No Print) -->
      <div class="p-6 bg-slate-50 border-t border-line flex flex-wrap items-center justify-between gap-3 no-print">
        <div class="flex items-center gap-2">
          <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-deep font-semibold text-xs border border-line flex items-center gap-1.5 transition cursor-pointer shadow-xs">
            <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
          </button>
          <a href="<?= base_url('ppdb/cek-status?keyword=' . urlencode($pendaftar['no_pendaftaran'])); ?>" class="px-4 py-2.5 rounded-xl bg-soft hover:bg-blue/15 text-blue font-semibold text-xs border border-blue/20 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-magnifying-glass"></i> Cek Status
          </a>
        </div>

        <?php 
          $waMsg = urlencode("Assalamu'alaikum Panitia PPDB RGI, saya telah mendaftar online dengan Nomor Registrasi: {$pendaftar['no_pendaftaran']}, Nama: {$pendaftar['nama_lengkap']}, Jurusan: {$pendaftar['jurusan_pilihan1']}. Mohon info tahapan seleksi selanjutnya. Terima kasih.");
        ?>
        <a href="https://wa.me/6281234567890?text=<?= $waMsg; ?>" target="_blank" rel="noopener" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition">
          <i class="fa-brands fa-whatsapp text-sm"></i> Konfirmasi ke Panitia WhatsApp
        </a>
      </div>

    </div>

    <!-- Back Link (No Print) -->
    <div class="text-center mt-6 no-print">
      <a href="<?= base_url('ppdb'); ?>" class="text-xs text-muted hover:text-deep transition inline-flex items-center gap-1">
        <i class="fa-solid fa-arrow-left text-3xs"></i> Kembali ke Halaman Utama PPDB
      </a>
    </div>

  </main>

  <!-- Footer Simple -->
  <footer class="text-center py-4 text-3xs text-muted no-print border-t border-line">
    &copy; 2026 Lembaga Pendidikan Rumah Gemilang Indonesia. Sistem Informasi Akademik Syiar Gemilang.
  </footer>

</body>
</html>
