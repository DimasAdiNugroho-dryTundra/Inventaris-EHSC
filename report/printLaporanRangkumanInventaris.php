<?php
ob_start();

require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

class MYPDF extends TCPDF
{
    public function Header()
    {
        $image_file = 'headerLaporan.png';
        $this->setPageMark();
        $this->Image($image_file, 60, 5, 190, 0, 'PNG', '', 'T', false, 300, 'T', false, false, 0, false, false, false);
    }
}

$tahunTerpilih = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

if (!is_numeric($tahunTerpilih) || $tahunTerpilih < 2000) {
    die('Tahun tidak valid.');
}

$query = "SELECT 
            i.nama_barang, 
            i.merk,
            r.nama_ruangan,
            i.sumber_inventaris,
            i.kode_inventaris,
            i.satuan,
            d.kode_departemen,
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
        LEFT JOIN kontrol_barang_cawu_satu kbc1 ON i.id_inventaris = kbc1.id_inventaris AND kbc1.tahun_kontrol = $tahunTerpilih
        LEFT JOIN kontrol_barang_cawu_dua kbc2 ON i.id_inventaris = kbc2.id_inventaris AND kbc2.tahun_kontrol = $tahunTerpilih
        LEFT JOIN kontrol_barang_cawu_tiga kbc3 ON i.id_inventaris = kbc3.id_inventaris AND kbc3.tahun_kontrol = $tahunTerpilih
        LEFT JOIN user u1 ON kbc1.id_user = u1.id_user
        LEFT JOIN user u2 ON kbc2.id_user = u2.id_user
        LEFT JOIN user u3 ON kbc3.id_user = u3.id_user
        WHERE 1=1";

$result = mysqli_query($conn, $query);

