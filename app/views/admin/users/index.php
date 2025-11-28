<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$users = $data['users'] ?? [];
$stats = $data['stats'] ?? [];
$filters = $data['filters'] ?? [];
$csrf_token = $data['csrf_token'] ?? '';

// Menggunakan path yang aman untuk include header
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="flex min-h-[calc(100vh-4rem)] bg-slate-50">
    <aside class="hidden w-64 border-r border-slate-200 bg-white lg:block">
        <div class="p-6">
            <nav class="space-y-1">
                <a href="<?= $baseUrl ?>/admin/dashboard" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path></svg>
                    Dashboard
                </a>
                <a href="<?= $baseUrl ?>/admin/hotels" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path></svg>
                    Hotels
                </a>
                <a href="<?= $baseUrl ?>/admin/payments" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Payments
                </a>
                <a href="<?= $baseUrl ?>/admin/users" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path></svg>
                    Users
                </a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 overflow-auto p-6 md:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Manajemen Pengguna</h1>
            <p class="mt-2 text-slate-600">Kelola data customer, owner hotel, dan administrator.</p>
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

        <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-8">
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-600">Total Users</p>
                <p class="mt-2 text-2xl font-bold text-slate-900"><?= number_format($stats['total'] ?? 0) ?></p>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-600">Owners</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 text-blue-600"><?= number_format($stats['owners'] ?? 0) ?></p>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-600">Customers</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 text-purple-600"><?= number_format($stats['customers'] ?? 0) ?></p>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-600">Active</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 text-emerald-600"><?= number_format($stats['active'] ?? 0) ?></p>
            </div>
        </div>

        <div class="mb-6">
            <form method="GET" class="flex flex-col gap-4 lg:flex-row">
                <div class="flex-1 relative">
                    <input type="text" name="search" placeholder="Cari nama atau email..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                           class="w-full rounded-lg border border-slate-300 pl-10 pr-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <select name="role" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <option value="all">Semua Role</option>
                    <option value="customer" <?= ($filters['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Customer</option>
                    <option value="owner" <?= ($filters['role'] ?? '') === 'owner' ? 'selected' : '' ?>>Owner</option>
                    <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
                <select name="status" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <option value="">Semua Status</option>
                    <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
                    <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Non-Aktif</option>
                </select>
                <button type="submit" class="rounded-lg bg-accent px-6 py-2.5 text-sm font-semibold text-white hover:bg-accentLight transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-semibold">User</th>
                            <th class="px-6 py-4 font-semibold">Role</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Bergabung</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <p>Tidak ada user ditemukan.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 font-bold uppercase">
                                            <?= substr($u['name'] ?? '?', 0, 1) ?>
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-900"><?= htmlspecialchars($u['name'] ?? 'Unknown') ?></p>
                                            <p class="text-xs text-slate-500"><?= htmlspecialchars($u['email'] ?? '-') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                        $roleColors = [
                                            'admin' => 'bg-red-100 text-red-700',
                                            'owner' => 'bg-blue-100 text-blue-700',
                                            'customer' => 'bg-purple-100 text-purple-700'
                                        ];
                                        $userRole = $u['role'] ?? 'customer';
                                    ?>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $roleColors[$userRole] ?? 'bg-gray-100' ?>">
                                        <?= ucfirst($userRole) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($u['is_active']): ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Non-Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    <?= isset($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '-' ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <?php if ($u['is_active']): ?>
                                            <form action="<?= $baseUrl ?>/admin/users/deactivate/<?= $u['id'] ?>" method="POST" onsubmit="return confirm('Non-aktifkan user ini?')">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <button type="submit" class="rounded-lg bg-amber-100 p-2 text-amber-700 hover:bg-amber-200 transition" title="Non-aktifkan">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?= $baseUrl ?>/admin/users/activate/<?= $u['id'] ?>" method="POST" onsubmit="return confirm('Aktifkan user ini?')">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <button type="submit" class="rounded-lg bg-emerald-100 p-2 text-emerald-700 hover:bg-emerald-200 transition" title="Aktifkan">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form action="<?= $baseUrl ?>/admin/users/delete/<?= $u['id'] ?>" method="POST" onsubmit="return confirm('Hapus permanen user ini? Data tidak bisa dikembalikan.')">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <button type="submit" class="rounded-lg bg-red-100 p-2 text-red-700 hover:bg-red-200 transition" title="Hapus Permanen">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="border-t border-slate-200 px-6 py-4 bg-slate-50">
                <p class="text-xs text-slate-500">Menampilkan <?= count($users) ?> pengguna</p>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>