<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');

// Handle AJAX request PERTAMA
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    if ($_GET['action'] == 'getDetails') {
        echo json_encode(getDetailInventaris($_GET['kode']));
        exit;
    }

    if ($_GET['action'] == 'getDataKontrol') {
        // Ambil id_inventaris dari kode
        $detail = getDetailInventaris($_GET['kode']);
        if ($detail['success']) {
            $kontrolData = getDataKontrol($detail['data']['id_inventaris']);
            echo json_encode([
                'success' => true,
                'data' => $kontrolData
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        exit;
    }
}

require('../layouts/header.php');

// Fungsi untuk mendapatkan detail inventaris
function getDetailInventaris($kode)
{
    global $conn;

    $kode = mysqli_real_escape_string($conn, $kode);

    $query = "SELECT i.*, d.nama_departemen, k.nama_kategori,
              CASE 
                  WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang 
                  ELSE i.nama_barang 
              END as nama_barang,
              i.jumlah_awal,
              i.jumlah_akhir,
              i.satuan
              FROM inventaris i
              JOIN departemen d ON i.id_departemen = d.id_departemen
              JOIN kategori k ON i.id_kategori = k.id_kategori
              LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
              WHERE i.kode_inventaris = '$kode'";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return [
            'success' => true,
            'data' => mysqli_fetch_assoc($result)
        ];
    }

    return [
        'success' => false,
        'message' => 'Data inventaris tidak ditemukan'
    ];
}

function getDataKontrol($id_inventaris)
{
    global $conn;

    $cawu_queries = [
        "SELECT tahun_kontrol, SUM(jumlah_baik) AS jumlah_baik, SUM(jumlah_rusak) AS total_rusak, 
                SUM(jumlah_pindah) AS total_pindah, SUM(jumlah_hilang) AS total_hilang, i.satuan
         FROM kontrol_barang_cawu_satu k
         JOIN inventaris i ON k.id_inventaris = i.id_inventaris
         WHERE k.id_inventaris = '$id_inventaris'
         GROUP BY tahun_kontrol",

        "SELECT tahun_kontrol, SUM(jumlah_baik) AS jumlah_baik, SUM(jumlah_rusak) AS total_rusak, 
                SUM(jumlah_pindah) AS total_pindah, SUM(jumlah_hilang) AS total_hilang, i.satuan
         FROM kontrol_barang_cawu_dua k
         JOIN inventaris i ON k.id_inventaris = i.id_inventaris
         WHERE k.id_inventaris = '$id_inventaris'
         GROUP BY tahun_kontrol",

        "SELECT tahun_kontrol, SUM(jumlah_baik) AS jumlah_baik, SUM(jumlah_rusak) AS total_rusak, 
                SUM(jumlah_pindah) AS total_pindah, SUM(jumlah_hilang) AS total_hilang, i.satuan
         FROM kontrol_barang_cawu_tiga k
         JOIN inventaris i ON k.id_inventaris = i.id_inventaris
         WHERE k.id_inventaris = '$id_inventaris'
         GROUP BY tahun_kontrol"
    ];

    $cawu_data = [];
    foreach ($cawu_queries as $query) {
        $result = mysqli_query($conn, $query);
        $cawu_data[] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $cawu_data;
}

?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <?php require('../layouts/navbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- Breadcrumb -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-1">Scan QR Code</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-style1">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="inventaris.php">Inventaris</a>
                                </li>
                                <li class="breadcrumb-item active">Scan QR Code</li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Card untuk Scanner -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Scanner QR Code</h5>
                            <div>
                                <button class="btn btn-secondary btn-sm me-2" id="startButton">
                                    <i class="ti ti-player-play"></i> Start
                                </button>
                                <button class="btn btn-danger btn-sm" id="stopButton">
                                    <i class="ti ti-player-stop"></i> Stop
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="border rounded p-2">
                                        <div id="reader"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <h5>Hasil Scan</h5>
                                        <div id="scanResult">
                                            <p class="text-muted">Silahkan scan QR Code untuk melihat detail barang...
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card untuk Detail Barang -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Detail Kontrol Barang</h5>
                        </div>
                        <div class="card-body">
                            <div id="DetailInventaris">
                                <p class="text-muted">Data kontrol barang akan muncul setelah QR Code dipindai...</p>
                            </div>

                            <!-- Tabel Status Barang untuk Cawu 1 -->
                            <h6 class="mb-3">Status Barang Cawu 1</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>Tahun</th>
                                            <th>Barang Baik</th>
                                            <th>Barang Rusak</th>
                                            <th>Barang Hilang</th>
                                            <th>Barang Pindah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cawu1-table">
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabel Status Barang untuk Cawu 2 -->
                            <h6 class="mb-3">Status Barang Cawu 2</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>Tahun</th>
                                            <th>Barang Baik</th>
                                            <th>Barang Rusak</th>
                                            <th>Barang Pindah</th>
                                            <th>Barang Hilang</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cawu2-table">
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabel Status Barang untuk Cawu 3 -->
                            <h6 class="mb-3">Status Barang Cawu 3</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>Tahun</th>
                                            <th>Barang Baik</th>
                                            <th>Barang Rusak</th>
                                            <th>Barang Pindah</th>
                                            <th>Barang Hilang</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cawu3-table">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php require('../layouts/footer.php'); ?>
        </div>
    </div>
</div>

<!-- Import library html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
const html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", {
        fps: 10,
        qrbox: {
            width: 250,
            height: 250
        },
        aspectRatio: 1.0
    }
);

let scanning = false;

