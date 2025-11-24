<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller {
    
    public function error403() {
        http_response_code(403);
        $this->view('errors/403');
    }

    public function error404() {
        http_response_code(404);
        $this->view('errors/404');
    }

    public function error500() {
        http_response_code(500);
        $this->view('errors/500');
    }
}
