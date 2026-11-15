<?php
/**
 * ============================================================================
 * Komponen Top Bar Modular - Syiar Gemilang
 * ============================================================================
 * Komponen header navigasi atas yang konsisten dan modular untuk seluruh halaman.
 *
 * Parameter / Variabel yang Didukung:
 * - $page_title (string) : Judul halaman (fallback ke $title, lalu 'Dashboard')
 * - $user (array|object) : Data pengguna kustom:
 *     - 'name' / 'nama'  : Nama user (fallback: session('nama') / 'Administrator')
 *     - 'role'           : Role/subtitle (fallback: session('role') / 'Admin / Instruktur')
 *     - 'avatar'/'foto'  : URL gambar profil (opsional)
 *     - 'initial'        : Inisial huruf profil (fallback: huruf pertama nama)
 * - $portal_badge (string) : Badge teks portal (default: 'Syiar Gemilang • Portal Akademik')
 * - $tahun_ajaran (string) : Teks tahun ajaran (default: '2026/2027 (Ganjil)')
 */

// Pastikan fungsi esc() tersedia jika di luar environment CodeIgniter
if (!function_exists('esc')) {
    function esc($data) {
        return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
    }
}

// 1. Judul Halaman Dinamis
$topbar_title = $page_title ?? $title ?? 'Dashboard';

// 2. Data Pengguna Dinamis
$userData = $user ?? [];
if (is_object($userData)) {
    $userData = (array)$userData;
}

// Nama User
$topbar_user_name = $userData['name']
    ?? $userData['nama']
    ?? (function_exists('session') && session()->has('nama') ? session()->get('nama') : null)
    ?? (function_exists('session') && session()->has('username') ? session()->get('username') : null)
    ?? 'Administrator';

// Role / Subtitle
$topbar_user_role = $userData['role'] ?? $userData['role_label'] ?? null;
if (empty($topbar_user_role) && function_exists('session') && session()->has('role')) {
    $sessionRole = session()->get('role');
    $roleMap = [
        'admin'       => 'Admin / Instruktur',
        'instruktur'  => 'Instruktur',
        'siswa'       => 'Santri',
        'wali_santri' => 'Wali Santri'
    ];
    $topbar_user_role = $roleMap[strtolower($sessionRole)] ?? ucfirst($sessionRole);
}
if (empty($topbar_user_role)) {
    $topbar_user_role = 'Admin / Instruktur';
}

// Avatar Gambar (URL / Path) - Resolusi Dinamis Sesuai Peran
$topbar_user_avatar = $userData['avatar']
    ?? $userData['photo']
    ?? $userData['foto']
    ?? (function_exists('session') && session()->has('avatar') ? session()->get('avatar') : null);

if (empty($topbar_user_avatar) && function_exists('session') && session()->has('user_id')) {
    $db = \Config\Database::connect();
    $sessUserId = (int)session()->get('user_id');
    $sessRole = (string)session()->get('role');
    $sessSantriId = session()->get('santri_id');

    // Jika santri atau wali santri: ambil foto santri dari tabel nilai_santri
    if (in_array($sessRole, ['siswa', 'wali_santri']) && !empty($sessSantriId)) {
        $st = $db->table('nilai_santri')->select('foto')->where('id', $sessSantriId)->get()->getRowArray();
        if ($st && !empty($st['foto']) && file_exists(FCPATH . 'uploads/santri/' . $st['foto'])) {
            $topbar_user_avatar = base_url('uploads/santri/' . $st['foto']);
        }
    }

    // Jika instruktur atau admin atau custom avatar
    if (empty($topbar_user_avatar)) {
        $u = $db->table('users')->select('avatar')->where('id', $sessUserId)->get()->getRowArray();
        if ($u && !empty($u['avatar']) && file_exists(FCPATH . 'uploads/avatars/' . $u['avatar'])) {
            $topbar_user_avatar = base_url('uploads/avatars/' . $u['avatar']);
        }
    }
}

// Avatar Inisial Huruf
$topbar_user_initial = $userData['initial'] ?? null;
if (empty($topbar_user_initial)) {
    $cleanName = trim($topbar_user_name);
    $topbar_user_initial = !empty($cleanName) ? strtoupper(mb_substr($cleanName, 0, 1, 'UTF-8')) : 'A';
}

