<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudInventaris.php');
require('../layouts/header.php');

// Ambil tahun kontrol dari dropdown
$tahunTerpilih = isset($_POST['year']) ? mysqli_real_escape_string($conn, $_POST['year']) : date('Y');

// Penanganan pencarian
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';

// Query untuk mengambil data inventaris dan kontrol berdasarkan tahun
// Gunakan tahun saat ini sebagai default
$tahunTerpilih = isset($_POST['year']) ? mysqli_real_escape_string($conn, $_POST['year']) : date('Y');

$query = "SELECT 
    i.id_inventaris,
    i.nama_barang, 
    i.merk,
    d.nama_departemen,
    k.nama_kategori,
    r.nama_ruangan,
    i.sumber_inventaris,
    i.kode_inventaris,
    i.satuan,
    u1.nama AS nama_petugas_cawu_satu,
    u2.nama AS nama_petugas_cawu_dua,
    u3.nama AS nama_petugas_cawu_tiga,
    COALESCE(
        CASE 
            WHEN kbc1.jumlah_baik IS NOT NULL THEN kbc1.jumlah_baik + kbc1.jumlah_rusak + kbc1.jumlah_pindah + kbc1.jumlah_hilang
            WHEN kbc2.jumlah_baik IS NOT NULL THEN kbc2.jumlah_baik + kbc2.jumlah_rusak + kbc2.jumlah_pindah + kbc2.jumlah_hilang
            WHEN kbc3.jumlah_baik IS NOT NULL THEN kbc3.jumlah_baik + kbc3.jumlah_rusak + kbc3.jumlah_pindah + kbc3.jumlah_hilang
            ELSE 0
        END, 0
    ) AS jumlah_awal,
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
    COALESCE(kbc3.jumlah_hilang, 0) as jumlah_hilang_cawu_tiga,
    COALESCE(
        CASE 
            WHEN kbc3.jumlah_baik IS NOT NULL THEN kbc3.jumlah_baik
            WHEN kbc2.jumlah_baik IS NOT NULL THEN kbc2.jumlah_baik
            WHEN kbc1.jumlah_baik IS NOT NULL THEN kbc1.jumlah_baik
            ELSE 0
        END, 0
    ) AS jumlah_akhir
FROM inventaris i
LEFT JOIN ruangan r ON i.id_ruangan = r.id_ruangan
LEFT JOIN departemen d ON i.id_departemen = d.id_departemen
LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
LEFT JOIN kontrol_barang_cawu_satu kbc1 ON i.id_inventaris = kbc1.id_inventaris AND kbc1.tahun_kontrol = '$tahunTerpilih'
LEFT JOIN kontrol_barang_cawu_dua kbc2 ON i.id_inventaris = kbc2.id_inventaris AND kbc2.tahun_kontrol = '$tahunTerpilih'
LEFT JOIN kontrol_barang_cawu_tiga kbc3 ON i.id_inventaris = kbc3.id_inventaris AND kbc3.tahun_kontrol = '$tahunTerpilih'
LEFT JOIN user u1 ON kbc1.id_user = u1.id_user
LEFT JOIN user u2 ON kbc2.id_user = u2.id_user
LEFT JOIN user u3 ON kbc3.id_user = u3.id_user
WHERE 1=1";

