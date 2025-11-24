<?php
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// [SECURITY]: Redirect jika sudah login
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    $homeUrl = trevio_view_route('home/index.php');
    header("Location: $homeUrl");
    exit;
}

// [BACKEND NOTE]: Handle registration logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // [SECURITY]: Verifikasi CSRF Token
    if (!trevio_verify_csrf()) {
        die('Akses ditolak: Token CSRF tidak valid. Silakan refresh halaman.');
    }

    $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';
    $userType = $_POST['user_type'] ?? 'guest';

    // Validasi input
    if (empty($fullName) || empty($email) || empty($password)) {
        $error = 'Semua field wajib diisi.';
    } elseif ($password !== $passwordConfirmation) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif ($email === 'user@gmail.com') {
        // [BACKEND NOTE]: Simulasi cek email duplikat
        $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
    } else {
        // Simulasi simpan user baru
        $newUser = [
            'id' => rand(1000, 9999),
            'name' => $fullName,
            'email' => $email,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($fullName) . '&background=random',
            'role' => $userType
        ];

        // Auto login setelah register
        $_SESSION['user_id'] = $newUser['id'];
        $_SESSION['user_name'] = $newUser['name'];
        $_SESSION['user_email'] = $newUser['email'];
        $_SESSION['user_avatar'] = $newUser['avatar'];
        $_SESSION['user_role'] = $newUser['role'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['login_provider'] = 'email';

        // Redirect ke home
        $homeUrl = trevio_view_route('home/index.php') . '?login_success=register';
        header("Location: $homeUrl");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trevio | Daftar Pengguna</title>
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
        <div class="bg-white rounded-[24px] shadow-[0px_4px_24px_0px_rgba(0,0,0,0.08)] max-w-[840px] w-full md:w-auto mx-auto flex flex-col md:flex-row md:overflow-hidden">
            <!-- Kolom kiri: form registrasi dan CTA -->
            <section class="md:w-[54%] p-6 md:p-10 flex flex-col justify-center order-2 md:order-none">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#111827] mb-1">Buat Akun Baru 🚀</h1>
                    <p class="text-sm text-[#6B7280]">Bergabunglah dengan Trevio dan mulai petualanganmu.</p>
                </div>

                <form method="post" action="#" class="space-y-3" autocomplete="off">
                    <?= trevio_csrf_field() ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm mb-4">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="full_name" class="block text-sm font-semibold text-[#374151] mb-1.5">FULL NAME</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                            <input
                                id="full_name"
                                name="full_name"
                                type="text"
                                placeholder="John Doe"
                                required
                                class="w-full pl-12 pr-4 py-2 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-trevio focus:border-transparent transition-all placeholder:text-[#9CA3AF]"
                            >
                        </div>
                    </div>

                    <div>
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
                        <label for="password" class="block text-sm font-semibold text-[#374151] mb-1.5">PASSWORD</label>
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
                                placeholder="Minimal 8 karakter"
                                minlength="8"
                                required
                                class="w-full pl-12 pr-4 py-2 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-trevio focus:border-transparent transition-all placeholder:text-[#9CA3AF]"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-[#374151] mb-1.5">KONFIRMASI PASSWORD</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"     
                                type="password"
                                placeholder="Ulangi password"
                                minlength="8"
                                required
                                class="w-full pl-12 pr-4 py-2 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-trevio focus:border-transparent transition-all placeholder:text-[#9CA3AF]"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="user_type" class="block text-sm font-semibold text-[#374151] mb-1.5">DAFTAR SEBAGAI</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                            <select
                                id="user_type"
                                name="user_type"
                                class="w-full appearance-none bg-white text-[#374151] pl-12 pr-10 py-2 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-trevio focus:border-transparent transition-all"
                                required
                            >
                                <option value="guest">Wisatawan (Guest)</option>
                                <option value="host">Pemilik Hotel (Host)</option>
                            </select>
                            <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-[#9CA3AF] leading-snug">
                        Ketika Anda menekan tombol <b class="text-xs text-[#9CA3AF]">Buat Akun</b>, berarti Anda menyetujui
                        <a href="#" class="text-trevio hover:text-trevio-dark">Terms of Service</a>
                        kami.
                    </p>

                    <button type="submit" class="w-full bg-trevio text-white px-4 py-2 rounded-lg hover:bg-trevio-dark transition-colors">Buat Akun</button>
                </form>

                <p class="text-center text-xs text-[#6B7280] mt-2">
                    Sudah punya akun?
                    <a href="./login.php" class="text-trevio hover:text-trevio-dark transition-colors font-medium"> Masuk di sini</a>
                </p>
            </section>

            <!-- Kolom kanan: hero slider storytelling -->
            <section id="auth-hero" class="md:w-[55%] relative min-h-[280px] md:min-h-[440px] order-1 md:order-2">
                <div id="auth-hero-bg" class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?w=800&h=1000&fit=crop');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="relative z-10 h-full flex flex-col justify-end p-7 text-white">
                    <button
                        id="auth-hero-prev"
                        type="button"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 text-white rounded-full p-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80"
                    >
                        <span class="sr-only">Sebelumnya</span>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 18L9 12L15 6"></path>
                        </svg>
                    </button>
                    <button
                        id="auth-hero-next"
                        type="button"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 text-white rounded-full p-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80"
                    >
                        <span class="sr-only">Berikutnya</span>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 6L15 12L9 18"></path>
                        </svg>
                    </button>
                    <h2 id="auth-hero-title" class="text-[24px] font-semibold mb-2">Mulai Perjalanan Anda</h2>
                    <p id="auth-hero-quote" class="text-white/80 leading-relaxed mb-3 text-xs">
                        &ldquo;Dunia adalah buku, dan mereka yang tidak melakukan perjalanan hanya membaca satu halaman.&rdquo;
                    </p>
                    <div id="auth-hero-dots" class="flex gap-2">
                        <span class="w-2 h-2 rounded-full bg-white" data-dot-index="0"></span>
                        <span class="w-2 h-2 rounded-full bg-white/40" data-dot-index="1"></span>
                        <span class="w-2 h-2 rounded-full bg-white/40" data-dot-index="2"></span>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <?php
    // Footer global menutup halaman registrasi.
    require __DIR__ . '/../layouts/footer.php';
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Skenario konten slider hero auth.
            const slides = [
                {
                    title: 'Mulai Perjalanan Anda',
                    quote: '"Dunia adalah buku, dan mereka yang tidak melakukan perjalanan hanya membaca satu halaman."',
                    image: 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?w=800&h=1000&fit=crop'
                },
                {
                    title: 'Temukan Perspektif Baru',
                    quote: '"Perjalanan bukan soal tempat yang Anda kunjungi, tetapi cerita yang Anda bawa pulang."',
                    image: 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?w=800&h=1000&fit=crop'
                },
                {
                    title: 'Rayakan Setiap Langkah',
                    quote: '"Jangan menunggu momen yang sempurna, jelajahilah dunia dan ciptakan momen itu sendiri."',
                    image: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800&h=1000&fit=crop'
                }
            ];

            // Ambil referensi elemen utama slider.
            const heroSection = document.querySelector('#auth-hero');
            const heroBackground = document.querySelector('#auth-hero-bg');
            const heroTitle = document.querySelector('#auth-hero-title');
            const heroQuote = document.querySelector('#auth-hero-quote');
            const dotsContainer = document.querySelector('#auth-hero-dots');
            const prevButton = document.querySelector('#auth-hero-prev');
            const nextButton = document.querySelector('#auth-hero-next');
            if (!heroSection || !heroBackground || !heroTitle || !heroQuote || !dotsContainer) {
                return;
            }

            // Kelas indikator aktif/nonaktif untuk dot slider.
            const activeClass = 'bg-white';
            const inactiveClass = 'bg-white/40';
            const dots = dotsContainer.querySelectorAll('[data-dot-index]');
            // Posisi slide saat ini.
            let currentIndex = 0;
            // Timer otomatis untuk rotasi slide.
            let rotationTimer = null;

            // Tandai dot mana yang aktif sesuai slide.
            const setActiveDot = function (index) {
                dots.forEach(function (dot, dotIndex) {
                    dot.classList.toggle(activeClass, dotIndex === index);
                    dot.classList.toggle(inactiveClass, dotIndex !== index);
                });
            };

            // Terapkan data slide ke elemen hero.
            const applySlide = function (index) {
                const slide = slides[index];
                heroTitle.textContent = slide.title;
                heroQuote.textContent = slide.quote;
                heroBackground.style.backgroundImage = "url('" + slide.image + "')";
                setActiveDot(index);
            };

            // Geser slide berdasarkan arah tertentu.
            const rotateSlide = function (step) {
                currentIndex = (currentIndex + step + slides.length) % slides.length;
                applySlide(currentIndex);
            };

            // Mulai interval autoplay agar hero tetap dinamis.
            const startRotation = function () {
                if (rotationTimer) {
                    clearInterval(rotationTimer);
                }
                rotationTimer = setInterval(function () {
                    rotateSlide(1);
                }, 30000);
            };

            if (prevButton) {
                // Navigasi ke slide sebelumnya dan reset timer.
                prevButton.addEventListener('click', function () {
                    rotateSlide(-1);
                    startRotation();
                });
            }

            if (nextButton) {
                // Navigasi ke slide selanjutnya dan reset timer.
                nextButton.addEventListener('click', function () {
                    rotateSlide(1);
                    startRotation();
                });
            }

            applySlide(currentIndex);
            startRotation();
        });
    </script>
</body>
</html>