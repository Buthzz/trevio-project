<?php
// [FIX ERROR]: Load Config & Helper jika belum ada
// Ini memastikan BASE_URL selalu tersedia, bahkan jika file diakses langsung
if (!defined('BASE_URL')) {
    $configPath = __DIR__ . '/../../../config/app.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    } else {
        // Fallback darurat jika config tidak ketemu
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/trevio-project/public');
    }
}

require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// [SECURITY]: Redirect ke home jika user sudah login
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    $homeUrl = (defined('BASE_URL') ? BASE_URL : '') . '/home';
    header("Location: $homeUrl");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login' ?> - Trevio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif';
            --color-trevio: #0EA5E9;
            --color-trevio-dark: #0284C7;
        }
    </style>
</head>

<body class="min-h-screen bg-[#F5F7FA] font-sans text-base text-[#111827]">
    <?php require __DIR__ . '/../layouts/header.php'; ?>

    <main
        class="flex items-start md:items-center justify-center px-4 py-6 md:px-6 md:py-10 md:min-h-[calc(100vh-10px)]">
        <div
            class="bg-white rounded-[24px] shadow-[0px_4px_24px_0px_rgba(0,0,0,0.08)] max-w-[840px] w-full md:w-auto mx-auto flex flex-col md:flex-row md:max-h-[520px] md:overflow-hidden">

            <section class="md:w-[46%] relative min-h-[280px] md:min-h-[440px] order-1 md:order-none">
                <div class="absolute inset-0 bg-cover bg-center"
                    style="background-image: url('<?= BASE_URL ?>/images/photo-1571896349842-33c89424de2d.jpg');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/35 to-transparent"></div>
                <div class="relative z-10 h-full flex flex-col justify-end p-7 text-white">
                    <span
                        class="inline-flex bg-white/15 backdrop-blur-md px-4 py-1.5 rounded-full mb-5 border border-white/25 text-xs tracking-widest"
                        style="user-select: none;">
                        TREVIO MEMBER
                    </span>
                    <h2 class="text-[24px] font-semibold mb-2.5" style="user-select: none;">Kembali Berpetualang</h2>
                    <p class="text-white/85 leading-relaxed text-xs" style="user-select: none;">
                        Akses ribuan hotel eksklusif dan kelola perjalanan Anda dengan mudah dalam satu dasbor.
                    </p>
                </div>
            </section>

            <section class="md:w-[54%] p-6 md:p-10 flex flex-col justify-center order-2 md:order-none">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#111827] mb-1">Selamat Datang! 👋</h1>
                    <p class="text-sm text-[#6B7280]">Silakan masuk untuk melanjutkan.</p>
                </div>

                <?php if (isset($_SESSION['flash_error'])): ?>
                    <div
                        class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm flex items-start gap-2 mb-4">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['flash_success'])): ?>
                    <div
                        class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-start gap-2 mb-4">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/auth/authenticate" class="space-y-4" autocomplete="off">
                    <?= trevio_csrf_field() ?>

                    <div class="form-group">
                        <label for="email" class="block text-sm font-semibold text-[#374151] mb-1.5">EMAIL
                            ADDRESS</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                                    <path d="M22 6L12 13L2 6" />
                                </svg>
                            </span>
                            <input id="email" name="email" type="email" placeholder="nama@email.com" required
                                class="w-full pl-12 pr-4 py-2.5 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-trevio focus:border-transparent transition-all placeholder:text-[#9CA3AF]">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-semibold text-[#374151]">PASSWORD</label>
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" />
                                    <path
                                        d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" />
                                </svg>
                            </span>
                            <input id="password" name="password" type="password" placeholder="••••••••" required
                                class="w-full pl-12 pr-4 py-2.5 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-trevio focus:border-transparent transition-all placeholder:text-[#9CA3AF]">
                        </div>
                        <div class="mt-1.5 flex justify-end">
                            <a href="#" class="text-xs text-trevio hover:text-trevio-dark transition-colors">Lupa
                                Password?</a>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-trevio text-white px-4 py-2.5 rounded-lg hover:bg-trevio-dark transition-all duration-200 flex items-center justify-center gap-2 font-medium shadow-sm hover:shadow-md">
                        <span>Masuk Sekarang</span>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12H19M19 12L12 5M19 12L12 19" />
                        </svg>
                    </button>
                </form>

                <p class="text-center text-xs text-[#6B7280] mt-6">
                    Belum punya akun?
                    <a href="<?= defined('BASE_URL') ? BASE_URL . '/auth/register' : '#' ?>"
                        class="text-trevio hover:text-trevio-dark transition-colors font-medium">Daftar Sekarang</a>
                </p>
            </section>
        </div>
    </main>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
</body>

</html>