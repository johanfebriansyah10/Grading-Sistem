<?php
/**
 * functions.php
 * Fungsi inti untuk analitik dan logika lainnya.
 */

/**
 * Menghitung prediksi skor siswa dengan regresi linier berganda yang disempurnakan.
 * Y = 15 + 0.3*Tugas + 0.2*UTS + 0.3*UAS + 0.2*Kehadiran
 */
function hitungPrediksi($tugas, $uts, $uas, $attendance) {
    $a = 15;
    $b1 = 0.3; // Tugas
    $b2 = 0.2; // UTS
    $b3 = 0.3; // UAS
    $b4 = 0.2; // Kehadiran

    $Y = $a + ($b1 * $tugas) + ($b2 * $uts) + ($b3 * $uas) + ($b4 * $attendance);
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
