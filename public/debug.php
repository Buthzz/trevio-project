<?php
// File: public/debug_auth_full.php
// Tujuan: Debugging Koneksi Database, Isi Tabel Users, dan Verifikasi Password V4   v44

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<style>body{font-family:sans-serif; padding:20px;} h3{border-bottom:2px solid #ccc; padding-bottom:5px; margin-top:30px;} .box{background:#f4f4f4; padding:15px; border-radius:5px; border:1px solid #ddd; overflow-x:auto;}</style>";
echo "<h1>🔍 TREVIO AUTH DEBUGGER</h1>";
echo "<p>Script ini akan mengecek file .env, koneksi database, isi tabel users, dan tes login.</p>";

// ---------------------------------------------------------
// 1. CEK FILE .ENV
// ---------------------------------------------------------
echo "<h3>1. Checking Configuration (.env)</h3>";
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    echo "✅ <strong>File .env ditemukan!</strong> Lokasi: " . realpath($envPath) . "<br>";
    
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    echo "<div class='box'><pre>";
    foreach ($lines as $line) {
        // Load variable ke environment sementara untuk script ini
        if (strpos(trim($line), '#') !== 0 && strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            putenv("$name=$value");
            
            // Tampilkan config DB (sensor password)
            if (strpos($name, 'DB_') === 0) {
                if (strpos($name, 'PASSWORD') !== false) {
                    echo "$name = ******** (HIDDEN)\n";
                } else {
                    echo "$name = $value\n";
                }
            }
        }
    }
    echo "</pre></div>";
} else {
    die("<div class='box' style='background:#ffebee; color:red;'>❌ <strong>CRITICAL: File .env TIDAK DITEMUKAN!</strong><br>Pastikan Anda menyalin file `.env.example` menjadi `.env` di folder root project.</div>");
}

// ---------------------------------------------------------
// 2. TES KONEKSI DATABASE
// ---------------------------------------------------------
echo "<h3>2. Testing Database Connection</h3>";

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_DATABASE') ?: 'trevio';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

echo "Mencoba koneksi ke Host: <strong>$host</strong> | DB: <strong>$db</strong> | User: <strong>$user</strong><br>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong>Koneksi Database BERHASIL!</strong><br>";
} catch (PDOException $e) {
    die("<div class='box' style='background:#ffebee; color:red;'>❌ <strong>Koneksi GAGAL:</strong> " . $e->getMessage() . "<br><br>Solusi: Cek nama database di .env apakah sudah sesuai dengan di phpMyAdmin.</div>");
}

// ---------------------------------------------------------
// 3. DUMP DATA TABEL USERS
// ---------------------------------------------------------
echo "<h3>3. Dumping Table 'users' (Var Dump)</h3>";

try {
    // Cek apakah tabel users ada
    $checkTable = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($checkTable->rowCount() == 0) {
        die("<div class='box' style='background:#ffebee; color:red;'>❌ Tabel 'users' TIDAK DITEMUKAN di database '$db'.<br>Silakan import file SQL terlebih dahulu.</div>");
    }

    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Ditemukan <strong>" . count($users) . "</strong> user di database.<br><br>";
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%; border-color:#ddd;'>";
        echo "<tr style='background:#eee; text-align:left;'><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Is Active</th><th>Password Hash (Depan)</th></tr>";
        foreach ($users as $u) {
            echo "<tr>";
            echo "<td>" . $u['id'] . "</td>";
            echo "<td>" . htmlspecialchars($u['name']) . "</td>";
            echo "<td>" . htmlspecialchars($u['email']) . "</td>";
            echo "<td>" . htmlspecialchars($u['role']) . "</td>";
            echo "<td>" . ($u['is_active'] ? '✅ Yes' : '❌ No') . "</td>";
            // Tampilkan potongan hash untuk verifikasi visual
            echo "<td style='font-family:monospace; color:#666;'>" . substr($u['password'], 0, 20) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h4>Full Var Dump Data:</h4>";
        echo "<div class='box'><pre>";
        var_dump($users); // INI YANG ANDA MINTA
        echo "</pre></div>";
    } else {
        echo "<div class='box' style='background:#fff3e0; color:orange;'>⚠️ Tabel 'users' ada tapi KOSONG. Silakan jalankan seeder atau register user baru.</div>";
    }

} catch (PDOException $e) {
    die("❌ Query Error: " . $e->getMessage());
}

// ---------------------------------------------------------
// 4. SIMULASI LOGIN & CEK PASSWORD
// ---------------------------------------------------------
echo "<h3>4. Simulation: Login Check</h3>";

// Ambil user pertama untuk testing (biasanya admin)
if (count($users) > 0) {
    $targetUser = $users[16]; // Ambil user paling atas
    $testEmail = $targetUser['email'];
    $testPass = 'admin123'; // Password default dari seeder biasanya ini
    
    echo "Mencoba login simulasi dengan:<br>";
    echo "Email: <strong>$testEmail</strong><br>";
    echo "Password (Asumsi): <strong>$testPass</strong><br><br>";
    
    // Cek manual
    if (password_verify($testPass, $targetUser['password'])) {
        echo "<div class='box' style='background:#e8f5e9; border-color:#a5d6a7; color:#2e7d32;'>";
        echo "✅ <strong>PASSWORD COCOK!</strong><br>";
        echo "Hash di database valid untuk password '$testPass'.<br>";
        echo "Jika di aplikasi masih gagal, masalahnya ada di <strong>AuthController.php</strong> (filter input) atau session.";
        echo "</div>";
    } else {
        echo "<div class='box' style='background:#ffebee; border-color:#ef9a9a; color:#c62828;'>";
        echo "❌ <strong>PASSWORD TIDAK COCOK!</strong><br>";
        echo "Hash di database: " . $targetUser['password'] . "<br>";
        echo "Hash baru dari '$testPass': " . password_hash($testPass, PASSWORD_BCRYPT) . "<br><br>";
        echo "<strong>Solusi:</strong> Password di database mungkin bukan 'password'. Coba reset password lewat database atau jalankan ulang seeder.";
        echo "</div>";
    }
} else {
    echo "Tidak ada user untuk dites.";
}
?>