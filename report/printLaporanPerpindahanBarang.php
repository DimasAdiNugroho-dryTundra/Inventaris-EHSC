<?php
// Mulai output buffering
ob_start();

require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

// Ambil ID dari URL
$id_perpindahan_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil data perpindahan barang berdasarkan ID dengan JOIN ke departemen
$query = "SELECT pb.*, i.nama_barang, i.kode_inventaris, d.nama_departemen 
          FROM perpindahan_barang pb 
          JOIN inventaris i ON pb.id_inventaris = i.id_inventaris 
          JOIN departemen d ON i.id_departemen = d.id_departemen
          WHERE pb.id_perpindahan_barang = $id_perpindahan_barang";
$result = mysqli_query($conn, $query);

// Buat class turunan TCPDF untuk kustomisasi header
class MYPDF extends TCPDF
{
    public function Header()
    {
        $image_file = 'headerLaporan.png';
        $this->setPageMark();
        $this->Image($image_file, 10, 5, 190, 0, 'PNG', '', 'T', false, 300, 'T', false, false, 0, false, false, false);
        $this->SetY(0);
    }
}

try {
    // Buat objek TCPDF dengan class kustom
    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Set informasi dokumen
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Laporan Perpindahan Barang');

    // Set margins
    $pdf->SetMargins(15, 30, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);

    // Set image scale factor
    $pdf->setImageScale(1.25);

    // Set font
    $pdf->SetFont('helvetica', '', 11);

    // Add a page
    $pdf->AddPage();

    // Cek apakah ada data yang ditemukan
    if ($row = mysqli_fetch_assoc($result)) {
        // HTML Content
        $html = '
        <h1 style="text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;">LAPORAN PERPINDAHAN BARANG</h1>

        <p>
Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $row['id_perpindahan_barang'] . '<br>
Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . date('d/m/Y', strtotime($row['tanggal_perpindahan'])) . '<br>
Departemen&nbsp;: ' . $row['nama_departemen'] . '
</p>

        <p>Kepada Yth.<br>Admin Gudang<br>Di Tempat</p>

        <p>Dengan Hormat,</p>
        <p style="text-align: justify;">Laporan ini merinci perpindahan barang yang terjadi pada inventaris kami. 
        Diharapkan dapat segera ditindaklanjuti sesuai dengan prosedur yang berlaku. 
        Kami menghargai perhatian dan kerjasama dari pihak terkait dalam proses ini.</p>
        
        <br>
        <table border="1" cellpadding="5">
            <tr style="background-color: #f2f2f2;">
                <td><strong>Nama Barang</strong></td>
                <td>' . $row['nama_barang'] . '</td>
            </tr>
            <tr>
                <td><strong>Kode Inventaris</strong></td>
                <td>' . $row['kode_inventaris'] . '</td>
            </tr>
            <tr>
                <td><strong>Jumlah Perpindahan</strong></td>
                <td>' . $row['jumlah_perpindahan'] . '</td>
            </tr>
            <tr>
                <td><strong>Cawu</strong></td>
                <td>' . $row['cawu'] . '</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td><strong>Keterangan</strong></td>
                <td>' . nl2br($row['keterangan']) . '</td>
            </tr>
        </table>
        
        <br>
        <p>Demikian laporan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
        
        <br><br>
        <table cellpadding="5">
            <tr>
                <td width="50%" style="text-align: center;">Menyetujui,</td>
                <td width="50%" style="text-align: center;">Pelapor,</td>
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
                <td style="text-align: center;">Admin Gudang</td>
                <td style="text-align: center;">Admin ' . $row['nama_departemen'] . '</td>
            </tr>
        </table>';

        // Output HTML
        $pdf->writeHTML($html, true, false, true, false, '');

    } else {
        $pdf->Cell(0, 10, 'Data tidak ditemukan', 0, 1, 'C');
    }

    // Bersihkan output buffer
    ob_end_clean();

    // Output PDF
    $pdf->Output('laporan_perpindahan_barang_' . $id_perpindahan_barang . '.pdf', 'I');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>