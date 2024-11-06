<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../layouts/header.php');

// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query untuk menampilkan data inventaris
$query = "SELECT i.*, pb.nama_barang, d.nama_departemen, c.kode_kategori 
          FROM inventaris i
          JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
          JOIN departemen d ON i.id_departemen = d.id_departemen
          JOIN kategori c ON i.id_kategori = c.id_kategori
          WHERE pb.nama_barang LIKE '%$search%'
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM inventaris i
               JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
               WHERE pb.nama_barang LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Proses penambahan inventaris
if (isset($_POST['tambahInventaris'])) {
    $id_penerimaan = $_POST['id_penerimaan'];
    $tanggal_terima = $_POST['tanggal_terima'];
    $jumlah = $_POST['jumlah'];
    $satuan = $_POST['satuan'];
    $kondisi = 1; // Default kondisi: Baik

    // Mendapatkan data departemen, kategori, dan tahun
    $getBarangQuery = "SELECT d.kode_departemen, c.kode_kategori, YEAR('$tanggal_terima') as tahun 
                       FROM penerimaan_barang pb 
                       JOIN permintaan_barang p ON pb.id_permintaan = p.id_permintaan
                       JOIN departemen d ON p.id_departemen = d.id_departemen
                       JOIN kategori c ON p.id_kategori = c.id_kategori
                       WHERE pb.id_penerimaan = '$id_penerimaan'";
    $barangResult = mysqli_query($conn, $getBarangQuery);
    $barangData = mysqli_fetch_assoc($barangResult);
    $kode_barang = $barangData['kode_departemen'] . '/' . $barangData['kode_kategori'] . '/' . $barangData['tahun'];

    $query = "INSERT INTO inventaris (id_penerimaan, kode_barang, tanggal_terima, jumlah, satuan, kondisi)
              VALUES ('$id_penerimaan', '$kode_barang', '$tanggal_terima', '$jumlah', '$satuan', '$kondisi')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Inventaris berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan inventaris: " . mysqli_error($conn);
    }
    header("Location: inventaris.php");
    exit();
}
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h2 class="mb-1">Manajemen Inventaris</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Manajemen Inventaris</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="card">
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                                <span class="alert-icon rounded"><i class="ti ti-ban"></i></span>
                                <?php echo $_SESSION['error_message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                                <span class="alert-icon rounded"><i class="ti ti-check"></i></span>
                                <?php echo $_SESSION['success_message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>

                        <h4 class="card-header d-flex justify-content-between align-items-center">
                            Data Inventaris
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahInventarisModal">Tambah Inventaris</button>
                        </h4>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Departemen</th>
                                        <th>Tanggal Terima</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = $offset + 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row['kode_barang']; ?></td>
                                            <td><?php echo $row['nama_barang']; ?></td>
                                            <td><?php echo $row['nama_departemen']; ?></td>
                                            <td><?php echo $row['tanggal_terima']; ?></td>
                                            <td><?php echo $row['jumlah']; ?></td>
                                            <td><?php echo $row['satuan']; ?></td>
                                            <td><?php echo $row['kondisi'] == 1 ? 'Baik' : 'Rusak'; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination pagination-rounded justify-content-center">
                                <?php if ($page > 1) { ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>"
                                            aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
                                    </li>
                                <?php } ?>
                                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                    <li class="page-item <?php if ($i == $page)
                                        echo 'active'; ?>">
                                        <a class="page-link"
                                            href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($page < $totalPages) { ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>"
                                            aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>

                    <!-- Modal Tambah Inventaris -->
                    <div class="modal fade" id="tambahInventarisModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Inventaris</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Penerimaan Barang</label>
                                            <select name="id_penerimaan" class="form-select" required>
                                                <?php
                                                $permintaan_query = "SELECT pb.id_penerimaan, pb.nama_barang 
                                                                     FROM penerimaan_barang pb
                                                                     LEFT JOIN inventaris i ON pb.id_penerimaan = i.id_penerimaan
                                                                     WHERE i.id_penerimaan IS NULL";
                                                $permintaan_result = mysqli_query($conn, $permintaan_query);
                                                while ($row = mysqli_fetch_assoc($permintaan_result)) {
                                                    echo "<option value='{$row['id_penerimaan']}'>{$row['nama_barang']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Terima</label>
                                            <input type="date" name="tanggal_terima" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" name="jumlah" class="form-control" required readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Satuan</label>
                                            <input type="text" name="satuan" class="form-control" required readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kondisi</label>
                                            <select name="kondisi" class="form-select" required>
                                                <option value="1" selected>Baik</option>
                                                <option value="0">Rusak</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="tambahInventaris"
                                            class="btn btn-primary">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php require('../layouts/footer.php'); ?>
            </div>
        </div>
    </div>
</div>