function onScanSuccess(decodedText, decodedResult) {
    // Update scan result dengan format yang lebih rapi
    document.getElementById('scanResult').innerHTML = `
        <div class="table-responsive">
            <div class="alert alert-success mb-3">
                <strong>QR Code terdeteksi!</strong><br>
                Kode: ${decodedText}
            </div>
            <table class="table table-borderless">
                <tr>
                    <th width="200">Nama Barang</th>
                    <td>: <span id="namaBarang">Memuat...</span></td>
                </tr>
                <tr>
                    <th>Departemen</th>
                    <td>: <span id="departemen">Memuat...</span></td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>: <span id="kategori">Memuat...</span></td>
                </tr>
                <tr>
                    <th>Jumlah Awal</th>
                    <td>: <span id="jumlahAwal">Memuat...</span></td>
                </tr>
                <tr>
                    <th>Jumlah Akhir</th>
                    <td>: <span id="jumlahAkhir">Memuat...</span></td>
                </tr>
            </table>
        </div>
    `;

    // Fetch inventory details
    fetch(`scanQRcode.php?action=getDetails&kode=${encodeURIComponent(decodedText)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update nilai-nilai dalam tabel
                document.getElementById('namaBarang').textContent = data.data.nama_barang;
                document.getElementById('departemen').textContent = data.data.nama_departemen;
                document.getElementById('kategori').textContent = data.data.nama_kategori;
                document.getElementById('jumlahAwal').textContent =
                    `${data.data.jumlah_awal} ${data.data.satuan}`;
                document.getElementById('jumlahAkhir').textContent =
                    `${data.data.jumlah_akhir} ${data.data.satuan}`;

                // Fetch and display control data
                fetch(`scanQRcode.php?action=getDataKontrol&kode=${encodeURIComponent(decodedText)}`)
                    .then(response => response.json())
                    .then(controlData => {
                        updateTabelKontrolData(controlData);
                    })
                    .catch(error => {
                        console.error('Error fetching control data:', error);
                        document.getElementById('DetailInventaris').innerHTML = `
                            <div class="alert alert-danger">
                                Terjadi kesalahan saat mengambil data kontrol barang.
                            </div>
                        `;
                    });
            } else {
                document.getElementById('scanResult').innerHTML = `
                    <div class="alert alert-danger">
                        ${data.message || 'Data tidak ditemukan'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('scanResult').innerHTML = `
                <div class="alert alert-danger">
                    Terjadi kesalahan saat mengambil data. Silakan coba lagi.
                </div>
            `;
        });
}

function updateTabelKontrolData(controlData) {
    if (!controlData.success) {
        document.getElementById('DetailInventaris').innerHTML = `
            <div class="alert alert-danger">
                ${controlData.message || 'Data kontrol tidak ditemukan'}
            </div>
        `;
        return;
    }

    const cawu1Table = document.getElementById('cawu1-table');
    const cawu2Table = document.getElementById('cawu2-table');
    const cawu3Table = document.getElementById('cawu3-table');

    cawu1Table.innerHTML = '';
    cawu2Table.innerHTML = '';
    cawu3Table.innerHTML = '';

    controlData.data.forEach((cawuData, index) => {
        if (cawuData.length > 0) {
            cawuData.forEach(row => {
                const tr = document.createElement('tr');
                tr.classList.add('text-center');

                const tahunTd = document.createElement('td');
                tahunTd.textContent = row.tahun_kontrol;

                const baikTd = document.createElement('td');
                baikTd.classList.add('text-success');
                baikTd.textContent = `${row.jumlah_baik} ${row.satuan}`;

                const rusakTd = document.createElement('td');
                rusakTd.classList.add('text-danger');
                rusakTd.textContent = `${row.total_rusak} ${row.satuan}`;

                const hilangTd = document.createElement('td');
                hilangTd.classList.add('text-warning');
                hilangTd.textContent = `${row.total_hilang} ${row.satuan}`;

                const pindahTd = document.createElement('td');
                pindahTd.classList.add('text-info');
                pindahTd.textContent = `${row.total_pindah} ${row.satuan}`;

                tr.appendChild(tahunTd);
                tr.appendChild(baikTd);
                tr.appendChild(rusakTd);
                tr.appendChild(hilangTd);
                tr.appendChild(pindahTd);

                if (index === 0) {
                    cawu1Table.appendChild(tr);
                } else if (index === 1) {
                    cawu2Table.appendChild(tr);
                } else {
                    cawu3Table.appendChild(tr);
                }
            });
        } else {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.setAttribute('colspan', '5');
            td.classList.add('text-center');
            td.textContent = index === 0 ? 'Tidak ada data untuk Cawu 1' :
                index === 1 ? 'Tidak ada data untuk Cawu 2' :
                'Tidak ada data untuk Cawu 3';
            tr.appendChild(td);

            if (index === 0) {
                cawu1Table.appendChild(tr);
            } else if (index === 1) {
                cawu2Table.appendChild(tr);
            } else {
                cawu3Table.appendChild(tr);
            }
        }
    });

    document.getElementById('DetailInventaris').style.display = 'block';
}

function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    // console.warn(`Code scan error = ${error}`);
}

// Buttons event listeners
document.getElementById('startButton').addEventListener('click', () => {
    if (!scanning) {
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        scanning = true;
    }
});

document.getElementById('stopButton').addEventListener('click', () => {
    if (scanning) {
        html5QrcodeScanner.clear();
        scanning = false;
        document.getElementById('scanResult').innerHTML = `
            <p class="text-muted">Scanner dihentikan. Klik Start untuk memulai kembali...</p>
        `;
    }
});

// Start scanner when page loads
document.addEventListener('DOMContentLoaded', () => {
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    scanning = true;
});
</script>