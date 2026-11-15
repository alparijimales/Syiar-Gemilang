<?php
$currentRole = session()->get('role') ?? 'admin';
switch ($currentRole) {
    case 'instruktur':
        require_once APPPATH . 'Views/instruktur/navbar.php';
        $dashUrl = base_url('instruktur');
        $portalLabel = 'Portal Instruktur';
        break;
    case 'siswa':
        require_once APPPATH . 'Views/siswa/navbar.php';
        $dashUrl = base_url('siswa');
        $portalLabel = 'Portal Santri';
        break;
    case 'wali_santri':
        require_once APPPATH . 'Views/wali/navbar.php';
        $dashUrl = base_url('wali');
        $portalLabel = 'Portal Wali Santri';
        break;
    case 'admin':
    default:
        require_once APPPATH . 'Views/admin/navbar.php';
        $dashUrl = base_url('admin');
        $portalLabel = 'Portal Admin';
        break;
}

$roleTitles = [
    'admin'       => 'Administrator Sistem Akademik',
    'instruktur'  => 'Instruktur / Pengajar Kejuruan',
    'siswa'       => 'Santri Diklat Vokasi',
    'wali_santri' => 'Wali Santri'
];
$displayRole = $roleTitles[$user['role']] ?? ucfirst($user['role']);
?>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">

    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Pengaturan Profil & Akun';
    require_once APPPATH . 'Views/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => $portalLabel, 'url' => $dashUrl],
                ['label' => 'Pengaturan Profil & Akun']
            ],
            'badge' => 'Manajemen Identitas & Kredensial',
            'title' => 'Pengaturan Profil Pengguna',
            'subtitle' => 'Kelola informasi identitas nama lengkap, username login, foto profil resmi, serta keamanan kata sandi akun Anda.',
            'stats' => [
                ['icon' => 'fa-solid fa-id-card', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => esc($user['username']), 'label' => 'Username Akun'],
                ['icon' => 'fa-solid fa-user-shield', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => strtoupper($user['role']), 'label' => 'Peran Sistem'],
                ['icon' => 'fa-solid fa-clock-rotate-left', 'bg' => 'bg-amber-400/20 text-amber-200', 'value' => 'Aktif', 'label' => 'Status Akun'],
            ]
        ];
        require_once APPPATH . 'Views/components/hero_header.php';
        ?>

        <div class="p-4 sm:p-6">
            <div class="max-w-3xl mx-auto space-y-6">

                <!-- Alert Feedback Flash Message -->
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-3 shadow-xs animate-in fade-in">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Pembaruan Berhasil!</h4>
                            <p class="mt-0.5 leading-relaxed"><?= esc(session()->getFlashdata('success')); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 shadow-xs animate-in fade-in">
                        <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Gagal Menyimpan Perubahan</h4>
                            <p class="mt-0.5 leading-relaxed"><?= esc(session()->getFlashdata('error')); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Profil & Akun -->
                <form action="<?= base_url('profil'); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <?= csrf_field(); ?>

                    <!-- Kartu Ringkasan & Upload Foto Profil -->
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-xs">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-camera text-[#0066CC]"></i> Foto Profil Pengguna
                        </h3>

                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                            <!-- Avatar Preview -->
                            <div class="relative shrink-0">
                                <div id="avatarPreviewContainer" class="w-24 h-24 rounded-2xl overflow-hidden ring-4 ring-[#0066CC]/15 shadow-md bg-gradient-to-br from-[#0066CC] to-[#04243F] flex items-center justify-center text-white">
                                    <?php if (!empty($avatarUrl)): ?>
                                        <img id="avatarPreviewImg" src="<?= esc($avatarUrl); ?>" alt="<?= esc($user['nama']); ?>" class="w-full h-full object-cover">
                                        <span id="avatarFallbackInitial" class="hidden text-3xl font-extrabold text-[#FFD23F] font-mono">
                                            <?= strtoupper(mb_substr($user['nama'], 0, 1, 'UTF-8')); ?>
                                        </span>
                                    <?php else: ?>
                                        <img id="avatarPreviewImg" src="" alt="Preview Foto" class="w-full h-full object-cover hidden">
                                        <span id="avatarFallbackInitial" class="text-3xl font-extrabold text-[#FFD23F] font-mono">
                                            <?= strtoupper(mb_substr($user['nama'], 0, 1, 'UTF-8')); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 ring-2 ring-white" title="Status Aktif"></span>
                            </div>

                            <!-- Petunjuk & Opsi Upload -->
                            <div class="flex-1 text-center sm:text-left min-w-0 space-y-2">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base"><?= esc($user['nama']); ?></h4>
                                    <p class="text-xs text-slate-500">@<?= esc($user['username']); ?> • <span class="text-[#0066CC] font-semibold"><?= esc($displayRole); ?></span></p>
                                </div>

                                <?php if ($user['role'] === 'instruktur' || $user['role'] === 'admin'): ?>
                                    <!-- Instruktur & Admin: Bebas Upload Sendiri -->
                                    <div class="pt-1">
                                        <label for="avatarUploadInput" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-[#0066CC] text-slate-700 hover:text-white font-semibold text-xs border border-slate-200 transition cursor-pointer shadow-2xs">
                                            <i class="fa-solid fa-arrow-up-from-bracket"></i> Pilih Foto Baru
                                        </label>
                                        <input type="file" id="avatarUploadInput" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden" onchange="previewSelectedAvatar(event)">
                                        <p class="text-3xs text-slate-400 mt-1.5">Mendukung format JPG, PNG, atau WebP. Maksimal ukuran 3 MB.</p>
                                    </div>
                                <?php elseif ($user['role'] === 'siswa'): ?>
                                    <!-- Santri: Bisa upload foto sendiri yang langsung tersinkron ke nilai_santri -->
                                    <div class="pt-1">
                                        <label for="avatarUploadInput" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-[#0066CC] text-slate-700 hover:text-white font-semibold text-xs border border-slate-200 transition cursor-pointer shadow-2xs">
                                            <i class="fa-solid fa-arrow-up-from-bracket"></i> Perbarui Foto Santri
                                        </label>
                                        <input type="file" id="avatarUploadInput" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden" onchange="previewSelectedAvatar(event)">
                                        <p class="text-3xs text-slate-400 mt-1.5">Foto resmi santri akan disinkronkan dengan data induk akademik dan kartu santri.</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Wali Santri: Terhubung ke Santri Asuhan -->
                                    <div class="p-2.5 rounded-xl bg-sky-50 border border-sky-100 text-xs text-sky-800">
                                        <i class="fa-solid fa-circle-info text-[#0066CC] mr-1"></i>
                                        Foto profil Anda disinkronkan otomatis dengan foto santri asuhan: <strong><?= esc($santri['nama'] ?? '-'); ?></strong> (NIS: <?= esc($santri['nis'] ?? '-'); ?>).
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($santri)): ?>
                            <!-- Card Detail Santri Terhubung -->
                            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                    <span class="text-3xs font-bold text-slate-400 uppercase block">Nomor Induk Santri</span>
                                    <span class="font-mono font-bold text-slate-800"><?= esc($santri['nis'] ?: '-'); ?></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                    <span class="text-3xs font-bold text-slate-400 uppercase block">Kelas / Kejuruan</span>
                                    <span class="font-semibold text-slate-800"><?= esc($santri['kelas'] ?: '-'); ?></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                    <span class="text-3xs font-bold text-slate-400 uppercase block">Kampus RGI</span>
                                    <span class="font-semibold text-slate-800"><?= esc($santri['cabang'] ?: 'Depok'); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informasi Akun (Nama Lengkap & Username) -->
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                            <i class="fa-solid fa-user-pen text-[#0066CC]"></i> Data Identitas Akun
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nama Lengkap -->
                            <div>
                                <label for="namaLengkapInput" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                        <i class="fa-regular fa-user text-xs"></i>
                                    </span>
                                    <input type="text" id="namaLengkapInput" name="nama" value="<?= esc(old('nama', $user['nama'])); ?>" required
                                           class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0066CC] font-semibold text-slate-800 transition">
                                </div>
                                <p class="text-3xs text-slate-400 mt-1">Nama ini digunakan pada seluruh sistem dan rekap data akademik.</p>
                            </div>

                            <!-- Username Login -->
                            <div>
                                <label for="usernameInput" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Username Login <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                        <i class="fa-solid fa-at text-xs"></i>
                                    </span>
                                    <input type="text" id="usernameInput" name="username" value="<?= esc(old('username', $user['username'])); ?>" required
                                           class="w-full pl-9 pr-3.5 py-2.5 text-xs font-mono font-bold rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0066CC] text-slate-800 transition">
                                </div>
                                <p class="text-3xs text-slate-400 mt-1">Hanya huruf, angka, titik, atau strip tanpa spasi.</p>
                            </div>
                        </div>

                        <!-- Info Role / Hak Akses (Read-Only) -->
                        <div class="pt-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Hak Akses / Peran Sistem</label>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-700"><?= esc($displayRole); ?></span>
                                <span class="px-2.5 py-1 rounded-lg text-3xs font-bold bg-[#0066CC]/10 text-[#0066CC] uppercase">
                                    <?= esc($user['role']); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Keamanan & Ganti Kata Sandi (Opsional) -->
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-lock text-[#0066CC]"></i> Perbarui Kata Sandi (Opsional)
                            </h3>
                            <span class="text-3xs text-slate-400 font-medium">Kosongkan jika tidak ingin mengubah</span>
                        </div>

                        <div class="space-y-4">
                            <!-- Kata Sandi Saat Ini -->
                            <div>
                                <label for="currentPasswordInput" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kata Sandi Saat Ini
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                        <i class="fa-solid fa-key text-xs"></i>
                                    </span>
                                    <input type="password" id="currentPasswordInput" name="current_password" autocomplete="current-password"
                                           placeholder="Masukkan kata sandi saat ini jika ingin mengganti"
                                           class="w-full pl-9 pr-10 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0066CC] text-slate-800 transition">
                                    <button type="button" onclick="togglePassVisibility('currentPasswordInput', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Kata Sandi Baru -->
                                <div>
                                    <label for="newPasswordInput" class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Kata Sandi Baru
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                            <i class="fa-solid fa-shield-halved text-xs"></i>
                                        </span>
                                        <input type="password" id="newPasswordInput" name="new_password" autocomplete="new-password"
                                               placeholder="Minimal 6 karakter"
                                               class="w-full pl-9 pr-10 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0066CC] text-slate-800 transition">
                                        <button type="button" onclick="togglePassVisibility('newPasswordInput', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Konfirmasi Kata Sandi Baru -->
                                <div>
                                    <label for="confirmPasswordInput" class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Konfirmasi Kata Sandi Baru
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                            <i class="fa-solid fa-shield-halved text-xs"></i>
                                        </span>
                                        <input type="password" id="confirmPasswordInput" name="confirm_password" autocomplete="new-password"
                                               placeholder="Ulangi kata sandi baru"
                                               class="w-full pl-9 pr-10 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0066CC] text-slate-800 transition">
                                        <button type="button" onclick="togglePassVisibility('confirmPasswordInput', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Simpan -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="<?= esc($dashUrl); ?>" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                            Kembali ke Dashboard
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#0066CC] hover:bg-[#0052A3] text-white font-bold text-xs shadow-md hover:shadow-lg transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>
</div>

<script>
function previewSelectedAvatar(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatarPreviewImg');
            const fallback = document.getElementById('avatarFallbackInitial');
            if (img) {
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            if (fallback) {
                fallback.classList.add('hidden');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function togglePassVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type === 'text';
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}
</script>
</body>
</html>
