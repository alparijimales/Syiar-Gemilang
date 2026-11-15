<?php

namespace App\Controllers;

use App\Models\HafalanModel;
use App\Models\StudentModel;

class Pendamping extends BaseController
{
    public function index()
    {
        $hafalanModel = new HafalanModel();
        $studentModel = new StudentModel();

        $status  = $this->request->getGet('status');
        $target  = $this->request->getGet('target');
        $tanggal = $this->request->getGet('tanggal');
        $search  = trim((string)$this->request->getGet('search'));

        $setoranList = $hafalanModel->getAllSetoran($status, $search, $target, $tanggal);
        $summary = $hafalanModel->getSummaryStats();
        $santriList = $studentModel->select('id, nama, nis, kelas')->orderBy('nama', 'ASC')->findAll();
        $targets = $hafalanModel->getAvailableTargets();

        $data = [
            'title'         => 'Portal Pendamping Agama - Setoran Hafalan & Tasmik',
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

    public function save()
    {
        $hafalanModel = new HafalanModel();

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
            return redirect()->to(base_url('pendamping'))->with('error', 'Santri, target hafalan, dan surat/ayat wajib diisi.');
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

        return redirect()->to(base_url('pendamping'))
            ->with('notif_type', 'add')
            ->with('notif_title', 'Setoran Hafalan Berhasil Disimpan!')
            ->with('notif_msg', "Bimbingan tajwid dan nilai untuk {$suratAyat} telah tercatat.");
    }

    public function delete($id)
    {
        $id = (int)$id;
        $hafalanModel = new HafalanModel();
        $hf = $hafalanModel->find($id);

        if (!$hf) {
            return redirect()->to(base_url('pendamping'))->with('error', 'Data setoran tidak ditemukan.');
        }

        $hafalanModel->delete($id);

        return redirect()->to(base_url('pendamping'))
            ->with('notif_type', 'delete')
            ->with('notif_title', 'Setoran Dihapus!')
            ->with('notif_msg', "Catatan setoran hafalan berhasil dihapus.");
    }
}
