<?php

namespace App\Core;

class App {
    protected $controller = 'HomeController'; // Default Controller
    protected $method = 'index';              // Default Method
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. ROUTING KHUSUS ADMIN
        // Cek jika URL dimulai dengan 'admin'
        if (isset($url[0]) && $url[0] === 'admin') {
            // Hapus 'admin' dari array url
            array_shift($url);

            if (isset($url[0])) {
                // Ambil nama resource (misal: 'users', 'hotels')
                $resource = ucfirst($url[0]);
                
                // Coba cari controller dengan nama Plural atau Singular
                // Contoh: URL 'users' -> Cari 'AdminUsersController' ATAU 'AdminUserController'
                $candidates = [
                    'Admin' . $resource . 'Controller',           // Plural (sesuai URL)
                    'Admin' . rtrim($resource, 's') . 'Controller' // Singular (buang 's' di akhir)
                ];

                $found = false;
                foreach ($candidates as $candidate) {
                    $pathLower = '../app/controllers/' . $candidate . '.php';
                    
                    if (file_exists($pathLower)) {
                        $this->controller = $candidate;
                        unset($url[0]); // Hapus resource dari URL
                        $found = true;
                        break;
                    }
                }

                // Jika tidak ketemu controller admin yang spesifik, bisa redirect ke dashboard atau 404
                if (!$found && $this->controller === 'HomeController') {
                     // Opsional: Set ke AdminController default jika hanya /admin saja
                     if (file_exists('../app/controllers/AdminController.php')) {
                         $this->controller = 'AdminController';
                     }
                }
            } else {
                // Jika URL hanya '/admin', arahkan ke AdminController
                if (file_exists('../app/controllers/AdminController.php')) {
                    $this->controller = 'AdminController';
                }
            }
        } 
        // 2. ROUTING STANDAR (NON-ADMIN)
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
            }
        }

        // 6. AMBIL PARAMETER SISA
        $this->params = $url ? array_values($url) : [];

        // 7. JALANKAN
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
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