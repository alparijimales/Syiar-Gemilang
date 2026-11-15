<?php require_once 'navbar.php'; ?>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">

    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Manajemen Pengguna';
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => 'Portal Admin', 'url' => base_url('admin')],
                ['label' => 'Manajemen Pengguna']
            ],
            'badge' => 'Otoritas & Hak Akses Multi-Peran E-RGI',
            'title' => 'Direktori & Manajemen User Login',
            'subtitle' => 'Kelola seluruh akun pengguna terdaftar (Admin, Instruktur, Siswa, dan Wali Santri), pantau status aktif, dan tambahkan pengguna baru.',
            'stats' => [
                ['icon' => 'fa-solid fa-users', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => $stats['total'] ?? count($users), 'label' => 'Total Akun'],
                ['icon' => 'fa-solid fa-user-shield', 'bg' => 'bg-blue-400/20 text-blue-200', 'value' => $stats['admin'] ?? 0, 'label' => 'Admin'],
                ['icon' => 'fa-solid fa-chalkboard-user', 'bg' => 'bg-purple-400/20 text-purple-200', 'value' => $stats['instruktur'] ?? 0, 'label' => 'Instruktur'],
                ['icon' => 'fa-solid fa-user-graduate', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => ($stats['siswa'] ?? 0) + ($stats['wali_santri'] ?? 0), 'label' => 'Siswa & Wali'],
            ]
        ];
        require_once __DIR__ . '/components/hero_header.php';
        ?>

        <div class="p-6 space-y-6">

            <!-- Flash Notification -->
            <?php if (session()->getFlashdata('notif_msg')) : ?>
                <?php
                    $type = session()->getFlashdata('notif_type') ?? 'add';
                    $bgClass = $type === 'add' ? 'bg-[#DCFCE7] border-[#86EFAC] text-[#166534]'
                             : ($type === 'edit' ? 'bg-amber-50 border-amber-200 text-amber-900'
                             : 'bg-[#FEE2E2] border-[#FCA5A5] text-[#991B1B]');
                    $iconBg  = $type === 'add' ? 'bg-emerald-600'
                             : ($type === 'edit' ? 'bg-amber-500' : 'bg-rose-600');
                    $iconFa  = $type === 'add' ? 'fa-circle-check'
                             : ($type === 'edit' ? 'fa-pen-to-square' : 'fa-trash-can');
                ?>
                <div id="flashUserAlert" class="rounded-xl border p-4 shadow-sm flex items-start justify-between gap-3 <?= $bgClass; ?>">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg <?= $iconBg; ?> text-white flex items-center justify-center text-base shrink-0 mt-0.5">
                            <i class="fa-solid <?= $iconFa; ?>"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold"><?= esc(session()->getFlashdata('notif_title') ?? 'Notifikasi Sistem'); ?></h4>
                            <p class="text-xs mt-0.5"><?= esc(session()->getFlashdata('notif_msg')); ?></p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('flashUserAlert').remove()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
                    <span><?= esc(session()->getFlashdata('error')); ?></span>
                </div>
            <?php endif; ?>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                
                <!-- Table Controls Bar -->
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                    
                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input type="text" id="userSearchInput" onkeyup="filterUserTable()"
                               placeholder="Cari user (nama, username, role)..." 
                               class="w-full pl-9 pr-8 py-2 text-xs border border-slate-300 rounded-xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition">
                    </div>

                    <!-- Filter Role Dropdown & Add User Button -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full md:w-auto">
                        <select id="userFilterRole" onchange="filterUserTable()" 
                                class="w-full sm:w-auto px-3 py-2 text-xs border border-slate-300 rounded-xl bg-white text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition">
                            <option value="">Semua Role</option>
                            <option value="admin">Administrator</option>
                            <option value="instruktur">Instruktur</option>
                            <option value="pendamping_agama">Pendamping Agama</option>
                            <option value="siswa">Siswa / Santri</option>
                            <option value="wali_santri">Wali Santri</option>
                        </select>

                        <!-- Add User Button -->
                        <a href="<?= base_url('admin/users/add'); ?>" 
                           class="w-full sm:w-auto justify-center bg-brand hover:bg-brand-dark text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-sm shadow-brand/20 flex items-center gap-2 transition cursor-pointer">
                            <i class="fa-solid fa-user-plus"></i> <span>Tambah User Baru</span>
                        </a>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left border-collapse" id="userMainTable">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700 uppercase tracking-wider">
                                <th class="py-3 px-3 border-r border-slate-200 w-12 text-center">ID</th>
                                <th class="py-3 px-3 border-r border-slate-200">Pengguna</th>
                                <th class="py-3 px-3 border-r border-slate-200">Username</th>
                                <th class="py-3 px-3 border-r border-slate-200 text-center">Role</th>
                                <th class="py-3 px-3 border-r border-slate-200">Tautan Santri</th>
                                <th class="py-3 px-3 border-r border-slate-200 text-center w-24">Status</th>
                                <th class="py-3 px-3 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-200" id="userTableBody">
                            <?php foreach ($users as $index => $u): ?>
                            <?php
                                $isSelf = ((int)$u['id'] === (int)session()->get('user_id'));
                                $roleBadge = [
                                    'admin'            => 'bg-blue-50 text-[#0066CC] border-blue-200',
                                    'instruktur'       => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'pendamping_agama' => 'bg-teal-50 text-teal-800 border-teal-200',
                                    'siswa'            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'wali_santri'      => 'bg-amber-50 text-amber-800 border-amber-200',
                                ][$u['role']] ?? 'bg-slate-50 text-slate-700 border-slate-200';

                                $roleLabel = [
                                    'admin'            => 'Administrator',
                                    'instruktur'       => 'Instruktur',
                                    'pendamping_agama' => 'Pendamping Agama',
                                    'siswa'            => 'Siswa / Santri',
                                    'wali_santri'      => 'Wali Santri',
                                ][$u['role']] ?? ucfirst($u['role']);

                                $roleIcon = [
                                    'admin'            => 'fa-solid fa-user-shield',
                                    'instruktur'       => 'fa-solid fa-chalkboard-user',
                                    'pendamping_agama' => 'fa-solid fa-book-quran',
                                    'siswa'            => 'fa-solid fa-user-graduate',
                                    'wali_santri'      => 'fa-solid fa-hands-holding-child',
                                ][$u['role']] ?? 'fa-solid fa-user';
                            ?>
                            <tr class="user-row <?= $index % 2 === 1 ? 'bg-slate-50/60' : 'bg-white'; ?> hover:bg-brand-light/30 transition">
                                <td class="py-3 px-3 border-r border-slate-200 font-mono text-xs font-semibold text-slate-500 text-center col-user-id">
                                    <?= esc($u['id']); ?>
                                </td>
                                
                                <td class="py-3 px-3 border-r border-slate-200 col-user-name">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-brand font-bold text-xs flex items-center justify-center shrink-0">
                                            <?= strtoupper(mb_substr($u['nama'] ?? 'U', 0, 1)); ?>
                                        </div>
                                        <div class="leading-tight">
                                            <p class="font-bold text-slate-800 text-xs sm:text-sm">
                                                <?= esc($u['nama']); ?>
                                                <?php if ($isSelf): ?>
                                                    <span class="ml-1 text-3xs font-extrabold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 border border-blue-200">Anda</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-3 border-r border-slate-200 font-mono text-xs text-slate-700 font-semibold col-user-username">
                                    <span class="text-slate-400">@</span><?= esc($u['username']); ?>
                                </td>

                                <td class="py-3 px-3 border-r border-slate-200 text-center col-user-role" data-role="<?= esc($u['role']); ?>">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-3xs font-bold border <?= $roleBadge; ?>">
                                        <i class="<?= $roleIcon; ?> text-2xs"></i> <?= $roleLabel; ?>
                                    </span>
                                </td>

                                <td class="py-3 px-3 border-r border-slate-200 text-xs">
                                    <?php if (!empty($u['santri_nama'])): ?>
                                        <div class="leading-tight">
                                            <span class="font-semibold text-slate-800 block truncate"><?= esc($u['santri_nama']); ?></span>
                                            <span class="text-3xs text-slate-500 font-mono">NIS: <?= esc($u['santri_nis'] ?? '-'); ?> • <?= esc($u['santri_kelas'] ?? '-'); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-3xs text-slate-400 italic">Tidak ditautkan</span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-3 px-3 border-r border-slate-200 text-center">
                                    <?php if (!empty($u['is_active'])): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-3xs font-bold bg-white text-emerald-700 border border-slate-200 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-3xs font-semibold bg-white text-slate-500 border border-slate-200 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <!-- Edit User Button -->
                                    <button type="button"
                                            onclick='openEditUserModal(<?= htmlspecialchars(json_encode($u), ENT_QUOTES, "UTF-8"); ?>)'
                                            title="Edit Data & Hak Akses Pengguna"
                                            class="p-1.5 rounded-lg border border-sky-200 text-brand hover:bg-sky-50 text-xs font-semibold mr-1 transition cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Toggle Status Button -->
                                    <?php if (!$isSelf): ?>
                                        <form action="<?= base_url('admin/users/toggle/' . urlencode($u['id'])); ?>" method="post" class="inline">
                                            <?= csrf_field(); ?>
                                            <button type="submit" 
                                                    title="<?= $u['is_active'] ? 'Nonaktifkan Akun' : 'Aktifkan Akun'; ?>"
                                                    class="p-1.5 rounded-lg border text-xs font-semibold mr-1 transition cursor-pointer <?= $u['is_active'] ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50'; ?>">
                                                <i class="fa-solid <?= $u['is_active'] ? 'fa-user-slash' : 'fa-user-check'; ?>"></i>
                                            </button>
                                        </form>

                                        <!-- Delete Button -->
                                        <form action="<?= base_url('admin/users/delete/' . urlencode($u['id'])); ?>" method="post" class="inline">
                                            <?= csrf_field(); ?>
                                            <button type="submit" onclick="return confirm('Hapus permanen akun user <?= esc($u['nama']); ?> (@<?= esc($u['username']); ?>)?')"
                                                    title="Hapus Akun Pengguna"
                                                    class="p-1.5 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50 text-xs font-semibold transition cursor-pointer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-3xs text-slate-400 italic">Akun aktif Anda</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </main>
