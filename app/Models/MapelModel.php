<?php 

namespace App\Models;

use CodeIgniter\Model;

class MapelModel extends Model
{
    protected $table         = 'mapel_kompetensi';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'kode_mapel',
        'nama_mapel',
        'jurusan',
        'instruktur_id',
        'bobot_tugas',
        'bobot_teori',
        'bobot_praktik',
        'kkm',
        'deskripsi',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;

    /**
     * Mengambil semua mapel kompetensi beserta info instruktur pengampu
     */
    public function getAllMapelWithInstruktur(?string $jurusan = null)
    {
        $builder = $this->db->table('mapel_kompetensi m')
            ->select('m.*, u.nama as nama_instruktur, u.username as username_instruktur')
            ->join('users u', 'u.id = m.instruktur_id', 'left')
            ->orderBy('m.jurusan', 'ASC')
            ->orderBy('m.kode_mapel', 'ASC');

        if (!empty($jurusan) && $jurusan !== 'all') {
            $builder->where('m.jurusan', $jurusan);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil mapel yang diampu oleh instruktur tertentu
     */
    public function getMapelByInstruktur(int $instrukturId)
    {
        return $this->db->table('mapel_kompetensi m')
            ->select('m.*, u.nama as nama_instruktur')
            ->join('users u', 'u.id = m.instruktur_id', 'left')
            ->where('m.instruktur_id', $instrukturId)
            ->orderBy('m.jurusan', 'ASC')
            ->orderBy('m.kode_mapel', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Mengambil daftar santri beserta nilai kompetensi pada mapel tertentu
     */
    public function getSantriNilaiByMapel(int $mapelId)
    {
        $mapel = $this->find($mapelId);
        if (!$mapel) {
            return [];
        }

        // Ambil santri di jurusan mapel tersebut
        return $this->db->table('nilai_santri s')
            ->select('s.id as santri_id, s.nama, s.nis, s.kelas, s.foto, nk.id as nilai_id, 
                      COALESCE(nk.nilai_tugas, 0) as nilai_tugas, 
                      COALESCE(nk.nilai_teori, 0) as nilai_teori, 
                      COALESCE(nk.nilai_praktik, 0) as nilai_praktik, 
                      COALESCE(nk.nilai_akhir, 0) as nilai_akhir, 
                      nk.catatan')
            ->join('nilai_kompetensi nk', 'nk.santri_id = s.id AND nk.mapel_id = ' . (int)$mapelId, 'left')
            ->where('s.kelas', $mapel['jurusan'])
            ->orderBy('s.nama', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Hitung dan simpan nilai kompetensi santri dengan bobot dinamis
     */
    public function saveOrUpdateNilaiKompetensi(int $santriId, int $mapelId, float $tugas, float $teori, float $praktik, string $catatan = '')
    {
        $mapel = $this->find($mapelId);
        if (!$mapel) {
            return false;
        }

        $bTugas   = (float)$mapel['bobot_tugas'];
        $bTeori   = (float)$mapel['bobot_teori'];
        $bPraktik = (float)$mapel['bobot_praktik'];
        $totalB   = ($bTugas + $bTeori + $bPraktik) ?: 100;

        // Formula nilai akhir terbobot
        $nilaiAkhir = round((($tugas * $bTugas) + ($teori * $bTeori) + ($praktik * $bPraktik)) / $totalB, 2);

        $existing = $this->db->table('nilai_kompetensi')
            ->where('santri_id', $santriId)
            ->where('mapel_id', $mapelId)
            ->get()
            ->getRowArray();

        $data = [
            'santri_id'     => $santriId,
            'mapel_id'      => $mapelId,
            'nilai_tugas'   => $tugas,
            'nilai_teori'   => $teori,
            'nilai_praktik' => $praktik,
            'nilai_akhir'   => $nilaiAkhir,
            'catatan'       => $catatan,
        ];

        if ($existing) {
            $this->db->table('nilai_kompetensi')->where('id', $existing['id'])->update($data);
        } else {
            $this->db->table('nilai_kompetensi')->insert($data);
        }

        // Sinkronisasi nilai akumulasi kejuruan ke tabel nilai_santri
        $avgRow = $this->db->table('nilai_kompetensi')
            ->select('ROUND(AVG(nilai_akhir), 2) as avg_akhir')
            ->where('santri_id', $santriId)
            ->get()
            ->getRowArray();

        if ($avgRow && isset($avgRow['avg_akhir'])) {
            $this->db->table('nilai_santri')->where('id', $santriId)->update([
                'nilai_kejuruan' => (float)$avgRow['avg_akhir']
            ]);
        }

        return $nilaiAkhir;
    }

    /**
     * Ambil rincian nilai kompetensi kejuruan seorang santri
     */
    public function getNilaiKompetensiBySantri(int $santriId)
    {
        return $this->db->table('nilai_kompetensi nk')
            ->select('nk.*, m.kode_mapel, m.nama_mapel, m.jurusan, m.bobot_tugas, m.bobot_teori, m.bobot_praktik, m.kkm, u.nama as nama_instruktur')
            ->join('mapel_kompetensi m', 'm.id = nk.mapel_id', 'inner')
            ->join('users u', 'u.id = m.instruktur_id', 'left')
            ->where('nk.santri_id', $santriId)
            ->orderBy('m.kode_mapel', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar semua instruktur yang tersedia untuk di-assign
     */
    public function getAvailableInstruktur()
    {
        return $this->db->table('users')
            ->select('id, username, nama')
            ->whereIn('role', ['instruktur', 'admin'])
            ->where('is_active', 1)
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar jurusan yang ada
     */
    public function getDaftarJurusan()
    {
        return [
            'Teknik Komputer dan Jaringan',
            'Tata Busana',
            'Fotografi dan Videografi',
            'Desain Grafis',
            'Otomotif',
            'Aplikasi Perkantoran'
        ];
    }
}