// Format Role Akademik Elegan
$academicRoleBadge = [
    'admin'            => '<span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-[#0066CC]"><i class="fa-solid fa-shield-halved text-[9.5px]"></i> Admin Akademik</span>',
    'instruktur'       => '<span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-emerald-700"><i class="fa-solid fa-chalkboard-user text-[9.5px]"></i> Instruktur Kejuruan</span>',
    'pendamping_agama' => '<span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-teal-700"><i class="fa-solid fa-book-quran text-[9.5px]"></i> Pendamping Agama</span>',
    'siswa'            => '<span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-sky-700"><i class="fa-solid fa-user-graduate text-[9.5px]"></i> Santri Diklat</span>',
    'wali_santri'      => '<span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-indigo-700"><i class="fa-solid fa-hands-holding-child text-[9.5px]"></i> Wali Santri</span>'
];
$rawRole = function_exists('session') && session()->has('role') ? strtolower((string)session()->get('role')) : 'admin';
$academicRoleHtml = $academicRoleBadge[$rawRole] ?? '<span class="text-[10.5px] font-semibold text-slate-500">' . esc($topbar_user_role) . '</span>';

// 3. Badge Dinamis
$topbar_badge = $portal_badge ?? 'Syiar Gemilang • Portal Akademik';
$topbar_badge_icon = $portal_badge_icon ?? 'fa-solid fa-graduation-cap';
$topbar_tahun_ajaran = $tahun_ajaran ?? '2026/2027 (Ganjil)';
?>

