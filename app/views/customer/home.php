<?php
?>
<?php
$pageTitle = 'Trevio | Temukan Hotel Favoritmu';

// Hanya sisakan penginapan sesuai permintaan
$quickFilters = ['Penginapan'];

$searchFields = [
    [
        'label' => 'Location',
        'value' => 'Where are you going?'
    ],
    [
        'label' => 'Check In',
        'value' => 'Add dates'
    ],
    [
        'label' => 'Check Out',
        'value' => 'Add dates'
    ],
    [
        'label' => 'Rooms and Guests',
        'value' => '1 rooms, 1 adults, 0 children'
    ],
];

$reasons = [
    [
        'title' => 'Transparansi Harga',
        'description' => 'Harga pajak, biaya layanan, dan promo ditampilkan sejak awal tanpa biaya tersembunyi.'
    ],
    [
        'title' => 'Konfirmasi Instan',
        'description' => 'Reservasi langsung terkonfirmasi melalui email dan WhatsApp otomatis.'
    ],
    [
        'title' => 'Dukungan 24/7',
        'description' => 'Tim Trevio siap membantu setiap perubahan rencana kapan pun dibutuhkan.'
    ],
];

$featuredHotels = [
    [
        'name' => 'Oceanview Retreat',
        'location' => 'Jimbaran, Bali',
        'rating' => '4.9',
        'price' => 'IDR 2.150.000 / malam',
        'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Skyline Luxe Hotel',
        'location' => 'Singapore',
        'rating' => '4.8',
        'price' => 'SGD 275 / malam',
        'image' => 'https://images.unsplash.com/photo-1475856034135-46dc3d162c66?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Aurora Peaks Resort',
        'location' => 'Sapporo, Jepang',
        'rating' => '4.9',
        'price' => 'JPY 32.000 / malam',
        'image' => 'https://images.unsplash.com/photo-1519821983755-6f5bbfc62a86?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Lagoon Serenity Villas',
        'location' => 'Maldives',
        'rating' => '5.0',
        'price' => 'USD 520 / malam',
        'image' => 'https://images.unsplash.com/photo-1500522144261-ea64433bbe27?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Heritage Grand Palace',
        'location' => 'Lisbon, Portugal',
        'rating' => '4.7',
        'price' => 'EUR 240 / malam',
        'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Mountain Ridge Lodge',
        'location' => 'Queenstown, New Zealand',
        'rating' => '4.8',
        'price' => 'NZD 390 / malam',
        'image' => 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=1200&q=80'
    ],
];

// Data dummy untuk section testimoni
$testimonials = [
    [
        'name' => 'Dewi, Solo Traveler',
        'trip' => 'Staycation di Bandung',
        'rating' => '5.0',
        'quote' => 'Booking hotel di Trevio itu cepat dan jelas, tidak ada biaya tersembunyi sama sekali.',
        'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&q=80'
    ],
    [
        'name' => 'Rizky & Sinta',
        'trip' => 'Liburan keluarga di Bali',
        'rating' => '4.9',
        'quote' => 'Kami bisa bandingkan banyak penginapan dalam satu halaman dan proses check in berjalan mulus.',
        'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80'
    ],
    [
        'name' => 'Bima, Business Trip',
        'trip' => 'Perjalanan dinas ke Jakarta',
        'rating' => '4.8',
        'quote' => 'Notifikasi dan voucher digitalnya membantu banget untuk atur jadwal yang mepet.',
        'avatar' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=300&q=80'
    ],
];

require __DIR__ . '/../layouts/header.php';
?>

