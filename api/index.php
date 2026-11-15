<?php

declare(strict_types=1);

// Deteksi lingkungan Vercel
if (!isset($_SERVER['VERCEL'])) {
    $_SERVER['VERCEL'] = '1';
    putenv('VERCEL=1');
}

// Inisialisasi folder /tmp/writable beserta subdirektorinya
$tmpWritable = sys_get_temp_dir() . '/writable';
foreach (['cache', 'logs', 'session', 'debugbar', 'uploads'] as $subdir) {
    $dir = $tmpWritable . '/' . $subdir;
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Teruskan ke Front Controller CodeIgniter 4
require __DIR__ . '/../public/index.php';
