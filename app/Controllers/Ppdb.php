<?php

namespace App\Controllers;

use App\Models\PpdbModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Ppdb extends BaseController
{
    protected PpdbModel $ppdbModel;

    public function __construct()
    {
        $this->ppdbModel = new PpdbModel();
    }

    /**
     * Halaman Utama PPDB (Penerimaan Peserta Didik Baru)
     */
    public function index()
    {
        $selectedJurusan = $this->request->getGet('jurusan');

        $data = [
            'title'           => 'PPDB 2026 – Penerimaan Santri Baru Rumah Gemilang Indonesia',
            'stats'           => $this->ppdbModel->getStats(),
            'selectedJurusan' => $selectedJurusan,
            'jurusanList'     => [
                'Teknik Komputer dan Jaringan',
                'Tata Busana',
                'Fotografi dan Videografi',
                'Desain Grafis',
                'Otomotif',
                'Aplikasi Perkantoran',
                'Kuliner Halal',
                'Rekayasa Perangkat Lunak',
            ],
            'cabangList'      => [
                'Kampus Utama Depok (Pusat)',
                'Kampus Surabaya (Jawa Timur)',
                'Kampus Yogyakarta',
                'Kampus Magelang',
                'Kampus Medan (Sumatera Utara)',
                'Kampus Aceh',
            ],
        ];

        return view('ppdb/index', $data);
    }

    /**
     * Proses Pendaftaran Santri Baru (Online)
     */
    public function submit()
    {
        // Validasi input
        $rules = [
            'nama_lengkap'        => 'required|min_length[3]|max_length[120]',
            'jenis_kelamin'       => 'required|in_list[L,P,Laki-laki,Perempuan]',
            'whatsapp'            => 'required|min_length[9]|max_length[20]',
            'alamat'              => 'required|min_length[5]',
            'jurusan_pilihan1'    => 'required',
            'cabang_kampus'       => 'required',
            'pendidikan_terakhir' => 'required',
        ];

        $messages = [
            'nama_lengkap' => [
                'required'   => 'Nama lengkap wajib diisi.',
                'min_length' => 'Nama lengkap minimal 3 karakter.',
            ],
            'jenis_kelamin' => [
                'required' => 'Pilih jenis kelamin.',
                'in_list'  => 'Pilihan jenis kelamin tidak valid.',
            ],
            'whatsapp' => [
                'required'   => 'Nomor WhatsApp aktif wajib diisi.',
                'min_length' => 'Nomor WhatsApp tidak valid (minimal 9 digit).',
            ],
            'alamat' => [
                'required'   => 'Alamat domisili lengkap wajib diisi.',
                'min_length' => 'Mohon isi alamat lengkap dengan jelas.',
            ],
            'jurusan_pilihan1' => [
                'required' => 'Pilih jurusan prioritas pertama.',
            ],
            'cabang_kampus' => [
                'required' => 'Pilih kampus diklat tujuan.',
            ],
            'pendidikan_terakhir' => [
                'required' => 'Pilih jenjang pendidikan terakhir.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Normalisasi jenis kelamin ke enum 'L' / 'P'
        $rawJk = (string)$this->request->getPost('jenis_kelamin');
        $jk = in_array($rawJk, ['L', 'Laki-laki']) ? 'L' : 'P';

        // Generate Nomor Pendaftaran Unik
        $noPendaftaran = $this->ppdbModel->generateNoPendaftaran(36);

        // Siapkan data
        $saveData = [
            'no_pendaftaran'      => $noPendaftaran,
            'nama_lengkap'        => trim($this->request->getPost('nama_lengkap')),
            'nik'                 => trim($this->request->getPost('nik') ?? ''),
            'jenis_kelamin'       => $jk,
            'tempat_lahir'        => trim($this->request->getPost('tempat_lahir') ?? ''),
            'tanggal_lahir'       => $this->request->getPost('tanggal_lahir') ?: null,
            'whatsapp'            => trim($this->request->getPost('whatsapp')),
            'email'               => trim($this->request->getPost('email') ?? ''),
            'alamat'              => trim($this->request->getPost('alamat')),
            'kota'                => trim($this->request->getPost('kota') ?? ''),
            'jurusan_pilihan1'    => $this->request->getPost('jurusan_pilihan1'),
            'jurusan_pilihan2'    => $this->request->getPost('jurusan_pilihan2') ?: null,
            'cabang_kampus'       => $this->request->getPost('cabang_kampus'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'asal_sekolah'        => trim($this->request->getPost('asal_sekolah') ?? ''),
            'nama_wali'           => trim($this->request->getPost('nama_wali') ?? ''),
            'whatsapp_wali'       => trim($this->request->getPost('whatsapp_wali') ?? ''),
            'motivasi'            => trim($this->request->getPost('motivasi') ?? ''),
            'status'              => 'Menunggu Seleksi',
        ];

        // Simpan file berkas jika diunggah
        $docKeys = ['berkas_ijazah', 'berkas_ktp', 'berkas_kk', 'berkas_sktm', 'berkas_sehat', 'berkas_vaksin'];
        $uploadDir = FCPATH . 'uploads/ppdb/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        foreach ($docKeys as $key) {
            $f = $this->request->getFile($key);
            if ($f && $f->isValid() && !$f->hasMoved()) {
                $ext = $f->guessExtension() ?: 'jpg';
                $fName = $noPendaftaran . '_' . str_replace('berkas_', '', $key) . '_' . time() . '.' . $ext;
                $f->move($uploadDir, $fName);
                $saveData[$key] = $fName;
            }
        }

        $this->ppdbModel->insert($saveData);

        return redirect()->to(base_url('ppdb/sukses/' . $noPendaftaran))
            ->with('notif_success', 'Alhamdulillah! Pendaftaran online Anda berhasil dicatat.');
    }

    /**
     * Halaman Bukti Pendaftaran Sukses
     */
    public function sukses(string $noPendaftaran)
    {
        $pendaftar = $this->ppdbModel->getByNoPendaftaran($noPendaftaran);
        if (!$pendaftar) {
            throw PageNotFoundException::forPageNotFound("Data pendaftaran dengan nomor {$noPendaftaran} tidak ditemukan.");
        }

        $data = [
            'title'     => "Bukti Pendaftaran PPDB – {$pendaftar['nama_lengkap']} ({$pendaftar['no_pendaftaran']})",
            'pendaftar' => $pendaftar,
        ];

        return view('ppdb/sukses', $data);
    }

    /**
     * Cek Status Pendaftaran Calon Santri
     */
    public function cekStatus()
    {
        $keyword = trim((string)$this->request->getGet('keyword'));
        $pendaftar = null;
        $searched = false;

        if (!empty($keyword)) {
            $searched = true;
            $pendaftar = $this->ppdbModel->where('no_pendaftaran', $keyword)
                ->orWhere('whatsapp', $keyword)
                ->orWhere('nik', $keyword)
                ->first();
        }

        $data = [
            'title'     => 'Cek Status Seleksi PPDB – Syiar Gemilang RGI',
            'keyword'   => $keyword,
            'pendaftar' => $pendaftar,
            'searched'  => $searched,
        ];

        return view('ppdb/cek_status', $data);
    }
}
