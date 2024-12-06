<?php
ob_start();
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

// Extend TCPDF with custom header
class MYPDF extends TCPDF
{
    public function Header()
    {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 15, 'Laporan Inventaris', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(5);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 15, 'Tahun: ' . $_POST['year'], 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$selectedYear = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

if (!is_numeric($selectedYear) || $selectedYear < 2000) {
    die('Tahun tidak valid.');
}

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

$result = mysqli_query($conn, $query);

// Create new PDF document
$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Laporan Inventaris ' . $selectedYear);
$pdf->SetMargins(10, 30, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage('L', 'A4');

$html = '
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 10px;
    }
    th, td {
        border: 1px solid #000;
        padding: 4px;
        font-size: 8pt;
        text-align: center;
        vertical-align: middle;
    }
    .cawu1 {
        background-color: #E8FFE8;
    }
    .cawu2 {
        background-color: #E8F8FF;
    }
    .cawu3 {
        background-color: #FFF8E8;
    }
    .header-main th {
        font-weight: bold;
        background-color: #f5f5f5;
    }
    .header-sub th {
        font-weight: bold;
    }
</style>

<table cellpadding="2">
    <thead>
        <tr class="header-main">
            <th rowspan="3">No</th>
            <th rowspan="3">Nama Barang</th>
            <th rowspan="3">Kode Barang</th>
            <th rowspan="3">Jumlah Awal</th>
            <th rowspan="3">Satuan</th>
            <th colspan="12">Kondisi Barang</th>
            <th rowspan="3">Jumlah Akhir</th>
        </tr>
        <tr class="header-main">
            <th colspan="4" class="cawu1">Cawu 1</th>
            <th colspan="4" class="cawu2">Cawu 2</th>
            <th colspan="4" class="cawu3">Cawu 3</th>
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

// Generate table content
if (mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $jumlah_akhir = $row['jumlah_baik_cawu_tiga'];
        $html .= '
        <tr>
            <td style="text-align: center;">' . $no++ . '</td>
            <td style="text-align: left;">' . $row['nama_barang'] . '</td>
            <td style="text-align: center;">' . $row['kode_inventaris'] . '</td>
            <td style="text-align: center;">' . $row['jumlah_awal'] . '</td>
            <td style="text-align: center;">' . $row['satuan'] . '</td>
            
            <!-- Cawu 1 -->
            <td class="cawu1">' . $row['jumlah_baik_cawu_satu'] . '</td>
            <td class="cawu1">' . $row['jumlah_rusak_cawu_satu'] . '</td>
            <td class="cawu1">' . $row['jumlah_pindah_cawu_satu'] . '</td>
            <td class="cawu1">' . $row['jumlah_hilang_cawu_satu'] . '</td>
            
            <!-- Cawu 2 -->
            <td class="cawu2">' . $row['jumlah_baik_cawu_dua'] . '</td>
            <td class="cawu2">' . $row['jumlah_rusak_cawu_dua'] . '</td>
            <td class="cawu2">' . $row['jumlah_pindah_cawu_dua'] . '</td>
            <td class="cawu2">' . $row['jumlah_hilang_cawu_dua'] . '</td>
            
            <!-- Cawu 3 -->
            <td class="cawu3">' . $row['jumlah_baik_cawu_tiga'] . '</td>
            <td class="cawu3">' . $row['jumlah_rusak_cawu_tiga'] . '</td>
            <td class="cawu3">' . $row['jumlah_pindah_cawu_tiga'] . '</td>
            <td class="cawu3">' . $row['jumlah_hilang_cawu_tiga'] . '</td>
            
            <td style="text-align: center;">' . $jumlah_akhir . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="18" style="text-align: center;">Tidak ada data yang dikontrol untuk tahun ' . $selectedYear . '</td></tr>';
}

$html .= '
    </tbody>
</table>

<!-- Keterangan dan Tanda Tangan -->
<table style="width: 100%; border: none; margin-top: 20px;">
    <tr>
        <td style="width: 60%; border: none;">
            <p style="font-size: 10pt;"><strong>Keterangan:</strong></p>
            <table style="width: auto; border: none;">
                <tr>
                    <td style="border: none; text-align: left; padding: 2px; font-size: 10pt;">B = Baik</td>
                </tr>
                <tr>
                    <td style="border: none; text-align: left; padding: 2px; font-size: 10pt;">R = Rusak</td>
                </tr>
                <tr>
                    <td style="border: none; text-align: left; padding: 2px; font-size: 10pt;">P = Pindah</td>
                </tr>
                <tr>
                    <td style="border: none; text-align: left; padding: 2px; font-size: 10pt;">H = Hilang</td>
                </tr>
            </table>
        </td>
        <td style="width: 40%; border: none; text-align: center;">
            <p style="font-size: 10pt;">Bandung, ' . date('d F Y') . '</p>
            <p style="font-size: 10pt;">Petugas Inventaris</p>
            <br><br><br>
            <p style="font-size: 10pt;">(_____________________)</p>
            <p style="font-size: 10pt;">NIP. </p>
        </td>
    </tr>
</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('Laporan_Inventaris_' . $selectedYear . '.pdf', 'I');

?>