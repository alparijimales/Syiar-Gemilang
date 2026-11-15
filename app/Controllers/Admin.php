<?php

namespace App\Controllers;

use App\Models\StudentModel;

class Admin extends BaseController
{
    public function index()
    {
        $studentModel = new StudentModel();

        $data = [
            'title'              => 'Dashboard Akademik & Kejuruan',
            'total_siswa'        => $studentModel->countAll(),
            'total_mapel'        => $studentModel->getJumlahMataPelajaran(),
            'rata_rata'          => $studentModel->getRataRataNilai(),
            'rata_kehadiran'     => $studentModel->getRataRataKehadiran(),
            'persentase_jurusan' => $studentModel->getPersentaseJurusan(),
            'top_students'       => $studentModel->getTopStudents()
        ];

        return view('admin/dashboard', $data);
    }

    public function addStudent()
    {
        $db = \Config\Database::connect();
        $mapels = $db->table('mapel_kompetensi')
            ->select('id, kode_mapel, nama_mapel, jurusan, kkm')
            ->orderBy('jurusan', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'  => 'Tambah Data Santri',
            'mapels' => $mapels
        ];
        return view('admin/student_add', $data);
    }

    public function saveStudent()
    {
        $studentModel = new StudentModel();

        $name            = trim((string)$this->request->getPost('nama'));
        $class           = $this->request->getPost('kelas');
        $nis             = trim((string)$this->request->getPost('nis'));
        $cabang          = trim((string)$this->request->getPost('cabang')) ?: 'Kampus Utama Depok';
        $angkatan        = trim((string)$this->request->getPost('angkatan')) ?: 'Angkatan 35';
        $status          = $this->request->getPost('status') ?: 'Aktif';

        // Nilai sekarang opsional / boleh kosong untuk santri baru
        $tauhidRaw       = $this->request->getPost('tauhid');
        $tauhid          = ($tauhidRaw !== '' && $tauhidRaw !== null) ? (float)$tauhidRaw : null;

        $fiqihRaw        = $this->request->getPost('fiqih');
        $fiqih           = ($fiqihRaw !== '' && $fiqihRaw !== null) ? (float)$fiqihRaw : null;

        $bIngRaw         = $this->request->getPost('b_ing');
        $b_ing           = ($bIngRaw !== '' && $bIngRaw !== null) ? (float)$bIngRaw : null;

        $kreatifRaw      = $this->request->getPost('menulis_kreatif');
        $menulis_kreatif = ($kreatifRaw !== '' && $kreatifRaw !== null) ? (float)$kreatifRaw : null;

        $adabRaw         = $this->request->getPost('adab_akhlaq');
        $adab_akhlaq     = ($adabRaw !== '' && $adabRaw !== null) ? (float)$adabRaw : null;

        $kehadiranRaw    = $this->request->getPost('kehadiran');
        $kehadiran       = ($kehadiranRaw !== '' && $kehadiranRaw !== null) ? (float)$kehadiranRaw : null;

        // Validasi: hanya nama lengkap dan kelas/jurusan yang wajib diisi untuk santri baru
        if (empty($name) || empty($class)) {
            return redirect()->back()->withInput()->with('error', 'Nama lengkap santri dan kelas/jurusan wajib diisi!');
        }

        // Handle Upload Foto Profil
        $fotoName = null;
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (in_array($fotoFile->getMimeType(), $allowedMimes, true)) {
                $fotoName = $fotoFile->getRandomName();
                $targetDir = FCPATH . 'uploads/santri';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fotoFile->move($targetDir, $fotoName);
            }
        }

        // Auto-generate NIS bila kosong
        if (empty($nis)) {
            $lastRow = $studentModel->orderBy('id', 'DESC')->first();
            $nextId = ($lastRow ? (int)$lastRow['id'] : 0) + 1;
            $nis = 'RGI-35' . str_pad((string)$nextId, 3, '0', STR_PAD_LEFT);
        }

        $dataToSave = [
            'nama'            => $name,
            'nis'             => $nis,
            'cabang'          => $cabang,
            'kelas'           => $class,
            'angkatan'        => $angkatan,
            'status'          => $status,
            'foto'            => $fotoName,
            'tauhid'          => $tauhid,
            'fiqih'           => $fiqih,
            'b_ing'           => $b_ing,
            'menulis_kreatif' => $menulis_kreatif,
            'nilai_kejuruan'  => null,
            'adab_akhlaq'     => $adab_akhlaq,
            'kehadiran'       => $kehadiran,
        ];

        $insertedId = $studentModel->insert($dataToSave);

        // Simpan nilai kejuruan sesuai mapel dari jurusan yang dipilih
        if ($insertedId) {
            $db = \Config\Database::connect();
            $mapelList = $db->table('mapel_kompetensi')->where('jurusan', $class)->get()->getResultArray();
            $mapelNilaiInput = $this->request->getPost('nilai_kejuruan_mapel') ?? [];

            $totK = 0;
            $cntFilled = 0;

            foreach ($mapelList as $mp) {
                $mId = $mp['id'];
                $valInput = (isset($mapelNilaiInput[$mId]) && $mapelNilaiInput[$mId] !== '') ? (float)$mapelNilaiInput[$mId] : null;

                $db->table('nilai_kompetensi')->insert([
                    'santri_id'     => $insertedId,
                    'mapel_id'      => $mId,
                    'nilai_tugas'   => $valInput ?? 0.0,
                    'nilai_teori'   => $valInput ?? 0.0,
                    'nilai_praktik' => $valInput ?? 0.0,
                    'nilai_akhir'   => $valInput ?? 0.0,
                    'catatan'       => ($valInput !== null) ? 'Nilai awal saat penempatan santri baru.' : 'Belum ada penilaian instruktur.'
                ]);

                if ($valInput !== null) {
                    $totK += $valInput;
                    $cntFilled++;
                }
            }

            if ($cntFilled > 0) {
                $studentModel->update($insertedId, ['nilai_kejuruan' => round($totK / $cntFilled, 2)]);
            }
        }

