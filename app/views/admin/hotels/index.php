<?php
// Tentukan judul dan layout
$pageTitle = 'Manajemen Hotel - Admin Trevio';
require_once __DIR__ . '/../../layouts/header.php';

// Helper status badge
function getStatusBadge($isActive, $isVerified) {
    if (!$isVerified) {
        return '<span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Menunggu Verifikasi</span>';
    }
    if ($isActive) {
        return '<span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Aktif</span>';
    }
    return '<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">Non-Aktif</span>';
}
?>

<div class="flex min-h-[calc(100vh-4rem)] bg-slate-50">
    <aside class="hidden w-64 border-r border-slate-200 bg-white lg:block">
        <div class="p-6">
            <nav class="space-y-1">
                <a href="<?= BASE_URL ?>/admin/dashboard" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path></svg>
                    Dashboard
                </a>
                <a href="<?= BASE_URL ?>/admin/hotels" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path></svg>
                    Hotels
                </a>
                <a href="<?= BASE_URL ?>/admin/payments" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Payments
                </a>
                </nav>
        </div>
    </aside>

    <main class="flex-1 overflow-auto p-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Daftar Hotel</h1>
                <p class="text-slate-500">Kelola hotel, verifikasi, dan pantau status properti.</p>
            </div>
            
            <form method="GET" class="flex gap-2">
                <select name="status" class="rounded-lg border-slate-200 text-sm focus:border-accent focus:ring-accent" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($data['filters']['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Perlu Verifikasi</option>
                    <option value="verified" <?= ($data['filters']['status'] ?? '') === 'verified' ? 'selected' : '' ?>>Terverifikasi</option>
                </select>

                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder="Cari nama hotel..." value="<?= htmlspecialchars($data['filters']['search'] ?? '') ?>" 
                           class="w-64 rounded-lg border-slate-200 pl-10 text-sm focus:border-accent focus:ring-accent">
                </div>

                <button type="submit" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white hover:bg-accentLight flex items-center justify-center">
                    Filter
                </button>
            </form>
        </div>

        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="mb-6 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700 border border-emerald-200">
                <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 border border-red-200">
                <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Hotel / Properti</th>
                            <th class="px-6 py-4 font-semibold">Pemilik</th>
                            <th class="px-6 py-4 font-semibold">Lokasi</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (!empty($data['hotels'])): ?>
                            <?php foreach ($data['hotels'] as $hotel): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <?php if (!empty($hotel['main_image'])): ?>
                                            <img src="<?= BASE_URL ?>/uploads/hotels/<?= htmlspecialchars($hotel['main_image']) ?>" 
                                                 class="h-12 w-12 rounded-lg object-cover bg-slate-200" alt="Hotel">
                                        <?php else: ?>
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-semibold text-slate-900"><?= htmlspecialchars($hotel['name']) ?></p>
                                            <div class="flex items-center gap-1 text-xs text-amber-500">
                                                <span>★</span>
                                                <span><?= $hotel['star_rating'] ?></span>
                                                <span class="text-slate-400">• <?= $hotel['total_rooms'] ?> Kamar</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-900"><?= htmlspecialchars($hotel['owner_name']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($hotel['owner_email']) ?></p>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <?= htmlspecialchars($hotel['city']) ?>, <?= htmlspecialchars($hotel['province']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= getStatusBadge($hotel['is_active'], $hotel['is_verified']) ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= BASE_URL ?>/hotel/detail/<?= $hotel['id'] ?>" target="_blank" 
                                           class="flex items-center justify-center rounded-lg border border-slate-200 h-9 w-9 text-slate-600 hover:bg-slate-50 hover:text-accent transition" title="Lihat Detail">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <?php if (!$hotel['is_verified']): ?>
                                            <form action="<?= BASE_URL ?>/admin/hotels/verify/<?= $hotel['id'] ?>" method="POST" class="inline" onsubmit="return confirm('Verifikasi hotel ini agar bisa mulai menerima pesanan?');">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                <button type="submit" class="flex items-center justify-center rounded-lg bg-emerald-50 border border-emerald-200 h-9 w-9 text-emerald-600 hover:bg-emerald-100 transition" title="Verifikasi Hotel">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form action="<?= BASE_URL ?>/admin/hotels/delete/<?= $hotel['id'] ?>" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus hotel ini secara permanen? Data tidak bisa dikembalikan.');">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                            <button type="submit" class="flex items-center justify-center rounded-lg bg-red-50 border border-red-200 h-9 w-9 text-red-600 hover:bg-red-100 transition" title="Hapus Hotel">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                        <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p>Tidak ada data hotel yang ditemukan.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="border-t border-slate-200 px-6 py-4">
                <p class="text-xs text-slate-500">Menampilkan <?= count($data['hotels'] ?? []) ?> hotel</p>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>