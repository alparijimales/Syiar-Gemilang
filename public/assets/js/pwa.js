/**
 * Syiar Gemilang - PWA & Web Push Notification Client Helper
 */

let deferredInstallPrompt = null;

// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log('PWA ServiceWorker terdaftar sukses dengan scope:', registration.scope);
            })
            .catch((err) => {
                console.warn('PWA ServiceWorker gagal didaftarkan:', err);
            });
    });
}

// Tangani event Add to Home Screen (A2HS)
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredInstallPrompt = e;
    
    // Tampilkan tombol / banner install jika ada elemen terkait
    const installBtns = document.querySelectorAll('.pwa-install-btn');
    installBtns.forEach(btn => {
        btn.classList.remove('hidden');
        btn.style.display = 'inline-flex';
    });

    const installBanner = document.getElementById('pwaInstallBanner');
    if (installBanner && !localStorage.getItem('pwa_banner_dismissed')) {
        installBanner.classList.remove('hidden');
    }
});

// Listener saat aplikasi berhasil di-install
window.addEventListener('appinstalled', () => {
    console.log('Aplikasi Syiar Gemilang berhasil di-install ke layar utama.');
    deferredInstallPrompt = null;
    const installBanner = document.getElementById('pwaInstallBanner');
    if (installBanner) installBanner.classList.add('hidden');
    const installBtns = document.querySelectorAll('.pwa-install-btn');
    installBtns.forEach(btn => btn.style.display = 'none');
});

// Fungsi Trigger Install PWA
function triggerPwaInstall() {
    if (deferredInstallPrompt) {
        deferredInstallPrompt.prompt();
        deferredInstallPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('Pengguna menyetujui instalasi PWA');
            } else {
                console.log('Pengguna menolak instalasi PWA');
            }
            deferredInstallPrompt = null;
        });
    } else {
        // Cek jika di iOS Safari
        const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if (isIos) {
            alert('Untuk meng-install aplikasi di iPhone / iPad:\n1. Ketuk tombol Bagikan (Share) di browser Safari.\n2. Gulir ke bawah lalu pilih "Tambah ke Layar Utama" (Add to Home Screen).');
        } else {
            alert('Untuk memasang aplikasi ke layar utama:\nBuka menu browser Anda (titik tiga di kanan atas) lalu pilih "Instal Aplikasi" atau "Tambahkan ke Layar Utama".');
        }
    }
}

// Tutup banner install sementara
function dismissPwaBanner() {
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) banner.classList.add('hidden');
    localStorage.setItem('pwa_banner_dismissed', '1');
}

// =========================================================================
// PUSH NOTIFICATION FITUR UNTUK WALI SANTRI & SISWA
// =========================================================================

// Minta izin Push Notification
async function requestPushNotificationPermission() {
    if (!('Notification' in window)) {
        alert('Browser Anda belum mendukung fitur Notifikasi Web.');
        return false;
    }

    if (Notification.permission === 'granted') {
        showPwaToast('Notifikasi HP sudah aktif! Anda akan menerima update pengumuman seketika.');
        sendLocalTestNotification('Notifikasi Aktif', 'Alhamdulillah! Notifikasi pengumuman untuk wali santri kini aktif di perangkat Anda.');
        return true;
    }

    if (Notification.permission !== 'denied') {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            showPwaToast('Berhasil mengaktifkan notifikasi pengumuman!');
            sendLocalTestNotification('Selamat Datang di E-RGI', 'Notifikasi resmi untuk wali santri dan siswa telah aktif.');
            localStorage.setItem('pwa_push_enabled', '1');
            updatePushToggleUI(true);
            return true;
        } else {
            alert('Izin notifikasi tidak diberikan. Anda tidak akan menerima pemberitahuan otomatis saat ada pengumuman baru.');
            updatePushToggleUI(false);
            return false;
        }
    } else {
        alert('Notifikasi diblokir di browser Anda. Harap buka Pengaturan Izin Situs pada browser Anda untuk mengaktifkan kembali notifikasi.');
        return false;
    }
}

// Kirim Notifikasi via Service Worker
function sendLocalTestNotification(title, body, url = '/wali') {
    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({
            action: 'triggerNotification',
            payload: {
                title: title,
                body: body,
                url: url
            }
        });
    } else if (Notification.permission === 'granted') {
        new Notification(title, {
            body: body,
            icon: '/assets/img/icon-192.png',
            badge: '/assets/img/icon-192.png'
        });
    }
}

// Sinkronisasi status UI tombol notifikasi
function updatePushToggleUI(isEnabled) {
    const pushStatusText = document.getElementById('pushStatusText');
    const pushToggleBtn = document.getElementById('btnTogglePush');
    if (pushStatusText) {
        pushStatusText.textContent = isEnabled ? 'Aktif (Menerima Pengumuman)' : 'Belum Aktif';
    }
    if (pushToggleBtn) {
        if (isEnabled) {
            pushToggleBtn.classList.remove('bg-[#0066CC]', 'hover:bg-[#0052A3]');
            pushToggleBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
            pushToggleBtn.innerHTML = '<i class="fa-solid fa-bell-check"></i> Notifikasi Aktif';
        } else {
            pushToggleBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
            pushToggleBtn.classList.add('bg-[#0066CC]', 'hover:bg-[#0052A3]');
            pushToggleBtn.innerHTML = '<i class="fa-solid fa-bell"></i> Aktifkan Notifikasi HP';
        }
    }
}

// Toast Notifikasi Ringan
function showPwaToast(msg) {
    let toast = document.getElementById('pwaLiveToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'pwaLiveToast';
        toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white text-xs px-4 py-3 rounded-xl shadow-xl flex items-center gap-2 border border-slate-700 transition duration-300 transform translate-y-10 opacity-0 pointer-events-none';
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i> <span>${msg}</span>`;
    toast.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
    }, 3500);
}

// BroadcastChannel untuk notifikasi antar tab jika admin mengirim pengumuman baru
try {
    const announcementChannel = new BroadcastChannel('syiar_announcements');
    announcementChannel.onmessage = (event) => {
        if (event.data && event.data.type === 'NEW_ANNOUNCEMENT') {
            const a = event.data.payload;
            if (Notification.permission === 'granted') {
                sendLocalTestNotification(
                    'Pengumuman Baru: ' + (a.judul || 'Diklat RGI'),
                    a.isi ? a.isi.substring(0, 100) + '...' : 'Silakan buka portal wali santri untuk melihat rincian.',
                    '/wali'
                );
            }
            showPwaToast('Pengumuman Baru: ' + (a.judul || 'Silakan cek portal'));
        }
    };
} catch(e) {
    // BroadcastChannel unsupported in older browser, fallback gracefully
}

document.addEventListener('DOMContentLoaded', () => {
    if ('Notification' in window && Notification.permission === 'granted') {
        updatePushToggleUI(true);
    }
});
