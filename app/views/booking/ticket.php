<?php
// [BACKEND NOTE]: File ini seharusnya di-load melalui controller
// yang sudah menyediakan variabel $data dengan struktur lengkap
// Jika diakses langsung, akan menggunakan data dummy/fallback

if (!isset($data)) {
    $data = [
        'title' => 'E-Ticket Booking',
        'booking' => [
            'booking_code' => 'N/A',
            'booking_status' => 'pending',
            'guest_name' => 'Guest',
            'guest_phone' => 'N/A',
            'check_in_date' => date('Y-m-d'),
            'check_out_date' => date('Y-m-d', strtotime('+1 day')),
            'num_nights' => 1,
            'num_rooms' => 1
        ],
        'hotel' => [
            'name' => 'Hotel',
            'address' => 'N/A'
        ],
        'room' => [
            'name' => 'Standard Room'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title']) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #333;
            margin: 0;
            padding: 40px 0;
        }
        .ticket-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px; }
        .header .status {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .content { padding: 30px; }
        .row { display: flex; margin-bottom: 20px; border-bottom: 1px dashed #eee; padding-bottom: 20px; }
        .row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .col { flex: 1; padding-right: 20px; }
        .col:last-child { padding-right: 0; }
        .label { font-size: 12px; color: #777; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px; }
        .value { font-size: 16px; font-weight: 600; color: #111; }
        .value.highlight { color: #2563eb; font-size: 18px; }
        .footer {
            background-color: #f9fafb;
            padding: 15px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .print-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 12px;
            background-color: #2563eb;
            color: white;
            text-align: center;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .print-btn:hover { background-color: #1d4ed8; }

        @media print {
            body { 
                background-color: white; 
                padding: 0; 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ticket-container { box-shadow: none; border: 2px solid #000; width: 100%; max-width: 100%; border-radius: 0; }
            .print-btn { display: none; }
            .header { background-color: #2563eb !important; color: white !important; }
        }
    </style>
</head>
<body>

    <div class="ticket-container">
        <div class="header">
            <h1>TREVIO E-TICKET</h1>
            <div class="status">
                <?php 
                    $status = $data['booking']['booking_status'];
                    if($status == 'confirmed') echo 'CONFIRMED';
                    elseif($status == 'pending_payment') echo 'UNPAID';
                    elseif($status == 'cancelled') echo 'CANCELLED';
                    else echo strtoupper($status);
                ?>
            </div>
        </div>

        <div class="content">
            <div class="row">
                <div class="col">
                    <div class="label">Kode Booking</div>
                    <div class="value highlight"><?= htmlspecialchars($data['booking']['booking_code']) ?></div>
                </div>
                <div class="col">
                    <div class="label">Hotel</div>
                    <div class="value"><?= htmlspecialchars($data['hotel']['name'] ?? 'Unknown Hotel') ?></div>
                    <div style="font-size: 12px; color: #666; margin-top: 2px;">
                        <?= htmlspecialchars($data['hotel']['address'] ?? '-') ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Nama Tamu</div>
                    <div class="value"><?= htmlspecialchars($data['booking']['guest_name']) ?></div>
                </div>
                <div class="col">
                    <div class="label">Kontak</div>
                    <div class="value"><?= htmlspecialchars($data['booking']['guest_phone']) ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Check-In</div>
                    <div class="value"><?= date('d F Y', strtotime($data['booking']['check_in_date'])) ?></div>
                    <div style="font-size: 12px; color: #666;">Dari jam 14:00</div>
                </div>
                <div class="col">
                    <div class="label">Check-Out</div>
                    <div class="value"><?= date('d F Y', strtotime($data['booking']['check_out_date'])) ?></div>
                    <div style="font-size: 12px; color: #666;">Sebelum jam 12:00</div>
                </div>
                <div class="col">
                    <div class="label">Durasi</div>
                    <div class="value"><?= htmlspecialchars($data['booking']['num_nights']) ?> Malam</div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Tipe Kamar</div>
                    <div class="value"><?= htmlspecialchars($data['room']['name'] ?? 'Standard Room') ?></div>
                </div>
                <div class="col">
                    <div class="label">Jumlah Kamar</div>
                    <div class="value"><?= htmlspecialchars($data['booking']['num_rooms']) ?> Kamar</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Harap tunjukkan E-Ticket ini (cetak atau digital) kepada resepsionis saat Check-in.</p>
            <p>&copy; <?= date('Y') ?> Trevio Booking System. All rights reserved.</p>
        </div>
    </div>

    <a href="javascript:window.print()" class="print-btn">🖨️ Cetak Tiket</a>

</body>
</html>