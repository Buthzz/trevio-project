<?php
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// [SECURITY]: Redirect jika sudah login
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    $homeUrl = trevio_view_route('home/index.php');
    header("Location: $homeUrl");
    exit;
}

// [BACKEND NOTE]: Handle login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // [SECURITY]: Verifikasi CSRF Token
    if (!trevio_verify_csrf()) {
        die('Akses ditolak: Token CSRF tidak valid. Silakan refresh halaman.');
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // [SECURITY]: Validasi input
    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        // [BACKEND NOTE]: Simulasi validasi user dari database
        // User dummy: user@gmail.com / password123
        $validEmail = 'user@gmail.com';
        $validPassword = 'password123';

        if ($email === $validEmail && $password === $validPassword) {
            // Simulasi data user dari database
            $dummyUser = [
                'id' => 1,
                'name' => 'Trevio User',
                'email' => $email,
                'avatar' => 'https://ui-avatars.com/api/?name=Trevio+User&background=0EA5E9&color=fff',
                'role' => 'guest'
            ];

            // Set session
            $_SESSION['user_id'] = $dummyUser['id'];
            $_SESSION['user_name'] = $dummyUser['name'];
            $_SESSION['user_email'] = $dummyUser['email'];
            $_SESSION['user_avatar'] = $dummyUser['avatar'];
            $_SESSION['user_role'] = $dummyUser['role'];
            $_SESSION['is_logged_in'] = true;
            $_SESSION['login_provider'] = 'email';

            // Redirect ke home atau return_url
            $returnUrl = $_GET['return_url'] ?? trevio_view_route('home/index.php');
            $returnUrl = urldecode($returnUrl);
            
            if (strpos($returnUrl, 'home/index.php') !== false) {
                $returnUrl .= (strpos($returnUrl, '?') === false ? '?' : '&') . 'login_success=email';
            }

            header("Location: $returnUrl");
            exit;
        } else {
            // [SECURITY]: Pesan error generik
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trevio &mdash; Login Pengguna</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        trevio: '#0EA5E9',
                        'trevio-dark': '#0284C7',
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <?php
    // Header global
    require __DIR__ . '/../layouts/header.php';
    ?>

    <main class="flex items-start md:items-center justify-center px-4 py-6 md:px-6 md:py-10 md:min-h-[calc(100vh-10px)]">
        <div class="bg-white rounded-[24px] shadow-[0px_4px_24px_0px_rgba(0,0,0,0.08)] max-w-[840px] w-full md:w-auto mx-auto flex flex-col md:flex-row md:max-h-[520px] md:overflow-hidden">
            
            <!-- Kolom kiri: Hero Image -->
            <section class="md:w-[46%] relative min-h-[280px] md:min-h-[440px] order-1 md:order-none">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=1000&fit=crop');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/35 to-transparent"></div>
                <div class="relative z-10 h-full flex flex-col justify-end p-7 text-white">
                    <span class="inline-flex bg-white/15 backdrop-blur-md px-4 py-1.5 rounded-full mb-5 border border-white/25 text-xs tracking-widest" style="user-select: none;">
                        TREVIO MEMBER 
                    </span>
                    <h2 class="text-[24px] font-semibold mb-2.5" style="user-select: none;">Kembali Berpetualang</h2>
                    <p class="text-white/85 leading-relaxed text-xs" style="user-select: none;">
                        Akses ribuan hotel eksklusif dan kelola perjalanan Anda dengan mudah dalam satu dasbor.
                    </p>
                </div>
            </section>

            <!-- Kolom kanan: Form Login -->
            <section class="md:w-[54%] p-6 md:p-10 flex flex-col justify-center order-2 md:order-none">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#111827] mb-1">Selamat Datang! 👋</h1>
                    <p class="text-sm text-[#6B7280]">Silakan masuk untuk melanjutkan.</p>
                </div>

                <form method="post" action="#" class="space-y-4" autocomplete="off">
                    <?= trevio_csrf_field() ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm mb-4">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="email" class="block text-sm font-semibold text-[#374151] mb-1.5">EMAIL ADDRESS</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="nama@email.com"
                                required
                                class="w-full pl-12 pr-4 py-2 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-trevio focus:border-transparent transition-all placeholder:text-[#9CA3AF]"
                            >
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-semibold text-[#374151]">PASSWORD</label>
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="••••••••"
                                required
                                class="w-full pl-12 pr-4 py-2 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-trevio focus:border-transparent transition-all placeholder:text-[#9CA3AF]">
                        </div>
                        <div class="mt-1.5 flex justify-end">
                            <a href="#" class="text-xs text-trevio hover:text-trevio-dark transition-colors">Lupa Password?</a>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-trevio text-white px-4 py-2 rounded-lg hover:bg-trevio-dark transition-colors flex items-center justify-center gap-2"
                    >
                        <span>Masuk Sekarang</span>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </form>

                <div class="flex items-center gap-3 mb-4 text-xs text-[#9CA3AF]">
                    <span class="flex-1 h-px bg-[#E5E7EB]"></span>
                    <span>Atau lanjutkan dengan</span>
                    <span class="flex-1 h-px bg-[#E5E7EB]"></span>
                </div>

                <div class="mb-5 flex flex-wrap justify-center gap-3">
                    <a href="google-callback.php?login_type=google&state=<?= trevio_csrf_token() ?>" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 md:px-16 py-2.5 border border-[#D1D5DB] rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                        </svg>
                        <span class="text-sm font-medium text-[#374151]">Google</span>
                    </a>
                </div>

                <p class="text-center text-xs text-[#6B7280]">
                    Belum punya akun?
                    <a href="./register.php" class="text-trevio hover:text-trevio-dark transition-colors font-medium"> Daftar Sekarang</a>
                </p>
            </section>
        </div>
    </main>
    <?php
    // Footer global menutup halaman login.
    require __DIR__ . '/../layouts/footer.php';
    ?>
</body>
</html>