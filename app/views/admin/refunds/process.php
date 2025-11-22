<?php
// Judul halaman supaya tab browser menampilkan konteks proses refund.
$pageTitle = 'Process Refund - Trevio';
// Link beranda admin untuk elemen navigasi global.
$homeLink  = '../dashboard.php';
// Sertakan header umum agar tampilan konsisten.
include __DIR__ . '/../../layouts/header.php';

// Ambil ID refund dari query atau gunakan default untuk demonstrasi.
$refundId = $_GET['id'] ?? 'REF-001';
// Data refund contoh; nantinya diganti data aktual dari database.
$refund = [
    'id' => $refundId,
    'customer' => 'John Doe',
    'email' => 'john@example.com',
    'booking' => 'BK-001',
    'hotel' => 'Grand Hotel Jakarta',
    'amount' => 'Rp 2.500.000',
    'amountNumeric' => 2500000,
    'reason' => 'Cancelled due to personal reasons',
    'requestDate' => '2024-01-15',
    'requestTime' => '14:30:00',
    'status' => 'pending',
    'bookingDates' => '15 Jan 2024 - 17 Jan 2024',
    'paymentMethod' => 'Bank Transfer',
    'bankAccount' => 'BCA - 1234567890',
    'originalTransaction' => 'TXN-001',
    'notes' => 'Customer requested cancellation',
];
?>

<!-- Layout proses refund admin -->
<div class="flex min-h-[calc(100vh-var(--header-height,4rem))] bg-slate-50">
    <!-- Sidebar Toggle Button (Mobile) -->
    <button id="sidebarToggle" class="fixed top-4 left-4 z-50 lg:hidden rounded-lg bg-accent p-2 text-white shadow-lg hover:bg-accentLight transition" onclick="toggleSidebar()">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 z-20 hidden bg-black/50 lg:hidden" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <!-- Sidebar modul admin -->
    <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-30 w-64 border-r border-slate-200 bg-white overflow-y-auto transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:pt-0" style="top: var(--header-height, 4rem);">
        <!-- Sidebar Header with Close Button (Mobile) -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 lg:hidden">
            <h3 class="font-bold text-slate-900">Menu</h3>
            <button class="rounded-lg p-1 hover:bg-slate-100 transition" onclick="closeSidebar()">
                <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <nav class="space-y-2">
                <a href="../dashboard.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="../hotels/index.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                    </svg>
                    Hotels
                </a>
                <a href="../payments/index.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Payments
                </a>
                <a href="index.php" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                    </svg>
                    Refunds
                </a>
                <a href="../users/index.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path>
                    </svg>
                    Users
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <!-- Konten form konfirmasi refund -->
    <main class="flex-1 overflow-auto">
        <div class="p-6 md:p-8">
            <!-- Page Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <a href="index.php" class="text-accent hover:underline text-sm font-medium">← Back to Refunds</a>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">Process Refund</h1>
                    <p class="mt-1 text-slate-600">Refund ID: <?= htmlspecialchars($refund['id']) ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column - Refund Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Refund Request Details -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Refund Request Details</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Refund ID</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['id']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Status</p>
                                    <p class="mt-1">
                                        <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-700">
                                            <?= ucfirst($refund['status']) ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Refund Amount</p>
                                    <p class="mt-1 text-xl font-bold text-slate-900"><?= htmlspecialchars($refund['amount']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Original Transaction</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['originalTransaction']) ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Request Date</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['requestDate']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Request Time</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['requestTime']) ?></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-600">Reason for Refund</p>
                                <p class="mt-1 text-slate-700"><?= htmlspecialchars($refund['reason']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Booking Information -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Customer & Booking Information</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Customer Name</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['customer']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Email</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['email']) ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Booking ID</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['booking']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Hotel</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['hotel']) ?></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-600">Check-in & Check-out</p>
                                <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['bookingDates']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Information for Refund -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Refund Bank Account</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Payment Method</p>
                                <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['paymentMethod']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-600">Bank Account</p>
                                <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($refund['bankAccount']) ?></p>
                            </div>
                            <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                                <p class="text-sm text-blue-900">
                                    <span class="font-semibold">Note:</span> Refund akan ditransfer ke rekening bank pelanggan sesuai metode pembayaran original.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Notes</h2>
                        <textarea placeholder="Tambahkan catatan atau komentar..." rows="4" class="w-full rounded-lg border border-slate-300 p-3 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"><?= htmlspecialchars($refund['notes']) ?></textarea>
                    </div>
                </div>

                <!-- Right Column - Action Panel -->
                <div class="space-y-6">
                    <!-- Processing Status -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Processing Status</h2>
                        <div class="space-y-4">
                            <!-- Timeline -->
                            <div class="space-y-4">
                                <!-- Step 1: Review -->
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">1</div>
                                        <div class="w-0.5 h-12 bg-blue-200 mt-2"></div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Review Request</p>
                                        <p class="text-xs text-slate-600">Completed</p>
                                    </div>
                                </div>

                                <!-- Step 2: Process -->
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold">2</div>
                                        <div class="w-0.5 h-12 bg-slate-300 mt-2"></div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Process Refund</p>
                                        <p class="text-xs text-slate-600">Pending</p>
                                    </div>
                                </div>

                                <!-- Step 3: Complete -->
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="h-8 w-8 rounded-full bg-slate-300 flex items-center justify-center text-slate-600 text-xs font-bold">3</div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Confirm Completion</p>
                                        <p class="text-xs text-slate-600">Waiting</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Actions</h2>
                        <div class="space-y-3">
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-3 text-white font-semibold hover:bg-green-700 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve Refund
                            </button>
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-white font-semibold hover:bg-blue-700 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Process Payment
                            </button>
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-3 text-white font-semibold hover:bg-red-700 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject Refund
                            </button>
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-slate-200 px-4 py-3 text-slate-700 font-semibold hover:bg-slate-300 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Request More Info
                            </button>
                        </div>
                    </div>

                    <!-- Important Info -->
                    <div class="rounded-xl bg-red-50 border border-red-200 p-6">
                        <h3 class="text-sm font-bold text-red-900 mb-3">Important Notes</h3>
                        <ul class="text-xs text-red-800 space-y-2">
                            <li class="flex gap-2">
                                <span class="text-red-600 font-bold">•</span>
                                <span>Pastikan data bank pelanggan sudah terverifikasi</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-red-600 font-bold">•</span>
                                <span>Refund biasanya memerlukan waktu 3-5 hari kerja</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-red-600 font-bold">•</span>
                                <span>Simpan bukti refund untuk laporan audit</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        // Mengatur buka/tutup sidebar saat tombol mobile dipakai.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function closeSidebar() {
        // Menutup sidebar agar area kerja kembali luas.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Saat link navigasi dipilih, tutup sidebar secara otomatis.
    document.querySelectorAll('#adminSidebar a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Untuk layar besar, pastikan sidebar tetap terlihat dan overlay hilang.
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('adminSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
