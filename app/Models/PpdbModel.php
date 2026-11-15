<?php

namespace App\Models;

use CodeIgniter\Model;

class PpdbModel extends Model
{
    protected $table            = 'ppdb_pendaftar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_pendaftaran',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'whatsapp',
        'email',
        'alamat',
        'kota',
        'jurusan_pilihan1',
        'jurusan_pilihan2',
        'cabang_kampus',
        'pendidikan_terakhir',
        'asal_sekolah',
        'nama_wali',
        'whatsapp_wali',
        'motivasi',
        'berkas_ijazah',
        'berkas_ktp',
        'berkas_kk',
        'berkas_sktm',
        'berkas_sehat',
        'berkas_vaksin',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate Nomor Pendaftaran Unik (Contoh: PPDB-36-004)
     */
    public function generateNoPendaftaran(int $angkatan = 36): string
    {
        $prefix = "PPDB-{$angkatan}-";
        $last = $this->like('no_pendaftaran', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        if (!$last) {
            return $prefix . '001';
        }

        $lastNo = $last['no_pendaftaran'];
        $num = (int)str_replace($prefix, '', $lastNo);
        $nextNum = str_pad((string)($num + 1), 3, '0', STR_PAD_LEFT);

        return $prefix . $nextNum;
    }

    /**
     * Ambil data berdasarkan nomor pendaftaran
     */
    public function getByNoPendaftaran(string $noPendaftaran): ?array
    {
        return $this->where('no_pendaftaran', $noPendaftaran)->first();
    }

    /**
     * Hitung statistik ringkas untuk admin / info
     */
    public function getStats(): array
    {
        return [
            'total'            => $this->countAllResults(),
            'menunggu'         => $this->where('status', 'Menunggu Seleksi')->countAllResults(false),
            'lolos_berkas'     => $this->where('status', 'Lolos Berkas')->countAllResults(false),
            'lolos_wawancara'  => $this->where('status', 'Lolos Wawancara')->countAllResults(false),
            'diterima'         => $this->where('status', 'Diterima')->countAllResults(false),
        ];
    }

    /**
     * Mengambil daftar berkas calon santri secara lengkap beserta status ketersediaannya di disk
     */
    public function getBerkasInfo(array $pendaftar): array
    {
        $docDefinitions = [
            'berkas_ijazah' => [
                'label'       => 'Ijazah / SKL Terakhir',
                'description' => 'Fotokopi Ijazah / SKL pendidikan terakhir',
                'icon'        => 'fa-graduation-cap',
            ],
            'berkas_ktp' => [
                'label'       => 'KTP Calon Santri',
                'description' => 'Kartu Tanda Penduduk / Kartu Pelajar',
                'icon'        => 'fa-id-card',
            ],
            'berkas_kk' => [
                'label'       => 'Kartu Keluarga (KK)',
                'description' => 'Kartu Keluarga calon santri',
                'icon'        => 'fa-users',
            ],
            'berkas_sktm' => [
                'label'       => 'Surat Keterangan Tidak Mampu (SKTM)',
                'description' => 'Surat keterangan dari kelurahan/desa/RT/RW',
                'icon'        => 'fa-file-invoice-dollar',
            ],
            'berkas_sehat' => [
                'label'       => 'Surat Keterangan Sehat / Bebas Narkoba',
                'description' => 'Surat sehat dari Puskesmas / RS',
                'icon'        => 'fa-heart-pulse',
            ],
            'berkas_vaksin' => [
                'label'       => 'Sertifikat Vaksin',
                'description' => 'Bukti sertifikat vaksinasi',
                'icon'        => 'fa-shield-virus',
            ],
        ];

        $uploadDir = FCPATH . 'uploads/ppdb/';
        $result = [];
        $totalUploaded = 0;

        foreach ($docDefinitions as $key => $meta) {
            $filename = $pendaftar[$key] ?? null;

            // Jika belum tercatat di DB, coba cari file di disk dengan pola no_pendaftaran
            if (empty($filename)) {
                $docKeySimple = str_replace('berkas_', '', $key);
                $pattern = $uploadDir . $pendaftar['no_pendaftaran'] . '_' . $docKeySimple . '_*';
                $matches = glob($pattern);
                if (!empty($matches)) {
                    $filename = basename($matches[0]);
                }
            }

            $exists = false;
            $fileUrl = null;
            $fileSize = 0;
            $extension = null;

            if (!empty($filename) && file_exists($uploadDir . $filename)) {
                $exists = true;
                $fileUrl = base_url('uploads/ppdb/' . $filename);
                $fileSize = filesize($uploadDir . $filename);
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $totalUploaded++;
            }

            $result[$key] = [
                'key'         => $key,
                'label'       => $meta['label'],
                'description' => $meta['description'],
                'icon'        => $meta['icon'],
                'filename'    => $filename,
                'exists'      => $exists,
                'url'         => $fileUrl,
                'size'        => $fileSize,
                'extension'   => $extension,
            ];
        }

        return [
            'docs'           => $result,
            'total_uploaded' => $totalUploaded,
            'total_required' => count($docDefinitions),
            'is_complete'    => ($totalUploaded >= count($docDefinitions)),
        ];
    }
}