<section class="relative overflow-hidden text-white">
    <div class="absolute inset-0">
        <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1528909514045-2fa4ac7a08ba?auto=format&fit=crop&w=2000&q=80" alt="Panorama villa tepi pantai dan dome glamping">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/80 via-slate-900/45 to-slate-900/20"></div>
    </div>

    <div class="relative mx-auto flex max-w-6xl flex-col items-center gap-10 px-6 pb-24 pt-28 md:items-start md:text-left">
        <div class="flex flex-col items-center gap-5 text-center md:items-start md:text-left">
            <h1 class="text-3xl font-extrabold uppercase tracking-tight sm:text-4xl md:text-5xl">Cari hotel dan tiket pesawat di satu tempat</h1>
            <p class="max-w-2xl text-base text-white/85 md:text-lg">Cari dan berpergian aman dan nyaman bersama Trevio. Bandingkan penginapan favoritmu, dapatkan harga terbaik, dan nikmati dukungan perjalanan penuh dalam satu platform.</p>

            <!-- Quick filter hanya penginapan -->
            <div class="inline-flex items-center gap-2 rounded-full bg-white/15 p-1 shadow-lg backdrop-blur">
                <?php foreach ($quickFilters as $filter): ?>
                    <button type="button" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-primary shadow">
                        <svg class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 11l9-8 9 8"></path>
                            <path d="M5 10v10h14V10"></path>
                        </svg>
                        <?= htmlspecialchars($filter) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Card form pencarian -->
        <div class="w-full max-w-3xl rounded-[32px] bg-white/90 p-6 text-slate-900 shadow-[0_28px_60px_-35px_rgba(15,23,42,0.75)] backdrop-blur">
            <form class="flex flex-col gap-4 md:flex-row md:items-center">
                <div class="flex flex-1 flex-col gap-4 md:flex-row md:items-center">
                    <?php foreach ($searchFields as $index => $field): ?>
                        <?php $isLastField = $index === array_key_last($searchFields); ?>
                        <div class="flex flex-1 flex-col gap-1 border-slate-200 pb-3 md:border-b-0 md:pb-0 <?= $isLastField ? '' : 'md:border-r md:pr-6' ?> <?= $index > 0 ? 'md:pl-6' : '' ?>">
                            <span class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400"><?= htmlspecialchars($field['label']) ?></span>
                            <span class="text-sm font-semibold text-primary/90"><?= htmlspecialchars($field['value']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button data-search-demo class="inline-flex items-center justify-center gap-2 rounded-full bg-accent px-6 py-3 text-sm font-semibold text-white shadow transition hover:bg-accentLight" type="button">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    Search
                </button>
            </form>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-6 py-20">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Mengapa Trevio</p>
            <h2 class="mt-2 text-3xl font-semibold text-primary">Mengapa wisatawan mempercayai Trevio?</h2>
        </div>
        <a class="inline-flex items-center gap-2 text-sm font-semibold text-accent transition hover:text-accentLight" href="/customer/search">
            Lihat hotel populer
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="m13 5 7 7-7 7"></path>
            </svg>
        </a>
    </header>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        <?php foreach ($reasons as $reason): ?>
            <article class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-accent">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m9.5 12.5 2 2L15 10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-primary"><?= htmlspecialchars($reason['title']) ?></h3>
                <p class="mt-3 text-sm leading-6 text-slate-600"><?= htmlspecialchars($reason['description']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto max-w-6xl px-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Rekomendasi Trevio</p>
                <h2 class="mt-2 text-3xl font-semibold text-primary">Hotel pilihan minggu ini</h2>
                <p class="mt-3 max-w-xl text-sm text-slate-500">Kurasi hotel berdasarkan ulasan tamu, fasilitas unggulan, dan lokasi strategis di destinasi populer.</p>
            </div>
            <div class="flex gap-3">
                <button class="chip" type="button">Semua</button>
                <button class="chip chip--ghost" type="button">Keluarga</button>
                <button class="chip chip--ghost" type="button">Romantis</button>
                <button class="chip chip--ghost" type="button">Bisnis</button>
            </div>
        </header>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($featuredHotels as $hotel): ?>
                <article class="flex flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="relative h-56 w-full bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($hotel['image']) ?>');">
                        <span class="absolute left-4 top-4 inline-flex items-center gap-1 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-primary">
                            <svg class="h-4 w-4 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 .5 15.7 8l8.3 1.2-6 5.8 1.4 8.2L12 19l-7.4 3.8 1.4-8.2-6-5.8L8.3 8z"></path>
                            </svg>
                            <?= htmlspecialchars($hotel['rating']) ?>
                        </span>
                    </div>
                    <div class="flex flex-1 flex-col gap-3 p-6">
                        <div>
                            <h3 class="text-lg font-semibold text-primary"><?= htmlspecialchars($hotel['name']) ?></h3>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars($hotel['location']) ?></p>
                        </div>
                        <p class="text-sm font-semibold text-primary"><?= htmlspecialchars($hotel['price']) ?></p>
                        <button class="mt-auto inline-flex items-center justify-center rounded-full bg-accent px-4 py-2 text-sm font-semibold text-white transition hover:bg-accentLight" type="button">Lihat Detail</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section Testimoni sesuai permintaan -->
<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-6xl px-6">
        <header class="text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Testimoni Tamu</p>
            <h2 class="mt-2 text-3xl font-semibold text-primary">Apa kata mereka tentang Trevio?</h2>
            <p class="mt-3 text-sm text-slate-500">Cerita nyata dari wisatawan yang sudah memesan penginapan melalui Trevio.</p>
        </header>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <?php foreach ($testimonials as $testimonial): ?>
                <article class="flex h-full flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="<?= htmlspecialchars($testimonial['avatar']) ?>" alt="Foto tamu">
                        <div>
                            <p class="text-sm font-semibold text-primary"><?= htmlspecialchars($testimonial['name']) ?></p>
                            <p class="text-xs text-slate-500"><?= htmlspecialchars($testimonial['trip']) ?></p>
                        </div>
                        <div class="ml-auto inline-flex items-center gap-1 text-xs font-semibold text-amber-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 .5 15.7 8l8.3 1.2-6 5.8 1.4 8.2L12 19l-7.4 3.8 1.4-8.2-6-5.8L8.3 8z"></path>
                            </svg>
                            <span><?= htmlspecialchars($testimonial['rating']) ?></span>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-600">"<?= htmlspecialchars($testimonial['quote']) ?>"</p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-6 pb-20">
    <div class="grid gap-8 lg:grid-cols-[2fr,1fr]">
        <div class="rounded-3xl bg-gradient-to-r from-blue-600 to-blue-500 p-8 text-white">
            <h2 class="text-2xl font-semibold">Unduh aplikasi Trevio</h2>
            <p class="mt-2 text-sm text-white/80">Kelola reservasi, dapatkan notifikasi real-time, dan nikmati promo eksklusif hanya di aplikasi.</p>
            <div class="mt-6 flex flex-wrap gap-4">
                <button class="inline-flex items-center gap-2 rounded-full border border-white/40 px-4 py-2 text-sm font-semibold text-white transition hover:border-white" type="button">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16.365 1.43c.27-.64.403-1.325.37-2.02-.72.028-1.507.5-2 1.085-.44.526-.83 1.357-.726 2.122.785.06 1.59-.445 2.356-1.187zM19.757 6.33c-.013-2.697 1.97-3.996 2.059-4.053-1.12-1.63-2.863-1.854-3.478-1.873-1.468-.144-2.867.84-3.605.84-.737 0-1.893-.82-3.114-.797-1.6.023-3.083.932-3.907 2.369-1.676 2.902-.427 7.208 1.207 9.569.8 1.147 1.744 2.435 2.982 2.39 1.2-.046 1.648-.771 3.097-.771 1.45 0 1.842.771 3.114.747 1.294-.023 2.11-1.165 2.9-2.316.915-1.338 1.29-2.632 1.303-2.699-.028-.01-2.502-.962-2.46-3.406z"></path>
                    </svg>
                    App Store
                </button>
                <button class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-primary transition hover:bg-blue-100" type="button">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21.594 5.844c.234-.845-.48-1.844-1.336-1.844H3.059c-.852 0-1.57.992-1.336 1.836l3.68 12.766c.234.844 1.05 1.368 1.903 1.175l4.773-1.091c.855-.195 1.502.366 1.437 1.224l-.22 2.841c-.067.858.373 1.063.979.457l1.777-1.775c.606-.606 1.748-1.103 2.607-1.107h1.036c.858-.004 1.353-.707 1.1-1.552l-3.997-12.935z"></path>
                    </svg>
                    Google Play
                </button>
            </div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-primary">Newsletter Trevio</h3>
            <p class="mt-2 text-sm text-slate-500">Dapatkan inspirasi destinasi, panduan perjalanan, dan promo rahasia setiap minggu.</p>
            <form class="mt-6 space-y-3">
                <label class="block text-sm font-medium text-primary">Email</label>
                <input class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm focus:border-accent focus:outline-none" type="email" placeholder="nama@email.com" />
                <button class="inline-flex w-full items-center justify-center rounded-full bg-accent px-4 py-2 text-sm font-semibold text-white transition hover:bg-accentLight" type="button">Berlangganan</button>
            </form>
        </div>
    </div>
</section>

<script>
// Demo SweetAlert untuk tombol Search di homepage
if (typeof Swal !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.querySelector('[data-search-demo]');
        if (!btn) return;
        btn.addEventListener('click', function () {
            Swal.fire({
                title: 'Demo pencarian',
                text: 'Fitur pencarian penginapan akan dihubungkan ke backend Trevio.',
                icon: 'info',
                confirmButtonColor: '#2563eb'
            });
        });
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>