</div>

<script>
function filterUserTable() {
    const searchVal = document.getElementById('userSearchInput').value.trim().toLowerCase();
    const roleVal = document.getElementById('userFilterRole').value.trim().toLowerCase();
    const rows = document.querySelectorAll('.user-row');

    rows.forEach(row => {
        const nameText = row.querySelector('.col-user-name')?.textContent.toLowerCase() || '';
        const userText = row.querySelector('.col-user-username')?.textContent.toLowerCase() || '';
        const roleData = row.querySelector('.col-user-role')?.getAttribute('data-role')?.toLowerCase() || '';

        const matchSearch = searchVal === '' || nameText.includes(searchVal) || userText.includes(searchVal);
        const matchRole = roleVal === '' || roleData === roleVal;

        if (matchSearch && matchRole) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function openEditUserModal(user) {
    document.getElementById('edit_user_id').value = user.id || '';
    document.getElementById('edit_nama').value = user.nama || '';
    document.getElementById('edit_username').value = user.username || '';
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_role').value = user.role || 'siswa';
    document.getElementById('edit_is_active').value = user.is_active !== undefined ? user.is_active : '1';
    
    const santriSelect = document.getElementById('edit_santri_id');
    if (santriSelect) {
        santriSelect.value = user.santri_id || '';
    }

    handleEditRoleChange(user.role);
    document.getElementById('modalEditUser').classList.remove('hidden');
}

function closeEditUserModal() {
    document.getElementById('modalEditUser').classList.add('hidden');
}

function handleEditRoleChange(role) {
    const santriWrap = document.getElementById('editSantriWrapper');
    if (role === 'siswa' || role === 'wali_santri') {
        santriWrap.classList.remove('hidden');
    } else {
        santriWrap.classList.add('hidden');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditUserModal();
    }
});
</script>

<!-- ===================== MODAL EDIT PENGGUNA (USER LOGIN) ===================== -->
<div id="modalEditUser" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div onclick="closeEditUserModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <!-- Header -->
        <div class="flex items-start justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Edit Data Pengguna</h3>
                    <p class="text-xs text-slate-500">Perbarui profil akun, otoritas role, dan kata sandi login</p>
                </div>
            </div>
            <button type="button" onclick="closeEditUserModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('admin/users/update'); ?>" method="POST" class="space-y-3.5">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="edit_user_id">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Pengguna <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="edit_nama" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-medium text-slate-800">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Username Login <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" id="edit_username" required 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-mono font-medium text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Role / Peran Akses <span class="text-rose-500">*</span></label>
                    <select name="role" id="edit_role" onchange="handleEditRoleChange(this.value)" required 
                            class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-medium text-slate-800 bg-white">
                        <option value="admin">Administrator (Admin)</option>
                        <option value="instruktur">Instruktur Kejuruan</option>
                        <option value="pendamping_agama">Pendamping Agama</option>
                        <option value="siswa">Siswa / Santri</option>
                        <option value="wali_santri">Wali Santri</option>
                    </select>
                </div>
            </div>

            <!-- Tautan Santri (Muncul jika role Siswa / Wali) -->
            <div id="editSantriWrapper" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Tautkan ke Data Santri:
                </label>
                <select name="santri_id" id="edit_santri_id" 
                        class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-medium text-slate-800 bg-white">
                    <option value="">-- Tidak Ditautkan --</option>
                    <?php if (!empty($santri_list)): ?>
                        <?php foreach ($santri_list as $sl): ?>
                            <option value="<?= esc($sl['id']); ?>">
                                <?= esc($sl['nama']); ?> (NIS: <?= esc($sl['nis'] ?? '-'); ?> - <?= esc($sl['kelas']); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="text-3xs text-slate-400 mt-0.5 block">Diperlukan agar akun dapat mengakses data rapor dan nilai santri terkait.</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" id="edit_password" placeholder="Kosongkan jika tidak diubah" 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-medium text-slate-800">
                    <span class="text-3xs text-slate-400 mt-0.5 block">Minimal 6 karakter jika ingin ganti password.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Akun</label>
                    <select name="is_active" id="edit_is_active" 
                            class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand font-medium text-slate-800 bg-white">
                        <option value="1">Aktif (Dapat Login)</option>
                        <option value="0">Nonaktif (Diblokir)</option>
                    </select>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditUserModal()" 
                        class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-600 hover:bg-slate-100 border border-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 text-xs font-bold rounded-xl text-white bg-brand hover:bg-brand-dark shadow-md shadow-brand/20 transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
