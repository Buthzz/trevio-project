<?php

namespace App\Core;

class Controller {
    // Method untuk memanggil view dan mengirim data
    public function view($view, $data = []) {
        if (file_exists('../app/views/' . $view . '.php')) {
            extract($data); // Ekstrak array ke variabel
            require_once '../app/views/' . $view . '.php';
        } else {
            // Error handling yang lebih bersih (opsional)
            die("View does not exist: " . $view);
        }
    }

    // Method untuk memanggil model
    public function model($model) {
        // Cek apakah file model ada
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            
            // Instansiasi Model
            // Mengasumsikan Model menggunakan namespace App\Models
            // Jika Model Anda tidak pakai namespace, gunakan: return new $model();
            
            $modelClassWithNamespace = "\\App\\Models\\" . $model;
            
            if (class_exists($modelClassWithNamespace)) {
                return new $modelClassWithNamespace();
            } else {
                // Fallback jika model tidak menggunakan namespace (Legacy support)
                return new $model();
            }
        } else {
            die("Model does not exist: " . $model);
        }
    }
}