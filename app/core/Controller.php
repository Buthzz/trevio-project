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

    // Method untuk memanggil model
    public function model($model) {
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            
            // Cek namespace App\Models
            $class = "\\App\\Models\\" . $model;
            if (class_exists($class)) {
                return new $class();
            }
            // Fallback jika tidak pakai namespace
            return new $model();
        } else {
            die("Model does not exist: " . $model);
        }
    }
}