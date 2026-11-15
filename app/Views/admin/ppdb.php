<?php require_once 'navbar.php'; ?>

<div class="flex-1 min-w-0 flex flex-col overflow-hidden bg-[#F8FAFC]">

    <!-- Top Navbar -->
    <?php 
    $page_title = $title ?? 'Manajemen Pendaftar PPDB 2026';
    require_once __DIR__ . '/components/topbar.php'; 
    ?>

    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto pb-16">

        <!-- ===================== HERO WAVE BANNER ===================== -->
        <?php
        $hero = [
            'breadcrumb' => [
                ['label' => 'Portal Admin', 'url' => base_url('admin')],
                ['label' => 'Pendaftar PPDB']
            ],
            'badge' => 'Penerimaan Santri Baru Angkatan 36',
            'title' => 'Manajemen Pendaftar PPDB Online',
            'subtitle' => 'Kelola seluruh data calon santri yang mendaftar online melalui landing page PPDB. Pantau verifikasi berkas, seleksi wawancara, hingga penetapan penerima beasiswa.',
            'stats' => [
                ['icon' => 'fa-solid fa-user-plus', 'bg' => 'bg-sky-400/20 text-sky-200', 'value' => $stats['total'] ?? 0, 'label' => 'Total Pendaftar'],
                ['icon' => 'fa-solid fa-clock', 'bg' => 'bg-amber-400/20 text-amber-200', 'value' => $stats['menunggu'] ?? 0, 'label' => 'Menunggu Seleksi'],
                ['icon' => 'fa-solid fa-id-card-clip', 'bg' => 'bg-blue-400/20 text-blue-200', 'value' => $stats['lolos_berkas'] ?? 0, 'label' => 'Lolos Berkas'],
                ['icon' => 'fa-solid fa-graduation-cap', 'bg' => 'bg-emerald-400/20 text-emerald-200', 'value' => $stats['diterima'] ?? 0, 'label' => 'Diterima Beasiswa'],
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
                <div id="flashNoticeAlert" class="rounded-xl border p-4 shadow-sm flex items-start justify-between gap-3 <?= $bgClass; ?>">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg <?= $iconBg; ?> text-white flex items-center justify-center text-base shrink-0 mt-0.5">
                            <i class="fa-solid <?= $iconFa; ?>"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold"><?= esc(session()->getFlashdata('notif_title') ?? 'Notifikasi Sistem'); ?></h4>
                            <p class="text-xs mt-0.5"><?= esc(session()->getFlashdata('notif_msg')); ?></p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('flashNoticeAlert').remove()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
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

            <!-- Filter & Search Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs">
                <form action="<?= base_url('admin/ppdb'); ?>" method="GET" class="flex flex-col md:flex-row gap-3 items-center justify-between">
                    
                    <!-- Search Input -->
                    <div class="relative flex-1 w-full">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="<?= esc($search ?? ''); ?>" 
                            placeholder="Cari nama calon santri, No. Registrasi, WhatsApp, atau jurusan..." 
                            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066CC] focus:border-[#0066CC] transition"
                        >
                    </div>

                    <!-- Filter Status -->
                    <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                        <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0066CC] font-medium w-full md:w-auto">
                            <option value="">-- Semua Status Seleksi --</option>
                            <option value="Menunggu Seleksi" <?= ($filterStatus ?? '') === 'Menunggu Seleksi' ? 'selected' : ''; ?>>Menunggu Seleksi</option>
                            <option value="Lolos Berkas" <?= ($filterStatus ?? '') === 'Lolos Berkas' ? 'selected' : ''; ?>>Lolos Berkas</option>
                            <option value="Lolos Wawancara" <?= ($filterStatus ?? '') === 'Lolos Wawancara' ? 'selected' : ''; ?>>Lolos Wawancara</option>
                            <option value="Diterima" <?= ($filterStatus ?? '') === 'Diterima' ? 'selected' : ''; ?>>Diterima (Santri Baru)</option>
                            <option value="Tidak Lolos" <?= ($filterStatus ?? '') === 'Tidak Lolos' ? 'selected' : ''; ?>>Tidak Lolos</option>
                        </select>

                        <button type="submit" class="px-3 py-2 bg-[#0066CC] hover:bg-[#0052A3] text-white rounded-xl text-xs font-semibold transition shrink-0">
                            Filter
                        </button>

                        <?php if (!empty($search) || !empty($filterStatus)) : ?>
                            <a href="<?= base_url('admin/ppdb'); ?>" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition shrink-0" title="Reset Filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Daftar Calon Santri Pendaftar PPDB</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Total <?= count($pendaftar); ?> pendaftar ditemukan.</p>
                    </div>
                    <a href="<?= base_url('ppdb'); ?>" target="_blank" class="px-3 py-1.5 bg-brand-light text-brand hover:bg-brand hover:text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 border border-brand-border">
                        <i class="fa-solid fa-arrow-up-right-from-square text-2xs"></i>
                        <span>Buka Halaman PPDB</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[860px]">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase tracking-wider text-3xs font-bold">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-4">Calon Santri</th>
                                <th class="py-3 px-4">Pilihan Jurusan</th>
                                <th class="py-3 px-3">Kampus</th>
                                <th class="py-3 px-3">Kontak WA</th>
                                <th class="py-3 px-3 text-center">Status Seleksi</th>
                                <th class="py-3 px-3 text-center">Tgl Daftar</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($pendaftar)) : ?>
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <i class="fa-solid fa-user-slash text-2xl mb-2 block"></i>
                                        <span>Tidak ada data pendaftar yang cocok dengan filter atau pencarian.</span>
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php 
                                    $statusConfigs = [
                                        'Menunggu Seleksi' => [
                                            'class' => 'bg-amber-500/10 text-amber-900 border-amber-500/30',
                                            'icon'  => 'fa-regular fa-clock text-amber-600'
                                        ],
                                        'Lolos Berkas'     => [
                                            'class' => 'bg-sky-500/10 text-sky-950 border-sky-500/30',
                                            'icon'  => 'fa-regular fa-file-lines text-sky-600'
                                        ],
                                        'Lolos Wawancara'  => [
                                            'class' => 'bg-purple-500/10 text-purple-950 border-purple-500/30',
                                            'icon'  => 'fa-regular fa-comments text-purple-600'
                                        ],
                                        'Diterima'         => [
                                            'class' => 'bg-emerald-500/10 text-emerald-950 border-emerald-500/30',
                                            'icon'  => 'fa-solid fa-check-double text-emerald-600'
                                        ],
                                        'Tidak Lolos'      => [
                                            'class' => 'bg-slate-500/10 text-slate-700 border-slate-300',
                                            'icon'  => 'fa-regular fa-circle-xmark text-slate-500'
                                        ],
                                    ];
                                ?>
                                <?php foreach ($pendaftar as $idx => $p) : ?>
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-3.5 text-center text-slate-400 font-semibold"><?= $idx + 1; ?></td>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-900 text-xs"><?= esc($p['nama_lengkap']); ?></div>
                                            <div class="text-3xs text-brand font-semibold"><?= esc($p['no_pendaftaran']); ?></div>
                                            <div class="text-3xs text-slate-400"><?= $p['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?> • <?= esc($p['kota'] ?: 'Domisili tidak diisi'); ?></div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-semibold text-slate-800 block text-xs"><?= esc($p['jurusan_pilihan1']); ?></span>
                                            <?php if (!empty($p['jurusan_pilihan2'])) : ?>
                                                <span class="text-3xs text-slate-400 block">Cadangan: <?= esc($p['jurusan_pilihan2']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3 text-slate-700 font-medium">
                                            <?= esc($p['cabang_kampus']); ?>
                                        </td>
                                        <td class="py-3 px-3">
                                            <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $p['whatsapp'])); ?>" target="_blank" class="font-semibold text-emerald-700 hover:underline flex items-center gap-1">
                                                <i class="fa-brands fa-whatsapp text-emerald-600"></i> <?= esc($p['whatsapp']); ?>
                                            </a>
                                            <?php if (!empty($p['nama_wali'])) : ?>
                                                <span class="text-3xs text-slate-400 block">Wali: <?= esc($p['nama_wali']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <?php 
                                                $cfg = $statusConfigs[$p['status']] ?? [
                                                    'class' => 'bg-slate-100 text-slate-700 border-slate-300',
                                                    'icon'  => 'fa-regular fa-circle-question text-slate-500'
                                                ];
                                            ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-2xs font-semibold tracking-tight border shadow-2xs <?= $cfg['class']; ?>">
                                                <i class="<?= $cfg['icon']; ?> text-[10.5px]"></i>
                                                <?= esc($p['status']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center text-3xs text-slate-500 whitespace-nowrap">
                                            <?= !empty($p['created_at']) ? date('d M Y H:i', strtotime($p['created_at'])) : '-'; ?>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <!-- Tombol Tinjau Berkas Langsung -->
                                                <button type="button" onclick='showDetailModal(<?= json_encode($p); ?>); scrollToBerkas();' class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded-lg text-3xs font-bold border border-amber-200 transition cursor-pointer flex items-center gap-1" title="Tinjau & Unduh Berkas Persyaratan">
                                                    <i class="fa-solid fa-folder-open text-amber-600"></i> Berkas
                                                </button>

                                                <!-- Tombol Detail -->
                                                <button type="button" onclick='showDetailModal(<?= json_encode($p); ?>)' class="px-2.5 py-1 bg-brand-light hover:bg-[#0066CC] text-brand hover:text-white rounded-lg text-3xs font-bold border border-brand-border transition cursor-pointer" title="Lihat Detail Lengkap">
                                                    Detail
                                                </button>

                                                <!-- Tombol Ubah Status Cepat -->
                                                <button type="button" onclick="showStatusModal(<?= $p['id']; ?>, '<?= esc($p['nama_lengkap'], 'js'); ?>', '<?= esc($p['no_pendaftaran'], 'js'); ?>', '<?= esc($p['status'], 'js'); ?>')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-3xs font-bold border border-slate-200 transition cursor-pointer" title="Ubah Status">
                                                    Status
                                                </button>

                                                <!-- Tombol Hapus -->
                                                <form action="<?= base_url('admin/ppdb/delete/' . $p['id']); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pendaftar ini?')" class="inline">
                                                    <?= csrf_field(); ?>
                                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg transition" title="Hapus Data">
                                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>
</div>

<!-- ================= MODAL DETAIL LENGKAP CALON SANTRI ================= -->
<div id="detailPpdbModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div onclick="closeDetailModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

    <div class="relative bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden z-10 border border-slate-200 my-8">
        <div class="h-2.5 bg-gradient-to-r from-deep via-blue to-pencil"></div>

        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-start justify-between gap-3">
            <div>
                <span class="text-3xs font-extrabold uppercase tracking-wider text-brand" id="modalNoReg">PPDB-36-001</span>
                <h3 class="text-lg font-bold text-slate-900" id="modalNama">Nama Santri</h3>
                <span class="text-xs text-slate-500" id="modalSub">Laki-laki • NIK: -</span>
            </div>
            <button onclick="closeDetailModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-5 sm:p-6 space-y-4 text-xs">
            
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-3xs text-slate-400 block uppercase font-bold">Jurusan Utama</span>
                    <strong class="text-slate-800 text-xs block mt-0.5" id="modalJurusan1">-</strong>
                    <span class="text-3xs text-slate-500" id="modalJurusan2">Cadangan: -</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-3xs text-slate-400 block uppercase font-bold">Kampus Diklat</span>
                    <strong class="text-slate-800 text-xs block mt-0.5" id="modalKampus">-</strong>
                    <span class="text-3xs text-slate-500" id="modalPendidikan">Pendidikan: -</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-3xs text-slate-400 block uppercase font-bold">Kontak &amp; Alamat Domisili</span>
                <div class="mt-1 space-y-0.5 text-slate-700">
                    <div>WhatsApp: <strong id="modalWa">-</strong></div>
                    <div>Email: <span id="modalEmail">-</span></div>
                    <div>Alamat: <span id="modalAlamat">-</span></div>
                    <div>Orang Tua/Wali: <span id="modalWali">-</span> (WA: <span id="modalWaWali">-</span>)</div>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-3xs text-slate-400 block uppercase font-bold">Motivasi &amp; Alasan Ingin Belajar di RGI</span>
                <p class="text-slate-700 mt-1 leading-relaxed italic" id="modalMotivasi">-</p>
            </div>

            <!-- ================= SEKSI BERKAS PERSYARATAN SELEKSI ================= -->
            <div id="berkasSectionBlock" class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-folder-open text-amber-600"></i> Berkas Persyaratan Calon Santri
                        </h4>
                        <p class="text-3xs text-slate-500">Tinjau dan unduh berkas untuk verifikasi seleksi berkas calon santri.</p>
                    </div>
                    <a id="btnDownloadAllZip" href="#" class="px-2.5 py-1 bg-[#0066CC] hover:bg-[#0052A3] text-white text-3xs font-bold rounded-lg shadow-xs transition flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-file-zipper"></i> Unduh Semua (ZIP)
                    </a>
                </div>

                <!-- Grid 6 Dokumen -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="berkasListContainer">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-between">
                <div>
                    <span class="text-3xs text-blue-700 block uppercase font-bold">Status Seleksi Terkini</span>
                    <span class="text-sm font-bold text-blue-900" id="modalStatusLabel">Menunggu Seleksi</span>
                </div>
                <button type="button" id="btnBukaStatusFromDetail" class="px-3 py-1.5 rounded-lg bg-[#0066CC] text-white font-semibold text-xs transition">
                    Ubah Status
                </button>
            </div>

        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <a id="btnChatWaModal" href="#" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs flex items-center gap-1.5">
                <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
            </a>
            <button onclick="closeDetailModal()" class="px-4 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ================= MODAL UPDATE STATUS SELEKSI ================= -->
<div id="statusPpdbModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div onclick="closeStatusModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

    <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden z-10 border border-slate-200 my-8">
        <form id="formUpdateStatus" action="" method="POST">
            <?= csrf_field(); ?>
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Ubah Status Seleksi</h3>
                    <p class="text-xs text-slate-500" id="statusModalNama">-</p>
                </div>
                <button type="button" onclick="closeStatusModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-5 space-y-3 text-xs">
                <label for="selectStatusModal" class="block font-bold text-slate-700">Pilih Status Baru:</label>
                <select name="status" id="selectStatusModal" class="w-full h-10 px-3 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#0066CC] font-semibold bg-white">
                    <option value="Menunggu Seleksi">Menunggu Seleksi</option>
                    <option value="Lolos Berkas">Lolos Berkas</option>
                    <option value="Lolos Wawancara">Lolos Wawancara</option>
                    <option value="Diterima">Diterima (Penerima Beasiswa)</option>
                    <option value="Tidak Lolos">Tidak Lolos</option>
                </select>
                <p class="text-3xs text-slate-400">
                    Status ini dapat dilihat oleh calon santri saat melakukan pengecekan di menu Cek Status PPDB.
                </p>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeStatusModal()" class="px-3.5 py-1.5 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-[#0066CC] hover:bg-[#0052A3] text-white text-xs font-bold transition shadow-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentPendaftar = null;

    function showDetailModal(p) {
        currentPendaftar = p;
        document.getElementById('modalNoReg').textContent = p.no_pendaftaran;
        document.getElementById('modalNama').textContent = p.nama_lengkap;
        document.getElementById('modalSub').textContent = `${p.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'} • NIK: ${p.nik || '-'}`;
        document.getElementById('modalJurusan1').textContent = p.jurusan_pilihan1;
        document.getElementById('modalJurusan2').textContent = `Cadangan: ${p.jurusan_pilihan2 || '-'}`;
        document.getElementById('modalKampus').textContent = p.cabang_kampus;
        document.getElementById('modalPendidikan').textContent = `Pendidikan: ${p.pendidikan_terakhir} (${p.asal_sekolah || '-'})`;
        document.getElementById('modalWa').textContent = p.whatsapp;
        document.getElementById('modalEmail').textContent = p.email || '-';
        document.getElementById('modalAlamat').textContent = `${p.alamat}${p.kota ? ', ' + p.kota : ''}`;
        document.getElementById('modalWali').textContent = p.nama_wali || '-';
        document.getElementById('modalWaWali').textContent = p.whatsapp_wali || '-';
        document.getElementById('modalMotivasi').textContent = p.motivasi || 'Tidak mengisi motivasi';
        document.getElementById('modalStatusLabel').textContent = p.status;

        const cleanWa = p.whatsapp.replace(/^0/, '62').replace(/[^0-9]/g, '');
        document.getElementById('btnChatWaModal').href = `https://wa.me/${cleanWa}?text=${encodeURIComponent(`Halo ${p.nama_lengkap}, kami dari Panitia PPDB Rumah Gemilang Indonesia terkait pendaftaran nomor ${p.no_pendaftaran}.`)}`;

        document.getElementById('btnBukaStatusFromDetail').onclick = () => {
            closeDetailModal();
            showStatusModal(p.id, p.nama_lengkap, p.no_pendaftaran, p.status);
        };

        document.getElementById('detailPpdbModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailPpdbModal').classList.add('hidden');
    }

    function showStatusModal(id, nama, noReg, currentStatus) {
        const form = document.getElementById('formUpdateStatus');
        form.action = '<?= base_url('admin/ppdb/status/'); ?>' + id;
        document.getElementById('statusModalNama').textContent = `${nama} (${noReg})`;
        document.getElementById('selectStatusModal').value = currentStatus;
        document.getElementById('statusPpdbModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusPpdbModal').classList.add('hidden');
    }

    function scrollToBerkas() {
        setTimeout(() => {
            const el = document.getElementById('berkasSectionBlock');
            if (el) el.scrollIntoView({ behavior: 'smooth' });
        }, 150);
    }

    // Daftar Definisi Berkas
    const docMeta = [
        { key: 'berkas_ijazah', label: 'Ijazah / SKL Terakhir', icon: 'fa-graduation-cap' },
        { key: 'berkas_ktp',    label: 'KTP Calon Santri',      icon: 'fa-id-card' },
        { key: 'berkas_kk',     label: 'Kartu Keluarga (KK)',   icon: 'fa-users' },
        { key: 'berkas_sktm',   label: 'Surat SKTM Kelurahan',  icon: 'fa-file-invoice-dollar' },
        { key: 'berkas_sehat',  label: 'Surat Bebas Narkoba',   icon: 'fa-heart-pulse' },
        { key: 'berkas_vaksin', label: 'Sertifikat Vaksin',     icon: 'fa-shield-virus' },
    ];

    // Perbarui fungsi showDetailModal untuk mengisi berkas secara dinamis
    const originalShowDetail = showDetailModal;
    showDetailModal = function(p) {
        originalShowDetail(p);

        // Update link ZIP
        document.getElementById('btnDownloadAllZip').href = '<?= base_url('admin/ppdb/download-zip'); ?>/' + p.no_pendaftaran;

        // Render 6 kartu dokumen berkas
        const container = document.getElementById('berkasListContainer');
        container.innerHTML = '';

        docMeta.forEach(doc => {
            const filename = p[doc.key];
            const hasFile = (filename && filename.trim() !== '');
            const fileUrl = hasFile ? '<?= base_url('uploads/ppdb'); ?>/' + filename : '#';

            const card = document.createElement('div');
            card.className = 'p-3 rounded-xl border ' + (hasFile ? 'bg-white border-slate-200' : 'bg-slate-100/70 border-slate-200 text-slate-400') + ' flex items-center justify-between gap-3';

            let actionsHtml = '';
            if (hasFile) {
                actionsHtml = `
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="previewBerkasFile('${doc.label}', '${fileUrl}')" class="px-2 py-1 bg-sky-50 hover:bg-[#0066CC] text-[#0066CC] hover:text-white rounded-lg text-3xs font-bold border border-sky-200 transition cursor-pointer flex items-center gap-1" title="Tinjau Berkas">
                            <i class="fa-regular fa-eye"></i> Tinjau
                        </button>
                        <a href="${fileUrl}" download class="p-1 text-slate-400 hover:text-brand rounded-lg transition" title="Unduh Berkas">
                            <i class="fa-solid fa-download text-xs"></i>
                        </a>
                    </div>
                `;
            } else {
                actionsHtml = `<span class="text-3xs text-rose-500 font-semibold italic shrink-0">Belum Ada</span>`;
            }

            card.innerHTML = `
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg ${hasFile ? 'bg-amber-50 text-amber-600' : 'bg-slate-200 text-slate-400'} flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid ${doc.icon}"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="text-xs font-bold ${hasFile ? 'text-slate-800' : 'text-slate-500'} truncate">${doc.label}</h5>
                        <span class="text-3xs text-slate-400 truncate block">${hasFile ? filename : 'Dokumen belum diunggah'}</span>
                    </div>
                </div>
                ${actionsHtml}
            `;
            container.appendChild(card);
        });
    };

    function previewBerkasFile(title, url) {
        document.getElementById('previewModalTitle').textContent = title;
        document.getElementById('previewModalImg').src = url;
        document.getElementById('previewModalDownloadBtn').href = url;
        document.getElementById('previewModalNewTabBtn').href = url;
        document.getElementById('modalPreviewBerkas').classList.remove('hidden');
    }

    function closePreviewBerkasModal() {
        document.getElementById('modalPreviewBerkas').classList.add('hidden');
        document.getElementById('previewModalImg').src = '';
    }
</script>

<!-- ================= MODAL PRATINJAU BERKAS (LIGHTBOX) ================= -->
<div id="modalPreviewBerkas" class="fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div onclick="closePreviewBerkasModal()" class="fixed inset-0 bg-slate-900/80 backdrop-blur-xs"></div>

    <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden z-10 border border-slate-200 my-8">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 truncate" id="previewModalTitle">Pratinjau Berkas Calon Santri</h3>
                    <p class="text-3xs text-slate-500">Pemeriksaan dokumen seleksi berkas PPDB</p>
                </div>
            </div>
            <button onclick="closePreviewBerkasModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-4 bg-slate-950 flex items-center justify-center min-h-[340px] max-h-[500px] overflow-auto">
            <img id="previewModalImg" src="" alt="Pratinjau Berkas" class="max-w-full max-h-[480px] object-contain rounded-lg shadow-lg">
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <a id="previewModalNewTabBtn" href="#" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square text-2xs"></i> Buka Layar Penuh
            </a>
            <div class="flex items-center gap-2">
                <a id="previewModalDownloadBtn" href="#" download class="px-4 py-1.5 rounded-xl bg-[#0066CC] hover:bg-[#0052A3] text-white font-bold text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-download"></i> Unduh File
                </a>
                <button onclick="closePreviewBerkasModal()" class="px-4 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
