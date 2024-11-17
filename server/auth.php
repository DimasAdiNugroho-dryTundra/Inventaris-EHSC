<?php
session_start();
require_once 'configDB.php';

function login($username, $password)
{
    global $conn;

    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (md5($password) === $user['password']) {
            // Verifikasi berhasil, buat sesi
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['jabatan'] = $user['jabatan'];
            $_SESSION['hak_akses'] = $user['hak_akses']; // Menyimpan hak akses

            return true; // Login sukses
        } else {
            return "Password salah."; // Password salah
        }
    } else {
        return "Username tidak ditemukan."; // Username tidak ditemukan
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login_result = login($username, $password);
    if ($login_result === true) {
        // Cek status aktif
        if ($_SESSION['hak_akses'] == 0) {
            $error_message = "Akun Anda belum aktif. Silakan hubungi operator.";
        } else {
            // Redirect ke dashboard
            header("Location: pages/dashboard.php");
            exit();
        }
    } else {
        // Tampilkan pesan kesalahan
        $error_message = $login_result;
    }
}
?>