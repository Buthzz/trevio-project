<?php
// Footer tidak butuh inisialisasi, blok ini disiapkan untuk penyesuaian variabel jika diperlukan.
?>
</main>
<!-- Footer global: update link/contacts via variabel jika perlu -->
<footer class="bg-slate-800 text-slate-200">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <div class="grid gap-8 md:grid-cols-5">
            <div class="space-y-4">
                <a class="inline-flex items-center gap-1" href="<?= htmlspecialchars($homeLink ?? '/') ?>">
                    <img class="h-20 w-auto" src="<?= htmlspecialchars($logoUrl ?? '/../../../public/images/trevio.svg') ?>" alt="Trevio logo">
                </a>
                <p class="max-w-xs text-sm text-slate-200">Kami membantu kamu menemukan dan memesan penginapan terbaik: dari guesthouse yang cozy sampai hotel bintang lima dengan proses yang simpel dan aman.</p>
            </div>

            <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-white">Jelajahi</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                        <li><a class="hover:text-white" href="#">Hotel</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-white">Jenis Properti</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                        <li><a class="hover:text-white" href="#">Hotel</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-white">Bantuan</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                        <li><a class="hover:text-white" href="#">Hubungi Kami</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-white">Hubungi Kami</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                        <li>+62 8810 8177 2005</li>
                        <li>trevio@gmail.com</li>
                </ul>
                <div class="mt-3 flex gap-3 text-slate-300">
                    <a href="https://facebook.com" target="_blank" rel="noreferrer" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 hover:text-white"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                    <a href="https://instagram.com" target="_blank" rel="noreferrer" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 hover:text-white"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.5" y2="6.5"></line></svg></a>
                    <a href="https://youtube.com" target="_blank" rel="noreferrer" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 hover:text-white"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.94 2C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"></path><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon></svg></a>
                    <a href="https://twitter.com" target="_blank" rel="noreferrer" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 hover:text-white"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg></a>
                </div>
            </div>
        </div>
        </div>
    </div>
</footer>
</body>
</html>
