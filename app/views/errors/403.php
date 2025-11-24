<?php
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden | Trevio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="flex min-h-[70vh] flex-col items-center justify-center px-6 py-12 text-center">
    <div class="mb-8 rounded-full bg-red-50 p-6">
        <svg class="h-20 w-20 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
        </svg>
    </div>
    
    <h1 class="mb-4 text-4xl font-extrabold text-slate-900 md:text-6xl" style="user-select: none;">403</h1>
    <h2 class="mb-6 text-xl font-bold text-slate-800 md:text-2xl" style="user-select: none;">Akses Ditolak</h2>
    
    <p class="mx-auto mb-8 max-w-lg text-slate-600 leading-relaxed" style="user-select: none;">
        Anda tidak memiliki izin untuk mengakses halaman atau sumber daya tersebut. Server memahami permintaan Anda, tetapi menolaknya karena masalah izin.
    </p>

    <div class="flex flex-col gap-4 sm:flex-row">
        <a href="<?= BASE_URL ?>" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Kembali ke Beranda
        </a>
        <a href="<?= BASE_URL ?>/auth/login" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:ring-offset-2">
            Login dengan Akun Lain
        </a>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>