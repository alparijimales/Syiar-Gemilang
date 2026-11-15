<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        // Jika sudah login, langsung arahkan ke dashboard masing-masing
        if (session()->get('logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        $data = [
            'title' => 'Masuk ke Sistem - Syiar Gemilang'
        ];

        return view('auth/login', $data);
    }

    public function attemptLogin()
    {
        $username = trim((string)$this->request->getPost('username'));
        $password = (string)$this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->to(base_url('login'))->withInput()->with('error', 'Nomor Induk Santri (NIS) / Username dan kata sandi wajib diisi.');
        }

        $db = \Config\Database::connect();
        $user = $db->table('users')
                   ->where('username', $username)
                   ->where('is_active', 1)
                   ->get()
                   ->getRowArray();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->to(base_url('login'))->withInput()->with('error', 'Username atau kata sandi yang Anda masukkan salah.');
        }

        // Set session login
        $avatarUrl = null;
        if (in_array($user['role'], ['siswa', 'wali_santri']) && !empty($user['santri_id'])) {
            $santri = $db->table('nilai_santri')->where('id', $user['santri_id'])->get()->getRowArray();
            if ($santri && !empty($santri['foto']) && file_exists(FCPATH . 'uploads/santri/' . $santri['foto'])) {
                $avatarUrl = base_url('uploads/santri/' . $santri['foto']);
            }
        }
        if (!$avatarUrl && !empty($user['avatar']) && file_exists(FCPATH . 'uploads/avatars/' . $user['avatar'])) {
            $avatarUrl = base_url('uploads/avatars/' . $user['avatar']);
        }

        session()->set([
            'user_id'   => (int)$user['id'],
            'username'  => $user['username'],
            'nama'      => $user['nama'],
            'role'      => $user['role'],
            'santri_id' => $user['santri_id'] ? (int)$user['santri_id'] : null,
            'avatar'    => $avatarUrl,
            'logged_in' => true,
        ]);

        return $this->redirectByRole($user['role']);
    }

    public function logout()
    {
        session()->remove(['user_id', 'username', 'nama', 'role', 'santri_id', 'avatar', 'logged_in']);
        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Halaman Pengaturan Profil Akun (Nama, Username, Foto Profil, Password)
     */
    public function profile()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userId = (int)session()->get('user_id');
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();

        $santri = null;
        $avatarUrl = null;

        // Jika siswa atau wali santri: ambil data santri terkait
        if (!empty($user['santri_id'])) {
            $santri = $db->table('nilai_santri')->where('id', $user['santri_id'])->get()->getRowArray();
            if ($santri && !empty($santri['foto']) && file_exists(FCPATH . 'uploads/santri/' . $santri['foto'])) {
                $avatarUrl = base_url('uploads/santri/' . $santri['foto']);
            }
        }

        // Jika instruktur atau admin atau santri yang upload custom avatar
        if (!$avatarUrl && !empty($user['avatar']) && file_exists(FCPATH . 'uploads/avatars/' . $user['avatar'])) {
            $avatarUrl = base_url('uploads/avatars/' . $user['avatar']);
        }

        $data = [
            'title'     => 'Pengaturan Profil & Akun',
            'user'      => $user,
            'santri'    => $santri,
            'avatarUrl' => $avatarUrl,
        ];

        return view('auth/profile', $data);
    }

    /**
     * Simpan Perubahan Profil (Nama Lengkap, Username, Foto Profil, Password)
     */
    public function updateProfile()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userId = (int)session()->get('user_id');
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();

        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $nama = trim((string)$this->request->getPost('nama'));
        $username = trim((string)$this->request->getPost('username'));

        if (empty($nama)) {
            return redirect()->back()->withInput()->with('error', 'Nama lengkap wajib diisi!');
        }

        if (empty($username)) {
            return redirect()->back()->withInput()->with('error', 'Username wajib diisi!');
        }

        // Validasi karakter username (huruf, angka, titik, underscore, strip)
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
            return redirect()->back()->withInput()->with('error', 'Username hanya boleh berupa huruf, angka, titik, strip, dan underscore tanpa spasi!');
        }

        // Cek apakah username sudah dipakai akun lain
        $duplicate = $db->table('users')
                        ->where('username', $username)
                        ->where('id !=', $userId)
                        ->get()
                        ->getRowArray();

        if ($duplicate) {
            return redirect()->back()->withInput()->with('error', 'Username "' . esc($username) . '" sudah digunakan pengguna lain!');
        }

        $updateData = [
            'nama'     => $nama,
            'username' => $username,
        ];

        // 1. Proses Upload Foto Profil
        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $mime = $avatarFile->getMimeType();

            if (!in_array($mime, $allowedTypes)) {
                return redirect()->back()->withInput()->with('error', 'Format file foto harus berupa JPG, PNG, atau WebP!');
            }

            if ($avatarFile->getSizeByUnit('mb') > 3) {
                return redirect()->back()->withInput()->with('error', 'Ukuran foto maksimal adalah 3 MB!');
            }

            $newExt = $avatarFile->guessExtension() ?: 'jpg';
            $newName = 'avatar_' . $userId . '_' . time() . '.' . $newExt;
            $uploadPath = FCPATH . 'uploads/avatars/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Hapus avatar lama jika ada
            if (!empty($user['avatar']) && file_exists($uploadPath . $user['avatar'])) {
                @unlink($uploadPath . $user['avatar']);
            }

            $avatarFile->move($uploadPath, $newName);
            $updateData['avatar'] = $newName;

            $newAvatarUrl = base_url('uploads/avatars/' . $newName);
            session()->set('avatar', $newAvatarUrl);

            // Jika role santri, sinkronkan juga ke tabel nilai_santri
            if ($user['role'] === 'siswa' && !empty($user['santri_id'])) {
                $santriUploadPath = FCPATH . 'uploads/santri/';
                if (!is_dir($santriUploadPath)) {
                    mkdir($santriUploadPath, 0777, true);
                }
                @copy($uploadPath . $newName, $santriUploadPath . $newName);
                $db->table('nilai_santri')->where('id', $user['santri_id'])->update(['foto' => $newName]);
            }
        }

        // 2. Proses Ganti Kata Sandi (Opsional jika diisi)
        $newPassword = (string)$this->request->getPost('new_password');
        $currentPassword = (string)$this->request->getPost('current_password');
        $confirmPassword = (string)$this->request->getPost('confirm_password');

        if (!empty($newPassword) || !empty($currentPassword) || !empty($confirmPassword)) {
            if (empty($currentPassword)) {
                return redirect()->back()->withInput()->with('error', 'Untuk mengubah kata sandi, harap masukkan kata sandi Anda saat ini!');
            }

            if (!password_verify($currentPassword, $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Kata sandi saat ini yang Anda masukkan salah!');
            }

            if (strlen($newPassword) < 6) {
                return redirect()->back()->withInput()->with('error', 'Kata sandi baru minimal 6 karakter!');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->back()->withInput()->with('error', 'Konfirmasi kata sandi baru tidak cocok!');
            }

            $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        // Simpan data pembaruan
        $db->table('users')->where('id', $userId)->update($updateData);

        // Perbarui session nama & username
        session()->set([
            'nama'     => $nama,
            'username' => $username,
        ]);

        return redirect()->to(base_url('profil'))->with('success', 'Profil dan kredensial akun berhasil diperbarui!');
    }

    public function changePassword()
    {
        return $this->profile();
    }

    public function updatePassword()
    {
        return $this->updateProfile();
    }

    private function redirectByRole(string $role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->to(base_url('admin'));
            case 'instruktur':
                return redirect()->to(base_url('instruktur'));
            case 'siswa':
                return redirect()->to(base_url('siswa'));
            case 'wali_santri':
                return redirect()->to(base_url('wali'));
            case 'pendamping_agama':
                return redirect()->to(base_url('pendamping'));
            default:
                return redirect()->to(base_url('login'));
        }
    }
}

