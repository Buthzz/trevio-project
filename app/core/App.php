<?php

namespace App\Core;

class App
{
    protected $controller = 'HomeController'; // Default Controller
    protected $method = 'index';              // Default Method
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // 1. ROUTING KHUSUS: ADMIN & OWNER
        // Cek jika URL dimulai dengan 'admin' atau 'owner'
        if (isset($url[0]) && ($url[0] === 'admin' || $url[0] === 'owner')) {
            $rolePrefix = ucfirst($url[0]); // Menjadi 'Admin' atau 'Owner'

            // Hapus prefix ('admin'/'owner') dari array url
            array_shift($url);

            // Cek segmen berikutnya (resource/halaman)
            if (isset($url[0])) {
                // Ambil nama resource (misal: 'users', 'hotels')
                $resource = ucfirst($url[0]);

                // Coba cari controller spesifik dengan pola: Role + Resource + Controller
                // Contoh: 'Owner' + 'Hotel' + 'Controller' = OwnerHotelController
                $candidates = [
                    $rolePrefix . $resource . 'Controller',           // Plural/As is (misal: OwnerHotelsController)
                    $rolePrefix . rtrim($resource, 's') . 'Controller' // Singular (misal: OwnerHotelController)
                ];

                $found = false;
                foreach ($candidates as $candidate) {
                    $pathLower = '../app/controllers/' . $candidate . '.php';

                    if (file_exists($pathLower)) {
                        $this->controller = $candidate;
                        unset($url[0]); // Hapus resource dari URL karena sudah jadi controller
                        $found = true;
                        break;
                    }
                }

                // Jika controller spesifik tidak ditemukan (misal URL: /owner/profile atau /owner/logout)
                // Maka anggap segmen ini adalah method dari controller utama (OwnerController/AdminController)
                if (!$found) {
                    $mainController = $rolePrefix . 'Controller'; // AdminController atau OwnerController
                    if (file_exists('../app/controllers/' . $mainController . '.php')) {
                        $this->controller = $mainController;
                        // JANGAN unset($url[0]) di sini, biarkan itu menjadi nama method nanti
                    }
                }
            } else {
                // Jika URL hanya '/admin' atau '/owner', arahkan ke Controller Utama Dashboard
                $mainController = $rolePrefix . 'Controller';
                if (file_exists('../app/controllers/' . $mainController . '.php')) {
                    $this->controller = $mainController;
                }
            }
        }
        // 2. ROUTING STANDAR (NON-ADMIN/OWNER)
        else if (isset($url[0])) {
            // Special case untuk Error pages
            if ($url[0] === 'errors') {
                $this->controller = 'ErrorController';
                unset($url[0]);
            } else {
                $u_controller = ucfirst($url[0]) . 'Controller';
                $pathLower = '../app/controllers/' . $u_controller . '.php';

                if (file_exists($pathLower)) {
                    $this->controller = $u_controller;
                    unset($url[0]);
                } else {
                    // Controller tidak ditemukan, redirect ke 404
                    $this->show404();
                    return;
                }
            }
        }

        // 3. INSTANSIASI CONTROLLER
        require_once '../app/controllers/' . $this->controller . '.php';

        // Gunakan namespace penuh
        $controllerClass = "\\App\\Controllers\\" . $this->controller;

        if (class_exists($controllerClass)) {
            $this->controller = new $controllerClass;
        } else {
            die("Critical Error: Controller class '$controllerClass' not found but file exists.");
        }

        // 4. RE-INDEX ARRAY URL
        // Sangat penting agar parameter method terbaca urut mulai dari index 0
        $url = array_values($url);

        // 5. DETEKSI METHOD
        if (isset($url[0])) {
            if (method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                unset($url[0]);
            } else {
                // Method tidak ditemukan, redirect ke 404
                $this->show404();
                return;
            }
        }

        // 6. AMBIL PARAMETER SISA
        $this->params = $url ? array_values($url) : [];

        // 7. JALANKAN
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Show 404 Error Page
     */
    protected function show404()
    {
        http_response_code(404);
        require_once '../app/controllers/ErrorController.php';
        $errorController = new \App\Controllers\ErrorController();
        $errorController->error404();
        exit;
    }

    public function parseUrl()
    {
        // Fix untuk Nginx & Apache compatibility
        $requestUri = $_SERVER['REQUEST_URI'];

        // Hapus query string (?foo=bar)
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // Hapus nama script (index.php) dan folder project dari path
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($requestUri, $scriptName) === 0 && $scriptName !== '/') {
            $requestUri = substr($requestUri, strlen($scriptName));
        }

        $url = trim($requestUri, '/');

        if (!empty($url)) {
            return explode('/', filter_var($url, FILTER_SANITIZE_URL));
        }

        return [];
    }
}