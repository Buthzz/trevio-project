<?php

//version2

namespace App\Core;

class App {
    protected $controller = 'HomeController'; // Default Controller
    protected $method = 'index';              // Default Method
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Deteksi Controller (Segmen URL pertama)
        if (isset($url[0])) {
            // Special handling for error pages
            if ($url[0] === 'errors' && isset($url[1])) {
                $this->controller = 'ErrorController';
                $this->method = 'error' . $url[1];
                $this->params = [];
                
                $pathLower = '../app/controllers/ErrorController.php';
                if (file_exists($pathLower)) {
                    require_once $pathLower;
                    $controllerClass = "\\App\\Controllers\\ErrorController";
                    $this->controller = new $controllerClass;
                    
                    if (method_exists($this->controller, $this->method)) {
                        call_user_func([$this->controller, $this->method]);
                        return;
                    }
                }
            }
            
            // Special handling for admin prefix routes
            // /admin/hotels -> AdminHotelController
            // /admin/payments -> AdminPaymentController
            if ($url[0] === 'admin' && isset($url[1])) {
                $u_controller = 'Admin' . ucfirst($url[1]) . 'Controller';
                
                $pathLower = '../app/controllers/' . $u_controller . '.php';
                $pathCap   = '../app/Controllers/' . $u_controller . '.php';

                if (file_exists($pathLower) || file_exists($pathCap)) {
                    $this->controller = $u_controller;
                    unset($url[0]); // Remove 'admin'
                    unset($url[1]); // Remove resource name
                }
            } else {
                $u_controller = ucfirst($url[0]) . 'Controller';
                
                // Cek keberadaan file di folder lowercase (app/controllers) DAN Capital (app/Controllers)
                // Ini penting untuk kompatibilitas VPS Linux
                $pathLower = '../app/controllers/' . $u_controller . '.php';
                $pathCap   = '../app/Controllers/' . $u_controller . '.php';

                if (file_exists($pathLower) || file_exists($pathCap)) {
                    $this->controller = $u_controller;
                    unset($url[0]);
                }
            }
        }

        // 2. Require File Controller yang Benar
        $pathLower = '../app/controllers/' . $this->controller . '.php';
        $pathCap   = '../app/Controllers/' . $this->controller . '.php';

        if (file_exists($pathLower)) {
            require_once $pathLower;
        } elseif (file_exists($pathCap)) {
            require_once $pathCap;
        } else {
            // Fallback darurat jika controller default tidak ketemu (misal salah nama file)
            die("Controller file not found: " . $this->controller);
        }
        
        // Instansiasi Controller
        // Pastikan namespace sesuai (biasanya App\Controllers\...)
        $controllerClass = "\\App\\Controllers\\" . $this->controller;
        if (class_exists($controllerClass)) {
            $this->controller = new $controllerClass;
        } else {
            // Fallback jika namespace salah (misal App\controllers\)
            $controllerClass = "\\App\\controllers\\" . $this->controller;
            $this->controller = new $controllerClass;
        }

        // 3. Cek Method (Segmen URL kedua)
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 4. Ambil Parameter (Sisa segmen URL)
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // Jalankan method controller
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        // Opsi 1: Ambil dari $_GET['url'] (Standard .htaccess / Apache)
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }

        // Opsi 2: Ambil langsung dari REQUEST_URI (Fix untuk Nginx VPS)
        // Ini menangani kasus dimana Nginx tidak otomatis mengisi parameter ?url=
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        // Bersihkan path jika aplikasi ada di subfolder
        if (strpos($requestUri, $scriptName) === 0 && $scriptName !== '/') {
            $requestUri = substr($requestUri, strlen($scriptName));
        }
        
        $url = trim($requestUri, '/');
        if (!empty($url)) {
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }

        return [];
    }
}