try {
    $pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Laporan Hasil Kontrol Inventaris ' . $tahunTerpilih);

    $pdf->SetMargins(10, 30, 10);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);

    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->setImageScale(1.25);

    $pdf->AddPage('L', 'A4');

    $html = '
    <h3 style="text-align: center;">LAPORAN HASIL KONTROL INVENTARIS<br>TAHUN ' . $tahunTerpilih . '</h3>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 5pt;
            text-align: center;
            vertical-align: middle;
        }
        .cawu1 { background-color: #E8FFE8; }
        .cawu2 { background-color: #E8F8FF; }
        .cawu3 { background-color: #FFF8E8; }
        .header-main th {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .header-sub th { font-weight: bold; }
    </style>

    <table cellpadding="2">
        <thead>
            <tr class="header-main">
                <th rowspan="2">No</th>
                <th rowspan="2">Barang</th>
                <th rowspan="2">Kode Barang</th>
                <th rowspan="2">Ruangan</th>
                <th rowspan="2">Sumber</th>
                <th rowspan="2">Jumlah Awal</th>
                <th rowspan="2">Satuan</th>
                <th colspan="4" class="cawu1">Cawu 1</th>
                <th colspan="4" class="cawu2">Cawu 2</th>
                <th colspan="4" class="cawu3">Cawu 3</th>
                <th rowspan="2">Jumlah Akhir</th>
            </tr>
            <tr class="header-sub">
                <th class="cawu1">B</th>
                <th class="cawu1">R</th>
                <th class="cawu1">P</th>
                <th class="cawu1">H</th>
                <th class="cawu2">B</th>
                <th class="cawu2">R</th>
                <th class="cawu2">P</th>
                <th class="cawu2">H</th>
                <th class="cawu3">B</th>
                <th class="cawu3">R</th>
                <th class="cawu3">P</th>
                <th class="cawu3">H</th>
            </tr>
        </thead>
        <tbody>';

    $memilikiDataKontrol = false;
    if (mysqli_num_rows($result) > 0) {
        $no = 1;

        while ($row = mysqli_fetch_assoc($result)) {
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

                $html .= '<tr>';
                $html .= '<td style="text-align: center;">' . $no++ . '</td>';
                $html .= '<td style="text-align: left;">' . $row['nama_barang'] . ' - ' . $row['merk'] . '</td>';
                $html .= '<td style="text-align: center;">' . $row['kode_inventaris'] . '</td>';
                $html .= '<td style="text-align: center;">' . $row['nama_ruangan'] . '</td>';
                $html .= '<td style="text-align: center;">' . $row['sumber_inventaris'] . '</td>';
                $html .= '<td style="text-align: center;">' . $row['jumlah_awal'] . '</td>';
                $html .= '<td style="text-align: center;">' . $row['satuan'] . '</td>';

                // Cawu 1
                if ($memilikiDataCawu1) {
                    $html .= '<td class="cawu1">' . $row['jumlah_baik_cawu_satu'] . '</td>';
                    $html .= '<td class="cawu1">' . $row['jumlah_rusak_cawu_satu'] . '</td>';
                    $html .= '<td class="cawu1">' . $row['jumlah_pindah_cawu_satu'] . '</td>';
                    $html .= '<td class="cawu1">' . $row['jumlah_hilang_cawu_satu'] . '</td>';
                } else {
                    $html .= '<td colspan="4" class="cawu1" style="text-align: center;">Tidak ada kontrol</td>';
                }

                // Cawu 2
                if ($memilikiDataCawu2) {
                    $html .= '<td class="cawu2">' . $row['jumlah_baik_cawu_dua'] . '</td>';
                    $html .= '<td class="cawu2">' . $row['jumlah_rusak_cawu_dua'] . '</td>';
                    $html .= '<td class="cawu2">' . $row['jumlah_pindah_cawu_dua'] . '</td>';
                    $html .= '<td class="cawu2">' . $row['jumlah_hilang_cawu_dua'] . '</td>';
                } else {
                    $html .= '<td colspan="4" class="cawu2" style="text-align: center;">Tidak ada kontrol</td>';
                }

                // Cawu 3
                if ($memilikiDataCawu3) {
                    $html .= '<td class="cawu3">' . $row['jumlah_baik_cawu_tiga'] . '</td>';
                    $html .= '<td class="cawu3">' . $row['jumlah_rusak_cawu_tiga'] . '</td>';
                    $html .= '<td class="cawu3">' . $row['jumlah_pindah_cawu_tiga'] . '</td>';
                    $html .= '<td class="cawu3">' . $row['jumlah_hilang_cawu_tiga'] . '</td>';
                } else {
                    $html .= '<td colspan="4" class="cawu3" style="text-align: center;">Tidak ada kontrol</td>';
                }

                $html .= '<td style="text-align: center;">' . $row['jumlah_akhir'] . '</td>';
                $html .= '</tr>';
            }
        }

        if (!$memilikiDataKontrol) {
            $html .= '<tr><td colspan="19" style="text-align: center;">Tidak ada data yang dikontrol untuk tahun ' . $tahunTerpilih . '</td></tr>';
        }
    } else {
        $html .= '<tr><td colspan="19" style="text-align: center;">Tidak ada data yang dikontrol untuk tahun ' . $tahunTerpilih . '</td></tr>';
    }

    $html .= '
        </tbody>
    </table>
    <br><br>
    <table cellpadding="1" style="width: 100%;">
        <tr>
            <td style="border: none; text-align: left; padding: 2px; font-size: 5pt;"><strong>Keterangan :</strong></td>
        </tr>
        <tr><td style="border: none; text-align: left; padding: 2px; font-size: 5pt;">B = Baik</td></tr>
        <tr><td style="border: none; text-align: left; padding: 2px; font-size: 5pt;">R = Rusak</td></tr>
        <tr><td style="border: none; text-align: left; padding: 2px; font-size: 5pt;">P = Pindah</td></tr>
        <tr><td style="border: none; text-align: left; padding: 2px; font-size: 5pt;">H = Hilang</td></tr>
    </table>
    <br><br>
    <table style="width: 100%; border: none;" cellpadding="10">
        <tr>
            <td style="width: 50%; text-align: center; border: none;">
                <p>Staff ' . $row['kode_departemen'] . '</p>
                <div style="height: 80px;"></div>
                <p>(........................)</p>
            </td>
            <td style="width: 50%; text-align: center; border: none;">
                <p>Admin ' . $row['kode_departemen'] . '</p>
                <div style="height: 80px;"></div>
                <p>(........................)</p>
            </td>
        </tr>
    </table>
    <br><br>
    <table style="width: 100%; border: none;" cellpadding="10">
        <tr>
            <td style="width: 33.33%; text-align: center; border: none;">
                <p>Petugas Kontrol Cawu 1</p>
                <div style="height: 80px;"></div>
                <p>' . $row['nama_petugas_cawu_satu'] . '</p>
            </td>
            <td style="width: 33.33%; text-align: center; border: none;">
                <p>Petugas Kontrol Cawu 2</p>
                <div style="height: 80px;"></div>
                <p>' . $row['nama_petugas_cawu_dua'] . '</p>
            </td>
            <td style="width: 33.33%; text-align: center; border: none;">
                <p>Petugas Kontrol Cawu 3</p>
                <div style="height: 80px;"></div>
                <p>' . $row['nama_petugas_cawu_tiga'] . '</p>
            </td>
        </tr>
    </table>
    ';

    if (mysqli_num_rows($result) === 0 || !$memilikiDataKontrol) {
        $html = '
        <h3 style="text-align: center;">LAPORAN RANGKUMAN INVENTARIS<br>TAHUN ' . $tahunTerpilih . '</h3>
        <p style="text-align: center; color: red;">Tidak ada data inventaris yang dikontrol untuk tahun ' . $tahunTerpilih . '</p>';
    }

    ob_end_clean();

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('Laporan_Hasil_Kontrol_Inventaris_' . $tahunTerpilih . '.pdf', 'I');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}