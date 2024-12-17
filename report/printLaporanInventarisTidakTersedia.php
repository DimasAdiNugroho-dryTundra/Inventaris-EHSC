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

// Mengubah orientasi menjadi lanskap
$query = "SELECT i.*, d.*, r.nama_ruangan, k.nama_kategori 
          FROM inventaris i 
          JOIN departemen d ON i.id_departemen = d.id_departemen 
          JOIN ruangan r ON i.id_ruangan = r.id_ruangan
          JOIN kategori k ON i.id_kategori = k.id_kategori 
          WHERE i.jumlah_akhir = 0";

$result = $conn->query($query);

$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false); // Ubah 'P' menjadi 'L'

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Laporan Inventaris Tidak Tersedia');
$pdf->SetMargins(15, 30, 15);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->setImageScale(1.25);
$pdf->SetFont('helvetica', '', 9); // Kecilkan ukuran font
$pdf->AddPage();

try {
    if ($result->num_rows > 0) {
        $html = '
        <h1 style="text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;">LAPORAN INVENTARIS TIDAK TERSEDIA</h1>
        <p>
        Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . date('d/m/Y') . '<br>
        </p>
        <table border="1" cellpadding="5">
            <tr style="background-color: #f2f2f2; text-align: center;">
                <td style="width: 30px;"><strong>No</strong></td> <!-- Kecilkan lebar kolom No -->
                <td><strong>Kode Inventaris</strong></td>
                <td><strong>Tanggal Perolehan</strong></td>
                <td style="width: 120px;"><strong>Departemen</strong></td>
                <td><strong>Ruangan</strong></td>
                <td><strong>Kategori</strong></td>
                <td><strong>Nama Barang</strong></td>
                <td><strong>Merk</strong></td>
                <td><strong>Jumlah Awal</strong></td>
                <td><strong>Jumlah Akhir</strong></td>
                <td><strong>Satuan</strong></td>
                <td><strong>Sumber Inventaris</strong></td>
            </tr>';

        $no = 1;
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr style="text-align: center;">
                <td style="width: 30px;">' . $no++ . '</td>
                <td>' . $row['kode_inventaris'] . '</td>
                <td>' . $row['tanggal_perolehan'] . '</td>
                <td style="width: 120px;">' . $row['nama_departemen'] . '</td>
                <td>' . $row['nama_ruangan'] . '</td>
                <td>' . $row['nama_kategori'] . '</td>
                <td>' . $row['nama_barang'] . '</td>
                <td>' . $row['merk'] . '</td>
                <td>' . $row['jumlah_awal'] . '</td>
                <td>' . $row['jumlah_akhir'] . '</td>
                <td>' . $row['satuan'] . '</td>
                <td>' . $row['sumber_inventaris'] . '</td>
            </tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        $pdf->Cell(0, 10, 'Tidak ada data inventaris tersedia.', 0, 1, 'C');
    }

    ob_end_clean();
    $pdf->Output('laporan_inventaris_tidak_tersedia.pdf', 'I');

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}