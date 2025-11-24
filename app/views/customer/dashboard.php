<?php
require_once __DIR__ . '/../../../helpers/functions.php';
require_once __DIR__ . '/../../../helpers/format.php';

// Include header
require __DIR__ . '/../layouts/header.php';
?>

<!-- Customer Dashboard -->
<section class="bg-slate-100/70 py-16">
    <div class="mx-auto max-w-6xl space-y-8 px-6">
        <!-- Header Section -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Dashboard</p>
                <h1 class="text-3xl font-semibold text-primary">Selamat datang, <?= htmlspecialchars($data['user']['user_name'] ?? 'Customer') ?>!</h1>
                <p class="text-sm text-slate-500">Kelola semua pemesanan Anda di satu tempat</p>
            </div>
            <div class="flex gap-3 text-sm font-semibold">
                <a class="inline-flex items-center justify-center rounded-full bg-accent px-6 py-2 text-white transition hover:bg-accentLight" href="<?= BASE_URL ?>/hotel/search">
                    Cari Hotel
                </a>
            </div>
        </div>

        <!-- Active Bookings Section -->
        <?php if (!empty($data['active_bookings'])): ?>
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-primary">Booking Aktif</h2>
            <?php foreach ($data['active_bookings'] as $booking): ?>
                <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Kode Booking</p>
                            <p class="text-lg font-semibold text-primary">#<?= htmlspecialchars($booking['booking_code'] ?? 'N/A') ?></p>
                        </div>
                        <div class="text-sm text-slate-500">
                            <p class="text-base font-semibold text-primary"><?= htmlspecialchars($booking['hotel_name'] ?? 'Hotel') ?></p>
                            <p><?= htmlspecialchars($booking['room_name'] ?? 'Room') ?></p>
                        </div>
                        <div class="text-sm text-slate-500">
                            <p class="text-xs font-semibold text-slate-500">Check-in / Check-out</p>
                            <p><?= isset($booking['check_in_date']) ? date('d M Y', strtotime($booking['check_in_date'])) : '-' ?></p>
                            <p><?= isset($booking['check_out_date']) ? date('d M Y', strtotime($booking['check_out_date'])) : '-' ?></p>
                        </div>
                        <div class="text-right text-sm text-slate-500">
                            <p class="text-xs font-semibold text-slate-500">Total</p>
                            <p class="text-base font-semibold text-primary">Rp <?= isset($booking['total_price']) ? number_format($booking['total_price'], 0, ',', '.') : '0' ?></p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <?php
                        $statusClass = 'bg-slate-100 text-slate-600';
                        $statusText = ucfirst($booking['status'] ?? 'unknown');
                        
                        switch($booking['status'] ?? '') {
                            case 'confirmed':
                                $statusClass = 'bg-emerald-50 text-emerald-700';
                                $statusText = 'Terkonfirmasi';
                                break;
                            case 'pending_payment':
                                $statusClass = 'bg-amber-50 text-amber-700';
                                $statusText = 'Menunggu Pembayaran';
                                break;
                            case 'pending_verification':
                                $statusClass = 'bg-blue-50 text-blue-700';
                                $statusText = 'Menunggu Verifikasi';
                                break;
                        }
                        ?>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold <?= $statusClass ?>">
                            <?= htmlspecialchars($statusText) ?>
                        </span>
                        <div class="flex flex-wrap gap-3 text-sm font-semibold">
                            <?php $bookingId = htmlspecialchars($booking['id'] ?? ''); ?>
                            <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-slate-600 transition hover:border-accent hover:text-accent" 
                               href="<?= BASE_URL ?>/booking/detail/<?= $bookingId ?>">
                                Lihat Detail
                            </a>
                            <?php if (($booking['status'] ?? '') === 'pending_payment'): ?>
                            <a class="inline-flex items-center justify-center rounded-full bg-accent px-4 py-2 text-white transition hover:bg-accentLight" 
                               href="<?= BASE_URL ?>/payment/form/<?= $bookingId ?>">
                                Bayar Sekarang
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center">
            <div class="mx-auto max-w-md space-y-4">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                    <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-primary">Belum Ada Booking Aktif</h3>
                <p class="text-sm text-slate-500">Mulai petualangan Anda dengan mencari dan memesan hotel favorit</p>
                <a class="inline-flex items-center justify-center rounded-full bg-accent px-6 py-3 text-sm font-semibold text-white transition hover:bg-accentLight" 
                   href="<?= BASE_URL ?>/hotel/search">
                    Cari Hotel Sekarang
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Past Bookings Section -->
        <?php if (!empty($data['past_bookings'])): ?>
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-primary">Riwayat Booking</h2>
            <?php foreach ($data['past_bookings'] as $booking): ?>
                <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Kode Booking</p>
                            <p class="text-lg font-semibold text-primary">#<?= htmlspecialchars($booking['booking_code'] ?? 'N/A') ?></p>
                        </div>
                        <div class="text-sm text-slate-500">
                            <p class="text-base font-semibold text-primary"><?= htmlspecialchars($booking['hotel_name'] ?? 'Hotel') ?></p>
                            <p><?= htmlspecialchars($booking['room_name'] ?? 'Room') ?></p>
                        </div>
                        <div class="text-sm text-slate-500">
                            <p class="text-xs font-semibold text-slate-500">Check-in / Check-out</p>
                            <p><?= isset($booking['check_in_date']) ? date('d M Y', strtotime($booking['check_in_date'])) : '-' ?></p>
                            <p><?= isset($booking['check_out_date']) ? date('d M Y', strtotime($booking['check_out_date'])) : '-' ?></p>
                        </div>
                        <div class="text-right text-sm text-slate-500">
                            <p class="text-xs font-semibold text-slate-500">Total</p>
                            <p class="text-base font-semibold text-primary">Rp <?= isset($booking['total_price']) ? number_format($booking['total_price'], 0, ',', '.') : '0' ?></p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <?php
                        $statusClass = 'bg-slate-100 text-slate-600';
                        $statusText = ucfirst($booking['status'] ?? 'unknown');
                        
                        switch($booking['status'] ?? '') {
                            case 'completed':
                                $statusClass = 'bg-emerald-50 text-emerald-700';
                                $statusText = 'Selesai';
                                break;
                            case 'cancelled':
                                $statusClass = 'bg-red-50 text-red-700';
                                $statusText = 'Dibatalkan';
                                break;
                            case 'refunded':
                                $statusClass = 'bg-purple-50 text-purple-700';
                                $statusText = 'Dikembalikan';
                                break;
                        }
                        ?>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold <?= $statusClass ?>">
                            <?= htmlspecialchars($statusText) ?>
                        </span>
                        <div class="flex flex-wrap gap-3 text-sm font-semibold">
                            <?php 
                            $bookingId = htmlspecialchars($booking['id'] ?? '');
                            $hotelId = htmlspecialchars($booking['hotel_id'] ?? '');
                            ?>
                            <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-slate-600 transition hover:border-accent hover:text-accent" 
                               href="<?= BASE_URL ?>/booking/detail/<?= $bookingId ?>">
                                Lihat Detail
                            </a>
                            <?php if (($booking['status'] ?? '') === 'completed'): ?>
                            <a class="inline-flex items-center justify-center rounded-full border border-accent px-4 py-2 text-accent transition hover:bg-accent hover:text-white" 
                               href="<?= BASE_URL ?>/hotel/detail/<?= $hotelId ?>">
                                Pesan Lagi
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Include footer
require __DIR__ . '/../layouts/footer.php';
?>
