<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flasher; // Pastikan class Flasher ada di namespace ini atau sesuaikan

class AdminPaymentController extends Controller {
    
    public function __construct()
    {
        // Pastikan session dimulai (opsional jika sudah di init)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Pastikan hanya admin yang bisa akses
        // Menggunakan isset untuk mencegah error "Undefined index" jika belum login
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    public function index()
    {
        $data['title'] = 'Kelola Pembayaran';
        
        // Mengambil semua pembayaran menggunakan Model Payment
        $data['payments'] = $this->model('Payment')->getAllPayments();

        // Load View
        $this->view('layouts/header', $data);
        $this->view('admin/payments/index', $data); 
        $this->view('layouts/footer');
    }

    public function verify($id)
    {
        // Halaman detail untuk verifikasi bukti transfer
        $data['title'] = 'Verifikasi Pembayaran';
        $data['payment'] = $this->model('Payment')->getPaymentById($id);

        if (!$data['payment']) {
            Flasher::setFlash('Data pembayaran tidak ditemukan', 'danger');
            header('Location: ' . BASEURL . '/admin/payment'); // Sesuaikan URL routing admin payment Anda
            exit;
        }

        $this->view('layouts/header', $data);
        $this->view('admin/payments/verify', $data);
        $this->view('layouts/footer');
    }

    public function process_confirm($id)
    {
        if ($this->model('Payment')->confirmPayment($id)) {
            Flasher::setFlash('Pembayaran berhasil dikonfirmasi', 'success');
        } else {
            Flasher::setFlash('Gagal mengonfirmasi pembayaran', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/payment');
        exit;
    }

    public function process_reject($id)
    {
        if ($this->model('Payment')->rejectPayment($id)) {
            Flasher::setFlash('Pembayaran telah ditolak', 'warning');
        } else {
            Flasher::setFlash('Gagal menolak pembayaran', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/payment');
        exit;
    }
}