<?php

namespace App\Core;

class App {
    protected $controller = 'HomeController'; // Default Controller
    protected $method = 'index';              // Default Method
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Cek Controller (Segmen URL ke-0)
        if (isset($url[0])) {
            $u_controller = ucfirst($url[0]) . 'Controller';
            if (file_exists('../app/controllers/' . $u_controller . '.php')) {
                $this->controller = $u_controller;
                unset($url[0]);
            }
        }

        require_once '../app/controllers/' . $this->controller . '.php';
        
        // Instansiasi Controller
        $controllerClass = "\\App\\Controllers\\" . $this->controller;
        $this->controller = new $controllerClass;

        // 2. Cek Method (Segmen URL ke-1)
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. Ambil Parameter (Sisa segmen URL)
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // Jalankan
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            // Trim slash di akhir dan awal (untuk kompatibilitas Nginx/Apache)
            $url = rtrim(ltrim($_GET['url'], '/'), '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}