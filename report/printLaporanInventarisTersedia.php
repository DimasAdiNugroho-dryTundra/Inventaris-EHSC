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
        $this->Image($image_file, 10, 5, 190, 0, 'PNG', '', 'T', false, 300, 'T', false, false, 0, false, false, false);
        $this->SetY(40);
    }
}

try {
    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Laporan Inventaris Tersedia');
    $pdf->SetMargins(15, 30, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->setImageScale(1.25);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->AddPage();

    // Query untuk mengambil data inventaris yang tersedia
    $query = "SELECT i.*, d.nama_departemen, k.nama_kategori 
              FROM inventaris i 
              JOIN departemen d ON i.id_departemen = d.id_departemen 
              JOIN kategori k ON i.id_kategori = k.id_kategori 
              WHERE i.jumlah_akhir > 0";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $html = '
        <h1 style="text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;">LAPORAN INVENTARIS TERSEDIA</h1>

        <p style="line-height: 1.5;">
Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: INV-' . date('Ymd') . '<br>
Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . date('d/m/Y') . '<br>
Lampiran&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: -
</p>
        <br>
        <table border="1" cellpadding="5">
            <tr style="background-color: #f2f2f2; text-align: center;">
                <td><strong>No</strong></td>
                <td><strong>Kode Inventaris</strong></td>
                <td><strong>Departemen</strong></td>
                <td><strong>Kategori</strong></td>
                <td><strong>Nama Barang</strong></td>
                <td><strong>Jumlah Awal</strong></td>
                <td><strong>Jumlah Akhir</strong></td>
                <td><strong>Satuan</strong></td>
            </tr>';

        $no = 1;
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr style="text-align: center;">
                <td>' . $no++ . '</td>
                <td>' . $row['kode_inventaris'] . '</td>
                <td>' . $row['nama_departemen'] . '</td>
                <td>' . $row['nama_kategori'] . '</td>
                <td>' . $row['nama_barang'] . '</td>
                <td>' . $row['jumlah_awal'] . '</td>
                <td>' . $row['jumlah_akhir'] . '</td>
                <td>' . $row['satuan'] . '</td>
            </tr>';
        }

        $html .= '</table>
        
        <br>
        <p>Demikian laporan ini kami buat untuk dapat digunakan sebagaimana mestinya.</p>
        
        <br><br>
        <table cellpadding="5">
            <tr>
                <td width="50%" style="text-align: center;">Mengetahui</td>
                <td width="50%" style="text-align: center;">Dibuat oleh</td>
            </tr>
            <tr>
                <td height="60"></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center;">(........................)</td>
                <td style="text-align: center;">(........................)</td>
            </tr>
            <tr>
                <td style="text-align: center;">Kepala Gudang</td>
                <td style="text-align: center;">Admin Inventaris</td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        $pdf->Cell(0, 10, 'Tidak ada data inventaris tersedia.', 0, 1, 'C');
    }

    ob_end_clean();
    $pdf->Output('laporan_inventaris_tersedia.pdf', 'I');

} catch (Exception $e) {
    error_log('PDF Generation Error: ' . $e->getMessage());
    echo 'Error: ' . $e->getMessage();
}