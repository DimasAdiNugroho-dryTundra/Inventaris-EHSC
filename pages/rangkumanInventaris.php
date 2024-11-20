<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudInventaris.php');
require('../layouts/header.php');

// Ambil tahun kontrol dari dropdown
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query untuk mengambil data inventaris dan kontrol berdasarkan tahun
$query = " SELECT 
        i.nama_barang, 
        i.kode_inventaris, 
        COALESCE(kbc1.jumlah_baik, 0) + COALESCE(kbc1.jumlah_rusak, 0) + COALESCE(kbc1.jumlah_pindah, 0) + COALESCE(kbc1.jumlah_hilang, 0) AS jumlah_awal,
        i.satuan,
        COALESCE(kbc1.jumlah_baik, 0) as jumlah_baik_cawu_satu,
        COALESCE(kbc1.jumlah_rusak, 0) as jumlah_rusak_cawu_satu,
        COALESCE(kbc1.jumlah_pindah, 0) as jumlah_pindah_cawu_satu,
        COALESCE(kbc1.jumlah_hilang, 0) as jumlah_hilang_cawu_satu,
        COALESCE(kbc2.jumlah_baik, 0) as jumlah_baik_cawu_dua,
        COALESCE(kbc2.jumlah_rusak, 0) as jumlah_rusak_cawu_dua,
        COALESCE(kbc2.jumlah_pindah, 0) as jumlah_pindah_cawu_dua,
        COALESCE(kbc2.jumlah_hilang, 0) as jumlah_hilang_cawu_dua,
        COALESCE(kbc3.jumlah_baik, 0) as jumlah_baik_cawu_tiga,
        COALESCE(kbc3.jumlah_rusak, 0) as jumlah_rusak_cawu_tiga,
        COALESCE(kbc3.jumlah_pindah, 0) as jumlah_pindah_cawu_tiga,
        COALESCE(kbc3.jumlah_hilang, 0) as jumlah_hilang_cawu_tiga
    FROM inventaris i
    LEFT JOIN kontrol_barang_cawu_satu kbc1 ON i.id_inventaris = kbc1.id_inventaris AND kbc1.tahun_kontrol = $selectedYear
    LEFT JOIN kontrol_barang_cawu_dua kbc2 ON i.id_inventaris = kbc2.id_inventaris AND kbc2.tahun_kontrol = $selectedYear
    LEFT JOIN kontrol_barang_cawu_tiga kbc3 ON i.id_inventaris = kbc3.id_inventaris AND kbc3.tahun_kontrol = $selectedYear
    WHERE EXISTS (
        SELECT 1 FROM kontrol_barang_cawu_satu k1 
        WHERE k1.id_inventaris = i.id_inventaris AND k1.tahun_kontrol = $selectedYear
        UNION
        SELECT 1 FROM kontrol_barang_cawu_dua k2 
        WHERE k2.id_inventaris = i.id_inventaris AND k2.tahun_kontrol = $selectedYear
        UNION
        SELECT 1 FROM kontrol_barang_cawu_tiga k3 
        WHERE k3.id_inventaris = i.id_inventaris AND k3.tahun_kontrol = $selectedYear
    )
";

// Tambahkan kondisi pencarian jika ada
if (!empty($search)) {
    $query .= " WHERE i.nama_barang LIKE '%$search%'
                OR i.kode_inventaris LIKE '%$search%'";
}

