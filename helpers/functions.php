<?php

/**
 * Mengambil base URL menuju /app/views relatif terhadap script yang sedang berjalan.
 * Membantu menjaga konsistensi routing meskipun file view berada di folder bertingkat.
 */
if (!function_exists('trevio_view_base_url')) {
	function trevio_view_base_url(): string
	{
		// Simpan nama script aktif agar bisa dianalisis pola folder-nya.
		$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

		// Jika script berada di dalam /app/views maka ambil path sebelum folder tersebut.
		if ($scriptName !== '' && preg_match('#^(.*)/app/views/#', $scriptName, $matches)) {
			return rtrim($matches[1], '/') . '/app/views/';
		}

		// Jika script hanya berada di /app maka tetap arahkan ke /app/views.
		if ($scriptName !== '' && preg_match('#^(.*)/app/#', $scriptName, $matches)) {
			return rtrim($matches[1], '/') . '/app/views/';
		}

		// Default: gunakan direktori dari script saat ini untuk fallback.
		$directory = rtrim(dirname($scriptName), '/') . '/';
		return $directory === '//' ? '/' : $directory;
	}
}

/**
 * Membangun URL yang dapat diakses browser menuju file view lain relatif ke app/views.
 */
if (!function_exists('trevio_view_route')) {
	function trevio_view_route(string $relativePath): string
	{
		// Ambil base URL standar agar rute konsisten.
		$base = trevio_view_base_url();
		if ($base === '') {
			return $relativePath;
		}

		// Satukan base dengan path relatif yang diminta.
		return rtrim($base, '/') . '/' . ltrim($relativePath, '/');
	}
}

/**
 * Membentuk context autentikasi bawaan untuk dibagikan ke header/layout.
 * Controller bisa mengoper override melalui trevio_share_auth_context().
 */
if (!function_exists('trevio_build_auth_context')) {
	function trevio_build_auth_context(array $overrides = []): array
	{
        trevio_start_session();

		$defaults = [
			'isAuthenticated' => !empty($_SESSION['user_id'] ?? null),
			'profileName' => $_SESSION['user_name'] ?? 'Profil Kamu',
			'profilePhoto' => $_SESSION['user_avatar'] ?? null,
			'profileLink' => trevio_view_route('profile/index.php'),
			'userRole' => $_SESSION['user_role'] ?? 'guest',
		];

		$context = array_merge($defaults, array_filter($overrides, static function ($value) {
			return $value !== null;
		}));
		$context['profileInitial'] = strtoupper(substr($context['profileName'], 0, 1));

		return $context;
	}
}

/**
 * Diserukan oleh controller sebelum render view untuk override data header.
 */
if (!function_exists('trevio_share_auth_context')) {
	function trevio_share_auth_context(array $context): void
	{
		$GLOBALS['trevioHeaderAuthContext'] = trevio_build_auth_context($context);
	}
}

/**
 * Dipakai layout/header untuk mengambil context gabungan override + sesi.
 */
if (!function_exists('trevio_get_auth_context')) {
	function trevio_get_auth_context(array $overrides = []): array
	{
		$shared = $GLOBALS['trevioHeaderAuthContext'] ?? [];
		return trevio_build_auth_context(array_merge($shared, $overrides));
	}
}

/**
 * Memulai sesi dengan aman jika belum aktif.
 */
if (!function_exists('trevio_start_session')) {
    function trevio_start_session(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

/**
 * Menghasilkan CSRF token dan menyimpannya di sesi.
 */
if (!function_exists('trevio_csrf_token')) {
    function trevio_csrf_token(): string
    {
        trevio_start_session();
        if (empty($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
            }
        }
        return $_SESSION['csrf_token'];
    }
}

/**
 * Membuat input hidden untuk CSRF token.
 */
if (!function_exists('trevio_csrf_field')) {
    function trevio_csrf_field(): string
    {
        $token = trevio_csrf_token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}

/**
 * Verifikasi CSRF token dari request POST.
 */
if (!function_exists('trevio_verify_csrf')) {
    function trevio_verify_csrf(): bool
    {
        trevio_start_session();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true; // Tidak perlu cek untuk GET
        }
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}
