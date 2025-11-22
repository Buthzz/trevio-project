<?php

namespace App\Core;

class Controller {
    // Method untuk memanggil view dan mengirim data
    public function view($view, $data = []) {
        if (file_exists('../app/views/' . $view . '.php')) {
            extract($data); // Ekstrak array ke variabel
            require_once '../app/views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }
}