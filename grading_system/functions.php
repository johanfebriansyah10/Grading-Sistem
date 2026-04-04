<?php
/**
 * functions.php
 * Fungsi inti untuk analitik dan logika lainnya.
 */

/**
 * Menghitung prediksi skor siswa dengan regresi linier berganda.
 * Y = 15 + 0.5*X1 + 0.3*X2 + 0.2*X3
 */
function hitungPrediksi($tugas, $uts, $attendance) {
    $a = 15;
    $b1 = 0.5;
    $b2 = 0.3;
    $b3 = 0.2;

    $Y = $a + ($b1 * $tugas) + ($b2 * $uts) + ($b3 * $attendance);
    return round($Y, 2); 
}

/**
 * Mengklasifikasikan skor berdasarkan KKM.
 */
function cekStatus($prediksi, $kkm = 70) {
    if ($prediksi < $kkm) {
        return "Berisiko";
    } else {
        return "Aman";
    }
}

/**
 * Utilitas untuk membatasi akses hanya untuk user yang sudah login.
 */
function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
}

/**
 * Utilitas untuk membatasi akses hanya untuk ADMIN.
 */
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        die('<div style="color:red; font-weight:bold; padding: 20px;">Akses Ditolak. Hanya untuk Admin. <a href="dashboard.php">Kembali</a></div>');
    }
}
?>