$result = mysqli_query($conn, $query);
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <?php require('../layouts/navbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h2 class="mb-1">Rangkuman Inventaris</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Rangkuman Inventaris</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="POST" class="d-flex">
                                        <input type="text" name="search" class="form-control me-2"
                                            placeholder="Cari barang..." value="<?php echo ($search); ?>">
                                        <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" class="d-flex align-items-center">
                                        <label for="year" class="label me-2">Tahun:</label>
                                        <select id="year" name="year" class="form-select" onchange="this.form.submit()">
                                            <?php
                                            $currentYear = date('Y');
                                            $years = range($currentYear - 5, $currentYear + 5);
                                            foreach ($years as $yr) {
                                                $selected = ($yr == $selectedYear) ? 'selected' : '';
                                                echo "<option value=\"$yr\" $selected>$yr</option>";
                                            }
                                            ?>
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover table-sm" id="inventarisTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2" class="text-center align-middle">No</th>
                                            <th rowspan="2" class="text-center align-middle">Nama Barang</th>
                                            <th rowspan="2" class="text-center align-middle">Kode Barang</th>
                                            <th rowspan="2" class="text-center align-middle">Jumlah Awal</th>
                                            <th rowspan="2" class="text-center align-middle">Satuan</th>
                                            <th colspan="4" class="text-center align-middle bg-label-success">Cawu 1
                                            </th>
                                            <th colspan="4" class="text-center align-middle bg-label-info">Cawu 2</th>
                                            <th colspan="4" class="text-center align-middle bg-label-warning">Cawu 3
                                            </th>
                                            <th rowspan="2" class="text-center align-middle">Jumlah Akhir</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center align-middle bg-label-success">B</th>
                                            <th class="text-center align-middle bg-label-success">R</th>
                                            <th class="text-center align-middle bg-label-success">P</th>
                                            <th class="text-center align-middle bg-label-success">H</th>
                                            <th class="text-center align-middle bg-label-info">B</th>
                                            <th class="text-center align-middle bg-label-info">R</th>
                                            <th class="text-center align-middle bg-label-info">P</th>
                                            <th class="text-center align-middle bg-label-info">H</th>
                                            <th class="text-center align-middle bg-label-warning">B</th>
                                            <th class="text-center align-middle bg-label-warning">R</th>
                                            <th class="text-center align-middle bg-label-warning">P</th>
                                            <th class="text-center align-middle bg-label-warning">H</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($result) > 0) {
                                            $no = 1;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $jumlah_akhir = $row['jumlah_baik_cawu_tiga'];
                                                ?>
                                                <tr>
                                                    <td class="text-center align-middle"><?php echo $no++; ?></td>
                                                    <td class="text-center align-middle">
                                                        <?php echo ($row['nama_barang']); ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php echo ($row['kode_inventaris']); ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php echo ($row['jumlah_awal']); ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php echo ($row['satuan']); ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-success">
                                                        <?php echo isset($row['jumlah_baik_cawu_satu']) ? ($row['jumlah_baik_cawu_satu']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-success">
                                                        <?php echo isset($row['jumlah_rusak_cawu_satu']) ? ($row['jumlah_rusak_cawu_satu']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-success">
                                                        <?php echo isset($row['jumlah_pindah_cawu_satu']) ? ($row['jumlah_pindah_cawu_satu']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-success">
                                                        <?php echo isset($row['jumlah_hilang_cawu_satu']) ? ($row['jumlah_hilang_cawu_satu']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-info">
                                                        <?php echo isset($row['jumlah_baik_cawu_dua']) ? ($row['jumlah_baik_cawu_dua']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-info">
                                                        <?php echo isset($row['jumlah_rusak_cawu_dua']) ? ($row['jumlah_rusak_cawu_dua']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-info">
                                                        <?php echo isset($row['jumlah_pindah_cawu_dua']) ? ($row['jumlah_pindah_cawu_dua']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-info">
                                                        <?php echo isset($row['jumlah_hilang_cawu_dua']) ? ($row['jumlah_hilang_cawu_dua']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-warning">
                                                        <?php echo isset($row['jumlah_baik_cawu_tiga']) ? ($row['jumlah_baik_cawu_tiga']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-warning">
                                                        <?php echo isset($row['jumlah_rusak_cawu_tiga']) ? ($row['jumlah_rusak_cawu_tiga']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-warning">
                                                        <?php echo isset($row['jumlah_pindah_cawu_tiga']) ? ($row['jumlah_pindah_cawu_tiga']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle bg-label-warning">
                                                        <?php echo isset($row['jumlah_hilang_cawu_tiga']) ? ($row['jumlah_hilang_cawu_tiga']) : 0; ?>
                                                    </td>
                                                    <td class="text-center align-middle"><?php echo $jumlah_akhir; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr>';
                                            echo '<td colspan="19" class="text-center py-3">Tidak ada data yang dikontrol untuk tahun <strong>' . $selectedYear . '</strong>.</td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <p><strong>Keterangan:</strong></p>
                                <ul>
                                    <li><strong>B</strong> = Baik</li>
                                    <li><strong>R</strong> = Rusak</li>
                                    <li><strong>P</strong> = Pindah</li>
                                    <li><strong>H</strong> = Hilang</li>
                                </ul>
                            </div>

                            <div class="mt-3">
                                <form method="POST" action="../report/printLaporanRangkumanInventaris.php"
                                    target="_blank">
                                    <input type="hidden" name="year" value="<?php echo $selectedYear; ?>">
                                    <button class="btn btn-primary" type="submit">Cetak Laporan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <?php require('../layouts/footer.php'); ?>
                <!-- / Footer -->
                <div class="content-backdrop fade"></div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
</div>
<?php require('../layouts/assetsFooter.php'); ?>