// Tambahkan kondisi pencarian jika ada
if (!empty($search)) {
    $query .= " AND (i.nama_barang LIKE '%$search%' 
                     OR i.merk LIKE '%$search%' 
                     OR i.kode_inventaris LIKE '%$search%'
                     OR r.nama_ruangan LIKE '%$search%'
                     OR d.nama_departemen LIKE '%$search%'
                     OR k.nama_kategori LIKE '%$search%'
                     OR u1.nama LIKE '%$search%'
                     OR u2.nama LIKE '%$search%'
                     OR u3.nama LIKE '%$search%')";
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
                                            $tahunSekarang = date('Y');
                                            $tahunRange = range($tahunSekarang - 5, $tahunSekarang + 5);
                                            foreach ($tahunRange as $th) {
                                                $terpilih = ($th == $tahunTerpilih) ? 'selected' : '';
                                                echo "<option value=\"$th\" $terpilih>$th</option>";
                                            }
                                            ?>
                                        </select>
                                    </form>
                                </div>
                                <div class="row p-3">
                                    <div class="col-12 d-flex justify-content-start align-items-center">
                                        <a href="../report/printLaporanRangkumanInventaris.php?year=<?php echo $tahunTerpilih; ?>"
                                            class="btn btn-success">
                                            <i class="ti ti-printer"></i> Cetak Laporan
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover table-sm" id="inventarisTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2" class="text-center align-middle">No</th>
                                            <th rowspan="2" class="text-center align-middle">Petugas Kontrol</th>
                                            <th rowspan="2" class="text-center align-middle">Barang</th>
                                            <th rowspan="2" class="text-center align-middle">Kode Barang</th>
                                            <th rowspan="2" class="text-center align-middle">Ruangan</th>
                                            <th rowspan="2" class="text-center align-middle">Sumber</th>
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
                                            $memilikiDataKontrol = false;

                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // Cek apakah ada data kontrol di salah satu cawu
                                                $memilikiDataCawu1 = $row['jumlah_baik_cawu_satu'] > 0 ||
                                                    $row['jumlah_rusak_cawu_satu'] > 0 ||
                                                    $row['jumlah_pindah_cawu_satu'] > 0 ||
                                                    $row['jumlah_hilang_cawu_satu'] > 0;

                                                $memilikiDataCawu2 = $row['jumlah_baik_cawu_dua'] > 0 ||
                                                    $row['jumlah_rusak_cawu_dua'] > 0 ||
                                                    $row['jumlah_pindah_cawu_dua'] > 0 ||
                                                    $row['jumlah_hilang_cawu_dua'] > 0;

                                                $memilikiDataCawu3 = $row['jumlah_baik_cawu_tiga'] > 0 ||
                                                    $row['jumlah_rusak_cawu_tiga'] > 0 ||
                                                    $row['jumlah_pindah_cawu_tiga'] > 0 ||
                                                    $row['jumlah_hilang_cawu_tiga'] > 0;

                                                if ($memilikiDataCawu1 || $memilikiDataCawu2 || $memilikiDataCawu3) {
                                                    $memilikiDataKontrol = true;
                                                    ?>
                                        <tr>
                                            <td class=" text-center align-middle"><?php echo $no++; ?></td>
                                            <td class="text-center align-middle">
                                                <?php
                                                            if ($memilikiDataCawu3) {
                                                                echo $row['nama_petugas_cawu_tiga'];
                                                            } elseif ($memilikiDataCawu2) {
                                                                echo $row['nama_petugas_cawu_dua'];
                                                            } elseif ($memilikiDataCawu1) {
                                                                echo $row['nama_petugas_cawu_satu'];
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php echo ($row['nama_barang'] . ' - ' . $row['nama_barang']); ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php echo ($row['kode_inventaris']); ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php echo ($row['nama_ruangan']); ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php echo ($row['sumber_inventaris']); ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php echo $row['jumlah_awal']; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php echo ($row['satuan']); ?>
                                            </td>

                                            <!-- Cawu 1 -->
                                            <?php if ($memilikiDataCawu1): ?>
                                            <td class="text-center align-middle bg-label-success">
                                                <?php echo $row['jumlah_baik_cawu_satu']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-success">
                                                <?php echo $row['jumlah_rusak_cawu_satu']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-success">
                                                <?php echo $row['jumlah_pindah_cawu_satu']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-success">
                                                <?php echo $row['jumlah_hilang_cawu_satu']; ?>
                                            </td>
                                            <?php else: ?>
                                            <td colspan="4" class="text-center align-middle bg-label-success">Tidak ada
                                                kontrol</td>
                                            <?php endif; ?>

                                            <!-- Cawu 2 -->
                                            <?php if ($memilikiDataCawu2): ?>
                                            <td class="text-center align-middle bg-label-info">
                                                <?php echo $row['jumlah_baik_cawu_dua']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-info">
                                                <?php echo $row['jumlah_rusak_cawu_dua']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-info">
                                                <?php echo $row['jumlah_pindah_cawu_dua']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-info">
                                                <?php echo $row['jumlah_hilang_cawu_dua']; ?>
                                            </td>
                                            <?php else: ?>
                                            <td colspan="4" class="text-center align-middle bg-label-info">Tidak ada
                                                kontrol</td>
                                            <?php endif; ?>

                                            <!-- Cawu 3 -->
                                            <?php if ($memilikiDataCawu3): ?>
                                            <td class="text-center align-middle bg-label-warning">
                                                <?php echo $row['jumlah_baik_cawu_tiga']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-warning">
                                                <?php echo $row['jumlah_rusak_cawu_tiga']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-warning">
                                                <?php echo $row['jumlah_pindah_cawu_tiga']; ?>
                                            </td>
                                            <td class="text-center align-middle bg-label-warning">
                                                <?php echo $row['jumlah_hilang_cawu_tiga']; ?>
                                            </td>
                                            <?php else: ?>
                                            <td colspan="4" class="text-center align-middle bg-label-warning">Tidak ada
                                                kontrol</td>
                                            <?php endif; ?>

                                            <td class="text-center align-middle">
                                                <?php
                                                            echo $row['jumlah_akhir'];
                                                            if ($row['jumlah_baik_cawu_tiga'] > 0) {
                                                                echo " (Cawu 3)";
                                                            } elseif ($row['jumlah_baik_cawu_dua'] > 0) {
                                                                echo " (Cawu 2)";
                                                            } elseif ($row['jumlah_baik_cawu_satu'] > 0) {
                                                                echo " (Cawu 1)";
                                                            }
                                                            ?>
                                            </td>

                                        </tr>
                                        <?php
                                                }
                                            }

                                            if (!$memilikiDataKontrol) {
                                                echo '<tr>';
                                                echo '<td colspan="19" class="text-center py-3">Tidak ada data yang dikontrol untuk tahun <strong>' . ($tahunTerpilih) . '</strong>.</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr>';
                                            echo '<td colspan="19" class="text-center py-3">Tidak ada data yang dikontrol untuk tahun <strong>' . ($tahunTerpilih) . '</strong>.</td>';
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require('../layouts/footer.php');
require('../layouts/assetsFooter.php');
?>

<script>
$(document).ready(function() {
    // Inisialisasi DataTables
    $('#inventarisTable').DataTable({
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });
});
</script>