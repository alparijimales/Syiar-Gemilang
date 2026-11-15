<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public Auth & Profile Routes
$routes->match(['GET', 'HEAD'], 'login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->match(['GET', 'HEAD'], 'logout', 'Auth::logout');
$routes->match(['GET', 'HEAD'], 'profil', 'Auth::profile');
$routes->post('profil', 'Auth::updateProfile');
$routes->match(['GET', 'HEAD'], 'profile', 'Auth::profile');
$routes->post('profile', 'Auth::updateProfile');
$routes->match(['GET', 'HEAD'], 'ganti-password', 'Auth::profile');
$routes->post('ganti-password', 'Auth::updateProfile');

// Root Route (Landing Page sekaligus Login Page)
$routes->match(['GET', 'HEAD'], '/', 'Home::index');

// PPDB (Penerimaan Peserta Didik Baru) Public Routes
$routes->match(['GET', 'HEAD'], 'ppdb', 'Ppdb::index');
$routes->post('ppdb/submit', 'Ppdb::submit');
$routes->match(['GET', 'HEAD'], 'ppdb/sukses/(:segment)', 'Ppdb::sukses/$1');
$routes->match(['GET', 'HEAD'], 'ppdb/cek-status', 'Ppdb::cekStatus');

// Admin Routes (Full Access)
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->match(['GET', 'HEAD'], '/', 'Admin::index');
    $routes->match(['GET', 'HEAD'], 'dashboard', 'Admin::index');
    $routes->get('data', 'Admin::dataStudent');
    $routes->get('add', 'Admin::addStudent');
    $routes->post('save', 'Admin::saveStudent');
    $routes->post('update', 'Admin::update');
    $routes->post('store', 'Admin::store');
    $routes->post('delete/(:segment)', 'Admin::delete/$1');
    $routes->get('pemeringkatan', 'Admin::pemeringkatan');
    $routes->get('kehadiran', 'Admin::kehadiran');
    $routes->post('kehadiran/add_sesi', 'Admin::addSesiKehadiran');
    $routes->post('kehadiran/update_sesi', 'Admin::updateSesiKehadiran');
    $routes->post('kehadiran/delete_sesi', 'Admin::deleteSesiKehadiran');
    $routes->post('kehadiran/catat', 'Admin::catatPresensiSantri');

    // User Management Routes
    $routes->get('users', 'Admin::users');
    $routes->get('users/add', 'Admin::addUser');
    $routes->post('users/save', 'Admin::saveUser');
    $routes->post('users/update', 'Admin::updateUser');
    $routes->post('users/toggle/(:segment)', 'Admin::toggleUserStatus/$1');
    $routes->post('users/delete/(:segment)', 'Admin::deleteUser/$1');

    // Announcement / Papan Pengumuman Diklat Routes
    $routes->get('announcements', 'Admin::announcements');
    $routes->post('announcements/save', 'Admin::saveAnnouncement');
    $routes->post('announcements/update', 'Admin::updateAnnouncement');
    $routes->post('announcements/toggle/(:segment)', 'Admin::toggleAnnouncementStatus/$1');
    $routes->post('announcements/delete/(:segment)', 'Admin::deleteAnnouncement/$1');

    // PPDB Management Routes
    $routes->match(['GET', 'HEAD'], 'ppdb', 'Admin::ppdb');
    $routes->post('ppdb/status/(:segment)', 'Admin::updatePpdbStatus/$1');
    $routes->post('ppdb/delete/(:segment)', 'Admin::deletePpdb/$1');
    $routes->get('ppdb/berkas/(:segment)/(:segment)', 'Admin::downloadPpdbFile/$1/$2');
    $routes->get('ppdb/download-zip/(:segment)', 'Admin::downloadPpdbZip/$1');

    // Export Data Santri & Ranking
    $routes->get('export-santri-csv', 'Admin::exportSantriCsv');
    $routes->get('cetak-santri', 'Admin::cetakSantri');
    $routes->get('export-ranking-csv', 'Admin::exportRankingCsv');
    $routes->get('cetak-ranking', 'Admin::cetakRanking');
    $routes->get('cetak-rapor/(:num)', 'Admin::cetakRapor/$1');

    // Database Backup (.sql)
    $routes->get('backup-db', 'Admin::downloadBackupSql');
    $routes->get('download-backup-sql', 'Admin::downloadBackupSql');

    // Mapel & Instruktur Kejuruan
    $routes->get('mapel', 'Admin::mapel');
    $routes->post('mapel/save', 'Admin::saveMapel');
    $routes->post('mapel/update/(:segment)', 'Admin::updateMapel/$1');
    $routes->post('mapel/delete/(:segment)', 'Admin::deleteMapel/$1');

    // Catatan Setoran Hafalan Tahfidz
    $routes->get('hafalan', 'Admin::hafalan');
    $routes->post('hafalan/save', 'Admin::saveHafalan');
    $routes->post('hafalan/delete/(:segment)', 'Admin::deleteHafalan/$1');

    // Broadcast Push Notification Pengumuman ke Wali
    $routes->post('announcements/broadcast-push/(:segment)', 'Admin::broadcastPushAnnouncement/$1');
});

