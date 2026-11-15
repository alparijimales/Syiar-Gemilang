<?php 
$current_page = basename($_SERVER['PHP_SELF']); 

// Generate inisial otomatis dari nama session (Misal: "Ahmad Fauzi" -> "AF")
$userName = session()->get('nama') ?? 'Santri RGI';
$words = preg_split('/\s+/', trim($userName));
$initials = '';
foreach ($words as $w) {
    if (!empty($w)) {
        $initials .= mb_strtoupper(mb_substr($w, 0, 1));
    }
    if (strlen($initials) >= 2) break;
}
if (strlen($initials) === 1 && strlen($userName) > 1) {
    $initials .= mb_strtoupper(mb_substr($userName, 1, 1));
}
if (empty($initials)) {
    $initials = 'ST';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Santri'); ?> - SIA Syiar Gemilang</title>
    <!-- Favicon & Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/img/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/img/favicon-16x16.png'); ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/apple-touch-icon.png'); ?>">

    <!-- PWA Manifest & Meta Tags -->
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
    <meta name="theme-color" content="#0077CC">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Syiar Gemilang">
    <script src="<?= base_url('assets/js/pwa.js'); ?>" defer></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'Inter', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#0077CC',
                            dark: '#0052A3',
                            deep: '#042f4b',
                            active: '#003E7E',
                            light: '#E8F3FC',
                            border: '#B9DCF7'
                        }
                    }
                }
            }
        };
    </script>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter + Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; text-rendering: optimizeLegibility; -webkit-font-smoothing: antialiased; }
        .sg-display { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        .sg-card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04); }
        .sg-banner { background: linear-gradient(100deg, rgba(0, 119, 204, 0.05) 0%, #FFFFFF 70%); border: 1px solid rgba(0, 119, 204, 0.15); border-radius: 12px; }
        .text-2xs { font-size: 0.70rem; line-height: 0.95rem; }
        .text-3xs { font-size: 0.625rem; line-height: 0.85rem; }

        /* =========================================================
           FLOATING RAIL & EXPANDED WAVE SIDEBAR STYLES (SISWA)
           ========================================================= */
        :root {
            --sg-navy: #042f4b;
            --sg-island-bg: #021e32;
            --sg-blue: #0077cc;
            --sg-icon-idle: #9ab8d3;
        }

        #appSidebar {
            background-color: var(--sg-navy);
            transition: width 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                        border-radius 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.32s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.32s ease;
            box-shadow: 0 20px 45px -12px rgba(2, 24, 43, 0.45);
            will-change: width, border-radius, transform;
        }

        .sidebar-header-brand,
        .sidebar-header-actions,
        .sidebar-profile-info,
        .sidebar-profile-actions {
            transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                        max-width 0.32s cubic-bezier(0.16, 1, 0.3, 1);
            white-space: nowrap;
            opacity: 1;
            max-width: 220px;
            transform: translateX(0);
            will-change: opacity, transform, max-width;
        }

        #sidebarToggleIcon {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #appSidebar.is-collapsed #sidebarToggleIcon {
            transform: rotate(180deg);
        }

        /* ----- MODE 1: FLOATING ICON RAIL (KONSEP A - DEFAULT DESKTOP) ----- */
        @media (min-width: 1024px) {
            #appSidebar.is-collapsed {
                width: 68px !important;
                margin: 12px 6px 12px 12px;
                height: calc(100vh - 24px);
                border-radius: 9999px;
                overflow: visible !important;
            }

            #appSidebar.is-collapsed .sidebar-header-brand,
            #appSidebar.is-collapsed .sidebar-header-actions,
            #appSidebar.is-collapsed .sidebar-profile-info,
            #appSidebar.is-collapsed .sidebar-profile-actions {
                display: none !important;
            }

            #appSidebar.is-collapsed .sidebar-wave-wrap,
            #appSidebar.is-collapsed #liquidActiveIndicator {
                opacity: 0 !important;
                pointer-events: none !important;
                visibility: hidden !important;
                display: none !important;
            }

            #appSidebar.is-collapsed .sidebar-header {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 14px 0 !important;
                border-bottom: none !important;
                width: 100% !important;
            }

            #appSidebar.is-collapsed .sidebar-header > div:first-child {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 0 !important;
                width: 46px !important;
                margin: 0 auto !important;
            }

            #appSidebar.is-collapsed #dynamicIslandWrapper {
                margin: 0 auto !important;
                width: 46px !important;
                height: 46px !important;
            }

            /* Navigasi: Pusatkan ikon menu presisi di tengah rail 68px */
            #appSidebar.is-collapsed #sidebarNav {
                overflow: visible !important;
                padding: 6px 0 !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                width: 100% !important;
            }

            #appSidebar.is-collapsed .sidebar-nav-link {
                position: relative;
                width: 46px !important;
                height: 44px !important;
                padding: 0 !important;
                border-radius: 9999px !important;
                justify-content: flex-start !important;
                overflow: visible !important;
                background: transparent !important;
                box-shadow: none !important;
                margin: 0 auto 4px auto !important;
            }

            #appSidebar.is-collapsed .sidebar-nav-link .rail-pill-inner {
                position: absolute;
                top: 0;
                left: 0;
                height: 44px;
                min-width: 46px;
                max-width: 46px;
                padding: 0;
                border-radius: 9999px;
                display: flex;
                align-items: center;
                overflow: hidden;
                white-space: nowrap;
                transition: max-width 0.36s cubic-bezier(0.16, 1, 0.3, 1),
                            background-color 0.22s ease,
                            box-shadow 0.32s cubic-bezier(0.16, 1, 0.3, 1),
                            padding-right 0.28s cubic-bezier(0.16, 1, 0.3, 1);
                z-index: 20;
                will-change: max-width, box-shadow;
                transform: translateZ(0);
                backface-visibility: hidden;
            }

            #appSidebar.is-collapsed .sidebar-nav-link .rail-icon-box {
                width: 46px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            #appSidebar.is-collapsed .sidebar-nav-link .rail-icon-box svg {
                transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                            stroke 0.2s ease,
                            color 0.2s ease;
            }

            #appSidebar.is-collapsed .sidebar-nav-link .sidebar-label {
                opacity: 0;
                transform: translateX(-8px);
                transition: opacity 0.18s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                font-weight: 600;
                font-size: 0.875rem;
                color: #ffffff;
                padding-left: 2px;
                pointer-events: none;
                white-space: nowrap;
            }

            #appSidebar.is-collapsed .sidebar-nav-link.is-active .rail-pill-inner {
                background-color: var(--sg-blue);
                color: #ffffff;
                box-shadow: 0 0 20px rgba(0, 119, 204, 0.65);
            }

            #appSidebar.is-collapsed .sidebar-nav-link:hover .rail-pill-inner,
            #appSidebar.is-collapsed .sidebar-nav-link:focus-visible .rail-pill-inner,
            #appSidebar.is-collapsed .sidebar-nav-link.touch-expanded .rail-pill-inner {
                max-width: 250px;
                padding-right: 18px;
                background-color: var(--sg-blue);
                color: #ffffff;
                box-shadow: 0 10px 28px -4px rgba(0, 119, 204, 0.65);
                z-index: 60;
            }

            #appSidebar.is-collapsed .sidebar-nav-link:hover .sidebar-label,
            #appSidebar.is-collapsed .sidebar-nav-link:focus-visible .sidebar-label,
            #appSidebar.is-collapsed .sidebar-nav-link.touch-expanded .sidebar-label {
                opacity: 1;
                transform: translateX(0);
                pointer-events: auto;
                transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1) 0.05s,
                            transform 0.28s cubic-bezier(0.16, 1, 0.3, 1) 0.04s;
            }

            #appSidebar.is-collapsed .sidebar-nav-link:hover .rail-icon-box svg {
                transform: scale(1.1);
            }

            #appSidebar.is-collapsed .sidebar-nav-link:hover svg,
            #appSidebar.is-collapsed .sidebar-nav-link.is-active svg {
                color: #ffffff !important;
                stroke: #ffffff !important;
            }

            #appSidebar.is-collapsed .sidebar-footer {
                padding: 10px 0 14px 0 !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                border-top: none !important;
                background: transparent !important;
                width: 100% !important;
            }

            #appSidebar.is-collapsed .sidebar-profile-box {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 0 !important;
                width: 44px !important;
                margin: 0 auto !important;
            }

            #appSidebar.is-collapsed .sidebar-profile-box > div:first-child {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 0 !important;
                width: 44px !important;
                margin: 0 auto !important;
            }

            #appSidebar.is-collapsed #railProfileTrigger {
                margin: 0 auto !important;
                width: 44px !important;
                height: 44px !important;
            }
        }

        /* ----- MODE 2: EXPANDED SIDEBAR + LIQUID PILL & WAVE (KONSEP B) ----- */
        #appSidebar:not(.is-collapsed) {
            width: 268px;
            border-radius: 28px;
            margin: 12px 6px 12px 12px;
            height: calc(100vh - 24px);
            overflow: hidden;
        }

        @media (max-width: 1023px) {
            #appSidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 272px !important;
                border-radius: 28px !important;
                margin: 10px !important;
                height: calc(100vh - 20px) !important;
                overflow: hidden !important;
                transform: translateX(-120%) !important;
                transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.32s ease !important;
                z-index: 50 !important;
                box-shadow: 0 20px 45px -12px rgba(2, 24, 43, 0.55);
            }
            #appSidebar.mobile-open {
                transform: translateX(0) !important;
            }

            #sidebarToggleBtn {
                display: none !important;
            }

            /* Header actions: tombol close X selalu tampil dan tidak terjepit */
            .sidebar-header-actions {
                display: flex !important;
                align-items: center !important;
                flex-shrink: 0 !important;
                opacity: 1 !important;
                max-width: none !important;
                pointer-events: auto !important;
                transform: none !important;
            }

            /* Brand title: vertikal flex-col, min-w-0, flex-1 agar teks rapi & tidak meluber */
            .sidebar-header-brand {
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                min-width: 0 !important;
                flex: 1 !important;
                opacity: 1 !important;
                max-width: none !important;
                pointer-events: auto !important;
                transform: none !important;
                overflow: hidden !important;
            }
            .sidebar-header-brand span {
                display: block !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            /* Profile info: nama di atas, badge peran di bawah */
            .sidebar-profile-info {
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                min-width: 0 !important;
                flex: 1 !important;
                opacity: 1 !important;
                max-width: none !important;
                pointer-events: auto !important;
                transform: none !important;
                overflow: hidden !important;
            }
            .sidebar-profile-actions {
                display: flex !important;
                align-items: center !important;
                flex-shrink: 0 !important;
                opacity: 1 !important;
                max-width: none !important;
                pointer-events: auto !important;
                transform: none !important;
            }

            /* Di mobile, nav links harus full-width & rapi sejajar */
            #sidebarNav {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                width: 100% !important;
                overflow-y: auto !important;
                padding: 10px 12px !important;
            }
            .sidebar-nav-link {
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                border-radius: 9999px !important;
            }
            .sidebar-nav-link .rail-pill-inner {
                display: flex !important;
                align-items: center !important;
                gap: 14px !important;
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                position: relative !important;
            }
            .sidebar-nav-link .sidebar-label {
                opacity: 1 !important;
                transform: none !important;
                pointer-events: auto !important;
                display: block !important;
                white-space: nowrap !important;
            }

            .sidebar-wave-wrap {
                opacity: 1 !important;
                display: block !important;
            }
            #liquidActiveIndicator {
                display: block !important;
            }
        }

        #appSidebar:not(.is-collapsed) .sidebar-nav-link {
            position: relative;
            z-index: 10;
            padding: 10px 16px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            transition: color 0.2s ease,
                        background-color 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        #appSidebar:not(.is-collapsed) .sidebar-nav-link:not(.is-active):hover {
            background-color: rgba(255, 255, 255, 0.07);
            color: #ffffff;
            transform: translateX(3px);
        }

        #appSidebar:not(.is-collapsed) .sidebar-nav-link:not(.is-active):hover svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
            transform: scale(1.08);
        }

        #appSidebar:not(.is-collapsed) .sidebar-nav-link .rail-pill-inner {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
        }

        #appSidebar:not(.is-collapsed) .sidebar-nav-link .rail-icon-box {
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        #appSidebar:not(.is-collapsed) .sidebar-nav-link .rail-icon-box svg {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        stroke 0.2s ease,
                        color 0.2s ease;
        }

        #liquidActiveIndicator {
            position: absolute;
            left: 12px;
            right: 12px;
            background-color: var(--sg-blue);
            border-radius: 9999px;
            z-index: 1;
            pointer-events: none;
            box-shadow: 0 6px 20px -2px rgba(0, 119, 204, 0.55);
            transition: top 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                        height 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                        opacity 0.25s ease;
            will-change: top, height;
        }

        .nav-ripple-ring {
            position: absolute;
            border-radius: 9999px;
            border: 1.5px solid rgba(125, 211, 252, 0.6);
            background: radial-gradient(circle, rgba(0, 119, 204, 0.35) 0%, rgba(0, 119, 204, 0) 70%);
            transform: scale(0.2);
            opacity: 1;
            pointer-events: none;
            z-index: 25;
            animation: sgRippleOut 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes sgRippleOut {
            to { transform: scale(2.2); opacity: 0; }
        }

        #dynamicIslandWrapper {
            position: relative;
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            z-index: 70;
        }

        #dynamicIsland {
            position: absolute;
            top: 0;
            left: 0;
            height: 46px;
            width: 46px;
            border-radius: 9999px;
            background-color: transparent;
            display: flex;
            align-items: center;
            overflow: hidden;
            box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
            transition: width 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                        height 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                        top 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                        left 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                        background-color 0.28s ease,
                        box-shadow 0.32s cubic-bezier(0.16, 1, 0.3, 1),
                        padding 0.32s ease;
            will-change: width, height, top, left;
        }

        #dynamicIsland.island-open {
            top: -4px;
            left: -4px;
            height: 54px;
            width: 310px;
            padding: 4px 10px 4px 4px;
            background-color: var(--sg-island-bg);
            box-shadow: 0 16px 36px -4px rgba(2, 18, 32, 0.7),
                        0 0 0 1px rgba(255, 255, 255, 0.1);
        }

        #dynamicIsland .island-logo-btn {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        background-color 0.2s ease,
                        box-shadow 0.2s ease;
        }
        #dynamicIsland .island-logo-btn:hover {
            transform: scale(1.05);
        }
        #dynamicIsland .island-logo-btn:active {
            transform: scale(0.95);
        }

        #dynamicIsland .island-content {
            opacity: 0;
            transform: translateX(-12px) scale(0.96);
            pointer-events: none;
            white-space: nowrap;
            transition: opacity 0.2s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #dynamicIsland.island-open .island-content {
            opacity: 1;
            transform: translateX(0) scale(1);
            pointer-events: auto;
            transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1) 0.08s,
                        transform 0.32s cubic-bezier(0.16, 1, 0.3, 1) 0.06s;
        }

        #islandNotifDot {
            transition: opacity 0.2s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            animation: sgNotifPulse 2.4s ease-in-out infinite;
        }

        #dynamicIsland.island-open #islandNotifDot {
            opacity: 0;
            transform: scale(0);
            animation: none;
        }

        @keyframes sgNotifPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(156, 192, 224, 0.6);
            }
            50% {
                transform: scale(1.15);
                box-shadow: 0 0 0 4px rgba(156, 192, 224, 0);
            }
        }

        .sidebar-wave-wrap {
            position: absolute;
            inset-inline: 0;
            bottom: 0;
            height: 112px;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
            transition: opacity 0.35s ease;
        }

        .wave-layer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            height: 100%;
            display: flex;
            will-change: transform;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.6s ease;
        }

        .wave-layer-1 {
            animation: sgWaveDrift 14s linear infinite;
            opacity: 0.45;
        }
        .wave-layer-2 {
            animation: sgWaveDriftReverse 10s linear infinite;
            opacity: 0.65;
        }

        #appSidebar.wave-surge .wave-layer-1 {
            transform: scaleY(1.25) translateY(-4px);
            opacity: 0.65;
        }
        #appSidebar.wave-surge .wave-layer-2 {
            transform: scaleY(1.32) translateY(-6px);
            opacity: 0.85;
        }

        @keyframes sgWaveDrift {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @keyframes sgWaveDriftReverse {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(0); }
        }

        #railProfilePopup {
            opacity: 0;
            visibility: hidden;
            transform: translateX(-10px) scale(0.94);
            transform-origin: bottom left;
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        visibility 0.25s;
            pointer-events: none;
        }
        #railProfilePopup.popup-open {
            opacity: 1;
            visibility: visible;
            transform: translateX(0) scale(1);
            pointer-events: auto;
        }

        #railProfileTrigger {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                        background-color 0.2s ease,
                        box-shadow 0.2s ease;
        }
        #railProfileTrigger:hover {
            transform: scale(1.06);
            box-shadow: 0 4px 14px rgba(0, 119, 204, 0.4);
        }
        #railProfileTrigger:active {
            transform: scale(0.95);
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 flex h-screen overflow-hidden antialiased">

    <!-- Mobile Drawer Backdrop -->
    <div id="mobileSidebarBackdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <!-- ===================== SIDEBAR NAVIGATION (FLOATING RAIL & WAVE) ===================== -->
    <aside id="appSidebar" class="fixed inset-y-0 left-0 z-50 text-slate-200 flex flex-col shrink-0 select-none lg:static">
        
        <!-- 1. HEADER LOGO & DYNAMIC ISLAND -->
        <div class="sidebar-header h-[74px] px-4 sm:px-5 flex items-center justify-between border-b border-white/10 shrink-0 relative z-40 gap-2.5">
            <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
                
                <!-- Dynamic Island Container -->
                <div id="dynamicIslandWrapper" class="relative w-[46px] h-[46px] shrink-0">
                    <div id="dynamicIsland">
                        <button type="button" onclick="toggleDynamicIsland(event)" 
                                title="Presensi mandiri santri"
                                class="island-logo-btn w-[46px] h-[46px] rounded-full bg-[#0077cc] hover:bg-[#0084e0] text-white flex items-center justify-center shrink-0 shadow-md shadow-[#0077cc]/40 transition cursor-pointer relative focus:outline-none">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/>
                            </svg>
                            <span id="islandNotifDot" class="absolute top-0.5 right-0.5 w-3 h-3 rounded-full bg-emerald-400 border-2 border-[#042f4b] transition-all duration-200"></span>
                        </button>

                        <div class="island-content flex items-center justify-between flex-1 pl-3 pr-1.5 gap-3">
                            <div class="leading-tight">
                                <p class="text-[13px] font-bold text-white tracking-tight">
                                    Presensi Santri Aktif
                                </p>
                                <p class="text-[11px] text-[#8ab4d8] font-medium mt-0.5">
                                    Konfirmasi kehadiran
                                </p>
                            </div>
                            <a href="<?= base_url('siswa/presensi'); ?>" 
                               class="px-3.5 py-1.5 rounded-full bg-[#0077cc] hover:bg-[#0086e6] text-white font-bold text-xs transition shadow-sm shrink-0">
                                Absen
                            </a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-header-brand min-w-0 flex-1 leading-tight overflow-hidden">
                    <span class="font-extrabold text-white text-[16px] tracking-tight block truncate sg-display">Syiar Gemilang</span>
                    <span class="text-[11px] text-[#8ab4d8] font-medium block truncate mt-0.5">Portal Santri Mandiri</span>
                </div>
            </div>

            <div class="sidebar-header-actions flex items-center gap-1.5 shrink-0">
                <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer" aria-label="Tutup Menu">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
                <button type="button" id="sidebarToggleBtn" onclick="toggleSidebar()" 
                        title="Ubah ke Mode Rail Melayang"
                        class="hidden lg:flex w-8 h-8 rounded-lg bg-white/10 hover:bg-[#0077cc] text-[#9ab8d3] hover:text-white items-center justify-center transition shrink-0 cursor-pointer">
                    <i id="sidebarToggleIcon" class="fa-solid fa-chevron-left text-xs transition-transform duration-300"></i>
                </button>
            </div>
        </div>

        <!-- 2. DAFTAR IKON MENU NAVIGASI -->
        <nav id="sidebarNav" class="flex-1 px-3 py-3 space-y-1 text-sm font-medium overflow-y-auto no-scrollbar relative z-30">
            <div id="liquidActiveIndicator" class="opacity-0"></div>

            <!-- 1. Beranda Santri -->
            <?php $activeDash = (url_is('siswa') || url_is('siswa/dashboard') || url_is('siswa/')); ?>
            <a href="<?= base_url('siswa'); ?>" data-nav-id="dashboard"
               class="sidebar-nav-link nav-link <?= $activeDash ? 'is-active text-white font-semibold' : 'text-[#9ab8d3] hover:text-white' ?>">
                <div class="rail-pill-inner">
                    <span class="rail-icon-box">
                        <svg class="w-5 h-5 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 10.5L12 3l9 7.5"/>
                            <path d="M5 9.5V19a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Beranda santri</span>
                </div>
            </a>

            <!-- 2. Presensi Mandiri -->
            <?php $activePresensi = url_is('siswa/presensi*'); ?>
            <a href="<?= base_url('siswa/presensi'); ?>" data-nav-id="presensi"
               class="sidebar-nav-link nav-link <?= $activePresensi ? 'is-active text-white font-semibold' : 'text-[#9ab8d3] hover:text-white' ?>">
                <div class="rail-pill-inner">
                    <span class="rail-icon-box">
                        <svg class="w-5 h-5 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="4" width="16" height="16" rx="2"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Presensi mandiri</span>
                </div>
            </a>
        </nav>

        <!-- 3. OMBAK DI DASAR -->
        <div class="sidebar-wave-wrap">
            <div class="wave-layer wave-layer-1">
                <svg class="w-1/2 h-full" viewBox="0 0 400 100" preserveAspectRatio="none">
                    <path d="M0,40 C70,10 130,70 200,40 C270,10 330,70 400,40 L400,100 L0,100 Z" fill="#005596"/>
                </svg>
                <svg class="w-1/2 h-full" viewBox="0 0 400 100" preserveAspectRatio="none">
                    <path d="M0,40 C70,10 130,70 200,40 C270,10 330,70 400,40 L400,100 L0,100 Z" fill="#005596"/>
                </svg>
            </div>
            <div class="wave-layer wave-layer-2">
                <svg class="w-1/2 h-full" viewBox="0 0 400 100" preserveAspectRatio="none">
                    <path d="M0,55 C65,80 135,25 200,55 C265,85 335,25 400,55 L400,100 L0,100 Z" fill="#0c446e"/>
                </svg>
                <svg class="w-1/2 h-full" viewBox="0 0 400 100" preserveAspectRatio="none">
                    <path d="M0,55 C65,80 135,25 200,55 C265,85 335,25 400,55 L400,100 L0,100 Z" fill="#0c446e"/>
                </svg>
            </div>
        </div>

        <!-- 4. FOOTER PROFIL USER & POPUP MENU -->
        <div class="sidebar-footer px-4 py-4 shrink-0 relative z-40">
            <div class="sidebar-profile-box flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5 min-w-0 flex-1 overflow-hidden">
                    
                    <button type="button" id="railProfileTrigger" onclick="toggleProfilePopup(event)"
                            title="<?= esc($userName); ?> - Klik untuk opsi akun"
                            class="w-10 h-10 rounded-full bg-[#13466b] hover:bg-[#0077cc] text-white font-bold text-sm flex items-center justify-center shrink-0 transition shadow-sm cursor-pointer overflow-hidden focus:outline-none">
                        <?php if (session()->has('avatar') && !empty(session()->get('avatar'))): ?>
                            <img src="<?= esc(session()->get('avatar')); ?>" alt="<?= esc($userName); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span><?= esc($initials); ?></span>
                        <?php endif; ?>
                    </button>

                    <div class="sidebar-profile-info min-w-0 flex-1 leading-tight overflow-hidden">
                        <h4 class="text-[13px] font-bold text-white truncate"><?= esc($userName); ?></h4>
                        <div class="mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9.5px] font-bold bg-[#0c3554] text-[#7dd3fc] border border-[#255c85] uppercase tracking-wider truncate max-w-full">
                                SANTRI
                            </span>
                        </div>
                    </div>
                </div>

                <div class="sidebar-profile-actions flex items-center shrink-0">
                    <button type="button" onclick="openLogoutModal()" title="Keluar / Logout" 
                       class="w-8 h-8 rounded-xl bg-[#072c47] hover:bg-rose-600 text-white flex items-center justify-center transition shrink-0 cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Popup Menu Saat Avatar Diklik di Mode Rail -->
            <div id="railProfilePopup" class="absolute left-[76px] bottom-2 w-60 bg-[#021e32] text-white rounded-2xl p-3.5 shadow-2xl border border-white/10 z-50">
                <div class="flex items-center gap-3 pb-3 mb-2.5 border-b border-white/10">
                    <div class="w-10 h-10 rounded-full bg-[#0077cc] text-white font-bold text-xs flex items-center justify-center shrink-0">
                        <?= esc($initials); ?>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate"><?= esc($userName); ?></p>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white/10 text-sky-200">
                            SANTRI
                        </span>
                    </div>
                </div>

                <div class="space-y-1 text-xs font-medium">
                    <a href="<?= base_url('profil'); ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[#9ab8d3] hover:bg-white/10 hover:text-white transition">
                        <i class="fa-solid fa-user-gear w-4 text-center"></i>
                        <span>Pengaturan Profil</span>
                    </a>
                    <button type="button" onclick="toggleSidebar(); closeProfilePopup();" class="hidden lg:flex w-full items-center gap-2.5 px-3 py-2 rounded-xl text-[#9ab8d3] hover:bg-white/10 hover:text-white transition cursor-pointer text-left">
                        <i class="fa-solid fa-arrows-left-right w-4 text-center"></i>
                        <span id="popupModeLabel">Perlebar Sidebar (Mode Ombak)</span>
                    </button>
                    <button type="button" onclick="closeProfilePopup(); openLogoutModal();" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-400 hover:bg-rose-500/20 hover:text-rose-200 transition cursor-pointer text-left">
                        <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                        <span>Keluar / Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- ===================== MODAL KONFIRMASI LOGOUT ===================== -->
    <div id="logoutConfirmationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none opacity-0 invisible transition-all duration-300">
        <div id="logoutBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs opacity-0 transition-opacity duration-300" onclick="closeLogoutModal()"></div>

        <div id="logoutModalCard" class="relative bg-white w-full max-w-sm sm:max-w-md rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform scale-95 translate-y-3 opacity-0 transition-all duration-300 ease-out z-10">
            <div class="h-2 bg-gradient-to-r from-rose-500 via-[#0077CC] to-[#042f4b]"></div>

            <div class="p-6 sm:p-7 text-center">
                <div class="w-14 h-14 mx-auto mb-3.5 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-xl shadow-xs">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-slate-800 sg-display mb-1.5">
                    Konfirmasi Keluar Sistem
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-xs mx-auto">
                    Apakah Anda yakin ingin keluar dari sistem <strong class="text-slate-700">Syiar Gemilang</strong>? Anda harus masuk kembali untuk memantau capaian belajar dan presensi.
                </p>

                <div class="mt-4 p-2.5 bg-slate-50 rounded-xl border border-slate-200 inline-flex items-center gap-2 text-xs text-slate-600 max-w-full truncate">
                    <span class="w-6 h-6 rounded-full bg-[#0077CC] text-white flex items-center justify-center text-3xs font-bold shrink-0">
                        <?= esc($initials); ?>
                    </span>
                    <span class="font-semibold text-slate-800 truncate"><?= esc($userName); ?></span>
                    <span class="text-3xs text-slate-400">•</span>
                    <span class="text-3xs font-bold text-sky-700 bg-sky-100/70 px-2 py-0.5 rounded-md uppercase tracking-wider">SANTRI</span>
                </div>

                <div class="mt-6 flex items-center justify-center gap-2.5">
                    <button type="button" onclick="closeLogoutModal()" 
                            class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs sm:text-sm transition cursor-pointer">
                        Batal
                    </button>
                    <a href="<?= base_url('logout'); ?>" 
                       class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-rose-600/25 transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        <span>Ya, Keluar</span>
                    </a>
                </div>
            </div>

            <button type="button" onclick="closeLogoutModal()" class="absolute top-3.5 right-3.5 text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- ===================== VANILLA JS: RAIL, DYNAMIC ISLAND, LIQUID PILL & WAVE ===================== -->
    <script>
        (function() {
            const sidebar = document.getElementById('appSidebar');
            const popupLabel = document.getElementById('popupModeLabel');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const savedState = localStorage.getItem('sidebar_collapsed');
            const isRail = (savedState === null) ? true : (savedState === 'true');

            if (sidebar) {
                if (window.innerWidth >= 1024) {
                    if (isRail) {
                        sidebar.classList.add('is-collapsed');
                    } else {
                        sidebar.classList.remove('is-collapsed');
                    }
                    if (popupLabel) {
                        popupLabel.textContent = isRail ? 'Perlebar Sidebar (Mode Ombak)' : 'Kecilkan ke Mode Rail';
                    }
                    if (toggleBtn) {
                        toggleBtn.title = isRail ? 'Perlebar Sidebar' : 'Ubah ke Mode Rail Melayang';
                    }
                } else {
                    sidebar.classList.remove('is-collapsed', 'mobile-open');
                }
            }

            window.addEventListener('DOMContentLoaded', function() {
                initLiquidIndicator();
                initNavClickEffects();
            });

            window.addEventListener('resize', function() {
                if (!sidebar) return;
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('mobile-open');
                    const backdrop = document.getElementById('mobileSidebarBackdrop');
                    if (backdrop) {
                        backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                        backdrop.classList.add('opacity-0', 'pointer-events-none');
                    }
                    document.body.classList.remove('overflow-hidden');
                    const saved = localStorage.getItem('sidebar_collapsed');
                    const rail = (saved === null) ? true : (saved === 'true');
                    if (rail) {
                        sidebar.classList.add('is-collapsed');
                    } else {
                        sidebar.classList.remove('is-collapsed');
                    }
                } else {
                    sidebar.classList.remove('is-collapsed');
                }
            });
        })();

        let islandTimer = null;
        function toggleDynamicIsland(e) {
            if (e) e.stopPropagation();
            const island = document.getElementById('dynamicIsland');
            if (!island) return;

            const isOpen = island.classList.toggle('island-open');
            if (islandTimer) clearTimeout(islandTimer);

            if (isOpen) {
                islandTimer = setTimeout(() => {
                    island.classList.remove('island-open');
                }, 6000);
            }
        }

        function toggleProfilePopup(e) {
            if (e) e.stopPropagation();
            const popup = document.getElementById('railProfilePopup');
            if (!popup) return;
            popup.classList.toggle('popup-open');
        }

        function closeProfilePopup() {
            const popup = document.getElementById('railProfilePopup');
            if (popup) popup.classList.remove('popup-open');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const popupLabel = document.getElementById('popupModeLabel');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            if (!sidebar) return;

            const isCollapsed = sidebar.classList.toggle('is-collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false');

            if (popupLabel) {
                popupLabel.textContent = isCollapsed ? 'Perlebar Sidebar (Mode Ombak)' : 'Kecilkan ke Mode Rail';
            }
            if (toggleBtn) {
                toggleBtn.title = isCollapsed ? 'Perlebar Sidebar' : 'Ubah ke Mode Rail Melayang';
            }

            if (!isCollapsed) {
                setTimeout(initLiquidIndicator, 220);
            }
        }

        function initLiquidIndicator() {
            const sidebar = document.getElementById('appSidebar');
            const nav = document.getElementById('sidebarNav');
            const indicator = document.getElementById('liquidActiveIndicator');
            const activeLink = nav ? nav.querySelector('.sidebar-nav-link.is-active') : null;

            if (!sidebar || !nav || !indicator || !activeLink) return;

            const targetTop = activeLink.offsetTop;
            const targetHeight = activeLink.offsetHeight;
            const prevTop = parseFloat(sessionStorage.getItem('sg_prev_nav_top'));

            if (!isNaN(prevTop) && Math.abs(prevTop - targetTop) > 4 && !sidebar.classList.contains('is-collapsed')) {
                const minTop = Math.min(prevTop, targetTop);
                const stretchDist = Math.abs(targetTop - prevTop) + targetHeight;

                indicator.classList.remove('opacity-0');
                indicator.style.opacity = '1';

                if (typeof indicator.animate === 'function') {
                    const anim = indicator.animate([
                        { top: prevTop + 'px', height: targetHeight + 'px', offset: 0, easing: 'cubic-bezier(0.2, 0.85, 0.25, 1)' },
                        { top: minTop + 'px', height: (stretchDist * 1.03) + 'px', offset: 0.42, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' },
                        { top: targetTop + 'px', height: targetHeight + 'px', offset: 1 }
                    ], {
                        duration: 380,
                        fill: 'forwards'
                    });
                    anim.onfinish = () => {
                        indicator.style.top = targetTop + 'px';
                        indicator.style.height = targetHeight + 'px';
                    };
                } else {
                    indicator.style.top = targetTop + 'px';
                    indicator.style.height = targetHeight + 'px';
                }

                triggerWaveSurge();
            } else {
                indicator.style.top = targetTop + 'px';
                indicator.style.height = targetHeight + 'px';
                indicator.classList.remove('opacity-0');
                indicator.style.opacity = '1';
            }

            sessionStorage.setItem('sg_prev_nav_top', targetTop);
        }

        function initNavClickEffects() {
            const nav = document.getElementById('sidebarNav');
            const links = document.querySelectorAll('.sidebar-nav-link');
            if (!nav || !links.length) return;

            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    const currentActive = nav.querySelector('.sidebar-nav-link.is-active');
                    if (currentActive) {
                        sessionStorage.setItem('sg_prev_nav_top', currentActive.offsetTop);
                    }
                    triggerWaveSurge();

                    const rect = link.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    ripple.className = 'nav-ripple-ring';
                    const size = Math.max(rect.width, rect.height, 48);
                    ripple.style.width = size + 'px';
                    ripple.style.height = size + 'px';
                    const clickX = (e && typeof e.clientX === 'number') ? (e.clientX - rect.left) : (rect.width / 2);
                    const clickY = (e && typeof e.clientY === 'number') ? (e.clientY - rect.top) : (rect.height / 2);
                    ripple.style.left = (clickX - size / 2) + 'px';
                    ripple.style.top = (clickY - size / 2) + 'px';
                    link.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        }

        function triggerWaveSurge() {
            const sidebar = document.getElementById('appSidebar');
            if (!sidebar) return;
            sidebar.classList.add('wave-surge');
            setTimeout(() => {
                sidebar.classList.remove('wave-surge');
            }, 750);
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('mobileSidebarBackdrop');
            if (!sidebar || !backdrop) return;

            if (window.innerWidth < 1024) {
                sidebar.classList.remove('is-collapsed');
            }

            const isOpen = sidebar.classList.toggle('mobile-open');
            if (isOpen) {
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100', 'pointer-events-auto');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
                setTimeout(initLiquidIndicator, 120);
            } else {
                backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        }

        function openLogoutModal() {
            const modal = document.getElementById('logoutConfirmationModal');
            const backdrop = document.getElementById('logoutBackdrop');
            const card = document.getElementById('logoutModalCard');
            if (modal) {
                modal.classList.remove('pointer-events-none', 'opacity-0', 'invisible');
                modal.classList.add('pointer-events-auto', 'opacity-100', 'visible');
                if (backdrop) {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                }
                if (card) {
                    card.classList.remove('scale-95', 'translate-y-3', 'opacity-0');
                    card.classList.add('scale-100', 'translate-y-0', 'opacity-100');
                }
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutConfirmationModal');
            const backdrop = document.getElementById('logoutBackdrop');
            const card = document.getElementById('logoutModalCard');
            if (modal) {
                if (backdrop) {
                    backdrop.classList.remove('opacity-100');
                    backdrop.classList.add('opacity-0');
                }
                if (card) {
                    card.classList.remove('scale-100', 'translate-y-0', 'opacity-100');
                    card.classList.add('scale-95', 'translate-y-3', 'opacity-0');
                }
                setTimeout(() => {
                    modal.classList.remove('pointer-events-auto', 'opacity-100', 'visible');
                    modal.classList.add('pointer-events-none', 'opacity-0', 'invisible');
                    document.body.classList.remove('overflow-hidden');
                }, 280);
            }
        }

        document.addEventListener('click', function(e) {
            const island = document.getElementById('dynamicIsland');
            const islandWrap = document.getElementById('dynamicIslandWrapper');
            if (island && islandWrap && !islandWrap.contains(e.target)) {
                island.classList.remove('island-open');
            }

            const popup = document.getElementById('railProfilePopup');
            const trigger = document.getElementById('railProfileTrigger');
            if (popup && trigger && !popup.contains(e.target) && !trigger.contains(e.target)) {
                popup.classList.remove('popup-open');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
                closeProfilePopup();
                const island = document.getElementById('dynamicIsland');
                if (island) island.classList.remove('island-open');
                const sidebar = document.getElementById('appSidebar');
                if (sidebar && sidebar.classList.contains('mobile-open')) {
                    toggleMobileSidebar();
                }
            }
        });
    </script>
