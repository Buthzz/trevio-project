<?php
// [FIX]: Pastikan BASE_URL tersedia agar form action tidak error
if (!defined('BASE_URL')) {
    $configPath = __DIR__ . '/../../../config/app.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    } else {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/trevio-project/public');
    }
}

require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

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
    <title><?= $title ?? 'Daftar' ?> - Trevio</title>
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

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <?php require __DIR__ . '/../layouts/header.php'; ?>

    <main
        class="flex items-start md:items-center justify-center px-4 py-6 md:px-6 md:py-10 md:min-h-[calc(100vh-10px)]">
        <div
            class="bg-white rounded-[24px] shadow-[0px_4px_24px_0px_rgba(0,0,0,0.08)] max-w-[840px] w-full md:w-auto mx-auto flex flex-col md:flex-row md:overflow-hidden">

            <section class="md:w-[54%] p-6 md:p-10 flex flex-col justify-center order-2 md:order-none">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#111827] mb-1">Buat Akun Baru 🚀</h1>
                    <p class="text-sm text-[#6B7280]">Bergabunglah dengan Trevio dan mulai petualanganmu.</p>
                </div>

                <form method="POST" action="<?= BASE_URL ?>/auth/store" class="space-y-4" autocomplete="off"
                    id="registerForm">
                    <?= trevio_csrf_field() ?>

                    <?php if (isset($_SESSION['flash_error'])): ?>
                        <div
                            class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
                        </div>
                        <?php unset($_SESSION['flash_error']); ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="full_name" class="block text-sm font-semibold text-[#374151] mb-1.5">NAMA
                            LENGKAP</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                                    <path
                                        d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z">
                                    </path>
                                </svg>
                            </span>
                            <input id="full_name" name="full_name" type="text" placeholder="John Doe" required
                                minlength="3"
                                class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-gray-400 text-gray-900">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-[#374151] mb-1.5">EMAIL
                            ADDRESS</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z">
                                    </path>
                                    <path d="M22 6L12 13L2 6"></path>
                                </svg>
                            </span>
                            <input id="email" name="email" type="email" placeholder="nama@email.com" required
                                class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-gray-400 text-gray-900">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-[#374151] mb-1.5">PASSWORD</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z">
                                    </path>
                                    <path
                                        d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11">
                                    </path>
                                </svg>
                            </span>
                            <input id="password" name="password" type="password" placeholder="Minimal 8 karakter"
                                minlength="8" required
                                class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-gray-400 text-gray-900">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-semibold text-[#374151] mb-1.5">KONFIRMASI PASSWORD</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </span>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                placeholder="Ulangi password" minlength="8" required
                                class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-gray-400 text-gray-900">
                        </div>
                        <p id="password-match-error" class="hidden text-xs text-red-600 mt-1">Password tidak cocok!</p>
                    </div>

                    <div>
                        <label for="user_type" class="block text-sm font-semibold text-[#374151] mb-1.5">DAFTAR
                            SEBAGAI</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21">
                                    </path>
                                    <path
                                        d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z">
                                    </path>
                                </svg>
                            </span>
                            <select id="user_type" name="user_type"
                                class="w-full appearance-none bg-white text-gray-900 pl-12 pr-10 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                required>
                                <option value="guest">Wisatawan (Guest)</option>
                                <option value="host">Pemilik Hotel (Host)</option>
                            </select>
                            <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 leading-snug">
                        Ketika Anda menekan tombol <b class="text-gray-700">Buat Akun</b>, berarti Anda menyetujui
                        <a href="#" class="text-blue-600 hover:text-blue-800 underline">Terms of Service</a> kami.
                    </p>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg font-semibold text-sm">
                        Buat Akun
                    </button>
                </form>

                <p class="text-center text-xs text-gray-500 mt-4">
                    Sudah punya akun?
                    <a href="<?= BASE_URL ?>/auth/login"
                        class="text-blue-600 hover:text-blue-800 transition-colors font-medium">Masuk di sini</a>
                </p>
            </section>

            <section id="auth-hero"
                class="md:w-[46%] relative min-h-[280px] md:min-h-[560px] order-1 md:order-2 bg-gray-900">
                <div id="auth-hero-bg" class="absolute inset-0 bg-cover bg-center transition-all duration-700"
                    style="background-image: url('<?= BASE_URL ?>/images/photo-1526778548025-fa2f459cd5c1.jpg');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                <div class="relative z-10 h-full flex flex-col justify-end p-8 text-white">
                    <div class="absolute top-4 right-4 flex gap-2">
                    </div>

                    <div class="mb-6">
                        <h2 id="auth-hero-title"
                            class="text-2xl md:text-3xl font-bold mb-3 leading-tight transition-opacity duration-500">
                            Mulai Perjalanan Anda</h2>
                        <p id="auth-hero-quote"
                            class="text-white/90 leading-relaxed text-sm transition-opacity duration-500">"Dunia adalah
                            buku, dan mereka yang tidak melakukan perjalanan hanya membaca satu halaman."</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div id="auth-hero-dots" class="flex gap-2">
                            <span class="w-2 h-2 rounded-full bg-white transition-all" data-dot-index="0"></span>
                            <span class="w-2 h-2 rounded-full bg-white/40 transition-all" data-dot-index="1"></span>
                            <span class="w-2 h-2 rounded-full bg-white/40 transition-all" data-dot-index="2"></span>
                        </div>
                        <div class="flex gap-2">
                            <button id="auth-hero-prev" type="button"
                                class="bg-white/20 hover:bg-white/40 text-white rounded-full p-2 transition focus:outline-none backdrop-blur-sm">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M15 18L9 12L15 6" />
                                </svg>
                            </button>
                            <button id="auth-hero-next" type="button"
                                class="bg-white/20 hover:bg-white/40 text-white rounded-full p-2 transition focus:outline-none backdrop-blur-sm">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M9 6L15 12L9 18" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('registerForm');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');
            const errorMsg = document.getElementById('password-match-error');

            function validatePassword() {
                if (passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                    errorMsg.classList.remove('hidden');
                    passwordConfirmation.classList.add('border-red-500', 'focus:ring-red-500');
                    passwordConfirmation.classList.remove('border-gray-300', 'focus:ring-blue-500');
                } else {
                    errorMsg.classList.add('hidden');
                    passwordConfirmation.classList.remove('border-red-500', 'focus:ring-red-500');
                    passwordConfirmation.classList.add('border-gray-300', 'focus:ring-blue-500');
                }
            }

            password.addEventListener('input', validatePassword);
            passwordConfirmation.addEventListener('input', validatePassword);

            form.addEventListener('submit', function (e) {
                if (password.value !== passwordConfirmation.value) {
                    e.preventDefault();
                    errorMsg.classList.remove('hidden');
                    passwordConfirmation.focus();
                }
            });

            // Slider Logic
            const slides = [
                { title: 'Mulai Perjalanan Anda', quote: '"Dunia adalah buku, dan mereka yang tidak melakukan perjalanan hanya membaca satu halaman."', image: '<?= BASE_URL ?>/images/photo-1526778548025-fa2f459cd5c1.jpg' },
                { title: 'Temukan Perspektif Baru', quote: '"Perjalanan bukan soal tempat yang Anda kunjungi, tetapi cerita yang Anda bawa pulang."', image: '<?= BASE_URL ?>/images/photo-1489515217757-5fd1be406fef.jpg' },
                { title: 'Rayakan Setiap Langkah', quote: '"Jangan menunggu momen yang sempurna, jelajahilah dunia dan ciptakan momen itu sendiri."', image: '<?= BASE_URL ?>/images/photo-1469854523086-cc02fe5d8800.jpg' }
            ];

            const heroBackground = document.getElementById('auth-hero-bg');
            const heroTitle = document.getElementById('auth-hero-title');
            const heroQuote = document.getElementById('auth-hero-quote');
            const dots = document.querySelectorAll('[data-dot-index]');
            let currentIndex = 0;

            function updateSlide(index) {
                const slide = slides[index];
                heroTitle.style.opacity = '0';
                heroQuote.style.opacity = '0';

                setTimeout(() => {
                    heroTitle.textContent = slide.title;
                    heroQuote.textContent = slide.quote;
                    heroBackground.style.backgroundImage = `url('${slide.image}')`;
                    heroTitle.style.opacity = '1';
                    heroQuote.style.opacity = '1';
                }, 300);

                dots.forEach((dot, i) => {
                    dot.classList.toggle('bg-white', i === index);
                    dot.classList.toggle('bg-white/40', i !== index);
                });
            }

            document.getElementById('auth-hero-prev').addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateSlide(currentIndex);
            });

            document.getElementById('auth-hero-next').addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlide(currentIndex);
            });

            setInterval(() => {
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlide(currentIndex);
            }, 5000);
        });
    </script>
</body>

</html>