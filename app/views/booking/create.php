<?php require_once '../app/views/layouts/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?= BASE_URL ?>" class="text-gray-700 hover:text-blue-600">Home</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="<?= BASE_URL ?>/hotel/detail/<?= $hotel['id'] ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2"><?= htmlspecialchars($hotel['name']) ?></a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Booking</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Flash Message for Errors -->
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?= $_SESSION['flash_error']; ?></span>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Form Booking -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Isi Data Pemesanan</h2>
                </div>
                
                <div class="p-6">
                    <form action="<?= BASE_URL ?>/booking/store" method="POST" id="bookingForm">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                        <input type="hidden" id="price_per_night" value="<?= $room['price_per_night'] ?>">

                        <!-- Section: Detail Menginap -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full mr-3 text-sm font-bold">1</span>
                            Detail Menginap
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 ml-11">
                            <div>
                                <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                                <input type="date" name="check_in" id="check_in" required
                                    min="<?= date('Y-m-d') ?>"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            </div>
                            <div>
                                <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                                <input type="date" name="check_out" id="check_out" required
                                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            </div>
                            <div>
                                <label for="num_rooms" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kamar</label>
                                <select name="num_rooms" id="num_rooms" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border bg-white">
                                    <?php 
                                    $maxSlots = min($room['available_slots'], 5); // Batasi max 5 per booking atau sisa slot
                                    for($i = 1; $i <= $maxSlots; $i++): 
                                    ?>
                                        <option value="<?= $i ?>"><?= $i ?> Kamar</option>
                                    <?php endfor; ?>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Tersedia: <?= $room['available_slots'] ?> kamar</p>
                            </div>
                        </div>

                        <hr class="border-gray-200 my-6">

                        <!-- Section: Data Tamu -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full mr-3 text-sm font-bold">2</span>
                            Data Tamu
                        </h3>

                        <div class="space-y-4 ml-11 mb-6">
                            <div>
                                <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="guest_name" id="guest_name" required
                                    value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                    placeholder="Nama sesuai KTP/Paspor"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="guest_email" id="guest_email" required
                                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                        placeholder="contoh@email.com"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                                </div>
                                <div>
                                    <label for="guest_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                    <input type="tel" name="guest_phone" id="guest_phone" required
                                        placeholder="08123456789"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                                </div>
                            </div>
                        </div>

                        <div class="ml-11">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md">
                                Lanjutkan Pembayaran
                            </button>
                            <p class="text-xs text-gray-500 mt-2 text-center">
                                Dengan menekan tombol di atas, Anda menyetujui Syarat & Ketentuan Trevio.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Pesanan -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg border border-gray-200 sticky top-4">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Ringkasan Pesanan</h3>
                </div>
                
                <div class="p-4">
                    <!-- Info Hotel -->
                    <div class="flex gap-3 mb-4">
                        <div class="w-20 h-20 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                            <!-- Placeholder Image if no main photo -->
                            <img src="<?= BASE_URL ?>/public/images/placeholder.jpg" alt="Hotel" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 line-clamp-2"><?= htmlspecialchars($hotel['name']) ?></h4>
                            <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($room['room_type']) ?></p>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-gray-300 my-4"></div>

                    <!-- Kalkulasi Harga Dinamis -->
                    <div id="price_summary" class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Harga / malam</span>
                            <span class="font-medium">Rp <?= number_format($room['price_per_night'], 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Durasi</span>
                            <span class="font-medium"><span id="summary_nights">0</span> Malam</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Kamar</span>
                            <span class="font-medium">x <span id="summary_rooms">1</span></span>
                        </div>
                        
                        <div class="border-t border-gray-200 my-2 pt-2"></div>
                        
                        <div class="flex justify-between font-bold text-lg text-blue-600">
                            <span>Total</span>
                            <span id="summary_total">Rp -</span>
                        </div>
                        <p class="text-xs text-right text-gray-400">*Termasuk pajak & biaya layanan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple Vanilla JS for Price Calculation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const numRoomsInput = document.getElementById('num_rooms');
    const pricePerNight = parseFloat(document.getElementById('price_per_night').value);
    
    // Elements to update
    const summaryNights = document.getElementById('summary_nights');
    const summaryRooms = document.getElementById('summary_rooms');
    const summaryTotal = document.getElementById('summary_total');

    function calculateTotal() {
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);
        const rooms = parseInt(numRoomsInput.value) || 1;

        summaryRooms.textContent = rooms;

        if (checkInDate && checkOutDate && checkOutDate > checkInDate) {
            const timeDiff = checkOutDate - checkInDate;
            const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            summaryNights.textContent = nights;

            // Base price
            const subtotal = pricePerNight * nights * rooms;
            
            // Tax 10% + Service 5% = 15% (Sesuai Controller)
            const tax = subtotal * 0.10;
            const service = subtotal * 0.05;
            const total = subtotal + tax + service;

            summaryTotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        } else {
            summaryNights.textContent = '0';
            summaryTotal.textContent = 'Rp -';
        }
    }

    // Auto-set check-out date to tomorrow when check-in is selected
    checkInInput.addEventListener('change', function() {
        if(this.value) {
            const date = new Date(this.value);
            date.setDate(date.getDate() + 1);
            const nextDay = date.toISOString().split('T')[0];
            checkOutInput.min = nextDay;
            if(checkOutInput.value && checkOutInput.value <= this.value) {
                checkOutInput.value = nextDay;
            }
        }
        calculateTotal();
    });

    checkOutInput.addEventListener('change', calculateTotal);
    numRoomsInput.addEventListener('change', calculateTotal);
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>