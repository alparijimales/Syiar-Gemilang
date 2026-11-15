<?php

namespace App\Controllers;

use App\Models\StudentModel;

class Wali extends BaseController
{
    public function index()
    {
        $studentModel = new StudentModel();
        $santriId = (int)session()->get('santri_id');

        // Ambil data santri (anak dari wali) dengan formula terbobot 5 mapel + detail kejuruan
        $santri = $studentModel->getStudentById($santriId);

        if (!$santri) {
            return redirect()->to('/login')->with('error', 'Data santri putra/putri Anda tidak ditemukan.');
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

        $db = \Config\Database::connect();
        // Ambil log kehadiran santri
        $myLogs = $db->table('presensi_log p')
                     ->select('p.*, s.nama_sesi, s.waktu, s.lokasi, s.tanggal as sesi_tanggal')
                     ->join('sesi_kehadiran s', 's.id = p.sesi_id', 'left')
                     ->where('p.santri_id', $santriId)
                     ->orderBy('p.tanggal', 'DESC')
                     ->orderBy('p.id', 'DESC')
                     ->get()
                     ->getResultArray();

        // Rekapitulasi presensi
        $rekapBulanan = $studentModel->getRekapBulananSantri();
        $myRekap = $rekapBulanan[$santriId] ?? [
            'total_sesi'  => count($myLogs),
            'count_hadir' => 0,
            'count_telat' => 0,
            'count_sakit' => 0,
            'count_absen' => 0,
        ];

        // Catatan Setoran Hafalan & Kompetensi Kejuruan
        $hafalanModel = new \App\Models\HafalanModel();
        $mapelModel   = new \App\Models\MapelModel();
        $announcementModel = new \App\Models\AnnouncementModel();

        $santriHafalan     = $hafalanModel->getSetoranBySantriId($santriId);
        $santriKompetensi  = $mapelModel->getNilaiKompetensiBySantri($santriId);
        $announcements     = $announcementModel->getActiveAnnouncements(5);

        $data = [
            'title'              => 'Portal Wali Santri',
            'santri'             => $santri,
            'rank'               => $rankPosition,
            'total_siswa'        => $studentModel->countAll(),
            'my_logs'            => $myLogs,
            'my_rekap'           => $myRekap,
            'santri_hafalan'     => $santriHafalan,
            'santri_kompetensi'  => $santriKompetensi,
            'announcements'      => $announcements,
        ];

        return view('wali/dashboard', $data);
    }

    /**
     * Halaman Cetak & Download E-Rapor Resmi Santri (A4 Portrait)
     */
    public function rapor()
    {
        $studentModel = new StudentModel();
        $santriId = (int)session()->get('santri_id');

        // Dukung preview langsung jika admin membuka halaman ini
        if (session()->get('role') === 'admin' && $this->request->getGet('id')) {
            $santriId = (int)$this->request->getGet('id');
        }

        $santri = $studentModel->getStudentById($santriId);
        if (!$santri) {
            return redirect()->to('/wali')->with('error', 'Data santri putra/putri Anda tidak ditemukan.');
        }

        // Peringkat santri
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

        // Fallback mapping nama instruktur per jurusan
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
}

