<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title ?? 'Cek Status Seleksi PPDB – Syiar Gemilang'); ?></title>
  
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
</head>
<body class="bg-[#F8FAFC] text-ink font-sans leading-[1.6] antialiased min-h-screen flex flex-col justify-between">

  <!-- Header Simple -->
  <header class="bg-blue text-white py-3.5 px-6 shadow-sm">
    <div class="max-w-[800px] mx-auto flex items-center justify-between">
      <a href="<?= base_url('ppdb'); ?>" class="flex items-center gap-2.5 font-disp font-bold text-lg text-white">
        <img src="<?= base_url('assets/img/rgi.png'); ?>" alt="Logo Syiar Gemilang" class="w-7 h-7 rounded-lg object-contain"> Syiar Gemilang PPDB
      </a>
      <div class="flex items-center gap-3 text-xs">
        <a href="<?= base_url('/'); ?>" class="text-white/80 hover:text-white transition">Beranda</a>
        <a href="<?= base_url('ppdb#form-daftar'); ?>" class="text-pencil font-semibold hover:underline">Daftar Sekarang</a>
      </div>
    </div>
  </header>

  <main class="flex-1 max-w-[760px] w-full mx-auto p-4 sm:p-6 my-4 sm:my-8">
    
    <div class="text-center mb-8">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue/10 text-blue font-bold text-xs uppercase tracking-wider mb-2">
        <i class="fa-solid fa-magnifying-glass"></i> Cek Hasil Seleksi
      </span>
      <h1 class="font-disp font-extrabold text-2xl sm:text-3xl text-deep tracking-tight">
        Cek Status Pendaftaran Calon Santri
      </h1>
      <p class="text-xs sm:text-sm text-muted max-w-[50ch] mx-auto mt-1">
        Masukkan Nomor Registrasi PPDB (contoh: <code>PPDB-36-001</code>) atau Nomor WhatsApp yang didaftarkan.
      </p>
    </div>

    <!-- Search Form Card -->
    <div class="bg-white rounded-3xl border border-line p-6 shadow-md mb-6">
      <form action="<?= base_url('ppdb/cek-status'); ?>" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <i class="fa-solid fa-id-card absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-xs"></i>
          <input 
            type="text" 
            name="keyword" 
            value="<?= esc($keyword ?? ''); ?>" 
            required 
            placeholder="Ketik Nomor Registrasi / No. WhatsApp / NIK..." 
            class="w-full h-12 pl-10 pr-4 rounded-xl border border-line text-sm focus:outline-none focus:border-blue focus:ring-2 focus:ring-blue/15 transition"
          >
        </div>
        <button type="submit" class="h-12 px-6 rounded-xl bg-blue hover:bg-[#0067AE] text-white font-bold text-sm transition flex items-center justify-center gap-2 shrink-0 cursor-pointer shadow-xs">
          <i class="fa-solid fa-magnifying-glass text-xs"></i>
          <span>Periksa Status</span>
        </button>
      </form>
    </div>

    <!-- Hasil Pencarian -->
    <?php if ($searched) : ?>
      <?php if ($pendaftar) : ?>
        <!-- Data Ditemukan -->
        <div class="bg-white rounded-3xl border border-line shadow-xl overflow-hidden animate-fadeIn">
          
          <?php 
            $statusConfigs = [
              'Menunggu Seleksi' => ['class' => 'bg-amber-500/10 text-amber-900 border-amber-500/30', 'icon' => 'fa-regular fa-clock text-amber-600'],
              'Lolos Berkas'     => ['class' => 'bg-sky-500/10 text-sky-950 border-sky-500/30', 'icon' => 'fa-regular fa-file-lines text-sky-600'],
              'Lolos Wawancara'  => ['class' => 'bg-purple-500/10 text-purple-950 border-purple-500/30', 'icon' => 'fa-regular fa-comments text-purple-600'],
              'Diterima'         => ['class' => 'bg-emerald-500/10 text-emerald-950 border-emerald-500/30', 'icon' => 'fa-solid fa-check-double text-emerald-600'],
              'Tidak Lolos'      => ['class' => 'bg-slate-500/10 text-slate-700 border-slate-300', 'icon' => 'fa-regular fa-circle-xmark text-slate-500'],
            ];
            $currentCfg = $statusConfigs[$pendaftar['status']] ?? ['class' => 'bg-slate-100 text-slate-800 border-slate-300', 'icon' => 'fa-regular fa-circle-question text-slate-500'];
          ?>

          <div class="p-6 sm:p-7 border-b border-line flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-soft/50 to-white">
            <div>
              <span class="text-3xs font-extrabold uppercase tracking-wider text-muted block">Data Ditemukan</span>
              <h3 class="font-disp font-extrabold text-xl text-deep"><?= esc($pendaftar['nama_lengkap']); ?></h3>
              <span class="text-xs text-blue font-bold"><?= esc($pendaftar['no_pendaftaran']); ?></span>
            </div>
            <div class="sm:text-right">
              <span class="text-3xs text-muted block mb-1">Status Seleksi Terkini:</span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded text-xs font-semibold tracking-tight border shadow-2xs <?= $currentCfg['class']; ?>">
                <i class="<?= $currentCfg['icon']; ?>"></i> <?= esc($pendaftar['status']); ?>
              </span>
            </div>
          </div>

          <div class="p-6 sm:p-7 space-y-4 text-xs sm:text-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="p-3.5 rounded-xl bg-soft/50 border border-line">
                <span class="text-3xs text-muted uppercase font-bold block mb-0.5">Jurusan Pilihan</span>
                <span class="font-bold text-deep block"><?= esc($pendaftar['jurusan_pilihan1']); ?></span>
                <span class="text-3xs text-muted">Cadangan: <?= esc($pendaftar['jurusan_pilihan2'] ?: '-'); ?></span>
              </div>
              <div class="p-3.5 rounded-xl bg-soft/50 border border-line">
                <span class="text-3xs text-muted uppercase font-bold block mb-0.5">Kampus Diklat</span>
                <span class="font-bold text-deep block"><?= esc($pendaftar['cabang_kampus']); ?></span>
                <span class="text-3xs text-muted">WhatsApp: <?= esc($pendaftar['whatsapp']); ?></span>
              </div>
            </div>

            <!-- Petunjuk Sesuai Status -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-line space-y-1.5 text-xs">
              <strong class="font-bold text-deep block">Keterangan &amp; Catatan Panitia:</strong>
              <?php if ($pendaftar['status'] === 'Menunggu Seleksi') : ?>
                <p class="text-muted">Berkas pendaftaran Anda sedang dalam antrean verifikasi panitia. Pastikan nomor WhatsApp aktif untuk menerima jadwal seleksi wawancara.</p>
              <?php elseif ($pendaftar['status'] === 'Lolos Berkas') : ?>
                <p class="text-emerald-700 font-medium">Selamat! Berkas Anda dinyatakan memenuhi syarat administrasi. Jadwal wawancara online/offline akan dikirimkan melalui WhatsApp.</p>
              <?php elseif ($pendaftar['status'] === 'Lolos Wawancara') : ?>
                <p class="text-purple-700 font-medium">Alhamdulillah! Anda telah menyelesaikan tahap wawancara dan direkomendasikan untuk penetapan santri penerima beasiswa penuh.</p>
              <?php elseif ($pendaftar['status'] === 'Diterima') : ?>
                <p class="text-emerald-800 font-bold">Barakallahu fiikum! Anda RESMI DITERIMA sebagai Santri Rumah Gemilang Indonesia Angkatan 36 Beasiswa Penuh 100%. Silakan hubungi panitia untuk informasi perlengkapan masuk asrama.</p>
              <?php else : ?>
                <p class="text-red-700">Mohon maaf, saat ini Anda belum dapat bergabung pada Angkatan 36 karena keterbatasan kuota. Tetap semangat!</p>
              <?php endif; ?>
            </div>

          </div>

          <div class="p-4 sm:p-5 bg-slate-50 border-t border-line flex items-center justify-between gap-3 text-xs">
            <a href="<?= base_url('ppdb/sukses/' . $pendaftar['no_pendaftaran']); ?>" class="text-blue font-bold hover:underline flex items-center gap-1">
              <i class="fa-solid fa-file-invoice"></i> Buka Tanda Bukti Registrasi
            </a>
            <a href="https://wa.me/6281234567890?text=<?= urlencode("Halo Panitia PPDB RGI, saya ingin menanyakan status pendaftaran nomor {$pendaftar['no_pendaftaran']}"); ?>" target="_blank" rel="noopener" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition flex items-center gap-1.5">
              <i class="fa-brands fa-whatsapp"></i> Chat Panitia
            </a>
          </div>

        </div>
      <?php else : ?>
        <!-- Data Tidak Ditemukan -->
        <div class="bg-white rounded-3xl border border-line p-8 text-center">
          <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 grid place-items-center text-xl mx-auto mb-3">
            <i class="fa-solid fa-triangle-exclamation"></i>
          </div>
          <h3 class="font-disp font-bold text-lg text-deep mb-1">Data Pendaftaran Tidak Ditemukan</h3>
          <p class="text-xs text-muted max-w-[42ch] mx-auto mb-4">
            Tidak ditemukan data dengan kata kunci "<strong><?= esc($keyword); ?></strong>". Pastikan nomor registrasi atau nomor WhatsApp yang Anda masukkan sudah benar.
          </p>
          <a href="<?= base_url('ppdb#form-daftar'); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-pencil text-deep font-bold text-xs shadow-xs hover:bg-[#FFE066] transition">
            <i class="fa-solid fa-user-plus"></i> Isi Formulir Pendaftaran Baru
          </a>
        </div>
      <?php endif; ?>
    <?php endif; ?>

  </main>

  <!-- Footer Simple -->
  <footer class="text-center py-4 text-3xs text-muted border-t border-line">
    &copy; 2026 Lembaga Pendidikan Rumah Gemilang Indonesia. Sistem Informasi Akademik Syiar Gemilang.
  </footer>

</body>
</html>