// Instruktur Routes (Input/Update Nilai & Presensi)
$routes->group('instruktur', ['filter' => 'role:instruktur'], function ($routes) {
    $routes->match(['GET', 'HEAD'], '/', 'Instruktur::index');
    $routes->match(['GET', 'HEAD'], 'kelas', 'Instruktur::kelas');
    $routes->match(['GET', 'HEAD'], 'nilai_kelas', 'Instruktur::kelas');
    $routes->match(['GET', 'HEAD'], 'nilai', 'Instruktur::nilai');
    $routes->post('nilai/update', 'Instruktur::updateNilai');
    $routes->match(['GET', 'HEAD'], 'kehadiran', 'Instruktur::kehadiran');
    $routes->post('kehadiran/catat', 'Instruktur::catatPresensi');
    
    // Download Rekapan Nilai & Presensi (CSV)
    $routes->get('download-nilai-csv', 'Instruktur::downloadRekapNilaiCsv');
    $routes->get('download-absensi-csv', 'Instruktur::downloadRekapAbsensiCsv');

    // Modul Kompetensi Kejuruan Dinamis (Bobot Tugas, Teori, Praktik Projek)
    $routes->match(['GET', 'HEAD'], 'kompetensi', 'Instruktur::kompetensi');
    $routes->post('kompetensi/bobot', 'Instruktur::updateBobot');
    $routes->post('kompetensi/save-nilai', 'Instruktur::updateNilaiKompetensi');
});

// Siswa Routes (Presensi Mandiri Per Nama Santri)
$routes->group('siswa', ['filter' => 'role:siswa'], function ($routes) {
    $routes->get('/', 'Siswa::index');
    $routes->get('presensi', 'Siswa::presensi');
    $routes->post('presensi/catat', 'Siswa::catatPresensi');
});

// Wali Santri Routes (Hanya Melihat Saja Secara Detail Tanpa Mengisi)
$routes->group('wali', ['filter' => 'role:wali_santri'], function ($routes) {
    $routes->get('/', 'Wali::index');
    $routes->get('rapor', 'Wali::rapor');
    $routes->get('cetak-rapor', 'Wali::rapor');
});

// Pendamping Agama Routes (Bimbingan Setoran Hafalan & Tajwid)
$routes->group('pendamping', ['filter' => 'role:pendamping_agama,admin'], function ($routes) {
    $routes->match(['GET', 'HEAD'], '/', 'Pendamping::index');
    $routes->match(['GET', 'HEAD'], 'hafalan', 'Pendamping::index');
    $routes->post('hafalan/save', 'Pendamping::save');
    $routes->post('hafalan/delete/(:num)', 'Pendamping::delete/$1');
});