<!-- ===================== TOP NAVBAR ===================== -->
<header class="relative z-20 h-16 shrink-0 flex items-center justify-between gap-3 border-b border-slate-200/90 bg-white/95 backdrop-blur-md px-4 sm:px-6">
    <div class="flex min-w-0 items-center gap-2.5 sm:gap-3.5">
        <!-- Tombol Hamburger Mobile -->
        <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden p-2 -ml-1 text-slate-600 hover:text-[#007ACE] hover:bg-slate-100 rounded-xl transition cursor-pointer" aria-label="Buka Menu Navigasi">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <!-- Brand Icon & Judul Halaman Portal Akademik -->
        <div class="flex items-center gap-2.5 min-w-0">
            <img src="<?= base_url('assets/img/rgi.png'); ?>" alt="Logo Syiar Gemilang" class="w-8 h-8 rounded-xl object-contain shadow-xs shrink-0" title="Syiar Gemilang Portal Akademik">
            <div class="leading-tight min-w-0">
                <h1 class="sg-display truncate text-base sm:text-lg font-extrabold tracking-tight text-[#04243F]"><?= esc($topbar_title); ?></h1>
                <p class="text-3xs text-slate-400 font-semibold tracking-wide hidden sm:block truncate">Syiar Gemilang • Portal Akademik</p>
            </div>
        </div>
    </div>

    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
        <?php if (!empty($topbar_extra_right)): ?>
            <?= $topbar_extra_right; ?>
        <?php endif; ?>

        <!-- Badge Tahun Ajaran (Academic Term Badge) -->
        <div class="hidden xl:inline-flex items-center divide-x divide-slate-200 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-2xs">
            <span class="px-2.5 py-1 text-slate-700 font-bold flex items-center gap-1.5"><i class="fa-solid fa-calendar-check text-[#007ACE] text-[11px]"></i> TA 2026/2027</span>
            <span class="px-2.5 py-1 text-slate-500 text-2xs">Semester Ganjil</span>
        </div>

        <!-- Ikon & Dropdown Notifikasi -->
        <div class="relative">
            <button type="button" id="sgNotifBtn"
                    class="inline-flex h-9 w-9 sm:h-10 sm:w-10 cursor-pointer items-center justify-center rounded-xl text-slate-500 transition-colors duration-200 hover:bg-[#007ACE]/10 hover:text-[#007ACE] active:text-[#04243F]"
                    aria-label="Notifikasi" aria-haspopup="true" aria-expanded="false" aria-controls="sgNotifPanel">
                <i class="fa-regular fa-bell text-base sm:text-lg" aria-hidden="true"></i>
            </button>
            <div id="sgNotifPanel" role="region" aria-label="Daftar notifikasi"
                 class="hidden absolute right-0 top-full z-30 mt-2 w-[min(20rem,calc(100vw-2rem))] rounded-2xl border border-slate-200 bg-white p-4 shadow-[0_16px_36px_-8px_rgba(4,36,63,0.18)]">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <p class="sg-display text-sm font-bold text-slate-800">Pemberitahuan</p>
                    <span class="text-3xs font-semibold text-slate-400 uppercase tracking-wider">Terbaru</span>
                </div>
                <div class="flex flex-col items-center px-2 py-7 text-center">
                    <span class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#007ACE]/10 text-[#007ACE]">
                        <i class="fa-regular fa-bell-slash text-base" aria-hidden="true"></i>
                    </span>
                    <p class="text-sm font-bold text-slate-800">Belum Ada Notifikasi Baru</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">Pemberitahuan presensi, nilai, dan agenda diklat akan muncul di sini.</p>
                </div>
            </div>
        </div>

        <!-- Profil Pengguna & Dropdown Menu (Academic Credential Card) -->
        <div class="relative pl-1">
            <button type="button" id="sgUserMenuBtn" onclick="toggleUserDropdownMenu()" 
                    class="group relative flex items-center gap-2.5 py-1 px-2 sm:pr-3 rounded-lg bg-white hover:bg-slate-50 border border-slate-200/90 border-l-[3px] border-l-[#007ACE] shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer text-left"
                    aria-label="Menu Profil Pengguna">
                <!-- Avatar / Foto Profil Resmi (Academic Pass Frame) -->
                <div class="relative w-9 h-10 rounded-md overflow-hidden ring-1 ring-[#04243F]/20 shadow-xs shrink-0 bg-gradient-to-b from-[#007ACE] to-[#04243F] flex items-center justify-center text-white" title="<?= esc($topbar_user_name); ?>">
                    <?php if (!empty($topbar_user_avatar)): ?>
                        <img src="<?= esc($topbar_user_avatar); ?>" alt="<?= esc($topbar_user_name); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="font-disp font-black text-sm tracking-tight text-[#FFD23F]"><?= esc($topbar_user_initial); ?></span>
                    <?php endif; ?>
                </div>

                <div class="hidden min-w-0 leading-tight sm:block pr-1">
                    <p class="truncate text-xs sm:text-[13px] font-bold text-[#04243F] group-hover:text-[#007ACE] transition-colors max-w-[140px] tracking-tight">
                        <?= esc($topbar_user_name); ?>
                    </p>
                    <div class="mt-0.5">
                        <?= $academicRoleHtml; ?>
                    </div>
                </div>

                <!-- Academic Chevron Indicator -->
                <i class="fa-solid fa-chevron-down text-3xs text-slate-400 group-hover:text-[#007ACE] transition-transform duration-200 group-hover:translate-y-0.5 hidden sm:block"></i>
            </button>

            <!-- Dropdown Menu Profil & Pengaturan Akun -->
            <div id="sgUserDropdownMenu" 
                 class="hidden absolute right-0 top-full z-30 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-2.5 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                <div class="px-3 py-2.5 border-b border-slate-100 mb-1.5 bg-slate-50/80 rounded-lg flex items-center gap-2.5">
                    <div class="w-9 h-10 rounded-md overflow-hidden shrink-0 ring-1 ring-[#04243F]/20 bg-gradient-to-b from-[#007ACE] to-[#04243F] flex items-center justify-center text-white">
                        <?php if (!empty($topbar_user_avatar)): ?>
                            <img src="<?= esc($topbar_user_avatar); ?>" alt="<?= esc($topbar_user_name); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="font-bold text-xs text-[#FFD23F]"><?= esc($topbar_user_initial); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 leading-tight">
                        <p class="text-xs font-bold text-slate-900 truncate"><?= esc($topbar_user_name); ?></p>
                        <p class="text-3xs text-slate-500 truncate mt-0.5">@<?= esc(function_exists('session') && session()->has('username') ? session()->get('username') : 'user'); ?></p>
                        <div class="mt-1">
                            <?= $academicRoleHtml; ?>
                        </div>
                    </div>
                </div>

                <a href="<?= base_url('profil'); ?>" 
                   class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-[#007ACE] hover:bg-[#007ACE]/10 rounded-lg transition">
                    <i class="fa-solid fa-user-pen text-[#007ACE] text-xs w-4 text-center"></i> Pengaturan Profil & Foto
                </a>
                <button type="button" onclick="openGlobalPasswordModal()" 
                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-[#007ACE] hover:bg-[#007ACE]/10 rounded-lg transition text-left cursor-pointer">
                    <i class="fa-solid fa-key text-slate-500 text-xs w-4 text-center"></i> Ganti Kata Sandi
                </button>
                <div class="border-t border-slate-100 my-1"></div>
                <a href="<?= base_url('logout'); ?>" 
                   class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs w-4 text-center"></i> Keluar
                </a>
            </div>
        </div>
    </div>
</header>

<!-- ===================== MODAL GANTI PASSWORD GLOBAL ===================== -->
<div id="modalGlobalGantiPassword" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeGlobalPasswordModal()"></div>

    <!-- Modal Card Content -->
    <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all z-10 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 bg-[#0066CC] text-white flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-key"></i> Ganti Kata Sandi Login
            </h3>
            <button type="button" onclick="closeGlobalPasswordModal()" class="text-white/80 hover:text-white cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('ganti-password'); ?>" method="post" class="p-6 space-y-4" autocomplete="off">
            <?= csrf_field(); ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                <div class="relative">
                    <input type="password" name="current_password" id="mCurPwd" required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-medium pr-8"
                           placeholder="Masukkan kata sandi lama">
                    <button type="button" onclick="togglePasswordVisibility('mCurPwd', 'mCurEye')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400">
                        <i id="mCurEye" class="fa-regular fa-eye text-2xs"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru (Min. 6 Karakter)</label>
                <div class="relative">
                    <input type="password" name="new_password" id="mNewPwd" required minlength="6"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-medium pr-8"
                           placeholder="Ketik kata sandi baru">
                    <button type="button" onclick="togglePasswordVisibility('mNewPwd', 'mNewEye')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400">
                        <i id="mNewEye" class="fa-regular fa-eye text-2xs"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                <div class="relative">
                    <input type="password" name="confirm_password" id="mConfPwd" required minlength="6"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-medium pr-8"
                           placeholder="Ketik ulang kata sandi baru">
                    <button type="button" onclick="togglePasswordVisibility('mConfPwd', 'mConfEye')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400">
                        <i id="mConfEye" class="fa-regular fa-eye text-2xs"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeGlobalPasswordModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#0066CC] hover:bg-[#0052A3] text-white text-xs font-bold shadow-sm shadow-[#0066CC]/20 cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Sandi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script Popover Notifikasi -->
<script>
(function () {
    var btn = document.getElementById('sgNotifBtn');
    var panel = document.getElementById('sgNotifPanel');
    if (!btn || !panel || btn.dataset.sgInit === 'true') return;
    btn.dataset.sgInit = 'true';

    function setOpen(open) {
        panel.classList.toggle('hidden', !open);
        btn.setAttribute('aria-expanded', String(open));
    }
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(panel.classList.contains('hidden'));
    });
    document.addEventListener('click', function (e) {
        if (!panel.contains(e.target)) setOpen(false);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.classList.contains('hidden')) {
            setOpen(false);
            btn.focus();
        }
    });
})();

function toggleUserDropdownMenu() {
    var menu = document.getElementById('sgUserDropdownMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

document.addEventListener('click', function(e) {
    var menu = document.getElementById('sgUserDropdownMenu');
    var btn = document.getElementById('sgUserMenuBtn');
    if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

function openGlobalPasswordModal() {
    var modal = document.getElementById('modalGlobalGantiPassword');
    var menu = document.getElementById('sgUserDropdownMenu');
    if (menu) menu.classList.add('hidden');
    if (modal) modal.classList.remove('hidden');
}

function closeGlobalPasswordModal() {
    var modal = document.getElementById('modalGlobalGantiPassword');
    if (modal) modal.classList.add('hidden');
}

if (typeof togglePasswordVisibility === 'undefined') {
    window.togglePasswordVisibility = function(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    };
}
</script>
