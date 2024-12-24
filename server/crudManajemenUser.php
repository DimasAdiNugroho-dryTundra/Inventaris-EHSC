<?php

// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5; // Default ke 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT * FROM user WHERE nama LIKE '%$search%' LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM user WHERE nama LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Fungsi untuk validasi gambar
function validasiFoto($file)
{
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $maxSize = 2097152;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Tidak ada file yang diupload.";
    }

    if ($file['size'] > $maxSize) {
        return "Ukuran file foto tidak boleh lebih dari 2MB.";
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return "Tipe file tidak valid. Harus berupa JPG, JPEG, atau PNG.";
    }

    return true;
}

// Proses penambahan user
if (isset($_POST['tambahUser'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $email = $_POST['email'];
    $jabatan = $_POST['jabatan'];
    $hak_akses = $_POST['hak_akses'];

    // Cek username yang sudah ada
    $checkUsername = "SELECT * FROM user WHERE username='$username'";
    $checkResult = mysqli_query($conn, $checkUsername);

    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error_message'] = "Username sudah ada, silakan gunakan username lain.";
    } else {
        $uploadDir = '../upload/user/';
        $foto = $_FILES['foto'];

        $validationResult = validasiFoto($foto);
        if ($validationResult !== true) {
            $_SESSION['error_message'] = $validationResult;
        } else {
            // Tambahkan timestamp ke nama file
            $fotoName = time() . '_' . basename($foto['name']);
            $fotoTmp = $foto['tmp_name'];
            $fotoPath = $uploadDir . $fotoName;

            if (move_uploaded_file($fotoTmp, $fotoPath)) {
                $query = "INSERT INTO user (nama, username, password, email, jabatan, foto, hak_akses)
                          VALUES ('$nama', '$username', '$password', '$email', '$jabatan', '$fotoName', '$hak_akses')";

                if (mysqli_query($conn, $query)) {
                    $_SESSION['success_message'] = "User berhasil ditambahkan!";
                } else {
                    $_SESSION['error_message'] = "Gagal menambahkan user: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = "Gagal mengupload foto.";
            }
        }
    }
    header("Location: manajemenUser.php");
    exit();
}

// Proses pengeditan user
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_user = $_POST['id_user'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $jabatan = $_POST['jabatan'];
    $hak_akses = $_POST['hak_akses'];
    $password = !empty($_POST['password']) ? md5($_POST['password']) : null;

    // Mulai query update
    $query = "UPDATE user SET 
              nama = '$nama', 
              username = '$username', 
              email = '$email', 
              jabatan = '$jabatan', 
              hak_akses = '$hak_akses'";

    // Tambahkan password jika ada
    if ($password) {
        $query .= ", password = '$password'";
    }

    // Proses upload foto jika ada
    $fotoUploaded = false;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../upload/user/';
        $foto = $_FILES['foto'];

        // Validasi foto
        $validationResult = validasiFoto($foto);
        if ($validationResult !== true) {
            $_SESSION['error_message'] = $validationResult;
        } else {
            // Tambahkan timestamp ke nama file
            $fotoName = time() . '_' . basename($foto['name']);
            $fotoTmp = $foto['tmp_name'];
            $fotoPath = $uploadDir . $fotoName;

            // Ambil dan hapus foto lama
            $queryOld = "SELECT foto FROM user WHERE id_user='$id_user'";
            $resultOld = mysqli_query($conn, $queryOld);

            if ($user = mysqli_fetch_assoc($resultOld)) {
                $oldFotoPath = $uploadDir . $user['foto'];
                // Hapus foto lama jika ada
                if (file_exists($oldFotoPath)) {
                    unlink($oldFotoPath);
                }
            }

            // Upload foto baru
            if (move_uploaded_file($fotoTmp, $fotoPath)) {
                $query .= ", foto = '$fotoName'";
                $fotoUploaded = true;
            } else {
                $_SESSION['error_message'] = "Gagal mengupload foto baru.";
            }
        }
    }

    // Akhiri query dengan kondisi WHERE
    $query .= " WHERE id_user = '$id_user'";
    if (empty($_SESSION['error_message'])) {
        if (!mysqli_query($conn, $query)) {
            $_SESSION['error_message'] = "Gagal mengubah user: " . mysqli_error($conn);
        } else {
            $_SESSION['success_message'] = "User berhasil diubah!";
        }
    }

    header("Location: manajemenUser.php");
    exit();
}

// Proses penghapusan user
if (isset($_GET['delete'])) {
    $id_user = $_GET['delete'];

    $query = "SELECT foto FROM user WHERE id_user='$id_user'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && !empty($user['foto'])) {
        $fotoPath = '../upload/user/' . $user['foto'];
        if (file_exists($fotoPath)) {
            unlink($fotoPath); // Menghapus file foto
        }
    }

    // Hapus data user dari database
    $query = "DELETE FROM user WHERE id_user='$id_user'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "User berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus user: " . mysqli_error($conn);
    }

    header("Location: manajemenUser.php");
    exit(); // Tambahkan exit agar tidak melanjutkan eksekusi
}
?>