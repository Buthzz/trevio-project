<?php

namespace App\Core;

class App {
    // Controller default saat halaman pertama dibuka (http://localhost/)
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Cek apakah file Controller ada berdasarkan segmen URL pertama
        if (isset($url[0])) {
            // Ubah 'auth' menjadi 'AuthController'
            $u_controller = ucfirst($url[0]) . 'Controller';
            
            if (file_exists('../app/controllers/' . $u_controller . '.php')) {
                $this->controller = $u_controller;
                unset($url[0]);
            }
        }

        // Require file controller yang ditemukan atau default
        require_once '../app/controllers/' . $this->controller . '.php';
        
        // Instansiasi Controller (contoh: new \App\Controllers\AuthController)
        $controllerClass = "\\App\\Controllers\\" . $this->controller;
        $this->controller = new $controllerClass;

        // 2. Cek Method (URL segmen kedua)
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. Ambil Parameter (sisa segmen URL)
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // Jalankan Controller & Method dengan Parameter
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Memecah URL menjadi array
     * URL: /auth/login/123 -> ['auth', 'login', '123']
     */
    public function parseUrl() {
        if (isset($_GET['url'])) {
            // Bersihkan URL dari tanda slash di akhir dan karakter ilegal
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}