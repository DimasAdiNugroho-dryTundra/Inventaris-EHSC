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
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['jabatan'] = $user['jabatan'];
            $_SESSION['hak_akses'] = $user['hak_akses'];

            return true;
        } else {
            return "Password salah."; 
        }
    } else {
        return "Username tidak ditemukan.";
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login_result = login($username, $password);
    if ($login_result === true) {
        if ($_SESSION['hak_akses'] == 0) {
            $error_message = "Akun Anda belum aktif. Silakan hubungi operator.";
        } else {
            header("Location: pages/dashboard.php");
            exit();
        }
    } else {
        $error_message = $login_result;
    }
}
?>