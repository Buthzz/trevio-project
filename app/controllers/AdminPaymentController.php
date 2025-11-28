<?php

class AdminPaymentController extends Controller {
    
    public function __construct()
    {
        // Pastikan hanya admin yang bisa akses
        // Sesuaikan 'isAdmin' dengan middleware/session check Anda
        if ($_SESSION['user_role'] != 'admin') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    public function index()
    {
        $data['title'] = 'Kelola Pembayaran';
        
        // Mengambil semua pembayaran menggunakan Model yang sudah diperbaiki (Part 1)
        $data['payments'] = $this->model('Payment')->getAllPayments();

        // Load View
        // Pastikan path view sesuai dengan struktur folder Anda
        $this->view('layouts/header', $data);
        $this->view('admin/payments/index', $data); // Sesuaikan jika view ada di folder lain
        $this->view('layouts/footer');
    }

    public function verify($id)
    {
        // Halaman detail untuk verifikasi bukti transfer
        $data['title'] = 'Verifikasi Pembayaran';
        $data['payment'] = $this->model('Payment')->getPaymentById($id);

        if (!$data['payment']) {
            Flasher::setFlash('Data pembayaran tidak ditemukan', 'danger');
            header('Location: ' . BASEURL . '/adminpayment');
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
        header('Location: ' . BASEURL . '/adminpayment');
        exit;
    }

    public function process_reject($id)
    {
        if ($this->model('Payment')->rejectPayment($id)) {
            Flasher::setFlash('Pembayaran telah ditolak', 'warning');
        } else {
            Flasher::setFlash('Gagal menolak pembayaran', 'danger');
        }
        header('Location: ' . BASEURL . '/adminpayment');
        exit;
    }
}