        return redirect()->to(base_url('/admin/data'))->with('notif_type', 'add')
            ->with('notif_title', 'Berhasil Menambahkan Santri!')
            ->with('notif_msg', "Data santri {$name} (NIS: {$nis}) berhasil ditambahkan.");
    }

    public function dataStudent()
    {
        $studentModel = new StudentModel();

        $data = [
            'title'          => 'Data Santri & Akademik',
            'total_siswa'    => $studentModel->countAll(),
            'data_siswa'     => $studentModel->getAllStudents(),
            'rata_rata'      => $studentModel->getRataRataNilai(),
            'rata_kehadiran' => $studentModel->getRataRataKehadiran(),
            'total_mapel'    => $studentModel->getJumlahMataPelajaran(),
            'top_students'   => $studentModel->getTopStudents()
        ];

        return view('admin/data_santri', $data);
    }

    public function update()
    {
        $studentModel = new StudentModel();
        $id = (int)$this->request->getPost('id');

        $dataToUpdate = [
            'nama'            => trim((string)$this->request->getPost('nama')),
            'nis'             => trim((string)$this->request->getPost('nis')),
            'cabang'          => trim((string)$this->request->getPost('cabang')) ?: 'Kampus Utama Depok',
            'kelas'           => $this->request->getPost('kelas'),
            'angkatan'        => trim((string)$this->request->getPost('angkatan')) ?: 'Angkatan 35',
            'status'          => $this->request->getPost('status') ?: 'Aktif',
            'tauhid'          => $this->request->getPost('tauhid'),
            'fiqih'           => $this->request->getPost('fiqih'),
            'b_ing'           => $this->request->getPost('b_ing'),
            'menulis_kreatif' => $this->request->getPost('menulis_kreatif'),
            'adab_akhlaq'     => $this->request->getPost('adab_akhlaq') ?? 85,
            'kehadiran'       => $this->request->getPost('kehadiran') ?? 95,
        ];

        // Handle foto upload jika diunggah file baru
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (in_array($fotoFile->getMimeType(), $allowedMimes, true)) {
                $fotoName = $fotoFile->getRandomName();
                $targetDir = FCPATH . 'uploads/santri';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fotoFile->move($targetDir, $fotoName);
                $dataToUpdate['foto'] = $fotoName;
            }
        }

        // Handle update nilai mata diklat kejuruan jika dikirimkan (array dinamis atau 2 fallback)
        $mapelModel = new \App\Models\MapelModel();
        $kejuruanScores = $this->request->getPost('kejuruan_scores');
        if (is_array($kejuruanScores) && !empty($kejuruanScores)) {
            foreach ($kejuruanScores as $mId => $val) {
                $mId = (int)$mId;
                if ($mId > 0 && $val !== null && $val !== '') {
                    $valF = (float)$val;
                    $mapelModel->saveOrUpdateNilaiKompetensi($id, $mId, $valF, $valF, $valF, 'Diperbarui via edit admin');
                }
            }
        } else {
            $k1Val     = $this->request->getPost('kejuruan_1');
            $k2Val     = $this->request->getPost('kejuruan_2');
            $k1MapelId = (int)$this->request->getPost('kejuruan_1_mapel_id');
            $k2MapelId = (int)$this->request->getPost('kejuruan_2_mapel_id');

            if ($k1MapelId > 0 && $k1Val !== null && $k1Val !== '') {
                $mapelModel->saveOrUpdateNilaiKompetensi($id, $k1MapelId, (float)$k1Val, (float)$k1Val, (float)$k1Val, 'Diperbarui via edit admin');
            }
            if ($k2MapelId > 0 && $k2Val !== null && $k2Val !== '') {
                $mapelModel->saveOrUpdateNilaiKompetensi($id, $k2MapelId, (float)$k2Val, (float)$k2Val, (float)$k2Val, 'Diperbarui via edit admin');
            }
        }

        // Sinkronisasi nilai akumulasi kejuruan
        $db = \Config\Database::connect();
        $avgK = $db->table('nilai_kompetensi')
            ->select('ROUND(AVG(nilai_akhir), 2) as avg_k')
            ->where('santri_id', $id)
            ->get()
            ->getRowArray();
        if ($avgK && isset($avgK['avg_k'])) {
            $dataToUpdate['nilai_kejuruan'] = (float)$avgK['avg_k'];
        }

        $studentModel->update($id, $dataToUpdate);

        return redirect()->to('/admin/data')
            ->with('notif_type', 'edit')
            ->with('notif_title', 'Data Santri Diperbarui!')
            ->with('notif_msg', "Data santri {$dataToUpdate['nama']} (NIS: {$dataToUpdate['nis']}) berhasil diedit.");
    }

    public function delete($id = null)
    {
        $studentModel = new StudentModel();

        $student = $studentModel->find($id);

        if (!$student) {
            return redirect()->to('/admin/data')
                ->with('notif_type', 'delete')
                ->with('notif_title', 'Gagal Menghapus!')
                ->with('notif_msg', "Data santri dengan ID {$id} tidak ditemukan.");
        }

        $deletedName = $student['nama'];
        $studentModel->delete($id);

        return redirect()->to('/admin/data')
            ->with('notif_type', 'delete')
            ->with('notif_title', 'Data Santri Dihapus!')
            ->with('notif_msg', "Data santri {$deletedName} ({$id}) telah dihapus dari daftar.");
    }

    public function pemeringkatan()
    {
        $studentModel = new StudentModel();

        $data = [
            'title'          => 'Pemeringkatan Siswa',
            'total_siswa'    => $studentModel->countAll(),
            'rata_rata'      => $studentModel->getRataRataNilai(),
            'total_mapel'    => $studentModel->getJumlahMataPelajaran(),
            'top3_per_kelas' => $studentModel->getTop3PerClass(),
            'ranking_all'    => $studentModel->getAllRanking()
        ];

        return view('admin/pemeringkatan', $data);
    }

    public function kehadiran()
    {
        $studentModel = new StudentModel();

        $data = [
            'title'                 => 'Sesi & Rekap Kehadiran Santri',
            'total_siswa'           => $studentModel->countAll(),
            'rata_kehadiran'        => $studentModel->getRataRataKehadiran(),
            'sesi_kehadiran'        => $studentModel->getSesiKehadiran(),
            'rekap_kehadiran_kelas' => $studentModel->getRekapKehadiranPerKelas(),
            'data_siswa'            => $studentModel->getAllStudents(),
            'presensi_terbaru'      => $studentModel->getLatestPresensiPerSantri(),
            'rekap_bulanan'         => $studentModel->getRekapBulananSantri(),
            'all_logs'              => $studentModel->getAllPresensiLogs(),
        ];

        return view('admin/kehadiran', $data);
    }

    public function catatPresensiSantri()
    {
        $studentModel = new StudentModel();

        $santriId   = (int)$this->request->getPost('santri_id');
        $sesiId     = (int)$this->request->getPost('sesi_id');
        $status     = $this->request->getPost('status'); // 'Hadir', 'Telat', 'Sakit', 'Absen'
        $tanggal    = $this->request->getPost('tanggal') ?: date('Y-m-d');
        $keterangan = $this->request->getPost('keterangan') ?: '';

        if (!$santriId || !$sesiId || !$status) {
            return redirect()->to('/admin/kehadiran')->with('notif_type', 'delete')
                ->with('notif_title', 'Gagal Mencatat Presensi')
                ->with('notif_msg', 'Santri, sesi kehadiran, dan status presensi wajib dipilih.');
        }

        $res = $studentModel->catatPresensi($santriId, $sesiId, $status, $tanggal, $keterangan);

        return redirect()->to('/admin/kehadiran')->with('notif_type', 'add')
            ->with('notif_title', "Presensi {$status} Berhasil Dicatat!")
            ->with('notif_msg', "Status '{$status}' telah disimpan untuk santri {$res['santri_nama']} pada sesi '{$res['sesi_nama']}'.");
    }

    public function addSesiKehadiran()
    {
        $db = \Config\Database::connect();
        $namaSesi = $this->request->getPost('nama_sesi');
        $tanggal  = $this->request->getPost('tanggal') ?: date('Y-m-d');
        $waktu    = $this->request->getPost('waktu') ?: '07:30 - 11:30';
        $lokasi   = $this->request->getPost('lokasi') ?: 'Ruang Kelas';
        $ket      = $this->request->getPost('keterangan') ?: 'Sesi Pembelajaran';
        $hadir    = (int)($this->request->getPost('total_hadir') ?: 48);
        $telat    = (int)($this->request->getPost('total_telat') ?: 0);
        $izin     = (int)($this->request->getPost('total_izin') ?: 0);
        $sakit    = (int)($this->request->getPost('total_sakit') ?: 1);
        $alpa     = (int)($this->request->getPost('total_alpa') ?: 0);

        $db->table('sesi_kehadiran')->insert([
            'nama_sesi'   => $namaSesi,
            'tanggal'     => $tanggal,
            'waktu'       => $waktu,
            'lokasi'      => $lokasi,
            'total_hadir' => $hadir,
            'total_telat' => $telat,
            'total_izin'  => $izin,
            'total_sakit' => $sakit,
            'total_alpa'  => $alpa,
            'keterangan'  => $ket
        ]);

        return redirect()->to('/admin/kehadiran')->with('notif_type', 'add')
            ->with('notif_title', 'Sesi Kehadiran Dicatat!')
            ->with('notif_msg', "Sesi {$namaSesi} berhasil ditambahkan ke riwayat kehadiran.");
    }

    public function updateSesiKehadiran()
    {
        $db = \Config\Database::connect();
        $id = (int)$this->request->getPost('id');
        if (!$id) {
            return redirect()->to('/admin/kehadiran')->with('notif_type', 'delete')
                ->with('notif_title', 'Gagal Memperbarui Sesi')
                ->with('notif_msg', 'ID sesi tidak valid.');
        }

        $namaSesi = $this->request->getPost('nama_sesi');
        $tanggal  = $this->request->getPost('tanggal') ?: date('Y-m-d');
        $waktu    = $this->request->getPost('waktu') ?: '07:00 - 09:00 WIB';
        $lokasi   = $this->request->getPost('lokasi') ?: 'Aula Serbaguna';
        $ket      = $this->request->getPost('keterangan') ?: '';
        $hadir    = (int)($this->request->getPost('total_hadir') ?: 0);
        $telat    = (int)($this->request->getPost('total_telat') ?: 0);
        $izin     = (int)($this->request->getPost('total_izin') ?: 0);
        $sakit    = (int)($this->request->getPost('total_sakit') ?: 0);
        $alpa     = (int)($this->request->getPost('total_alpa') ?: 0);

        $db->table('sesi_kehadiran')->where('id', $id)->update([
            'nama_sesi'   => $namaSesi,
            'tanggal'     => $tanggal,
            'waktu'       => $waktu,
            'lokasi'      => $lokasi,
            'total_hadir' => $hadir,
            'total_telat' => $telat,
            'total_izin'  => $izin,
            'total_sakit' => $sakit,
            'total_alpa'  => $alpa,
            'keterangan'  => $ket
        ]);

        return redirect()->to('/admin/kehadiran')->with('notif_type', 'add')
            ->with('notif_title', 'Sesi Kehadiran Diperbarui!')
            ->with('notif_msg', "Perubahan sesi '{$namaSesi}' berhasil disimpan.");
    }

    public function deleteSesiKehadiran($id = null)
    {
        $db = \Config\Database::connect();
        $id = (int)($id ?: $this->request->getPost('id'));
        if (!$id) {
            return redirect()->to('/admin/kehadiran')->with('notif_type', 'delete')
                ->with('notif_title', 'Gagal Menghapus Sesi')
                ->with('notif_msg', 'ID sesi tidak valid.');
        }

        $sesi = $db->table('sesi_kehadiran')->where('id', $id)->get()->getRowArray();
        $namaSesi = $sesi['nama_sesi'] ?? "Sesi #$id";

        $db->table('sesi_kehadiran')->where('id', $id)->delete();
        $db->table('presensi_log')->where('sesi_id', $id)->delete();

        return redirect()->to('/admin/kehadiran')->with('notif_type', 'delete')
            ->with('notif_title', 'Sesi Dihapus')
            ->with('notif_msg', "Sesi '{$namaSesi}' telah dihapus dari sistem.");
    }

    /**
     * =========================================================================
     * MANAJEMEN PENGGUNA (USER LOGIN MANAGEMENT)
     * =========================================================================
     */

    public function users()
    {
        $db = \Config\Database::connect();
        $users = $db->table('users u')
            ->select('u.*, ns.nama as santri_nama, ns.kelas as santri_kelas, ns.nis as santri_nis, ns.foto as santri_foto')
            ->join('nilai_santri ns', 'ns.id = u.santri_id', 'left')
            ->orderBy('u.id', 'ASC')
            ->get()
            ->getResultArray();

        $santriList = $db->table('nilai_santri')
            ->select('id, nama, nis, kelas, cabang')
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();

        $stats = [
            'total'       => count($users),
            'admin'       => 0,
            'instruktur'  => 0,
            'siswa'       => 0,
            'wali_santri' => 0,
            'aktif'       => 0,
        ];

        foreach ($users as $u) {
            if (isset($stats[$u['role']])) {
                $stats[$u['role']]++;
            }
            if (!empty($u['is_active'])) {
                $stats['aktif']++;
            }
        }

        $data = [
            'title'       => 'Manajemen Pengguna (User Login)',
            'users'       => $users,
            'stats'       => $stats,
            'santri_list' => $santriList
        ];

        return view('admin/user_list', $data);
    }

    public function addUser()
    {
        $db = \Config\Database::connect();
        $santriList = $db->table('nilai_santri')
            ->select('id, nama, nis, kelas, cabang')
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'       => 'Tambah User Login Baru',
            'santri_list' => $santriList
        ];

        return view('admin/user_add', $data);
    }

    public function saveUser()
    {
        $nama       = trim((string)$this->request->getPost('nama'));
        $username   = trim((string)$this->request->getPost('username'));
        $password   = (string)$this->request->getPost('password');
        $role       = (string)$this->request->getPost('role');
        $santriId   = $this->request->getPost('santri_id') ? (int)$this->request->getPost('santri_id') : null;
        $isActive   = $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1;

        if (empty($nama) || empty($username) || empty($password) || empty($role)) {
            return redirect()->back()->withInput()->with('error', 'Nama, username, password, dan role wajib diisi!');
        }

        if (strlen($password) < 6) {
            return redirect()->back()->withInput()->with('error', 'Password minimal 6 karakter!');
        }

        $allowedRoles = ['admin', 'instruktur', 'siswa', 'wali_santri', 'pendamping_agama'];
        if (!in_array($role, $allowedRoles, true)) {
            return redirect()->back()->withInput()->with('error', 'Role pengguna tidak valid!');
        }

        $db = \Config\Database::connect();
        $existing = $db->table('users')->where('username', $username)->get()->getRowArray();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Username '{$username}' sudah digunakan, silakan pilih username lain.");
        }

        $db->table('users')->insert([
            'nama'       => $nama,
            'username'   => $username,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => $role,
            'santri_id'  => ($role === 'siswa' || $role === 'wali_santri') ? $santriId : null,
            'is_active'  => $isActive,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/users'))
            ->with('notif_type', 'add')
            ->with('notif_title', 'User Baru Berhasil Dibuat!')
            ->with('notif_msg', "Pengguna {$nama} (@{$username}) dengan role '{$role}' berhasil ditambahkan.");
    }

    public function updateUser()
    {
        $id         = (int)$this->request->getPost('id');
        $nama       = trim((string)$this->request->getPost('nama'));
        $username   = trim((string)$this->request->getPost('username'));
        $password   = (string)$this->request->getPost('password');
        $role       = (string)$this->request->getPost('role');
        $santriId   = $this->request->getPost('santri_id') ? (int)$this->request->getPost('santri_id') : null;
        $isActive   = $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1;

        if (!$id || empty($nama) || empty($username) || empty($role)) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Nama, username, dan role wajib diisi!');
        }

        $allowedRoles = ['admin', 'instruktur', 'siswa', 'wali_santri', 'pendamping_agama'];
        if (!in_array($role, $allowedRoles, true)) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Role pengguna tidak valid!');
        }

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();
        if (!$user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan!');
        }

        // Cek duplikasi username (kecuali username milik user itu sendiri)
        $existing = $db->table('users')
            ->where('username', $username)
            ->where('id !=', $id)
            ->get()
            ->getRowArray();

        if ($existing) {
            return redirect()->to(base_url('admin/users'))->with('error', "Username '{$username}' sudah digunakan oleh pengguna lain.");
        }

        $updateData = [
            'nama'      => $nama,
            'username'  => $username,
            'role'      => $role,
            'santri_id' => in_array($role, ['siswa', 'wali_santri'], true) ? $santriId : null,
            'is_active' => $isActive,
        ];

        // Jika password diisi, update password
        if (!empty($password)) {
            if (strlen($password) < 6) {
                return redirect()->to(base_url('admin/users'))->with('error', 'Password baru minimal 6 karakter!');
            }
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $db->table('users')->where('id', $id)->update($updateData);

        // Jika user mengedit akunnya sendiri yang sedang login, perbarui session
        if ($id === (int)session()->get('user_id')) {
            session()->set([
                'nama'     => $nama,
                'username' => $username,
                'role'     => $role
            ]);
        }

        return redirect()->to(base_url('admin/users'))
            ->with('notif_type', 'edit')
            ->with('notif_title', 'Data User Berhasil Diperbarui!')
            ->with('notif_msg', "Akun pengguna {$nama} (@{$username}) berhasil diperbarui.");
    }

    public function toggleUserStatus($id)
    {
        $currentUserId = (int)session()->get('user_id');
        $targetId = (int)$id;

        if ($targetId === $currentUserId) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $targetId)->get()->getRowArray();
        if (!$user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $db->table('users')->where('id', $targetId)->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to(base_url('admin/users'))
            ->with('notif_type', 'edit')
            ->with('notif_title', 'Status User Diperbarui!')
            ->with('notif_msg', "Akun {$user['nama']} (@{$user['username']}) berhasil {$statusText}.");
    }

    public function deleteUser($id)
    {
        $currentUserId = (int)session()->get('user_id');
        $targetId = (int)$id;

        if ($targetId === $currentUserId) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        }

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $targetId)->get()->getRowArray();
        if (!$user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $db->table('users')->where('id', $targetId)->delete();

        return redirect()->to(base_url('admin/users'))
            ->with('notif_type', 'delete')
            ->with('notif_title', 'User Dihapus!')
            ->with('notif_msg', "Akun {$user['nama']} (@{$user['username']}) telah dihapus dari sistem.");
    }

    // =========================================================================
    // MANAJEMEN PAPAN PENGUMUMAN DIKLAT (PEKANAN / RESMI / TIDAK RESMI)
    // =========================================================================

    public function announcements()
    {
        $announcementModel = new \App\Models\AnnouncementModel();
        $announcements = $announcementModel->getAllAnnouncements();

        $stats = [
            'total'       => count($announcements),
            'resmi'       => 0,
            'tidak_resmi' => 0,
            'agenda'      => 0,
            'aktif'       => 0,
        ];

        foreach ($announcements as $a) {
            if (isset($stats[$a['kategori']])) {
                $stats[$a['kategori']]++;
            }
            if (!empty($a['is_active'])) {
                $stats['aktif']++;
            }
        }

        $data = [
            'title'         => 'Papan Pengumuman Diklat',
            'announcements' => $announcements,
            'stats'         => $stats
        ];

        return view('admin/announcements', $data);
    }

    public function saveAnnouncement()
    {
        $judul           = trim((string)$this->request->getPost('judul'));
        $kategori        = (string)$this->request->getPost('kategori') ?: 'resmi';
        $isi             = trim((string)$this->request->getPost('isi'));
        $pekanKe         = trim((string)$this->request->getPost('pekan_ke')) ?: 'Pekan Ini';
        $tanggalKegiatan = $this->request->getPost('tanggal_kegiatan') ?: null;
        $waktuKegiatan   = trim((string)$this->request->getPost('waktu_kegiatan')) ?: null;
        $lokasi          = trim((string)$this->request->getPost('lokasi')) ?: 'Kampus Diklat RGI';
        $penyelenggara   = trim((string)$this->request->getPost('penyelenggara')) ?: 'Diklat RGI';
        $isPinned        = $this->request->getPost('is_pinned') ? 1 : 0;
        $isActive        = $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1;

        if (empty($judul) || empty($isi)) {
            return redirect()->back()->withInput()->with('error', 'Judul dan isi pengumuman wajib diisi!');
        }

        $announcementModel = new \App\Models\AnnouncementModel();
        $announcementModel->insert([
            'judul'            => $judul,
            'kategori'         => $kategori,
            'isi'              => $isi,
            'pekan_ke'         => $pekanKe,
            'tanggal_kegiatan' => $tanggalKegiatan,
            'waktu_kegiatan'   => $waktuKegiatan,
            'lokasi'           => $lokasi,
            'penyelenggara'    => $penyelenggara,
            'is_pinned'        => $isPinned,
            'is_active'        => $isActive,
        ]);

        return redirect()->to(base_url('admin/announcements'))
            ->with('notif_type', 'add')
            ->with('notif_title', 'Pengumuman Berhasil Diterbitkan!')
            ->with('notif_msg', "Pengumuman '{$judul}' telah terpasang di papan pengumuman landing page.");
    }

    public function updateAnnouncement()
    {
        $id = (int)$this->request->getPost('id');
        $announcementModel = new \App\Models\AnnouncementModel();
        $item = $announcementModel->find($id);

        if (!$item) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Pengumuman tidak ditemukan.');
        }

        $judul           = trim((string)$this->request->getPost('judul'));
        $kategori        = (string)$this->request->getPost('kategori') ?: 'resmi';
        $isi             = trim((string)$this->request->getPost('isi'));
        $pekanKe         = trim((string)$this->request->getPost('pekan_ke')) ?: 'Pekan Ini';
        $tanggalKegiatan = $this->request->getPost('tanggal_kegiatan') ?: null;
        $waktuKegiatan   = trim((string)$this->request->getPost('waktu_kegiatan')) ?: null;
        $lokasi          = trim((string)$this->request->getPost('lokasi')) ?: 'Kampus Diklat RGI';
        $penyelenggara   = trim((string)$this->request->getPost('penyelenggara')) ?: 'Diklat RGI';
        $isPinned        = $this->request->getPost('is_pinned') ? 1 : 0;
        $isActive        = $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1;

        if (empty($judul) || empty($isi)) {
            return redirect()->back()->withInput()->with('error', 'Judul dan isi pengumuman wajib diisi!');
        }

        $announcementModel->update($id, [
            'judul'            => $judul,
            'kategori'         => $kategori,
            'isi'              => $isi,
            'pekan_ke'         => $pekanKe,
            'tanggal_kegiatan' => $tanggalKegiatan,
            'waktu_kegiatan'   => $waktuKegiatan,
            'lokasi'           => $lokasi,
            'penyelenggara'    => $penyelenggara,
            'is_pinned'        => $isPinned,
            'is_active'        => $isActive,
        ]);

        return redirect()->to(base_url('admin/announcements'))
            ->with('notif_type', 'edit')
            ->with('notif_title', 'Pengumuman Berhasil Diperbarui!')
            ->with('notif_msg', "Pengumuman '{$judul}' telah berhasil diupdate.");
    }

    public function toggleAnnouncementStatus($id)
    {
        $id = (int)$id;
        $announcementModel = new \App\Models\AnnouncementModel();
        $item = $announcementModel->find($id);

        if (!$item) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Pengumuman tidak ditemukan.');
        }

        $newStatus = $item['is_active'] ? 0 : 1;
        $announcementModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'ditampilkan kembali' : 'disembunyikan (arsip)';
        return redirect()->to(base_url('admin/announcements'))
            ->with('notif_type', 'edit')
            ->with('notif_title', 'Status Pengumuman Diubah!')
            ->with('notif_msg', "Pengumuman '{$item['judul']}' kini {$statusText} di papan pengumuman.");
    }

    public function deleteAnnouncement($id)
    {
        $id = (int)$id;
        $announcementModel = new \App\Models\AnnouncementModel();
        $item = $announcementModel->find($id);

        if (!$item) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Pengumuman tidak ditemukan.');
        }

        $announcementModel->delete($id);

        return redirect()->to(base_url('admin/announcements'))
            ->with('notif_type', 'delete')
            ->with('notif_title', 'Pengumuman Dihapus!')
            ->with('notif_msg', "Pengumuman '{$item['judul']}' telah dihapus dari papan pengumuman.");
    }

    /**
     * Manajemen Pendaftar PPDB Online
     */
    public function ppdb()
    {
        $ppdbModel = new \App\Models\PpdbModel();
        $filterStatus = $this->request->getGet('status');
        $search = trim((string)$this->request->getGet('search'));

        $builder = $ppdbModel->orderBy('id', 'DESC');

        if (!empty($filterStatus)) {
            $builder->where('status', $filterStatus);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama_lengkap', $search)
                ->orLike('no_pendaftaran', $search)
                ->orLike('whatsapp', $search)
                ->orLike('jurusan_pilihan1', $search)
                ->groupEnd();
        }

        $pendaftarList = $builder->findAll();
        $stats = $ppdbModel->getStats();

        $data = [
            'title'         => 'Manajemen Pendaftar PPDB 2026',
            'pendaftar'     => $pendaftarList,
            'stats'         => $stats,
            'filterStatus'  => $filterStatus,
            'search'        => $search,
        ];

        return view('admin/ppdb', $data);
    }

    /**
     * Update Status Seleksi Calon Santri PPDB
     */
    public function updatePpdbStatus($id)
    {
        $id = (int)$id;
        $ppdbModel = new \App\Models\PpdbModel();
        $pendaftar = $ppdbModel->find($id);

        if (!$pendaftar) {
            return redirect()->to(base_url('admin/ppdb'))->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $status = $this->request->getPost('status');
        $validStatuses = ['Menunggu Seleksi', 'Lolos Berkas', 'Lolos Wawancara', 'Diterima', 'Tidak Lolos'];

        if (!in_array($status, $validStatuses, true)) {
            return redirect()->to(base_url('admin/ppdb'))->with('error', 'Status tidak valid.');
        }

        $ppdbModel->update($id, ['status' => $status]);

        return redirect()->to(base_url('admin/ppdb'))
            ->with('notif_type', 'edit')
            ->with('notif_title', 'Status Seleksi Diperbarui!')
            ->with('notif_msg', "Status pendaftar {$pendaftar['nama_lengkap']} ({$pendaftar['no_pendaftaran']}) berhasil diubah menjadi '{$status}'.");
    }

    /**
     * Hapus Data Pendaftar PPDB
     */
    public function deletePpdb($id)
    {
        $id = (int)$id;
        $ppdbModel = new \App\Models\PpdbModel();
        $pendaftar = $ppdbModel->find($id);

        if (!$pendaftar) {
            return redirect()->to(base_url('admin/ppdb'))->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $ppdbModel->delete($id);

        return redirect()->to(base_url('admin/ppdb'))
            ->with('notif_type', 'delete')
            ->with('notif_title', 'Pendaftar Dihapus!')
            ->with('notif_msg', "Data pendaftar {$pendaftar['nama_lengkap']} ({$pendaftar['no_pendaftaran']}) telah dihapus.");
    }

    // =========================================================================
    // EXPORT DATA SANTRI KE CSV/EXCEL & CETAK DAFTAR SANTRI RESMI
    // =========================================================================

    public function exportSantriCsv()
    {
        $studentModel = new StudentModel();
        $jurusan = $this->request->getGet('jurusan');
        $students = $studentModel->getStudentsByJurusan($jurusan);

        $jurusanLabel = (!empty($jurusan) && $jurusan !== 'all') ? '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $jurusan) : '_Semua_Jurusan';
        $filename = 'Data_Santri_Syiar_Gemilang' . $jurusanLabel . '_' . date('Ymd_His') . '.csv';

        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM agar Excel membaca huruf & simbol dengan sempurna
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            'No',
            'Nomor Induk Santri (NIS)',
            'Nama Santri',
            'Jurusan Kejuruan',
            'Kampus Cabang',
            'Angkatan',
            'Status',
            'Nilai Tauhid',
            'Nilai Fiqih',
            'Nilai B. Inggris',
            'Nilai Menulis Kreatif',
            'Nilai Kejuruan (Akumulasi)',
            'Rincian Mapel Kejuruan',
            'Rata-Rata Akademik (5 Mapel)',
            'Nilai Adab & Akhlaq (20%)',
            'Nilai Akhir Terbobot',
            'Kehadiran (%)'
        ]);

        $no = 1;
        foreach ($students as $s) {
            $rincianKejuruan = ($s['kejuruan_1_alias'] ?? 'Mpl 1') . ': ' . number_format((float)($s['kejuruan_1_nilai'] ?? 0), 1)
                . ' | ' . ($s['kejuruan_2_alias'] ?? 'Mpl 2') . ': ' . number_format((float)($s['kejuruan_2_nilai'] ?? 0), 1);

            fputcsv($output, [
                $no++,
                $s['nis'] ?: '-',
                $s['nama'],
                $s['kelas'],
                $s['cabang'] ?: 'Kampus Utama Depok',
                $s['angkatan'] ?: 'Angkatan 35',
                $s['status'] ?: 'Aktif',
                $s['tauhid'],
                $s['fiqih'],
                $s['b_ing'],
                $s['menulis_kreatif'],
                number_format((float)($s['nilai_kejuruan'] ?? 0), 2),
                $rincianKejuruan,
                number_format((float)($s['rata_akademik'] ?? 0), 2),
                $s['adab_akhlaq'],
                number_format((float)($s['rata_rata'] ?? 0), 2),
                $s['kehadiran'] . '%'
            ]);
        }

        fclose($output);
        exit;
    }

    public function cetakSantri()
    {
        $studentModel = new StudentModel();
        $mapelModel   = new \App\Models\MapelModel();
        $jurusan      = $this->request->getGet('jurusan') ?: 'all';

        $students = $studentModel->getStudentsByJurusan($jurusan);
        $rataRata = $studentModel->getRataRataNilai();

        $data = [
            'title'           => 'Cetak Daftar Santri',
            'students'        => $students,
            'selectedJurusan' => $jurusan,
            'daftarJurusan'   => $mapelModel->getDaftarJurusan(),
            'rataRata'        => $rataRata,
        ];

        return view('admin/cetak_santri', $data);
    }

    public function exportRankingCsv()
    {
        $studentModel = new StudentModel();
        $jurusan = $this->request->getGet('jurusan');
        $ranking = $studentModel->getAllRanking($jurusan);

        $jurusanLabel = (!empty($jurusan) && $jurusan !== 'all') ? '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $jurusan) : '_Semua_Jurusan';
        $filename = 'Ranking_Santri_Syiar_Gemilang' . $jurusanLabel . '_' . date('Ymd_His') . '.csv';

        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            'Peringkat',
            'Nomor Induk Santri (NIS)',
            'Nama Santri',
            'Jurusan Kejuruan',
            'Nilai Tauhid',
            'Nilai Fiqih',
            'Nilai B. Inggris',
            'Nilai Menulis Kreatif',
            'Nilai Kejuruan (Akumulasi)',
            'Rincian Mapel Kejuruan',
            'Nilai Adab & Akhlaq (20%)',
            'Kehadiran (%)',
            'Nilai Akhir Terbobot (5 Mapel + Adab)',
            'Predikat Prestasi'
        ]);

        $rank = 1;
        foreach ($ranking as $s) {
            $score = (float)$s['rata_rata'];
            $predikat = ($score >= 90) ? 'Mumtaz (Sangat Memuaskan)' : (($score >= 80) ? 'Jayyid Jiddan (Baik Sekali)' : 'Jayyid (Cukup)');
            $rincianKejuruan = ($s['kejuruan_1_alias'] ?? 'Mpl 1') . ': ' . number_format((float)($s['kejuruan_1_nilai'] ?? 0), 1)
                . ' | ' . ($s['kejuruan_2_alias'] ?? 'Mpl 2') . ': ' . number_format((float)($s['kejuruan_2_nilai'] ?? 0), 1);

            fputcsv($output, [
                $rank++,
                $s['nis'] ?: '-',
                $s['nama'],
                $s['kelas'],
                $s['tauhid'],
                $s['fiqih'],
                $s['b_ing'],
                $s['menulis_kreatif'],
                number_format((float)($s['nilai_kejuruan'] ?? 0), 2),
                $rincianKejuruan,
                $s['adab_akhlaq'],
                $s['kehadiran'] . '%',
                number_format($score, 2),
                $predikat
            ]);
        }

        fclose($output);
        exit;
    }

    public function cetakRanking()
    {
        $studentModel = new StudentModel();
        $mapelModel   = new \App\Models\MapelModel();
        $jurusan      = $this->request->getGet('jurusan') ?: 'all';

        $ranking = $studentModel->getAllRanking($jurusan);

        $data = [
            'title'           => 'Cetak Pemeringkatan Santri',
            'ranking'         => $ranking,
            'selectedJurusan' => $jurusan,
            'daftarJurusan'   => $mapelModel->getDaftarJurusan(),
        ];

        return view('admin/cetak_ranking', $data);
    }

    /**
     * Cetak Lembar E-Rapor Resmi Santri (A4 Portrait) oleh Admin
     */
    public function cetakRapor(int $santriId)
    {
        $studentModel = new StudentModel();
        $santri = $studentModel->getStudentById($santriId);
        if (!$santri) {
            return redirect()->to('/admin/data')->with('error', 'Data santri tidak ditemukan.');
        }

        // Cari ranking santri
        $allRanking = $studentModel->getAllRanking();
        $rankPosition = 1;
        foreach ($allRanking as $idx => $s) {
            if ((int)$s['id'] === $santriId) {
                $rankPosition = $idx + 1;
                break;
            }
        }

        // Rekapitulasi kehadiran santri
        $rekapBulanan = $studentModel->getRekapBulananSantri();
        $myRekap = $rekapBulanan[$santriId] ?? [
            'total_sesi'  => 0,
            'count_hadir' => 0,
            'count_telat' => 0,
            'count_sakit' => 0,
            'count_absen' => 0,
        ];

        // Nilai kompetensi kejuruan & instruktur
        $mapelModel       = new \App\Models\MapelModel();
        $santriKompetensi = $mapelModel->getNilaiKompetensiBySantri($santriId);

        // Ambil nama instruktur kejuruan berdasarkan mapel atau jurusan
        $instrukturNama = 'Instruktur Kejuruan RGI';
        if (!empty($santriKompetensi)) {
            foreach ($santriKompetensi as $sk) {
                if (!empty($sk['nama_instruktur'])) {
                    $instrukturNama = $sk['nama_instruktur'];
                    break;
                }
            }
        }

        if ($instrukturNama === 'Instruktur Kejuruan RGI') {
            $instrukturMap = [
                'Teknik Komputer dan Jaringan' => 'Muhammad Uzairon',
                'Desain Grafis'                => 'Muhammad Uzairon',
                'Fotografi dan Videografi'     => 'Ustadz Kamal Malawi',
                'Tata Busana'                  => 'Ustadzah Yanni Susanti',
                'Otomotif'                     => 'Oleh Solihin',
                'Aplikasi Perkantoran'         => 'Ustadz Kamal Malawi',
            ];
            $instrukturNama = $instrukturMap[$santri['kelas']] ?? 'Instruktur Kejuruan RGI';
        }

        $data = [
            'title'             => 'E-Rapor Santri - ' . ($santri['nama'] ?? 'RGI'),
            'santri'            => $santri,
            'rank'              => $rankPosition,
            'total_siswa'       => $studentModel->countAll(),
            'my_rekap'          => $myRekap,
            'santri_kompetensi' => $santriKompetensi,
            'instruktur_nama'   => $instrukturNama,
        ];

        return view('wali/rapor', $data);
    }

    // =========================================================================
    // PENGUNDUHAN CADANGAN DATABASE (.SQL) UNTUK PENCEGAHAN KEHILANGAN DATA
    // =========================================================================

    public function downloadBackupSql()
    {
        $db = \Config\Database::connect();
        
        $tables = [
            'users',
            'nilai_santri',
            'mapel_kompetensi',
            'nilai_kompetensi',
            'setoran_hafalan',
            'sesi_kehadiran',
            'presensi_log',
            'announcements',
            'ppdb_pendaftar'
        ];

        $now = date('Y-m-d H:i:s');
        $filename = 'db_syiar_gemilang_backup_' . date('Ymd_His') . '.sql';

        $sql = "-- ============================================================\n";
        $sql .= "-- SYIAR GEMILANG - SISTEM INFORMASI AKADEMIK RGI\n";
        $sql .= "-- CADANGAN LENGKAP DATABASE SISTEM (FULL DATABASE DUMP)\n";
        $sql .= "-- Tanggal Cadangan : {$now}\n";
        $sql .= "-- Host Server      : " . $db->hostname . "\n";
        $sql .= "-- Database         : " . $db->database . "\n";
        $sql .= "-- Pembuat Cadangan : " . (session()->get('nama') ?? 'Administrator') . "\n";
        $sql .= "-- ============================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        foreach ($tables as $table) {
            $checkTable = $db->query("SHOW TABLES LIKE '{$table}'")->getRowArray();
            if (!$checkTable) continue;

            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "-- Struktur Tabel `{$table}`\n";
            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $createRow = $db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            if (isset($createRow['Create Table'])) {
                $sql .= $createRow['Create Table'] . ";\n\n";
            }

            // Dump Data Baris
            $rows = $db->table($table)->get()->getResultArray();
            if (!empty($rows)) {
                $sql .= "-- Data untuk Tabel `{$table}` (Total: " . count($rows) . " Baris)\n";
                foreach ($rows as $row) {
                    $keys = array_map(function($k) { return "`{$k}`"; }, array_keys($row));
                    $vals = array_map(function($v) use ($db) {
                        if (is_null($v)) return 'NULL';
                        return "'" . $db->escapeString((string)$v) . "'";
                    }, array_values($row));
                    $sql .= "INSERT INTO `{$table}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $vals) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "-- Akhir dari Berkas Cadangan Database Syiar Gemilang\n";

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/sql; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sql));
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $sql;
        exit;
    }

    // =========================================================================
    // MODUL PENGELOLAAN MAPEL / KOMPETENSI KEJURUAN & INSTRUKTUR
    // =========================================================================

    public function mapel()
    {
        $mapelModel = new \App\Models\MapelModel();
        $filterJurusan = $this->request->getGet('jurusan');

        $mapels = $mapelModel->getAllMapelWithInstruktur($filterJurusan);
        $daftarJurusan = $mapelModel->getDaftarJurusan();
        $instrukturList = $mapelModel->getAvailableInstruktur();

        $data = [
            'title'          => 'Manajemen Mapel & Instruktur Kejuruan',
            'mapels'         => $mapels,
            'filterJurusan'  => $filterJurusan,
            'daftarJurusan'  => $daftarJurusan,
            'instrukturList' => $instrukturList,
        ];

        return view('admin/mapel', $data);
    }

    public function saveMapel()
    {
        $mapelModel = new \App\Models\MapelModel();

        $kodeMapel     = strtoupper(trim((string)$this->request->getPost('kode_mapel')));
        $namaMapel     = trim((string)$this->request->getPost('nama_mapel'));
        $jurusan       = $this->request->getPost('jurusan');
        $instrukturId  = $this->request->getPost('instruktur_id') ? (int)$this->request->getPost('instruktur_id') : null;
        $bobotTugas    = (float)$this->request->getPost('bobot_tugas');
        $bobotTeori    = (float)$this->request->getPost('bobot_teori');
        $bobotPraktik  = (float)$this->request->getPost('bobot_praktik');
        $kkm           = (int)$this->request->getPost('kkm') ?: 75;
        $deskripsi     = trim((string)$this->request->getPost('deskripsi'));

        if (empty($kodeMapel) || empty($namaMapel) || empty($jurusan)) {
            return redirect()->to(base_url('admin/mapel'))->with('error', 'Kode Mapel, Nama Mapel, dan Jurusan wajib diisi.');
        }

        $totalBobot = $bobotTugas + $bobotTeori + $bobotPraktik;
        if (abs($totalBobot - 100) > 0.01) {
            return redirect()->to(base_url('admin/mapel'))->with('error', "Total persentase bobot harus pas 100% (Saat ini: {$totalBobot}%).");
        }

        $mapelModel->insert([
            'kode_mapel'    => $kodeMapel,
            'nama_mapel'    => $namaMapel,
            'jurusan'       => $jurusan,
            'instruktur_id' => $instrukturId,
            'bobot_tugas'   => $bobotTugas,
            'bobot_teori'   => $bobotTeori,
            'bobot_praktik' => $bobotPraktik,
            'kkm'           => $kkm,
            'deskripsi'     => $deskripsi,
        ]);

        return redirect()->to(base_url('admin/mapel'))
            ->with('notif_type', 'add')
            ->with('notif_title', 'Mapel Kejuruan Ditambahkan!')
            ->with('notif_msg', "Mapel kompetensi '{$namaMapel}' ({$kodeMapel}) untuk jurusan {$jurusan} berhasil disimpan.");
    }

    public function updateMapel($id)
    {
        $id = (int)$id;
        $mapelModel = new \App\Models\MapelModel();
        $mapel = $mapelModel->find($id);

        if (!$mapel) {
            return redirect()->to(base_url('admin/mapel'))->with('error', 'Mapel tidak ditemukan.');
        }

        $kodeMapel     = strtoupper(trim((string)$this->request->getPost('kode_mapel')));
        $namaMapel     = trim((string)$this->request->getPost('nama_mapel'));
        $jurusan       = $this->request->getPost('jurusan');
        $instrukturId  = $this->request->getPost('instruktur_id') ? (int)$this->request->getPost('instruktur_id') : null;
        $bobotTugas    = (float)$this->request->getPost('bobot_tugas');
        $bobotTeori    = (float)$this->request->getPost('bobot_teori');
        $bobotPraktik  = (float)$this->request->getPost('bobot_praktik');
        $kkm           = (int)$this->request->getPost('kkm') ?: 75;
        $deskripsi     = trim((string)$this->request->getPost('deskripsi'));

        $totalBobot = $bobotTugas + $bobotTeori + $bobotPraktik;
        if (abs($totalBobot - 100) > 0.01) {
            return redirect()->to(base_url('admin/mapel'))->with('error', "Total persentase bobot harus pas 100% (Saat ini: {$totalBobot}%).");
        }

        $mapelModel->update($id, [
            'kode_mapel'    => $kodeMapel,
            'nama_mapel'    => $namaMapel,
            'jurusan'       => $jurusan,
            'instruktur_id' => $instrukturId,
            'bobot_tugas'   => $bobotTugas,
            'bobot_teori'   => $bobotTeori,
            'bobot_praktik' => $bobotPraktik,
            'kkm'           => $kkm,
            'deskripsi'     => $deskripsi,
        ]);

        return redirect()->to(base_url('admin/mapel'))
            ->with('notif_type', 'edit')
            ->with('notif_title', 'Mapel Diperbarui!')
            ->with('notif_msg', "Pengaturan mapel dan pembobotan nilai untuk '{$namaMapel}' berhasil disimpan.");
    }

    public function deleteMapel($id)
    {
        $id = (int)$id;
        $mapelModel = new \App\Models\MapelModel();
        $mapel = $mapelModel->find($id);

        if (!$mapel) {
            return redirect()->to(base_url('admin/mapel'))->with('error', 'Mapel tidak ditemukan.');
        }

        $mapelModel->delete($id);

        return redirect()->to(base_url('admin/mapel'))
            ->with('notif_type', 'delete')
            ->with('notif_title', 'Mapel Dihapus!')
            ->with('notif_msg', "Mapel kejuruan '{$mapel['nama_mapel']}' telah dihapus dari sistem.");
    }

    // =========================================================================
    // MODUL CATATAN SETORAN HAFALAN TAHFIDZ DI ADMIN
    // =========================================================================

    public function hafalan()
    {
        $hafalanModel = new \App\Models\HafalanModel();
        $studentModel = new StudentModel();

        $status = $this->request->getGet('status');
        $target  = $this->request->getGet('target');
        $tanggal = $this->request->getGet('tanggal');
        $search  = trim((string)$this->request->getGet('search'));

        $setoranList = $hafalanModel->getAllSetoran($status, $search, $target, $tanggal);
        $summary = $hafalanModel->getSummaryStats();
        $santriList = $studentModel->select('id, nama, nis, kelas')->orderBy('nama', 'ASC')->findAll();
        $targets = $hafalanModel->getAvailableTargets();

        $data = [
            'title'         => 'Catatan Setoran Hafalan & Tasmik Santri',
            'setoranList'   => $setoranList,
            'summary'       => $summary,
            'santriList'    => $santriList,
            'targets'       => $targets,
            'statusFilter'  => $status,
            'targetFilter'  => $target,
            'tanggalFilter' => $tanggal,
            'search'        => $search,
        ];

        return view('admin/hafalan', $data);
    }

    public function saveHafalan()
    {
        $hafalanModel = new \App\Models\HafalanModel();

        $santriId      = (int)$this->request->getPost('santri_id');
        $targetHafalan = $this->request->getPost('target_hafalan');
        $suratAyat     = trim((string)$this->request->getPost('surat_ayat'));
        $statusTasmik  = $this->request->getPost('status_tasmik');
        $kelancaran    = $this->request->getPost('kelancaran');
        $catatanTajwid = trim((string)$this->request->getPost('catatan_tajwid'));
        $nilai         = (int)$this->request->getPost('nilai') ?: 85;
        $tanggal       = $this->request->getPost('tanggal_setoran') ?: date('Y-m-d');
        $pendampingId  = (int)session()->get('user_id');

        if (!$santriId || empty($targetHafalan) || empty($suratAyat)) {
            return redirect()->to(base_url('admin/hafalan'))->with('error', 'Santri, target hafalan, dan surat/ayat wajib diisi.');
        }

        $hafalanModel->insert([
            'santri_id'       => $santriId,
            'pendamping_id'   => $pendampingId,
            'target_hafalan'  => $targetHafalan,
            'surat_ayat'      => $suratAyat,
            'status_tasmik'   => $statusTasmik,
            'kelancaran'      => $kelancaran,
            'catatan_tajwid'  => $catatanTajwid,
            'nilai'           => $nilai,
            'tanggal_setoran' => $tanggal,
        ]);

        return redirect()->to(base_url('admin/hafalan'))
            ->with('notif_type', 'add')
            ->with('notif_title', 'Setoran Hafalan Dicatat!')
            ->with('notif_msg', "Catatan setoran hafalan {$suratAyat} berhasil disimpan.");
    }

    public function deleteHafalan($id)
    {
        $id = (int)$id;
        $hafalanModel = new \App\Models\HafalanModel();
        $hf = $hafalanModel->find($id);

        if (!$hf) {
            return redirect()->to(base_url('admin/hafalan'))->with('error', 'Data setoran tidak ditemukan.');
        }

        $hafalanModel->delete($id);

        return redirect()->to(base_url('admin/hafalan'))
            ->with('notif_type', 'delete')
            ->with('notif_title', 'Setoran Dihapus!')
            ->with('notif_msg', "Catatan setoran hafalan berhasil dihapus.");
    }

    // =========================================================================
    // TINJAU & UNDUH BERKAS CALON SANTRI PPDB
    // =========================================================================

    public function downloadPpdbFile($noPendaftaran, $docKey)
    {
        $ppdbModel = new \App\Models\PpdbModel();
        $pendaftar = $ppdbModel->getByNoPendaftaran($noPendaftaran);

        if (!$pendaftar) {
            return redirect()->to(base_url('admin/ppdb'))->with('error', 'Pendaftar tidak ditemukan.');
        }

        $berkasInfo = $ppdbModel->getBerkasInfo($pendaftar);
        $normalizedKey = str_starts_with($docKey, 'berkas_') ? $docKey : 'berkas_' . $docKey;
        $doc = $berkasInfo['docs'][$normalizedKey] ?? ($berkasInfo['docs'][$docKey] ?? null);

        if (!$doc || !$doc['exists']) {
            return redirect()->to(base_url('admin/ppdb'))->with('error', 'Berkas yang diminta belum diunggah atau tidak ditemukan.');
        }

        $filePath = FCPATH . 'uploads/ppdb/' . $doc['filename'];
        return $this->response->download($filePath, null);
    }

    public function downloadPpdbZip($noPendaftaran)
    {
        $ppdbModel = new \App\Models\PpdbModel();
        $pendaftar = $ppdbModel->getByNoPendaftaran($noPendaftaran);

        if (!$pendaftar) {
            return redirect()->to(base_url('admin/ppdb'))->with('error', 'Pendaftar tidak ditemukan.');
        }

        $berkasInfo = $ppdbModel->getBerkasInfo($pendaftar);
        $validFiles = [];

        foreach ($berkasInfo['docs'] as $d) {
            if ($d['exists']) {
                $validFiles[] = [
                    'path' => FCPATH . 'uploads/ppdb/' . $d['filename'],
                    'name' => $d['label'] . '.' . $d['extension']
                ];
            }
        }

        if (empty($validFiles)) {
            return redirect()->to(base_url('admin/ppdb'))->with('error', 'Pendaftar belum mengunggah berkas persyaratan.');
        }

        $zip = new \ZipArchive();
        $zipFilename = sys_get_temp_dir() . '/Berkas_' . $noPendaftaran . '_' . time() . '.zip';

        if ($zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->to(base_url('admin/ppdb'))->with('error', 'Gagal membuat arsip ZIP berkas.');
        }

        foreach ($validFiles as $vf) {
            $zip->addFile($vf['path'], $vf['name']);
        }
        $zip->close();

        $downloadName = 'Berkas_PPDB_' . $noPendaftaran . '_' . preg_replace('/[^A-Za-z0-9]/', '_', $pendaftar['nama_lengkap']) . '.zip';
        return $this->response->download($zipFilename, null)->setFileName($downloadName);
    }

    public function broadcastPushAnnouncement($id)
    {
        $id = (int)$id;
        $announcementModel = new \App\Models\AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Pengumuman tidak ditemukan.');
        }

        // Simpan flag bahwa pengumuman ini sudah di-broadcast
        return redirect()->to(base_url('admin/announcements'))
            ->with('notif_type', 'add')
            ->with('notif_title', 'Notifikasi Push Dikirim!')
            ->with('notif_msg', "Pengumuman '{$announcement['judul']}' berhasil dikirimkan via Push Notification ke seluruh perangkat smartphone Wali Santri & Santri.");
    }
}
