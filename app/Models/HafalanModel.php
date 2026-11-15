<?php

namespace App\Models;

use CodeIgniter\Model;

class HafalanModel extends Model
{
    protected $table         = 'setoran_hafalan';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'santri_id',
        'pendamping_id',
        'target_hafalan',
        'surat_ayat',
        'status_tasmik',
        'kelancaran',
        'catatan_tajwid',
        'nilai',
        'tanggal_setoran',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;

    /**
     * Mengambil semua data setoran hafalan dengan relasi santri dan pendamping agama
     */
    public function getAllSetoran(?string $filterStatus = null, ?string $search = null, ?string $filterTarget = null, ?string $filterTanggal = null)
    {
        $builder = $this->db->table('setoran_hafalan h')
            ->select('h.*, s.nama as nama_santri, s.nis, s.kelas as jurusan, s.foto as foto_santri, u.nama as nama_pendamping')
            ->join('nilai_santri s', 's.id = h.santri_id', 'inner')
            ->join('users u', 'u.id = h.pendamping_id', 'left')
            ->orderBy('h.tanggal_setoran', 'DESC')
            ->orderBy('h.id', 'DESC');

        if (!empty($filterStatus) && $filterStatus !== 'all') {
            $builder->where('h.status_tasmik', $filterStatus);
        }

        if (!empty($filterTarget) && $filterTarget !== 'all') {
            $builder->like('h.target_hafalan', $filterTarget);
        }

        if (!empty($filterTanggal) && $filterTanggal !== 'all') {
            $builder->where('h.tanggal_setoran', $filterTanggal);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('s.nama', $search)
                ->orLike('s.nis', $search)
                ->orLike('s.kelas', $search)
                ->orLike('h.surat_ayat', $search)
                ->orLike('h.catatan_tajwid', $search)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil riwayat setoran hafalan untuk santri tertentu
     */
    public function getSetoranBySantriId(int $santriId)
    {
        return $this->db->table('setoran_hafalan h')
            ->select('h.*, u.nama as nama_pendamping')
            ->join('users u', 'u.id = h.pendamping_id', 'left')
            ->where('h.santri_id', $santriId)
            ->orderBy('h.tanggal_setoran', 'DESC')
            ->orderBy('h.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Statistik ringkasan hafalan santri
     */
    public function getSummaryStats()
    {
        $totalSetoran = $this->countAll();
        
        $lulusTasmik = $this->where('status_tasmik', 'Lulus Tasmik')->countAllResults();
        $sedangProses = $this->where('status_tasmik', 'Sedang Proses')->countAllResults();
        $perluUlang = $this->where('status_tasmik', 'Perlu Mengulang')->countAllResults();

        // Rata-rata nilai hafalan
        $avgQuery = $this->selectAvg('nilai', 'avg_nilai')->get()->getRowArray();
        $avgNilai = round((float)($avgQuery['avg_nilai'] ?? 0), 1);

        // Total santri unik yang sudah setor
        $santriAktif = $this->db->table('setoran_hafalan')
            ->select('COUNT(DISTINCT santri_id) as total')
            ->get()
            ->getRowArray()['total'] ?? 0;

        return [
            'total_setoran' => $totalSetoran,
            'lulus_tasmik'  => $lulusTasmik,
            'sedang_proses' => $sedangProses,
            'perlu_ulang'   => $perluUlang,
            'avg_nilai'     => $avgNilai,
            'santri_aktif'  => $santriAktif,
        ];
    }

    /**
     * Ambil target-target hafalan umum yang tersedia
     */
    public function getAvailableTargets()
    {
        return [
            'Juz 30',
            'Surat Pilihan: Al-Mulk',
            'Surat Pilihan: Ar-Rahman',
            'Surat Pilihan: Al-Waqi\'ah',
            'Surat Pilihan: Ya-Sin',
            'Surat Pilihan: Al-Kahfi',
            'Surat Pilihan: As-Sajdah',
            'Surat Pilihan: Al-Insan',
        ];
    }
}
